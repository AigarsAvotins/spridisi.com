<?php
/**
 * Site footer.
 *
 * @package Spridisi
 */
?>
<footer class="site-footer">
	<div class="container"><div class="footer-grid">
		<div><a class="wordmark" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'SPRĪDĪŠI', 'spridisi' ); ?></a><p class="footer-location"><?php echo esc_html( spridisi_section_summary( 'contact', __( 'Vecsēlpils · Sēlija', 'spridisi' ) ) ); ?></p></div>
		<div class="footer-contact"><h2 class="eyebrow"><?php esc_html_e( 'SAZINĀTIES', 'spridisi' ); ?></h2>
			<?php $contact_defaults = spridisi_default_contact_details(); $email = sanitize_email( get_theme_mod( 'spridisi_email', $contact_defaults['email'] ) ); $phone = get_theme_mod( 'spridisi_phone', $contact_defaults['phone'] ); $address = get_theme_mod( 'spridisi_address', $contact_defaults['address'] ); ?>
			<?php $contact_page = spridisi_section_page( 'contact' ); ?>
			<?php if ( spridisi_homepage_body_markup( 'contact' ) || ( $contact_page && trim( $contact_page->post_content ) ) ) : ?>
				<?php spridisi_section_content( 'contact' ); ?>
			<?php else : ?>
			<?php if ( $email ) : ?><a href="<?php echo esc_url( 'mailto:' . $email ); ?>"><?php echo esc_html( $email ); ?></a><?php endif; ?>
			<?php if ( $phone ) : ?><a href="<?php echo esc_url( 'tel:' . preg_replace( '/[^0-9+]/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a><?php endif; ?>
			<?php if ( $address ) : ?><p><?php echo esc_html( $address ); ?></p><?php endif; ?>
			<?php if ( ! $email && ! $phone && ! $address ) : ?><p class="footer-pending"><?php esc_html_e( 'Kontaktinformācija tiks papildināta.', 'spridisi' ); ?></p><?php endif; ?>
			<?php endif; ?>
		</div>
		<div class="footer-social"><h2 class="eyebrow"><?php esc_html_e( 'SEKO LĪDZI', 'spridisi' ); ?></h2>
			<?php $has_social = false; foreach ( array( 'instagram' => __( 'Instagram', 'spridisi' ), 'facebook' => __( 'Facebook', 'spridisi' ) ) as $network => $label ) : $social_url = get_theme_mod( 'spridisi_' . $network, $contact_defaults[ $network ] ); ?>
				<?php if ( $social_url ) : $has_social = true; ?><a href="<?php echo esc_url( $social_url ); ?>"><?php echo esc_html( $label ); ?><span aria-hidden="true"> ↗</span></a><?php endif; ?>
			<?php endforeach; ?>
			<?php if ( ! $has_social ) : ?><p class="footer-pending"><?php esc_html_e( 'Sociālās saites tiks papildinātas.', 'spridisi' ); ?></p><?php endif; ?>
		</div>
	</div>
<?php
$review_defaults = spridisi_default_review_details();
$review_sources = array(
	'google' => array(
		'label' => 'Google',
		'rating' => get_theme_mod( 'spridisi_google_rating', $review_defaults['google_rating'] ),
		'count' => absint( get_theme_mod( 'spridisi_google_review_count', $review_defaults['google_review_count'] ) ),
		'reviews' => get_theme_mod( 'spridisi_google_reviews', $review_defaults['google_reviews'] ),
		'scale' => '5',
		'url' => 'https://www.google.com/maps/place/Vecs%C4%93lpils+SPR%C4%AAD%C4%AA%C5%A0I/@56.5885515,25.6567323,17z/data=!4m8!3m7!1s0x46e9afeec58a9a53:0x52425a7be3abcf70!8m2!3d56.5885486!4d25.6593072!9m1!1b1!16s%2Fg%2F11qqynwcrm',
	),
	'booking' => array(
		'label' => 'Booking.com',
		'rating' => get_theme_mod( 'spridisi_booking_rating', $review_defaults['booking_rating'] ),
		'count' => absint( get_theme_mod( 'spridisi_booking_review_count', $review_defaults['booking_review_count'] ) ),
		'reviews' => get_theme_mod( 'spridisi_booking_reviews', $review_defaults['booking_reviews'] ),
		'scale' => '10',
		'url' => 'https://www.booking.com/hotel/lv/brivdienu-maja-spridisi-te-ir-ta-laimes-zeme.lv.html',
	),
);
$show_reviews = false;
foreach ( $review_sources as $review_source ) {
	if ( trim( (string) $review_source['rating'] ) || $review_source['count'] || trim( (string) $review_source['reviews'] ) ) {
		$show_reviews = true;
		break;
	}
}
if ( $show_reviews ) :
	?>
	<section class="footer-reviews" aria-labelledby="footer-reviews-heading">
		<div class="container">
			<h2 id="footer-reviews-heading"><?php esc_html_e( 'Viesu atsauksmes', 'spridisi' ); ?></h2>
			<div class="footer-review-grid">
				<?php foreach ( $review_sources as $review_source ) : ?>
					<?php if ( ! trim( (string) $review_source['rating'] ) && ! $review_source['count'] && ! trim( (string) $review_source['reviews'] ) ) : ?>
						<?php continue; ?>
					<?php endif; ?>
					<article class="footer-review-source">
						<h3><?php echo esc_html( $review_source['label'] ); ?></h3>
						<?php if ( trim( (string) $review_source['rating'] ) ) : ?><p class="footer-review-score"><strong><?php echo esc_html( $review_source['rating'] ); ?></strong><span> / <?php echo esc_html( $review_source['scale'] ); ?></span></p><?php endif; ?>
						<?php if ( $review_source['count'] ) : ?><p class="footer-review-count"><?php printf( esc_html( _n( '%s atsauksme', '%s atsauksmes', $review_source['count'], 'spridisi' ) ), esc_html( number_format_i18n( $review_source['count'] ) ) ); ?></p><?php endif; ?>
						<?php if ( trim( (string) $review_source['reviews'] ) ) : ?><div class="footer-review-excerpts"><?php foreach ( preg_split( '/\r\n|\r|\n/', trim( $review_source['reviews'] ) ) as $review_line ) : ?><p><?php echo esc_html( $review_line ); ?></p><?php endforeach; ?></div><?php endif; ?>
						<a class="footer-review-link" href="<?php echo esc_url( $review_source['url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Skatīt atsauksmes', 'spridisi' ); ?><span aria-hidden="true"> ↗</span></a>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
<?php endif; ?>
<div class="container"><div class="footer-bottom"><span>© <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php esc_html_e( 'SPRĪDĪŠI', 'spridisi' ); ?></span><span><?php echo esc_html( spridisi_section_title( 'hero', __( 'Te ir tā laimes zeme.', 'spridisi' ) ) ); ?></span><a href="<?php echo esc_url( spridisi_home_anchor( 'sakums' ) ); ?>"><?php esc_html_e( 'Uz sākumu', 'spridisi' ); ?> <span aria-hidden="true">↑</span></a></div></div>
</footer>
<?php wp_footer(); ?>
</body>
</html>