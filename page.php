<?php
/**
 * Standard page template.
 *
 * @package Spridisi
 */

get_header();
?>
<main id="main-content" class="site-main" tabindex="-1">
	<?php while ( have_posts() ) : ?>
		<?php the_post(); ?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<?php the_title( '<h1>', '</h1>' ); ?>
			<div class="entry-content">
				<?php
				the_content();
				wp_link_pages();
				?>
			</div>
		</article>
	<?php endwhile; ?>
</main>
<?php
get_footer();