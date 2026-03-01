<?php

// Регистрация меню темы
function bcc_register_menus() {
    register_nav_menus([
        'header_menu' => 'Меню в шапке',
        'header_catalog_menu' => 'Каталог в шапке',
        'footer_menu' => 'Меню в футере',
    ]);
}
add_action('after_setup_theme', 'bcc_register_menus');

function bcc_ru_pluralize(int $number, string $one, string $two, string $many): string {
    $number = abs($number);
    $n10 = $number % 10;
    $n100 = $number % 100;
    if ($n100 >= 11 && $n100 <= 14) {
        return $many;
    }
    if ($n10 === 1) {
        return $one;
    }
    if ($n10 >= 2 && $n10 <= 4) {
        return $two;
    }
    return $many;
}

function bcc_header_catalog_has_items(): bool {
    if (!has_nav_menu('header_catalog_menu')) {
        return false;
    }

    $locations = get_nav_menu_locations();
    $menu_id = $locations['header_catalog_menu'] ?? 0;
    if (!$menu_id) {
        return false;
    }

    $items = wp_get_nav_menu_items($menu_id);
    if (empty($items) || !is_array($items)) {
        return false;
    }

    foreach ($items as $item) {
        if ((int) ($item->menu_item_parent ?? 0) === 0) {
            return true;
        }
    }

    return false;
}

function bcc_get_current_product_category_id(): int {
    if (is_tax('product_category')) {
        return (int) get_queried_object_id();
    }

    if (is_singular('product')) {
        $term_ids = wp_get_post_terms(get_the_ID(), 'product_category', ['fields' => 'ids']);
        if (!is_wp_error($term_ids) && !empty($term_ids)) {
            return (int) $term_ids[0];
        }
    }

    return 0;
}

function bcc_get_product_category_menu_image_url(int $term_id, string $default_url): string {
    $attachment_id = 0;
    if (function_exists('carbon_get_term_meta')) {
        $attachment_id = (int) carbon_get_term_meta($term_id, 'product_category_menu_image');
    }
    if (!$attachment_id) {
        $attachment_id = (int) get_term_meta($term_id, 'thumbnail_id', true);
    }
    if ($attachment_id) {
        $maybe_url = wp_get_attachment_image_url($attachment_id, 'thumbnail');
        if (!empty($maybe_url)) {
            return (string) $maybe_url;
        }
    }

    return $default_url;
}

function bcc_get_products_for_parent_category(int $parent_term_id): array {
    if ($parent_term_id <= 0) {
        return [];
    }

    $products = get_posts([
        'post_type' => 'product',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
        'fields' => 'ids',
        'no_found_rows' => true,
        'tax_query' => [
            [
                'taxonomy' => 'product_category',
                'field' => 'term_id',
                'terms' => [$parent_term_id],
                'include_children' => true,
            ],
        ],
    ]);

    if (empty($products) || !is_array($products)) {
        return [];
    }

    return array_map('intval', $products);
}

function bcc_header_catalog_has_fallback_items(): bool {
    $terms = get_terms([
        'taxonomy' => 'product_category',
        'hide_empty' => true,
        'parent' => 0,
        'number' => 1,
    ]);
    if (!empty($terms) && !is_wp_error($terms)) {
        return true;
    }

    $products = get_posts([
        'post_type' => 'product',
        'post_status' => 'publish',
        'posts_per_page' => 1,
        'fields' => 'ids',
        'no_found_rows' => true,
    ]);

    return !empty($products);
}

function bcc_header_catalog_can_render(): bool {
    return bcc_header_catalog_has_items() || bcc_header_catalog_has_fallback_items();
}

function bcc_render_header_catalog_fallback(): void {
    $default_icon = get_template_directory_uri() . '/assets/images/catalog-icon.svg';
    $arrow_icon = get_template_directory_uri() . '/assets/images/header-catalog-link-icon.svg';

    $current_term_id = bcc_get_current_product_category_id();

    $terms = get_terms([
        'taxonomy' => 'product_category',
        'hide_empty' => true,
        'parent' => 0,
        'orderby' => 'name',
        'order' => 'ASC',
    ]);

    $parents = (!empty($terms) && !is_wp_error($terms)) ? $terms : [];

    // Если категорий нет, но товары есть — покажем 1 колонку "Все товары".
    if (!$parents) {
        $total = (int) wp_count_posts('product')->publish;
        $all_products_url = get_products_archive_url();
        echo '<div class="header-catalog">';
        echo '<ul class="header-catalog__left">';
        echo '<li class="header-catalog__link active">';
        echo '<a href="' . esc_url($all_products_url) . '" class="header-catalog__link-left">';
        echo '<img src="' . esc_url($default_icon) . '" alt="" class="main-img">';
        echo '<div class="text">';
        echo '<h3>' . esc_html__('Все товары', 'bcc-project') . '</h3>';
        echo '<span>[' . esc_html((string) $total) . ' ' . esc_html(bcc_ru_pluralize($total, 'товар', 'товара', 'товаров')) . ']</span>';
        echo '</div></a>';
        echo '<img src="' . esc_url($arrow_icon) . '" alt="" class="icon">';
        echo '</li>';
        echo '</ul>';

        echo '<div class="header-catalog__content">';
        echo '<div class="header-catalog__content-item active"><ul>';
        $all_terms = get_terms([
            'taxonomy' => 'product_category',
            'hide_empty' => true,
            'orderby' => 'name',
            'order' => 'ASC',
        ]);
        foreach ($all_terms as $term) {
            $term_link = get_term_link($term);
            if (is_wp_error($term_link)) {
                continue;
            }
            echo '<li><a href="' . esc_url($term_link) . '">' . esc_html($term->name) . '</a></li>';
        }
        echo '</ul></div>';
        echo '</div>';
        echo '</div>';
        return;
    }

    // Определяем активную родительскую категорию (если открыта дочерняя — подсвечиваем её root).
    $active_parent_index = 0;
    if ($current_term_id) {
        $maybe_term = get_term($current_term_id, 'product_category');
        if ($maybe_term && !is_wp_error($maybe_term)) {
            $root_id = (int) $maybe_term->term_id;
            while (!empty($maybe_term->parent)) {
                $maybe_term = get_term((int) $maybe_term->parent, 'product_category');
                if (!$maybe_term || is_wp_error($maybe_term)) {
                    break;
                }
                $root_id = (int) $maybe_term->term_id;
            }

            foreach ($parents as $idx => $term) {
                if ((int) $term->term_id === $root_id) {
                    $active_parent_index = (int) $idx;
                    break;
                }
            }
        }
    }

    echo '<div class="header-catalog">';
    echo '<ul class="header-catalog__left">';

    foreach ($parents as $idx => $term) {
        $is_active = $idx === $active_parent_index ? ' active' : '';
        $count = (int) ($term->count ?? 0);
        $img = bcc_get_product_category_menu_image_url((int) $term->term_id, $default_icon);
        $parent_link = get_term_link($term);
        $parent_url = is_wp_error($parent_link) ? '#' : $parent_link;

        echo '<li class="header-catalog__link' . $is_active . '">';
        echo '<a href="' . esc_url($parent_url) . '" class="header-catalog__link-left">';
        echo '<img src="' . esc_url($img) . '" alt="" class="main-img">';
        echo '<div class="text">';
        echo '<h3>' . esc_html($term->name) . '</h3>';
        echo '<span>[' . esc_html((string) $count) . ' ' . esc_html(bcc_ru_pluralize($count, 'товар', 'товара', 'товаров')) . ']</span>';
        echo '</div></a>';
        echo '<img src="' . esc_url($arrow_icon) . '" alt="" class="icon">';
        echo '</li>';
    }

    echo '</ul>';
    echo '<div class="header-catalog__content">';

    foreach ($parents as $idx => $term) {
        $is_active = $idx === $active_parent_index ? ' active' : '';
        echo '<div class="header-catalog__content-item' . $is_active . '">';
        echo '<ul>';

        $product_ids = bcc_get_products_for_parent_category((int) $term->term_id);

        if (!empty($product_ids)) {
            foreach ($product_ids as $product_id) {
                $link = get_permalink($product_id);
                $title = get_the_title($product_id);
                if (!$link || $title === '') {
                    continue;
                }
                echo '<li><a href="' . esc_url($link) . '">' . esc_html($title) . '</a></li>';
            }
        } else {
            $parent_link = get_term_link($term);
            if (!is_wp_error($parent_link)) {
                echo '<li><a href="' . esc_url($parent_link) . '">' . esc_html($term->name) . '</a></li>';
            }
        }

        echo '</ul>';
        echo '</div>';
    }

    echo '</div>';
    echo '</div>';
}

function bcc_render_header_catalog(): void {
    if (bcc_header_catalog_has_items()) {
        bcc_render_header_catalog_menu();
        return;
    }

    bcc_render_header_catalog_fallback();
}

/**
 * Рендер выпадающего каталога в шапке.
 *
 * Структура меню:
 * - 1 уровень: пункты левой колонки
 *   - description: текст под заголовком (например: "[10 товаров]")
 *   - attr_title: URL изображения (иконка слева)
 * - 2 уровень: ссылки в правой колонке для соответствующего пункта 1 уровня
 */
function bcc_render_header_catalog_menu(): void {
    if (!bcc_header_catalog_has_items()) {
        return;
    }

    $locations = get_nav_menu_locations();
    $menu_id = $locations['header_catalog_menu'] ?? 0;
    if (!$menu_id) {
        return;
    }

    $items = wp_get_nav_menu_items($menu_id);
    if (empty($items) || !is_array($items)) {
        return;
    }

    $parents = [];
    $children_by_parent = [];

    $current_term_id = 0;
    if (is_tax('product_category')) {
        $current_term_id = (int) get_queried_object_id();
    } elseif (is_singular('product')) {
        $term_ids = wp_get_post_terms(get_the_ID(), 'product_category', ['fields' => 'ids']);
        if (!is_wp_error($term_ids) && !empty($term_ids)) {
            $current_term_id = (int) $term_ids[0];
        }
    }

    foreach ($items as $item) {
        $parent_id = (int) ($item->menu_item_parent ?? 0);
        if ($parent_id === 0) {
            $parents[] = $item;
        } else {
            $children_by_parent[$parent_id][] = $item;
        }
    }

    if (!$parents) {
        return;
    }

    $active_parent_index = 0;
    if ($current_term_id) {
        foreach ($parents as $idx => $parent) {
            if (
                !empty($parent->type) && $parent->type === 'taxonomy'
                && !empty($parent->object) && $parent->object === 'product_category'
                && !empty($parent->object_id)
            ) {
                $menu_term_id = (int) $parent->object_id;
                if ($menu_term_id === $current_term_id || term_is_ancestor_of($menu_term_id, $current_term_id, 'product_category')) {
                    $active_parent_index = (int) $idx;
                    break;
                }
            }
        }
    }

    $default_icon = get_template_directory_uri() . '/assets/images/catalog-icon.svg';
    $arrow_icon = get_template_directory_uri() . '/assets/images/header-catalog-link-icon.svg';

    echo '<div class="header-catalog">';
    echo '<ul class="header-catalog__left">';

    foreach ($parents as $index => $parent) {
        $is_active = $index === $active_parent_index ? ' active' : '';

        $title = isset($parent->title) ? wp_strip_all_tags($parent->title) : '';
        $parent_url = !empty($parent->url) ? $parent->url : '#';

        $image_url = '';
        $desc = '';

        $is_product_category = (
            !empty($parent->type) && $parent->type === 'taxonomy'
            && !empty($parent->object) && $parent->object === 'product_category'
            && !empty($parent->object_id)
        );

        if ($is_product_category) {
            $term_id = (int) $parent->object_id;
            $term = get_term($term_id, 'product_category');
            if ($term && !is_wp_error($term)) {
                $count = (int) ($term->count ?? 0);
                $desc = '[' . $count . ' ' . bcc_ru_pluralize($count, 'товар', 'товара', 'товаров') . ']';

                $attachment_id = 0;
                if (function_exists('carbon_get_term_meta')) {
                    $attachment_id = (int) carbon_get_term_meta($term_id, 'product_category_menu_image');
                }
                if (!$attachment_id) {
                    $attachment_id = (int) get_term_meta($term_id, 'thumbnail_id', true);
                }
                if ($attachment_id) {
                    $maybe_url = wp_get_attachment_image_url($attachment_id, 'thumbnail');
                    if (!empty($maybe_url)) {
                        $image_url = $maybe_url;
                    }
                }
            }
        }

        if ($image_url === '' && !empty($parent->attr_title)) {
            $image_url = $parent->attr_title;
        }
        if ($image_url === '') {
            $image_url = $default_icon;
        }

        if ($desc === '') {
            $desc = isset($parent->description) ? wp_strip_all_tags($parent->description) : '';
        }

        echo '<li class="header-catalog__link' . $is_active . '">';
        echo '<a href="' . esc_url($parent_url) . '" class="header-catalog__link-left">';
        echo '<img src="' . esc_url($image_url) . '" alt="" class="main-img">';
        echo '<div class="text">';
        echo '<h3>' . esc_html($title) . '</h3>';
        if ($desc !== '') {
            echo '<span>' . esc_html($desc) . '</span>';
        }
        echo '</div>';
        echo '</a>';
        echo '<img src="' . esc_url($arrow_icon) . '" alt="" class="icon">';
        echo '</li>';
    }

    echo '</ul>';
    echo '<div class="header-catalog__content">';

    foreach ($parents as $index => $parent) {
        $is_active = $index === $active_parent_index ? ' active' : '';
        echo '<div class="header-catalog__content-item' . $is_active . '">';
        echo '<ul>';

        $is_product_category = (
            !empty($parent->type) && $parent->type === 'taxonomy'
            && !empty($parent->object) && $parent->object === 'product_category'
            && !empty($parent->object_id)
        );

        if ($is_product_category) {
            $product_ids = bcc_get_products_for_parent_category((int) $parent->object_id);
            foreach ($product_ids as $product_id) {
                $link = get_permalink($product_id);
                $title = get_the_title($product_id);
                if (!$link || $title === '') {
                    continue;
                }
                echo '<li><a href="' . esc_url($link) . '">' . esc_html($title) . '</a></li>';
            }
        } else {
            $parent_id = (int) $parent->ID;
            $children = $children_by_parent[$parent_id] ?? [];
            if (!empty($children)) {
                foreach ($children as $child) {
                    $child_title = isset($child->title) ? wp_strip_all_tags($child->title) : '';
                    $child_url = !empty($child->url) ? esc_url($child->url) : '#';
                    echo '<li><a href="' . $child_url . '">' . esc_html($child_title) . '</a></li>';
                }
            }
        }

        echo '</ul>';
        echo '</div>';
    }

    echo '</div>';
    echo '</div>';
}

function bcc_render_mobile_catalog(): void {
    $default_icon = get_template_directory_uri() . '/assets/images/catalog-icon.svg';
    $arrow_icon = get_template_directory_uri() . '/assets/images/header-catalog-link-icon.svg';

    $items_data = [];

    if (bcc_header_catalog_has_items()) {
        $locations = get_nav_menu_locations();
        $menu_id = $locations['header_catalog_menu'] ?? 0;
        $items = $menu_id ? wp_get_nav_menu_items($menu_id) : [];

        if (!empty($items) && is_array($items)) {
            $parents = [];
            $children_by_parent = [];

            foreach ($items as $item) {
                $parent_id = (int) ($item->menu_item_parent ?? 0);
                if ($parent_id === 0) {
                    $parents[] = $item;
                } else {
                    $children_by_parent[$parent_id][] = $item;
                }
            }

            foreach ($parents as $parent) {
                $title = isset($parent->title) ? wp_strip_all_tags($parent->title) : '';
                $image_url = '';
                $desc = '';
                $links = [];

                $is_product_category = (
                    !empty($parent->type) && $parent->type === 'taxonomy'
                    && !empty($parent->object) && $parent->object === 'product_category'
                    && !empty($parent->object_id)
                );

                if ($is_product_category) {
                    $term_id = (int) $parent->object_id;
                    $term = get_term($term_id, 'product_category');
                    if ($term && !is_wp_error($term)) {
                        $count = (int) ($term->count ?? 0);
                        $desc = '[' . $count . ' ' . bcc_ru_pluralize($count, 'товар', 'товара', 'товаров') . ']';
                        $image_url = bcc_get_product_category_menu_image_url($term_id, $default_icon);
                    }
                }

                if ($image_url === '' && !empty($parent->attr_title)) {
                    $image_url = $parent->attr_title;
                }
                if ($image_url === '') {
                    $image_url = $default_icon;
                }
                if ($desc === '') {
                    $desc = isset($parent->description) ? wp_strip_all_tags($parent->description) : '';
                }

                if ($is_product_category) {
                    $product_ids = bcc_get_products_for_parent_category((int) $parent->object_id);
                    foreach ($product_ids as $product_id) {
                        $link = get_permalink($product_id);
                        $title = get_the_title($product_id);
                        if (!$link || $title === '') {
                            continue;
                        }
                        $links[] = [
                            'title' => $title,
                            'url'   => $link,
                        ];
                    }
                } else {
                    $children = $children_by_parent[(int) $parent->ID] ?? [];
                    foreach ($children as $child) {
                        $links[] = [
                            'title' => isset($child->title) ? wp_strip_all_tags($child->title) : '',
                            'url'   => !empty($child->url) ? $child->url : '#',
                        ];
                    }
                }

                $items_data[] = [
                    'title' => $title,
                    'desc'  => $desc,
                    'image' => $image_url,
                    'links' => $links,
                ];
            }
        }
    }

    if (empty($items_data)) {
        $terms = get_terms([
            'taxonomy' => 'product_category',
            'hide_empty' => true,
            'parent' => 0,
            'orderby' => 'name',
            'order' => 'ASC',
        ]);

        if (!empty($terms) && !is_wp_error($terms)) {
            foreach ($terms as $term) {
                $count = (int) ($term->count ?? 0);
                $links = [];

                $product_ids = bcc_get_products_for_parent_category((int) $term->term_id);
                if (!empty($product_ids)) {
                    foreach ($product_ids as $product_id) {
                        $link = get_permalink($product_id);
                        $title = get_the_title($product_id);
                        if (!$link || $title === '') {
                            continue;
                        }
                        $links[] = [
                            'title' => $title,
                            'url'   => $link,
                        ];
                    }
                } else {
                    $term_link = get_term_link($term);
                    if (!is_wp_error($term_link)) {
                        $links[] = [
                            'title' => $term->name,
                            'url'   => $term_link,
                        ];
                    }
                }

                $items_data[] = [
                    'title' => $term->name,
                    'desc'  => '[' . $count . ' ' . bcc_ru_pluralize($count, 'товар', 'товара', 'товаров') . ']',
                    'image' => bcc_get_product_category_menu_image_url((int) $term->term_id, $default_icon),
                    'links' => $links,
                ];
            }
        }
    }

    if (empty($items_data)) {
        return;
    }

    echo '<button class="menu-catalog__btn">';
    echo '<span>';
    echo '<img src="' . esc_url($default_icon) . '" alt="">';
    echo '<span>Каталог</span>';
    echo '</span>';
    echo '<img src="' . esc_url($arrow_icon) . '" alt="" class="icon">';
    echo '</button>';

    echo '<ul class="menu-catalog">';
    foreach ($items_data as $item) {
        echo '<li>';
        echo '<img src="' . esc_url($item['image']) . '" alt="">';
        echo '<div class="text">';
        echo '<b>' . esc_html($item['title']) . '</b>';
        if (!empty($item['desc'])) {
            echo '<span>' . esc_html($item['desc']) . '</span>';
        }
        echo '</div>';
        echo '<img src="' . esc_url($arrow_icon) . '" alt="" class="icon">';
        echo '</li>';
    }
    echo '</ul>';

    echo '<div class="menu-catalog__content">';
    foreach ($items_data as $item) {
        echo '<ul class="menu-catalog__content-item">';
        if (!empty($item['links'])) {
            foreach ($item['links'] as $link) {
                echo '<li><a href="' . esc_url($link['url']) . '">' . esc_html($link['title']) . '</a></li>';
            }
        }
        echo '</ul>';
    }
    echo '</div>';
}

// Custom Walker для очистки классов и ID WordPress
class BCC_Clean_Walker extends Walker_Nav_Menu {
    
    // Убираем ID у элементов
    function start_lvl(&$output, $depth = 0, $args = null) {
        $indent = str_repeat("\t", $depth);
        $output .= "\n$indent<ul class=\"sub-menu\">\n";
    }
    
    // Убираем классы и ID у элементов меню
    function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $indent = ($depth) ? str_repeat("\t", $depth) : '';
        
        $classes = empty($item->classes) ? [] : (array) $item->classes;
        
        // Оставляем только кастомные классы (не начинающиеся с menu-)
        $custom_classes = array_filter($classes, function($class) {
            return strpos($class, 'menu-') !== 0 && 
                   strpos($class, 'current-') !== 0 &&
                   $class !== 'page_item' &&
                   $class !== 'page-item' &&
                   !is_numeric($class);
        });
        
        $class_names = join(' ', $custom_classes);
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';
        
        $output .= $indent . '<li' . $class_names . '>';
        
        $atts = [];
        $atts['href'] = !empty($item->url) ? $item->url : '';
        
        // Добавляем класс для ссылки из аргументов
        if (isset($args->link_class)) {
            $atts['class'] = $args->link_class;
        }
        
        // Добавляем target если задан
        if (!empty($item->target)) {
            $atts['target'] = $item->target;
        }
        
        // Применяем фильтры
        $atts = apply_filters('nav_menu_link_attributes', $atts, $item, $args, $depth);
        
        $attributes = '';
        foreach ($atts as $attr => $value) {
            if (!empty($value)) {
                $value = ('href' === $attr) ? esc_url($value) : esc_attr($value);
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }
        
        $item_output = $args->before ?? '';
        $item_output .= '<a' . $attributes . '>';
        $item_output .= ($args->link_before ?? '') . apply_filters('the_title', $item->title, $item->ID) . ($args->link_after ?? '');
        $item_output .= '</a>';
        $item_output .= $args->after ?? '';
        
        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }
}
