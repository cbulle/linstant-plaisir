<?php
defined('ABSPATH') || exit;

function linstant_plaisir_scripts()
{
    $theme_version = wp_get_theme()->get('Version');

    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css', array(), '6.5.1');

    wp_enqueue_style('style-principale', get_stylesheet_uri(), array(), $theme_version);
    wp_enqueue_style('theme-global', get_template_directory_uri() . '/css/global.css', array(), $theme_version);
    wp_enqueue_style('theme-header', get_template_directory_uri() . '/css/header.css', array(), $theme_version);
    wp_enqueue_style('theme-footer', get_template_directory_uri() . '/css/footer.css', array(), $theme_version);
    wp_enqueue_style('theme-home', get_template_directory_uri() . '/css/home.css', array(), $theme_version);
    wp_enqueue_style('theme-woocommerce', get_template_directory_uri() . '/css/woocommerce.css', array(), $theme_version);
    wp_enqueue_style('theme-hb', get_template_directory_uri() . '/css/wp-hotel-booking.css', array(), $theme_version);
    wp_enqueue_style('theme-pages', get_template_directory_uri() . '/css/pages.css', array(), $theme_version);
    wp_enqueue_style( 'lip-pages', get_template_directory_uri() . '/pages.css', array('lip-global', 'lip-home'), '1.0' );

    wp_enqueue_script('theme-js', get_template_directory_uri() . '/js/main.js', array('jquery'), $theme_version, true);

    if (is_singular('hb_room') || is_cart() || is_checkout()) {
        wp_enqueue_script('hb-booking-form');
        wp_enqueue_style('wp-hotel-booking');
    }
}
add_action('wp_enqueue_scripts', 'linstant_plaisir_scripts');

function linstant_plaisir_setup()
{
    load_theme_textdomain('linstant-plaisir', get_template_directory() . '/languages');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');

    register_nav_menus(array(
        'menu-principal' => esc_html__('Menu Principal', 'linstant-plaisir'),
    ));
}
add_action('after_setup_theme', 'linstant_plaisir_setup');

// HB utilise WooCommerce pour le paiement
add_filter('wp_hotel_booking_is_checkout_woocommerce', '__return_true');

// Empêcher HB de rediriger sur la page panier WC
add_action('template_redirect', function () {
    if (!function_exists('is_cart'))
        return;
    if (is_cart() || is_checkout()) {
        remove_action('template_redirect', 'tp_hotel_booking_template_redirect', 10);
        remove_action('template_redirect', 'hb_template_redirect', 10);
    }
}, 1);

add_action('woocommerce_before_calculate_totals', function ($cart) {
    if (is_admin() && !defined('DOING_AJAX'))
        return;
    if (did_action('woocommerce_before_calculate_totals') >= 2)
        return;

    foreach ($cart->get_cart() as $key => $item) {
        if (!isset($item['check_in_date']) || !isset($item['check_out_date']))
            continue;

        $check_in = $item['check_in_date'];
        $check_out = $item['check_out_date'];
        $room_id = $item['product_id'];

        // Convertir les dates
        $date_in = DateTime::createFromFormat('Y/m/d', $check_in);
        $date_out = DateTime::createFromFormat('Y/m/d', $check_out);
        if (!$date_in || !$date_out)
            continue;

        // Calculer le prix via HB
        if (function_exists('hb_get_room_price')) {
            $price = hb_get_room_price($room_id, $check_in, $check_out);
        } elseif (class_exists('WP_Hotel_Booking')) {
            $booking = WP_Hotel_Booking::instance();
            if (method_exists($booking, 'get_room_price')) {
                $price = $booking->get_room_price($room_id, $check_in, $check_out);
            }
        }

        // Fallback : calcul manuel basé sur les nuits
        if (empty($price) || $price == 0) {
            $nights = $date_out->diff($date_in)->days;
            if ($nights < 1)
                $nights = 1;

            // Récupérer le prix du produit WC comme base
            $product = wc_get_product($room_id);
            $base_price = $product ? $product->get_regular_price() : 0;

            // Chercher le prix HB via les métadonnées de la chambre
            $hb_price = get_post_meta($room_id, 'tp_price', true);
            if (!$hb_price)
                $hb_price = get_post_meta($room_id, 'hb_price', true);
            if (!$hb_price)
                $hb_price = get_post_meta($room_id, '_price', true);

            $base = $hb_price ? $hb_price : $base_price;
            $price = $base * $nights;
        }

        if ($price > 0) {
            $item['data']->set_price($price);
        }
    }
}, 20);

// Fonction commune : génère le HTML de la grille de suggestions boutique
function linstant_plaisir_shop_suggestions_html( $limit = 6, $exclude = array() )
{
    if (!function_exists('wc_get_products'))
        return;

    $products = wc_get_products(array(
        'status'  => 'publish',
        'limit'   => $limit,
        'orderby' => 'date',
        'order'   => 'DESC',
        'exclude' => $exclude,
    ));

    if (empty($products))
        return;
    ?>
    <div id="shop-suggestions-luxe" class="shop-suggestions-luxe woocommerce">
        <div class="container">
            <h2 class="shop-suggestions-title">L'Univers Secret</h2>
            <ul class="products columns-4">
                <?php foreach ($products as $wc_product): ?>
                    <li <?php wc_product_class('', $wc_product); ?>>
                        <a href="<?php echo esc_url(get_permalink($wc_product->get_id())); ?>"
                            class="woocommerce-LoopProduct-link">
                            <?php echo $wc_product->get_image('medium'); ?>
                            <h2 class="woocommerce-loop-product__title"><?php echo esc_html($wc_product->get_name()); ?></h2>
                            <span class="price"><?php echo $wc_product->get_price_html(); ?></span>
                        </a>
                        <a href="<?php echo esc_url($wc_product->add_to_cart_url()); ?>" class="button" data-quantity="1"
                            data-product_id="<?php echo esc_attr($wc_product->get_id()); ?>">
                            <?php echo esc_html($wc_product->add_to_cart_text()); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <?php
}

// Page room — injecté dans wp_footer, repositionné par JS (voir main.js)
add_action('wp_footer', function () {
    if (!is_singular('hb_room'))
        return;
    linstant_plaisir_shop_suggestions_html(6);
}, 5);

// Page panier — affiché directement après le tableau du panier
add_action('woocommerce_after_cart', function () {
    linstant_plaisir_shop_suggestions_html(6);
});

// Calcule le prix des 7 prochains jours pour la chambre, envoyé au JS
// (utilisé par le bloc "Prix à partir de / Prix :" sur la page room)
add_action('wp_footer', function () {
    if (!is_singular('hb_room'))
        return;

    global $post;
    $room_id = $post->ID;
    $week_prices = array();

    for ($i = 0; $i < 7; $i++) {
        $check_in  = date('Y/m/d', strtotime("+{$i} days"));
        $check_out = date('Y/m/d', strtotime('+' . ($i + 1) . ' days'));

        $price = 0;

        if (function_exists('hb_get_room_price')) {
            $price = hb_get_room_price($room_id, $check_in, $check_out);
        } elseif (class_exists('WP_Hotel_Booking')) {
            $booking = WP_Hotel_Booking::instance();
            if (method_exists($booking, 'get_room_price')) {
                $price = $booking->get_room_price($room_id, $check_in, $check_out);
            }
        }

        // Fallback : métadonnées de la chambre (même logique que le panier)
        if (empty($price) || $price == 0) {
            $hb_price = get_post_meta($room_id, 'tp_price', true);
            if (!$hb_price) $hb_price = get_post_meta($room_id, 'hb_price', true);
            if (!$hb_price) $hb_price = get_post_meta($room_id, '_price', true);
            if (!$hb_price) {
                $product = wc_get_product($room_id);
                $hb_price = $product ? $product->get_regular_price() : 0;
            }
            $price = $hb_price;
        }

        if ($price && $price > 0) {
            $week_prices[$check_in] = (float) $price;
        }
    }

    if (!empty($week_prices)) {
        $lowest = min($week_prices);
        ?>
        <script>
            window.hbRoomWeekPrices  = <?php echo wp_json_encode($week_prices); ?>;
            window.hbRoomLowestPrice = <?php echo wp_json_encode($lowest); ?>;
        </script>
        <?php
    }
    // Si aucun prix n'est trouvé, on ne fait rien silencieusement —
    // le bloc JS gardera alors le prix affiché par défaut par le plugin
}, 4);

// Traitement du formulaire de contact (page-contact.php)
add_action('template_redirect', function () {
    if (!is_page_template('page-contact.php'))
        return;
    // Le traitement réel se fait dans page-contact.php lui-même
});