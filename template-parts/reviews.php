<?php

/**
 * Template Part: Отзывы
 * Глобальная секция отзывов, подключаемая везде на сайте
 */

// Получаем данные из глобальных настроек темы
$reviews_title = carbon_get_theme_option('global_reviews_title');
$reviews_description = carbon_get_theme_option('global_reviews_description');
$reviews_display_type = carbon_get_theme_option('global_reviews_display_type');
$reviews_items = carbon_get_theme_option('global_reviews_items');
$reviews_widget_code = carbon_get_theme_option('global_reviews_widget_code');
$reviews_link_text = carbon_get_theme_option('global_reviews_link_text');
$reviews_link_url = carbon_get_theme_option('global_reviews_link_url');
$card_enabled = carbon_get_theme_option('global_reviews_card_enabled');
$card_title = carbon_get_theme_option('contact_us_title');
$card_description = carbon_get_theme_option('contact_us_description');
$card_button_text = carbon_get_theme_option('contact_us_button_text');
$card_action_type = carbon_get_theme_option('contact_us_action_type');
$card_button_url = carbon_get_theme_option('contact_us_button_url');
$card_bg_image = carbon_get_theme_option('contact_us_bg_image');
$card_main_image = carbon_get_theme_option('contact_us_main_image');

// Проверяем, есть ли что выводить
$should_display = false;
if ($reviews_display_type === 'widget' && ! empty($reviews_widget_code)) {
    $should_display = true;
} elseif ($reviews_display_type === 'manual' && ! empty($reviews_items)) {
    $should_display = true;
}

if ($should_display) :
?>
    <section class="review">
        <div class="container reivew-container">
            <h2 class="sec-title review-title">
                <span><?php echo esc_html($reviews_title); ?></span>
            </h2>
            <div class="review-head">
                <p><?php echo wp_kses_post($reviews_description); ?></p>
                <?php if ($reviews_link_text && $reviews_link_url) : ?>
                    <div class="review-head__right">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/yandex-text.svg" alt="" class="yandex-text">
                        <a href="<?php echo esc_url($reviews_link_url); ?>" class="btn-light">
                            <span><?php echo esc_html($reviews_link_text); ?></span>
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/duble-arrow-right.svg" alt="">
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($reviews_display_type === 'widget' && ! empty($reviews_widget_code)) : ?>
                <!-- Яндекс виджет -->
                <div class="review-widget">
                    <?php echo wp_kses_post($reviews_widget_code); ?>
                </div>
            <?php elseif ($reviews_display_type === 'manual' && ! empty($reviews_items)) : ?>
                <!-- Отзывы вручную -->
                <div class="review-content">
                    <div class="review-swp">
                        <div class="swiper">
                            <div class="swiper-wrapper">
                                <?php foreach ($reviews_items as $review) :
                                    $author = isset($review['author']) ? $review['author'] : '';
                                    $date = isset($review['date']) ? $review['date'] : '';
                                    $text = isset($review['text']) ? $review['text'] : '';
                                ?>
                                    <div class="swiper-slide">
                                        <h3><?php echo esc_html($author); ?></h3>
                                        <h4><?php echo esc_html($date); ?></h4>
                                        <p><?php echo wp_kses_post($text); ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Блок гарантии (показывается независимо от режима отзывов) -->
                    <?php if ($card_enabled  && $card_title && $card_description) : ?>

                        <div class="review-card">
                            <h3><?php echo wp_kses_post($card_title); ?></h3>
                            <p><?php echo wp_kses_post($card_description); ?></p>
                            <?php if ($card_button_text) : ?>
                                <?php if ($card_action_type === 'modal') : ?>
                                    <button class="btn-blue modal-open">
                                        <span><?php echo esc_html($card_button_text); ?></span>
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/duble-arrow-right-white.svg" alt="">
                                    </button>
                                <?php elseif ($card_action_type === 'link' && ! empty($card_button_url)) : ?>
                                    <a href="<?php echo esc_url($card_button_url); ?>" class="btn-blue">
                                        <span><?php echo esc_html($card_button_text); ?></span>
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/duble-arrow-right-white.svg" alt="">
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php
                            $bg_img_url = ! empty($card_bg_image) ? wp_get_attachment_image_url($card_bg_image, 'full') : get_template_directory_uri() . '/assets/images/reivew-card-bg.png';
                            $main_img_url = ! empty($card_main_image) ? wp_get_attachment_image_url($card_main_image, 'full') : get_template_directory_uri() . '/assets/images/review-card-1.png';
                            ?>
                            <img src="<?php echo esc_url($bg_img_url); ?>" alt="" class="bg-img">
                            <img src="<?php echo esc_url($main_img_url); ?>" alt="" class="main-img">
                        </div>

                    <?php endif; ?>
                </div>
            <?php endif; ?>


        </div>
    </section>
<?php endif; ?>