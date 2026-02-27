<?php
/**
 * Страница поиска товаров
 *
 * @package BCC_Project
 */

get_header();

$search_query = trim( (string) get_search_query() );
$paged = max( 1, (int) get_query_var( 'paged' ) );
$sort = function_exists( 'bcc_get_current_catalog_sort' ) ? bcc_get_current_catalog_sort() : 'default';
$price_from = function_exists( 'bcc_get_price_from' ) ? bcc_get_price_from() : null;
$price_to = function_exists( 'bcc_get_price_to' ) ? bcc_get_price_to() : null;

$args = array(
    'post_type'      => 'product',
    'post_status'    => 'publish',
    's'              => $search_query,
    'posts_per_page' => 12,
    'paged'          => $paged,
);

if ( $search_query === '' ) {
    $args['post__in'] = array( 0 );
}

if ( $price_from !== null || $price_to !== null ) {
    $meta_query = array();
    $price_conditions = array( 'relation' => 'OR' );
    $keys = array( 'product_price_num', 'product_price' );

    if ( $price_from !== null && $price_to !== null ) {
        if ( $price_from > $price_to ) {
            $tmp = $price_from;
            $price_from = $price_to;
            $price_to = $tmp;
        }

        foreach ( $keys as $key ) {
            $price_conditions[] = array(
                'key'     => $key,
                'value'   => array( $price_from, $price_to ),
                'compare' => 'BETWEEN',
                'type'    => 'NUMERIC',
            );
        }
    } elseif ( $price_from !== null ) {
        foreach ( $keys as $key ) {
            $price_conditions[] = array(
                'key'     => $key,
                'value'   => $price_from,
                'compare' => '>=',
                'type'    => 'NUMERIC',
            );
        }
    } elseif ( $price_to !== null ) {
        foreach ( $keys as $key ) {
            $price_conditions[] = array(
                'key'     => $key,
                'value'   => $price_to,
                'compare' => '<=',
                'type'    => 'NUMERIC',
            );
        }
    }

    if ( count( $price_conditions ) > 1 ) {
        $meta_query[] = $price_conditions;
        $args['meta_query'] = $meta_query;
    }
}

switch ( $sort ) {
    case 'popular':
        $args['orderby'] = 'comment_count';
        $args['order'] = 'DESC';
        break;

    case 'cheap':
        $args['meta_key'] = 'product_price_num';
        $args['meta_type'] = 'NUMERIC';
        $args['orderby'] = 'meta_value_num';
        $args['order'] = 'ASC';
        break;

    case 'exp':
        $args['meta_key'] = 'product_price_num';
        $args['meta_type'] = 'NUMERIC';
        $args['orderby'] = 'meta_value_num';
        $args['order'] = 'DESC';
        break;

    case 'default':
    default:
        $args['orderby'] = 'date';
        $args['order'] = 'DESC';
        break;
}

$products_query = new WP_Query( $args );
?>

<?php bcc_breadcrumbs(); ?>

<section class="catalog">
    <div class="container catalog-container">
        <div class="catalog-head">
            <h2 class="sec-title">
                <span>Результаты поиска<?php echo $search_query ? ': ' . esc_html( $search_query ) : ''; ?></span>
            </h2>
            <p>Найдено: <?php echo (int) $products_query->found_posts; ?></p>
        </div>

        <div class="catalog-content">
            <!-- <form method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="catalog-search-filters">
                <input type="hidden" name="s" value="<?php echo esc_attr( $search_query ); ?>">
                <input type="hidden" name="sort" value="<?php echo esc_attr( $sort ); ?>">
                <input type="text" name="price_from" placeholder="Цена от" value="<?php echo isset( $_GET['price_from'] ) ? esc_attr( wp_unslash( $_GET['price_from'] ) ) : ''; ?>">
                <input type="text" name="price_to" placeholder="Цена до" value="<?php echo isset( $_GET['price_to'] ) ? esc_attr( wp_unslash( $_GET['price_to'] ) ) : ''; ?>">
                <button type="submit">Применить</button>
                <a href="<?php echo esc_url( add_query_arg( array( 's' => $search_query ), home_url( '/' ) ) ); ?>">Сбросить</a>
            </form> -->

            <?php if ( function_exists( 'bcc_render_catalog_sort' ) ) : ?>
                <?php bcc_render_catalog_sort(); ?>
            <?php endif; ?>
        </div>

        <div class="catalog-tab__body active">
            <div class="catalog-list">
                <?php if ( $products_query->have_posts() ) : ?>
                    <?php while ( $products_query->have_posts() ) : $products_query->the_post(); ?>
                        <?php
                        $price = function_exists( 'carbon_get_post_meta' ) ? carbon_get_post_meta( get_the_ID(), 'product_price' ) : get_post_meta( get_the_ID(), 'product_price', true );
                        $gallery = function_exists( 'carbon_get_post_meta' ) ? carbon_get_post_meta( get_the_ID(), 'product_gallery' ) : get_post_meta( get_the_ID(), 'product_gallery', true );
                        ?>
                        <div class="product-card">
                            <?php if ( $gallery && is_array( $gallery ) ) : ?>
                                <div class="swiper product-card__swp">
                                    <div class="swiper-wrapper">
                                        <?php foreach ( $gallery as $image_id ) : ?>
                                            <?php $image = wp_get_attachment_image_url( $image_id, 'medium' ); ?>
                                            <?php if ( $image ) : ?>
                                                <div class="swiper-slide">
                                                    <img src="<?php echo esc_url( $image ); ?>" alt="<?php the_title_attribute(); ?>">
                                                </div>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="swp-pagination"></div>
                                </div>
                            <?php elseif ( has_post_thumbnail() ) : ?>
                                <div class="swiper product-card__swp">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <?php the_post_thumbnail( 'medium' ); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php else : ?>
                                <div class="swiper product-card__swp">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/product-card-1.png' ); ?>" alt="<?php the_title_attribute(); ?>">
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="product-card__body">
                                <div class="product-card__body-text">
                                    <h3><?php the_title(); ?></h3>
                                    <p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 20 ) ); ?></p>
                                </div>
                                <?php if ( $price ) : ?>
                                    <div class="price"><?php echo esc_html( $price ); ?> руб/м2</div>
                                <?php endif; ?>
                                <a href="<?php the_permalink(); ?>">Подробнее</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else : ?>
                    <p>По вашему запросу товары не найдены.</p>
                <?php endif; ?>
            </div>
        </div>

        <?php
        $pagination = paginate_links( array(
            'base'      => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
            'format'    => '?paged=%#%',
            'current'   => $paged,
            'total'     => (int) $products_query->max_num_pages,
            'mid_size'  => 2,
            'prev_text' => '<img src="' . esc_url( get_template_directory_uri() . '/assets/images/prev-icon.svg' ) . '" alt="Предыдущая">',
            'next_text' => '<img src="' . esc_url( get_template_directory_uri() . '/assets/images/next-icon.svg' ) . '" alt="Следующая">',
            'add_args'  => array_filter( array(
                's'          => $search_query,
                'sort'       => isset( $_GET['sort'] ) ? sanitize_text_field( wp_unslash( $_GET['sort'] ) ) : null,
                'price_from' => isset( $_GET['price_from'] ) ? sanitize_text_field( wp_unslash( $_GET['price_from'] ) ) : null,
                'price_to'   => isset( $_GET['price_to'] ) ? sanitize_text_field( wp_unslash( $_GET['price_to'] ) ) : null,
            ), function( $value ) {
                return $value !== null && $value !== '';
            } ),
        ) );

        if ( $pagination ) {
            echo '<div class="pagination">' . wp_kses_post( $pagination ) . '</div>';
        }
        ?>
    </div>
</section>

<?php
wp_reset_postdata();
get_footer();
