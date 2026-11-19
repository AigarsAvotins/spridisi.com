<?php
/**
 * Template Name: Spridisi Gallery
 *
 * @package Spridisi
 */

get_header();
?>
<main id="main-content" class="site-main gallery-page" tabindex="-1">
	<?php while ( have_posts() ) : ?>
		<?php the_post(); ?>
		<?php spridisi_gallery_render_content(); ?>
	<?php endwhile; ?>
</main>
<?php
get_footer();