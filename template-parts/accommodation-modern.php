<?php
/**
 * Template Part: Accommodation Section - Modernizēta Versija
 * 
 * Faila nosaukums: template-parts/accommodation-modern.php
 * Novieto šo failu blakus accommodation.php un lietojiet izmantojot:
 * get_template_part( 'template-parts/accommodation', 'modern' );
 */

// Sekcijas dati
$section_title = spridisi_section_title( 'accommodation', __( 'Paliec pa nakti', 'spridisi' ) );
$section_eyebrow = spridisi_homepage_text( 'accommodation', 'eyebrow', __( 'MIERA PIETURA', 'spridisi' ) );
$section_location = spridisi_section_summary( 'accommodation', __( 'Sprīdīši · Vecsēlpils', 'spridisi' ) );
$section_content = spridisi_section_content( 'accommodation' );
$accommodation_image = spridisi_photo_data( 'accommodation' );

?>

<section id="naksno" class="accommodation section-space" aria-labelledby="accommodation-heading">
	<div class="container">
		<div class="accommodation-content">
			<!-- Left: Image -->
			<div class="accommodation__image photo-frame" data-aos="fade-right" data-aos-duration="1000">
				<?php spridisi_photo( 'accommodation' ); ?>
			</div>

			<!-- Right: Content -->
			<div class="accommodation-copy" data-aos="fade-left" data-aos-duration="1000">
				<!-- Section Header -->
				<p class="eyebrow section-kicker">
					<span class="section-number-badge" data-aos="zoom-in">03 /</span>
					<?php echo esc_html( $section_eyebrow ); ?>
				</p>

				<h2 id="accommodation-heading" class="section-title">
					<?php echo esc_html( $section_title ); ?>
				</h2>

				<p class="location-note">
					<?php echo esc_html( $section_location ); ?>
				</p>

				<!-- Main Content -->
				<?php if ( $section_content ) : ?>
					<div class="accommodation-description" data-aos="fade-up" data-aos-delay="200">
						<?php echo wp_kses_post( $section_content ); ?>
					</div>
				<?php endif; ?>

				<!-- Benefits List -->
				<div class="accommodation-benefits" data-aos="fade-up" data-aos-delay="300">
					<?php
					$benefits = array(
						array(
							'icon' => '🛏️',
							'text' => sprintf(
								__( 'Vietas <strong>līdz %s viesiem</strong> – Nesarežģītā nakšņošana', 'spridisi' ),
								'28'
							),
						),
						array(
							'icon' => '👨‍👩‍👧‍👦',
							'text' => __( 'Ideāli <strong>kāzām un jubilejai</strong> – atpūtas brīvs laiks pēc svinībām', 'spridisi' ),
						),
						array(
							'icon' => '🗺️',
							'text' => __( '<strong>Perfekti viesi no tālām vietām</strong> – bez steidzīgā pagrieziena mājās', 'spridisi' ),
						),
						array(
							'icon' => '🌅',
							'text' => __( '<strong>Rileksa rīts</strong> – sāc nākamo dienu bez spiediena', 'spridisi' ),
						),
					);

					foreach ( $benefits as $index => $benefit ) {
						$delay = 100 + ( $index * 50 );
						?>
						<div class="benefit" data-aos="fade-up" data-aos-delay="<?php echo esc_attr( $delay ); ?>">
							<span class="benefit-icon" role="img"><?php echo esc_html( $benefit['icon'] ); ?></span>
							<span class="benefit-text"><?php echo wp_kses_post( $benefit['text'] ); ?></span>
						</div>
						<?php
					}
					?>
				</div>

				<!-- CTA Buttons -->
				<div class="accommodation-actions" data-aos="fade-up" data-aos-delay="500">
					<?php spridisi_section_actions( 'accommodation' ); ?>
				</div>
			</div>
		</div>
	</div>
</section>

<?php
/* Optional: Pievienot CSS un JS loaders */
wp_enqueue_style( 'sections-modern', get_template_directory_uri() . '/assets/css/sections-modern.css', array(), '1.1' );
wp_enqueue_script( 'aos', 'https://unpkg.com/aos@next/dist/aos.umd.js', array(), '2.3.4', true );
wp_add_inline_script( 'aos', "
	document.addEventListener('DOMContentLoaded', function() {
		AOS.init({
			duration: 1000,
			offset: 100,
			easing: 'ease-in-out-back',
			once: false,
		});
	});
" );
?>
