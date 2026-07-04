<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
  <header class="site-header">
    <div class="header-inner">

      <div class="logo">
        <a href="<?php echo home_url(); ?>" class="logo-link">
          <?php
          $logo_image = get_page_by_path('logo', OBJECT, 'attachment');
          if ($logo_image):
            echo wp_get_attachment_image($logo_image->ID, 'medium', false, array('class' => 'logo-img'));

          endif;
          ?>
          <div class="logo-text">
            L'INSTANT <span class="gold-text">PLAISIR</span>
            <small class="tagline">Secret & Volupté</small>
          </div>
        </a>
      </div>

      <nav class="main-nav">
        <?php
        wp_nav_menu([
          'theme_location' => 'menu-principal',
          'container' => false,
          'menu_class' => 'nav-list-luxe'
        ]);
        ?>
      </nav>

      <div class="header-actions">
        <a href="<?php echo esc_url( home_url( '/produit/loveroom/' ) ); ?>" class="btn-reserver-luxe">
          <span class="btn-text"><?php esc_html_e('Réserver mon séjour', 'linstant-plaisir'); ?></span>
        </a>
        <button class="burger" aria-label="Menu">
          <span></span><span></span><span></span>
        </button>
      </div>

    </div>
  </header>