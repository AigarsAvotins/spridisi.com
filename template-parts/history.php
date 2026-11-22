<?php
$history_page    = spridisi_section_page( 'history' );
$history_entries = spridisi_content_children( $history_page ? $history_page->ID : 0 );
$timeline_items  = $history_entries ? $history_entries : array( null, null, null );
$history_id      = $history_page ? $history_page->ID : absint( get_theme_mod( 'spridisi_history_page', 0 ) );
$source_history  = get_page_by_path( 'history', OBJECT, 'page' );
$history_url     = $source_history ? get_permalink( $source_history ) : ( $history_id ? get_permalink( $history_id ) : '' );
?>
<section id="stasts" class="history section-space" aria-labelledby="history-heading">
	<div class="container"><div class="history__intro" data-reveal><p class="eyebrow section-kicker"><span class="section-number">06 /</span><?php esc_html_e( 'VIETAS ATMIŅA', 'spridisi' ); ?></p><h2 id="history-heading"><?php echo esc_html( spridisi_section_title( 'history', __( 'Šai vietai ir stāsts.', 'spridisi' ) ) ); ?></h2><p class="history__subtitle"><?php echo esc_html( spridisi_section_summary( 'history', __( 'Vecsēlpils un Sprīdīši cauri gadsimtiem.', 'spridisi' ) ) ); ?></p><?php spridisi_section_content( 'history' ); ?></div>
		<ol id="stasts-laiklinija" class="timeline" aria-label="<?php echo esc_attr( $history_entries ? __( 'Vietas vēsture', 'spridisi' ) : __( 'Laika līnijas paraugs — vēsturiskais saturs tiks papildināts', 'spridisi' ) ); ?>" data-reveal>
			<?php /* Temporary SAMPLE markers are shown only until verified history child pages are published. */ ?>
			<?php foreach ( $timeline_items as $entry_index => $entry ) : ?>
				<li><span class="timeline__marker" aria-hidden="true"></span>
					<?php if ( ! $entry || trim( $entry->post_excerpt ) ) : ?><span class="eyebrow"><?php echo esc_html( $entry ? wp_strip_all_tags( $entry->post_excerpt ) : sprintf( __( 'PARAUGS / %s', 'spridisi' ), sprintf( '%02d', $entry_index + 1 ) ) ); ?></span><?php endif; ?>
					<h3><?php echo $entry ? esc_html( get_the_title( $entry ) ) : esc_html__( 'Stāsta pietura', 'spridisi' ); ?></h3>
					<?php if ( $entry ) : ?>
						<div class="timeline__description"><?php spridisi_page_content( $entry ); ?></div>
					<?php else : ?>
						<p><?php esc_html_e( 'Vēsturiskais saturs tiks papildināts.', 'spridisi' ); ?></p>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ol>
		<div class="history__action"><a class="text-link" href="<?php echo esc_url( $history_url ? $history_url : '#stasts-laiklinija' ); ?>"><?php esc_html_e( 'Atklāt visu stāstu', 'spridisi' ); ?><span aria-hidden="true">↗</span></a></div>
	</div>
</section>