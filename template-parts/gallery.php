<?php
$gallery_page   = spridisi_section_page( 'gallery' );
$gallery_images = array_slice( spridisi_content_children( $gallery_page ? $gallery_page->ID : 0 ), 0, 3 );
$gallery_items  = $gallery_images ? $gallery_images : array( null, null, null );
?>
<section id="galerija" class="gallery section-space" aria-labelledby="gallery-heading">
	<div class="container"><div class="section-heading" data-reveal><div><p class="eyebrow section-kicker"><span class="section-number">07 /</span><?php echo esc_html( spridisi_section_summary( 'gallery', __( 'SPRĪDĪŠU MIRKĻI', 'spridisi' ) ) ); ?></p><h2 id="gallery-heading"><?php echo esc_html( spridisi_section_title( 'gallery', __( 'Sajust šo vietu.', 'spridisi' ) ) ); ?></h2></div></div><?php spridisi_section_content( 'gallery' ); ?><div class="gallery-grid">
		<?php foreach ( $gallery_items as $image_index => $image_page ) : ?>
			<div class="gallery__image photo-frame" data-reveal><?php spridisi_photo( 'gallery_' . ( $image_index + 1 ), 'large', $image_page ); ?></div>
		<?php endforeach; ?>
	</div></div>
</section>