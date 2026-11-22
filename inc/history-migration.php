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