<?php
/*
Template Name: О Компании
*/

get_header(); ?>

<?php bcc_breadcrumbs(); ?>

     <section class="company-home">
                <div class="container company-home__container">
                    <div class="company-home__content">
                        <div class="company-home__head">
                            <h2 class="sec-title">
                                <span> <?php echo esc_html( get_the_title() ); ?></span>
                            </h2>
                           <?php the_content(); ?>
                        </div>
                        <div class="text">
                            <h3><?php echo esc_html( carbon_get_post_meta( get_the_ID(), 'company_goals_title' ) ); ?></h3>
                            <ul>
                                <?php 
                                $goals_items = carbon_get_post_meta( get_the_ID(), 'company_goals_items' );
                                if ( ! empty( $goals_items ) && is_array( $goals_items ) ) :
                                    foreach ( $goals_items as $goal ) :
                                        $goal_text = isset( $goal['text'] ) ? $goal['text'] : '';
                                ?>
                                <li><?php echo wp_kses_post( $goal_text ); ?></li>
                                <?php endforeach; endif; ?>
                            </ul>
                        </div>
                    </div>
                    <ul class="company-home__list">
                        <?php 
                        $company_stats = carbon_get_post_meta( get_the_ID(), 'company_stats' );
                        if ( ! empty( $company_stats ) && is_array( $company_stats ) ) :
                            foreach ( $company_stats as $stat ) :
                                $stat_value = isset( $stat['stat_value'] ) ? $stat['stat_value'] : '';
                                $stat_label = isset( $stat['label'] ) ? $stat['label'] : '';
                        ?>
                        <li>
                            <h3><?php echo wp_kses_post( $stat_value ); ?></h3>
                            <p><?php echo wp_kses_post( $stat_label ); ?></p>
                        </li>
                        <?php endforeach; endif; ?>
                    </ul>
                    <?php 
                    $card_image = carbon_get_post_meta( get_the_ID(), 'company_card_image' );
                    if ( $card_image ) :
                        echo wp_get_attachment_image( $card_image, 'full', false, array( 'class' => 'company-home__card' ) );
                    endif;
                    ?>
                </div>
            </section>
            <!-- Company home end -->

            <!-- Company product -->
            <section class="company-product">
                <div class="container company-product__container">
                    <h2 class="sec-title">
                        <span><?php echo esc_html( carbon_get_post_meta( get_the_ID(), 'company_product_title' ) ?: 'Каталоги продукции 2026' ); ?></span>
                    </h2>
                    <div class="company-product__head">
                        <p><?php echo wp_kses_post( carbon_get_post_meta( get_the_ID(), 'company_product_description' ) ); ?></p>
                        <?php 
                        $product_btn_text = carbon_get_post_meta( get_the_ID(), 'company_product_button_text' );
                        $product_btn_url = carbon_get_post_meta( get_the_ID(), 'company_product_button_url' );
                        ?>
                        <?php if ( $product_btn_text ) : ?>
                            <a href="<?php echo esc_url( $product_btn_url ); ?>" class="btn-light">
                                <span><?php echo esc_html( $product_btn_text ); ?></span>
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/duble-arrow-right.svg" alt="">
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="company-product__content">
                        <?php 
                        $product_items = carbon_get_post_meta( get_the_ID(), 'company_product_items' );
                        if ( ! empty( $product_items ) && is_array( $product_items ) ) :
                            foreach ( $product_items as $index => $item ) :
                                $catalog_title = isset( $item['catalog_title'] ) ? $item['catalog_title'] : '';
                                $catalog_description = isset( $item['catalog_description'] ) ? $item['catalog_description'] : '';
                                $catalog_image = isset( $item['catalog_image'] ) ? $item['catalog_image'] : '';
                                $catalog_button_text = isset( $item['catalog_button_text'] ) ? $item['catalog_button_text'] : 'Скачать каталог PDF';
                                $catalog_pdf_file = isset( $item['catalog_pdf_file'] ) ? $item['catalog_pdf_file'] : '';
                                $catalog_pdf_url = $catalog_pdf_file ? wp_get_attachment_url( $catalog_pdf_file ) : '#';
                                $is_even = ( $index + 1 ) % 2 === 0; // Проверяем четный ли элемент (2-й, 4-й и т.д.)
                        ?>
                        <div class="company-product__content-item">
                            <?php if ( $is_even ) : // Четный элемент - сначала изображение ?>
                                <div class="company-product__content-img">
                                    <?php 
                                    if ( $catalog_image ) :
                                        echo wp_get_attachment_image( $catalog_image, 'full' );
                                    endif;
                                    ?>
                                </div>
                                <div class="company-product__content-text">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/company-logo.svg" alt="" class="logo">
                                    <div class="text">
                                        <h3><?php echo esc_html( $catalog_title ); ?></h3>
                                        <p><?php echo wp_kses_post( $catalog_description ); ?></p>
                                    </div>
                                    <?php if ( $catalog_pdf_file ) : ?>
                                    <a href="<?php echo esc_url( $catalog_pdf_url ); ?>" class="btn-blue">
                                        <span><?php echo esc_html( $catalog_button_text ); ?></span>
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/download-icon.svg" alt="">
                                    </a>
                                    <?php endif; ?>
                                </div>
                            <?php else : // Нечетный элемент - сначала контент ?>
                                <div class="company-product__content-text">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/company-logo.svg" alt="" class="logo">
                                    <div class="text">
                                        <h3><?php echo esc_html( $catalog_title ); ?></h3>
                                        <p><?php echo wp_kses_post( $catalog_description ); ?></p>
                                    </div>
                                    <?php if ( $catalog_pdf_file ) : ?>
                                    <a href="<?php echo esc_url( $catalog_pdf_url ); ?>" class="btn-blue">
                                        <span><?php echo esc_html( $catalog_button_text ); ?></span>
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/download-icon.svg" alt="">
                                    </a>
                                    <?php endif; ?>
                                </div>
                                <div class="company-product__content-img">
                                    <?php 
                                    if ( $catalog_image ) :
                                        echo wp_get_attachment_image( $catalog_image, 'full' );
                                    endif;
                                    ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; endif; ?>
                    </div>
                </div>
            </section>
            <!-- Company product end -->

            <!-- Review -->
       <?php get_template_part( 'template-parts/reviews' ); ?>

<?php get_footer(); ?>
