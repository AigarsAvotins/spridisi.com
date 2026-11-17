<?php
/**
 * Theme setup and assets.
 *
 * @package Spridisi
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function spridisi_setup() {
	load_theme_textdomain( 'spridisi', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' )
	);
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 120,
			'width'       => 320,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'wp-block-styles' );
	add_post_type_support( 'page', 'excerpt' );

	register_nav_menus(
		array(
			'primary' => __( 'Primary Navigation', 'spridisi' ),
			'footer'  => __( 'Footer Navigation', 'spridisi' ),
			'language' => __( 'Language Navigation', 'spridisi' ),
		)
	);
}
add_action( 'after_setup_theme', 'spridisi_setup' );

function spridisi_enqueue_assets() {
	$theme_version = wp_get_theme()->get( 'Version' );

	wp_enqueue_style(
		'spridisi-main',
		get_theme_file_uri( '/assets/css/main.css' ),
		array(),
		$theme_version
	);

	wp_enqueue_script(
		'spridisi-main',
		get_theme_file_uri( '/assets/js/main.js' ),
		array(),
		$theme_version,
		array(
			'strategy'  => 'defer',
			'in_footer' => true,
		)
	);
}
add_action( 'wp_enqueue_scripts', 'spridisi_enqueue_assets' );

require_once get_theme_file_path( '/inc/homepage-content.php' );

function spridisi_customize_register( $wp_customize ) {
	$wp_customize->add_section( 'spridisi_homepage', array( 'title' => __( 'Sprīdīši: homepage', 'spridisi' ), 'priority' => 30 ) );
	$images = array(
		'hero'          => __( 'Hero', 'spridisi' ),
		'venue'         => __( 'Venue', 'spridisi' ),
		'accommodation' => __( 'Accommodation', 'spridisi' ),
		'sauna'         => __( 'Sauna', 'spridisi' ),
		'daugava'       => __( 'Daugava', 'spridisi' ),
		'sup'           => __( 'SUP', 'spridisi' ),
		'selija'        => __( 'Sēlija', 'spridisi' ),
		'gallery_1'     => __( 'Gallery 1', 'spridisi' ),
		'gallery_2'     => __( 'Gallery 2', 'spridisi' ),
		'gallery_3'     => __( 'Gallery 3', 'spridisi' ),
	);
	foreach ( $images as $key => $label ) {
		$wp_customize->add_setting( 'spridisi_image_' . $key, array( 'default' => 0, 'sanitize_callback' => 'absint' ) );
		$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'spridisi_image_' . $key, array( 'label' => $label, 'section' => 'spridisi_homepage', 'mime_type' => 'image' ) ) );
	}
	$fields = array(
		'email'       => array( __( 'Booking email', 'spridisi' ), 'email', 'sanitize_email' ),
		'phone'       => array( __( 'Phone', 'spridisi' ), 'text', 'sanitize_text_field' ),
		'address'     => array( __( 'Address', 'spridisi' ), 'text', 'sanitize_text_field' ),
		'booking_url' => array( __( 'Booking URL (optional; overrides email)', 'spridisi' ), 'url', 'esc_url_raw' ),
		'instagram'   => array( __( 'Instagram URL', 'spridisi' ), 'url', 'esc_url_raw' ),
		'facebook'    => array( __( 'Facebook URL', 'spridisi' ), 'url', 'esc_url_raw' ),
	);
	foreach ( $fields as $key => $field ) {
		$wp_customize->add_setting( 'spridisi_' . $key, array( 'default' => '', 'sanitize_callback' => $field[2] ) );
		$wp_customize->add_control( 'spridisi_' . $key, array( 'label' => $field[0], 'section' => 'spridisi_homepage', 'type' => $field[1] ) );
	}
	$wp_customize->add_setting( 'spridisi_history_page', array( 'default' => 0, 'sanitize_callback' => 'absint' ) );
	$wp_customize->add_control( 'spridisi_history_page', array( 'label' => __( 'Full history page', 'spridisi' ), 'section' => 'spridisi_homepage', 'type' => 'dropdown-pages' ) );
}
add_action( 'customize_register', 'spridisi_customize_register' );

function spridisi_home_anchor( $anchor ) {
	return ( is_front_page() ? '' : home_url( '/' ) ) . '#' . $anchor;
}

function spridisi_default_menu() {
	$items = array(
		'sakums'   => __( 'Sākums', 'spridisi' ),
		'svini'    => __( 'Svini', 'spridisi' ),
		'naksno'   => __( 'Nakšņo', 'spridisi' ),
		'pirts'    => __( 'Pirts', 'spridisi' ),
		'piedzivo' => __( 'Piedzīvo', 'spridisi' ),
		'stasts'   => __( 'Mūsu stāsts', 'spridisi' ),
		'kontakti' => __( 'Kontakti', 'spridisi' ),
	);
	echo '<ul class="menu">';
	foreach ( $items as $anchor => $label ) {
		echo '<li><a href="' . esc_url( spridisi_home_anchor( $anchor ) ) . '">' . esc_html( $label ) . '</a></li>';
	}
	echo '</ul>';
}

function spridisi_photo( $key, $size = 'large', $page = null ) {
	$native_key = array( 'daugava' => 'experiences/daugava', 'sup' => 'experiences/sup', 'selija' => 'experiences/selija', 'gallery_1' => 'gallery/photo-1', 'gallery_2' => 'gallery/photo-2', 'gallery_3' => 'gallery/photo-3' );
	if ( spridisi_homepage_photo( $native_key[ $key ] ?? $key, 'editorial-photo', $size ) ) {
		return;
	}
	$page     = $page ? $page : spridisi_section_page( $key );
	$image_id = $page ? get_post_thumbnail_id( $page ) : 0;
	$image_id = $image_id ? $image_id : absint( get_theme_mod( 'spridisi_image_' . $key, 0 ) );
	$image    = $image_id ? wp_get_attachment_image( $image_id, $size, false, array( 'class' => 'editorial-photo' ) ) : '';
	if ( $image ) {
		echo $image;
	} elseif ( in_array( $key, array( 'venue', 'sauna' ), true ) ) {
		$alt_text = array(
			'venue' => __( 'Sprīdīšu svinību zāle ar klātiem galdiem un koka sijām.', 'spridisi' ),
			'sauna' => __( 'Sprīdīšu koka pirts ar pirts slotām uz āra galda.', 'spridisi' ),
		);
		$width  = 'venue' === $key ? 736 : 1536;
		$height = 'venue' === $key ? 1000 : 2048;
		printf(
			'<img class="editorial-photo" src="%1$s" alt="%2$s" width="%3$d" height="%4$d" loading="lazy" decoding="async">',
			esc_url( get_theme_file_uri( '/assets/images/' . $key . '-spridisi.jpg' ) ),
			esc_attr( $alt_text[ $key ] ),
			$width,
			$height
		);
	} else {
		echo '<div class="photo-placeholder" aria-hidden="true"><span class="photo-placeholder__monogram">S</span><span class="photo-placeholder__caption">' . esc_html__( 'SPRĪDĪŠI', 'spridisi' ) . ' <span>·</span> ' . esc_html__( 'Fotogrāfija', 'spridisi' ) . '</span></div>';
	}
}

function spridisi_booking_url() {
	if ( null !== spridisi_homepage_field( 'contact', 'primary' ) ) {
		return spridisi_homepage_link( 'contact', 'primary' );
	}
	$url   = get_theme_mod( 'spridisi_booking_url', '' );
	$email = sanitize_email( get_theme_mod( 'spridisi_email', '' ) );
	return $url ? $url : ( $email ? 'mailto:' . $email : '' );
}

function spridisi_content_children( $parent_id ) {
	if ( ! $parent_id ) {
		return array();
	}
	$query = new WP_Query(
		array(
			'post_type'        => 'page',
			'post_parent'      => absint( $parent_id ),
			'post_status'      => 'publish',
			'has_password'     => false,
			'posts_per_page'   => -1,
			'orderby'          => array( 'menu_order' => 'ASC', 'ID' => 'ASC' ),
			'no_found_rows'    => true,
			'suppress_filters' => false,
		)
	);
	return $query->posts;
}

function spridisi_section_page( $section ) {
	static $sections = array();
	$root_id = is_front_page() && is_page() ? get_queried_object_id() : absint( get_option( 'page_on_front' ) );
	$slots   = array( 'hero' => 10, 'introduction' => 20, 'venue' => 30, 'accommodation' => 40, 'sauna' => 50, 'experiences' => 60, 'history' => 70, 'gallery' => 80, 'contact' => 90 );
	if ( ! array_key_exists( $root_id, $sections ) ) {
		$sections[ $root_id ] = array();
		foreach ( spridisi_content_children( $root_id ) as $page ) {
			$sections[ $root_id ][ $page->menu_order ] = $page;
		}
	}
	return isset( $slots[ $section ], $sections[ $root_id ][ $slots[ $section ] ] ) ? $sections[ $root_id ][ $slots[ $section ] ] : null;
}

function spridisi_section_title( $section, $fallback ) {
	if ( null !== spridisi_homepage_field( $section, 'title' ) ) {
		return spridisi_homepage_text( $section, 'title' );
	}
	$page = spridisi_section_page( $section );
	return $page ? get_the_title( $page ) : $fallback;
}

function spridisi_section_summary( $section, $fallback ) {
	$fields = array( 'hero' => 'text', 'introduction' => 'eyebrow', 'venue' => 'eyebrow', 'accommodation' => 'summary', 'sauna' => 'eyebrow', 'experiences' => 'summary', 'gallery' => 'eyebrow', 'contact' => 'location' );
	if ( isset( $fields[ $section ] ) && null !== spridisi_homepage_field( $section, $fields[ $section ] ) ) {
		return spridisi_homepage_text( $section, $fields[ $section ] );
	}
	$page = spridisi_section_page( $section );
	return $page && '' !== trim( $page->post_excerpt ) ? wp_strip_all_tags( $page->post_excerpt ) : $fallback;
}

function spridisi_page_content( $page ) {
	if ( ! $page || '' === trim( $page->post_content ) ) {
		return;
	}
	$GLOBALS['post'] = $page;
	setup_postdata( $page );
	the_content();
	wp_reset_postdata();
}

function spridisi_section_content( $section ) {
	$sections = spridisi_homepage_sections();
	if ( isset( $sections[ $section ] ) ) {
		$html = spridisi_homepage_body_markup( $section );
		if ( $html ) {
			echo 'introduction' === $section ? $html : '<div class="section-content">' . $html . '</div>';
		}
		return;
	}
	$page = spridisi_section_page( $section );
	if ( $page && '' !== trim( $page->post_content ) ) {
		echo '<div class="section-content">';
		spridisi_page_content( $page );
		echo '</div>';
	}
}