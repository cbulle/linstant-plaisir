<?php
/**
 * Cart totals - Thème Luxe & Secret
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="cart_totals <?php echo ( WC()->customer->has_calculated_shipping() ) ? 'calculated_shipping' : ''; ?>">

  <?php do_action( 'woocommerce_before_cart_totals' ); ?>

  <h2 class="playfair-title-sm">Résumé</h2>

  <table cellspacing="0" class="shop_table shop_table_responsive_luxe">

    <tr class="cart-subtotal">
      <th>Sous-total</th>
      <td data-title="Sous-total"><?php wc_cart_totals_subtotal_html(); ?></td>
    </tr>

    <?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
      <tr class="cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
        <th>Coupon: <?php echo esc_html( $code ); ?></th>
        <td data-title="Coupon: <?php echo esc_html( $code ); ?>"><?php wc_cart_totals_coupon_html( $coupon ); ?></td>
      </tr>
    <?php endforeach; ?>

    <?php do_action( 'woocommerce_cart_totals_before_order_total' ); ?>

    <tr class="order-total">
      <th>Total <span class="tax-discret">(TTC)</span></th>
      <td data-title="Total" class="gold-text-big"><?php wc_cart_totals_order_total_html(); ?></td>
    </tr>

    <?php do_action( 'woocommerce_cart_totals_after_order_total' ); ?>

  </table>

  <div class="wc-proceed-to-checkout">
    <?php do_action( 'woocommerce_proceed_to_checkout' ); ?>
  </div>

  <?php do_action( 'woocommerce_after_cart_totals' ); ?>

</div>