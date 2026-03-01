<?php
/**
 * Template Name: Главная страница
 * 
 * @package BCC_Project
 */

get_header();
?>

    <!-- Home -->
<?php
$title       = carbon_get_post_meta( get_the_ID(), 'home_hero_title_line' );
$text1       = carbon_get_post_meta( get_the_ID(), 'home_hero_text_1' );
$text2       = carbon_get_post_meta( get_the_ID(), 'home_hero_text_2' );
$action_type = carbon_get_post_meta( get_the_ID(), 'home_hero_action_type' );

$btn_text = carbon_get_post_meta( get_the_ID(), 'home_hero_button_text' );
$link_text = carbon_get_post_meta( get_the_ID(), 'home_hero_link_text' );
$link_url = carbon_get_post_meta( get_the_ID(), 'home_hero_link_url' );

$image_desktop = carbon_get_post_meta( get_the_ID(), 'home_hero_image_desktop' );
$image_mobile  = carbon_get_post_meta( get_the_ID(), 'home_hero_image_mobile' );
?>

<section class="home">
    <div class="container home-container">
        <div class="home-left">

            <div class="home-title">
                <?php echo $title; ?>
            </div>

            <div>
                <ul>
                    <li><?php echo apply_filters( 'the_content', $text1 ); ?></li>
                    <li><?php echo apply_filters( 'the_content', $text2 ); ?></li>
                </ul>

                <?php if ( $action_type === 'button' && $btn_text ) : ?>
                    <a href="#" class="btn-blue">
                        <?php echo $btn_text; ?>
                    </a>
                <?php endif; ?>

                <?php if ( $action_type === 'link' && $link_url ) : ?>
                    <a href="<?php echo esc_url( $link_url ); ?>">
                        <?php echo esc_html( $link_text ); ?>
                    </a>
                <?php endif; ?>
            </div>

            <div class="bg-text"><?php echo carbon_get_post_meta( get_the_ID(), 'home_hero_bg_text' ); ?></div>

        </div>

        <div class="home-right">
            <?php if ( $image_desktop ) : ?>
                <img src="<?php echo wp_get_attachment_image_url( $image_desktop, 'full' ); ?>" alt="">
            <?php endif; ?>

            <?php if ( $image_mobile ) : ?>
                <img src="<?php echo wp_get_attachment_image_url( $image_mobile, 'full' ); ?>" alt="" class="sm">
            <?php endif; ?>
        </div>
    </div>
</section>
    <!-- Home end -->

    <!-- Product catalog -->
    <section class="product-catalog">
        <div class="container product-catalog__container">
            <h2 class="sec-title">
                <span><?php echo carbon_get_post_meta( get_the_ID(), 'home_catalog_title' ); ?></span>
            </h2>
            <div class="product-catalog__head">
                <p><?php echo carbon_get_post_meta( get_the_ID(), 'home_catalog_description' ); ?></p>
                <a href="<?php echo carbon_get_post_meta( get_the_ID(), 'home_catalog_button_url' ); ?>" class="btn-light">
                    <span><?php echo carbon_get_post_meta( get_the_ID(), 'home_catalog_button_text' ); ?></span>
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/duble-arrow-right.svg" alt="">
                </a>
            </div>

            <?php
            // Получаем выбранные категории из Carbon Fields
            $selected_categories = carbon_get_post_meta( get_the_ID(), 'home_catalog_categories' );
            
            // Если категории выбраны - берем их, иначе все категории
            if ( ! empty( $selected_categories ) && is_array( $selected_categories ) ) {
                // Преобразуем массив в ID для WP_Query
                $category_ids = array();
                foreach ( $selected_categories as $cat ) {
                    if ( isset( $cat['id'] ) ) {
                        $category_ids[] = $cat['id'];
                    }
                }
                $categories = get_terms( array(
                    'taxonomy' => 'product_category',
                    'include' => $category_ids,
                    'hide_empty' => false,
                ) );
            } else {
                // Если не выбрано - выводим все категории
                $categories = get_terms( array(
                    'taxonomy' => 'product_category',
                    'hide_empty' => false,
                ) );
            }

            if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) :
            ?>
            <div class="product-catalog__content">
                <div class="tab-head">
                    <?php foreach ( $categories as $index => $category ) : ?>
                        <a href="#" class="<?php echo $index === 0 ? 'active' : ''; ?>" data-category="<?php echo esc_attr( $category->term_id ); ?>">
                            <?php echo esc_html( $category->name ); ?>
                        </a>
                    <?php endforeach; ?>
                </div>

                <div class="tab-body">
                    <?php foreach ( $categories as $index => $category ) : 
                        // Получаем товары в категории
                        $products = get_posts( array(
                            'post_type' => 'product',
                            'posts_per_page' => -1,
                            'tax_query' => array(
                                array(
                                    'taxonomy' => 'product_category',
                                    'field' => 'term_id',
                                    'terms' => $category->term_id,
                                )
                            )
                        ) );
                    ?>
                        <div class="tab-body__item <?php echo $index === 0 ? 'active' : ''; ?>">
                            <?php if ( ! empty( $products ) ) : ?>
                                <div class="swiper">
                                    <div class="swiper-wrapper">
                                        <?php foreach ( $products as $product ) : 
                                            // Получаем изображение товара
                                            $image = wp_get_attachment_image_url( get_post_thumbnail_id( $product->ID ), 'full' );
                                            // Получаем описание из кастомного поля
                                            $head_text = carbon_get_post_meta( $product->ID, 'product_head_text' );
                                            $card_text_source = $head_text ? $head_text : $product->post_content;
                                            $card_text = wp_trim_words( wp_strip_all_tags( (string) $card_text_source ), 20, '...' );
                                            $product_permalink = get_permalink( $product->ID );
                                            if ( ! $product_permalink ) {
                                                continue;
                                            }
                                        ?>
                                            <a href="<?php echo esc_url( $product_permalink ); ?>" class="swiper-slide product-catalog__card">
                                                <?php if ( $image ) : ?>
                                                    <img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $product->post_title ); ?>" class="main-img">
                                                <?php endif; ?>
                                                <div class="product-catalog__card-body">
                                                    <h3><?php echo esc_html( $product->post_title ); ?></h3>
                                                    <p><?php echo esc_html( $card_text ); ?></p>
                                                    <span class="more-link__text">Подробнее</span>
                                                </div>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                    <button class="swp-prev">
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/swp-prev-white.svg" alt="">
                                    </button>
                                    <button class="swp-next">
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/swp-next-white.svg" alt="">
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Company -->
    <section class="company">
        <div class="container company-container">
            <div class="company-head">
                <h2 class="sec-title">
                    <span><?php echo carbon_get_post_meta( get_the_ID(), 'home_company_title' ); ?></span>
                </h2>
                <ul>
                    <?php 
                    $text1 = carbon_get_post_meta( get_the_ID(), 'home_company_text_1' );
                    $text2 = carbon_get_post_meta( get_the_ID(), 'home_company_text_2' );
                    ?>
                    <?php if ( $text1 ) : ?>
                        <li><?php echo wp_kses_post( $text1 ); ?></li>
                    <?php endif; ?>
                    <?php if ( $text2 ) : ?>
                        <li><?php echo wp_kses_post( $text2 ); ?></li>
                    <?php endif; ?>
                </ul>
                <?php 
                $btn_text = carbon_get_post_meta( get_the_ID(), 'home_company_button_text' );
                $btn_url = carbon_get_post_meta( get_the_ID(), 'home_company_button_url' );
                ?>
                <?php if ( $btn_text && $btn_url ) : ?>
                    <a href="<?php echo esc_url( $btn_url ); ?>" class="btn-blue">
                        <span><?php echo esc_html( $btn_text ); ?></span>
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/duble-arrow-right-white.svg" alt="">
                    </a>
                <?php endif; ?>
                <div class="bg-text"><?php echo carbon_get_post_meta( get_the_ID(), 'home_company_bg_text' ); ?></div>
                <?php 
                $img_desktop = carbon_get_post_meta( get_the_ID(), 'home_company_image_desktop' );
                $img_mobile = carbon_get_post_meta( get_the_ID(), 'home_company_image_mobile' );
                ?>
                <?php if ( $img_desktop ) : ?>
                    <img src="<?php echo wp_get_attachment_image_url( $img_desktop, 'full' ); ?>" alt="" class="bg-img">
                <?php endif; ?>
                <?php if ( $img_mobile ) : ?>
                    <img src="<?php echo wp_get_attachment_image_url( $img_mobile, 'full' ); ?>" alt="" class="bg-img sm">
                <?php endif; ?>
            </div>
            <div class="company-content">
                <?php 
                $stats = carbon_get_post_meta( get_the_ID(), 'home_company_stats' );
                if ( ! empty( $stats ) && is_array( $stats ) ) :
                    foreach ( $stats as $stat ) :
                        $value = isset( $stat['stat_value'] ) ? $stat['stat_value'] : '';
                        $label = isset( $stat['stat_label'] ) ? $stat['stat_label'] : '';
                        $image_id = isset( $stat['stat_image'] ) ? $stat['stat_image'] : '';
                        $image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'full' ) : '';
                ?>
                    <div class="company-card">
                        <h3><?php echo esc_html( $value ); ?></h3>
                        <p><?php echo esc_html( $label ); ?></p>
                        <?php if ( $image_url ) : ?>
                            <img src="<?php echo esc_url( $image_url ); ?>" alt="">
                        <?php endif; ?>
                    </div>
                <?php 
                    endforeach; 
                endif; 
                ?>
            </div>
        </div>
    </section>
    <!-- Company end -->

    <!-- Skill -->
    <section class="skill">
        <?php 
        $skill_bg = carbon_get_post_meta( get_the_ID(), 'home_skills_bg_image' );
        if ( $skill_bg ) :
        ?>
            <img src="<?php echo wp_get_attachment_image_url( $skill_bg, 'full' ); ?>" alt="" class="skill-bg">
        <?php else : ?>
            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/skill-bg.png" alt="" class="skill-bg">
        <?php endif; ?>
        <div class="container skill-container">
            <h2 class="sec-title skill-title">
                <span><?php echo carbon_get_post_meta( get_the_ID(), 'home_skills_title' ); ?></span>
            </h2>
            <?php 
            $skills = carbon_get_post_meta( get_the_ID(), 'home_skills_items' );
            if ( ! empty( $skills ) && is_array( $skills ) ) :
            ?>
            <ul class="skill-list">
                <?php foreach ( $skills as $skill ) : 
                    $skill_title = isset( $skill['title'] ) ? $skill['title'] : '';
                    $skill_text = isset( $skill['text'] ) ? $skill['text'] : '';
                ?>
                    <li class="skill-list__item">
                        <h3><?php echo esc_html( $skill_title ); ?></h3>
                        <p><?php echo wp_kses_post( $skill_text ); ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
        </div>
    </section>
    <!-- Skill end -->

    <!-- Review -->
    <?php get_template_part( 'template-parts/reviews' ); ?>
    <!-- Review end -->

    <!-- FAQ -->
    <?php 
    $faq_enabled = carbon_get_post_meta( get_the_ID(), 'home_faq_enabled' );
    if ( $faq_enabled === 'yes' ) : 
    ?>
    <section class="faq">
        <div class="container faq-container">
            <h2 class="sec-title faq-title">
                <span><?php echo esc_html( carbon_get_post_meta( get_the_ID(), 'home_faq_title' ) ); ?></span>
            </h2>
            <div class="faq-head">
                <p><?php echo wp_kses_post( carbon_get_post_meta( get_the_ID(), 'home_faq_description' ) ); ?></p>
                <?php 
                $faq_btn_text = carbon_get_post_meta( get_the_ID(), 'home_faq_button_text' );
                $faq_action_type = carbon_get_post_meta( get_the_ID(), 'home_faq_action_type' );
                $faq_btn_url = carbon_get_post_meta( get_the_ID(), 'home_faq_button_url' );
                ?>
                <?php if ( $faq_btn_text ) : ?>
                    <?php if ( $faq_action_type === 'modal' ) : ?>
                        <button class="btn-blue modal-open">
                            <span><?php echo esc_html( $faq_btn_text ); ?></span>
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/duble-arrow-right-white.svg" alt="">
                        </button>
                    <?php elseif ( $faq_action_type === 'link' && ! empty( $faq_btn_url ) ) : ?>
                        <a href="<?php echo esc_url( $faq_btn_url ); ?>" class="btn-blue">
                            <span><?php echo esc_html( $faq_btn_text ); ?></span>
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/duble-arrow-right-white.svg" alt="">
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <div class="accordions faq-accordions">
                <?php 
                $faq_items = carbon_get_post_meta( get_the_ID(), 'home_faq_items' );
                if ( ! empty( $faq_items ) && is_array( $faq_items ) ) :
                    foreach ( $faq_items as $item ) :
                        $question = isset( $item['question'] ) ? $item['question'] : '';
                        $answer = isset( $item['answer'] ) ? $item['answer'] : '';
                ?>
                <div class="accordion">
                    <button class="accordion-btn">
                        <span><?php echo esc_html( $question ); ?></span>
                        <span class="icon">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/plus-icon.svg" alt="">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/minus-icon.svg" alt="">
                        </span>
                    </button>
                    <div class="accordion-body__wrap">
                        <div class="accordion-body">
                            <p><?php echo wp_kses_post( $answer ); ?></p>
                        </div>
                    </div>
                </div>
                <?php 
                    endforeach; 
                endif; 
                ?>
            </div>
            <?php 
            $secondary_btn_text = carbon_get_post_meta( get_the_ID(), 'home_faq_secondary_button_text' );
            $secondary_action_type = carbon_get_post_meta( get_the_ID(), 'home_faq_secondary_action_type' );
            $secondary_btn_url = carbon_get_post_meta( get_the_ID(), 'home_faq_secondary_button_url' );
            ?>
            <?php if ( $secondary_btn_text ) : ?>
                <?php if ( $secondary_action_type === 'modal' ) : ?>
                    <button class="btn-light modal-open">
                        <span><?php echo esc_html( $secondary_btn_text ); ?></span>
                        <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M0 8.07692V6.92308H6.92308V0H8.07692V6.92308H15V8.07692H8.07692V15H6.92308V8.07692H0Z" fill="#112D55" />
                        </svg>
                    </button>
                <?php elseif ( $secondary_action_type === 'link' && ! empty( $secondary_btn_url ) ) : ?>
                    <a href="<?php echo esc_url( $secondary_btn_url ); ?>" class="btn-light">
                        <span><?php echo esc_html( $secondary_btn_text ); ?></span>
                        <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M0 8.07692V6.92308H6.92308V0H8.07692V6.92308H15V8.07692H8.07692V15H6.92308V8.07692H0Z" fill="#112D55" />
                        </svg>
                    </a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </section>
    <!-- FAQ end -->
    <?php endif; ?>


<?php get_footer(); ?>
