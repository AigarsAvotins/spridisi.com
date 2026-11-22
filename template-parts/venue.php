<section id="svini" class="venue section-space" aria-labelledby="venue-heading">
	<div class="container"><div class="section-heading" data-reveal><div><p class="eyebrow section-kicker"><span class="section-number">02 /</span><?php echo esc_html( spridisi_section_summary( 'venue', __( 'KOPĀ BŪŠANA', 'spridisi' ) ) ); ?></p><h2 id="venue-heading"><?php echo esc_html( spridisi_section_title( 'venue', __( 'Svini Sprīdīšos', 'spridisi' ) ) ); ?></h2></div><?php spridisi_section_actions( 'venue' ); ?></div><?php spridisi_section_content( 'venue' ); ?>
		<?php foreach ( array( 'large-hall', 'small-hall', 'outdoor' ) as $hall ) : ?>
			<?php $hall_content = spridisi_homepage_body_markup( 'venue', $hall ); ?>
			<?php if ( $hall_content || 'outdoor' === $hall ) : ?>
				<div class="section-content"><h3><?php echo esc_html( spridisi_homepage_text( 'venue', $hall . '-title', 'outdoor' === $hall ? __( 'Āra terase un teritorija', 'spridisi' ) : '' ) ); ?></h3><?php echo wp_kses_post( $hall_content ); ?></div>
			<?php endif; ?>
		<?php endforeach; ?>
		<div class="venue__image photo-frame" data-reveal><?php spridisi_photo( 'venue', 'full' ); ?><span class="image-index" aria-hidden="true"><?php esc_html_e( 'SPRĪDĪŠI / 01', 'spridisi' ); ?></span></div></div>
</section>