<?php
/**
 * One-time migration of the legacy history page into timeline entries.
 *
 * @package Spridisi
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function spridisi_history_import_parse( $content ) {
	if ( ! class_exists( 'DOMDocument' ) ) {
		return new WP_Error( 'history_dom_missing', __( 'Serverī nav pieejams HTML dokumentu parsētājs.', 'spridisi' ) );
	}
	$document = new DOMDocument( '1.0', 'UTF-8' );
	$previous = libxml_use_internal_errors( true );
	$loaded = $document->loadHTML( '<?xml encoding="utf-8" ?><div id="spridisi-history-source">' . $content . '</div>', LIBXML_HTML_NODEFDTD | LIBXML_NONET );
	libxml_clear_errors();
	libxml_use_internal_errors( $previous );
	if ( ! $loaded ) {
		return new WP_Error( 'history_parse_failed', __( 'Vēstures lapas saturu neizdevās nolasīt.', 'spridisi' ) );
	}
	$xpath = new DOMXPath( $document );
	$container = $xpath->query( '//*[@id="spridisi-history-source"]' )->item( 0 );
	if ( ! $container ) {
		return new WP_Error( 'history_container_missing', __( 'Vēstures lapas saturs nav atrasts.', 'spridisi' ) );
	}
	$intro = '';
	$entries = array();
	$current = null;
	foreach ( $container->childNodes as $node ) {
		if ( XML_ELEMENT_NODE === $node->nodeType && 'h2' === strtolower( $node->nodeName ) ) {
			if ( $current ) {
				$entries[] = $current;
			}
			$current = array( 'title' => sanitize_text_field( $node->textContent ), 'content' => '' );
		} elseif ( $current ) {
			$current['content'] .= $document->saveHTML( $node );
		} else {
			$intro .= $document->saveHTML( $node );
		}
	}
	if ( $current ) {
		$entries[] = $current;
	}
	$entries = array_values( array_filter( $entries, static function ( $entry ) {
		return '' !== $entry['title'] && ( '' !== trim( wp_strip_all_tags( $entry['content'] ) ) || preg_match( '/<img\b/i', $entry['content'] ) );
	} ) );
	if ( ! $entries ) {
		return new WP_Error( 'history_entries_missing', __( 'Vēstures lapā netika atrasti datēti ieraksti.', 'spridisi' ) );
	}
	return array( 'intro' => wp_kses_post( $intro ), 'entries' => $entries );
}

function spridisi_history_import_source( $source_id = 0, $remote = false ) {
	$source = $remote ? null : ( $source_id ? get_post( $source_id ) : get_page_by_path( 'history', OBJECT, 'page' ) );
	if ( $source && 'page' === $source->post_type && 'publish' === $source->post_status ) {
		return (object) array( 'ID' => $source->ID, 'post_type' => 'page', 'post_status' => 'publish', 'post_content' => $source->post_content, 'source_url' => get_permalink( $source ), 'remote' => false );
	}
	$response = wp_remote_get( 'https://spridisi.com/wp-json/wp/v2/pages?slug=history&_fields=id,link,status,content', array( 'timeout' => 15, 'redirection' => 3, 'limit_response_size' => 8388608 ) );
	if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
		return null;
	}
	$pages = json_decode( wp_remote_retrieve_body( $response ), true );
	if ( ! is_array( $pages ) || empty( $pages[0]['content']['rendered'] ) || empty( $pages[0]['link'] ) ) {
		return null;
	}
	return (object) array( 'ID' => absint( $pages[0]['id'] ), 'post_type' => 'page', 'post_status' => 'publish', 'post_content' => $pages[0]['content']['rendered'], 'source_url' => esc_url_raw( $pages[0]['link'] ), 'remote' => true );
}

function spridisi_history_import_available( $source ) {
	if ( ! $source || 'page' !== $source->post_type || 'publish' !== $source->post_status || ! current_user_can( 'manage_options' ) || ! current_user_can( 'publish_pages' ) || ( empty( $source->remote ) && ! current_user_can( 'edit_post', $source->ID ) ) || ! get_option( 'page_on_front' ) ) {
		return false;
	}
	$history_page = spridisi_section_page( 'history' );
	if ( $history_page && $source->ID === $history_page->ID ) {
		$history_page = null;
	}
	if ( ! $history_page ) {
		return true;
	}
	if ( 'complete' === get_post_meta( $history_page->ID, '_spridisi_history_import_state', true ) ) {
		return false;
	}
	$imported_source_id = (int) get_post_meta( $history_page->ID, '_spridisi_history_source_id', true );
	if ( $imported_source_id && $imported_source_id !== (int) $source->ID ) {
		return false;
	}
	foreach ( spridisi_content_children( $history_page->ID ) as $child ) {
		if ( (int) get_post_meta( $child->ID, '_spridisi_history_source_id', true ) !== (int) $source->ID ) {
			return false;
		}
	}
	return true;
}

function spridisi_history_import_notice() {
	if ( ! current_user_can( 'manage_options' ) || ! current_user_can( 'publish_pages' ) ) {
		return;
	}
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || 'edit-page' !== $screen->id ) {
		return;
	}
	$history_page = spridisi_section_page( 'history' );
	if ( $history_page && 'complete' === get_post_meta( $history_page->ID, '_spridisi_history_import_state', true ) && current_user_can( 'edit_post', $history_page->ID ) && spridisi_history_images_pending( $history_page->ID ) ) {
		echo '<div class="notice notice-info"><p>' . esc_html__( 'Vēstures fakti ir pārnesti. Tagad vari pārnest bildes uz šīs vietnes mediju bibliotēku.', 'spridisi' ) . '</p><form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
		wp_nonce_field( 'spridisi_import_history_images_' . $history_page->ID );
		echo '<input type="hidden" name="action" value="spridisi_import_history_images"><input type="hidden" name="history_archive_id" value="' . esc_attr( $history_page->ID ) . '">';
		submit_button( __( 'Pārnest bildes', 'spridisi' ), 'secondary', 'submit', false );
		echo '</form></div>';
		return;
	}
	$source = spridisi_history_import_source();
	if ( ! spridisi_history_import_available( $source ) ) {
		return;
	}
	echo '<div class="notice notice-info"><p>' . esc_html__( 'Pārnes vēstures faktus, attēlus un atsauces no publiskās lapas “Vēsture” uz esošās tēmas laika līniju. Avota lapa un tās URL paliks nemainīgi.', 'spridisi' ) . '</p><form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
	wp_nonce_field( 'spridisi_import_history' );
	echo '<input type="hidden" name="action" value="spridisi_import_history">';
	echo '<input type="hidden" name="history_source_remote" value="' . esc_attr( ! empty( $source->remote ) ? '1' : '0' ) . '">';
	if ( empty( $source->remote ) ) {
		echo '<input type="hidden" name="history_source_id" value="' . esc_attr( $source->ID ) . '">';
	}
	submit_button( __( 'Pārnest vēsturi uz laika līniju', 'spridisi' ), 'secondary', 'submit', false );
	echo '</form></div>';
}
add_action( 'admin_notices', 'spridisi_history_import_notice' );

function spridisi_history_import_row_action( $actions, $post ) {
	if ( ! $post instanceof WP_Post ) {
		return $actions;
	}
	if ( 'complete' === get_post_meta( $post->ID, '_spridisi_history_import_state', true ) && current_user_can( 'edit_post', $post->ID ) && spridisi_history_images_pending( $post->ID ) ) {
		$url = add_query_arg( array( 'action' => 'spridisi_import_history_images', 'history_archive_id' => $post->ID ), admin_url( 'admin-post.php' ) );
		$url = wp_nonce_url( $url, 'spridisi_import_history_images_' . $post->ID );
		$actions['spridisi_import_history_images'] = '<a href="' . esc_url( $url ) . '">' . esc_html__( 'Pārnest bildes', 'spridisi' ) . '</a>';
		return $actions;
	}
	$local_source = 'history' === $post->post_name;
	$remote_source = ! $local_source && ! get_page_by_path( 'history', OBJECT, 'page' ) && (int) get_option( 'page_on_front' ) === (int) $post->ID;
	$source = $local_source ? spridisi_history_import_source( $post->ID ) : ( $remote_source ? spridisi_history_import_source( 0, true ) : null );
	if ( ! spridisi_history_import_available( $source ) ) {
		return $actions;
	}
	$query = array( 'action' => 'spridisi_import_history' );
	$query['history_source_remote'] = ! empty( $source->remote ) ? '1' : '0';
	if ( empty( $source->remote ) ) {
		$query['history_source_id'] = $source->ID;
	}
	$url = add_query_arg( $query, admin_url( 'admin-post.php' ) );
	$url = wp_nonce_url( $url, 'spridisi_import_history' );
	$actions['spridisi_import_history'] = '<a href="' . esc_url( $url ) . '">' . esc_html__( 'Pārnest uz laika līniju', 'spridisi' ) . '</a>';
	return $actions;
}
add_filter( 'page_row_actions', 'spridisi_history_import_row_action', 10, 2 );

function spridisi_import_history() {
	if ( ! current_user_can( 'manage_options' ) || ! current_user_can( 'publish_pages' ) ) {
		wp_die( esc_html__( 'Tev nav atļauts pārnest vēstures saturu.', 'spridisi' ) );
	}
	check_admin_referer( 'spridisi_import_history' );
	$source_id = isset( $_REQUEST['history_source_id'] ) ? absint( wp_unslash( $_REQUEST['history_source_id'] ) ) : 0;
	$remote_source = isset( $_REQUEST['history_source_remote'] ) && '1' === wp_unslash( $_REQUEST['history_source_remote'] );
	$source = spridisi_history_import_source( $source_id, $remote_source );
	$homepage_id = absint( get_option( 'page_on_front' ) );
	if ( ! spridisi_history_import_available( $source ) || ! $homepage_id ) {
		wp_die( esc_html__( 'Publicētā vēstures lapa vai sākumlapa nav atrasta. Nekas netika mainīts.', 'spridisi' ) );
	}
	$parsed = spridisi_history_import_parse( $source->post_content );
	if ( is_wp_error( $parsed ) ) {
		wp_die( esc_html( $parsed->get_error_message() ) );
	}
	$history_page = spridisi_section_page( 'history' );
	if ( $history_page && $source->ID === $history_page->ID ) {
		$history_page = null;
	}
	$history_children = $history_page ? spridisi_content_children( $history_page->ID ) : array();
	foreach ( $history_children as $child ) {
		if ( (int) get_post_meta( $child->ID, '_spridisi_history_source_id', true ) !== (int) $source->ID ) {
			wp_die( esc_html__( 'Laika līnijā jau ir citi vēstures ieraksti. Dublikāti netika izveidoti.', 'spridisi' ) );
		}
	}
	if ( $history_page && get_post_meta( $history_page->ID, '_spridisi_history_source_id', true ) && (int) get_post_meta( $history_page->ID, '_spridisi_history_source_id', true ) !== (int) $source->ID ) {
		wp_die( esc_html__( 'Laika līnija ir piesaistīta citai avota lapai. Nekas netika mainīts.', 'spridisi' ) );
	}
	$created_history_page = false;
	if ( ! $history_page ) {
		$history_id = wp_insert_post( wp_slash( array(
			'post_type' => 'page',
			'post_status' => 'publish',
			'post_title' => __( 'Vēsture', 'spridisi' ),
			'post_name' => 'vesture-laika-linija',
			'post_parent' => $homepage_id,
			'menu_order' => 70,
			'post_excerpt' => __( 'Vecsēlpils un Sēlpils vēstures notikumi.', 'spridisi' ),
		) ), true );
		if ( is_wp_error( $history_id ) ) {
			wp_die( esc_html( $history_id->get_error_message() ) );
		}
		$history_page = get_post( $history_id );
		$created_history_page = true;
	}
	update_post_meta( $history_page->ID, '_spridisi_history_source_id', $source->ID );
	update_post_meta( $history_page->ID, '_spridisi_history_source_url', esc_url_raw( $source->source_url ) );
	update_post_meta( $history_page->ID, '_spridisi_history_import_state', 'in_progress' );
	$intro = $parsed['intro'];
	$intro .= '<ul class="history-facts"><li><strong>1876</strong> ' . esc_html__( 'Būvniecības gads', 'spridisi' ) . '</li><li><strong>6897 ha</strong> ' . esc_html__( 'Platība', 'spridisi' ) . '</li><li><strong>16+</strong> ' . esc_html__( 'Muižas ēkas', 'spridisi' ) . '</li><li><strong>96%</strong> ' . esc_html__( 'Ēkas iznīcinātas', 'spridisi' ) . '</li></ul>';
	if ( (int) get_post_meta( $history_page->ID, '_spridisi_history_intro_source_id', true ) !== (int) $source->ID ) {
		$existing_content = trim( $history_page->post_content );
		$combined_content = $existing_content ? $existing_content . "\n\n" . wp_kses_post( $intro ) : wp_kses_post( $intro );
		$updated = wp_update_post( wp_slash( array( 'ID' => $history_page->ID, 'post_content' => $combined_content ) ), true );
		if ( is_wp_error( $updated ) ) {
			if ( $created_history_page ) {
				wp_delete_post( $history_page->ID, true );
			}
			wp_die( esc_html( $updated->get_error_message() ) );
		}
		update_post_meta( $history_page->ID, '_spridisi_history_intro_source_id', $source->ID );
	}
	$created_ids = array();
	foreach ( $parsed['entries'] as $index => $entry ) {
		$source_key = hash( 'sha256', $source->ID . '|' . $index . '|' . $entry['title'] );
		$existing = get_posts( array(
			'post_type' => 'page',
			'post_status' => array( 'publish', 'private', 'draft', 'pending' ),
			'post_parent' => $history_page->ID,
			'meta_key' => '_spridisi_history_source_key',
			'meta_value' => $source_key,
			'numberposts' => 1,
			'fields' => 'ids',
		) );
		if ( $existing ) {
			continue;
		}
		$entry_id = wp_insert_post( wp_slash( array(
			'post_type' => 'page',
			'post_status' => 'publish',
			'post_title' => $entry['title'],
			'post_name' => sanitize_title( $entry['title'] . '-' . ( $index + 1 ) ),
			'post_parent' => $history_page->ID,
			'menu_order' => ( $index + 1 ) * 10,
			'post_content' => wp_kses_post( $entry['content'] ),
		) ), true );
		if ( is_wp_error( $entry_id ) ) {
			foreach ( $created_ids as $created_id ) {
				wp_delete_post( $created_id, true );
			}
			wp_die( esc_html( $entry_id->get_error_message() ) );
		}
		$created_ids[] = $entry_id;
		update_post_meta( $entry_id, '_spridisi_history_source_id', $source->ID );
		update_post_meta( $entry_id, '_spridisi_history_source_key', $source_key );
	}
	update_post_meta( $history_page->ID, '_spridisi_history_import_state', 'complete' );
	wp_safe_redirect( get_edit_post_link( $history_page->ID, 'raw' ) );
	exit;
}
add_action( 'admin_post_spridisi_import_history', 'spridisi_import_history' );

function spridisi_history_render_timeline( $entries, $list_id, $label ) {
	if ( ! $entries ) {
		return;
	}
	echo '<ol id="' . esc_attr( $list_id ) . '" class="timeline" aria-label="' . esc_attr( $label ) . '">';
	foreach ( $entries as $entry ) {
		echo '<li><span class="timeline__marker" aria-hidden="true"></span>';
		if ( trim( $entry->post_excerpt ) ) {
			echo '<span class="eyebrow">' . esc_html( wp_strip_all_tags( $entry->post_excerpt ) ) . '</span>';
		}
		echo '<h3>' . esc_html( get_the_title( $entry ) ) . '</h3><div class="timeline__description"><div class="timeline__description-content">';
		spridisi_page_content( $entry );
		echo '</div><button class="timeline__expand" type="button" aria-expanded="false" data-more="' . esc_attr__( 'Rādīt vairāk', 'spridisi' ) . '" data-less="' . esc_attr__( 'Rādīt mazāk', 'spridisi' ) . '" hidden>' . esc_html__( 'Rādīt vairāk', 'spridisi' ) . '</button></div></li>';
	}
	echo '</ol>';
}

function spridisi_history_featured_entries( $entries, $limit = 3 ) {
	$selected = array();
	$remaining = array();
	foreach ( $entries as $entry ) {
		if ( '1' === get_post_meta( $entry->ID, '_spridisi_history_featured', true ) ) {
			$selected[] = $entry;
		} else {
			$remaining[] = $entry;
		}
	}
	return array_slice( array_merge( $selected, $remaining ), 0, $limit );
}

function spridisi_history_entry_meta_box( $post ) {
	$parent = $post instanceof WP_Post ? get_post( $post->post_parent ) : null;
	if ( ! $parent || ! get_post_meta( $parent->ID, '_spridisi_history_source_id', true ) ) {
		return;
	}
	add_meta_box( 'spridisi-history-featured', __( 'Vēstures izcēlums', 'spridisi' ), 'spridisi_history_entry_meta_box_content', 'page', 'side', 'default' );
}
add_action( 'add_meta_boxes_page', 'spridisi_history_entry_meta_box' );

function spridisi_history_entry_meta_box_content( $post ) {
	wp_nonce_field( 'spridisi_history_featured_' . $post->ID, 'spridisi_history_featured_nonce' );
	$checked = '1' === get_post_meta( $post->ID, '_spridisi_history_featured', true );
	echo '<label><input type="checkbox" name="spridisi_history_featured" value="1"' . checked( $checked, true, false ) . '> ' . esc_html__( 'Rādīt sākumlapā starp trim faktiem', 'spridisi' ) . '</label>';
	echo '<p class="description">' . esc_html__( 'Sākumlapā var izcelt ne vairāk kā trīs ierakstus.', 'spridisi' ) . '</p>';
}

function spridisi_history_entry_save_featured( $post_id, $post ) {
	if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) || ! current_user_can( 'edit_post', $post_id ) || ! isset( $_POST['spridisi_history_featured_nonce'] ) ) {
		return;
	}
	$nonce = sanitize_text_field( wp_unslash( $_POST['spridisi_history_featured_nonce'] ) );
	if ( ! wp_verify_nonce( $nonce, 'spridisi_history_featured_' . $post_id ) ) {
		return;
	}
	$parent = get_post( $post->post_parent );
	if ( ! $parent || ! get_post_meta( $parent->ID, '_spridisi_history_source_id', true ) ) {
		return;
	}
	if ( isset( $_POST['spridisi_history_featured'] ) ) {
		$selected_ids = get_posts( array(
			'post_type' => 'page',
			'post_status' => array( 'publish', 'private', 'draft', 'pending' ),
			'post_parent' => $parent->ID,
			'meta_key' => '_spridisi_history_featured',
			'meta_value' => '1',
			'numberposts' => 4,
			'fields' => 'ids',
		) );
		$other_selected = array_filter( $selected_ids, static function ( $selected_id ) use ( $post_id ) {
			return (int) $selected_id !== (int) $post_id;
		} );
		if ( count( $other_selected ) >= 3 ) {
			set_transient( 'spridisi_history_featured_limit_' . get_current_user_id(), true, MINUTE_IN_SECONDS );
			return;
		}
		update_post_meta( $post_id, '_spridisi_history_featured', '1' );
	} else {
		delete_post_meta( $post_id, '_spridisi_history_featured' );
	}
}
add_action( 'save_post_page', 'spridisi_history_entry_save_featured', 10, 2 );

function spridisi_history_featured_limit_notice() {
	$key = 'spridisi_history_featured_limit_' . get_current_user_id();
	if ( get_transient( $key ) ) {
		delete_transient( $key );
		echo '<div class="notice notice-error"><p>' . esc_html__( 'Sākumlapā var izcelt ne vairāk kā trīs vēstures faktus. Noņem atzīmi kādam citam ierakstam un mēģini vēlreiz.', 'spridisi' ) . '</p></div>';
	}
}
add_action( 'admin_notices', 'spridisi_history_featured_limit_notice' );

function spridisi_history_archive_template( $template ) {
	if ( ! is_page() || ! get_post_meta( get_queried_object_id(), '_spridisi_history_import_state', true ) ) {
		return $template;
	}
	$archive_template = get_theme_file_path( '/page-templates/history-archive.php' );
	return file_exists( $archive_template ) ? $archive_template : $template;
}
add_filter( 'template_include', 'spridisi_history_archive_template', 99 );

function spridisi_history_image_manifest() {
	$cached = get_transient( 'spridisi_history_image_manifest' );
	if ( is_array( $cached ) ) {
		return $cached;
	}
	$response = wp_remote_get( 'https://spridisi.com/history/', array( 'timeout' => 20, 'redirection' => 3, 'limit_response_size' => 16777216 ) );
	if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
		return new WP_Error( 'history_images_unavailable', __( 'Avota lapas bildes pašlaik nav pieejamas.', 'spridisi' ) );
	}
	$document = new DOMDocument( '1.0', 'UTF-8' );
	$previous = libxml_use_internal_errors( true );
	$loaded = $document->loadHTML( '<?xml encoding="utf-8" ?>' . wp_remote_retrieve_body( $response ), LIBXML_HTML_NODEFDTD | LIBXML_NONET );
	libxml_clear_errors();
	libxml_use_internal_errors( $previous );
	if ( ! $loaded ) {
		return new WP_Error( 'history_images_parse_failed', __( 'Avota lapas bildes neizdevās nolasīt.', 'spridisi' ) );
	}
	$xpath = new DOMXPath( $document );
	$nodes = $xpath->query( '//img[contains(concat(" ", normalize-space(@class), " "), " pagelayer-img ")]' );
	$images = array();
	$seen = array();
	foreach ( $nodes as $node ) {
		$url = esc_url_raw( $node->getAttribute( 'src' ) );
		$parts = wp_parse_url( $url );
		$path = $parts['path'] ?? '';
		if ( ! in_array( $parts['host'] ?? '', array( 'spridisi.com', 'www.spridisi.com' ), true ) || 'https' !== ( $parts['scheme'] ?? '' ) || ! str_starts_with( $path, '/wp-content/uploads/' ) || isset( $seen[ $url ] ) ) {
			continue;
		}
		$filename = rawurldecode( wp_basename( $path ) );
		if ( 'pils.jpg' === strtolower( $filename ) ) {
			$year = '1337.gads';
		} elseif ( str_contains( strtolower( $filename ), 'vecelpils-skola' ) ) {
			$year = '1926.gads';
		} elseif ( str_contains( strtolower( $filename ), 'raina-klubs' ) ) {
			$year = '1927.gads';
		} elseif ( str_contains( $filename, '1966' ) ) {
			$year = '1966.gads';
		} elseif ( str_contains( $filename, '1973' ) ) {
			$year = '1973.gads';
		} else {
			continue;
		}
		$images[] = array( 'url' => $url, 'alt' => sanitize_text_field( $node->getAttribute( 'alt' ) ), 'year' => $year );
		$seen[ $url ] = true;
	}
	set_transient( 'spridisi_history_image_manifest', $images, 12 * HOUR_IN_SECONDS );
	return $images;
}

function spridisi_history_image_entry( $history_id, $year ) {
	foreach ( spridisi_content_children( $history_id ) as $entry ) {
		if ( get_the_title( $entry ) === $year ) {
			return $entry;
		}
	}
	$target_year = (int) $year;
	$previous_order = 0;
	$next_order = 0;
	foreach ( spridisi_content_children( $history_id ) as $entry ) {
		$title = get_the_title( $entry );
		if ( ! preg_match( '/^(\d{4})/', $title, $matches ) ) {
			continue;
		}
		$entry_year = (int) $matches[1];
		if ( $entry_year < $target_year ) {
			$previous_order = max( $previous_order, (int) $entry->menu_order );
		} elseif ( $entry_year > $target_year && ( ! $next_order || (int) $entry->menu_order < $next_order ) ) {
			$next_order = (int) $entry->menu_order;
		}
	}
	$order = $previous_order ? $previous_order + 1 : ( $next_order ? $next_order - 1 : 10 );
	$id = wp_insert_post( wp_slash( array(
		'post_type' => 'page',
		'post_status' => 'publish',
		'post_title' => $year,
		'post_name' => sanitize_title( $year . '-pictures' ),
		'post_parent' => $history_id,
		'menu_order' => $order,
		'post_content' => '',
	) ), true );
	if ( is_wp_error( $id ) ) {
		return $id;
	}
	update_post_meta( $id, '_spridisi_history_source_id', get_post_meta( $history_id, '_spridisi_history_source_id', true ) );
	return get_post( $id );
}

function spridisi_history_images_pending( $history_id ) {
	if ( 'complete' !== get_post_meta( $history_id, '_spridisi_history_import_state', true ) || 'complete' === get_post_meta( $history_id, '_spridisi_history_images_state', true ) ) {
		return false;
	}
	$images = spridisi_history_image_manifest();
	if ( is_wp_error( $images ) ) {
		return true;
	}
	foreach ( $images as $image ) {
		$entry = spridisi_history_image_entry_lookup( $history_id, $image['year'] );
		if ( ! $entry || ! in_array( $image['url'], get_post_meta( $entry->ID, '_spridisi_history_imported_images', true ) ?: array(), true ) ) {
			return true;
		}
	}
	return false;
}

function spridisi_history_image_entry_lookup( $history_id, $year ) {
	foreach ( spridisi_content_children( $history_id ) as $entry ) {
		if ( get_the_title( $entry ) === $year ) {
			return $entry;
		}
	}
	return null;
}

function spridisi_import_history_images() {
	$history_id = isset( $_REQUEST['history_archive_id'] ) ? absint( wp_unslash( $_REQUEST['history_archive_id'] ) ) : 0;
	if ( ! current_user_can( 'manage_options' ) || ! current_user_can( 'publish_pages' ) || ! $history_id || ! current_user_can( 'edit_post', $history_id ) ) {
		wp_die( esc_html__( 'Tev nav atļauts pārnest vēstures bildes.', 'spridisi' ) );
	}
	check_admin_referer( 'spridisi_import_history_images_' . $history_id );
	$history_page = get_post( $history_id );
	if ( ! $history_page || 'complete' !== get_post_meta( $history_id, '_spridisi_history_import_state', true ) ) {
		wp_die( esc_html__( 'Pilnā vēstures lapa nav atrasta.', 'spridisi' ) );
	}
	$images = spridisi_history_image_manifest();
	if ( is_wp_error( $images ) ) {
		wp_die( esc_html( $images->get_error_message() ) );
	}
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
	$imported = 0;
	$errors = array();
	foreach ( $images as $image ) {
		$entry = spridisi_history_image_entry( $history_id, $image['year'] );
		if ( is_wp_error( $entry ) ) {
			$errors[] = $entry->get_error_message();
			continue;
		}
		$imported_urls = get_post_meta( $entry->ID, '_spridisi_history_imported_images', true );
		$imported_urls = is_array( $imported_urls ) ? $imported_urls : array();
		if ( in_array( $image['url'], $imported_urls, true ) ) {
			continue;
		}
		$attachment_id = 0;
		$matches = get_posts( array(
			'post_type' => 'attachment',
			'post_status' => 'inherit',
			'meta_key' => '_spridisi_history_source_image_url',
			'meta_value' => $image['url'],
			'numberposts' => 1,
			'fields' => 'ids',
		) );
		if ( $matches ) {
			$attachment_id = (int) $matches[0];
		} else {
			$attachment_id = media_sideload_image( $image['url'], $entry->ID, $image['alt'], 'id' );
			if ( is_wp_error( $attachment_id ) ) {
				$errors[] = $image['url'] . ': ' . $attachment_id->get_error_message();
				continue;
			}
			update_post_meta( $attachment_id, '_spridisi_history_source_image_url', $image['url'] );
			if ( $image['alt'] ) {
				update_post_meta( $attachment_id, '_wp_attachment_image_alt', $image['alt'] );
			}
		}
		$markup = wp_get_attachment_image( $attachment_id, 'large', false, array( 'class' => 'history-entry-image', 'alt' => $image['alt'], 'loading' => 'lazy', 'decoding' => 'async' ) );
		if ( ! $markup ) {
			$errors[] = $image['url'] . ': ' . __( 'Bildes HTML neizdevās izveidot.', 'spridisi' );
			continue;
		}
		$result = wp_update_post( wp_slash( array( 'ID' => $entry->ID, 'post_content' => rtrim( $entry->post_content ) . "\n\n<figure class=\"wp-block-image\">" . $markup . '</figure>' ) ), true );
		if ( is_wp_error( $result ) ) {
			$errors[] = $image['url'] . ': ' . $result->get_error_message();
			continue;
		}
		$imported_urls[] = $image['url'];
		update_post_meta( $entry->ID, '_spridisi_history_imported_images', $imported_urls );
		++$imported;
	}
	if ( ! $errors ) {
		update_post_meta( $history_id, '_spridisi_history_images_state', 'complete' );
	}
	set_transient( 'spridisi_history_images_result_' . get_current_user_id(), array( 'imported' => $imported, 'errors' => $errors ), MINUTE_IN_SECONDS );
	wp_safe_redirect( admin_url( 'edit.php?post_type=page' ) );
	exit;
}
add_action( 'admin_post_spridisi_import_history_images', 'spridisi_import_history_images' );

function spridisi_history_images_admin_notice() {
	$key = 'spridisi_history_images_result_' . get_current_user_id();
	$result = get_transient( $key );
	if ( ! is_array( $result ) ) {
		return;
	}
	delete_transient( $key );
	$message = sprintf( __( 'Pārnesti %d attēli.', 'spridisi' ), (int) $result['imported'] );
	$class = empty( $result['errors'] ) ? 'notice-success' : 'notice-warning';
	echo '<div class="notice ' . esc_attr( $class ) . '"><p>' . esc_html( $message ) . '</p>';
	if ( ! empty( $result['errors'] ) ) {
		echo '<ul>';
		foreach ( $result['errors'] as $error ) {
			echo '<li>' . esc_html( $error ) . '</li>';
		}
		echo '</ul>';
	}
	echo '</div>';
}
add_action( 'admin_notices', 'spridisi_history_images_admin_notice' );