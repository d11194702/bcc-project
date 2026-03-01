<?php
/**
 * Страница товара
 * 
 * @package BCC_Project
 */

get_header();

while (have_posts()) : the_post();
    $price = function_exists('carbon_get_post_meta') ? carbon_get_post_meta(get_the_ID(), 'product_price') : get_post_meta(get_the_ID(), 'product_price', true);
    $gallery = function_exists('carbon_get_post_meta') ? carbon_get_post_meta(get_the_ID(), 'product_gallery') : get_post_meta(get_the_ID(), 'product_gallery', true);
    $specifications = function_exists('carbon_get_post_meta') ? carbon_get_post_meta(get_the_ID(), 'product_specifications') : get_post_meta(get_the_ID(), 'product_specifications', true);
    $colors = function_exists('carbon_get_post_meta') ? carbon_get_post_meta(get_the_ID(), 'product_colors') : get_post_meta(get_the_ID(), 'product_colors', true);
    $colors_enabled_raw = function_exists('carbon_get_post_meta') ? carbon_get_post_meta(get_the_ID(), 'product_colors_enabled') : get_post_meta(get_the_ID(), 'product_colors_enabled', true);
    $colors_title = function_exists('carbon_get_post_meta') ? carbon_get_post_meta(get_the_ID(), 'product_colors_title') : get_post_meta(get_the_ID(), 'product_colors_title', true);
    $colors_description = function_exists('carbon_get_post_meta') ? carbon_get_post_meta(get_the_ID(), 'product_colors_description') : get_post_meta(get_the_ID(), 'product_colors_description', true);
    $colors_show_right_raw = function_exists('carbon_get_post_meta') ? carbon_get_post_meta(get_the_ID(), 'product_colors_show_right_image') : get_post_meta(get_the_ID(), 'product_colors_show_right_image', true);
    $colors_right_image = function_exists('carbon_get_post_meta') ? carbon_get_post_meta(get_the_ID(), 'product_colors_right_image') : get_post_meta(get_the_ID(), 'product_colors_right_image', true);
    $palette_colors_assoc = function_exists('carbon_get_post_meta') ? carbon_get_post_meta(get_the_ID(), 'product_palette_colors') : get_post_meta(get_the_ID(), 'product_palette_colors', true);
    $thickness_prices = function_exists('carbon_get_post_meta') ? carbon_get_post_meta(get_the_ID(), 'product_thickness_prices') : get_post_meta(get_the_ID(), 'product_thickness_prices', true);
    $product_head_text = function_exists('carbon_get_post_meta') ? carbon_get_post_meta(get_the_ID(), 'product_head_text') : get_post_meta(get_the_ID(), 'product_head_text', true);
    $schema_image = function_exists('carbon_get_post_meta') ? carbon_get_post_meta(get_the_ID(), 'product_schema') : get_post_meta(get_the_ID(), 'product_schema', true);
?>


<?php bcc_breadcrumbs(); ?>

    <!-- Product -->
    <section class="product">
        <div class="container product-container">
            <h2 class="sec-title">
                <span><?php the_title(); ?></span>
            </h2>
            <div class="product-head">
                <?php
                $head_html = '';
                if (!empty($product_head_text)) {
                    $head_html = $product_head_text;
                } else {
                    $raw_content = (string) get_post_field('post_content', get_the_ID());
                    $content_html = wpautop($raw_content);

                    if (preg_match_all('/<p\b[^>]*>.*?<\/p>/is', $content_html, $matches) && !empty($matches[0])) {
                        foreach ($matches[0] as $p_html) {
                            if (trim(wp_strip_all_tags($p_html)) !== '') {
                                $head_html = $p_html;
                                break;
                            }
                        }
                    }
                }
                ?>
                <?php if (!empty($head_html)) : ?>
                    <div class="product-head__text"><?php echo wp_kses_post($head_html); ?></div>
                <?php endif; ?>
                <a href="#" class="btn-light">
                    <span>Мы в соц. сетях</span>
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/duble-arrow-right.svg" alt="">
                </a>
            </div>
            <div class="product-block">
                <div class="product-block__left">
                    <?php if ($gallery && is_array($gallery) && !empty($gallery)) : ?>
                        <div class="swiper swp-parent">
                            <div class="swiper-wrapper">
                                <?php foreach ($gallery as $image_id) : 
                                    $image = wp_get_attachment_image_url($image_id, 'large');
                                    if ($image) : ?>
                                        <div class="swiper-slide">
                                            <img src="<?php echo esc_url($image); ?>" alt="<?php the_title(); ?>">
                                        </div>
                                    <?php endif;
                                endforeach; ?>
                            </div>
                        </div>
                        <div class="swiper swp-child">
                            <div class="swiper-wrapper">
                                <?php foreach ($gallery as $image_id) : 
                                    $image = wp_get_attachment_image_url($image_id, 'thumbnail');
                                    if ($image) : ?>
                                        <div class="swiper-slide">
                                            <img src="<?php echo esc_url($image); ?>" alt="<?php the_title(); ?>">
                                        </div>
                                    <?php endif;
                                endforeach; ?>
                            </div>
                        </div>
                    <?php elseif (has_post_thumbnail()) : ?>
                        <div class="swiper swp-parent">
                            <div class="swiper-wrapper">
                                <div class="swiper-slide">
                                    <?php the_post_thumbnail('large'); ?>
                                </div>
                            </div>
                        </div>
                    <?php else : ?>
                        <div class="swiper swp-parent">
                            <div class="swiper-wrapper">
                                <div class="swiper-slide">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/product-block-card-1.png" alt="<?php the_title(); ?>">
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="product-block__text">
                    <?php
                    $normalized_thickness_prices = [];
                    if (is_array($thickness_prices)) {
                        foreach ($thickness_prices as $row) {
                            if (!is_array($row)) {
                                continue;
                            }

                            $thickness_label = isset($row['thickness']) ? trim((string) $row['thickness']) : '';
                            $thickness_price = isset($row['price']) ? trim((string) $row['price']) : '';

                            if ($thickness_label === '' || $thickness_price === '') {
                                continue;
                            }

                            $normalized_thickness_prices[] = [
                                'thickness' => $thickness_label,
                                'price' => $thickness_price,
                            ];
                        }
                    }

                    $has_thickness_selector = !empty($normalized_thickness_prices);
                    ?>
                    <div class="product-block__text-head">
                        <h3>Спецификация</h3>
                        <ul>
                            <?php if ($specifications && is_array($specifications)) : ?>
                                <?php foreach ($specifications as $spec) : ?>
                                    <li>
                                        <b><?php echo esc_html($spec['label']); ?></b>
                                        <span><?php echo esc_html($spec['spec_value']); ?></span>
                                    </li>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <li>
                                    <b>Полезная ширина листа, мм</b>
                                    <span>1110</span>
                                </li>
                                <li>
                                    <b>Общая ширина листа, мм</b>
                                    <span>1190</span>
                                </li>
                                <li>
                                    <b>Длина волны, мм</b>
                                    <span>222</span>
                                </li>
                                <li>
                                    <b>Высота волны, мм</b>
                                    <span>25</span>
                                </li>
                            <?php endif; ?>

                            <?php if ($has_thickness_selector) : ?>
                                <li class="product-spec-thickness-row">
                                    <b>Толщина металла, мм</b>
                                    <div class="main-select product-thickness-select">
                                        <button class="main-select__btn" type="button">
                                            <span><?php echo esc_html($normalized_thickness_prices[0]['thickness']); ?></span>
                                            <input type="hidden" name="product_thickness" value="<?php echo esc_attr($normalized_thickness_prices[0]['thickness']); ?>">
                                            <svg width="10" height="6" viewBox="0 0 10 6" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
                                                <path d="M1 1L5 5L9 1" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </button>
                                        <div class="main-select__list">
                                            <?php foreach ($normalized_thickness_prices as $index => $item) : ?>
                                                <button
                                                    type="button"
                                                    data-thickness="<?php echo esc_attr($item['thickness']); ?>"
                                                    data-price="<?php echo esc_attr($item['price']); ?>"
                                                    <?php echo $index === 0 ? 'class="selected"' : ''; ?>
                                                >
                                                    <span><?php echo esc_html($item['thickness']); ?></span>
                                                </button>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <div class="product-block__text-bottom">
                        <?php
                        $display_price = trim((string) $price);
                        if ($display_price === '' && $has_thickness_selector) {
                            $display_price = $normalized_thickness_prices[0]['price'];
                        }
                        ?>

                        <?php if ($display_price !== '') : ?>
                            <div class="price">
                                <span class="product-price-value"><?php echo esc_html($display_price); ?></span>
                                <span class="product-price-unit"> руб/м2</span>
                            </div>
                        <?php endif; ?>
                        <a href="#" class="btn-blue">
                            <span>Заказать</span>
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/duble-arrow-right-white.svg" alt="">
                        </a>
                    </div>
                </div>
            </div>
            <div class="product-text">
                <?php the_content(); ?>
            </div>

            <?php
            $colors_enabled = ($colors_enabled_raw === '' || $colors_enabled_raw === null) ? true : in_array((string) $colors_enabled_raw, ['1', 'yes', 'true'], true);
            $colors_show_right = ($colors_show_right_raw === '' || $colors_show_right_raw === null) ? true : in_array((string) $colors_show_right_raw, ['1', 'yes', 'true'], true);
            $selected_term_ids = [];
            if (is_array($palette_colors_assoc)) {
                foreach ($palette_colors_assoc as $row) {
                    if (is_array($row) && isset($row['id'])) {
                        $selected_term_ids[] = (int) $row['id'];
                    }
                }
            }
            $selected_term_ids = array_values(array_unique(array_filter($selected_term_ids)));

            $selected_terms = [];
            if (!empty($selected_term_ids)) {
                foreach ($selected_term_ids as $tid) {
                    $t = get_term($tid, 'product_color');
                    if ($t && !is_wp_error($t)) {
                        $selected_terms[] = $t;
                    }
                }
            }

            $taxonomy_colors = get_the_terms(get_the_ID(), 'product_color');
            $has_taxonomy_colors = (!empty($taxonomy_colors) && !is_wp_error($taxonomy_colors));
            $has_selected_terms = !empty($selected_terms);
            $has_manual_colors = ($colors && is_array($colors) && !empty($colors));
            $has_palette_content = $has_selected_terms || $has_taxonomy_colors || $has_manual_colors || !empty($colors_title) || !empty($colors_description) || !empty($colors_right_image);
            ?>

            <?php if ($colors_enabled && $has_palette_content) : ?>
                <div class="product-content">
                    <div class="product-content__left">
                        <div class="text">
                            <h3><?php echo esc_html($colors_title ?: 'Цветовая палитра модели'); ?></h3>
                            <p><?php echo esc_html($colors_description ?: 'Мы предлагаем широкий выбор цветов. Поможем воплотить любую вашу идею! Также, по запросу дополнительно покроем листы защитным слоем.'); ?></p>
                        </div>
                        <?php if ($has_selected_terms) : ?>
                            <ul>
                                <?php
                                foreach ($selected_terms as $term) {
                                    $term_id = (int) $term->term_id;

                                    $attachment_id = 0;
                                    if (function_exists('carbon_get_term_meta')) {
                                        $attachment_id = (int) carbon_get_term_meta($term_id, 'product_color_image');
                                    }
                                    if (!$attachment_id) {
                                        $attachment_id = (int) get_term_meta($term_id, 'thumbnail_id', true);
                                    }

                                    $img = $attachment_id ? wp_get_attachment_image_url($attachment_id, 'thumbnail') : '';
                                    $name = $term->name;
                                    if (!$img && $name === '') continue;
                                    ?>
                                    <li class="product-color-item" data-tooltip="<?php echo esc_attr($name); ?>" tabindex="0">
                                        <?php if ($img) : ?>
                                            <img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr($name); ?>">
                                        <?php endif; ?>
                                        <span><?php echo esc_html($name); ?></span>
                                    </li>
                                <?php } ?>
                            </ul>
                        <?php elseif ($has_taxonomy_colors) : ?>
                            <ul>
                                <?php
                                foreach ($taxonomy_colors as $term) {
                                    $term_id = (int) $term->term_id;

                                    $attachment_id = 0;
                                    if (function_exists('carbon_get_term_meta')) {
                                        $attachment_id = (int) carbon_get_term_meta($term_id, 'product_color_image');
                                    }
                                    if (!$attachment_id) {
                                        $attachment_id = (int) get_term_meta($term_id, 'thumbnail_id', true);
                                    }

                                    $img = $attachment_id ? wp_get_attachment_image_url($attachment_id, 'thumbnail') : '';
                                    $name = $term->name;
                                    if (!$img && $name === '') continue;
                                    ?>
                                    <li class="product-color-item" data-tooltip="<?php echo esc_attr($name); ?>" tabindex="0">
                                        <?php if ($img) : ?>
                                            <img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr($name); ?>">
                                        <?php endif; ?>
                                        <span><?php echo esc_html($name); ?></span>
                                    </li>
                                <?php } ?>
                            </ul>
                        <?php elseif ($has_manual_colors) : ?>
                            <ul>
                                <?php foreach ($colors as $color) :
                                    $color_image = !empty($color['image']) ? wp_get_attachment_image_url($color['image'], 'thumbnail') : '';
                                    $color_name = !empty($color['name']) ? $color['name'] : '';
                                    if (!$color_image && $color_name === '') continue;
                                    ?>
                                    <li class="product-color-item" data-tooltip="<?php echo esc_attr($color_name); ?>" tabindex="0">
                                        <?php if ($color_image) : ?>
                                            <img src="<?php echo esc_url($color_image); ?>" alt="<?php echo esc_attr($color_name); ?>">
                                        <?php endif; ?>
                                        <span><?php echo esc_html($color_name); ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                    <?php if ($colors_show_right) :
                        $right_url = '';
                        if (!empty($colors_right_image)) {
                            $right_url = wp_get_attachment_image_url($colors_right_image, 'large');
                        }
                        if (!$right_url) {
                            $right_url = get_template_directory_uri() . '/assets/images/product-content-card-1.png';
                        }
                        ?>
                        <img src="<?php echo esc_url($right_url); ?>" alt="" class="product-content__right">
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ($schema_image) : 
                $schema_url = wp_get_attachment_image_url($schema_image, 'large');
                if ($schema_url) : ?>
                    <div class="product-foot">
                        <div class="text">
                            <h3>Схема</h3>
                            <?php 
                            $schema_description = function_exists('carbon_get_post_meta')
                                ? carbon_get_post_meta(get_the_ID(), 'product_schema_description')
                                : get_post_meta(get_the_ID(), 'product_schema_description', true);
                            if (!empty($schema_description)) : ?>
                                <div class="product-foot__desc"><?php echo wp_kses_post($schema_description); ?></div>
                            <?php endif; ?>
                        </div>
                        <img src="<?php echo esc_url($schema_url); ?>" alt="Схема">
                    </div>
                <?php endif;
            endif; ?>
        </div>
    </section>
    <!-- Product end -->

    <?php
    $current_terms = wp_get_post_terms(get_the_ID(), 'product_category', ['fields' => 'ids']);
    $related_args = [
        'post_type' => 'product',
        'posts_per_page' => 8,
        'post__not_in' => [get_the_ID()],
        'orderby' => 'rand',
    ];

    if ($current_terms && !is_wp_error($current_terms)) {
        $related_args['tax_query'] = [
            [
                'taxonomy' => 'product_category',
                'field' => 'term_id',
                'terms' => $current_terms,
            ],
        ];
    }

    $related_products = new WP_Query($related_args);
    ?>

    <?php if ($related_products->have_posts()) : ?>
        <!-- Other product -->
        <section class="other-product">
            <div class="container other-product__container">
                <div class="other-product__head">
                    <h2>Похожие товары</h2>
                    <div class="btn">
                        <button class="btn-prev">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/swp-prev.svg" alt="">
                        </button>
                        <button class="btn-next">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/swp-next.svg" alt="">
                        </button>
                    </div>
                </div>
                <div class="swiper other-product__swp">
                    <div class="swiper-wrapper">
                        <?php
                        while ($related_products->have_posts()) : $related_products->the_post();
                            $related_gallery = function_exists('carbon_get_post_meta') ? carbon_get_post_meta(get_the_ID(), 'product_gallery') : get_post_meta(get_the_ID(), 'product_gallery', true);
                            ?>
                            <a href="<?php the_permalink(); ?>" class="swiper-slide product-card">
                                <?php if ($related_gallery && is_array($related_gallery) && !empty($related_gallery)) : ?>
                                    <div class="swiper product-card__swp">
                                        <div class="swiper-wrapper">
                                            <?php foreach (array_slice($related_gallery, 0, 3) as $image_id) :
                                                $image = wp_get_attachment_image_url($image_id, 'large');
                                                if ($image) : ?>
                                                    <div class="swiper-slide">
                                                        <img src="<?php echo esc_url($image); ?>" alt="<?php the_title(); ?>">
                                                    </div>
                                                <?php endif;
                                            endforeach; ?>
                                        </div>
                                        <div class="swp-pagination"></div>
                                    </div>
                                <?php elseif (has_post_thumbnail()) : ?>
                                    <div class="swiper product-card__swp">
                                        <div class="swiper-wrapper">
                                            <div class="swiper-slide">
                                                <?php the_post_thumbnail('large'); ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php else : ?>
                                    <div class="swiper product-card__swp">
                                        <div class="swiper-wrapper">
                                            <div class="swiper-slide">
                                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/product-card-1.png" alt="<?php the_title(); ?>">
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <div class="product-card__body">
                                    <div class="product-card__body-text">
                                        <h3><?php the_title(); ?></h3>
                                        <p><?php echo wp_trim_words(get_the_excerpt(), 15); ?></p>
                                    </div>
                                    <span>Подробнее</span>
                                </div>
                            </a>
                        <?php endwhile;
                        wp_reset_postdata();
                        ?>
                    </div>
                </div>
            </div>
        </section>
        <!-- Other product end -->
    <?php endif; ?>




<?php get_template_part( 'template-parts/section-contact-us' ); ?>
<?php
endwhile;
get_footer();
?>
