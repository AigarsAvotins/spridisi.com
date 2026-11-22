<?php
/**
 * Full migrated history archive.
 *
 * @package Spridisi
 */

get_header();
?>
<main id="main-content" class="site-main history-archive-page" tabindex="-1">
	<?php while ( have_posts() ) : the_post(); ?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'history-archive' ); ?>>
			<header class="history-archive__intro container">
				<p class="eyebrow section-kicker"><?php esc_html_e( 'VIETAS ATMIŅA', 'spridisi' ); ?></p>
				<h1><?php the_title(); ?></h1>
				<div class="history-archive__facts"><?php the_content(); ?></div>
			</header>
			<div class="container">
				<?php spridisi_history_render_timeline( spridisi_content_children( get_the_ID() ), 'history-archive-timeline', __( 'Visi vēstures fakti', 'spridisi' ) ); ?>
			</div>
		</article>
	<?php endwhile; ?>
</main>
<?php
get_footer();