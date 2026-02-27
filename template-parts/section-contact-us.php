<?php
$title = carbon_get_theme_option( 'contact_us_title' );
$description = carbon_get_theme_option( 'contact_us_description' );
$button_text = carbon_get_theme_option( 'contact_us_button_text' );
$action_type = carbon_get_theme_option( 'contact_us_action_type' );
$button_url = carbon_get_theme_option( 'contact_us_button_url' );
$bg_image = carbon_get_theme_option( 'contact_us_bg_image' );
$main_image = carbon_get_theme_option( 'contact_us_main_image' );
$sm_image = carbon_get_theme_option( 'contact_us_sm_image' );
?>

<section class="contact-us">
    <div class="container contact-us__container">
        <?php if ( $bg_image ) : ?>
            <?php echo wp_get_attachment_image( $bg_image, 'full', false, array( 'class' => 'bg-img' ) ); ?>
        <?php endif; ?>

        <?php if ( $main_image ) : ?>
            <?php echo wp_get_attachment_image( $main_image, 'full', false, array( 'class' => 'card-img' ) ); ?>
        <?php endif; ?>

        <?php if ( $sm_image ) : ?>
            <?php echo wp_get_attachment_image( $sm_image, 'full', false, array( 'class' => 'card-img sm' ) ); ?>
        <?php endif; ?>

        <div class="contact-us__content">
            <div class="contact-us__head">
                <?php if ( $title ) : ?>
                    <h2><?php echo wp_kses_post( $title ); ?></h2>
                <?php endif; ?>
                <?php if ( $description ) : ?>
                    <p><?php echo wp_kses_post( $description ); ?></p>
                <?php endif; ?>
            </div>

            <?php if ( $button_text ) : ?>
                <?php if ( $action_type === 'modal' ) : ?>
                    <button type="button" class="btn-blue modal-open">
                        <span><?php echo esc_html( $button_text ); ?></span>
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/duble-arrow-right-white.svg" alt="">
                    </button>
                <?php else : ?>
                    <a href="<?php echo esc_url( $button_url ?: '#' ); ?>" class="btn-blue">
                        <span><?php echo esc_html( $button_text ); ?></span>
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/duble-arrow-right-white.svg" alt="">
                    </a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</section>
