<?php
$experiences_page = spridisi_section_page( 'experiences' );
$experience_items = spridisi_content_children( $experiences_page ? $experiences_page->ID : 0 );
if ( ! $experience_items ) {
	$experience_items = array( 'daugava' => __( 'Daugava', 'spridisi' ), 'sup' => __( 'SUP', 'spridisi' ), 'selija' => __( 'Iepazīt Sēliju', 'spridisi' ) );
}
$native_items = spridisi_homepage_items( 'experiences' );
if ( $native_items ) {
	$experience_items = array();
	foreach ( $native_items as $item ) {
		$experience_items[ $item ] = array( 'section' => 'experiences/' . $item );
	}
}
?>
<section id="piedzivo" class="experiences section-space" aria-labelledby="experiences-heading">
	<div class="container"><div class="section-heading" data-reveal><div><p class="eyebrow section-kicker"><span class="section-number">05 /</span><?php echo esc_html( spridisi_homepage_text( 'experiences', 'eyebrow', __( 'AIZ IKDIENAS', 'spridisi' ) ) ); ?></p><h2 id="experiences-heading"><?php echo esc_html( spridisi_section_title( 'experiences', __( 'Piedzīvo Sēliju', 'spridisi' ) ) ); ?></h2></div><span class="section-aside"><?php echo esc_html( spridisi_section_summary( 'experiences', __( 'Daugava. Daba. Sēlija.', 'spridisi' ) ) ); ?></span></div><?php spridisi_section_content( 'experiences' ); ?><div class="experience-grid">
		<?php foreach ( $experience_items as $key => $experience ) : ?>
			<?php
			$experience_page = $experience instanceof WP_Post ? $experience : null;
			$native_section  = is_array( $experience ) ? $experience['section'] : '';
			$label = $native_section ? spridisi_homepage_text( $native_section, 'title' ) : ( $experience_page ? get_the_title( $experience_page ) : $experience );
			$url   = $native_section ? spridisi_homepage_link( $native_section, 'primary', '#kontakti' ) : ( $experience_page ? get_permalink( $experience_page ) : '#kontakti' );
			$action = $native_section ? spridisi_homepage_text( $native_section, 'primary', __( 'Uzzināt vairāk', 'spridisi' ) ) : __( 'Uzzināt vairāk', 'spridisi' );
			?>
			<article class="experience" data-reveal><div class="experience__image photo-frame"><?php spridisi_photo( $native_section ? $native_section : (string) $key, 'large', $experience_page ); ?></div><h3><?php echo esc_html( $label ); ?></h3><a class="experience__link" href="<?php echo esc_url( $url ); ?>"><span class="screen-reader-text"><?php echo esc_html( sprintf( __( '%1$s: %2$s', 'spridisi' ), $action, $label ) ); ?></span><span aria-hidden="true">↗</span></a><?php if ( $native_section && spridisi_homepage_body_markup( $native_section ) ) : ?><div class="experience__description"><?php spridisi_homepage_body( $native_section ); ?></div><?php elseif ( $experience_page ) : ?><div class="experience__description"><?php if ( $experience_page->post_excerpt ) : ?><p><?php echo esc_html( wp_strip_all_tags( $experience_page->post_excerpt ) ); ?></p><?php endif; ?><?php spridisi_page_content( $experience_page ); ?></div><?php endif; ?></article>
		<?php endforeach; ?>
	</div></div>
</section>