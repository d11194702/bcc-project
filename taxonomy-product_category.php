<?php
/**
 * Шаблон таксономии категорий товаров
 * (должен выглядеть как каталог)
 *
 * @package BCC_Project
 */

get_header();
?>


<?php bcc_breadcrumbs(); ?>

    <!-- Catalog -->
    <section class="catalog">
        <div class="container catalog-container">
            <div class="catalog-head">
                <?php 
                    $page_title = 'Каталог продукции';
                    $default_description = 'Добро пожаловать в каталог кровельных материалов от нашей компании! Мы предлагаем полный спектр решений для устройства надёжной и эстетичной кровли — от базовой металлочерепицы до комплексных систем водоотведения.';
                    $page_description = $default_description;
                    
                    // Получаем текущую категорию
                    $term = get_queried_object();
                    if ( $term && isset( $term->name ) ) {
                        $page_title = $term->name;
                        // Используем встроенное описание категории, если оно задано
                        if ( ! empty( $term->description ) ) {
                            $page_description = $term->description;
                        } else {
                            $page_description = $default_description;
                        }
                    }
                ?>
                <h2 class="sec-title">
                    <span><?php echo esc_html( $page_title ); ?></span>
                </h2>
                <p><?php echo wp_kses_post( $page_description ); ?></p>
            </div>
            <div class="catalog-content">
                <div class="catalog-tab__head">
                    <button class="catalog-tab__head-btn">
                        <span>Категория товара</span>
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/arrow-down-light.svg" alt="">
                    </button>
                    <div class="catalog-tab__head-list">
                        <button data-url="<?php echo esc_url(get_post_type_archive_link('product')); ?>">Все товары</button>
                        <?php
                        $current_term_id = get_queried_object_id();
                        $categories = get_terms([
                            'taxonomy' => 'product_category',
                            'hide_empty' => false,
                            'parent' => (int) $current_term_id,
                            'orderby' => 'name',
                            'order' => 'ASC',
                        ]);
                        if ($categories && !is_wp_error($categories)) :
                            foreach ($categories as $category) :
                                $term_link = get_term_link($category);
                                ?>
                                <button
                                    data-category="<?php echo esc_attr($category->slug); ?>"
                                    data-url="<?php echo esc_url($term_link); ?>"
                                >
                                    <?php echo esc_html($category->name); ?>
                                </button>
                            <?php endforeach;
                        endif;
                        ?>
                    </div>
                </div>
                <?php bcc_render_catalog_sort(); ?>
            </div>
            <div class="catalog-tab__body active">
                <div class="catalog-list">
                    <?php
                    $current_term_id = get_queried_object_id();
                    $child_terms = get_terms([
                        'taxonomy' => 'product_category',
                        'hide_empty' => false,
                        'parent' => (int) $current_term_id,
                        'orderby' => 'name',
                        'order' => 'ASC',
                    ]);

                    if (!empty($child_terms) && !is_wp_error($child_terms)) :
                        foreach ($child_terms as $child_term) :
                            $term_link = get_term_link($child_term);
                            if (is_wp_error($term_link)) {
                                continue;
                            }

                            $attachment_id = 0;
                            if (function_exists('carbon_get_term_meta')) {
                                $attachment_id = (int) carbon_get_term_meta((int) $child_term->term_id, 'product_category_menu_image');
                            }
                            if (!$attachment_id) {
                                $attachment_id = (int) get_term_meta((int) $child_term->term_id, 'thumbnail_id', true);
                            }

                            $image_url = $attachment_id ? wp_get_attachment_image_url($attachment_id, 'medium') : '';
                            $description = !empty($child_term->description) ? wp_trim_words(wp_strip_all_tags($child_term->description), 20) : '';
                            $count = (int) ($child_term->count ?? 0);
                            ?>
                            <div class="product-card">
                                <div class="swiper product-card__swp">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <img src="<?php echo esc_url($image_url ? $image_url : get_template_directory_uri() . '/assets/images/product-card-1.png'); ?>" alt="<?php echo esc_attr($child_term->name); ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="product-card__body">
                                    <div class="product-card__body-text">
                                        <h3><?php echo esc_html($child_term->name); ?></h3>
                                        <p><?php echo esc_html($description); ?></p>
                                    </div>
                                    <div class="price"><?php echo esc_html($count); ?> товаров</div>
                                    <a href="<?php echo esc_url($term_link); ?>">Подробнее</a>
                                </div>
                            </div>
                        <?php endforeach;
                    elseif (have_posts()) :
                        while (have_posts()) : the_post();
                            $price = function_exists('carbon_get_post_meta') ? carbon_get_post_meta(get_the_ID(), 'product_price') : get_post_meta(get_the_ID(), 'product_price', true);
                            $gallery = function_exists('carbon_get_post_meta') ? carbon_get_post_meta(get_the_ID(), 'product_gallery') : get_post_meta(get_the_ID(), 'product_gallery', true);
                            ?>
                            <div class="product-card">
                                <?php if ($gallery && is_array($gallery)) : ?>
                                    <div class="swiper product-card__swp">
                                        <div class="swiper-wrapper">
                                            <?php foreach ($gallery as $image_id) :
                                                $image = wp_get_attachment_image_url($image_id, 'medium');
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
                                                <?php the_post_thumbnail('medium'); ?>
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
                                        <p><?php echo wp_trim_words(get_the_excerpt(), 20); ?></p>
                                    </div>
                                    <?php if ($price) : ?>
                                        <div class="price"><?php echo esc_html($price); ?> руб/м2</div>
                                    <?php endif; ?>
                                    <a href="<?php the_permalink(); ?>">Подробнее</a>
                                </div>
                            </div>
                        <?php endwhile;
                    else : ?>
                        <p>Товары и подкатегории не найдены.</p>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (empty($child_terms) || is_wp_error($child_terms)) : ?>
                <?php
                the_posts_pagination([
                    'mid_size'  => 2,
                    'prev_text' => '<img src="' . get_template_directory_uri() . '/assets/images/prev-icon.svg" alt="Предыдущая">',
                    'next_text' => '<img src="' . get_template_directory_uri() . '/assets/images/next-icon.svg" alt="Следующая">',
                ]);
                ?>
            <?php endif; ?>
        </div>
    </section>
    <!-- Catalog end -->


<?php get_footer(); ?>
