<?php
/**
 * Функция вывода хлебных крошек
 * 
 * @package BCC_Project
 */

function bcc_breadcrumbs() {
    // Не показываем на главной странице
    if (is_front_page()) {
        return;
    }
    
    $separator = '<li><img src="' . get_template_directory_uri() . '/assets/images/breadgrumb-icon.svg" alt=""></li>';
    $home_title = 'Главная';
    
    echo '<section class="breadgrumb">';
    echo '<ul class="container breadgrumb-container">';
    
    // Ссылка на главную
    echo '<li><a href="' . home_url('/') . '">' . $home_title . '</a></li>';
    echo $separator;
    
    if (is_single()) {
        // Для отдельных записей
        $post_type = get_post_type();
        
        if ($post_type === 'product') {
            // Для товаров - ссылка на архив
            echo '<li><a href="' . get_post_type_archive_link('product') . '">Каталог</a></li>';
            echo $separator;
            
            // Категория товара
            $terms = get_the_terms(get_the_ID(), 'product_category');
            if ($terms && !is_wp_error($terms)) {
                $current_term = $terms[0];
                $max_depth = -1;

                foreach ($terms as $candidate_term) {
                    $candidate_depth = count(get_ancestors($candidate_term->term_id, 'product_category'));
                    if ($candidate_depth > $max_depth) {
                        $max_depth = $candidate_depth;
                        $current_term = $candidate_term;
                    }
                }

                $root_term = bcc_get_root_product_category_term($current_term);
                if ($root_term) {
                    $root_term_link = get_term_link($root_term);
                    if (!is_wp_error($root_term_link)) {
                        echo '<li><a href="' . esc_url($root_term_link) . '">' . esc_html($root_term->name) . '</a></li>';
                        echo $separator;
                    }
                } else {
                    $current_term_link = get_term_link($current_term);
                    if (!is_wp_error($current_term_link)) {
                        echo '<li><a href="' . esc_url($current_term_link) . '">' . esc_html($current_term->name) . '</a></li>';
                        echo $separator;
                    }
                }

            }
            
            // Текущий товар
            echo '<li><span>' . get_the_title() . '</span></li>';
        } elseif ($post_type === 'post') {
            // Для обычных записей
            $category = get_the_category();
            if ($category) {
                echo '<li><a href="' . get_category_link($category[0]->term_id) . '">' . $category[0]->name . '</a></li>';
                echo $separator;
            }
            echo '<li><span>' . get_the_title() . '</span></li>';
        } else {
            // Для других типов записей
            $post_type_obj = get_post_type_object($post_type);
            if ($post_type_obj) {
                echo '<li><a href="' . get_post_type_archive_link($post_type) . '">' . $post_type_obj->labels->name . '</a></li>';
                echo $separator;
            }
            echo '<li><span>' . get_the_title() . '</span></li>';
        }
    } elseif (is_page()) {
        // Для страниц
        if (wp_get_post_parent_id(get_the_ID())) {
            $parent_id = wp_get_post_parent_id(get_the_ID());
            $breadcrumbs = [];
            
            while ($parent_id) {
                $page = get_post($parent_id);
                $breadcrumbs[] = '<li><a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a></li>';
                $parent_id = wp_get_post_parent_id($page->ID);
            }
            
            $breadcrumbs = array_reverse($breadcrumbs);
            foreach ($breadcrumbs as $crumb) {
                echo $crumb;
                echo $separator;
            }
        }
        
        echo '<li><span>' . get_the_title() . '</span></li>';
    } elseif (is_post_type_archive()) {
        // Для архивов типов записей
        $post_type = get_post_type();
        $post_type_obj = get_post_type_object($post_type);
        
        if ($post_type === 'product') {
            echo '<li><span>Каталог</span></li>';
        } elseif ($post_type_obj) {
            echo '<li><span>' . $post_type_obj->labels->name . '</span></li>';
        }
    } elseif (is_tax('product_category')) {
        // Для категорий товаров
        echo '<li><a href="' . get_post_type_archive_link('product') . '">Каталог</a></li>';
        echo $separator;
        
        $term = get_queried_object();

        $root_term = bcc_get_root_product_category_term($term);
        if ($root_term) {
            echo '<li><span>' . esc_html($root_term->name) . '</span></li>';
        } elseif ($term && !is_wp_error($term)) {
            echo '<li><span>' . esc_html($term->name) . '</span></li>';
        }
    } elseif (is_category()) {
        // Для категорий блога
        $category = get_queried_object();
        
        if ($category->parent) {
            $parent_terms = [];
            $parent_id = $category->parent;
            
            while ($parent_id) {
                $parent = get_term($parent_id, 'category');
                $parent_terms[] = '<li><a href="' . get_category_link($parent->term_id) . '">' . $parent->name . '</a></li>';
                $parent_id = $parent->parent;
            }
            
            $parent_terms = array_reverse($parent_terms);
            foreach ($parent_terms as $parent_term) {
                echo $parent_term;
                echo $separator;
            }
        }
        
        echo '<li><span>' . $category->name . '</span></li>';
    } elseif (is_tag()) {
        // Для меток
        echo '<li><span>' . single_tag_title('', false) . '</span></li>';
    } elseif (is_author()) {
        // Для страниц автора
        echo '<li><span>Автор: ' . get_the_author() . '</span></li>';
    } elseif (is_search()) {
        // Для поиска
        echo '<li><span>Результаты поиска: ' . get_search_query() . '</span></li>';
    } elseif (is_404()) {
        // Для 404
        echo '<li><span>Страница не найдена</span></li>';
    } elseif (is_archive()) {
        // Для других архивов
        echo '<li><span>' . get_the_archive_title() . '</span></li>';
    }
    
    echo '</ul>';
    echo '</section>';
}

function bcc_get_root_product_category_term($term) {
    if (!$term || is_wp_error($term)) {
        return null;
    }

    $root = $term;
    while (!empty($root->parent)) {
        $parent = get_term((int) $root->parent, 'product_category');
        if (!$parent || is_wp_error($parent)) {
            break;
        }
        $root = $parent;
    }

    return $root;
}
