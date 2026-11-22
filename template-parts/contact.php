<section id="kontakti" class="contact section-space" aria-labelledby="contact-heading">
	<div class="container contact__layout" data-reveal><div><p class="eyebrow section-kicker"><?php echo esc_html( spridisi_homepage_text( 'contact', 'eyebrow', __( 'TE IR TĀ LAIMES ZEME.', 'spridisi' ) ) ); ?></p><h2 id="contact-heading"><?php echo esc_html( spridisi_section_title( 'contact', __( 'Tiekamies Sprīdīšos.', 'spridisi' ) ) ); ?></h2><?php spridisi_section_content( 'contact' ); ?></div><div class="contact__action">
		<?php if ( spridisi_booking_url() ) : ?>
			<a class="button" href="<?php echo esc_url( spridisi_booking_url() ); ?>"><?php echo esc_html( spridisi_reservation_cta_label() ); ?><span aria-hidden="true">↗</span></a>
		<?php else : ?>
			<button class="button" type="button" disabled aria-describedby="booking-pending"><?php echo esc_html( spridisi_reservation_cta_label() ); ?><span aria-hidden="true">↗</span></button><p id="booking-pending" class="content-pending"><?php esc_html_e( 'Rezervācijas kontaktinformācija tiks papildināta.', 'spridisi' ); ?></p>
		<?php endif; ?>
		<p class="contact__location"><?php echo esc_html( spridisi_section_summary( 'contact', __( 'Vecsēlpils • Sēlija', 'spridisi' ) ) ); ?></p>
		<?php $contact_details = spridisi_default_contact_details(); ?>
		<address class="contact__address"><?php echo esc_html( get_theme_mod( 'spridisi_address', $contact_details['address'] ) ); ?></address>
		<div class="contact__directions">
			<a class="button button--small" href="https://www.google.com/maps/dir/?api=1&amp;destination=56.5885486%2C25.6593072" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Norādes Google Maps', 'spridisi' ); ?><span aria-hidden="true">↗</span></a>
			<a class="button button--small" href="https://waze.com/ul?ll=56.5885486%2C25.6593072&amp;navigate=yes" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Norādes Waze', 'spridisi' ); ?><span aria-hidden="true">↗</span></a>
		</div>
	</div>
	<div class="contact__map"><iframe title="<?php esc_attr_e( 'Sprīdīšu atrašanās vietas karte', 'spridisi' ); ?>" src="https://maps.google.com/maps?q=56.5885486%2C25.6593072&amp;z=15&amp;output=embed" loading="lazy" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe></div>
	</div>
</section>