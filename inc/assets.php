<?php

/**
 * Подключение стилей и скриптов
 */
add_action('wp_enqueue_scripts', function () {
	$theme_dir = get_template_directory();
	
	// Основные стили
	$style_version = filemtime($theme_dir . '/assets/scss/style.css');
	wp_enqueue_style('bcc-project-style', get_template_directory_uri() . '/assets/scss/style.css', [], $style_version);
	
	// Swiper CSS
	wp_enqueue_style('swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css', [], '11.0.0');
	
	// FancyBox CSS
	wp_enqueue_style('fancybox', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css', [], '5.0.0');
	
	// Библиотека масок
	$imask_version = filemtime($theme_dir . '/assets/js/iMask.js');
	wp_enqueue_script('imask', get_template_directory_uri() . '/assets/js/iMask.js', [], $imask_version, true);
	
	// Swiper JS
	wp_enqueue_script('swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', [], '11.0.0', true);
	
	// FancyBox JS
	wp_enqueue_script('fancybox', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js', [], '5.0.0', true);
	
	// Основной скрипт
	$main_version = filemtime($theme_dir . '/assets/js/main.js');
	wp_enqueue_script('bcc-project-main', get_template_directory_uri() . '/assets/js/main.js', ['jquery', 'imask', 'swiper'], $main_version, true);
	
	// Поиск функционал
	$search_version = filemtime($theme_dir . '/assets/js/search-function.js');
	wp_enqueue_script('bcc-project-search', get_template_directory_uri() . '/assets/js/search-function.js', [], $search_version, true);
	
	// Передаём URL для AJAX
	wp_localize_script('bcc-project-search', 'ajaxurl', admin_url('admin-ajax.php'));
	wp_localize_script('bcc-project-search', 'bccDefaultImage', get_template_directory_uri() . '/assets/images/placeholder.png');
});