<?php
/**
 * Шаблон для отображения результата поиска
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'search-result-item' ); ?>>
    <div class="search-result-item__content">
        <?php if ( has_post_thumbnail() ) : ?>
            <div class="search-result-item__thumbnail">
                <a href="<?php the_permalink(); ?>">
                    <?php the_post_thumbnail( 'medium', array( 'alt' => get_the_title() ) ); ?>
                </a>
            </div>
        <?php endif; ?>

        <div class="search-result-item__text">
            <h3 class="search-result-item__title">
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            </h3>

            <div class="search-result-item__excerpt">
                <?php the_excerpt(); ?>
            </div>

            <?php 
            // Если это товар, показываем цену
            if ( get_post_type() === 'product' ) {
                $price = get_post_meta( get_the_ID(), 'product_price', true );
                if ( $price ) {
                    echo '<div class="search-result-item__price"><strong>' . esc_html( $price ) . '</strong></div>';
                }
            }
            ?>

            <a href="<?php the_permalink(); ?>" class="search-result-item__link">
                <?php _e( 'Узнать больше →', 'bcc' ); ?>
            </a>
        </div>
    </div>
</article>
