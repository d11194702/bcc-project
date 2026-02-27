<?php

/**
 * Фильтры/сортировки каталога товаров
 */

function bcc_get_catalog_sort_options(): array {
	return [
		'default' => 'По порядку',
		'popular' => 'По популярности',
		'cheap'   => 'Дешевле',
		'exp'     => 'Дороже',
	];
}

function bcc_get_current_catalog_sort(): string {
	$sort = isset($_GET['sort']) ? sanitize_key((string) $_GET['sort']) : 'default';
	$options = bcc_get_catalog_sort_options();
	return array_key_exists($sort, $options) ? $sort : 'default';
}

function bcc_get_catalog_sort_url(string $sort_key): string {
	$sort_key = sanitize_key($sort_key);
	$options = bcc_get_catalog_sort_options();
	if (!array_key_exists($sort_key, $options)) {
		$sort_key = 'default';
	}

	$url = home_url(add_query_arg(null, null));
	$url = remove_query_arg(['paged'], $url);

	if ($sort_key === 'default') {
		$url = remove_query_arg(['sort'], $url);
	} else {
		$url = add_query_arg(['sort' => $sort_key], $url);
	}

	return $url;
}

function bcc_render_catalog_sort(): void {
	$options = bcc_get_catalog_sort_options();
	$current = bcc_get_current_catalog_sort();
	?>
	<div class="sort">
		<button class="sort-btn">
			<span>Сортировка:</span>
			<img src="<?php echo get_template_directory_uri(); ?>/assets/images/arrow-down.svg" alt="">
		</button>
		<ul class="sort-list">
			<?php foreach ($options as $key => $label) :
				$is_active = $key === $current;
				?>
				<li>
					<a href="<?php echo esc_url(bcc_get_catalog_sort_url($key)); ?>"<?php echo $is_active ? ' class="active"' : ''; ?>>
						<?php echo esc_html($label); ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
	<?php
}

function bcc_parse_price_value($value): ?float {
	if ($value === null) {
		return null;
	}
	$value = trim((string) $value);
	if ($value === '') {
		return null;
	}

	// Берём ПЕРВОЕ число из строки ("750", "750,5", "750 руб/м2"), чтобы не ловить "2" из "м2".
	$value = str_replace(["\xC2\xA0"], ' ', $value);
	if (!preg_match('/-?\d+(?:[\.,]\d+)?/u', $value, $m)) {
		return null;
	}

	$number_str = str_replace(',', '.', $m[0]);
	$number = (float) $number_str;
	return is_finite($number) ? $number : null;
}

function bcc_get_price_from(): ?float {
	$raw = $_GET['price_from'] ?? $_GET['min_price'] ?? null;
	return bcc_parse_price_value($raw);
}

function bcc_get_price_to(): ?float {
	$raw = $_GET['price_to'] ?? $_GET['max_price'] ?? null;
	return bcc_parse_price_value($raw);
}

// Поддерживаем числовое мета-поле для фильтрации/сортировки.
add_action('save_post_product', function ($post_id) {
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
	if (wp_is_post_revision($post_id)) return;
	if (!current_user_can('edit_post', $post_id)) return;

	$raw = function_exists('carbon_get_post_meta')
		? carbon_get_post_meta($post_id, 'product_price')
		: get_post_meta($post_id, 'product_price', true);

	$number = bcc_parse_price_value($raw);

	if ($number === null) {
		delete_post_meta($post_id, 'product_price_num');
		return;
	}
	update_post_meta($post_id, 'product_price_num', $number);
}, 15);

// Одноразовая переиндексация цен (если раньше товары были созданы до внедрения product_price_num)
add_action('admin_init', function () {
	if (!is_admin() || !current_user_can('manage_options')) {
		return;
	}
	if (empty($_GET['bcc_reindex_prices'])) {
		return;
	}

	error_log("BCC: Reindex started");

	$per_page = 200;
	$paged = isset($_GET['bcc_reindex_page']) ? max(1, (int) $_GET['bcc_reindex_page']) : 1;

	$q = new WP_Query([
		'post_type' => 'product',
		'post_status' => 'any',
		'posts_per_page' => $per_page,
		'paged' => $paged,
		'fields' => 'ids',
		'no_found_rows' => false,
	]);

	$updated = 0;
	foreach ($q->posts as $post_id) {
		$raw = function_exists('carbon_get_post_meta')
			? carbon_get_post_meta($post_id, 'product_price')
			: get_post_meta($post_id, 'product_price', true);
		$number = bcc_parse_price_value($raw);
		if ($number === null) {
			delete_post_meta($post_id, 'product_price_num');
			continue;
		}
		update_post_meta($post_id, 'product_price_num', $number);
		$updated++;
	}

	// Если есть ещё страницы — делаем следующий шаг автоматически через редирект.
	$max_pages = (int) $q->max_num_pages;
	if ($paged < $max_pages) {
		$next = add_query_arg([
			'bcc_reindex_prices' => 1,
			'bcc_reindex_page' => $paged + 1,
		], admin_url('edit.php?post_type=product'));
		wp_safe_redirect($next);
		exit;
	}



	$done = add_query_arg([
		'post_type' => 'product',
		'bcc_reindex_done' => 1,
		'bcc_reindex_updated' => $updated,
	], admin_url('edit.php'));
	wp_safe_redirect($done);
	exit;
});

add_action('pre_get_posts', function ($query) {
	if (is_admin() || !$query->is_main_query()) {
		return;
	}

	if (!($query->is_post_type_archive('product') || $query->is_tax('product_category')))
		return;

	// Фильтр по цене
	$price_from = bcc_get_price_from();
	$price_to = bcc_get_price_to();
	if ($price_from !== null || $price_to !== null) {
		$meta_query = (array) $query->get('meta_query');
		$key_group = [];
		// Сначала пробуем числовое поле, но оставляем фолбэк на старое текстовое.
		$keys = ['product_price_num', 'product_price'];
		if ($price_from !== null && $price_to !== null) {
			if ($price_from > $price_to) {
				$tmp = $price_from;
				$price_from = $price_to;
				$price_to = $tmp;
			}
			foreach ($keys as $k) {
				$key_group[] = [
					'key' => $k,
					'value' => [$price_from, $price_to],
					'compare' => 'BETWEEN',
					'type' => 'NUMERIC',
				];
			}
		} elseif ($price_from !== null) {
			foreach ($keys as $k) {
				$key_group[] = [
					'key' => $k,
					'value' => $price_from,
					'compare' => '>=',
					'type' => 'NUMERIC',
				];
			}
		} elseif ($price_to !== null) {
			foreach ($keys as $k) {
				$key_group[] = [
					'key' => $k,
					'value' => $price_to,
					'compare' => '<=',
					'type' => 'NUMERIC',
				];
			}
		}

		if (!empty($key_group)) {
			$meta_query[] = array_merge(['relation' => 'OR'], $key_group);
		}
		$query->set('meta_query', $meta_query);
	}

	$sort = bcc_get_current_catalog_sort();

	switch ($sort) {
		case 'popular':
			$query->set('orderby', 'comment_count');
			$query->set('order', 'DESC');
			break;

		case 'cheap':
		case 'exp':
			// Используем meta_key + meta_value_num для сортировки
			// WordPress должен обработать это стандартным образом
			global $wpdb;
			$order = $sort === 'cheap' ? 'ASC' : 'DESC';

			error_log("BCC: Setting sort to $sort (order=$order)");
			
			// Очищаем стандартный orderby
			$query->set('orderby', '');
			
			// Используем стандартный WP синтаксис для сортировки по мета
			$query->set('meta_key', 'product_price_num');
			$query->set('meta_type', 'NUMERIC');
			$query->set('orderby', 'meta_value_num');
			$query->set('order', $order);
			
			error_log("BCC: meta_key=product_price_num, meta_type=NUMERIC, orderby=meta_value_num, order={$order}");
			break;

		case 'default':
		default:
			// Оставляем дефолтный порядок WordPress
			break;
	}
});
