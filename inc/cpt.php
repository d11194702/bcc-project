<?php

/**
 * Регистрация типа записи "Товары"
 */
add_action('init', 'register_product_post_type');
function register_product_post_type() {
	$labels = [
		'name'               => 'Товары',
		'singular_name'      => 'Товар',
		'add_new'            => 'Добавить товар',
		'add_new_item'       => 'Добавить новый товар',
		'edit_item'          => 'Редактировать товар',
		'new_item'           => 'Новый товар',
		'view_item'          => 'Посмотреть товар',
		'search_items'       => 'Найти товар',
		'not_found'          => 'Товары не найдены',
		'not_found_in_trash' => 'В корзине товары не найдены',
		'menu_name'          => 'Товары',
	];

	$args = [
		'labels'              => $labels,
		'public'              => true,
		'publicly_queryable'  => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'query_var'           => true,
		'rewrite'             => [
			'slug'       => 'products/%product_category%',
			'with_front' => false,
		],
		'capability_type'     => 'post',
		'has_archive'         => 'products',
		'hierarchical'        => false,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-products',
		'supports'            => ['title', 'editor', 'thumbnail', 'excerpt'],
		'show_in_rest'        => true,
	];

	register_post_type('product', $args);
}

/**
 * Регистрация таксономии "Категории товаров"
 */
add_action('init', 'register_product_taxonomy');
function register_product_taxonomy() {
	$labels = [
		'name'              => 'Категории товаров',
		'singular_name'     => 'Категория товаров',
		'search_items'      => 'Найти категорию',
		'all_items'         => 'Все категории',
		'parent_item'       => 'Родительская категория',
		'parent_item_colon' => 'Родительская категория:',
		'edit_item'         => 'Редактировать категорию',
		'update_item'       => 'Обновить категорию',
		'add_new_item'      => 'Добавить новую категорию',
		'new_item_name'     => 'Название новой категории',
		'menu_name'         => 'Категории',
	];

	$args = [
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => ['slug' => 'product-category'],
		'show_in_rest'      => true,
	];

	register_taxonomy('product_category', ['product'], $args);
}

/**
 * Регистрация таксономии "Цвета" для товаров
 */
add_action('init', 'register_product_colors_taxonomy');
function register_product_colors_taxonomy() {
	$labels = [
		'name'              => 'Цвета',
		'singular_name'     => 'Цвет',
		'search_items'      => 'Найти цвет',
		'all_items'         => 'Все цвета',
		'edit_item'         => 'Редактировать цвет',
		'update_item'       => 'Обновить цвет',
		'add_new_item'      => 'Добавить новый цвет',
		'new_item_name'     => 'Название нового цвета',
		'menu_name'         => 'Цвета',
	];

	$args = [
		'hierarchical'        => false,
		'labels'              => $labels,
		'public'              => false,
		'publicly_queryable'  => false,
		'show_ui'             => true,
		'show_admin_column'   => true,
		'show_in_nav_menus'   => false,
		'show_tagcloud'       => false,
		'query_var'           => false,
		'rewrite'             => false,
		'show_in_rest'        => false,
	];

	register_taxonomy('product_color', ['product'], $args);
}

/**
 * Синхронизация выбора цветов из Carbon Fields (association) в таксономию.
 */
add_action('save_post_product', function ($post_id) {
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return;
	}
	if (wp_is_post_revision($post_id)) {
		return;
	}
	if (!current_user_can('edit_post', $post_id)) {
		return;
	}
	if (!function_exists('carbon_get_post_meta')) {
		return;
	}

	$assoc = carbon_get_post_meta($post_id, 'product_palette_colors');
	$term_ids = [];
	if (is_array($assoc)) {
		foreach ($assoc as $row) {
			if (is_array($row) && isset($row['id'])) {
				$term_ids[] = (int) $row['id'];
			}
		}
	}
	$term_ids = array_values(array_unique(array_filter($term_ids)));

	wp_set_object_terms($post_id, $term_ids, 'product_color', false);
}, 20);

/**
 * Подставляет полный путь категории в permalink товара.
 */
add_filter('post_type_link', 'bcc_product_permalink', 10, 2);
function bcc_product_permalink($permalink, $post) {
	if (!isset($post->post_type) || 'product' !== $post->post_type) {
		return $permalink;
	}

	$terms = wp_get_post_terms($post->ID, 'product_category');
	if (is_wp_error($terms) || empty($terms)) {
		return str_replace('%product_category%', 'no-category', $permalink);
	}

	$category = $terms[0];
	$max_depth = 0;
	foreach ($terms as $term_item) {
		$ancestors = get_ancestors($term_item->term_id, 'product_category');
		$depth = count($ancestors);
		if ($depth > $max_depth) {
			$max_depth = $depth;
			$category = $term_item;
		}
	}

	$ancestors = array_reverse(get_ancestors($category->term_id, 'product_category'));
	$slugs = [];

	foreach ($ancestors as $ancestor_id) {
		$parent = get_term($ancestor_id, 'product_category');
		if (!is_wp_error($parent) && !empty($parent->slug)) {
			$slugs[] = $parent->slug;
		}
	}

	$slugs[] = $category->slug;

	return str_replace('%product_category%', implode('/', $slugs), $permalink);
}

/**
 * Обрабатывает иерархические URL товаров.
 */
add_action('init', function () {
	add_rewrite_rule(
		'^products/(.+)/([^/]+)/?$',
		'index.php?product=$matches[2]',
		'top'
	);
});

add_filter('query_vars', function ($vars) {
	$vars[] = 'bcc_parent_cat';
	return $vars;
});

add_action('init', function () {
	add_rewrite_rule(
		'^products/([^/]+)/?$',
		'index.php?post_type=product&bcc_parent_cat=$matches[1]',
		'top'
	);
}, 15);

add_action('init', function () {
	$rewrite_version = get_option('bcc_products_rewrite_version', '');
	if ('2026-02-27-2' !== $rewrite_version) {
		flush_rewrite_rules(false);
		update_option('bcc_products_rewrite_version', '2026-02-27-2', false);
	}
}, 20);

/**
 * Drag-and-drop иерархии категорий товаров в админке.
 */
add_action('admin_enqueue_scripts', function ($hook) {
	if ('edit-tags.php' !== $hook) {
		return;
	}

	$taxonomy = isset($_GET['taxonomy']) ? sanitize_key(wp_unslash($_GET['taxonomy'])) : '';
	if ('product_category' !== $taxonomy) {
		return;
	}

	$script_path = get_template_directory() . '/assets/js/admin-product-category-dnd.js';
	$script_url = get_template_directory_uri() . '/assets/js/admin-product-category-dnd.js';
	$version = file_exists($script_path) ? (string) filemtime($script_path) : '1.0.0';

	wp_enqueue_script('bcc-product-category-dnd', $script_url, [], $version, true);
	wp_localize_script('bcc-product-category-dnd', 'bccProductCategoryDnd', [
		'ajaxUrl' => admin_url('admin-ajax.php'),
		'nonce' => wp_create_nonce('bcc_product_category_dnd'),
	]);
});

add_action('wp_ajax_bcc_set_product_category_parent', function () {
	if (!current_user_can('manage_categories')) {
		wp_send_json_error(['message' => 'Недостаточно прав.'], 403);
	}

	check_ajax_referer('bcc_product_category_dnd', 'nonce');

	$term_id = isset($_POST['term_id']) ? (int) $_POST['term_id'] : 0;
	$new_parent = isset($_POST['new_parent']) ? (int) $_POST['new_parent'] : 0;

	if ($term_id <= 0) {
		wp_send_json_error(['message' => 'Некорректный term_id.'], 400);
	}

	if ($term_id === $new_parent) {
		wp_send_json_error(['message' => 'Категория не может быть родителем самой себя.'], 400);
	}

	if ($new_parent > 0) {
		$parent_term = get_term($new_parent, 'product_category');
		if (!$parent_term || is_wp_error($parent_term)) {
			wp_send_json_error(['message' => 'Родительская категория не найдена.'], 404);
		}

		$parent_ancestors = get_ancestors($new_parent, 'product_category');
		if (in_array($term_id, $parent_ancestors, true)) {
			wp_send_json_error(['message' => 'Нельзя вложить категорию в собственную подкатегорию.'], 400);
		}
	}

	$result = wp_update_term($term_id, 'product_category', [
		'parent' => $new_parent,
	]);

	if (is_wp_error($result)) {
		wp_send_json_error(['message' => $result->get_error_message()], 400);
	}

	wp_send_json_success(['message' => 'Иерархия обновлена.']);
});

add_action('template_redirect', function () {
	if (!is_post_type_archive('product')) {
		return;
	}

	if (!isset($_GET['parent_cat'])) {
		return;
	}

	$raw_parent = sanitize_text_field(wp_unslash($_GET['parent_cat']));
	if ($raw_parent === '') {
		return;
	}

	$term = null;
	if (is_numeric($raw_parent)) {
		$term = get_term((int) $raw_parent, 'product_category');
	} else {
		$term = get_term_by('slug', sanitize_title($raw_parent), 'product_category');
	}

	if (!$term || is_wp_error($term)) {
		return;
	}

	wp_safe_redirect(trailingslashit(home_url('/products/' . $term->slug)), 301);
	exit;
});
