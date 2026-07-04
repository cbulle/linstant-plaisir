<?php
/**
 * Order Received / Thank You — L'Instant Plaisir
 * Thème Luxe & Secret
 */
defined( 'ABSPATH' ) || exit;
?>

<div class="thankyou-luxe-wrapper">

    <!-- Particules dorées animées -->
    <div class="ty-particles" aria-hidden="true">
        <?php for ($i = 1; $i <= 12; $i++) : ?>
            <span class="ty-particle ty-particle--<?php echo $i; ?>"></span>
        <?php endfor; ?>
    </div>

    <?php if ( $order ) : ?>

        <!-- ============================================================
             EN-TÊTE — Confirmation
        ============================================================ -->
        <div class="ty-header">
            <div class="ty-seal">
                <svg viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="40" cy="40" r="38" stroke="var(--color-gold)" stroke-width="1" stroke-dasharray="4 3" opacity="0.4"/>
                    <circle cx="40" cy="40" r="30" stroke="var(--color-gold)" stroke-width="0.5" opacity="0.6"/>
                    <path d="M26 40 L35 50 L54 30" stroke="var(--color-gold)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>

            <span class="ty-eyebrow">Réservation confirmée</span>
            <h1 class="ty-title">Votre parenthèse<br><span class="ty-title-gold">vous attend</span></h1>

            <p class="ty-subtitle">
                Un secret partagé entre vous et nous.<br>
                Nous avons bien reçu votre réservation.
            </p>

            <div class="ty-order-ref">
                Référence · <strong>#<?php echo $order->get_order_number(); ?></strong>
            </div>
        </div>

        <!-- ============================================================
             DÉTAILS DE LA COMMANDE
        ============================================================ -->
        <div class="ty-grid">

            <!-- Récapitulatif -->
            <div class="ty-card ty-card--summary">
                <div class="ty-card-label">Votre séjour</div>

                <table class="ty-table">
                    <thead>
                        <tr>
                            <th>Prestation</th>
                            <th class="text-right">Montant</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $order->get_items() as $item_id => $item ) : ?>
                            <tr>
                                <td>
                                    <span class="ty-product-name"><?php echo esc_html( $item->get_name() ); ?></span>
                                    <?php if ( $item->get_quantity() > 1 ) : ?>
                                        <span class="ty-qty"> × <?php echo $item->get_quantity(); ?></span>
                                    <?php endif; ?>
                                    <?php
                                    // Données meta (dates HB, options WooCommerce)
                                    $item_data = wc_get_formatted_cart_item_data( $item, true );
                                    if ( $item_data ) echo '<div class="ty-item-meta">' . wp_kses_post( $item_data ) . '</div>';
                                    ?>
                                </td>
                                <td class="text-right ty-price">
                                    <?php echo wp_kses_post( $order->get_formatted_line_subtotal( $item ) ); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <?php if ( $order->get_total_discount() > 0 ) : ?>
                        <tr class="ty-row-discount">
                            <td>Réduction</td>
                            <td class="text-right">−<?php echo wc_price( $order->get_total_discount() ); ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr class="ty-row-total">
                            <td>Total réglé</td>
                            <td class="text-right ty-total-amount"><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Infos pratiques -->
            <div class="ty-sidebar">

                <!-- Dates & accès -->
                <div class="ty-card ty-card--access">
                    <div class="ty-card-label">Votre accès</div>
                    <ul class="ty-access-list">
                        <li>
                            <span class="ty-access-icon">◈</span>
                            <span>
                                <strong>Arrivée autonome</strong><br>
                                Code d'accès transmis par e-mail
                            </span>
                        </li>
                        <li>
                            <span class="ty-access-icon">◈</span>
                            <span>
                                <strong>Aucune réception</strong><br>
                                Discrétion absolue garantie
                            </span>
                        </li>
                        <li>
                            <span class="ty-access-icon">◈</span>
                            <span>
                                <strong>Check-in</strong><br>
                                À partir de 15h00
                            </span>
                        </li>
                        <li>
                            <span class="ty-access-icon">◈</span>
                            <span>
                                <strong>Check-out</strong><br>
                                Avant 11h00
                            </span>
                        </li>
                    </ul>
                </div>

                <!-- Adresse de facturation -->
                <?php if ( $order->get_billing_email() ) : ?>
                <div class="ty-card ty-card--billing">
                    <div class="ty-card-label">Confirmation envoyée à</div>
                    <p class="ty-email"><?php echo esc_html( $order->get_billing_email() ); ?></p>
                    <p class="ty-billing-note">Vérifiez vos spams si vous ne la recevez pas sous 10 minutes.</p>
                </div>
                <?php endif; ?>

                <!-- Moyen de paiement -->
                <div class="ty-card ty-card--payment">
                    <div class="ty-card-label">Paiement</div>
                    <div class="ty-payment-row">
                        <span class="ty-payment-method"><?php echo esc_html( $order->get_payment_method_title() ); ?></span>
                        <span class="ty-payment-status ty-status-<?php echo esc_attr( $order->get_status() ); ?>">
                            <?php
                            $statuses = [
                                'pending'    => 'En attente',
                                'processing' => 'Confirmé',
                                'completed'  => 'Confirmé',
                                'on-hold'    => 'En vérification',
                            ];
                            echo $statuses[ $order->get_status() ] ?? ucfirst( $order->get_status() );
                            ?>
                        </span>
                    </div>
                </div>

            </div>
        </div>

        <!-- CTA Retour -->
        <div class="ty-footer-actions">
            <a href="<?php echo esc_url( home_url('/') ); ?>" class="ty-btn-home">
                Retour à l'accueil
            </a>
            <a href="<?php echo esc_url( wc_get_account_endpoint_url('orders') ); ?>" class="ty-btn-orders">
                Mes réservations
            </a>
        </div>

    <?php else : ?>

        <!-- Pas de commande trouvée -->
        <div class="ty-header">
            <div class="ty-seal ty-seal--empty">
                <svg viewBox="0 0 80 80" fill="none">
                    <circle cx="40" cy="40" r="38" stroke="var(--color-primary)" stroke-width="1" opacity="0.4"/>
                    <line x1="28" y1="28" x2="52" y2="52" stroke="var(--color-primary)" stroke-width="1.5" stroke-linecap="round"/>
                    <line x1="52" y1="28" x2="28" y2="52" stroke="var(--color-primary)" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
            </div>
            <span class="ty-eyebrow">Confirmation</span>
            <h1 class="ty-title">Merci pour<br><span class="ty-title-gold">votre commande</span></h1>
            <p class="ty-subtitle">Un e-mail de confirmation vous a été envoyé.</p>
            <div class="ty-footer-actions">
                <a href="<?php echo esc_url( home_url('/') ); ?>" class="ty-btn-home">Retour à l'accueil</a>
            </div>
        </div>

    <?php endif; ?>

</div><!-- .thankyou-luxe-wrapper -->


<style>
/* ================================================================
   PAGE REMERCIEMENT — styles inline (ou à déplacer dans woocommerce.css)
================================================================ */

@keyframes ty-seal-spin {
    from { transform: rotate(0deg); }
    to   { transform: rotate(360deg); }
}
@keyframes ty-reveal {
    from { opacity: 0; transform: translateY(30px); }
    to   { opacity: 1; transform: translateY(0); }
}
@keyframes ty-particle-float {
    0%        { transform: translateY(0) scale(1);   opacity: 0; }
    10%       { opacity: 1; }
    90%       { opacity: 0.6; }
    100%      { transform: translateY(-120vh) scale(0.4); opacity: 0; }
}
@keyframes shimmer-gold {
    0%   { background-position: -200% center; }
    100% { background-position: 200% center; }
}

/* Wrapper global */
.thankyou-luxe-wrapper {
    max-width: 1000px;
    margin: 60px auto 100px;
    padding: 0 20px;
    position: relative;
    color: var(--text, #e8e8e8);
}

/* Particules flottantes */
.ty-particles {
    position: fixed;
    inset: 0;
    pointer-events: none;
    z-index: 0;
    overflow: hidden;
}

.ty-particle {
    position: absolute;
    bottom: -20px;
    width: 4px;
    height: 4px;
    border-radius: 50%;
    background: var(--color-gold, #c5a059);
    opacity: 0;
    animation: ty-particle-float linear infinite;
}

/* Positions et timings variés */
.ty-particle--1  { left: 8%;   animation-duration: 12s; animation-delay: 0s;    width: 3px;  height: 3px; }
.ty-particle--2  { left: 18%;  animation-duration: 18s; animation-delay: 2s;    width: 5px;  height: 5px; background: var(--color-primary); }
.ty-particle--3  { left: 28%;  animation-duration: 14s; animation-delay: 4s;    }
.ty-particle--4  { left: 38%;  animation-duration: 20s; animation-delay: 1s;    width: 2px;  height: 2px; }
.ty-particle--5  { left: 48%;  animation-duration: 16s; animation-delay: 6s;    width: 6px;  height: 6px; }
.ty-particle--6  { left: 58%;  animation-duration: 11s; animation-delay: 3s;    background: var(--color-primary); width: 3px; height: 3px; }
.ty-particle--7  { left: 68%;  animation-duration: 22s; animation-delay: 5s;    width: 2px;  height: 2px; }
.ty-particle--8  { left: 75%;  animation-duration: 15s; animation-delay: 0.5s;  }
.ty-particle--9  { left: 83%;  animation-duration: 19s; animation-delay: 7s;    width: 4px;  height: 4px; background: var(--color-primary); }
.ty-particle--10 { left: 90%;  animation-duration: 13s; animation-delay: 2.5s;  width: 3px;  height: 3px; }
.ty-particle--11 { left: 12%;  animation-duration: 17s; animation-delay: 8s;    width: 5px;  height: 5px; }
.ty-particle--12 { left: 55%;  animation-duration: 21s; animation-delay: 1.5s;  width: 2px;  height: 2px; background: var(--color-primary); }

/* En-tête */
.ty-header {
    text-align: center;
    padding: 60px 20px 50px;
    position: relative;
    z-index: 1;
    animation: ty-reveal 0.9s ease both;
}

/* Sceau SVG animé */
.ty-seal {
    width: 80px;
    height: 80px;
    margin: 0 auto 30px;
    position: relative;
}

.ty-seal circle:first-child {
    transform-origin: 40px 40px;
    animation: ty-seal-spin 30s linear infinite;
}

.ty-eyebrow {
    display: block;
    font-family: 'Cinzel', serif;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 5px;
    color: var(--color-gold);
    opacity: 0.8;
    margin-bottom: 20px;
    animation: ty-reveal 0.8s ease 0.2s both;
}

.ty-title {
    font-family: 'Playfair Display', serif;
    font-size: clamp(36px, 6vw, 58px);
    font-weight: 400;
    line-height: 1.2;
    color: #fff;
    margin: 0 0 20px;
    animation: ty-reveal 0.8s ease 0.35s both;
}

.ty-title-gold {
    color: var(--color-gold);
    font-style: italic;
}

.ty-subtitle {
    color: var(--text-soft, #888);
    font-size: 16px;
    line-height: 1.8;
    margin-bottom: 25px;
    animation: ty-reveal 0.8s ease 0.5s both;
}

.ty-order-ref {
    display: inline-block;
    font-family: 'Cinzel', serif;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 3px;
    color: #555;
    border: 1px solid rgba(197,160,89,0.15);
    padding: 10px 25px;
    border-radius: 30px;
    animation: ty-reveal 0.8s ease 0.65s both;
}

.ty-order-ref strong {
    color: var(--color-gold);
}

/* Grille principale */
.ty-grid {
    display: grid;
    grid-template-columns: 1.3fr 0.7fr;
    gap: 30px;
    margin-top: 50px;
    position: relative;
    z-index: 1;
    animation: ty-reveal 0.9s ease 0.5s both;
}

/* Cartes */
.ty-card {
    background: var(--color-bg2, #111114);
    border: 1px solid rgba(197,160,89,0.1);
    border-radius: var(--radius, 8px);
    padding: 30px;
    position: relative;
    overflow: hidden;
    transition: border-color 0.4s ease;
}

.ty-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, var(--color-gold), transparent);
    opacity: 0;
    transition: opacity 0.4s ease;
}

.ty-card:hover { border-color: rgba(197,160,89,0.3); }
.ty-card:hover::before { opacity: 1; }

.ty-card-label {
    font-family: 'Cinzel', serif;
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: 3px;
    color: var(--color-gold);
    opacity: 0.7;
    margin-bottom: 20px;
}

/* Sidebar */
.ty-sidebar {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

/* Table récapitulatif */
.ty-table {
    width: 100%;
    border-collapse: collapse;
}

.ty-table th {
    font-family: 'Cinzel', serif;
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: 2px;
    color: #444;
    padding: 0 0 15px;
    border-bottom: 1px solid rgba(255,255,255,0.05);
    font-weight: 400;
}

.ty-table td {
    padding: 14px 0;
    border-bottom: 1px solid rgba(255,255,255,0.04);
    color: var(--text-soft, #888);
    font-size: 14px;
    vertical-align: top;
}

.ty-product-name {
    color: #ddd;
    font-size: 15px;
}

.ty-qty { color: #555; font-size: 13px; }

.ty-item-meta {
    font-size: 12px;
    color: #555;
    margin-top: 5px;
    font-style: italic;
}

.ty-price { color: #bbb; font-size: 15px; }
.text-right { text-align: right; }

.ty-row-discount td { color: #888; font-size: 13px; }

.ty-row-total td {
    padding-top: 18px;
    border-bottom: none;
    border-top: 1px solid rgba(197,160,89,0.2);
    color: #fff;
    font-size: 15px;
    font-weight: 500;
}

.ty-total-amount {
    color: var(--color-gold) !important;
    font-family: 'Playfair Display', serif;
    font-size: 24px !important;
}

/* Liste accès */
.ty-access-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 18px;
}

.ty-access-list li {
    display: flex;
    gap: 14px;
    align-items: flex-start;
    font-size: 13px;
    color: var(--text-soft, #888);
    line-height: 1.6;
}

.ty-access-icon {
    color: var(--color-gold);
    font-size: 14px;
    flex-shrink: 0;
    margin-top: 1px;
    opacity: 0.7;
}

.ty-access-list strong {
    color: #ddd;
    display: block;
    font-weight: 500;
    margin-bottom: 2px;
}

/* Email */
.ty-email {
    font-family: 'Cinzel', serif;
    font-size: 13px;
    color: var(--color-gold);
    letter-spacing: 1px;
    margin: 0 0 8px;
}

.ty-billing-note {
    font-size: 11px;
    color: #444;
    margin: 0;
}

/* Paiement */
.ty-payment-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.ty-payment-method {
    color: #bbb;
    font-size: 14px;
}

.ty-payment-status {
    font-family: 'Cinzel', serif;
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: 2px;
    padding: 5px 12px;
    border-radius: 20px;
}

.ty-status-processing,
.ty-status-completed {
    background: rgba(197,160,89,0.1);
    color: var(--color-gold);
    border: 1px solid rgba(197,160,89,0.2);
}

.ty-status-pending,
.ty-status-on-hold {
    background: rgba(122,10,96,0.15);
    color: #ffb3e6;
    border: 1px solid rgba(122,10,96,0.3);
}

/* CTA footer */
.ty-footer-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
    margin-top: 50px;
    position: relative;
    z-index: 1;
    animation: ty-reveal 0.8s ease 0.8s both;
}

.ty-btn-home,
.ty-btn-orders {
    font-family: 'Cinzel', serif;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 3px;
    padding: 16px 35px;
    text-decoration: none;
    border-radius: var(--radius, 8px);
    transition: all 0.4s ease;
    border: 1px solid transparent;
}

.ty-btn-home {
    background: rgba(255,255,255,0.03);
    border-color: rgba(255,255,255,0.08);
    color: #888;
}

.ty-btn-home:hover {
    border-color: rgba(197,160,89,0.4);
    color: var(--color-gold);
}

.ty-btn-orders {
    background: linear-gradient(135deg, var(--color-primary, #7a0a60), var(--color-secondary, #4a0038));
    color: #fff;
    box-shadow: 0 5px 20px rgba(122,10,96,0.4);
}

.ty-btn-orders:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 35px rgba(122,10,96,0.6);
    color: #fff;
}

/* Responsive */
@media (max-width: 768px) {
    .ty-grid { grid-template-columns: 1fr; }
    .ty-footer-actions { flex-direction: column; align-items: center; }
    .ty-btn-home, .ty-btn-orders { width: 100%; text-align: center; }
}
</style>
