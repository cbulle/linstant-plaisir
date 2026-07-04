<?php
/**
 * Checkout Form - Thème Luxe & Secret
 */
if ( ! defined( 'ABSPATH' ) ) exit;

wc_print_notices();

do_action( 'woocommerce_before_checkout_form', $checkout );
?>

<div class="checkout-luxe-container">
    <h1 class="playfair-title">Finaliser <span class="gold-text">l'Expérience</span></h1>

    <form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">

        <?php if ( $checkout->get_checkout_fields() ) : ?>

        <div class="checkout-grid">

            <div class="col-billing">
                <?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>
                <div id="customer_details">
                    <?php do_action( 'woocommerce_checkout_billing' ); ?>
                    <?php do_action( 'woocommerce_checkout_shipping' ); ?>
                </div>
                <?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>
            </div>

            <div class="col-order-review">
                <h3 id="order_review_heading"><?php esc_html_e( 'Votre commande', 'linstant-plaisir' ); ?></h3>
                <?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>
                <?php do_action( 'woocommerce_checkout_before_order_review' ); ?>
                <div id="order_review" class="woocommerce-checkout-review-order">
                    <?php do_action( 'woocommerce_checkout_order_review' ); ?>
                </div>
                <?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
            </div>

        </div>

        <?php endif; ?>

    </form>
</div>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>