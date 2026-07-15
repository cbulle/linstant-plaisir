<?php
/* Template Name: Le Concept */
get_header();
?>

<section class="page-hero-luxe">
    <div class="hero-overlay"></div>
    <div class="page-hero-content">
        <span class="hero-subtitle">L'Expérience</span>
        <h1>Le <span class="gold-title">Concept</span></h1>
    </div>
</section>

<section class="concept-section">
    <div class="container concept-grid">

        <div class="concept-text">
            <h2 class="title-with-line">Pourquoi venir dans une love room</h2>

            <p>Découvrez l'univers d'une love room, un espace intime et raffiné où la sensualité se conjugue avec l'art de vivre. Une love room est conçue pour stimuler vos sens et vous offrir un cadre unique propice à l'évasion, à la détente et à l'exploration de vos désirs, qu'ils soient traditionnels ou plus audacieux.</p>

            <p>Pourquoi venir dans une love room ? Parce qu'elle vous permet de vous évader du quotidien, de redécouvrir votre corps et votre intimité dans un environnement pensé pour le plaisir et l'expérimentation. Que vous soyez amateur de jeux érotiques ou simplement en quête d'un moment de détente hors du commun, une love room vous invite à vivre une expérience personnalisée et discrète, dans le respect de vos envies et de vos limites.</p>

            <p>Pourquoi choisir « L'Instant Plaisir » ? Nous avons imaginé une love room qui marie subtilement l'univers soft du BDSM à un confort moderne et accessible à tous. Ici, vous trouverez des équipements raffinés tels qu'une croix de Saint André, une baignoire balnéo, une balançoire coquine, ainsi que des fouets et menottes pour les amateurs de sensations plus affirmées. Notre espace de vie, aux allures d'un Airbnb classique, garantit à ceux qui préfèrent une ambiance plus sobre un lieu de détente et de convivialité.</p>

            <h3 class="concept-subtitle">Inclus dans votre réservation</h3>

            <ul class="concept-included-list">
                <li><i class="fas fa-check"></i> Parking privé facile d'accès</li>
                <li><i class="fas fa-check"></i> Arrivée autonome dès 17h30</li>
                <li><i class="fas fa-check"></i> Thé et café offerts</li>
                <li><i class="fas fa-check"></i> Départ jusqu'à 11h</li>
                <li><i class="fas fa-check"></i> Une surprise sucrée à votre arrivée</li>
                <li><i class="fas fa-check"></i> Cuisine à votre disposition</li>
                <li><i class="fas fa-check"></i> Lit Queen Size</li>
                <li><i class="fas fa-check"></i> Baignoire balnéo</li>
                <li><i class="fas fa-check"></i> Enceinte Bluetooth</li>
                <li><i class="fas fa-check"></i> Accès Spotify &amp; Netflix</li>
            </ul>

            <a href="<?php echo esc_url( home_url('/room/love-room/') ); ?>" class="btn-gold-full">Réserver</a>
        </div>

    </div>
</section>

<?php
// Suggestions produits — ⚠️ AJUSTE les slugs de catégorie ci-dessous si besoin
// (Produits → Catégories dans ton admin WooCommerce)
if ( function_exists('wc_get_products') ) :

    $love_box = wc_get_products( array(
        'status'   => 'publish',
        'limit'    => 4,
        'category' => array( 'love-box' ), // ⚠️ slug à vérifier
    ) );

    if ( ! empty( $love_box ) ) : ?>
        <section class="concept-shop-section woocommerce">
            <div class="container">
                <h2 class="title-with-line concept-shop-title">Nos Love Box</h2>
                <p class="concept-shop-subtitle">Conçues pour agrémenter vos jeux en amoureux</p>
                <ul class="products columns-4">
                    <?php foreach ( $love_box as $wc_product ) : ?>
                        <li <?php wc_product_class( '', $wc_product ); ?>>
                            <a href="<?php echo esc_url( get_permalink( $wc_product->get_id() ) ); ?>" class="woocommerce-LoopProduct-link">
                                <?php echo $wc_product->get_image( 'medium' ); ?>
                                <h2 class="woocommerce-loop-product__title"><?php echo esc_html( $wc_product->get_name() ); ?></h2>
                                <span class="price"><?php echo $wc_product->get_price_html(); ?></span>
                            </a>
                            <a href="<?php echo esc_url( $wc_product->add_to_cart_url() ); ?>" class="button" data-quantity="1" data-product_id="<?php echo esc_attr( $wc_product->get_id() ); ?>">
                                <?php echo esc_html( $wc_product->add_to_cart_text() ); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </section>
    <?php endif;

    $options = wc_get_products( array(
        'status'   => 'publish',
        'limit'    => 5,
        'category' => array( 'options' ), // ⚠️ slug à vérifier
    ) );

    if ( ! empty( $options ) ) : ?>
        <section class="concept-shop-section woocommerce">
            <div class="container">
                <h2 class="title-with-line concept-shop-title">Les Services en Option</h2>
                <p class="concept-shop-subtitle">Afin de personnaliser votre expérience et de la rendre unique, voici nos suggestions</p>
                <ul class="products columns-5">
                    <?php foreach ( $options as $wc_product ) : ?>
                        <li <?php wc_product_class( '', $wc_product ); ?>>
                            <a href="<?php echo esc_url( get_permalink( $wc_product->get_id() ) ); ?>" class="woocommerce-LoopProduct-link">
                                <?php echo $wc_product->get_image( 'medium' ); ?>
                                <h2 class="woocommerce-loop-product__title"><?php echo esc_html( $wc_product->get_name() ); ?></h2>
                                <span class="price"><?php echo $wc_product->get_price_html(); ?></span>
                            </a>
                            <a href="<?php echo esc_url( $wc_product->add_to_cart_url() ); ?>" class="button" data-quantity="1" data-product_id="<?php echo esc_attr( $wc_product->get_id() ); ?>">
                                <?php echo esc_html( $wc_product->add_to_cart_text() ); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </section>
    <?php endif;

endif;
?>

<section class="concept-cta">
    <div class="container">
        <a href="<?php echo esc_url( home_url('/produit/loveroom/') ); ?>" class="btn-gold-full">Réserver</a>
    </div>
</section>

<?php get_footer(); ?>