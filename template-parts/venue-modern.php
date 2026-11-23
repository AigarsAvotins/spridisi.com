<?php
/**
 * Template Part: Venue Section - Modernizēta Kartītu Versija
 * 
 * Faila nosaukums: template-parts/venue-modern.php
 * Novieto šo failu blakus venue.php un lietojiet izmantojot:
 * get_template_part( 'template-parts/venue', 'modern' );
 */

// Sekcijas dati
$section_title = spridisi_section_title( 'venue', __( 'Svini Sprīdīšos', 'spridisi' ) );
$section_summary = spridisi_section_summary( 'venue', __( 'KOPĀ BŪŠANA', 'spridisi' ) );
$section_content = spridisi_section_content( 'venue' );

?>

<section id="svini" class="venue section-space" aria-labelledby="venue-heading">
	<div class="container">
		<!-- Section Header -->
		<div class="section-heading" data-aos="fade-up">
			<div>
				<p class="eyebrow section-kicker">
					<span class="section-number-badge" data-aos="zoom-in">02 /</span>
					<?php echo esc_html( $section_summary ); ?>
				</p>
				<h2 id="venue-heading" class="section-title">
					<?php echo esc_html( $section_title ); ?>
				</h2>
			</div>
			<?php spridisi_section_actions( 'venue' ); ?>
		</div>

		<!-- Section Description -->
		<?php if ( $section_content ) : ?>
			<div class="section-intro" data-aos="fade-up" data-aos-delay="100">
				<?php echo wp_kses_post( $section_content ); ?>
			</div>
		<?php endif; ?>

		<!-- Venues Grid -->
		<div class="venue-cards-container">
			<div class="venue-cards">
				<?php
				$halls = array(
					array(
						'id'       => 'large-hall',
						'icon'     => '🏛️',
						'name'     => __( 'Lielā Zāle', 'spridisi' ),
						'details'  => array(
							array(
								'icon' => '📐',
								'text' => sprintf(
									__( '<strong>%s m²</strong> plaša telpa', 'spridisi' ),
									'220'
								),
							),
							array(
								'icon' => '👥',
								'text' => sprintf(
									__( 'Vietas līdz <strong>%s viesiem</strong>', 'spridisi' ),
									'80'
								),
							),
							array(
								'icon' => '🍽️',
								'text' => __( 'Galdi un krēsli <strong>jau pieejami</strong>', 'spridisi' ),
							),
						),
						'cta_text' => __( 'Par svinībām', 'spridisi' ),
						'delay'    => '200',
					),
					array(
						'id'       => 'small-hall',
						'icon'     => '🎪',
						'name'     => __( 'Mazā Zāle', 'spridisi' ),
						'details'  => array(
							array(
								'icon' => '📐',
								'text' => sprintf(
									__( '<strong>%s m²</strong> telpa', 'spridisi' ),
									'50'
								),
							),
							array(
								'icon' => '👥',
								'text' => sprintf(
									__( '<strong>%s-%s cilvēki</strong>', 'spridisi' ),
									'10',
									'15'
								),
							),
							array(
								'icon' => '👨‍🍳',
								'text' => __( '<strong>Pilnībā aprīkota virtuve</strong>', 'spridisi' ),
							),
						),
						'cta_text' => __( 'Par svinībām', 'spridisi' ),
						'delay'    => '400',
					),
				);

				foreach ( $halls as $hall ) {
					$hall_full_content = spridisi_homepage_body_markup( 'venue', $hall['id'] );
					$hall_image        = spridisi_homepage_image( 'venue', $hall['id'] . '-image' );
					?>
					<div class="venue-card" data-aos="zoom-in" data-aos-delay="<?php echo esc_attr( $hall['delay'] ); ?>">
						<span class="venue-card__icon" role="img"><?php echo esc_html( $hall['icon'] ); ?></span>
						<h3 class="venue-card__title"><?php echo esc_html( $hall['name'] ); ?></h3>
						<div class="venue-card__details">
							<?php foreach ( $hall['details'] as $detail ) : ?>
								<div class="detail">
									<span class="detail-icon" role="img"><?php echo esc_html( $detail['icon'] ); ?></span>
									<span class="detail-text"><?php echo wp_kses_post( $detail['text'] ); ?></span>
								</div>
							<?php endforeach; ?>
						</div>

						<?php if ( $hall_full_content ) : ?>
							<div class="venue-card__description">
								<?php echo wp_kses_post( $hall_full_content ); ?>
							</div>
						<?php endif; ?>

						<a href="#kontakti" class="venue-card__cta">
							<?php echo esc_html( $hall['cta_text'] ); ?>
						</a>
					</div>
					<?php
				}
				?>

				<!-- Outdoor Section -->
				<div class="venue-card venue-card--full" data-aos="zoom-in" data-aos-delay="600">
					<span class="venue-card__icon" role="img">🌳</span>
					<h3 class="venue-card__title">
						<?php echo esc_html( spridisi_homepage_text( 'venue', 'outdoor-title', __( 'Āra Terase un Teritorija', 'spridisi' ) ) ); ?>
					</h3>
					<div class="venue-card__description">
						<?php spridisi_homepage_body( 'venue', 'outdoor' ); ?>
					</div>
					<a href="#kontakti" class="venue-card__cta">
						<?php echo esc_html( __( 'Uzzināt detaļas', 'spridisi' ) ); ?>
					</a>
				</div>
			</div>
		</div>

		<!-- Featured Image -->
		<?php $venue_image = spridisi_homepage_image( 'venue', 'full' ); ?>
		<?php if ( ! empty( $venue_image['url'] ) ) : ?>
			<div class="venue__image photo-frame" data-aos="fade-up" data-aos-delay="800">
				<?php spridisi_photo( 'venue', 'full' ); ?>
				<span class="image-index" aria-hidden="true">
					<?php esc_html_e( 'SPRĪDĪŠI / 01', 'spridisi' ); ?>
				</span>
			</div>
		<?php endif; ?>
	</div>
</section>

<?php
/* Optional: Pievienot CSS un JS loaders */
wp_enqueue_style( 'sections-modern', get_template_directory_uri() . '/assets/css/sections-modern.css' );
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
