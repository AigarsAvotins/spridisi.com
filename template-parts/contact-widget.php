<?php
/**
 * Floating contact widget for homepage.
 *
 * @package Spridisi
 */
$phone = get_theme_mod( 'spridisi_phone', '+37126344495' );
$phone_digits = preg_replace( '/\D/', '', $phone );
$reservation_link = spridisi_reservation_link();
?>
<div class="contact-widget" id="contact-widget" data-delay="7000">
	<button class="contact-widget__toggle" aria-expanded="false" aria-controls="contact-widget-menu">
		<span class="contact-widget__icon" aria-hidden="true">💬</span>
		<span class="contact-widget__label"><?php esc_html_e( 'Sazinies ar mums', 'spridisi' ); ?></span>
	</button>
	<div class="contact-widget__menu" id="contact-widget-menu" hidden>
		<div class="contact-widget__header">
			<h3><?php esc_html_e( 'Kā varam palīdzēt?', 'spridisi' ); ?></h3>
			<button class="contact-widget__close" aria-label="<?php esc_attr_e( 'Aizvērt', 'spridisi' ); ?>">×</button>
		</div>
		<div class="contact-widget__content">
			<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ); ?>" class="contact-widget__option">
				<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/Whatsapp.png' ); ?>" alt="Tel" class="contact-widget__option-icon">
				<span class="contact-widget__option-text"><?php echo esc_html( $phone ); ?></span>
			</a>
			<a href="<?php echo esc_url( $reservation_link ); ?>" class="contact-widget__option">
				<span class="contact-widget__option-icon" aria-hidden="true">📋</span>
				<span class="contact-widget__option-text"><?php esc_html_e( 'Rezervēt', 'spridisi' ); ?></span>
			</a>
		</div>
	</div>
</div>
