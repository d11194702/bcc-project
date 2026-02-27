       </main>
     <!-- Footer -->
        <footer class="footer">
            <div class="container">
                <div class="footer-head">
                    <div class="footer-head__left">
                        <a href="<?php echo home_url('/'); ?>" class="footer-logo">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo-light.svg" alt="">
                            <span>БАЛТСЕВЕРСТАЛЬ</span>
                        </a>
                    </div>
                    <div class="footer-head__right">
                        <div class="footer-head__right-item">
                            <label for="">АДРЕС ОФИСА</label>
                            <p><?php echo nl2br( esc_html( get_theme_footer_address() ) ); ?></p>
                        </div>
                        <div class="footer-head__right-item">
                            <label for="">СВЯЗЬ</label>
                            <?php 
                                $footer_phone = get_theme_footer_phone();
                                if ( $footer_phone ) {
                                    echo '<a href="tel:' . esc_attr( $footer_phone ) . '" target="_blank">' . esc_html( $footer_phone ) . '</a>';
                                }
                            ?>
                            <?php 
                                $footer_email = get_theme_footer_email();
                                if ( $footer_email ) {
                                    echo '<a href="mailto:' . esc_attr( $footer_email ) . '" target="_blank">' . esc_html( $footer_email ) . '</a>';
                                }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="footer-content">
                    <div class="footer-content__left">
                        <ul class="networks">
                            <?php 
                                $whatsapp = get_theme_whatsapp_link();
                                if ( $whatsapp ) {
                                    echo '<li><a href="' . esc_url( $whatsapp ) . '" target="_blank">Whatsapp</a></li>';
                                    echo '<li><span>/</span></li>';
                                }
                                
                                $vk = get_theme_vk();
                                if ( $vk ) {
                                    echo '<li><a href="' . esc_url( $vk ) . '" target="_blank">VK</a></li>';
                                    if ( get_theme_telegram() ) {
                                        echo '<li><span>/</span></li>';
                                    }
                                }
                                
                                $telegram = get_theme_telegram();
                                if ( $telegram ) {
                                    echo '<li><a href="' . esc_url( $telegram ) . '" target="_blank">Telegram</a></li>';
                                }
                            ?>
                        </ul>
                        <?php
                            $footer_ratings = get_theme_footer_ratings();
                            $footer_ratings_prepared = array();

                            if ( ! empty( $footer_ratings ) && is_array( $footer_ratings ) ) {
                                foreach ( $footer_ratings as $rating ) {
                                    $title = isset( $rating['title'] ) ? trim( $rating['title'] ) : '';
                                    $value = isset( $rating['rating_value'] ) ? trim( $rating['rating_value'] ) : '';

                                    if ( $title && $value ) {
                                        $footer_ratings_prepared[] = array(
                                            'title' => $title,
                                            'value' => $value,
                                        );
                                    }
                                }
                            }

                            if ( ! empty( $footer_ratings_prepared ) ) :
                        ?>
                            <ul class="ratings">
                                <?php foreach ( $footer_ratings_prepared as $rating_item ) : ?>
                                    <li>
                                        <span>Отзывы <br> <?php echo esc_html( $rating_item['title'] ); ?></span>
                                        <div class="line"></div>
                                        <b><?php echo esc_html( $rating_item['value'] ); ?></b>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                    <div class="footer-content__right">
                        <ul>
                        <?php
                        wp_nav_menu([
                            'theme_location' => 'footer_menu',
                            'container' => false,
                            'items_wrap' => '%3$s',
                            'walker' => new BCC_Clean_Walker(),
                            'fallback_cb' => false,
                        ]);
                        ?>
                        </ul>
                        <div class="footer-content__right-text">
                            <div class="link">
                                <?php 
                                    $footer_links = get_theme_footer_links();
                                    if ( ! empty( $footer_links ) ) {
                                        foreach ( $footer_links as $link ) {
                                            if ( ! empty( $link['text'] ) && ! empty( $link['url'] ) ) {
                                                echo '<a href="' . esc_url( $link['url'] ) . '" target="_blank" rel="noopener">' . esc_html( $link['text'] ) . '</a>';
                                            }
                                        }
                                    }
                                ?>
                            </div>
                            <?php 
                                $copyright = get_theme_footer_copyright();
                                if ( $copyright ) {
                                    echo '<div style="margin-top: 15px; font-size: 12px; color: #7d7d7d;">' . wp_kses_post( $copyright ) . '</div>';
                                }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <!-- Footer end -->
<?php get_template_part( 'template-parts/section-modal' ); ?>
    </div>

<?php wp_footer(); ?>
</body>
</html>
