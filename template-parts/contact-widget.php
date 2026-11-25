<?php
/**
 * Floating contact widget for homepage.
 *
 * @package Spridisi
 */
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
			<a href="<?php echo esc_url( 'https://wa.me/' . preg_replace( '/\D/', '', get_theme_mod( 'spridisi_phone', '+37126344495' ) ) ); ?>" target="_blank" rel="noopener noreferrer" class="contact-widget__option contact-widget__whatsapp">
				<span class="contact-widget__option-icon" aria-hidden="true">📱</span>
				<span class="contact-widget__option-text"><?php esc_html_e( 'WhatsApp', 'spridisi' ); ?></span>
			</a>
			<button class="contact-widget__option contact-widget__form-toggle" type="button">
				<span class="contact-widget__option-icon" aria-hidden="true">📋</span>
				<span class="contact-widget__option-text"><?php esc_html_e( 'Rezervācijas forma', 'spridisi' ); ?></span>
			</button>
		</div>
		<div class="contact-widget__form" hidden>
			<form class="contact-widget__form-content" id="widget-booking-form">
				<div class="contact-widget__form-group">
					<label for="widget-name"><?php esc_html_e( 'Vārds', 'spridisi' ); ?></label>
					<input type="text" id="widget-name" name="name" required>
				</div>
				<div class="contact-widget__form-group">
					<label for="widget-email"><?php esc_html_e( 'E-pasts', 'spridisi' ); ?></label>
					<input type="email" id="widget-email" name="email" required>
				</div>
				<div class="contact-widget__form-group">
					<label for="widget-phone"><?php esc_html_e( 'Tālrunis', 'spridisi' ); ?></label>
					<input type="tel" id="widget-phone" name="phone">
				</div>
				<div class="contact-widget__form-group">
					<label for="widget-dates"><?php esc_html_e( 'Vēlamie datumi', 'spridisi' ); ?></label>
					<input type="text" id="widget-dates" name="dates" placeholder="piem. 2024-12-15 līdz 2024-12-20">
				</div>
				<div class="contact-widget__form-group">
					<label for="widget-message"><?php esc_html_e( 'Ziņojums', 'spridisi' ); ?></label>
					<textarea id="widget-message" name="message" rows="3"></textarea>
				</div>
				<button type="submit" class="contact-widget__submit"><?php esc_html_e( 'Pieprasīt', 'spridisi' ); ?></button>
			</form>
		</div>
	</div>
</div>
