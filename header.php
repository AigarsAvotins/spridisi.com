<?php
/**
 * Site header.
 *
 * @package Spridisi
 */
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#main-content"><?php esc_html_e( 'Pāriet uz saturu', 'spridisi' ); ?></a>
<header class="site-header">
	<div class="header-inner container">
		<a class="wordmark" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" aria-label="<?php esc_attr_e( 'Sprīdīši — sākums', 'spridisi' ); ?>"><?php esc_html_e( 'SPRĪDĪŠI', 'spridisi' ); ?><span><?php esc_html_e( 'VECSĒLPILS · SĒLIJA', 'spridisi' ); ?></span></a>
		<?php $header_contact_defaults = spridisi_default_contact_details(); $header_phone = get_theme_mod( 'spridisi_phone', $header_contact_defaults['phone'] ); $header_phone_digits = preg_replace( '/\D/', '', $header_phone ); ?>
		<div class="header-contact">
			<span class="header-contact__text"><?php echo esc_html( $header_phone ); ?></span>
		</div>
		<button class="menu-toggle" type="button" aria-expanded="false" aria-controls="primary-menu" hidden><span class="menu-toggle__lines" aria-hidden="true"></span><span class="menu-toggle__label"><?php esc_html_e( 'Izvēlne', 'spridisi' ); ?></span></button>
		<div id="primary-menu" class="primary-navigation">
			<nav class="site-navigation" aria-label="<?php esc_attr_e( 'Galvenā navigācija', 'spridisi' ); ?>">
				<?php wp_nav_menu( array( 'theme_location' => 'primary', 'container' => false, 'fallback_cb' => 'spridisi_default_menu', 'depth' => 2 ) ); ?>
			</nav>
			<?php if ( has_nav_menu( 'language' ) ) : ?>
				<nav class="language-navigation" aria-label="<?php esc_attr_e( 'Valodas izvēle', 'spridisi' ); ?>">
					<?php wp_nav_menu( array( 'theme_location' => 'language', 'container' => false, 'fallback_cb' => false, 'depth' => 1 ) ); ?>
				</nav>
			<?php endif; ?>
		</div>
		<a class="button button--small header-booking" href="<?php echo esc_url( spridisi_reservation_link() ?: spridisi_home_anchor( 'kontakti' ) ); ?>"><?php echo esc_html( spridisi_reservation_cta_label() ); ?><span aria-hidden="true">↗</span></a>
	</div>
</header>