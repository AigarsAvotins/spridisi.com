<?php
/**
 * Not found template.
 *
 * @package Spridisi
 */

get_header();
?>
<main id="main-content" class="site-main" tabindex="-1">
	<h1><?php esc_html_e( 'Page Not Found', 'spridisi' ); ?></h1>
	<p><?php esc_html_e( 'The requested page could not be found.', 'spridisi' ); ?></p>
	<p><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Return to the homepage', 'spridisi' ); ?></a></p>
	<?php get_search_form(); ?>
</main>
<?php
get_footer();