<?php
/**
 * Native homepage block content and protected editing.
 *
 * @package Spridisi
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function spridisi_homepage_sections() {
	static $cache = array();
	$homepage_id = is_front_page() && is_page() ? get_queried_object_id() : absint( get_option( 'page_on_front' ) );
	if ( ! $homepage_id || post_password_required( $homepage_id ) ) {
		return array();
	}
	if ( ! array_key_exists( $homepage_id, $cache ) ) {
		$cache[ $homepage_id ] = array();
		$blocks = parse_blocks( get_post_field( 'post_content', $homepage_id ) );
		foreach ( $blocks as $block ) {
			if ( spridisi_block_has_class( $block, 'spridisi-homepage-content' ) ) {
				foreach ( $block['innerBlocks'] as $section ) {
					$classes = explode( ' ', $section['attrs']['className'] ?? '' );
					foreach ( $classes as $class ) {
						if ( str_starts_with( $class, 'spridisi-content-' ) ) {
							$cache[ $homepage_id ][ substr( $class, strlen( 'spridisi-content-' ) ) ] = $section;
						}
					}
				}
			}
		}
	}
	return $cache[ $homepage_id ];
}

function spridisi_block_has_class( $block, $class ) {
	return in_array( $class, explode( ' ', $block['attrs']['className'] ?? '' ), true );
}

function spridisi_homepage_group( $section ) {
	$sections = spridisi_homepage_sections();
	$path     = explode( '/', $section );
	$group    = $sections[ array_shift( $path ) ] ?? null;
	foreach ( $path as $item ) {
		$found = null;
		foreach ( $group['innerBlocks'] ?? array() as $child ) {
			if ( spridisi_block_has_class( $child, 'spridisi-item-' . $item ) ) {
				$found = $child;
				break;
			}
		}
		$group = $found;
	}
	return $group;
}

function spridisi_homepage_field( $section, $field ) {
	$group = spridisi_homepage_group( $section );
	if ( null === $group ) {
		return null;
	}
	foreach ( $group['innerBlocks'] as $block ) {
		if ( spridisi_block_has_class( $block, 'spridisi-field-' . $field ) ) {
			return $block;
		}
	}
	return null;
}

function spridisi_homepage_items( $section ) {
	$sections = spridisi_homepage_sections();
	$items    = array();
	foreach ( $sections[ $section ]['innerBlocks'] ?? array() as $block ) {
		foreach ( explode( ' ', $block['attrs']['className'] ?? '' ) as $class ) {
			if ( str_starts_with( $class, 'spridisi-item-' ) ) {
				$items[] = substr( $class, strlen( 'spridisi-item-' ) );
			}
		}
	}
	return $items;
}

function spridisi_homepage_text( $section, $field, $fallback = '' ) {
	$block = spridisi_homepage_field( $section, $field );
	return null !== $block ? trim( html_entity_decode( wp_strip_all_tags( render_block( $block ) ), ENT_QUOTES, get_bloginfo( 'charset' ) ) ) : $fallback;
}

function spridisi_homepage_link( $section, $field, $fallback = '' ) {
	$block = spridisi_homepage_field( $section, $field );
	if ( null === $block ) {
		return $fallback;
	}
	$processor = new WP_HTML_Tag_Processor( render_block( $block ) );
	return $processor->next_tag( 'A' ) ? esc_url_raw( $processor->get_attribute( 'href' ) ?? '' ) : '';
}

function spridisi_homepage_image( $section, $field = 'image' ) {
	$block = spridisi_homepage_field( $section, $field );
	if ( null === $block ) {
		return null;
	}
	$processor = new WP_HTML_Tag_Processor( render_block( $block ) );
	if ( ! $processor->next_tag( 'IMG' ) ) {
		return array();
	}
	$url  = esc_url_raw( $processor->get_attribute( 'src' ) ?? '' );
	$path = wp_parse_url( $url, PHP_URL_PATH );
	foreach ( array( 'hero-spridisi.jpg', 'venue-spridisi.jpg', 'sauna-spridisi.jpg' ) as $filename ) {
		$asset_path = '/assets/images/' . $filename;
		if ( is_string( $path ) && str_ends_with( $path, $asset_path ) && ! absint( $block['attrs']['id'] ?? 0 ) ) {
			$url = get_theme_file_uri( $asset_path );
			break;
		}
	}
	return array(
		'id'     => absint( $block['attrs']['id'] ?? 0 ),
		'url'    => esc_url_raw( $url ),
		'alt'    => sanitize_text_field( $processor->get_attribute( 'alt' ) ?? '' ),
		'width'  => absint( $processor->get_attribute( 'width' ) ?? 0 ),
		'height' => absint( $processor->get_attribute( 'height' ) ?? 0 ),
	);
}

function spridisi_homepage_body( $section, $field = 'body' ) {
	echo spridisi_homepage_body_markup( $section, $field );
}

function spridisi_homepage_body_markup( $section, $field = 'body' ) {
	$block = spridisi_homepage_field( $section, $field );
	$blocks = array();
	if ( 'body' === $field && 'hero' !== $section ) {
		$group = spridisi_homepage_group( $section );
		foreach ( $group['innerBlocks'] ?? array() as $child ) {
			if ( spridisi_block_has_class( $child, 'spridisi-field-body' ) ) {
				$blocks = array_merge( $blocks, 'core/group' === $child['blockName'] ? $child['innerBlocks'] : array( $child ) );
				continue;
			}
			$classes = $child['attrs']['className'] ?? '';
			$is_field = preg_match( '/(?:^|\s)spridisi-(?:field|item)-/', $classes );
			if ( ( ! $is_field || spridisi_block_has_class( $child, 'spridisi-field-text' ) ) && in_array( $child['blockName'], array( 'core/paragraph', 'core/group', 'core/list', 'core/quote', 'core/verse', 'core/freeform' ), true ) ) {
				$blocks[] = $child;
			}
		}
	} elseif ( null !== $block ) {
		$blocks = 'core/group' === $block['blockName'] ? $block['innerBlocks'] : array( $block );
	}
	$html = '';
	foreach ( $blocks as $body_block ) {
		$html .= render_block( spridisi_homepage_public_body_block( $body_block ) );
	}
	return '' !== trim( wp_strip_all_tags( $html ) ) ? wp_kses_post( $html ) : '';
}

function spridisi_homepage_public_body_block( $block ) {
	$text = trim( html_entity_decode( wp_strip_all_tags( $block['innerHTML'] ?? '' ), ENT_QUOTES, get_bloginfo( 'charset' ) ) );
	$instructions = array(
		'Add verified content here. Empty text is not displayed on the homepage.',
		__( 'Add verified content here. Empty text is not displayed on the homepage.', 'spridisi' ),
	);
	$is_empty_helper = '' === $text && ( spridisi_block_has_class( $block, 'spridisi-field-text' ) || isset( $block['attrs']['placeholder'] ) );
	if ( empty( $block['innerBlocks'] ) && ( $is_empty_helper || in_array( $text, $instructions, true ) ) ) {
		return array( 'blockName' => null, 'attrs' => array(), 'innerBlocks' => array(), 'innerHTML' => '', 'innerContent' => array( '' ) );
	}
	foreach ( $block['innerBlocks'] ?? array() as $index => $child ) {
		$block['innerBlocks'][ $index ] = spridisi_homepage_public_body_block( $child );
	}
	return $block;
}

function spridisi_homepage_photo( $section, $class = 'editorial-photo', $size = 'large', $decorative = false, $field = 'image' ) {
	$image = spridisi_homepage_image( $section, $field );
	if ( empty( $image['url'] ) ) {
		return false;
	}
	$attributes = array( 'class' => $class, 'alt' => $decorative ? '' : $image['alt'], 'loading' => $decorative ? 'eager' : 'lazy', 'decoding' => 'async' );
	if ( $decorative ) {
		$attributes['fetchpriority'] = 'high';
	}
	if ( $image['id'] ) {
		$html = wp_get_attachment_image( $image['id'], $size, false, $attributes );
		if ( $html ) {
			echo $html;
			return true;
		}
	}
	$dimensions = array( 'hero' => array( 2048, 1536 ), 'venue' => array( 736, 1000 ), 'sauna' => array( 1536, 2048 ) );
	if ( isset( $dimensions[ $section ] ) && $image['url'] === get_theme_file_uri( '/assets/images/' . $section . '-spridisi.jpg' ) ) {
		list( $image['width'], $image['height'] ) = $dimensions[ $section ];
	}
	printf( '<img src="%s"', esc_url( $image['url'] ) );
	foreach ( $attributes as $attribute => $value ) {
		printf( ' %s="%s"', esc_attr( $attribute ), esc_attr( $value ) );
	}
	if ( $image['width'] && $image['height'] ) {
		printf( ' width="%d" height="%d"', $image['width'], $image['height'] );
	}
	echo '>';
	return true;
}

function spridisi_homepage_editor_settings( $settings, $context ) {
	if ( ! empty( $context->post ) && 'page' === $context->post->post_type && str_contains( $context->post->post_content, 'spridisi-homepage-content' ) ) {
		$settings['canLockBlocks']      = false;
		$settings['codeEditingEnabled'] = false;
		$settings['templateLock']       = 'all';
	}
	return $settings;
}
add_filter( 'block_editor_settings_all', 'spridisi_homepage_editor_settings', 10, 2 );

function spridisi_seed_block( $name, $attributes, $html = '' ) {
	return array( 'blockName' => $name, 'attrs' => $attributes, 'innerBlocks' => array(), 'innerHTML' => $html, 'innerContent' => array( $html ) );
}

function spridisi_seed_text( $field, $text, $heading = false ) {
	$class = 'spridisi-field-' . $field;
	$label = $heading ? __( 'Heading', 'spridisi' ) : __( 'Text', 'spridisi' );
	$tag   = $heading ? 'h2' : 'p';
	$attributes = array( 'className' => $class, 'metadata' => array( 'name' => $label ) );
	if ( $heading ) {
		$class = 'wp-block-heading ' . $class;
	} elseif ( '' === $text ) {
		$attributes['placeholder'] = __( 'Add verified content here. Empty text is not displayed on the homepage.', 'spridisi' );
	}
	return spridisi_seed_block( $heading ? 'core/heading' : 'core/paragraph', $attributes, '<' . $tag . ' class="' . $class . '">' . esc_html( $text ) . '</' . $tag . '>' );
}

function spridisi_seed_group( $class, $label, $children ) {
	$block = spridisi_seed_block( 'core/group', array( 'className' => $class, 'metadata' => array( 'name' => $label ), 'templateLock' => 'contentOnly', 'lock' => array( 'move' => true, 'remove' => true ) ) );
	$block['innerBlocks']  = $children;
	$block['innerHTML']    = '<div class="wp-block-group ' . $class . '"></div>';
	$block['innerContent'] = array_merge( array( '<div class="wp-block-group ' . $class . '">' ), array_fill( 0, count( $children ), null ), array( '</div>' ) );
	return $block;
}

function spridisi_seed_button( $field, $label, $url ) {
	$button = spridisi_seed_block( 'core/button', array(), '<div class="wp-block-button"><a class="wp-block-button__link wp-element-button"' . ( $url ? ' href="' . esc_url( $url ) . '"' : '' ) . '>' . esc_html( $label ) . '</a></div>' );
	$block  = spridisi_seed_block( 'core/buttons', array( 'className' => 'spridisi-field-' . $field, 'metadata' => array( 'name' => __( 'Button text and destination', 'spridisi' ) ) ) );
	$block['innerBlocks']  = array( $button );
	$block['innerHTML']    = '<div class="wp-block-buttons spridisi-field-' . $field . '"></div>';
	$block['innerContent'] = array( '<div class="wp-block-buttons spridisi-field-' . $field . '">', null, '</div>' );
	return $block;
}

function spridisi_seed_image( $section, $field = 'image' ) {
	$page = 'image' === $field ? spridisi_section_page( $section ) : null;
	$image_id = $page ? get_post_thumbnail_id( $page ) : 0;
	$image_id = $image_id ? $image_id : ( 'image' === $field ? absint( get_theme_mod( 'spridisi_image_' . $section, 0 ) ) : 0 );
	$attributes = array( 'className' => 'spridisi-field-' . $field, 'metadata' => array( 'name' => __( 'Photograph', 'spridisi' ) ), 'sizeSlug' => 'full', 'linkDestination' => 'none' );
	$alt = '';
	$url = $image_id ? wp_get_attachment_image_url( $image_id, 'full' ) : '';
	if ( $url ) {
		$attributes['id'] = $image_id;
		$alt = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
	} elseif ( 'image' === $field && in_array( $section, array( 'hero', 'venue', 'sauna' ), true ) ) {
		$url = get_theme_file_uri( '/assets/images/' . $section . '-spridisi.jpg' );
		$alts = array( 'hero' => '', 'venue' => 'Sprīdīšu svinību zāle ar klātiem galdiem un koka sijām.', 'sauna' => 'Sprīdīšu koka pirts ar pirts slotām uz āra galda.' );
		$alt = $alts[ $section ];
	}
	if ( ! $url ) {
		return spridisi_seed_block( 'core/image', $attributes, '<figure class="wp-block-image size-full spridisi-field-' . esc_attr( $field ) . '"><img alt=""/></figure>' );
	}
	$attributes['url'] = $url;
	$attributes['alt'] = $alt;
	$image_class = $image_id ? ' class="wp-image-' . $image_id . '"' : '';
	return spridisi_seed_block( 'core/image', $attributes, '<figure class="wp-block-image size-full spridisi-field-' . esc_attr( $field ) . '"><img src="' . esc_url( $url ) . '" alt="' . esc_attr( $alt ) . '"' . $image_class . '/></figure>' );
}

function spridisi_seed_body( $section, $fallback = '' ) {
	$page = spridisi_section_page( $section );
	$children = $page && trim( $page->post_content ) ? parse_blocks( $page->post_content ) : array( spridisi_seed_text( 'text', $fallback ) );
	return spridisi_seed_group( 'spridisi-field-body', __( 'Description', 'spridisi' ), $children );
}

function spridisi_homepage_starter_content() {
	$sections = array();
	$sections[] = spridisi_seed_group( 'spridisi-content-hero', __( 'Hero', 'spridisi' ), array(
		spridisi_seed_text( 'eyebrow', spridisi_section_summary( 'contact', 'VECSĒLPILS • SĒLIJA' ) ),
		spridisi_seed_text( 'title', spridisi_section_title( 'hero', 'Te ir tā laimes zeme.' ), true ),
		spridisi_seed_text( 'text', spridisi_section_summary( 'hero', 'Vieta Vecsēlpilī, kur svinēt, atpūsties un piedzīvot Sēliju.' ) ),
		spridisi_seed_button( 'primary', spridisi_reservation_cta_label(), '#kontakti' ),
		spridisi_seed_button( 'secondary', 'Iepazīt Sprīdīšus', '#ievads' ),
		spridisi_seed_image( 'hero' ),
	) );
	$sections[] = spridisi_seed_group( 'spridisi-content-introduction', __( 'Introduction', 'spridisi' ), array(
		spridisi_seed_text( 'eyebrow', spridisi_section_summary( 'introduction', 'TEPAT, SĒLIJĀ' ) ),
		spridisi_seed_text( 'title', spridisi_section_title( 'introduction', 'Vieta, kur palikt ilgāk.' ), true ),
		spridisi_seed_body( 'introduction', 'Sprīdīši. Vecsēlpils • Sēlija. Vieta svinībām, nakšņošanai un atpūtai.' ),
	) );
	$sections[] = spridisi_seed_group( 'spridisi-content-venue', __( 'Celebrations and halls', 'spridisi' ), array(
		spridisi_seed_text( 'eyebrow', spridisi_section_summary( 'venue', 'KOPĀ BŪŠANA' ) ),
		spridisi_seed_text( 'title', spridisi_section_title( 'venue', 'Svini Sprīdīšos' ), true ),
		spridisi_seed_body( 'venue' ),
		spridisi_seed_text( 'large-hall-title', 'Lielā zāle', true ),
		spridisi_seed_text( 'large-hall', '' ),
		spridisi_seed_image( 'venue', 'large-hall-image' ),
		spridisi_seed_text( 'small-hall-title', 'Mazā zāle', true ),
		spridisi_seed_text( 'small-hall', '' ),
		spridisi_seed_image( 'venue', 'small-hall-image' ),
		spridisi_seed_text( 'outdoor-title', 'Āra terase un teritorija', true ),
		spridisi_seed_text( 'outdoor', '' ),
		spridisi_seed_image( 'venue' ),
	) );
	foreach ( array( 'sauna' => array( __( 'Sauna', 'spridisi' ), 'LAIKS ATPŪTAI', 'Melnā pirts', 'Par pirti' ) ) as $key => $defaults ) {
		$sections[] = spridisi_seed_group( 'spridisi-content-' . $key, $defaults[0], array(
			spridisi_seed_text( 'eyebrow', 'sauna' === $key ? spridisi_section_summary( $key, $defaults[1] ) : $defaults[1] ),
			spridisi_seed_text( 'title', spridisi_section_title( $key, $defaults[2] ), true ),
			spridisi_seed_text( 'summary', 'accommodation' === $key ? spridisi_section_summary( $key, 'Sprīdīši · Vecsēlpils' ) : '' ),
			spridisi_seed_body( $key ),
			spridisi_seed_button( 'primary', $defaults[3], '#kontakti' ),
			spridisi_seed_image( $key ),
		) );
	}
	$experiences = array( spridisi_seed_text( 'eyebrow', 'AIZ IKDIENAS' ), spridisi_seed_text( 'title', spridisi_section_title( 'experiences', 'Piedzīvo Sēliju' ), true ), spridisi_seed_text( 'summary', spridisi_section_summary( 'experiences', 'Daugava. Daba. Sēlija.' ) ), spridisi_seed_body( 'experiences' ) );
	foreach ( array( 'daugava' => 'Daugava', 'sup' => 'SUP', 'selija' => 'Iepazīt Sēliju' ) as $key => $title ) {
		$experiences[] = spridisi_seed_group( 'spridisi-item-' . $key, $title, array( spridisi_seed_text( 'title', $title, true ), spridisi_seed_body( $key ), spridisi_seed_button( 'primary', 'Uzzināt vairāk', '#kontakti' ), spridisi_seed_image( $key ) ) );
	}
	$sections[] = spridisi_seed_group( 'spridisi-content-experiences', __( 'Experiences and SUP', 'spridisi' ), $experiences );
	$gallery = array( spridisi_seed_text( 'eyebrow', spridisi_section_summary( 'gallery', 'SPRĪDĪŠU MIRKĻI' ) ), spridisi_seed_text( 'title', spridisi_section_title( 'gallery', 'Sajust šo vietu.' ), true ), spridisi_seed_body( 'gallery' ) );
	foreach ( array( 1, 2, 3 ) as $number ) {
		$gallery[] = spridisi_seed_group( 'spridisi-item-photo-' . $number, sprintf( __( 'Gallery photograph %d', 'spridisi' ), $number ), array( spridisi_seed_image( 'gallery_' . $number ) ) );
	}
	$sections[] = spridisi_seed_group( 'spridisi-content-gallery', __( 'Gallery', 'spridisi' ), $gallery );
	$sections[] = spridisi_seed_group( 'spridisi-content-contact', __( 'Booking and contact details', 'spridisi' ), array(
		spridisi_seed_text( 'eyebrow', 'TE IR TĀ LAIMES ZEME.' ),
		spridisi_seed_text( 'title', spridisi_section_title( 'contact', 'Tiekamies Sprīdīšos.' ), true ),
		spridisi_seed_text( 'location', spridisi_section_summary( 'contact', 'Vecsēlpils • Sēlija' ) ),
		spridisi_seed_body( 'contact' ),
		spridisi_seed_button( 'primary', spridisi_reservation_cta_label(), spridisi_booking_url() ),
	) );
	return serialize_blocks( array( spridisi_seed_group( 'spridisi-homepage-content', __( 'Sprīdīši homepage content', 'spridisi' ), $sections ) ) );
}

function spridisi_homepage_setup_notice() {
	if ( ! current_user_can( 'manage_options' ) || ! current_user_can( 'publish_pages' ) || spridisi_homepage_sections() ) {
		return;
	}
	echo '<div class="notice notice-info"><p>' . esc_html__( 'Sprīdīši: create a protected, editable homepage using native WordPress blocks. Existing pages are kept; a new homepage is published if the current one is not empty.', 'spridisi' ) . '</p><form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
	wp_nonce_field( 'spridisi_setup_homepage' );
	echo '<input type="hidden" name="action" value="spridisi_setup_homepage">';
	submit_button( __( 'Set up editable homepage', 'spridisi' ), 'secondary', 'submit', false );
	echo '</form><p></p></div>';
}
add_action( 'admin_notices', 'spridisi_homepage_setup_notice' );

function spridisi_setup_editable_homepage() {
	if ( ! current_user_can( 'manage_options' ) || ! current_user_can( 'publish_pages' ) ) {
		wp_die( esc_html__( 'You cannot set up this homepage.', 'spridisi' ) );
	}
	check_admin_referer( 'spridisi_setup_homepage' );
	$page_id = absint( get_option( 'page_on_front' ) );
	if ( ! spridisi_homepage_sections() ) {
		$content = spridisi_homepage_starter_content();
		$current = $page_id ? get_post( $page_id ) : null;
		$data = array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => __( 'Sākumlapa', 'spridisi' ), 'post_content' => $content );
		if ( $current && current_user_can( 'edit_post', $page_id ) && '' === trim( $current->post_content ) ) {
			$data['ID'] = $page_id;
			$data['post_title'] = $current->post_title;
		}
		$page_id = wp_insert_post( wp_slash( $data ), true );
		if ( is_wp_error( $page_id ) ) {
			wp_die( esc_html( $page_id->get_error_message() ) );
		}
		update_option( 'page_on_front', $page_id );
		update_option( 'show_on_front', 'page' );
	}
	wp_safe_redirect( get_edit_post_link( $page_id, 'raw' ) );
	exit;
}
add_action( 'admin_post_spridisi_setup_homepage', 'spridisi_setup_editable_homepage' );

function spridisi_homepage_outdoor_notice() {
	if ( ! current_user_can( 'manage_options' ) || ! current_user_can( 'publish_pages' ) ) {
		return;
	}
	$homepage_id = absint( get_option( 'page_on_front' ) );
	$sections = spridisi_homepage_sections();
	if ( ! $homepage_id || ! isset( $sections['venue'] ) || ( spridisi_homepage_field( 'venue', 'outdoor-title' ) && spridisi_homepage_field( 'venue', 'outdoor' ) ) ) {
		return;
	}
	echo '<div class="notice notice-info"><p>' . esc_html__( 'Pievieno rediģējamai sākumlapai sadaļu “Āra terase un teritorija” pēc mazās zāles. Esošais sākumlapas saturs tiks saglabāts.', 'spridisi' ) . '</p><form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
	wp_nonce_field( 'spridisi_homepage_add_outdoor' );
	echo '<input type="hidden" name="action" value="spridisi_homepage_add_outdoor">';
	submit_button( __( 'Pievienot terases sadaļu', 'spridisi' ), 'secondary', 'submit', false );
	echo '</form></div>';
}
add_action( 'admin_notices', 'spridisi_homepage_outdoor_notice' );

function spridisi_homepage_add_outdoor() {
	if ( ! current_user_can( 'manage_options' ) || ! current_user_can( 'publish_pages' ) ) {
		wp_die( esc_html__( 'Tev nav atļauts atjaunināt šo sākumlapu.', 'spridisi' ) );
	}
	check_admin_referer( 'spridisi_homepage_add_outdoor' );
	$homepage_id = absint( get_option( 'page_on_front' ) );
	if ( ! $homepage_id || ! current_user_can( 'edit_post', $homepage_id ) ) {
		wp_die( esc_html__( 'Tev nav atļauts rediģēt šo sākumlapu.', 'spridisi' ) );
	}
	$homepage = get_post( $homepage_id );
	if ( ! $homepage || 'page' !== $homepage->post_type ) {
		wp_die( esc_html__( 'Sākumlapa nav atrasta.', 'spridisi' ) );
	}
	$blocks = parse_blocks( $homepage->post_content );
	$updated = false;
	foreach ( $blocks as $root_index => $root ) {
		if ( ! spridisi_block_has_class( $root, 'spridisi-homepage-content' ) ) {
			continue;
		}
		foreach ( $root['innerBlocks'] as $section_index => $section ) {
			if ( ! spridisi_block_has_class( $section, 'spridisi-content-venue' ) ) {
				continue;
			}
			$fields = array();
			$insert_index = count( $section['innerBlocks'] );
			foreach ( $section['innerBlocks'] as $index => $block ) {
				foreach ( array( 'outdoor-title', 'outdoor' ) as $field ) {
					if ( spridisi_block_has_class( $block, 'spridisi-field-' . $field ) ) {
						$fields[ $field ] = true;
					}
				}
				if ( spridisi_block_has_class( $block, 'spridisi-field-small-hall' ) ) {
					$insert_index = $index + 1;
				}
			}
			$new_blocks = array();
			if ( empty( $fields['outdoor-title'] ) ) {
				$new_blocks[] = spridisi_seed_text( 'outdoor-title', 'Āra terase un teritorija', true );
			}
			if ( empty( $fields['outdoor'] ) ) {
				$new_blocks[] = spridisi_seed_text( 'outdoor', '' );
			}
			if ( ! $new_blocks ) {
				$updated = true;
				break 2;
			}
			$inner_content_index = count( $section['innerContent'] ) - 1;
			$child_index = 0;
			foreach ( $section['innerContent'] as $content_index => $content ) {
				if ( null !== $content ) {
					continue;
				}
				if ( $child_index === $insert_index ) {
					$inner_content_index = $content_index;
					break;
				}
				++$child_index;
			}
			array_splice( $section['innerBlocks'], $insert_index, 0, $new_blocks );
			array_splice( $section['innerContent'], $inner_content_index, 0, array_fill( 0, count( $new_blocks ), null ) );
			$blocks[ $root_index ]['innerBlocks'][ $section_index ] = $section;
			$updated = true;
			break 2;
		}
	}
	if ( ! $updated ) {
		wp_die( esc_html__( 'Sākumlapā nav atrasta svinību sadaļa. Saturs netika mainīts.', 'spridisi' ) );
	}
	$result = wp_update_post( wp_slash( array( 'ID' => $homepage_id, 'post_content' => serialize_blocks( $blocks ) ) ), true );
	if ( is_wp_error( $result ) ) {
		wp_die( esc_html( $result->get_error_message() ) );
	}
	wp_safe_redirect( get_edit_post_link( $homepage_id, 'raw' ) );
	exit;
}
add_action( 'admin_post_spridisi_homepage_add_outdoor', 'spridisi_homepage_add_outdoor' );

function spridisi_homepage_hall_images_notice() {
	if ( ! current_user_can( 'manage_options' ) || ! current_user_can( 'publish_pages' ) ) {
		return;
	}
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	$homepage_id = absint( get_option( 'page_on_front' ) );
	$is_homepage_editor = $screen && 'page' === $screen->id && isset( $_GET['post'] ) && $homepage_id === absint( $_GET['post'] );
	if ( ! $screen || ( 'edit-page' !== $screen->id && ! $is_homepage_editor ) ) {
		return;
	}
	$sections = spridisi_homepage_sections();
	if ( ! isset( $sections['venue'] ) || ( spridisi_homepage_field( 'venue', 'large-hall-image' ) && spridisi_homepage_field( 'venue', 'small-hall-image' ) ) ) {
		return;
	}
	echo '<div class="notice notice-info"><p>' . esc_html__( 'Pievieno sākumlapas redaktorā atsevišķus attēlu blokus Lielajai un Mazajai zālei. Esošie zāļu apraksti netiks mainīti.', 'spridisi' ) . '</p><form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
	wp_nonce_field( 'spridisi_homepage_add_hall_images' );
	echo '<input type="hidden" name="action" value="spridisi_homepage_add_hall_images">';
	submit_button( __( 'Pievienot zāļu attēlu blokus', 'spridisi' ), 'secondary', 'submit', false );
	echo '</form></div>';
}
add_action( 'admin_notices', 'spridisi_homepage_hall_images_notice' );

function spridisi_homepage_add_hall_images() {
	if ( ! current_user_can( 'manage_options' ) || ! current_user_can( 'publish_pages' ) ) {
		wp_die( esc_html__( 'Tev nav atļauts atjaunināt šo sākumlapu.', 'spridisi' ) );
	}
	check_admin_referer( 'spridisi_homepage_add_hall_images' );
	$homepage_id = absint( get_option( 'page_on_front' ) );
	if ( ! $homepage_id || ! current_user_can( 'edit_post', $homepage_id ) ) {
		wp_die( esc_html__( 'Sākumlapa nav atrasta vai to nevar rediģēt.', 'spridisi' ) );
	}
	$blocks = parse_blocks( get_post_field( 'post_content', $homepage_id ) );
	$updated = false;
	foreach ( $blocks as $root_index => $root ) {
		if ( ! spridisi_block_has_class( $root, 'spridisi-homepage-content' ) ) {
			continue;
		}
		foreach ( $root['innerBlocks'] as $section_index => $section ) {
			if ( ! spridisi_block_has_class( $section, 'spridisi-content-venue' ) ) {
				continue;
			}
			foreach ( array( 'large-hall', 'small-hall' ) as $hall ) {
				$image_field = $hall . '-image';
				$has_image = false;
				$insert_index = count( $section['innerBlocks'] );
				foreach ( $section['innerBlocks'] as $index => $block ) {
					if ( spridisi_block_has_class( $block, 'spridisi-field-' . $image_field ) ) {
						$has_image = true;
					}
					if ( spridisi_block_has_class( $block, 'spridisi-field-' . $hall ) ) {
						$insert_index = $index + 1;
					}
				}
				if ( $has_image ) {
					continue;
				}
				$new_block = spridisi_seed_image( 'venue', $image_field );
				$inner_content_index = count( $section['innerContent'] ) - 1;
				$child_index = 0;
				foreach ( $section['innerContent'] as $content_index => $content ) {
					if ( null !== $content ) {
						continue;
					}
					if ( $child_index === $insert_index ) {
						$inner_content_index = $content_index;
						break;
					}
					++$child_index;
				}
				array_splice( $section['innerBlocks'], $insert_index, 0, array( $new_block ) );
				array_splice( $section['innerContent'], $inner_content_index, 0, array( null ) );
				$updated = true;
			}
			$blocks[ $root_index ]['innerBlocks'][ $section_index ] = $section;
			break 2;
		}
	}
	if ( $updated ) {
		$result = wp_update_post( wp_slash( array( 'ID' => $homepage_id, 'post_content' => serialize_blocks( $blocks ) ) ), true );
		if ( is_wp_error( $result ) ) {
			wp_die( esc_html( $result->get_error_message() ) );
		}
	}
	wp_safe_redirect( get_edit_post_link( $homepage_id, 'raw' ) );
	exit;
}
add_action( 'admin_post_spridisi_homepage_add_hall_images', 'spridisi_homepage_add_hall_images' );