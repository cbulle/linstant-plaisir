<?php
/* Template Name: Contact */
get_header();

// Traitement du formulaire
$contact_sent  = false;
$contact_error = '';

if ( isset($_POST['lip_contact_nonce']) && wp_verify_nonce($_POST['lip_contact_nonce'], 'lip_contact_form') ) {
    $name    = isset($_POST['contact_name'])    ? sanitize_text_field($_POST['contact_name'])       : '';
    $email   = isset($_POST['contact_email'])   ? sanitize_email($_POST['contact_email'])            : '';
    $message = isset($_POST['contact_message']) ? sanitize_textarea_field($_POST['contact_message']) : '';

    if ( $name && is_email($email) && $message ) {
        $to      = 'contact@instantplaisir.fr';
        $subject = 'Nouveau message depuis le site — ' . $name;
        $body    = "Nom : {$name}\nEmail : {$email}\n\nMessage :\n{$message}";
        $headers = array( 'Reply-To: ' . $email );

        $contact_sent = wp_mail( $to, $subject, $body, $headers );

        if ( ! $contact_sent ) {
            $contact_error = "Une erreur est survenue, réessayez ou contactez-nous directement par téléphone.";
        }
    } else {
        $contact_error = "Merci de remplir tous les champs correctement.";
    }
}
?>

<section class="page-hero-luxe">
    <div class="hero-overlay"></div>
    <div class="page-hero-content">
        <span class="hero-subtitle">Nous Écrire</span>
        <h1>Contact</h1>
    </div>
</section>

<section class="contact-section">
    <div class="container contact-grid">

        <div class="contact-intro">
            <h2 class="title-with-line">Une question ?</h2>
            <p>Ou simplement besoin d'information supplémentaire ?<br>
            Vous souhaitez un devis pour une demande spéciale ?</p>
            <p>N'hésitez pas à nous contacter.<br>
            Notre équipe se fera un plaisir de vous renseigner.</p>

            <ul class="contact-details-list">
                <li><i class="fas fa-map-marker-alt"></i> Discrètement situé à Bettant</li>
                <li><a href="tel:+33780572710"><i class="fas fa-phone"></i> 07 80 57 27 10</a></li>
                <li><a href="mailto:reservation@linstantplaisir.fr"><i class="fas fa-envelope"></i> reservation@linstantplaisir.fr</a></li>
            </ul>
        </div>

        <div class="contact-form-wrap">

            <?php if ( $contact_sent ) : ?>

                <div class="contact-form-success">
                    <p>Votre message a bien été envoyé. Nous revenons vers vous très vite.</p>
                </div>

            <?php else : ?>

                <?php if ( $contact_error ) : ?>
                    <div class="contact-form-error"><p><?php echo esc_html( $contact_error ); ?></p></div>
                <?php endif; ?>

                <form method="post" class="contact-form-luxe">
                    <?php wp_nonce_field( 'lip_contact_form', 'lip_contact_nonce' ); ?>

                    <div class="form-group">
                        <label for="contact_name">Nom</label>
                        <input type="text" id="contact_name" name="contact_name" required>
                    </div>

                    <div class="form-group">
                        <label for="contact_email">Adresse e-mail</label>
                        <input type="email" id="contact_email" name="contact_email" required>
                    </div>

                    <div class="form-group">
                        <label for="contact_message">Message</label>
                        <textarea id="contact_message" name="contact_message" rows="6" required></textarea>
                    </div>

                    <button type="submit" class="btn-gold-full">Envoyer</button>
                </form>

            <?php endif; ?>

        </div>

    </div>
</section>

<?php get_footer(); ?>