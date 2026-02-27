<?php
/*
Template Name: Контакты
*/

get_header(); ?>

<?php bcc_breadcrumbs(); ?>

    <section class="contact">
        <div class="container contact-container">
            <h2 class="sec-title contact-title">
                <span><?php echo esc_html( carbon_get_post_meta( get_the_ID(), 'contact_title' ) ?: 'Контакты' ); ?></span>
            </h2>
            <?php 
            $contact_content = get_the_content();
            if ( ! empty( trim( $contact_content ) ) ) :
                echo '<div class="sec-content">' . wp_kses_post( $contact_content ) . '</div>';
            endif;
            ?>
            <div class="contact-content">
                <div class="contact-content__left">
                    <?php
                    $contact_address = carbon_get_post_meta( get_the_ID(), 'contact_address' );
                    $phone_1 = carbon_get_post_meta( get_the_ID(), 'contact_phone_1' );
                    $phone_2 = carbon_get_post_meta( get_the_ID(), 'contact_phone_2' );
                    $email = carbon_get_post_meta( get_the_ID(), 'contact_email' );
                    
                    if ( ! empty( $contact_address ) || ! empty( $phone_1 ) || ! empty( $phone_2 ) || ! empty( $email ) ) :
                    ?>
                    <div class="contact-head">
                        <?php if ( ! empty( $contact_address ) ) : ?>
                        <div class="contact-head__left">
                            <label for="">АДРЕС ОФИСА</label>
                            <p><?php echo wp_kses_post( $contact_address ); ?></p>
                        </div>
                        <?php endif; ?>
                        <?php if ( ! empty( $phone_1 ) || ! empty( $phone_2 ) || ! empty( $email ) ) : ?>
                        <div class="contact-head__right">
                            <label for="">контакты</label>
                            <div class="networks">
                                <?php 
                                $phone_1 = carbon_get_post_meta( get_the_ID(), 'contact_phone_1' );
                                $phone_2 = carbon_get_post_meta( get_the_ID(), 'contact_phone_2' );
                                $email = carbon_get_post_meta( get_the_ID(), 'contact_email' );
                                ?>
                                <?php if ( $phone_1 ) : ?>
                                <a href="tel:<?php echo esc_attr( preg_replace( '/\D/', '', $phone_1 ) ); ?>" target="_blank"><?php echo esc_html( $phone_1 ); ?></a>
                                <?php endif; ?>
                                <?php if ( $phone_2 ) : ?>
                                <a href="tel:<?php echo esc_attr( preg_replace( '/\D/', '', $phone_2 ) ); ?>" target="_blank"><?php echo esc_html( $phone_2 ); ?></a>
                                <?php endif; ?>
                                <?php if ( $email ) : ?>
                                <a href="mailto:<?php echo esc_attr( $email ); ?>" target="_blank"><?php echo esc_html( $email ); ?></a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    <?php
                    $office_schedule = carbon_get_post_meta( get_the_ID(), 'contact_office_schedule' );
                    $warehouse_schedule = carbon_get_post_meta( get_the_ID(), 'contact_warehouse_schedule' );
                    
                    if ( ( ! empty( $office_schedule ) && is_array( $office_schedule ) ) || ( ! empty( $warehouse_schedule ) && is_array( $warehouse_schedule ) ) ) :
                    ?>
                    <div class="contact-foot">
                        <label for="">режим работы</label>
                        <div class="contact-foot__content">
                            <div class="contact-foot__content-item">
                                <h3>Офис</h3>
                                <ul>
                                    <?php 
                                    $office_schedule = carbon_get_post_meta( get_the_ID(), 'contact_office_schedule' );
                                    if ( ! empty( $office_schedule ) && is_array( $office_schedule ) ) :
                                        foreach ( $office_schedule as $schedule ) :
                                            $day = isset( $schedule['day'] ) ? $schedule['day'] : '';
                                            $time = isset( $schedule['time'] ) ? $schedule['time'] : '';
                                    ?>
                                    <li>
                                        <b><?php echo esc_html( $day ); ?>:</b>
                                        <span><?php echo esc_html( $time ); ?></span>
                                    </li>
                                    <?php endforeach; endif; ?>
                                </ul>
                            </div>
                            <div class="contact-foot__content-item">
                                <h3>Склад</h3>
                                <ul>
                                    <?php 
                                    $warehouse_schedule = carbon_get_post_meta( get_the_ID(), 'contact_warehouse_schedule' );
                                    if ( ! empty( $warehouse_schedule ) && is_array( $warehouse_schedule ) ) :
                                        foreach ( $warehouse_schedule as $schedule ) :
                                            $day = isset( $schedule['day'] ) ? $schedule['day'] : '';
                                            $time = isset( $schedule['time'] ) ? $schedule['time'] : '';
                                    ?>
                                    <li>
                                        <b><?php echo esc_html( $day ); ?>:</b>
                                        <span><?php echo esc_html( $time ); ?></span>
                                    </li>
                                    <?php endforeach; endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php
                $contact_details = carbon_get_post_meta( get_the_ID(), 'contact_details' );
                if ( ! empty( $contact_details ) && is_array( $contact_details ) ) :
                ?>
                <div class="contact-content__right">
                    <label for="">реквизиты</label>
                    <ul>
                        <?php 
                        if ( ! empty( $contact_details ) && is_array( $contact_details ) ) :
                            foreach ( $contact_details as $detail ) :
                                $label = isset( $detail['label'] ) ? $detail['label'] : '';
                                $value = isset( $detail['value_text'] ) ? $detail['value_text'] : '';
                        ?>
                        <li><b><?php echo esc_html( $label ); ?>:</b> <?php echo esc_html( $value ); ?></li>
                        <?php endforeach; endif; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </div>
            <div class="contact-map">
                <?php 
                $map_url = carbon_get_post_meta( get_the_ID(), 'contact_map_url' );
                if ( $map_url ) :
                    echo '<iframe src="' . esc_url( $map_url ) . '" width="1280" height="720" frameborder="0"></iframe>';
                endif;
                ?>
            </div>
        </div>
    </section>


<?php get_footer(); ?>
