<?php
defined('ABSPATH') || exit;

get_header(); ?>

<div class="container product-page">

    <?php
    while (have_posts()):
        the_post();

        do_action('woocommerce_before_single_product');

        if (post_password_required()) {
            echo get_the_password_form();
            return;
        }
        ?>

        <div id="product-<?php the_ID(); ?>" <?php wc_product_class(); ?>>

            <div class="product-grid">

                <div class="product-gallery">
                    <?php
                    do_action('woocommerce_before_single_product_summary');
                    ?>
                </div>

                <div class="product-summary">
                    <?php do_action('woocommerce_single_product_summary'); ?>
                </div>

            </div>

            <?php
            do_action('woocommerce_after_single_product_summary');
            ?>

        </div>

    <?php endwhile; ?>

</div>

<?php get_footer(); ?>