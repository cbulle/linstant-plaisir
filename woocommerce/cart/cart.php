<?php
/**
 * Cart Page - Thème Luxe & Secret
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_cart' ); ?>

<div class="woocommerce-cart-wrapper container">
  
  <div class="cart-header-luxe">
    <h1 class="playfair-title"><?php esc_html_e( 'Votre', 'linstant-plaisir' ); ?> <span class="gold-text"><?php esc_html_e( 'Séjour', 'linstant-plaisir' ); ?></span></h1>
    <p class="subtitle-discret"><?php esc_html_e( 'Vérifiez les détails de votre parenthèse intime.', 'linstant-plaisir' ); ?></p>
  </div>

  <form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
    
    <?php do_action( 'woocommerce_before_cart_table' ); ?>

    <div class="cart-items-luxe">
      <?php
      foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
        $_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
        $product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

        if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
          $product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
          ?>
          
          <div class="cart-item-card-luxe <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
            
            <div class="product-thumbnail">
              <?php
              $thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
              if ( ! $product_permalink ) {
                echo $thumbnail; // PHPCS: XSS ok.
              } else {
                printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail ); // PHPCS: XSS ok.
              }
              ?>
            </div>

            <div class="product-details-luxe">
              <div class="product-name-row">
                <div class="product-info-wrapper">
                    <h3 class="product-name">
                    <?php
                    if ( ! $product_permalink ) {
                        echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . '&nbsp;' );
                    } else {
                        echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $cart_item_key ) );
                    }
                    ?>
                    </h3>
                    
                    <?php 
                    // CRITIQUE : C'est ici que s'affichent les dates de réservation WP Hotel Booking !
                    echo wc_get_formatted_cart_item_data( $cart_item ); // PHPCS: XSS ok.
                    ?>
                </div>
                
                <?php
                  echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    'woocommerce_cart_item_remove_link',
                    sprintf(
                      '<a href="%s" class="remove-luxe" aria-label="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
                      esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
                      /* translators: %s is the product name */
                      esc_attr( sprintf( __( 'Remove %s from cart', 'woocommerce' ), wp_strip_all_tags( $_product->get_name() ) ) ),
                      esc_attr( $product_id ),
                      esc_attr( $_product->get_sku() )
                    ),
                    $cart_item_key
                  );
                ?>
              </div>

              <div class="product-meta-row">
                <span class="product-price gold-text">
                  <?php
                    echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); // PHPCS: XSS ok.
                  ?>
                </span>
                
                <?php if ( $_product->is_sold_individually() ) : ?>
                  <span class="qty-discret"><?php esc_html_e( 'Qté: 1', 'linstant-plaisir' ); ?></span>
                  <input type="hidden" name="cart[<?php echo esc_attr( $cart_item_key ); ?>][qty]" value="1" />
                <?php else: ?>
                  <div class="product-quantity">
                    <?php
                      $product_quantity = woocommerce_quantity_input(
                        array(
                          'input_name'   => "cart[{$cart_item_key}][qty]",
                          'input_value'  => $cart_item['quantity'],
                          'max_value'    => $_product->get_max_purchase_quantity(),
                          'min_value'    => '0',
                          'product_name' => $_product->get_name(),
                        ),
                        $_product,
                        false
                      );
                      echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item );
                    ?>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <?php
        }
      }
      ?>
      
      <?php do_action( 'woocommerce_cart_actions' ); ?>
      <?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
      
    </div>

    <?php do_action( 'woocommerce_after_cart_table' ); ?>

  </form>

  <div class="cart-collaterals-luxe">
    <?php do_action( 'woocommerce_cart_collaterals' ); ?>
  </div>

</div>

<?php do_action( 'woocommerce_after_cart' ); ?>