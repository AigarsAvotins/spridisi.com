<?php
/**
 * Template Name: Spridisi Booking Request
 *
 * @package Spridisi
 */

get_header();
?>
<main id="main-content" class="site-main booking-page" tabindex="-1">
	<div class="booking-layout">
		<header class="booking-intro">
			<?php while ( have_posts() ) : the_post(); ?>
				<?php spridisi_request_page_content(); ?>
			<?php endwhile; ?>
		</header>
		<div class="booking-form-area"><?php spridisi_request_form(); ?></div>
	</div>
</main>
<?php
get_footer();