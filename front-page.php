<?php 
get_header(); 

// Fonction temporaire pour trouver l'URL d'un média par son nom (slug)
function get_url_by_media_slug($slug) {
    $query = new WP_Query(array(
        'post_type'      => 'attachment',
        'name'           => $slug,
        'posts_per_page' => 1,
        'post_status'    => 'inherit',
    ));
    
    if ($query->have_posts()) {
        return wp_get_attachment_url($query->posts[0]->ID);
    }
    return ''; // Retourne vide si non trouvé
}
?>
<section class="hero-luxe">
    <video class="hero-video" autoplay muted loop playsinline>
        <source src="<?php echo esc_url(get_url_by_media_slug('love_room')); ?>" type="video/mp4">
    </video>
    <div class="hero-overlay"></div>
    <div class="hero-content-luxe">
        <span class="hero-subtitle">Bettant — Sanctuaire Privé</span>
        <h1>L'Instant <br><span class="gold-title">Plaisir</span></h1>
        <p>Une parenthèse intime hors du temps. <br>Laissez vos sens dicter les règles.</p>
        
        <div class="hero-buttons">
            <a href="<?php echo esc_url( home_url( '/produit/loveroom/' ) ); ?>" class="btn-gold-full">Découvrir la Suite</a>
            <a href="<?php echo esc_url( home_url( '/le-concept/' ) ); ?>" class="btn-outline-white">L'Univers Secret</a>
        </div>
    </div>
    <div class="scroll-indicator"><span></span></div>
</section>
<section class="experience-section">
    <div class="container">
        <div class="section-intro">
            <h2 class="title-with-line">L'Excellence du Secret</h2>
        </div>
        
        <div class="features-grid-luxe">
            <div class="feat-card">
                <div class="feat-icon">01</div>
                <h3>Balnéo Privée</h3>
                <img src="<?php echo esc_url(get_url_by_media_slug('spa')); ?>" alt="Balnéo Privée" class="feat-card-img">
                <p>Un cocon d'eau et de lumière pour une détente absolue sous le signe de la volupté.</p>
            </div>
            <div class="feat-card">
                <div class="feat-icon">02</div>
                <h3>Ambiance Sensorielle</h3>
                <img src="<?php echo esc_url(get_url_by_media_slug('lit')); ?>" alt="Ambiance Sensorielle" class="feat-card-img">
                <p>Jeux de textures, parfums envoûtants et domotique de pointe pour créer votre atmosphère.</p>
            </div>
            <div class="feat-card">
                <div class="feat-icon">03</div>
                <h3>Discrétion Absolue</h3>
                <video autoplay muted loop playsinline class="feat-card-video">
                    <source src="<?php echo esc_url(get_url_by_media_slug('love_room')); ?>" type="video/mp4">
                </video>
                <p>Accès autonome sécurisé et anonymat total pour un séjour en toute sérénité.</p>
            </div>
        </div>
    </div>
</section>
<?php get_footer(); ?>