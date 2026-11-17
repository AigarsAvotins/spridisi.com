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
			<?php $email = sanitize_email( get_theme_mod( 'spridisi_email', '' ) ); $phone = get_theme_mod( 'spridisi_phone', '' ); $address = get_theme_mod( 'spridisi_address', '' ); ?>
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
		<nav class="footer-navigation" aria-label="<?php esc_attr_e( 'Kājenes navigācija', 'spridisi' ); ?>"><h2 class="eyebrow"><?php esc_html_e( 'SPRĪDĪŠOS', 'spridisi' ); ?></h2><?php wp_nav_menu( array( 'theme_location' => 'footer', 'container' => false, 'fallback_cb' => 'spridisi_default_menu', 'depth' => 1 ) ); ?></nav>
		<div class="footer-social"><h2 class="eyebrow"><?php esc_html_e( 'SEKO LĪDZI', 'spridisi' ); ?></h2>
			<?php $has_social = false; foreach ( array( 'instagram' => __( 'Instagram', 'spridisi' ), 'facebook' => __( 'Facebook', 'spridisi' ) ) as $network => $label ) : $social_url = get_theme_mod( 'spridisi_' . $network, '' ); ?>
				<?php if ( $social_url ) : $has_social = true; ?><a href="<?php echo esc_url( $social_url ); ?>"><?php echo esc_html( $label ); ?><span aria-hidden="true"> ↗</span></a><?php endif; ?>
			<?php endforeach; ?>
			<?php if ( ! $has_social ) : ?><p class="footer-pending"><?php esc_html_e( 'Sociālās saites tiks papildinātas.', 'spridisi' ); ?></p><?php endif; ?>
		</div>
	</div><div class="footer-bottom"><span>© <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php esc_html_e( 'SPRĪDĪŠI', 'spridisi' ); ?></span><span><?php echo esc_html( spridisi_section_title( 'hero', __( 'Te ir tā laimes zeme.', 'spridisi' ) ) ); ?></span><a href="<?php echo esc_url( spridisi_home_anchor( 'sakums' ) ); ?>"><?php esc_html_e( 'Uz sākumu', 'spridisi' ); ?> <span aria-hidden="true">↑</span></a></div></div>
</footer>
<?php wp_footer(); ?>
</body>
</html>