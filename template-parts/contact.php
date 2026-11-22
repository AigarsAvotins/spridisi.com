<section id="kontakti" class="contact section-space" aria-labelledby="contact-heading">
	<div class="container contact__layout" data-reveal><div><p class="eyebrow section-kicker"><?php echo esc_html( spridisi_homepage_text( 'contact', 'eyebrow', __( 'TE IR TĀ LAIMES ZEME.', 'spridisi' ) ) ); ?></p><h2 id="contact-heading"><?php echo esc_html( spridisi_section_title( 'contact', __( 'Tiekamies Sprīdīšos.', 'spridisi' ) ) ); ?></h2><?php spridisi_section_content( 'contact' ); ?></div><div class="contact__action">
		<?php if ( spridisi_booking_url() ) : ?>
			<a class="button" href="<?php echo esc_url( spridisi_booking_url() ); ?>"><?php echo esc_html( spridisi_reservation_cta_label() ); ?><span aria-hidden="true">↗</span></a>
		<?php else : ?>
			<button class="button" type="button" disabled aria-describedby="booking-pending"><?php echo esc_html( spridisi_reservation_cta_label() ); ?><span aria-hidden="true">↗</span></button><p id="booking-pending" class="content-pending"><?php esc_html_e( 'Rezervācijas kontaktinformācija tiks papildināta.', 'spridisi' ); ?></p>
		<?php endif; ?>
		<p class="contact__location"><?php echo esc_html( spridisi_section_summary( 'contact', __( 'Vecsēlpils • Sēlija', 'spridisi' ) ) ); ?></p>
	</div></div>
</section>