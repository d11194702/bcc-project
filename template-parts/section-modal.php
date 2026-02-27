    <section class="modal" id="consultation-modal">
        <div class="modal-bg"></div>
        <div class="modal-dialog">
            <div class="container modal-container">
                <div class="modal-content">
                    <button class="modal-close">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/modal-close-icon.svg" alt="">
                    </button>
                    <div class="modal-content__head">
                        <h2>Бесплатная консультация</h2>
                        <p>Если у вас есть какие-либо вопросы, свяжитесь с нами в любом удобном для вас мессенджере либо напрямую по телефону. Наш специалист проконсультирует вас и предложит решение под ваш запрос.</p>
                    </div>
                    <div class="modal-content__foot">
                        <?php
                        $whatsapp = carbon_get_theme_option( 'theme_whatsapp' );
                        $telegram = carbon_get_theme_option( 'theme_telegram' );
                        $vk = carbon_get_theme_option( 'theme_vk' );
                        ?>
                        <div class="networks">
                            <?php if ( ! empty( $whatsapp ) ) : ?>
                                <a href="https://wa.me/<?php echo esc_attr( $whatsapp ); ?>" class="btn-blue" target="_blank">WHATSAPP</a>
                            <?php endif; ?>
                            <?php if ( ! empty( $telegram ) ) : ?>
                                <a href="<?php echo esc_url( $telegram ); ?>" class="btn-lightblue" target="_blank">TELEGRAM</a>
                            <?php endif; ?>
                            <?php if ( ! empty( $vk ) ) : ?>
                                <a href="<?php echo esc_url( $vk ); ?>" class="btn-light" target="_blank">VK</a>
                            <?php endif; ?>
                        </div>
                        <div class="links">
                            <?php
                            $phone_1 = carbon_get_theme_option( 'theme_phone' );
                            $phone_2 = carbon_get_theme_option( 'theme_phone_alt' );
                            $email = carbon_get_theme_option( 'theme_email' );
                            ?>
                            <?php if ( ! empty( $phone_1 ) ) : ?>
                                <a href="tel:<?php echo esc_attr( preg_replace( '/\D/', '', $phone_1 ) ); ?>" target="_blank"><?php echo esc_html( $phone_1 ); ?></a>
                            <?php endif; ?>
                            <?php if ( ! empty( $phone_2 ) ) : ?>
                                <a href="tel:<?php echo esc_attr( preg_replace( '/\D/', '', $phone_2 ) ); ?>" target="_blank"><?php echo esc_html( $phone_2 ); ?></a>
                            <?php endif; ?>
                            <?php if ( ! empty( $email ) ) : ?>
                                <a href="mailto:<?php echo esc_attr( $email ); ?>" target="_blank"><?php echo esc_html( $email ); ?></a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
     