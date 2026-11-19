<section id="sakums" class="hero" aria-labelledby="hero-heading">
	<?php
	$native_hero = spridisi_homepage_photo( 'hero', 'hero__image', 'full', true );
	$hero_image = absint( get_theme_mod( 'spridisi_image_hero', 0 ) );
	$hero_page  = spridisi_section_page( 'hero' );
	if ( $hero_page && has_post_thumbnail( $hero_page ) ) {
		$hero_image = get_post_thumbnail_id( $hero_page );
	}
	if ( ! $hero_image && is_page() ) {
		$hero_image = get_post_thumbnail_id( get_queried_object_id() );
	}
	if ( $hero_image && ! $native_hero ) {
		echo wp_get_attachment_image( $hero_image, 'full', false, array( 'class' => 'hero__image', 'alt' => '', 'loading' => 'eager', 'fetchpriority' => 'high' ) );
	} elseif ( ! $native_hero ) {
		?>
		<img class="hero__image" src="<?php echo esc_url( get_theme_file_uri( '/assets/images/hero-spridisi.jpg' ) ); ?>" alt="" width="2048" height="1536" loading="eager" fetchpriority="high" decoding="async">
		<?php
	}
	?>
	<div class="hero__content container">
		<p class="eyebrow hero__eyebrow"><?php echo esc_html( spridisi_homepage_text( 'hero', 'eyebrow', spridisi_section_summary( 'contact', __( 'VECSĒLPILS • SĒLIJA', 'spridisi' ) ) ) ); ?></p>
		<h1 id="hero-heading"><?php echo esc_html( spridisi_section_title( 'hero', __( 'Te ir tā laimes zeme.', 'spridisi' ) ) ); ?></h1>
		<p class="hero__description"><?php echo esc_html( spridisi_section_summary( 'hero', __( 'Vieta Vecsēlpilī, kur svinēt, atpūsties un piedzīvot Sēliju.', 'spridisi' ) ) ); ?></p>
		<?php spridisi_section_content( 'hero' ); ?>
		<div class="hero__actions"><a class="button" href="<?php echo esc_url( spridisi_reservation_link( spridisi_homepage_link( 'hero', 'primary', '#kontakti' ) ) ); ?>"><?php echo esc_html( spridisi_homepage_text( 'hero', 'primary', __( 'Rezervēt', 'spridisi' ) ) ); ?><span aria-hidden="true">↗</span></a><a class="text-link text-link--light" href="<?php echo esc_url( spridisi_homepage_link( 'hero', 'secondary', '#ievads' ) ); ?>"><?php echo esc_html( spridisi_homepage_text( 'hero', 'secondary', __( 'Iepazīt Sprīdīšus', 'spridisi' ) ) ); ?><span aria-hidden="true">↓</span></a></div>
	</div>
	<div class="hero__bottom container"><a class="scroll-cue" href="#ievads"><span class="scroll-cue__line" aria-hidden="true"></span><?php esc_html_e( 'Tālāk, nesteidzoties', 'spridisi' ); ?></a></div>
</section>
<section id="ievads" class="introduction section-space" aria-labelledby="intro-heading">
	<div class="container introduction__layout" data-reveal>
		<p class="eyebrow section-kicker"><span class="section-number">01 /</span><?php echo esc_html( spridisi_section_summary( 'introduction', __( 'TEPAT, SĒLIJĀ', 'spridisi' ) ) ); ?></p>
		<div><h2 id="intro-heading"><?php echo esc_html( spridisi_section_title( 'introduction', __( 'Vieta, kur palikt ilgāk.', 'spridisi' ) ) ); ?></h2><div class="introduction__copy">
			<?php if ( null !== spridisi_homepage_field( 'introduction', 'body' ) || spridisi_section_page( 'introduction' ) ) : ?>
				<?php spridisi_section_content( 'introduction' ); ?>
			<?php elseif ( is_page() && get_post_field( 'post_content', get_queried_object_id() ) ) : ?>
				<?php while ( have_posts() ) : the_post(); ?>
					<?php the_content(); wp_link_pages(); ?>
				<?php endwhile; ?>
			<?php else : ?>
				<p><?php esc_html_e( 'Sprīdīši. Vecsēlpils • Sēlija. Vieta svinībām, nakšņošanai un atpūtai.', 'spridisi' ); ?></p>
			<?php endif; ?>
		</div></div>
	</div>
</section>