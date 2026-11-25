<?php
/**
 * Floating contact widget for homepage.
 *
 * @package Spridisi
 */
$phone = get_theme_mod( 'spridisi_phone', '+37126344495' );
$phone_digits = preg_replace( '/\D/', '', $phone );
$reservation_link = spridisi_reservation_link();
$site_email = get_option( 'admin_email' );
?>
<div class="contact-widget" id="contact-widget" data-delay="10000" data-email="<?php echo esc_attr( $site_email ); ?>">
	<div class="contact-widget__menu" id="contact-widget-menu">
		<div class="contact-widget__header">
			<h3><?php esc_html_e( 'Kā varam palīdzēt?', 'spridisi' ); ?></h3>
			<button class="contact-widget__close" aria-label="<?php esc_attr_e( 'Aizvērt', 'spridisi' ); ?>">×</button>
		</div>
		<div class="contact-widget__content">
			<a href="https://wa.me/<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ); ?>" class="contact-widget__option" target="_blank" rel="noopener noreferrer">
				<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/Whatsapp.png' ); ?>" alt="WhatsApp" class="contact-widget__option-icon">
				<span class="contact-widget__option-text"><?php echo esc_html( $phone ); ?></span>
			</a>
			<a href="<?php echo esc_url( $reservation_link ); ?>" class="contact-widget__option">
				<span class="contact-widget__option-icon" aria-hidden="true">📅</span>
				<span class="contact-widget__option-text"><?php esc_html_e( 'Noskaidrot cenu un rezervēt', 'spridisi' ); ?></span>
			</a>
			<button type="button" class="contact-widget__option contact-widget__message-toggle" aria-expanded="false">
				<span class="contact-widget__option-icon" aria-hidden="true">📧</span>
				<span class="contact-widget__option-text"><?php esc_html_e( 'Nosūtīt ziņu', 'spridisi' ); ?></span>
			</button>
		</div>
		<div class="contact-widget__message-form" hidden>
			<form class="contact-widget__form-content" id="widget-message-form">
				<div class="contact-widget__form-group">
					<label for="widget-msg-name"><?php esc_html_e( 'Vārds', 'spridisi' ); ?></label>
					<input type="text" id="widget-msg-name" name="name" required>
				</div>
				<div class="contact-widget__form-group">
					<label for="widget-msg-email"><?php esc_html_e( 'E-pasts', 'spridisi' ); ?></label>
					<input type="email" id="widget-msg-email" name="email" required>
				</div>
				<div class="contact-widget__form-group">
					<label for="widget-msg-message"><?php esc_html_e( 'Ziņa', 'spridisi' ); ?></label>
					<textarea id="widget-msg-message" name="message" rows="4" required></textarea>
				</div>
				<button type="submit" class="contact-widget__submit"><?php esc_html_e( 'Nosūtīt', 'spridisi' ); ?></button>
				<button type="button" class="contact-widget__message-close" aria-label="<?php esc_attr_e( 'Aizvērt formu', 'spridisi' ); ?>"><?php esc_html_e( 'Atcelt', 'spridisi' ); ?></button>
			</form>
		</div>
	</div>
</div>
