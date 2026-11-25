<?php
/**
 * Floating contact widget for homepage.
 *
 * @package Spridisi
 */
$phone = get_theme_mod( 'spridisi_phone', '+37126344495' );
$phone_digits = preg_replace( '/\D/', '', $phone );
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
			<a href="<?php echo esc_url( 'https://wa.me/' . $phone_digits ); ?>" target="_blank" rel="noopener noreferrer" class="contact-widget__whatsapp">
				<svg class="contact-widget__whatsapp-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
					<path fill="currentColor" d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.67-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.076 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421-7.403h-.004a9.87 9.87 0 00-9.746 9.798c0 2.734.732 5.41 2.124 7.738L.957 23.08l8.305-2.164a9.822 9.822 0 004.692 1.195h.005c5.487 0 9.968-4.479 9.968-9.979 0-2.665-.975-5.166-2.724-7.141-1.802-1.998-4.374-3.123-7.212-3.123" />
				</svg>
				<span class="contact-widget__whatsapp-text"><?php echo esc_html( $phone ); ?></span>
			</a>
		</div>
		<div class="contact-widget__form">
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
