<?php
/**
 * Default content template.
 *
 * @package Spridisi
 */

get_header();
?>
<main id="main-content" class="site-main" tabindex="-1">
	<?php if ( have_posts() ) : ?>
		<?php if ( ! is_singular() ) : ?>
			<header class="page-header">
				<?php if ( is_archive() ) : ?>
					<?php the_archive_title( '<h1>', '</h1>' ); ?>
				<?php elseif ( is_search() ) : ?>
					<h1><?php esc_html_e( 'Search Results', 'spridisi' ); ?></h1>
				<?php else : ?>
					<h1><?php esc_html_e( 'Posts', 'spridisi' ); ?></h1>
				<?php endif; ?>
			</header>
		<?php endif; ?>
		<?php while ( have_posts() ) : ?>
			<?php the_post(); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<?php if ( is_singular() ) : ?>
					<?php the_title( '<h1>', '</h1>' ); ?>
					<div class="entry-content">
						<?php
						the_content();
						wp_link_pages();
						?>
					</div>
				<?php else : ?>
					<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<?php the_excerpt(); ?>
				<?php endif; ?>
			</article>
		<?php endwhile; ?>
		<?php the_posts_pagination(); ?>
	<?php else : ?>
		<h1><?php esc_html_e( 'Nothing Found', 'spridisi' ); ?></h1>
		<p><?php esc_html_e( 'No content was found.', 'spridisi' ); ?></p>
	<?php endif; ?>
</main>
<?php
get_footer();