<?php
/**
 * Архив товаров (Каталог)
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
                    
                    // Если это архив категории товаров
                    if ( is_tax( 'product_category' ) ) {
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
                        <?php
                        $parent_categories = get_terms([
                            'taxonomy' => 'product_category',
                            'hide_empty' => false,
                            'parent' => 0,
                            'orderby' => 'name',
                            'order' => 'ASC',
                        ]);

                        $selected_parent_id = 0;
                        $selected_parent_slug = '';

                        $raw_parent = (string) get_query_var('bcc_parent_cat');
                        if ($raw_parent === '' && isset($_GET['parent_cat'])) {
                            $raw_parent = sanitize_text_field(wp_unslash($_GET['parent_cat']));
                        }

                        if ($raw_parent !== '') {

                            if (is_numeric($raw_parent)) {
                                $selected_parent_id = (int) $raw_parent;
                                $selected_term = get_term($selected_parent_id, 'product_category');
                                if ($selected_term && !is_wp_error($selected_term)) {
                                    $selected_parent_slug = (string) $selected_term->slug;
                                }
                            } else {
                                $selected_parent_slug = sanitize_title($raw_parent);
                                $selected_term = get_term_by('slug', $selected_parent_slug, 'product_category');
                                if ($selected_term && !is_wp_error($selected_term)) {
                                    $selected_parent_id = (int) $selected_term->term_id;
                                }
                            }
                        }

                        if (!empty($parent_categories) && !is_wp_error($parent_categories) && $selected_parent_id <= 0) {
                            $selected_parent_id = (int) $parent_categories[0]->term_id;
                            $selected_parent_slug = (string) $parent_categories[0]->slug;
                        }

                        if ($parent_categories && !is_wp_error($parent_categories)) :
                            foreach ($parent_categories as $category) :
                                $is_active = (int) $category->term_id === (int) $selected_parent_id;
                                $tab_url = trailingslashit(home_url('/products/' . $category->slug));
                                ?>
                                <button<?php echo $is_active ? ' class="active"' : ''; ?> data-category="<?php echo esc_attr($category->slug); ?>" data-url="<?php echo esc_url($tab_url); ?>">
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
                    $subcategories = [];
                    if ($selected_parent_id > 0) {
                        $subcategories = get_terms([
                            'taxonomy' => 'product_category',
                            'hide_empty' => false,
                            'parent' => (int) $selected_parent_id,
                            'orderby' => 'name',
                            'order' => 'ASC',
                        ]);
                    }

                    if (!empty($subcategories) && !is_wp_error($subcategories)) :
                        foreach ($subcategories as $subcategory) :
                            $term_link = get_term_link($subcategory);
                            if (is_wp_error($term_link)) {
                                continue;
                            }

                            $attachment_id = 0;
                            if (function_exists('carbon_get_term_meta')) {
                                $attachment_id = (int) carbon_get_term_meta((int) $subcategory->term_id, 'product_category_menu_image');
                            }
                            if (!$attachment_id) {
                                $attachment_id = (int) get_term_meta((int) $subcategory->term_id, 'thumbnail_id', true);
                            }

                            $image_url = $attachment_id ? wp_get_attachment_image_url($attachment_id, 'medium') : '';
                            $description = !empty($subcategory->description) ? wp_trim_words(wp_strip_all_tags($subcategory->description), 20) : '';
                            $count = (int) ($subcategory->count ?? 0);
                            ?>
                            <div class="product-card">
                                <div class="swiper product-card__swp">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <img src="<?php echo esc_url($image_url ? $image_url : get_template_directory_uri() . '/assets/images/product-card-1.png'); ?>" alt="<?php echo esc_attr($subcategory->name); ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="product-card__body">
                                    <div class="product-card__body-text">
                                        <h3><?php echo esc_html($subcategory->name); ?></h3>
                                        <p><?php echo esc_html($description); ?></p>
                                    </div>
                                    <div class="price"><?php echo esc_html($count); ?> товаров</div>
                                    <a href="<?php echo esc_url($term_link); ?>">Подробнее</a>
                                </div>
                            </div>
                        <?php endforeach;
                    else : ?>
                        <p>Подкатегории не найдены.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
    <!-- Catalog end -->
<?php get_template_part( 'template-parts/section-contact-us' ); ?>

<?php get_footer(); ?>

