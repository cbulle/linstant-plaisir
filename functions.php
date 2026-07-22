<?php
defined('ABSPATH') || exit;

/* ==========================================================================
   ASSETS
   ========================================================================== */

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
    wp_enqueue_style('theme-booking', get_template_directory_uri() . '/css/booking.css', array(), $theme_version);
    wp_enqueue_style('theme-pages', get_template_directory_uri() . '/css/pages.css', array(), $theme_version);
    wp_enqueue_style('lip-pages', get_template_directory_uri() . '/pages.css', array('lip-global', 'lip-home'), '1.0');

    $theme_js_deps = array('jquery');

    // Flatpickr : calendrier de dates avec blocage des jours déjà réservés — uniquement sur la page Love Room
    // Hébergé en local dans le thème (au lieu du CDN cdnjs) pour éviter qu'un bloqueur de pub / vie privée
    // du navigateur ne bloque silencieusement le script et casse le calendrier + le calcul du prix.
    // Filet de sécurité : si le fichier local est absent/mal nommé sur le serveur (donc erreur 404),
    // un script de secours identique est chargé automatiquement depuis un CDN, pour que le
    // calendrier fonctionne toujours, même en cas d'oubli lors de l'upload par (S)FTP.
    if (function_exists('is_product') && is_product() && get_queried_object_id() === lip_get_room_product_id()) {
        wp_enqueue_style('flatpickr', get_template_directory_uri() . '/css/flatpickr.min.css', array(), '4.6.13');

        wp_enqueue_script('flatpickr', get_template_directory_uri() . '/js/flatpickr/flatpickr.min.js', array(), '4.6.13', true);
        wp_add_inline_script('flatpickr', "if (typeof flatpickr === 'undefined') { document.write('<scr' + 'ipt src=\"https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js\"><\/scr' + 'ipt>'); }");

        wp_enqueue_script('flatpickr-fr', get_template_directory_uri() . '/js/flatpickr/fr.js', array('flatpickr'), '4.6.13', true);
        wp_add_inline_script('flatpickr-fr', "if (typeof flatpickr === 'undefined' || !flatpickr.l10ns || !flatpickr.l10ns.fr) { document.write('<scr' + 'ipt src=\"https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/l10n/fr.js\"><\/scr' + 'ipt>'); }");

        $theme_js_deps[] = 'flatpickr';
        $theme_js_deps[] = 'flatpickr-fr';
    }

    wp_enqueue_script('theme-js', get_template_directory_uri() . '/js/main.js', $theme_js_deps, $theme_version, true);
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


/* ==========================================================================
   RÉSERVATION LOVE ROOM — remplace WP Hotel Booking
   ========================================================================== */

// Retrouve l'ID produit WooCommerce de la Love Room à partir de son slug.
function lip_get_room_product_id()
{
    static $id = null;
    if ($id === null) {
        $product = get_page_by_path('loveroom', OBJECT, 'product');
        $id = $product ? $product->ID : 0;
    }
    return $id;
}

// Renvoie le prix d'une nuit pour une date donnée (format Y-m-d).
// Ordre de priorité : 1) tarif spécial pour CETTE date précise, 2) tarif du jour de la semaine
// (Lundi/Mardi/.../Dimanche), 3) prix par défaut du produit (onglet Général).
function lip_get_room_price_for_date($date_ymd, $default_price)
{
    $product_id = lip_get_room_product_id();

    // 1) Tarif spécial sur une date précise (prioritaire sur tout le reste)
    $overrides = get_post_meta($product_id, '_lip_price_overrides', true);
    if (is_array($overrides) && isset($overrides[$date_ymd]) && $overrides[$date_ymd] > 0) {
        return (float) $overrides[$date_ymd];
    }

    // 2) Tarif du jour de la semaine (1 = Lundi ... 7 = Dimanche)
    $weekly = get_post_meta($product_id, '_lip_weekly_prices', true);
    if (is_array($weekly)) {
        $weekday = date('N', strtotime($date_ymd)); // '1' à '7'
        if (isset($weekly[$weekday]) && $weekly[$weekday] > 0) {
            return (float) $weekly[$weekday];
        }
    }

    // 3) Prix par défaut du produit
    return (float) $default_price;
}

// Empêche le double-affichage : sur la fiche Love Room, on retire le prix et le bouton
// "Ajouter au panier" par défaut de WooCommerce, remplacés par notre boîte de réservation
// tout-en-un ci-dessous (dates + prix en direct + bouton), affichée dans TOUS les cas.
add_action('woocommerce_single_product_summary', function () {
    global $post;
    if (!$post || (int) $post->ID !== lip_get_room_product_id())
        return;
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
}, 1);

// Boîte de réservation Love Room : dates d'arrivée/départ, prix recalculé en direct par main.js,
// et bouton "Réserver mon séjour". C'est un formulaire autonome qui poste directement vers le
// gestionnaire standard d'ajout au panier de WooCommerce (name="add-to-cart"), donc elle
// s'affiche systématiquement, sans dépendre du rendu interne du template WooCommerce.
add_action('woocommerce_single_product_summary', 'lip_render_room_booking_box', 25);
function lip_render_room_booking_box()
{
    global $product;
    if (!$product || $product->get_id() !== lip_get_room_product_id())
        return;

    $today    = date('Y-m-d');
    $tomorrow = date('Y-m-d', strtotime('+1 day'));

    // Filet de sécurité admin : si le prix par défaut (onglet Général) est vide, WooCommerce
    // considère le produit comme non "achetable" et masque normalement tout. Ici la boîte de
    // réservation s'affiche quand même, mais on prévient l'admin pour qu'il corrige la vraie cause.
    if (current_user_can('manage_woocommerce') && $product->get_regular_price() === '') {
        echo '<p style="background:#fff3cd;border:1px solid #e0a800;color:#7a5c00;padding:10px 14px;border-radius:4px;font-size:13px;">'
            . esc_html__("Admin uniquement : le champ « Prix régulier » (onglet Général de la fiche produit) est vide. Sans lui, le prix par défaut ne peut pas s'appliquer et WooCommerce peut masquer le bouton d'achat pour les clients.", 'linstant-plaisir')
            . '</p>';
    }
    ?>
    <div class="lip-booking-box">
        <form class="cart lip-booking-form" method="post" enctype="multipart/form-data">

            <div class="lip-booking-dates">
                <p class="form-row lip-check-in">
                    <label for="lip_check_in"><?php esc_html_e('Arrivée', 'linstant-plaisir'); ?></label>
                    <input type="text" id="lip_check_in" name="lip_check_in" readonly
                        value="<?php echo esc_attr($today); ?>" required>
                </p>
                <p class="form-row lip-check-out">
                    <label for="lip_check_out"><?php esc_html_e('Départ', 'linstant-plaisir'); ?></label>
                    <input type="text" id="lip_check_out" name="lip_check_out" readonly
                        value="<?php echo esc_attr($tomorrow); ?>" required>
                </p>
                <p class="lip-price-box">
                    <span class="lip-price-label"><?php esc_html_e('Prix du séjour', 'linstant-plaisir'); ?></span>
                    <span class="lip-price-amount" id="lip_price_amount">…</span>
                </p>
            </div>

            <input type="hidden" name="quantity" value="1">
            <button type="submit" name="add-to-cart" value="<?php echo esc_attr($product->get_id()); ?>"
                class="single_add_to_cart_button button alt lip-book-button">
                <?php esc_html_e('Réserver mon séjour', 'linstant-plaisir'); ?>
            </button>

        </form>
    </div>
    <?php
}

// Vérifie les dates avant l'ajout au panier : format valide + pas de chevauchement (site + Airbnb + Booking.com)
add_filter('woocommerce_add_to_cart_validation', 'lip_validate_room_dates', 10, 3);
function lip_validate_room_dates($passed, $product_id, $quantity)
{
    if ($product_id !== lip_get_room_product_id())
        return $passed;

    if (empty($_POST['lip_check_in']) || empty($_POST['lip_check_out'])) {
        wc_add_notice(__("Merci de sélectionner une date d'arrivée et de départ.", 'linstant-plaisir'), 'error');
        return false;
    }

    $date_in  = DateTime::createFromFormat('Y-m-d', wc_clean(wp_unslash($_POST['lip_check_in'])));
    $date_out = DateTime::createFromFormat('Y-m-d', wc_clean(wp_unslash($_POST['lip_check_out'])));

    if (!$date_in || !$date_out || $date_out <= $date_in) {
        wc_add_notice(__('Les dates sélectionnées ne sont pas valides.', 'linstant-plaisir'), 'error');
        return false;
    }

    if (lip_room_has_conflict($date_in, $date_out)) {
        wc_add_notice(__("Ces dates ne sont plus disponibles, merci d'en choisir d'autres.", 'linstant-plaisir'), 'error');
        return false;
    }

    return $passed;
}

// Vérifie la plage demandée contre : les commandes déjà passées sur le site, ET les réservations
// importées d'Airbnb / Booking.com. Le site reste toujours prioritaire : cette fonction ne sert
// qu'à bloquer une NOUVELLE réservation, jamais à annuler une commande déjà existante sur le site.
function lip_room_has_conflict($date_in, $date_out, $exclude_order_id = 0)
{
    $orders = wc_get_orders(array(
        'status'  => array('processing', 'completed', 'on-hold'),
        'limit'   => -1,
        'exclude' => array($exclude_order_id),
    ));

    foreach ($orders as $order) {
        foreach ($order->get_items() as $item) {
            if ($item->get_product_id() !== lip_get_room_product_id())
                continue;

            $existing_in  = $item->get_meta('_lip_check_in');
            $existing_out = $item->get_meta('_lip_check_out');
            if (!$existing_in || !$existing_out)
                continue;

            $existing_date_in  = DateTime::createFromFormat('Y/m/d', $existing_in);
            $existing_date_out = DateTime::createFromFormat('Y/m/d', $existing_out);
            if (!$existing_date_in || !$existing_date_out)
                continue;

            if ($date_in < $existing_date_out && $existing_date_in < $date_out) {
                return true;
            }
        }
    }

    $external = get_option('lip_external_busy_dates', array());
    if (is_array($external)) {
        foreach ($external as $range) {
            $ext_start = DateTime::createFromFormat('Y-m-d', $range['start']);
            $ext_end   = DateTime::createFromFormat('Y-m-d', $range['end']);
            if (!$ext_start || !$ext_end)
                continue;

            if ($date_in < $ext_end && $ext_start < $date_out) {
                return true;
            }
        }
    }

    return false;
}

// Stocke les dates choisies dans le panier (format Y/m/d, utilisé partout ailleurs sur le site)
add_filter('woocommerce_add_cart_item_data', 'lip_add_cart_item_data', 10, 2);
function lip_add_cart_item_data($cart_item_data, $product_id)
{
    if ($product_id !== lip_get_room_product_id())
        return $cart_item_data;

    if (empty($_POST['lip_check_in']) || empty($_POST['lip_check_out']))
        return $cart_item_data;

    $date_in  = DateTime::createFromFormat('Y-m-d', wc_clean(wp_unslash($_POST['lip_check_in'])));
    $date_out = DateTime::createFromFormat('Y-m-d', wc_clean(wp_unslash($_POST['lip_check_out'])));

    if (!$date_in || !$date_out || $date_out <= $date_in)
        return $cart_item_data;

    $cart_item_data['lip_check_in']  = $date_in->format('Y/m/d');
    $cart_item_data['lip_check_out'] = $date_out->format('Y/m/d');
    $cart_item_data['unique_key']    = md5(microtime() . wp_rand());

    return $cart_item_data;
}

// Affiche Arrivée / Départ dans le panier (cart.php les récupère via wc_get_formatted_cart_item_data)
add_filter('woocommerce_get_item_data', 'lip_display_cart_item_dates', 10, 2);
function lip_display_cart_item_dates($item_data, $cart_item)
{
    if (!empty($cart_item['lip_check_in'])) {
        $item_data[] = array(
            'name'  => __('Arrivée', 'linstant-plaisir'),
            'value' => wc_clean($cart_item['lip_check_in']),
        );
    }
    if (!empty($cart_item['lip_check_out'])) {
        $item_data[] = array(
            'name'  => __('Départ', 'linstant-plaisir'),
            'value' => wc_clean($cart_item['lip_check_out']),
        );
    }
    return $item_data;
}

// Recalcule le prix nuit par nuit, en tenant compte des tarifs spéciaux par date
add_action('woocommerce_before_calculate_totals', function ($cart) {
    if (is_admin() && !defined('DOING_AJAX'))
        return;
    if (did_action('woocommerce_before_calculate_totals') >= 2)
        return;

    foreach ($cart->get_cart() as $item) {
        if (empty($item['lip_check_in']) || empty($item['lip_check_out']))
            continue;

        $date_in  = DateTime::createFromFormat('Y/m/d', $item['lip_check_in']);
        $date_out = DateTime::createFromFormat('Y/m/d', $item['lip_check_out']);
        if (!$date_in || !$date_out)
            continue;

        $default_price = (float) $item['data']->get_regular_price();
        $total  = 0;
        $cursor = clone $date_in;

        while ($cursor < $date_out) {
            $total += lip_get_room_price_for_date($cursor->format('Y-m-d'), $default_price);
            $cursor->modify('+1 day');
        }

        if ($total > 0) {
            $item['data']->set_price($total);
        }
    }
}, 20);

// Sauvegarde les dates sur la ligne de commande (email / wp-admin + réutilisées pour les conflits et l'export iCal)
add_action('woocommerce_checkout_create_order_line_item', 'lip_save_order_item_dates', 10, 4);
function lip_save_order_item_dates($item, $cart_item_key, $values, $order)
{
    if (!empty($values['lip_check_in'])) {
        $item->add_meta_data(__('Arrivée', 'linstant-plaisir'), $values['lip_check_in']);
        $item->add_meta_data('_lip_check_in', $values['lip_check_in']);
    }
    if (!empty($values['lip_check_out'])) {
        $item->add_meta_data(__('Départ', 'linstant-plaisir'), $values['lip_check_out']);
        $item->add_meta_data('_lip_check_out', $values['lip_check_out']);
    }
}

// Envoie au JS : prix des 60 prochains jours + toutes les plages déjà indisponibles (site + Airbnb + Booking.com)
add_action('wp_footer', function () {
    global $post;
    if (!is_product() || !$post || (int) $post->ID !== lip_get_room_product_id())
        return;

    $product = wc_get_product($post->ID);
    if (!$product)
        return;

    $default_price = (float) $product->get_regular_price();
    $day_prices    = array();
    for ($i = 0; $i < 60; $i++) {
        $date = date('Y-m-d', strtotime("+{$i} days"));
        $day_prices[$date] = lip_get_room_price_for_date($date, $default_price);
    }

    $disabled_ranges = array();

    $orders = wc_get_orders(array('status' => array('processing', 'completed', 'on-hold'), 'limit' => -1));
    foreach ($orders as $order) {
        foreach ($order->get_items() as $item) {
            if ($item->get_product_id() !== lip_get_room_product_id())
                continue;
            $in  = $item->get_meta('_lip_check_in');
            $out = $item->get_meta('_lip_check_out');
            if (!$in || !$out)
                continue;
            $date_in  = DateTime::createFromFormat('Y/m/d', $in);
            $date_out = DateTime::createFromFormat('Y/m/d', $out);
            if (!$date_in || !$date_out)
                continue;
            // Le dernier jour occupé est la veille du départ
            $last_night = (clone $date_out)->modify('-1 day');
            $disabled_ranges[] = array('from' => $date_in->format('Y-m-d'), 'to' => $last_night->format('Y-m-d'));
        }
    }

    $external = get_option('lip_external_busy_dates', array());
    if (is_array($external)) {
        foreach ($external as $range) {
            $ext_end    = DateTime::createFromFormat('Y-m-d', $range['end']);
            $last_night = $ext_end ? $ext_end->modify('-1 day')->format('Y-m-d') : $range['end'];
            $disabled_ranges[] = array('from' => $range['start'], 'to' => $last_night);
        }
    }

    $lowest = !empty($day_prices) ? min($day_prices) : 0;
    ?>
    <script>
        window.lipRoomDayPrices   = <?php echo wp_json_encode($day_prices); ?>;
        window.lipRoomLowestPrice = <?php echo wp_json_encode($lowest); ?>;
        window.lipDisabledRanges  = <?php echo wp_json_encode($disabled_ranges); ?>;
    </script>
    <?php
}, 4);


/* ==========================================================================
   ADMIN — onglet "Tarifs & disponibilité" sur la fiche produit Love Room
   ========================================================================== */

add_filter('woocommerce_product_data_tabs', function ($tabs) {
    global $post;
    if (!$post || (int) $post->ID !== lip_get_room_product_id())
        return $tabs;

    $tabs['lip_pricing'] = array(
        'label'  => __('Tarifs & disponibilité', 'linstant-plaisir'),
        'target' => 'lip_pricing_data',
        'class'  => array(),
    );
    return $tabs;
});

add_action('woocommerce_product_data_panels', function () {
    global $post;
    if (!$post || (int) $post->ID !== lip_get_room_product_id())
        return;

    $weekly_labels = array(
        1 => __('Lundi', 'linstant-plaisir'),
        2 => __('Mardi', 'linstant-plaisir'),
        3 => __('Mercredi', 'linstant-plaisir'),
        4 => __('Jeudi', 'linstant-plaisir'),
        5 => __('Vendredi', 'linstant-plaisir'),
        6 => __('Samedi', 'linstant-plaisir'),
        7 => __('Dimanche', 'linstant-plaisir'),
    );
    $weekly = get_post_meta($post->ID, '_lip_weekly_prices', true);
    if (!is_array($weekly))
        $weekly = array();

    $overrides = get_post_meta($post->ID, '_lip_price_overrides', true);
    if (!is_array($overrides))
        $overrides = array();
    ksort($overrides);
    ?>
    <div id="lip_pricing_data" class="panel woocommerce_options_panel">

        <div class="options_group" style="padding: 12px;">
            <p><strong><?php esc_html_e('1. Prix par jour de la semaine', 'linstant-plaisir'); ?></strong><br>
                <?php esc_html_e("Ce sont vos tarifs habituels, toujours les mêmes chaque semaine. Laissez un champ vide pour utiliser le prix par défaut (onglet Général) ce jour-là.", 'linstant-plaisir'); ?>
            </p>
            <table class="widefat lip-weekly-prices-table" style="max-width: 420px;">
                <thead>
                    <tr>
                        <th><?php esc_html_e('Jour', 'linstant-plaisir'); ?></th>
                        <th><?php esc_html_e('Prix (€)', 'linstant-plaisir'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($weekly_labels as $day_num => $day_label): ?>
                        <tr>
                            <td><?php echo esc_html($day_label); ?></td>
                            <td>
                                <input type="number" step="0.01" min="0"
                                    name="lip_weekly_price[<?php echo esc_attr($day_num); ?>]"
                                    value="<?php echo isset($weekly[$day_num]) ? esc_attr($weekly[$day_num]) : ''; ?>"
                                    placeholder="<?php esc_attr_e('prix par défaut', 'linstant-plaisir'); ?>">
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="options_group" style="padding: 12px; border-top: 1px solid #eee;">
            <p><strong><?php esc_html_e('2. Dates exceptionnelles', 'linstant-plaisir'); ?></strong><br>
                <?php esc_html_e("Pour une date précise (fête, jour férié, promo...), le prix ci-dessous remplace le prix du jour de la semaine.", 'linstant-plaisir'); ?>
            </p>
            <table class="widefat lip-price-overrides-table" style="max-width: 420px;">
                <thead>
                    <tr>
                        <th><?php esc_html_e('Date', 'linstant-plaisir'); ?></th>
                        <th><?php esc_html_e('Prix (€)', 'linstant-plaisir'); ?></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($overrides as $date => $price): ?>
                        <tr>
                            <td><input type="date" name="lip_override_date[]" value="<?php echo esc_attr($date); ?>"></td>
                            <td><input type="number" step="0.01" min="0" name="lip_override_price[]" value="<?php echo esc_attr($price); ?>"></td>
                            <td><button type="button" class="button lip-remove-row">&times;</button></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p><button type="button" class="button" id="lip-add-price-row"><?php esc_html_e('+ Ajouter une date', 'linstant-plaisir'); ?></button></p>
        </div>

    </div>
    <script>
        jQuery(function ($) {
            $('#lip-add-price-row').on('click', function () {
                var row = '<tr>' +
                    '<td><input type="date" name="lip_override_date[]"></td>' +
                    '<td><input type="number" step="0.01" min="0" name="lip_override_price[]"></td>' +
                    '<td><button type="button" class="button lip-remove-row">&times;</button></td>' +
                    '</tr>';
                $('.lip-price-overrides-table tbody').append(row);
            });
            $('.lip-price-overrides-table').on('click', '.lip-remove-row', function () {
                $(this).closest('tr').remove();
            });
        });
    </script>
    <?php
});

add_action('woocommerce_process_product_meta', function ($post_id) {
    if ($post_id !== lip_get_room_product_id())
        return;

    // Tarifs par jour de la semaine
    $weekly = array();
    if (!empty($_POST['lip_weekly_price']) && is_array($_POST['lip_weekly_price'])) {
        $raw = wp_unslash($_POST['lip_weekly_price']);
        foreach ($raw as $day_num => $price) {
            $day_num = (int) $day_num;
            $price   = (float) $price;
            if ($day_num >= 1 && $day_num <= 7 && $price > 0) {
                $weekly[$day_num] = $price;
            }
        }
    }
    update_post_meta($post_id, '_lip_weekly_prices', $weekly);

    // Dates exceptionnelles
    $overrides = array();

    if (!empty($_POST['lip_override_date']) && is_array($_POST['lip_override_date'])) {
        $dates  = wp_unslash($_POST['lip_override_date']);
        $prices = isset($_POST['lip_override_price']) ? wp_unslash($_POST['lip_override_price']) : array();

        foreach ($dates as $i => $date) {
            $date  = sanitize_text_field($date);
            $price = isset($prices[$i]) ? (float) $prices[$i] : 0;
            if ($date && $price > 0) {
                $overrides[$date] = $price;
            }
        }
    }

    update_post_meta($post_id, '_lip_price_overrides', $overrides);
});


/* ==========================================================================
   SYNCHRO AIRBNB / BOOKING.COM (iCal)
   ========================================================================== */

// Lit un flux iCal brut et renvoie les plages de dates occupées (format Y-m-d)
function lip_parse_ical_busy_dates($ics_text)
{
    $busy = array();

    if (!preg_match_all('/BEGIN:VEVENT(.*?)END:VEVENT/s', $ics_text, $matches)) {
        return $busy;
    }

    foreach ($matches[1] as $event) {
        if (preg_match('/DTSTART[^:\r\n]*:(\d{8})/', $event, $m_start)
            && preg_match('/DTEND[^:\r\n]*:(\d{8})/', $event, $m_end)) {
            $start = DateTime::createFromFormat('Ymd', $m_start[1]);
            $end   = DateTime::createFromFormat('Ymd', $m_end[1]);
            if ($start && $end) {
                $busy[] = array('start' => $start->format('Y-m-d'), 'end' => $end->format('Y-m-d'));
            }
        }
    }

    return $busy;
}

// Va chercher les 2 flux (Airbnb, Booking.com) et met à jour la liste des dates occupées
function lip_sync_external_calendars()
{
    $sources = array(
        'airbnb'  => get_option('lip_ical_airbnb_url'),
        'booking' => get_option('lip_ical_booking_url'),
    );

    $all_busy = array();

    foreach ($sources as $source => $url) {
        if (empty($url))
            continue;

        $response = wp_remote_get($url, array('timeout' => 20));
        if (is_wp_error($response))
            continue;

        $body = wp_remote_retrieve_body($response);
        if (!$body)
            continue;

        foreach (lip_parse_ical_busy_dates($body) as $range) {
            $range['source'] = $source;
            $all_busy[]      = $range;
        }
    }

    update_option('lip_external_busy_dates', $all_busy, false);
    update_option('lip_last_sync_time', time(), false);
}
add_action('lip_sync_external_calendars_event', 'lip_sync_external_calendars');

// Programme la synchro automatique toutes les heures
add_action('init', function () {
    if (!wp_next_scheduled('lip_sync_external_calendars_event')) {
        wp_schedule_event(time(), 'hourly', 'lip_sync_external_calendars_event');
    }
});

// Génère le flux iCal du site, à coller dans Airbnb et Booking.com
function lip_generate_ical_export()
{
    $lines = array('BEGIN:VCALENDAR', 'VERSION:2.0', "PRODID:-//L'Instant Plaisir//Booking//FR");

    $orders = wc_get_orders(array('status' => array('processing', 'completed', 'on-hold'), 'limit' => -1));

    foreach ($orders as $order) {
        foreach ($order->get_items() as $item) {
            if ($item->get_product_id() !== lip_get_room_product_id())
                continue;

            $in  = $item->get_meta('_lip_check_in');
            $out = $item->get_meta('_lip_check_out');
            if (!$in || !$out)
                continue;

            $date_in  = DateTime::createFromFormat('Y/m/d', $in);
            $date_out = DateTime::createFromFormat('Y/m/d', $out);
            if (!$date_in || !$date_out)
                continue;

            $lines[] = 'BEGIN:VEVENT';
            $lines[] = 'UID:lip-' . $order->get_id() . '-' . $item->get_id() . '@linstantplaisir.fr';
            $lines[] = 'DTSTAMP:' . gmdate('Ymd\THis\Z');
            $lines[] = 'DTSTART;VALUE=DATE:' . $date_in->format('Ymd');
            $lines[] = 'DTEND;VALUE=DATE:' . $date_out->format('Ymd');
            $lines[] = "SUMMARY:Réservé - L'Instant Plaisir";
            $lines[] = 'END:VEVENT';
        }
    }

    $lines[] = 'END:VCALENDAR';

    return implode("\r\n", $lines);
}

// URL publique du flux export : https://linstantplaisir.fr/?lip_ical_export=1
add_action('template_redirect', function () {
    if (!isset($_GET['lip_ical_export']))
        return;

    header('Content-Type: text/calendar; charset=utf-8');
    header('Content-Disposition: inline; filename="loveroom.ics"');
    echo lip_generate_ical_export();
    exit;
});

// Page de réglages wp-admin : coller les 2 liens iCal + voir le lien d'export
add_action('admin_menu', function () {
    add_menu_page(
        'Synchro Réservations',
        'Synchro Réservations',
        'manage_woocommerce',
        'lip-sync-settings',
        'lip_render_sync_settings_page',
        'dashicons-update',
        56
    );
});

function lip_render_sync_settings_page()
{
    if (isset($_POST['lip_sync_nonce']) && wp_verify_nonce($_POST['lip_sync_nonce'], 'lip_save_sync_settings')) {
        update_option('lip_ical_airbnb_url', esc_url_raw(trim(wp_unslash($_POST['lip_ical_airbnb_url']))));
        update_option('lip_ical_booking_url', esc_url_raw(trim(wp_unslash($_POST['lip_ical_booking_url']))));
        echo '<div class="notice notice-success"><p>Liens enregistrés.</p></div>';
    }

    if (isset($_POST['lip_sync_now']) && check_admin_referer('lip_sync_now_action')) {
        lip_sync_external_calendars();
        echo '<div class="notice notice-success"><p>Synchronisation effectuée.</p></div>';
    }

    $airbnb_url  = get_option('lip_ical_airbnb_url', '');
    $booking_url = get_option('lip_ical_booking_url', '');
    $export_url  = add_query_arg('lip_ical_export', '1', home_url('/'));
    $external    = get_option('lip_external_busy_dates', array());
    $last_sync   = get_option('lip_last_sync_time', 0);
    ?>
    <div class="wrap">
        <h1>Synchro Réservations — Love Room</h1>

        <h2>1. Importer les réservations d'Airbnb et Booking.com</h2>
        <form method="post">
            <?php wp_nonce_field('lip_save_sync_settings', 'lip_sync_nonce'); ?>
            <table class="form-table">
                <tr>
                    <th><label for="lip_ical_airbnb_url">Lien iCal Airbnb</label></th>
                    <td><input type="url" style="width:500px" id="lip_ical_airbnb_url" name="lip_ical_airbnb_url"
                            value="<?php echo esc_attr($airbnb_url); ?>"
                            placeholder="https://www.airbnb.fr/calendar/ical/....ics"></td>
                </tr>
                <tr>
                    <th><label for="lip_ical_booking_url">Lien iCal Booking.com</label></th>
                    <td><input type="url" style="width:500px" id="lip_ical_booking_url" name="lip_ical_booking_url"
                            value="<?php echo esc_attr($booking_url); ?>"
                            placeholder="https://admin.booking.com/....ics"></td>
                </tr>
            </table>
            <?php submit_button('Enregistrer les liens'); ?>
        </form>

        <form method="post">
            <?php wp_nonce_field('lip_sync_now_action'); ?>
            <input type="hidden" name="lip_sync_now" value="1">
            <?php submit_button('Synchroniser maintenant', 'secondary'); ?>
        </form>

        <p>
            <?php if ($last_sync): ?>
                <em>Dernière synchro : <?php echo esc_html(wp_date('d/m/Y H:i', $last_sync)); ?> — automatique toutes les heures ensuite.</em>
            <?php else: ?>
                <em>Pas encore synchronisé.</em>
            <?php endif; ?>
        </p>

        <hr>

        <h2>2. Exporter les réservations du site vers Airbnb et Booking.com</h2>
        <p>Colle ce lien dans les réglages "Synchroniser des calendriers" d'Airbnb et de Booking.com :</p>
        <p><input type="text" readonly style="width:500px" value="<?php echo esc_url($export_url); ?>" onclick="this.select()"></p>

        <hr>

        <h2>3. Dates actuellement bloquées via Airbnb / Booking.com</h2>
        <?php if (empty($external)): ?>
            <p>Aucune donnée pour l'instant — renseigne les liens ci-dessus puis clique sur "Synchroniser maintenant".</p>
        <?php else: ?>
            <ul>
                <?php foreach ($external as $range): ?>
                    <li>
                        <?php echo esc_html(ucfirst($range['source'])); ?> :
                        du <?php echo esc_html($range['start']); ?> au <?php echo esc_html($range['end']); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
    <?php
}


/* ==========================================================================
   SUGGESTIONS BOUTIQUE
   ========================================================================== */

function linstant_plaisir_shop_suggestions_html($limit = 6, $exclude = array())
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
            <h2 class="shop-suggestions-title">Envie d'un séjour encore plus unique</h2>
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

add_action('wp_footer', function () {
    global $post;
    if (!is_product() || !$post || (int) $post->ID !== lip_get_room_product_id())
        return;
    linstant_plaisir_shop_suggestions_html(6);
}, 5);

add_action('woocommerce_after_cart', function () {
    linstant_plaisir_shop_suggestions_html(6);
});


/* ==========================================================================
   CONTACT
   ========================================================================== */

add_action('template_redirect', function () {
    if (!is_page_template('page-contact.php'))
        return;
    // Le traitement réel se fait dans page-contact.php lui-même
});