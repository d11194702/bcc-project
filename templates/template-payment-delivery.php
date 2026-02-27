<?php
/*
Template Name: Доставка и оплата
*/

get_header(); ?>

<?php bcc_breadcrumbs(); ?>

    <section class="payment_delivery">
      <div class="container">
        <div class="payment_delivery_head">
          <h2 class="sec-title certificat-title">
              <span>
                <?php echo esc_html( get_the_title() ); ?>
              </span>
          </h2>         
            <?php the_content(); ?>         
        </div>
        <div class="tabs" data-tabs>
          <div class="tabs__nav">
            <button class="tabs__btn is-active" data-tab="tab-1">
              Доставка
            </button>
            <button class="tabs__btn" data-tab="tab-2">
              Оплата
            </button>
            <button class="tabs__btn tabs__btn-full" data-tab="tab-3">
              График работы
            </button>
          </div>

          <div class="tabs__content">
            <div class="tabs__panel is-active" id="tab-1">
              <div class="payment_delivery_content">
                <div class="payment_delivery_banner one">
                  <div class="payment_delivery_banner_text">
                    <h3 class="one">
                      <?php echo esc_html( carbon_get_post_meta( get_the_ID(), 'pd_delivery_banner_text' ) ?: 'БСС' ); ?>
                    </h3>
                  </div>
                  <?php 
                  $delivery_image = carbon_get_post_meta( get_the_ID(), 'pd_delivery_banner_image' );
                  if ( $delivery_image ) :
                      echo wp_get_attachment_image( $delivery_image, 'full', false, array( 'class' => 'one' ) );
                  endif;
                  ?>
                  <div class="circle"></div>
                </div>
                <div class="payment_delivery_text">
                  <?php echo wp_kses_post( carbon_get_post_meta( get_the_ID(), 'pd_delivery_content' ) ); ?>
                </div>
              </div>
            </div>
            <div class="tabs__panel" id="tab-2">
              <div class="payment_delivery_content">
                <div class="payment_delivery_banner two">
                  <div class="payment_delivery_banner_text">
                    <h3 class="one">
                      <?php echo esc_html( carbon_get_post_meta( get_the_ID(), 'pd_payment_banner_text' ) ?: 'БСС' ); ?>
                    </h3>
                  </div>
                  <?php 
                  $payment_image = carbon_get_post_meta( get_the_ID(), 'pd_payment_banner_image' );
                  if ( $payment_image ) :
                      echo wp_get_attachment_image( $payment_image, 'full', false, array( 'class' => 'two' ) );
                  endif;
                  ?>
                </div>
                <div class="payment_delivery_text">
                  <?php echo wp_kses_post( carbon_get_post_meta( get_the_ID(), 'pd_payment_content' ) ); ?>
                </div>
              </div>
            </div>
            <div class="tabs__panel" id="tab-3">
              <div class="payment_delivery_content">
                <div class="payment_delivery_banner three">
                  <div class="payment_delivery_banner_text">
                    <h3 class="one">
                      <?php echo esc_html( carbon_get_post_meta( get_the_ID(), 'pd_schedule_banner_text' ) ?: 'БСС' ); ?>
                    </h3>
                  </div>
                  <?php 
                  $schedule_image = carbon_get_post_meta( get_the_ID(), 'pd_schedule_banner_image' );
                  if ( $schedule_image ) :
                      echo wp_get_attachment_image( $schedule_image, 'full', false, array( 'class' => 'three' ) );
                  endif;
                  ?>
                </div>
                <div class="payment_delivery_text">
                  <?php echo wp_kses_post( carbon_get_post_meta( get_the_ID(), 'pd_schedule_content' ) ); ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

<?php get_footer(); ?>
