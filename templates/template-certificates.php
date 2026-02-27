<?php
/*
Template Name: Сертификаты
*/

get_header(); ?>

<?php bcc_breadcrumbs(); ?>

<!-- Certificat -->
<section class="certificat">
    <div class="container certificat-container">
        <div class="certificat-head">
            <h2 class="sec-title certificat-title">
                <span><?php echo esc_html( get_the_title() ); ?></span>
            </h2>
            <?php
            $page_content = trim( get_the_content() );
            if ( ! empty( $page_content ) ) :
            ?>
                <div class="description"><?php echo wp_kses_post( $page_content ); ?></div>
            <?php endif; ?>
        </div>
        
        <?php
        $guarantees = carbon_get_post_meta( get_the_ID(), 'cert_guarantees' );
        if ( ! empty( $guarantees ) && is_array( $guarantees ) ) :
        ?>
        <div class="certificat-content">
            <h3>Гарантии и сертификаты</h3>
            <div class="certificat-list">
                <?php foreach ( $guarantees as $item ) : 
                    $title = isset( $item['title'] ) ? $item['title'] : '';
                    $image = isset( $item['image'] ) ? $item['image'] : '';
                ?>
                <div class="certificat-card">
                    <div class="certificat-card__text">
                        <p><?php echo esc_html( $title ); ?></p>
                        <a href="<?php echo ! empty( $image ) ? esc_url( wp_get_attachment_image_url( $image, 'full' ) ) : '#'; ?>" class="certificat-card__text-icon">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/duble-arrow-right-white.svg" alt="">
                        </a>
                    </div>
                    <?php if ( ! empty( $image ) ) : ?>
                        <?php echo wp_get_attachment_image( $image, 'medium', false, array( 'class' => 'main-img', 'data-fancybox' => 'guarantees' ) ); ?>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <?php
        $technical = carbon_get_post_meta( get_the_ID(), 'cert_technical' );
        if ( ! empty( $technical ) && is_array( $technical ) ) :
        ?>
        <div class="certificat-content">
            <h3>Техническая документация</h3>
            <div class="certificat-list">
                <?php foreach ( $technical as $item ) : 
                    $title = isset( $item['title'] ) ? $item['title'] : '';
                    $image = isset( $item['image'] ) ? $item['image'] : '';
                ?>
                <div class="certificat-card">
                    <div class="certificat-card__text">
                        <p><?php echo esc_html( $title ); ?></p>
                        <a href="<?php echo ! empty( $image ) ? esc_url( wp_get_attachment_image_url( $image, 'full' ) ) : '#'; ?>" class="certificat-card__text-icon">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/duble-arrow-right-white.svg" alt="">
                        </a>
                    </div>
                    <?php if ( ! empty( $image ) ) : ?>
                        <?php echo wp_get_attachment_image( $image, 'medium', false, array( 'class' => 'main-img', 'data-fancybox' => 'technical' ) ); ?>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>
<?php get_template_part( 'template-parts/section-contact-us' ); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Fancybox.bind('[data-fancybox]', {
        on: {
            reveal: (fancybox, slide) => {
                console.log('onReveal');
            }
        }
    });
});
</script>

<?php get_footer(); ?>