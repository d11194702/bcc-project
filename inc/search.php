<?php

/**
 * AJAX Поиск товаров
 */

add_action( 'wp_ajax_bcc_search', 'bcc_ajax_search' );
add_action( 'wp_ajax_nopriv_bcc_search', 'bcc_ajax_search' );

function bcc_ajax_search() {
	// Проверяем nonce для безопасности
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'bcc_search_nonce' ) ) {
		wp_send_json_error( array( 'message' => 'Ошибка безопасности' ) );
	}

	$search_query = isset( $_POST['query'] ) ? sanitize_text_field( $_POST['query'] ) : '';

	if ( strlen( $search_query ) < 2 ) {
		wp_send_json_success( array( 'items' => array() ) );
	}

	// Ищем ТОЛЬКО ТОВАРЫ по названию (не по категориям)
	$args = array(
		'post_type'              => 'product',
		'posts_per_page'         => 12,
		's'                      => $search_query,
		'post_status'            => 'publish',
		'suppress_filters'       => false,
		'fields'                 => 'ids',
	);

	// Отключаем поиск по taxonomies
	add_filter( 'posts_search', function( $search, $query ) {
		global $wpdb;
		
		if ( ! $query->is_search() || ! isset( $_POST['query'] ) ) {
			return $search;
		}

		// Ищем только по названию поста
		$q = $wpdb->esc_like( sanitize_text_field( $_POST['query'] ) );
		$search = ' AND (' . $wpdb->posts . '.post_title LIKE \'%' . $q . '%\' OR ' . $wpdb->posts . '.post_content LIKE \'%' . $q . '%\') ';
		
		return $search;
	}, 10, 2 );

	$query = new WP_Query( $args );
	$items = array();

	if ( $query->have_posts() ) {
		foreach ( $query->posts as $post_id ) {
			$post = get_post( $post_id );
			
			$thumbnail = has_post_thumbnail( $post_id ) ? get_the_post_thumbnail_url( $post_id, 'thumbnail' ) : '';
			$title = $post->post_title;
			$link = get_permalink( $post_id );
			$price = get_post_meta( $post_id, 'product_price', true );

			$items[] = array(
				'id'        => $post_id,
				'title'     => $title,
				'thumbnail' => $thumbnail,
				'price'     => $price,
				'link'      => $link,
			);
		}
	}

	// Убираем фильтр
	remove_filter( 'posts_search', 'bcc_search_filter', 10 );
	wp_reset_postdata();

	wp_send_json_success( array( 'items' => $items ) );
}

/**
 * Добавляем nonce для безопасности AJAX
 */
add_action( 'wp_head', function() {
	if ( ! is_admin() ) {
		echo '<script>var bcc_search_nonce = "' . wp_create_nonce( 'bcc_search_nonce' ) . '";</script>';
	}
} );
