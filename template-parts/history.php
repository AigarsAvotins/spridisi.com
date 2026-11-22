<?php
$history_page    = spridisi_section_page( 'history' );
$history_entries = spridisi_content_children( $history_page ? $history_page->ID : 0 );
$timeline_items  = $history_entries ? spridisi_history_featured_entries( $history_entries ) : array( null, null, null );
$history_id      = $history_page ? $history_page->ID : absint( get_theme_mod( 'spridisi_history_page', 0 ) );
$source_history  = get_page_by_path( 'history', OBJECT, 'page' );
$history_source_url = $history_page ? get_post_meta( $history_page->ID, '_spridisi_history_source_url', true ) : '';
$history_url     = $history_page ? get_permalink( $history_page ) : ( $history_source_url ? $history_source_url : ( $source_history ? get_permalink( $source_history ) : ( $history_id ? get_permalink( $history_id ) : '' ) ) );
?>
<section id="stasts" class="history section-space" aria-labelledby="history-heading">
	<div class="container"><div class="history__intro" data-reveal><p class="eyebrow section-kicker"><span class="section-number">06 /</span><?php esc_html_e( 'VIETAS ATMIŅA', 'spridisi' ); ?></p><h2 id="history-heading"><?php echo esc_html( spridisi_section_title( 'history', __( 'Šai vietai ir stāsts.', 'spridisi' ) ) ); ?></h2><p class="history__subtitle"><?php echo esc_html( spridisi_section_summary( 'history', __( 'Vecsēlpils un Sprīdīši cauri gadsimtiem.', 'spridisi' ) ) ); ?></p><?php spridisi_section_content( 'history' ); ?></div>
		<?php if ( $history_entries ) : ?>
			<?php spridisi_history_render_timeline( $timeline_items, 'stasts-laiklinija', __( 'Izcelti vēstures fakti', 'spridisi' ) ); ?>
		<?php else : ?>
			<ol id="stasts-laiklinija" class="timeline" aria-label="<?php echo esc_attr__( 'Laika līnijas paraugs — vēsturiskais saturs tiks papildināts', 'spridisi' ); ?>" data-reveal><li><span class="timeline__marker" aria-hidden="true"></span><span class="eyebrow"><?php esc_html_e( 'PARAUGS / 01', 'spridisi' ); ?></span><h3><?php esc_html_e( 'Stāsta pietura', 'spridisi' ); ?></h3><p><?php esc_html_e( 'Vēsturiskais saturs tiks papildināts.', 'spridisi' ); ?></p></li></ol>
		<?php endif; ?>
		<div class="history__action"><a class="text-link" href="<?php echo esc_url( $history_url ? $history_url : '#stasts-laiklinija' ); ?>"><?php esc_html_e( 'Atklāt visu stāstu', 'spridisi' ); ?><span aria-hidden="true">↗</span></a></div>
	</div>
</section>