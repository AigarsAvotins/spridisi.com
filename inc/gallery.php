<?php
/**
 * Native Gallery page and homepage action links.
 *
 * @package Spridisi
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function spridisi_gallery_page() {
	$pages = new WP_Query( array( 'post_type' => 'page', 'post_status' => 'publish', 'posts_per_page' => 1, 'no_found_rows' => true, 'meta_key' => '_wp_page_template', 'meta_value' => 'page-templates/gallery.php', 'suppress_filters' => false ) );
	if ( $pages->posts ) {
		return $pages->posts[0];
	}
	$page = get_page_by_path( 'galerija', OBJECT, 'page' );
	return $page && 'publish' === $page->post_status ? $page : null;
}

function spridisi_gallery_url( $anchor = '' ) {
	$page = spridisi_gallery_page();
	return $page ? get_permalink( $page ) . ( $anchor ? '#' . sanitize_title( $anchor ) : '' ) : '';
}

function spridisi_section_action_defaults() {
	return array(
		'venue'         => array( 'anchor' => 'svinibas', 'path' => 'svini', 'label' => __( 'Par svinībām', 'spridisi' ) ),
		'accommodation' => array( 'anchor' => 'naksnosana', 'path' => 'naksno', 'label' => __( 'Par nakšņošanu', 'spridisi' ) ),
		'sauna'         => array( 'anchor' => 'pirts', 'path' => 'pirts', 'label' => __( 'Par pirti', 'spridisi' ) ),
		'experiences'   => array( 'anchor' => 'selija', 'path' => 'piedzivo', 'label' => __( 'Piedzīvot Sēliju', 'spridisi' ) ),
	);
}

function spridisi_section_actions( $section, $light = false ) {
	$defaults = spridisi_section_action_defaults()[ $section ];
	$page     = get_page_by_path( $defaults['path'], OBJECT, 'page' );
	$detail   = spridisi_homepage_link( $section, 'primary', $page && 'publish' === $page->post_status ? get_permalink( $page ) : '#kontakti' );
	$photo    = spridisi_homepage_link( $section, 'photos', spridisi_gallery_url( $defaults['anchor'] ) );
	$labels   = array( spridisi_homepage_text( $section, 'primary', $defaults['label'] ), spridisi_homepage_text( $section, 'photos', __( 'Skatīt vairāk bilžu galerijā', 'spridisi' ) ) );
	echo '<div class="section-actions">';
	foreach ( array( $detail, $photo ) as $index => $url ) {
		if ( ! $url ) {
			continue;
		}
		$class = 'text-link' . ( $light ? ' text-link--light' : '' ) . ( $index ? ' text-link--secondary' : '' );
		$accessible_label = $index ? sprintf( __( '%1$s: %2$s', 'spridisi' ), $labels[ $index ], spridisi_section_title( $section, $defaults['label'] ) ) : $labels[ $index ];
		printf( '<a class="%s" href="%s" aria-label="%s">%s<span aria-hidden="true">→</span></a>', esc_attr( $class ), esc_url( $url ), esc_attr( $accessible_label ), esc_html( $labels[ $index ] ) );
	}
	echo '</div>';
}

function spridisi_gallery_group( $class, $label, $children, $anchor = '' ) {
	$block = spridisi_seed_group( $class, $label, $children );
	$block['attrs']['templateLock'] = false;
	if ( $anchor ) {
		$block['attrs']['tagName'] = 'section';
		$block['attrs']['anchor']  = $anchor;
		$open = '<section class="wp-block-group ' . $class . '" id="' . esc_attr( $anchor ) . '">';
		$block['innerHTML'] = $open . '</section>';
		$block['innerContent'][0] = $open;
		$block['innerContent'][ count( $block['innerContent'] ) - 1 ] = '</section>';
	}
	return $block;
}

function spridisi_gallery_starter_content() {
	$groups = array( spridisi_gallery_group( 'spridisi-gallery-intro', __( 'Gallery introduction', 'spridisi' ), array( spridisi_seed_text( 'eyebrow', 'SPRĪDĪŠI BILDĒS' ), spridisi_seed_text( 'intro', 'Ieskaties Sprīdīšos — svinību zālēs, nakšņošanā, melnajā pirtī un apkārtnē.' ) ) ) );
	$categories = array(
		'svinibas'   => array( '01 / SVINĪBAS', 'Svinības Sprīdīšos' ),
		'naksnosana' => array( '02 / NAKŠŅOŠANA', 'Paliec pa nakti' ),
		'pirts'      => array( '03 / MELNĀ PIRTS', 'Melnā pirts' ),
		'apkartne'   => array( '04 / SPRĪDĪŠI UN APKĀRTNE', 'Sprīdīši un apkārtne' ),
		'selija'     => array( '05 / PIEDZĪVO SĒLIJU', 'Sēlija un tuvējā apkārtne' ),
	);
	foreach ( $categories as $anchor => $copy ) {
		$gallery = spridisi_seed_block( 'core/gallery', array( 'columns' => 3, 'imageCrop' => false, 'linkTo' => 'none', 'className' => 'spridisi-photo-grid', 'metadata' => array( 'name' => __( 'Add real photographs from the Media Library', 'spridisi' ) ), 'lock' => array( 'move' => true, 'remove' => true ) ), '<figure class="wp-block-gallery has-nested-images columns-3 spridisi-photo-grid"></figure>' );
		$groups[] = spridisi_gallery_group( 'spridisi-gallery-category', $copy[1], array( spridisi_seed_text( 'eyebrow', $copy[0] ), spridisi_seed_text( 'title', $copy[1], true ), $gallery ), $anchor );
	}
	return serialize_blocks( array( spridisi_gallery_group( 'spridisi-gallery-content', __( 'Sprīdīši Gallery', 'spridisi' ), $groups ) ) );
}

function spridisi_gallery_render_content() {
	$blocks = parse_blocks( get_the_content() );
	$has_structure = false;
	foreach ( $blocks as $block ) {
		$has_structure = $has_structure || spridisi_block_has_class( $block, 'spridisi-gallery-content' );
	}
	if ( ! $has_structure ) {
		the_title( '<header class="gallery-page-intro"><h1>', '</h1></header>' );
		the_content();
		return;
	}
	foreach ( $blocks as $block ) {
		if ( ! spridisi_block_has_class( $block, 'spridisi-gallery-content' ) ) {
			echo render_block( $block );
			continue;
		}
		foreach ( $block['innerBlocks'] as $group ) {
			if ( spridisi_block_has_class( $group, 'spridisi-gallery-intro' ) ) {
				echo '<header class="gallery-page-intro">';
				foreach ( $group['innerBlocks'] as $field ) {
					if ( spridisi_block_has_class( $field, 'spridisi-field-eyebrow' ) ) {
						echo render_block( $field );
					}
				}
				the_title( '<h1>', '</h1>' );
				foreach ( $group['innerBlocks'] as $field ) {
					if ( ! spridisi_block_has_class( $field, 'spridisi-field-eyebrow' ) ) {
						echo render_block( $field );
					}
				}
				echo '</header>';
			} else {
				echo render_block( $group );
			}
		}
	}
}

function spridisi_gallery_native_images( $block, $source, $parent ) {
	if ( is_page_template( 'page-templates/gallery.php' ) && 'core/image' === $block['blockName'] && $parent && 'core/gallery' === $parent->name && ! isset( $block['attrs']['lightbox']['enabled'] ) ) {
		$block['attrs']['lightbox']['enabled'] = true;
	}
	return $block;
}
add_filter( 'render_block_data', 'spridisi_gallery_native_images', 10, 3 );

function spridisi_gallery_assets() {
	if ( is_page_template( 'page-templates/gallery.php' ) ) {
		wp_enqueue_script( 'spridisi-gallery', get_theme_file_uri( '/assets/js/gallery.js' ), array(), (string) filemtime( get_theme_file_path( '/assets/js/gallery.js' ) ), array( 'strategy' => 'defer', 'in_footer' => true ) );
	}
}
add_action( 'wp_enqueue_scripts', 'spridisi_gallery_assets' );

add_filter( 'the_content', 'spridisi_gallery_add_homepage_actions', 20 );

function spridisi_gallery_add_homepage_actions( $content ) {
	$blocks = parse_blocks( $content );
	$defaults = spridisi_section_action_defaults();
	foreach ( $blocks as &$root ) {
		if ( ! spridisi_block_has_class( $root, 'spridisi-homepage-content' ) ) {
			continue;
		}
		foreach ( $root['innerBlocks'] as &$section ) {
			foreach ( $defaults as $key => $settings ) {
				if ( ! spridisi_block_has_class( $section, 'spridisi-content-' . $key ) ) {
					continue;
				}
				foreach ( array( 'primary', 'photos' ) as $field ) {
					$present = false;
					foreach ( $section['innerBlocks'] as $child ) {
						$present = $present || spridisi_block_has_class( $child, 'spridisi-field-' . $field );
					}
					if ( $present ) {
						continue;
					}
					$page = get_page_by_path( $settings['path'], OBJECT, 'page' );
					$url = 'photos' === $field ? spridisi_gallery_url( $settings['anchor'] ) : ( $page && 'publish' === $page->post_status ? get_permalink( $page ) : '#kontakti' );
					$url = $url && '#' !== $url[0] ? wp_make_link_relative( $url ) : $url;
					$section['innerBlocks'][] = spridisi_seed_button( $field, 'photos' === $field ? 'Skatīt vairāk bilžu galerijā' : $settings['label'], $url );
					array_splice( $section['innerContent'], count( $section['innerContent'] ) - 1, 0, array( null ) );
				}
			}
		}
		unset( $section );
	}
	unset( $root );
	return serialize_blocks( $blocks );
}

function spridisi_gallery_update_menu( $gallery_id ) {
	$locations = get_nav_menu_locations();
	$menu_id = absint( $locations['primary'] ?? 0 );
	if ( ! $menu_id ) {
		$menu_id = wp_create_nav_menu( __( 'Sprīdīši primary navigation', 'spridisi' ) );
		if ( is_wp_error( $menu_id ) ) {
			return $menu_id;
		}
		$items = array( 'sakums' => 'Sākums', 'svini' => 'Svini', 'naksno' => 'Nakšņo', 'pirts' => 'Pirts', 'piedzivo' => 'Piedzīvo', 'galerija' => 'Galerija', 'stasts' => 'Mūsu stāsts', 'kontakti' => 'Kontakti' );
		foreach ( $items as $anchor => $label ) {
			$args = array( 'menu-item-title' => $label, 'menu-item-status' => 'publish', 'menu-item-type' => 'custom', 'menu-item-url' => wp_make_link_relative( home_url( '/' ) ) . '#' . $anchor );
			if ( 'galerija' === $anchor ) {
				$args['menu-item-type'] = 'post_type';
				$args['menu-item-object'] = 'page';
				$args['menu-item-object-id'] = $gallery_id;
			}
			$result = wp_update_nav_menu_item( $menu_id, 0, $args );
			if ( is_wp_error( $result ) ) {
				return $result;
			}
		}
		$locations['primary'] = $menu_id;
		set_theme_mod( 'nav_menu_locations', $locations );
		return true;
	}
	$items = wp_get_nav_menu_items( $menu_id );
	$position = count( $items ? $items : array() ) + 1;
	foreach ( $items ? $items : array() as $item ) {
		if ( 'page' === $item->object && (int) $item->object_id === $gallery_id ) {
			return true;
		}
		if ( ! $item->menu_item_parent && 'piedzivo' === wp_parse_url( $item->url, PHP_URL_FRAGMENT ) ) {
			$position = $item->menu_order + 1;
		}
	}
	foreach ( array_reverse( $items ? $items : array() ) as $item ) {
		if ( $item->menu_order >= $position ) {
			wp_update_post( array( 'ID' => $item->ID, 'menu_order' => $item->menu_order + 1 ) );
		}
	}
	return wp_update_nav_menu_item( $menu_id, 0, array( 'menu-item-title' => get_the_title( $gallery_id ), 'menu-item-type' => 'post_type', 'menu-item-object' => 'page', 'menu-item-object-id' => $gallery_id, 'menu-item-status' => 'publish', 'menu-item-position' => $position ) );
}

function spridisi_gallery_setup_notice() {
	if ( ! current_user_can( 'manage_options' ) || ! current_user_can( 'publish_pages' ) ) {
		return;
	}
	$complete = (bool) spridisi_gallery_page();
	foreach ( array_keys( spridisi_section_action_defaults() ) as $section ) {
		$complete = $complete && null !== spridisi_homepage_field( $section, 'photos' );
	}
	if ( $complete ) {
		return;
	}
	echo '<div class="notice notice-info"><p>' . esc_html__( 'Sprīdīši: create or reuse the Gallery page, add missing editable homepage photo links, and add Gallery to the primary menu. Existing editorial text and action links are preserved.', 'spridisi' ) . '</p><form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
	wp_nonce_field( 'spridisi_setup_gallery' );
	echo '<input type="hidden" name="action" value="spridisi_setup_gallery">';
	submit_button( __( 'Set up Gallery and photo links', 'spridisi' ), 'secondary', 'submit', false );
	echo '</form><p></p></div>';
}
add_action( 'admin_notices', 'spridisi_gallery_setup_notice' );

function spridisi_setup_gallery() {
	if ( ! current_user_can( 'manage_options' ) || ! current_user_can( 'publish_pages' ) ) {
		wp_die( esc_html__( 'You cannot set up the Gallery page.', 'spridisi' ) );
	}
	check_admin_referer( 'spridisi_setup_gallery' );
	$gallery = spridisi_gallery_page();
	$gallery = $gallery ? $gallery : get_page_by_path( 'galerija', OBJECT, 'page' );
	if ( $gallery && ! current_user_can( 'edit_post', $gallery->ID ) ) {
		wp_die( esc_html__( 'You cannot edit the existing Gallery page.', 'spridisi' ) );
	}
	if ( ! $gallery || '' === trim( $gallery->post_content ) ) {
		$data = array( 'post_type' => 'page', 'post_status' => $gallery ? $gallery->post_status : 'publish', 'post_name' => 'galerija', 'post_title' => $gallery ? $gallery->post_title : 'Galerija', 'post_content' => spridisi_gallery_starter_content() );
		if ( $gallery ) {
			$data['ID'] = $gallery->ID;
		}
		$gallery_id = wp_insert_post( wp_slash( $data ), true );
		if ( is_wp_error( $gallery_id ) ) {
			wp_die( esc_html( $gallery_id->get_error_message() ) );
		}
		update_post_meta( $gallery_id, '_wp_page_template', 'page-templates/gallery.php' );
		$gallery = get_post( $gallery_id );
	}
	if ( str_contains( $gallery->post_content, 'spridisi-gallery-content' ) ) {
		update_post_meta( $gallery->ID, '_wp_page_template', 'page-templates/gallery.php' );
	}
	$homepage_id = absint( get_option( 'page_on_front' ) );
	if ( $homepage_id && current_user_can( 'edit_post', $homepage_id ) && spridisi_homepage_sections() && 'publish' === $gallery->post_status ) {
		$before = get_post_field( 'post_content', $homepage_id );
		$after  = spridisi_gallery_add_homepage_actions( $before );
		if ( $after !== $before ) {
			$result = wp_update_post( wp_slash( array( 'ID' => $homepage_id, 'post_content' => $after ) ), true );
			if ( is_wp_error( $result ) ) {
				wp_die( esc_html( $result->get_error_message() ) );
			}
		}
	}
	if ( 'publish' === $gallery->post_status ) {
		$result = spridisi_gallery_update_menu( $gallery->ID );
		if ( is_wp_error( $result ) ) {
			wp_die( esc_html( $result->get_error_message() ) );
		}
	}
	wp_safe_redirect( get_edit_post_link( $gallery->ID, 'raw' ) );
	exit;
}
add_action( 'admin_post_spridisi_setup_gallery', 'spridisi_setup_gallery' );