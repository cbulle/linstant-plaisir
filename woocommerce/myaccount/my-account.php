<?php
/**
 * My Account — L'Instant Plaisir
 * Thème Luxe & Secret
 * Remplace woocommerce/myaccount/my-account.php
 */
defined( 'ABSPATH' ) || exit;

$current_user = wp_get_current_user();
$first_name   = $current_user->first_name ?: $current_user->display_name;
?>

<div class="account-luxe-wrapper">

    <!-- ============================================================
         EN-TÊTE COMPTE
    ============================================================ -->
    <div class="account-header">
        <div class="account-header-inner">
            <div class="account-avatar-wrap">
                <div class="account-avatar-ring"></div>
                <div class="account-avatar">
                    <?php echo substr( $first_name, 0, 1 ); ?>
                </div>
            </div>
            <div class="account-header-text">
                <span class="account-greeting">Bienvenue,</span>
                <h1 class="account-name"><?php echo esc_html( $first_name ); ?></h1>
                <span class="account-member-since">
                    Membre depuis <?php echo date_i18n( 'F Y', strtotime( $current_user->user_registered ) ); ?>
                </span>
            </div>
        </div>
        <div class="account-header-line"></div>
    </div>

    <div class="account-layout">

        <!-- ============================================================
             NAVIGATION LATÉRALE
        ============================================================ -->
        <nav class="account-nav">
            <?php
            // On remplace le nav WooCommerce par défaut par un nav stylisé
            $nav_items = wc_get_account_menu_items();
            $current   = WC()->query->get_current_endpoint();

            // Icônes pour chaque endpoint
            $icons = [
                'dashboard'       => '◈',
                'orders'          => '◇',
                'downloads'       => '↓',
                'edit-address'    => '⊡',
                'payment-methods' => '◻',
                'edit-account'    => '○',
                'customer-logout' => '→',
            ];

            foreach ( $nav_items as $endpoint => $label ) :
                $url     = wc_get_account_endpoint_url( $endpoint );
                $is_active = ( $current === $endpoint || ( empty($current) && $endpoint === 'dashboard' ) );
                $icon    = $icons[$endpoint] ?? '·';
            ?>
                <a href="<?php echo esc_url( $url ); ?>"
                   class="account-nav-item <?php echo $is_active ? 'is-active' : ''; ?>"
                   <?php echo $endpoint === 'customer-logout' ? 'data-confirm="Vous déconnecter ?"' : ''; ?>>
                    <span class="account-nav-icon"><?php echo $icon; ?></span>
                    <span class="account-nav-label"><?php echo esc_html( $label ); ?></span>
                    <?php if ( $is_active ) : ?>
                        <span class="account-nav-active-bar"></span>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <!-- ============================================================
             CONTENU PRINCIPAL
        ============================================================ -->
        <div class="account-content">
            <?php
            /**
             * WooCommerce injecte ici le contenu de l'endpoint actif
             * (dashboard, orders, edit-account, etc.)
             */
            do_action( 'woocommerce_account_content' );
            ?>
        </div>

    </div><!-- .account-layout -->

</div><!-- .account-luxe-wrapper -->


<style>
/* ================================================================
   PAGE COMPTE — styles inline (ou à déplacer dans woocommerce.css)
================================================================ */

@keyframes acct-reveal {
    from { opacity: 0; transform: translateY(20px); }
    to   { opacity: 1; transform: translateY(0); }
}
@keyframes ring-spin {
    from { transform: rotate(0deg); }
    to   { transform: rotate(360deg); }
}
@keyframes shimmer-gold {
    0%   { background-position: -200% center; }
    100% { background-position: 200% center; }
}

/* Wrapper */
.account-luxe-wrapper {
    max-width: 1100px;
    margin: 50px auto 100px;
    padding: 0 20px;
    animation: acct-reveal 0.7s ease both;
}

/* ---- En-tête ---- */
.account-header {
    margin-bottom: 50px;
}

.account-header-inner {
    display: flex;
    align-items: center;
    gap: 30px;
    margin-bottom: 30px;
}

/* Avatar */
.account-avatar-wrap {
    position: relative;
    flex-shrink: 0;
    width: 70px;
    height: 70px;
}

.account-avatar-ring {
    position: absolute;
    inset: -4px;
    border-radius: 50%;
    border: 1px dashed rgba(197,160,89,0.4);
    animation: ring-spin 20s linear infinite;
}

.account-avatar {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--color-primary, #7a0a60), var(--color-secondary, #4a0038));
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Playfair Display', serif;
    font-size: 28px;
    color: rgba(255,255,255,0.9);
    text-transform: uppercase;
    box-shadow: 0 0 30px rgba(122,10,96,0.3);
}

/* Texte header */
.account-greeting {
    display: block;
    font-family: 'Cinzel', serif;
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: 4px;
    color: #555;
    margin-bottom: 5px;
}

.account-name {
    font-family: 'Playfair Display', serif;
    font-size: 36px;
    color: #fff;
    margin: 0 0 6px;
    font-weight: 400;
    line-height: 1.1;
}

.account-member-since {
    font-family: 'Cinzel', serif;
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: 2px;
    color: var(--color-gold, #c5a059);
    opacity: 0.6;
}

/* Ligne décorative */
.account-header-line {
    height: 1px;
    background: linear-gradient(90deg, var(--color-gold), var(--color-primary, #7a0a60), transparent);
    background-size: 200% auto;
    animation: shimmer-gold 5s linear infinite;
}

/* ---- Layout ---- */
.account-layout {
    display: grid;
    grid-template-columns: 220px 1fr;
    gap: 40px;
    align-items: start;
}

/* ---- Navigation ---- */
.account-nav {
    background: var(--color-bg2, #111114);
    border: 1px solid rgba(197,160,89,0.08);
    border-radius: var(--radius, 8px);
    overflow: hidden;
    position: sticky;
    top: 100px;
}

.account-nav-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px 20px;
    text-decoration: none;
    color: #666;
    font-family: 'Cinzel', serif;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    border-bottom: 1px solid rgba(255,255,255,0.03);
    position: relative;
    transition: all 0.3s ease;
    overflow: hidden;
}

/* Dernier item (logout) : sans bordure */
.account-nav-item:last-child {
    border-bottom: none;
    color: #444;
    margin-top: 5px;
    border-top: 1px solid rgba(255,255,255,0.04);
}

.account-nav-item:last-child:hover {
    color: var(--color-primary, #7a0a60) !important;
}

.account-nav-item::before {
    content: '';
    position: absolute;
    inset: 0;
    background: rgba(197,160,89,0.04);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.account-nav-item:hover { color: #bbb; }
.account-nav-item:hover::before { opacity: 1; }

.account-nav-item.is-active {
    color: var(--color-gold, #c5a059) !important;
    background: rgba(197,160,89,0.05);
}

.account-nav-item.is-active::before { opacity: 1; }

.account-nav-icon {
    font-size: 14px;
    width: 18px;
    text-align: center;
    flex-shrink: 0;
    opacity: 0.6;
    transition: opacity 0.3s ease;
}

.account-nav-item.is-active .account-nav-icon,
.account-nav-item:hover .account-nav-icon { opacity: 1; }

.account-nav-label { flex: 1; }

/* Barre active verticale */
.account-nav-active-bar {
    position: absolute;
    right: 0; top: 10%; bottom: 10%;
    width: 2px;
    background: var(--color-gold, #c5a059);
    border-radius: 2px 0 0 2px;
}

/* ---- Contenu ---- */
.account-content {
    background: var(--color-bg2, #111114);
    border: 1px solid rgba(197,160,89,0.08);
    border-radius: var(--radius, 8px);
    padding: 40px;
    min-height: 400px;
}

/* Dashboard WooCommerce */
.account-content .woocommerce-MyAccount-content p {
    color: var(--text-soft, #888);
    font-size: 15px;
    line-height: 1.8;
}

.account-content .woocommerce-MyAccount-content a {
    color: var(--color-gold, #c5a059);
    text-decoration: none;
    transition: opacity 0.2s ease;
}

.account-content .woocommerce-MyAccount-content a:hover { opacity: 0.75; }

/* Titres dans le contenu */
.account-content h2,
.account-content h3 {
    font-family: 'Playfair Display', serif;
    color: #fff;
    margin-bottom: 20px;
    font-weight: 400;
}

.account-content h2 { font-size: 28px; }
.account-content h3 { font-size: 22px; }

/* Tables des commandes */
.account-content .woocommerce-orders-table,
.account-content .shop_table {
    width: 100%;
    border-collapse: collapse;
}

.account-content .woocommerce-orders-table th,
.account-content .shop_table th {
    font-family: 'Cinzel', serif;
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: 2px;
    color: #444;
    padding: 0 15px 15px 0;
    border-bottom: 1px solid rgba(255,255,255,0.05);
    font-weight: 400;
    text-align: left;
}

.account-content .woocommerce-orders-table td,
.account-content .shop_table td {
    padding: 15px 15px 15px 0;
    border-bottom: 1px solid rgba(255,255,255,0.03);
    color: var(--text-soft, #888);
    font-size: 14px;
    vertical-align: middle;
}

.account-content .woocommerce-orders-table tr:hover td {
    background: rgba(197,160,89,0.02);
    color: #bbb;
}

/* Statuts commandes */
.account-content .woocommerce-orders-table .woocommerce-orders-table__cell-order-status mark,
.account-content mark {
    background: transparent;
    font-family: 'Cinzel', serif;
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    padding: 4px 10px;
    border-radius: 20px;
}

.account-content mark.processing,
.account-content mark.completed {
    background: rgba(197,160,89,0.1) !important;
    color: var(--color-gold, #c5a059) !important;
}

.account-content mark.pending,
.account-content mark.on-hold {
    background: rgba(122,10,96,0.15) !important;
    color: #ffb3e6 !important;
}

/* Boutons WooCommerce dans le compte */
.account-content .button,
.account-content .woocommerce-button {
    background: transparent !important;
    border: 1px solid rgba(197,160,89,0.3) !important;
    color: var(--color-gold, #c5a059) !important;
    font-family: 'Cinzel', serif !important;
    font-size: 10px !important;
    text-transform: uppercase !important;
    letter-spacing: 2px !important;
    padding: 9px 18px !important;
    cursor: pointer;
    transition: all 0.3s ease !important;
    border-radius: var(--radius, 8px) !important;
    text-decoration: none;
    display: inline-block;
}

.account-content .button:hover,
.account-content .woocommerce-button:hover {
    background: var(--color-gold, #c5a059) !important;
    color: #000 !important;
    border-color: var(--color-gold) !important;
}

/* Formulaires dans le compte */
.account-content .woocommerce-form-row label,
.account-content .form-row label {
    font-family: 'Cinzel', serif !important;
    font-size: 10px !important;
    text-transform: uppercase;
    letter-spacing: 2px;
    color: var(--color-gold, #c5a059) !important;
    margin-bottom: 8px;
    display: block;
}

.account-content .woocommerce-form-row input,
.account-content .form-row input,
.account-content .form-row select {
    background: rgba(255,255,255,0.03) !important;
    border: 1px solid rgba(197,160,89,0.15) !important;
    color: #fff !important;
    padding: 13px 15px !important;
    width: 100%;
    border-radius: var(--radius, 8px) !important;
    font-size: 14px;
    transition: border-color 0.3s ease;
}

.account-content .woocommerce-form-row input:focus,
.account-content .form-row input:focus {
    border-color: var(--color-gold, #c5a059) !important;
    outline: none;
    box-shadow: 0 0 15px rgba(197,160,89,0.08);
}

/* Notices dans le compte */
.account-content .woocommerce-Message,
.account-content .woocommerce-message {
    background: rgba(197,160,89,0.05) !important;
    border: 1px solid rgba(197,160,89,0.2) !important;
    border-left: 3px solid var(--color-gold) !important;
    color: #bbb !important;
    padding: 15px 20px !important;
    border-radius: var(--radius, 8px);
    font-size: 13px;
    margin-bottom: 20px;
}

.account-content .woocommerce-error {
    background: rgba(122,10,96,0.1) !important;
    border-left-color: var(--color-primary, #7a0a60) !important;
    color: #ffb3e6 !important;
}

/* Responsive */
@media (max-width: 900px) {
    .account-layout {
        grid-template-columns: 1fr;
        gap: 25px;
    }

    .account-nav {
        position: static;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
    }

    .account-nav-item {
        border-bottom: none;
        border-right: 1px solid rgba(255,255,255,0.03);
        padding: 12px 15px;
        flex-direction: column;
        gap: 6px;
        text-align: center;
        font-size: 9px;
    }

    .account-nav-active-bar {
        top: auto; left: 10%; right: 10%; bottom: 0;
        width: auto; height: 2px;
        border-radius: 2px 2px 0 0;
    }

    .account-content { padding: 25px; }
    .account-name { font-size: 28px; }
}

@media (max-width: 480px) {
    .account-header-inner { flex-direction: column; text-align: center; }
    .account-nav { grid-template-columns: repeat(3, 1fr); }
}
</style>
