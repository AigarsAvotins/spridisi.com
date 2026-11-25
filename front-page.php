<?php
/**
 * Front page.
 *
 * @package Spridisi
 */

get_header();
?>
<main id="main-content" class="site-main homepage" tabindex="-1">
	<?php
	get_template_part( 'template-parts/hero' );
	get_template_part( 'template-parts/venue' );
	get_template_part( 'template-parts/accommodation' );
	get_template_part( 'template-parts/sauna' );
	get_template_part( 'template-parts/experiences' );
	get_template_part( 'template-parts/history' );
	get_template_part( 'template-parts/gallery' );
	get_template_part( 'template-parts/contact' );
	get_template_part( 'template-parts/contact-widget' );
	?>
</main>
<?php
get_footer();