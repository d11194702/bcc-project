<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php wp_title('|', true, 'right'); ?><?php bloginfo('name'); ?></title>
    <?php
    // SEO мета-теги из Carbon Fields
    if (is_singular()) {
        $post_id = get_the_ID();
        
        // Получаем SEO поля из Carbon Fields
        $seo_title = carbon_get_post_meta($post_id, 'seo_title');
        $seo_description = carbon_get_post_meta($post_id, 'seo_description');
        $seo_keywords = carbon_get_post_meta($post_id, 'seo_keywords');
        
        // Fallback на обычный контент если SEO поля пустые
        if (empty($seo_title)) {
            $seo_title = get_the_title($post_id);
        }
        
        if (empty($seo_description)) {
            $excerpt = get_the_excerpt($post_id);
            if (empty($excerpt)) {
                // Если нет excerpt, берем первые 160 символов контента
                $content = get_post_field('post_content', $post_id);
                $content = strip_tags($content);
                $content = wp_trim_words($content, 25, '...');
                $seo_description = $content;
            } else {
                $seo_description = $excerpt;
            }
        }
        
        // Выводим мета-теги
        if (!empty($seo_title)) {
            echo '<meta name="title" content="' . esc_attr($seo_title) . '">' . "\n    ";
        }
        if (!empty($seo_description)) {
            echo '<meta name="description" content="' . esc_attr($seo_description) . '">' . "\n    ";
        }
        if (!empty($seo_keywords)) {
            echo '<meta name="keywords" content="' . esc_attr($seo_keywords) . '">' . "\n    ";
        }
    }
    ?>
    <?php wp_head(); ?>
</head>
<body>
    
    <div class="wrapper">

        <!-- Header -->
        <header class="header">
            <div class="container header-container">
                <div class="header-left">
                    <?php
                        $header_logo_text = get_theme_header_logo_text();                      
                    ?>
                    <a href="<?php echo home_url('/'); ?>" class="header-logo">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo.svg" alt="">
                        <span><?php echo esc_html( $header_logo_text ); ?></span>
                    </a>
                    <ul class="header-nav">
                        <?php if (function_exists('bcc_header_catalog_can_render') && bcc_header_catalog_can_render()) : ?>
                            <li class="header-catalog__wrap">
                                <button class="header-nav__link header-ctalog__open">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/catalog-icon.svg" alt="">
                                    <span>Каталог</span>
                                </button>
                                <?php bcc_render_header_catalog(); ?>
                            </li>
                        <?php endif; ?>
                        <?php
                        wp_nav_menu([
                            'theme_location' => 'header_menu',
                            'container' => false,
                            'items_wrap' => '%3$s',
                            'walker' => new BCC_Clean_Walker(),
                            'link_class' => 'header-nav__link',
                            'fallback_cb' => false,
                        ]);
                        
                        // Добавляем пункт "Товары" если это архив товаров или его категория
                        if ( is_post_type_archive( 'product' ) || is_tax( 'product_category' ) ) {
                            echo '<li><a href="' . esc_url( get_products_archive_url() ) . '" class="header-nav__link' . ( is_post_type_archive( 'product' ) && ! is_tax() ? ' active' : '' ) . '">Товары</a></li>';
                        }
                        ?>
                    </ul>
                </div>
                <div class="search">
                    <div class="search-head">
                        <input type="text" placeholder="Поиск по товарам">
                        <button class="clear">
                            <svg width="29" height="29" viewBox="0 0 29 29" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7.615 21.7571L6.52714 20.6693L13.0543 14.1421L6.52714 7.615L7.615 6.52714L14.1421 13.0543L20.6693 6.52714L21.7571 7.615L15.23 14.1421L21.7571 20.6693L20.6693 21.7571L14.1421 15.23L7.615 21.7571Z" fill="#878787" />
                            </svg>
                        </button>
                    </div>
                    <div class="search-result__wrap">
                        <ul class="search-result"></ul>
                        <div class="more-link">
                            <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Результаты поиска</a>
                        </div>
                    </div>
                </div>
                <div class="header-right">
                    <div class="header-text">
                        <?php 
                            $header_address = get_theme_header_info();
                            if ( ! $header_address ) {
                                $header_address = get_theme_address();
                            }
                            if ( $header_address ) {
                                echo '<p>' . wp_kses_post( $header_address ) . '</p>';
                            }
                        ?>
                        <p>
                            <?php 
                                $header_phone = get_theme_header_phone();
                                if ( $header_phone ) {
                                    echo '<a href="tel:' . esc_attr( $header_phone ) . '" target="_blank">' . esc_html( $header_phone ) . '</a>, ';
                                }
                                
                                $header_email = get_theme_header_email();
                                if ( $header_email ) {
                                    echo '<a href="mailto:' . esc_attr( $header_email ) . '" class="email-link" target="_blank">' . esc_html( $header_email ) . '</a>';
                                }
                            ?>
                        </p>
                    </div>
                    <button class="search-btn">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/search-icon.svg" alt="">
                    </button>
                    <a href="#" class="btn-light modal-open">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/call-icon.svg" alt="">
                        <span>Консультация</span>
                    </a>
                    <button class="bars">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/hamburger.svg" alt="">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/times.svg" alt="">
                    </button>
                </div>
            </div>
        </header>

        <section class="menu">
            <div class="container">
                <?php $has_mobile_catalog = function_exists('bcc_header_catalog_can_render') && bcc_header_catalog_can_render(); ?>

                <?php if ($has_mobile_catalog && function_exists('bcc_render_mobile_catalog')) : ?>
                    <?php bcc_render_mobile_catalog(); ?>
                <?php endif; ?>

                <ul class="menu-navs">
                    <?php
                    wp_nav_menu([
                        'theme_location' => 'header_menu',
                        'container' => false,
                        'items_wrap' => '%3$s',
                        'walker' => new BCC_Clean_Walker(),
                        'link_class' => 'menu-navs__link',
                        'fallback_cb' => false,
                    ]);

                    if ( is_post_type_archive( 'product' ) || is_tax( 'product_category' ) ) {
                        echo '<li><a href="' . esc_url( get_products_archive_url() ) . '" class="menu-navs__link' . ( is_post_type_archive( 'product' ) && ! is_tax() ? ' active' : '' ) . '">Товары</a></li>';
                    }
                    ?>
                </ul>

                <a href="#" class="btn-light modal-open">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/call-icon.svg" alt="">
                    <span>Консультация</span>
                </a>

                <div class="menu-bottom">
                    <div class="menu-bottom__item">
                        <label>Адрес</label>
                        <p><?php echo wp_kses_post( get_theme_address() ); ?></p>
                    </div>
                    <div class="menu-bottom__item">
                        <label>Связь</label>
                        <?php
                        $header_phone = get_theme_header_phone();
                        if ( $header_phone ) {
                            echo '<a href="tel:' . esc_attr( $header_phone ) . '">' . esc_html( $header_phone ) . '</a>';
                        }

                        $header_email = get_theme_header_email();
                        if ( $header_email ) {
                            echo '<a href="mailto:' . esc_attr( $header_email ) . '">' . esc_html( $header_email ) . '</a>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </section>

        <main class="site-main">