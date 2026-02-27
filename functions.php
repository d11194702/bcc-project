<?php

// Подключение файлов из inc
require_once get_template_directory() . '/inc/theme-helpers.php';
require_once get_template_directory() . '/inc/assets.php';
require_once get_template_directory() . '/inc/carbon-fields.php';
require_once get_template_directory() . '/inc/cpt.php';
require_once get_template_directory() . '/inc/theme-menu.php';
require_once get_template_directory() . '/inc/breadcrumbs.php';
require_once get_template_directory() . '/inc/filters.php';
require_once get_template_directory() . '/inc/search.php';
require_once get_template_directory() . '/inc/inquiry-form.php';

add_action('after_setup_theme', function () {
	add_theme_support('title-tag');
	add_theme_support('post-thumbnails');
	add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script']);
});

/**
 * Полное отключение комментариев на сайте
 */
// Отключить поддержку комментариев для всех типов записей
add_action('admin_init', function () {
	$post_types = get_post_types();
	foreach ($post_types as $post_type) {
		if (post_type_supports($post_type, 'comments')) {
			remove_post_type_support($post_type, 'comments');
			remove_post_type_support($post_type, 'trackbacks');
		}
	}
});

// Закрыть комментарии на фронтенде
add_filter('comments_open', '__return_false', 20, 2);
add_filter('pings_open', '__return_false', 20, 2);

// Скрыть существующие комментарии
add_filter('comments_array', '__return_empty_array', 10, 2);

// Удалить пункт "Комментарии" из меню админки
add_action('admin_menu', function () {
	remove_menu_page('edit-comments.php');
});

// Удалить виджет комментариев из дашборда
add_action('wp_dashboard_setup', function () {
	remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
});

// Убрать комментарии из админ-бара
add_action('wp_before_admin_bar_render', function () {
	global $wp_admin_bar;
	$wp_admin_bar->remove_menu('comments');
});

/**
 * Сброс правил перезаписи URL при активации темы
 */
add_action('after_switch_theme', function() {
	flush_rewrite_rules();
});
