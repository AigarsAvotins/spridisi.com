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
	$theme_version  = wp_get_theme()->get( 'Version' );
	$style_path     = get_theme_file_path( '/assets/css/main.css' );
	$script_path    = get_theme_file_path( '/assets/js/main.js' );
	$style_version  = is_file( $style_path ) ? (string) filemtime( $style_path ) : $theme_version;
	$script_version = is_file( $script_path ) ? (string) filemtime( $script_path ) : $theme_version;
	$force_refresh  = '20261125'; // Force cache refresh

	wp_enqueue_style(
		'spridisi-main',
		esc_url( get_theme_file_uri( '/assets/css/main.css' ) ),
		array(),
		$style_version . '-' . $force_refresh
	);

	wp_enqueue_script(
		'spridisi-main',
		esc_url( get_theme_file_uri( '/assets/js/main.js' ) ),
		array(),
		$script_version . '-' . $force_refresh,
		array(
			'strategy'  => 'defer',
			'in_footer' => true,
		)
	);

	// Contact widget script (only on homepage)
	if ( is_front_page() ) {
		$widget_script_path = get_theme_file_path( '/assets/js/contact-widget.js' );
		$widget_script_version = is_file( $widget_script_path ) ? (string) filemtime( $widget_script_path ) : $theme_version;
		wp_enqueue_script(
			'spridisi-contact-widget',
			esc_url( get_theme_file_uri( '/assets/js/contact-widget.js' ) ),
			array(),
			$widget_script_version . '-' . $force_refresh,
			array(
				'strategy'  => 'defer',
				'in_footer' => true,
			)
		);
	}
}
add_action( 'wp_enqueue_scripts', 'spridisi_enqueue_assets' );

// Contact widget - send message via email
function spridisi_send_contact_message() {
	// Check if this is a POST request
	if ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) {
		return;
	}

	// Check for required fields
	if ( empty( $_POST['name'] ) || empty( $_POST['email'] ) || empty( $_POST['message'] ) ) {
		wp_die( 'Missing required fields' );
	}

	$name = sanitize_text_field( $_POST['name'] );
	$email = sanitize_email( $_POST['email'] );
	$message = sanitize_textarea_field( $_POST['message'] );
	$admin_email = get_option( 'admin_email' );

	// Compose email
	$subject = sprintf( 'Juna ziņa no %s', $name );
	$body = sprintf( "Vārds: %s\nE-pasts: %s\n\nZiņa:\n%s", $name, $email, $message );
	$headers = array( 'Content-Type: text/plain; charset=UTF-8', 'From: ' . $email );

	// Send email
	if ( wp_mail( $admin_email, $subject, $body, $headers ) ) {
		wp_die( 'success' );
	} else {
		wp_die( 'error' );
	}
}

// Hook for frontend form submission
if ( isset( $_POST['action'] ) && $_POST['action'] === 'send_contact_message' ) {
	add_action( 'wp', 'spridisi_send_contact_message', 1 );
	spridisi_send_contact_message();
}

// SEO: Add meta tags and structured data
function spridisi_add_meta_tags() {
	$site_title = get_bloginfo( 'name' );
	$site_description = get_bloginfo( 'description' );
	$site_url = home_url( '/' );
	
	// Meta description based on page
	if ( is_front_page() ) {
		$meta_description = $site_description ?: 'Sprīdīši - Skaists pasākumu vieta Vecsēlpilī, Jēkabpils novadā. Organizējam kāzas, dzimšanas dienas, ģimenes svētkus un korporatīvos pasākumus.';
		$page_title = $site_title;
	} elseif ( is_page( 'rezervacija' ) ) {
		$meta_description = 'Rezervējiet Sprīdīši savu nākamo pasākumu vai nakšņošanu. Personisks pieeja, skaists skats uz Daugavu.';
		$page_title = 'Rezervācija – ' . $site_title;
	} else {
		$meta_description = $site_description;
		$page_title = get_the_title() ? get_the_title() . ' – ' . $site_title : $site_title;
	}
	
	echo "\n<!-- SEO Meta Tags -->\n";
	echo '<meta name="description" content="' . esc_attr( wp_trim_words( $meta_description, 160 ) ) . '">' . "\n";
	echo '<meta name="viewport" content="width=device-width, initial-scale=1">' . "\n";
	echo '<meta name="theme-color" content="#ffffff">' . "\n";
	
	// Open Graph tags for social sharing
	echo "\n<!-- Open Graph Meta Tags -->\n";
	echo '<meta property="og:type" content="' . ( is_front_page() ? 'website' : 'article' ) . '">' . "\n";
	echo '<meta property="og:title" content="' . esc_attr( $page_title ) . '">' . "\n";
	echo '<meta property="og:description" content="' . esc_attr( wp_trim_words( $meta_description, 160 ) ) . '">' . "\n";
	echo '<meta property="og:url" content="' . esc_attr( is_front_page() ? $site_url : get_permalink() ) . '">' . "\n";
	echo '<meta property="og:site_name" content="' . esc_attr( $site_title ) . '">' . "\n";
	
	// Image for social sharing
	$image_id = get_post_thumbnail_id();
	if ( $image_id ) {
		$image_url = wp_get_attachment_image_url( $image_id, 'large' );
		if ( $image_url ) {
			echo '<meta property="og:image" content="' . esc_attr( $image_url ) . '">' . "\n";
			echo '<meta property="og:image:width" content="1200">' . "\n";
			echo '<meta property="og:image:height" content="630">' . "\n";
		}
	}
	
	// Twitter Card tags
	echo "\n<!-- Twitter Card Tags -->\n";
	echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
	echo '<meta name="twitter:title" content="' . esc_attr( $page_title ) . '">' . "\n";
	echo '<meta name="twitter:description" content="' . esc_attr( wp_trim_words( $meta_description, 160 ) ) . '">' . "\n";
	
	// Canonical URL
	echo '<link rel="canonical" href="' . esc_attr( is_front_page() ? $site_url : get_permalink() ) . '">' . "\n";
	
	// Alternate language links
	echo '<link rel="alternate" hreflang="lv" href="' . esc_attr( $site_url ) . '">' . "\n";
	echo '<link rel="alternate" hreflang="x-default" href="' . esc_attr( $site_url ) . '">' . "\n";
}
add_action( 'wp_head', 'spridisi_add_meta_tags', 5 );

// SEO: Add structured data (Schema.org)
function spridisi_add_structured_data() {
	$contact_details = spridisi_default_contact_details();
	$review_details = spridisi_default_review_details();
	
	$structured_data = array(
		'@context' => 'https://schema.org',
		'@type' => 'LocalBusiness',
		'name' => get_bloginfo( 'name' ),
		'description' => get_bloginfo( 'description' ),
		'url' => home_url( '/' ),
		'telephone' => $contact_details['phone'],
		'email' => $contact_details['email'],
		'address' => array(
			'@type' => 'PostalAddress',
			'streetAddress' => 'Sprīdīši, Sēlpils pagasts',
			'addressLocality' => 'Jēkabpils',
			'postalCode' => 'LV-5232',
			'addressCountry' => 'LV',
		),
		'sameAs' => array(
			$contact_details['facebook'],
			$contact_details['instagram'],
		),
		'aggregateRating' => array(
			'@type' => 'AggregateRating',
			'ratingValue' => $review_details['google_rating'],
			'reviewCount' => $review_details['google_review_count'],
		),
	);
	
	echo "\n<!-- Structured Data (Schema.org) -->\n";
	echo '<script type="application/ld+json">' . wp_json_encode( $structured_data ) . '</script>' . "\n";
}
add_action( 'wp_head', 'spridisi_add_structured_data', 6 );

require_once get_theme_file_path( '/inc/homepage-content.php' );
require_once get_theme_file_path( '/inc/gallery.php' );
require_once get_theme_file_path( '/inc/booking.php' );
require_once get_theme_file_path( '/inc/history-migration.php' );

function spridisi_default_contact_details() {
	return array(
		'email' => 'info@spridisi.com',
		'phone' => '+371 26344495',
		'address' => 'Sprīdīši, Sēlpils pagasts, Jēkabpils novads, LV-5232',
	'instagram' => 'https://instagram.com/spridisi',
	'facebook' => 'https://facebook.com/teirtalaimeszeme',
	);
}

function spridisi_default_review_details() {
	return array(
		'google_rating' => '4.9',
		'google_review_count' => '28',
		'google_reviews' => "Kristīne Buče · 5/5 · pirms 3 mēnešiem\nMāris Dubinskis · 5/5 · pirms 5 mēnešiem · izceļ viesmīlīgos saimniekus.\nArmands Skrējāns · 5/5 · pirms 6 mēnešiem",
		'booking_rating' => '9.2',
		'booking_review_count' => '11',
		'booking_reviews' => '',
	);
}

function spridisi_customize_register( $wp_customize ) {
	$wp_customize->add_section( 'spridisi_homepage', array( 'title' => __( 'Sprīdīši: homepage', 'spridisi' ), 'priority' => 30 ) );
	$wp_customize->add_section( 'spridisi_reviews', array( 'title' => __( 'Sprīdīši: reviews', 'spridisi' ), 'priority' => 31 ) );
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
	$defaults = spridisi_default_contact_details();
	$fields = array(
		'email'       => array( __( 'Booking email', 'spridisi' ), 'email', 'sanitize_email', $defaults['email'] ),
		'phone'       => array( __( 'Phone', 'spridisi' ), 'text', 'sanitize_text_field', $defaults['phone'] ),
		'address'     => array( __( 'Address', 'spridisi' ), 'text', 'sanitize_text_field', $defaults['address'] ),
		'booking_url' => array( __( 'Booking URL (optional; overrides reservation page)', 'spridisi' ), 'url', 'esc_url_raw', '' ),
		'instagram'   => array( __( 'Instagram URL', 'spridisi' ), 'url', 'esc_url_raw', $defaults['instagram'] ),
		'facebook'    => array( __( 'Facebook URL', 'spridisi' ), 'url', 'esc_url_raw', $defaults['facebook'] ),
	);
	foreach ( $fields as $key => $field ) {
		$wp_customize->add_setting( 'spridisi_' . $key, array( 'default' => $field[3], 'sanitize_callback' => $field[2] ) );
		$wp_customize->add_control( 'spridisi_' . $key, array( 'label' => $field[0], 'section' => 'spridisi_homepage', 'type' => $field[1] ) );
	}
	$review_defaults = spridisi_default_review_details();
	$review_fields = array(
		'google_rating' => array( __( 'Google overall rating (out of 5)', 'spridisi' ), 'text', 'sanitize_text_field' ),
		'google_review_count' => array( __( 'Google review count', 'spridisi' ), 'number', 'absint' ),
		'google_reviews' => array( __( 'Google latest reviews (one per line)', 'spridisi' ), 'textarea', 'sanitize_textarea_field' ),
		'booking_rating' => array( __( 'Booking.com overall rating (out of 10)', 'spridisi' ), 'text', 'sanitize_text_field' ),
		'booking_review_count' => array( __( 'Booking.com review count', 'spridisi' ), 'number', 'absint' ),
		'booking_reviews' => array( __( 'Booking.com review excerpts (one per paragraph)', 'spridisi' ), 'textarea', 'sanitize_textarea_field' ),
	);
	foreach ( $review_fields as $key => $field ) {
		$wp_customize->add_setting( 'spridisi_' . $key, array( 'default' => $review_defaults[ $key ], 'sanitize_callback' => $field[2] ) );
		$wp_customize->add_control( 'spridisi_' . $key, array( 'label' => $field[0], 'section' => 'spridisi_reviews', 'type' => $field[1] ) );
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
		if ( 'stasts' === $anchor && spridisi_gallery_url() ) {
			echo '<li><a href="' . esc_url( spridisi_gallery_url() ) . '">' . esc_html__( 'Galerija', 'spridisi' ) . '</a></li>';
		}
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
		return spridisi_reservation_link( spridisi_homepage_link( 'contact', 'primary' ) );
	}
	$defaults = spridisi_default_contact_details();
	$url   = get_theme_mod( 'spridisi_booking_url', '' );
	$email = sanitize_email( get_theme_mod( 'spridisi_email', $defaults['email'] ) );
	return $url ? $url : ( spridisi_reservation_link() ?: ( $email ? 'mailto:' . $email : '' ) );
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
	if ( 'history' === $section ) {
		foreach ( $sections[ $root_id ] as $page ) {
			if ( get_post_meta( $page->ID, '_spridisi_history_source_id', true ) ) {
				return $page;
			}
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