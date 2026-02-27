<?php

/**
 * Helper функции для получения контактов и информации темы
 * Используют Carbon Fields с fallback на стандартные WP функции
 */

// РАЗРЕШЕНИЕ ЗАГРУЗКИ SVG

/**
 * Разрешить загрузку SVG файлов в медиа-библиотеку
 */
add_filter( 'upload_mimes', function( $mimes ) {
	$mimes['svg']  = 'image/svg+xml';
	$mimes['svgz'] = 'image/svg+xml';
	return $mimes;
} );

/**
 * Разрешить SVG в wp_get_attachment_image() и других функциях
 */
add_filter( 'wp_check_filetype_and_ext', function( $data, $file, $filename, $mimes ) {
	$filetype = wp_check_filetype( $filename, $mimes );
	
	// Если файл SVG
	if ( false === $filetype['type'] && in_array( strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) ), array( 'svg', 'svgz' ) ) ) {
		$data['ext']  = 'svg';
		$data['type'] = 'image/svg+xml';
	}
	
	return $data;
}, 10, 4 );

/**
 * Непостоянный фиксер — разрешает загрузку SVG в админке
 * (добавляем в whitelist)
 */
add_filter( 'wp_prepare_attachment_for_js', function( $response, $attachment, $meta ) {
	if ( isset( $response['mime'] ) && 'image/svg+xml' === $response['mime'] ) {
		$response['type']  = 'image';
		$response['icon']  = wp_mime_type_icon( $response['mime'] );
		$response['width']  = 100; // По умолчанию
		$response['height'] = 100;
	}
	return $response;
}, 10, 3 );


/**
 * Получить основной номер телефона
 * @param bool $return_field - Если true, возвращает значение из specified контейнера
 */
function get_theme_phone( $source = 'main' ) {
	$option_key = $source === 'header' ? 'theme_header_phone' : 'theme_phone';
	
	// Пробуем Carbon Fields
	if ( function_exists( 'carbon_get_theme_option' ) ) {
		$phone = carbon_get_theme_option( $option_key );
		if ( $phone ) {
			return $phone;
		}
	}
	
	// Fallback для header — берём основной номер если header не заполнен
	if ( $source === 'header' ) {
		if ( function_exists( 'carbon_get_theme_option' ) ) {
			return carbon_get_theme_option( 'theme_phone' );
		}
	}
	
	// Стандартный способ
	return get_option( $option_key );
}

/**
 * Получить основной email
 */
function get_theme_email( $source = 'main' ) {
	$option_key = $source === 'header' ? 'theme_header_email' : 'theme_email';
	
	if ( function_exists( 'carbon_get_theme_option' ) ) {
		$email = carbon_get_theme_option( $option_key );
		if ( $email ) {
			return $email;
		}
	}
	
	// Fallback для header
	if ( $source === 'header' ) {
		if ( function_exists( 'carbon_get_theme_option' ) ) {
			return carbon_get_theme_option( 'theme_email' );
		}
	}
	
	return get_option( $option_key );
}

/**
 * Получить альтернативный номер телефона
 */
function get_theme_phone_alt() {
	if ( function_exists( 'carbon_get_theme_option' ) ) {
		return carbon_get_theme_option( 'theme_phone_alt' );
	}
	return get_option( 'theme_phone_alt' );
}

/**
 * Получить альтернативный email
 */
function get_theme_email_alt() {
	if ( function_exists( 'carbon_get_theme_option' ) ) {
		return carbon_get_theme_option( 'theme_email_alt' );
	}
	return get_option( 'theme_email_alt' );
}

/**
 * Получить адрес компании
 */
function get_theme_address( $source = 'main' ) {
	$option_key = $source === 'footer' ? 'theme_footer_address' : 'theme_address';
	
	if ( function_exists( 'carbon_get_theme_option' ) ) {
		$address = carbon_get_theme_option( $option_key );
		if ( $address ) {
			return $address;
		}
	}
	
	// Fallback для footer
	if ( $source === 'footer' ) {
		if ( function_exists( 'carbon_get_theme_option' ) ) {
			return carbon_get_theme_option( 'theme_address' );
		}
	}
	
	return get_option( $option_key );
}

/**
 * Получить город
 */
function get_theme_city() {
	if ( function_exists( 'carbon_get_theme_option' ) ) {
		return carbon_get_theme_option( 'theme_city' );
	}
	return get_option( 'theme_city' );
}

/**
 * Получить почтовый индекс
 */
function get_theme_zip() {
	if ( function_exists( 'carbon_get_theme_option' ) ) {
		return carbon_get_theme_option( 'theme_zip' );
	}
	return get_option( 'theme_zip' );
}

/**
 * Получить информацию для эконом (хедер)
 */
function get_theme_header_info() {
	if ( function_exists( 'carbon_get_theme_option' ) ) {
		return carbon_get_theme_option( 'theme_header_info' );
	}
	return get_option( 'theme_header_info' );
}

/**
 * Получить логотип (ID или URL)
 */
function get_theme_header_logo_id() {
	if ( function_exists( 'carbon_get_theme_option' ) ) {
		return carbon_get_theme_option( 'theme_header_logo' );
	}
	return get_option( 'theme_header_logo' );
}

/**
 * Получить логотип UID
 */
function get_theme_header_logo_url() {
	$image_id = get_theme_header_logo_id();
	if ( ! $image_id ) {
		return '';
	}
	
	$image = wp_get_attachment_image_src( $image_id, 'full' );
	return $image ? $image[0] : '';
}

/**
 * Получить текст логотипа в хедере
 */
function get_theme_header_logo_text() {
	if ( function_exists( 'carbon_get_theme_option' ) ) {
		return carbon_get_theme_option( 'theme_header_logo_text' );
	}
	return get_option( 'theme_header_logo_text' );
}

/**
 * ФУТЕР: Copyright текст
 */
function get_theme_footer_copyright() {
	if ( function_exists( 'carbon_get_theme_option' ) ) {
		return carbon_get_theme_option( 'theme_footer_copyright' );
	}
	return get_option( 'theme_footer_copyright' );
}

/**
 * ФУТЕР: Информация (левая колонка)
 */
function get_theme_footer_info() {
	if ( function_exists( 'carbon_get_theme_option' ) ) {
		return carbon_get_theme_option( 'theme_footer_info' );
	}
	return get_option( 'theme_footer_info' );
}

/**
 * ФУТЕР: Номер телефона
 */
function get_theme_footer_phone() {
	return get_theme_phone( 'footer' );
}

/**
 * ФУТЕР: Email
 */
function get_theme_footer_email() {
	return get_theme_email( 'footer' );
}

/**
 * ФУТЕР: Дополнительные ссылки
 */
function get_theme_footer_links() {
	if ( function_exists( 'carbon_get_theme_option' ) ) {
		return carbon_get_theme_option( 'theme_footer_links' );
	}
	return get_option( 'theme_footer_links' );
}

/**
 * ФУТЕР: Рейтинги (блок отзывов)
 */
function get_theme_footer_ratings() {
	if ( function_exists( 'carbon_get_theme_option' ) ) {
		return carbon_get_theme_option( 'theme_footer_ratings' );
	}
	return get_option( 'theme_footer_ratings' );
}

/**
 * ФУТЕР: Адрес
 */
function get_theme_footer_address() {
	return get_theme_address( 'footer' );
}

/**
 * ХЕДЕР: Номер телефона
 */
function get_theme_header_phone() {
	return get_theme_phone( 'header' );
}

/**
 * ХЕДЕР: Email
 */
function get_theme_header_email() {
	return get_theme_email( 'header' );
}

// СОЦИАЛЬНЫЕ СЕТИ

/**
 * Получить VK профиль
 */
function get_theme_vk() {
	if ( function_exists( 'carbon_get_theme_option' ) ) {
		return carbon_get_theme_option( 'theme_vk' );
	}
	return get_option( 'theme_vk' );
}

/**
 * Получить WhatsApp номер
 */
function get_theme_whatsapp() {
	if ( function_exists( 'carbon_get_theme_option' ) ) {
		return carbon_get_theme_option( 'theme_whatsapp' );
	}
	return get_option( 'theme_whatsapp' );
}

/**
 * Получить WhatsApp ссылку (готовую для href)
 */
function get_theme_whatsapp_link() {
	$phone = get_theme_whatsapp();
	if ( ! $phone ) {
		return '';
	}
	// Убираем все кроме цифр и +
	$phone = preg_replace( '/[^\d+]/', '', $phone );
	return 'https://wa.me/' . $phone;
}

/**
 * Получить Telegram
 */
function get_theme_telegram() {
	if ( function_exists( 'carbon_get_theme_option' ) ) {
		return carbon_get_theme_option( 'theme_telegram' );
	}
	return get_option( 'theme_telegram' );
}

/**
 * Получить URL архива товаров
 */
function get_products_archive_url() {
	return get_post_type_archive_link( 'product' );
}

// SVG ФУНКЦИИ

/**
 * Загрузить и вывести SVG файл inline (встроенный в HTML)
 * 
 * @param string $filename Имя файла без расширения (например: 'arrow-down')
 * @param string $folder Папка относительно assets (по умолчанию 'images')
 * @param array $attrs Массив атрибутов для SVG (class, alt, data-* и т.д.)
 * 
 * @return string SVG код или пустая строка если файл не найден
 * 
 * Примеры:
 * echo get_svg( 'arrow-down' );
 * echo get_svg( 'logo', 'images', array( 'class' => 'header-logo', 'alt' => 'Logo' ) );
 * echo get_svg( 'icon-phone', 'images/icons' );
 */
function get_svg( $filename, $folder = 'images', $attrs = array() ) {
	// Санизируем имя файла (только буквы, цифры, дефис, подчеркивание)
	$filename = sanitize_file_name( $filename );
	$filename = preg_replace( '/[^a-z0-9_-]/i', '', $filename );
	
	if ( empty( $filename ) ) {
		return '';
	}
	
	// Строим путь к файлу
	$svg_path = get_template_directory() . '/assets/' . trim( $folder, '/' ) . '/' . $filename . '.svg';
	
	// Проверяем что файл существует и находится в папке темы
	if ( ! file_exists( $svg_path ) || ! is_file( $svg_path ) ) {
		return '';
	}
	
	// Загружаем содержимое SVG
	$svg_content = file_get_contents( $svg_path );
	
	if ( ! $svg_content ) {
		return '';
	}
	
	// Если нужно добавить атрибуты к SVG
	if ( ! empty( $attrs ) && is_array( $attrs ) ) {
		// Ищем открывающий тег <svg
		if ( preg_match( '/<svg\s+/i', $svg_content ) ) {
			$attr_string = '';
			foreach ( $attrs as $key => $value ) {
				// Санизируем ключ атрибута
				$key = preg_replace( '/[^a-z0-9_-]/i', '', $key );
				// Экранируем значение
				$value = esc_attr( $value );
				$attr_string .= ' ' . $key . '="' . $value . '"';
			}
			
			// Добавляем атрибуты к открывающему тегу SVG
			$svg_content = preg_replace( '/<svg\s+/i', '<svg ' . $attr_string . ' ', $svg_content );
		}
	}
	
	return $svg_content;
}

/**
 * Вывести SVG файл без экранирования (ВАЖНО: используй только для SVG из папки assets!)
 * 
 * @param string $filename Имя файла без расширения
 * @param string $folder Папка относительно assets
 * @param array $attrs Массив атрибутов
 */
function the_svg( $filename, $folder = 'images', $attrs = array() ) {
	echo wp_kses_post( get_svg( $filename, $folder, $attrs ) );
}

/**
 * Получить URL к SVG файлу (для использования в src, href и т.д.)
 * 
 * @param string $filename Имя файла без расширения
 * @param string $folder Папка относительно assets
 * 
 * @return string URL к SVG файлу
 * 
 * Пример: <img src="<?php echo get_svg_url( 'arrow-down' ); ?>" alt="Arrow">
 */
function get_svg_url( $filename, $folder = 'images' ) {
	$filename = sanitize_file_name( $filename );
	$filename = preg_replace( '/[^a-z0-9_-]/i', '', $filename );
	
	if ( empty( $filename ) ) {
		return '';
	}
	
	// Проверяем что файл существует
	$svg_path = get_template_directory() . '/assets/' . trim( $folder, '/' ) . '/' . $filename . '.svg';
	if ( ! file_exists( $svg_path ) ) {
		return '';
	}
	
	// Возвращаем URL
	return get_template_directory_uri() . '/assets/' . trim( $folder, '/' ) . '/' . $filename . '.svg';
}
