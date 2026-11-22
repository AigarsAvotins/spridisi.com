<?php
/**
 * Email-only booking requests. Acceptance is not booking confirmation.
 *
 * @package Spridisi
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function spridisi_request_page() {
	$pages = new WP_Query( array( 'post_type' => 'page', 'post_status' => 'publish', 'posts_per_page' => 1, 'no_found_rows' => true, 'meta_key' => '_wp_page_template', 'meta_value' => 'page-templates/booking.php', 'suppress_filters' => false ) );
	if ( $pages->posts ) {
		return $pages->posts[0];
	}
	$page = get_page_by_path( 'rezervacija', OBJECT, 'page' );
	return $page && 'publish' === $page->post_status ? $page : null;
}

function spridisi_reservation_link( $fallback = '' ) {
	if ( $fallback && '#kontakti' !== $fallback ) {
		return $fallback;
	}
	$page = spridisi_request_page();
	return $page ? get_permalink( $page ) : $fallback;
}

function spridisi_reservation_cta_label() {
	return __( 'Noskaidro cenu un rezervē', 'spridisi' );
}

function spridisi_request_events() {
	return array( 'wedding' => __( 'Kāzas', 'spridisi' ), 'birthday' => __( 'Dzimšanas diena', 'spridisi' ), 'family' => __( 'Ģimenes svētki', 'spridisi' ), 'corporate' => __( 'Korporatīvs pasākums', 'spridisi' ), 'stay' => __( 'Nakšņošana', 'spridisi' ), 'sauna' => __( 'Atpūta ar pirti', 'spridisi' ), 'other' => __( 'Cits', 'spridisi' ) );
}

function spridisi_request_spaces() {
	return array( 'large_hall' => __( 'Lielā zāle', 'spridisi' ), 'small_hall' => __( 'Mazā zāle', 'spridisi' ), 'stay' => __( 'Nakšņošana', 'spridisi' ), 'sauna' => __( 'Melnā pirts', 'spridisi' ), 'consultation' => __( 'Vēl nezinu, vēlos konsultāciju', 'spridisi' ) );
}

function spridisi_request_contacts() {
	return array( 'phone' => __( 'Pa tālruni', 'spridisi' ), 'email' => __( 'E-pastā', 'spridisi' ), 'any' => __( 'Nav nozīmes', 'spridisi' ) );
}

function spridisi_request_error_message() {
	return __( 'Pieprasījumu neizdevās nosūtīt. Lūdzu, pārbaudi ievadīto informāciju un mēģini vēlreiz.', 'spridisi' );
}

function spridisi_request_input( $input, $key, $textarea = false ) {
	$value = isset( $input[ $key ] ) && is_string( $input[ $key ] ) ? trim( $input[ $key ] ) : '';
	return $textarea ? sanitize_textarea_field( $value ) : sanitize_text_field( $value );
}

function spridisi_request_valid_date( $value ) {
	if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/D', $value ) ) {
		return false;
	}
	$date = DateTimeImmutable::createFromFormat( '!Y-m-d', $value, wp_timezone() );
	return $date && $date->format( 'Y-m-d' ) === $value;
}

function spridisi_request_validate( $input ) {
	$data = array();
	$errors = array();
	foreach ( array( 'name', 'email', 'phone', 'start_date', 'end_date', 'guests', 'overnight_guests', 'event_type', 'event_other', 'contact_method' ) as $key ) {
		$data[ $key ] = spridisi_request_input( $input, $key );
	}
	$data['information'] = spridisi_request_input( $input, 'information', true );
	$data['consent'] = isset( $input['consent'] ) && '1' === $input['consent'];
	$spaces = isset( $input['spaces'] ) && is_array( $input['spaces'] ) ? $input['spaces'] : array();
	$data['spaces'] = array_values( array_unique( array_filter( $spaces, 'is_string' ) ) );
	if ( '' === $data['name'] || preg_match_all( '/./us', $data['name'] ) > 150 ) {
		$errors['name'] = __( 'Norādi vārdu un uzvārdu (līdz 150 rakstzīmēm).', 'spridisi' );
	}
	$raw_email = isset( $input['email'] ) && is_string( $input['email'] ) ? trim( $input['email'] ) : '';
	if ( preg_match( '/[\r\n]/', $raw_email ) || strlen( $raw_email ) > 254 || ! is_email( $raw_email ) ) {
		$errors['email'] = __( 'Norādi derīgu e-pasta adresi.', 'spridisi' );
	} else {
		$data['email'] = sanitize_email( $raw_email );
	}
	$digits = preg_replace( '/\D/', '', $data['phone'] );
	if ( ! preg_match( '/^[+0-9().\s-]+$/D', $data['phone'] ) || strlen( $digits ) < 7 || strlen( $digits ) > 20 || strlen( $data['phone'] ) > 60 ) {
		$errors['phone'] = __( 'Norādi derīgu tālruni, vajadzības gadījumā ar valsts kodu.', 'spridisi' );
	}
	$today = current_datetime()->format( 'Y-m-d' );
	if ( ! spridisi_request_valid_date( $data['start_date'] ) || $data['start_date'] < $today ) {
		$errors['start_date'] = __( 'Norādi šodienas vai vēlāku sākuma datumu.', 'spridisi' );
	}
	if ( ! spridisi_request_valid_date( $data['end_date'] ) || ( ! isset( $errors['start_date'] ) && $data['end_date'] < $data['start_date'] ) ) {
		$errors['end_date'] = __( 'Beigu datums nedrīkst būt agrāks par sākuma datumu.', 'spridisi' );
	}
	if ( false === filter_var( $data['guests'], FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 1 ) ) ) ) {
		$errors['guests'] = __( 'Norādi veselu viesu skaitu, kas nav mazāks par 1.', 'spridisi' );
	}
	if ( '' !== $data['overnight_guests'] && false === filter_var( $data['overnight_guests'], FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 0, 'max_range' => 40 ) ) ) ) {
		$errors['overnight_guests'] = __( 'Norādi veselu nakšņotāju skaitu no 0 līdz 40.', 'spridisi' );
	}
	if ( in_array( 'stay', $data['spaces'], true ) && '' === $data['overnight_guests'] ) {
		$errors['overnight_guests'] = __( 'Norādi nakšņotāju skaitu.', 'spridisi' );
	}
	if ( '' !== $data['overnight_guests'] && (int) $data['overnight_guests'] > 0 && ! in_array( 'stay', $data['spaces'], true ) ) {
		$errors['overnight_guests'] = __( 'Lai norādītu nakšņotāju skaitu, izvēlies nakšņošanu.', 'spridisi' );
	}
	if ( ! isset( spridisi_request_events()[ $data['event_type'] ] ) ) {
		$errors['event_type'] = __( 'Izvēlies pasākuma veidu.', 'spridisi' );
	}
	if ( 'other' === $data['event_type'] && ( '' === $data['event_other'] || preg_match_all( '/./us', $data['event_other'] ) > 200 ) ) {
		$errors['event_other'] = __( 'Norādi pasākuma veidu (līdz 200 rakstzīmēm).', 'spridisi' );
	}
	if ( ! $data['spaces'] || array_diff( $data['spaces'], array_keys( spridisi_request_spaces() ) ) ) {
		$errors['spaces'] = __( 'Izvēlies vismaz vienu telpu vai piedāvājumu.', 'spridisi' );
		$data['spaces'] = array_values( array_intersect( $data['spaces'], array_keys( spridisi_request_spaces() ) ) );
	}
	if ( $data['contact_method'] && ! isset( spridisi_request_contacts()[ $data['contact_method'] ] ) ) {
		$errors['contact_method'] = __( 'Izvēlies saziņas veidu.', 'spridisi' );
	}
	if ( preg_match_all( '/./us', $data['information'] ) > 2000 ) {
		$errors['information'] = __( 'Papildu informācija nedrīkst pārsniegt 2000 rakstzīmes.', 'spridisi' );
	}
	if ( ! $data['consent'] ) {
		$errors['consent'] = __( 'Lai nosūtītu pieprasījumu, nepieciešama piekrišana datu apstrādei.', 'spridisi' );
	}
	return array( 'values' => $data, 'errors' => $errors );
}

function spridisi_request_ticket( $page_id ) {
	$ticket = array( 'token' => bin2hex( random_bytes( 16 ) ), 'started' => time() );
	$browser = isset( $_COOKIE['spridisi_request_browser'] ) && is_string( $_COOKIE['spridisi_request_browser'] ) ? $_COOKIE['spridisi_request_browser'] : '';
	$ticket['signature'] = hash_hmac( 'sha256', $page_id . '|' . $ticket['started'] . '|' . $ticket['token'] . '|' . $browser, wp_salt( 'nonce' ) );
	return $ticket;
}

function spridisi_request_ticket_valid( $input, $page_id ) {
	$token = spridisi_request_input( $input, 'request_token' );
	$started = spridisi_request_input( $input, 'request_started' );
	$signature = spridisi_request_input( $input, 'request_signature' );
	$browser = isset( $_COOKIE['spridisi_request_browser'] ) && is_string( $_COOKIE['spridisi_request_browser'] ) ? $_COOKIE['spridisi_request_browser'] : '';
	if ( ! preg_match( '/^[a-f0-9]{64}$/D', $browser ) || ! preg_match( '/^[a-f0-9]{32}$/D', $token ) || ! ctype_digit( $started ) || ! preg_match( '/^[a-f0-9]{64}$/D', $signature ) ) {
		return false;
	}
	$age = time() - (int) $started;
	return $age >= 3 && $age <= 2 * HOUR_IN_SECONDS && hash_equals( hash_hmac( 'sha256', $page_id . '|' . $started . '|' . $token . '|' . $browser, wp_salt( 'nonce' ) ), $signature );
}

function spridisi_request_browser_cookie() {
	$value = isset( $_COOKIE['spridisi_request_browser'] ) && is_string( $_COOKIE['spridisi_request_browser'] ) ? $_COOKIE['spridisi_request_browser'] : '';
	if ( ! preg_match( '/^[a-f0-9]{64}$/D', $value ) ) {
		$value = bin2hex( random_bytes( 32 ) );
		$path = wp_parse_url( home_url( '/' ), PHP_URL_PATH );
		setcookie( 'spridisi_request_browser', $value, array( 'expires' => time() + 2 * HOUR_IN_SECONDS, 'path' => $path ? $path : '/', 'secure' => is_ssl(), 'httponly' => true, 'samesite' => 'Lax' ) );
		$_COOKIE['spridisi_request_browser'] = $value;
	}
}

function spridisi_request_mail( $recipient, $subject, $body, $headers ) {
	try {
		return wp_mail( $recipient, $subject, $body, $headers );
	} catch ( Throwable $error ) {
		return false;
	}
}

function spridisi_request_cleanup_lock( $key ) {
	if ( preg_match( '/^spridisi_request_[a-f0-9]{64}$/D', $key ) ) {
		delete_option( $key );
	}
}
add_action( 'spridisi_request_cleanup', 'spridisi_request_cleanup_lock' );

function spridisi_request_receipt( $page_id, $token ) {
	return $token . '.' . hash_hmac( 'sha256', 'receipt|' . $page_id . '|' . $token, wp_salt( 'auth' ) );
}

function spridisi_request_received( $page_id ) {
	$cookie = isset( $_COOKIE['spridisi_request_receipt'] ) && is_string( $_COOKIE['spridisi_request_receipt'] ) ? wp_unslash( $_COOKIE['spridisi_request_receipt'] ) : '';
	$parts = explode( '.', $cookie );
	return count( $parts ) === 2 && preg_match( '/^[a-f0-9]{32}$/D', $parts[0] ) && hash_equals( spridisi_request_receipt( $page_id, $parts[0] ), $cookie ) && 'sent' === get_option( 'spridisi_request_' . hash( 'sha256', $parts[0] ) );
}

function spridisi_request_rate_allowed() {
	$address = isset( $_SERVER['REMOTE_ADDR'] ) && is_string( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 'unknown';
	$key = 'spridisi_request_rate_' . hash_hmac( 'sha256', $address, wp_salt( 'nonce' ) );
	$count = (int) get_transient( $key );
	if ( $count >= 5 ) {
		return false;
	}
	set_transient( $key, $count + 1, 15 * MINUTE_IN_SECONDS );
	return true;
}

function spridisi_request_send_mail( $data, $page_id ) {
	$recipient = sanitize_email( get_option( 'admin_email' ) );
	if ( ! is_email( $recipient ) ) {
		return false;
	}
	$event = spridisi_request_events()[ $data['event_type'] ];
	if ( 'other' === $data['event_type'] ) {
		$event .= ': ' . $data['event_other'];
	}
	$spaces = array_map( static function ( $key ) { return spridisi_request_spaces()[ $key ]; }, $data['spaces'] );
	$contact = spridisi_request_contacts()[ $data['contact_method'] ] ?? __( 'Nav nozīmes', 'spridisi' );
	$lines = array(
		__( 'Jauns rezervācijas pieprasījums. Rezervācija nav apstiprināta.', 'spridisi' ),
		'',
		__( 'Saņemts:', 'spridisi' ) . ' ' . wp_date( 'Y-m-d H:i:s T' ),
		__( 'Vārds, uzvārds:', 'spridisi' ) . ' ' . $data['name'],
		__( 'E-pasts:', 'spridisi' ) . ' ' . $data['email'],
		__( 'Tālrunis:', 'spridisi' ) . ' ' . $data['phone'],
		__( 'Ierašanās vai pasākuma sākuma datums:', 'spridisi' ) . ' ' . $data['start_date'],
		__( 'Izbraukšanas vai pasākuma beigu datums:', 'spridisi' ) . ' ' . $data['end_date'],
		__( 'Plānotais viesu skaits:', 'spridisi' ) . ' ' . $data['guests'],
		__( 'Nakšņotāju skaits:', 'spridisi' ) . ' ' . ( '' === $data['overnight_guests'] ? '0' : $data['overnight_guests'] ),
		__( 'Pasākuma veids:', 'spridisi' ) . ' ' . $event,
		__( 'Vēlamā telpa vai piedāvājums:', 'spridisi' ) . ' ' . implode( ', ', $spaces ),
		__( 'Kā visērtāk ar Tevi sazināties?', 'spridisi' ) . ' ' . $contact,
		__( 'Papildu informācija:', 'spridisi' ),
		$data['information'],
		'',
		__( 'Lapa:', 'spridisi' ) . ' ' . get_permalink( $page_id ),
		__( 'Valoda:', 'spridisi' ) . ' ' . get_locale(),
		__( 'Datu apstrādes piekrišana: jā.', 'spridisi' ),
	);
	$headers = array( 'Content-Type: text/plain; charset=UTF-8', 'Reply-To: ' . $data['email'] );
	if ( ! spridisi_request_mail( $recipient, __( 'Jauns rezervācijas pieprasījums — Sprīdīši', 'spridisi' ), implode( "\n", $lines ), $headers ) ) {
		return false;
	}
	$acknowledgement = sprintf(
		__( "Labdien!\n\nPaldies par interesi par Sprīdīšiem. Esam saņēmuši Tavu rezervācijas pieprasījumu.\n\nVēlamie datumi:\n%1\$s–%2\$s\n\nViesu skaits:\n%3\$s\n\nPasākuma veids:\n%4\$s\n\nPārbaudīsim pieejamību un sazināsimies ar Tevi.\n\nSvarīgi: šis e-pasts vēl neapstiprina rezervāciju. Rezervācija būs apstiprināta tikai pēc atsevišķas vienošanās.\n\nSprīdīši\nVecsēlpils, Sēlija", 'spridisi' ),
		$data['start_date'], $data['end_date'], $data['guests'], $event
	);
	spridisi_request_mail( $data['email'], __( 'Sprīdīši — rezervācijas pieprasījums saņemts', 'spridisi' ), $acknowledgement, array( 'Content-Type: text/plain; charset=UTF-8' ) );
	return true;
}

function spridisi_request_process( $input, $page_id ) {
	$state = spridisi_request_validate( $input );
	$nonce = spridisi_request_input( $input, '_spridisi_nonce' );
	$origin = isset( $_SERVER['HTTP_ORIGIN'] ) && is_string( $_SERVER['HTTP_ORIGIN'] ) ? $_SERVER['HTTP_ORIGIN'] : '';
	$origin_parts = wp_parse_url( $origin );
	$home_parts = wp_parse_url( home_url( '/' ) );
	$same_origin = ! $origin || ( is_array( $origin_parts ) && ( $origin_parts['scheme'] ?? '' ) === ( $home_parts['scheme'] ?? '' ) && ( $origin_parts['host'] ?? '' ) === ( $home_parts['host'] ?? '' ) && ( $origin_parts['port'] ?? null ) === ( $home_parts['port'] ?? null ) );
	if ( ! wp_verify_nonce( $nonce, 'spridisi_request_' . $page_id ) || ! $same_origin || ! spridisi_request_ticket_valid( $input, $page_id ) || spridisi_request_input( $input, 'website' ) ) {
		$state['errors']['form'] = spridisi_request_error_message();
		return $state;
	}
	$token = spridisi_request_input( $input, 'request_token' );
	$key = 'spridisi_request_' . hash( 'sha256', $token );
	if ( 'sent' === get_option( $key ) ) {
		$state['accepted'] = true;
		$state['token'] = $token;
		return $state;
	}
	if ( ! spridisi_request_rate_allowed() ) {
		$state['errors']['form'] = spridisi_request_error_message();
		return $state;
	}
	if ( $state['errors'] ) {
		return $state;
	}
	if ( ! add_option( $key, 'pending', '', false ) ) {
		$state['errors']['form'] = spridisi_request_error_message();
		return $state;
	}
	wp_schedule_single_event( time() + 2 * HOUR_IN_SECONDS, 'spridisi_request_cleanup', array( $key ) );
	if ( ! spridisi_request_send_mail( $state['values'], $page_id ) ) {
		delete_option( $key );
		$state['errors']['form'] = spridisi_request_error_message();
		return $state;
	}
	update_option( $key, 'sent', false );
	$state['accepted'] = true;
	$state['token'] = $token;
	return $state;
}

function spridisi_request_route() {
	if ( ! is_page_template( 'page-templates/booking.php' ) ) {
		return;
	}
	if ( ! defined( 'DONOTCACHEPAGE' ) ) {
		define( 'DONOTCACHEPAGE', true );
	}
	nocache_headers();
	if ( 'POST' !== ( $_SERVER['REQUEST_METHOD'] ?? 'GET' ) ) {
		spridisi_request_browser_cookie();
		return;
	}
	if ( (int) ( $_SERVER['CONTENT_LENGTH'] ?? 0 ) > 32768 ) {
		$GLOBALS['spridisi_request_state'] = array( 'values' => array(), 'errors' => array( 'form' => spridisi_request_error_message() ) );
		return;
	}
	$page_id = get_queried_object_id();
	$state = spridisi_request_process( wp_unslash( $_POST ), $page_id );
	if ( ! empty( $state['accepted'] ) ) {
		$path = wp_parse_url( get_permalink( $page_id ), PHP_URL_PATH );
		setcookie( 'spridisi_request_receipt', spridisi_request_receipt( $page_id, $state['token'] ), array( 'expires' => time() + 15 * MINUTE_IN_SECONDS, 'path' => $path ? $path : '/', 'secure' => is_ssl(), 'httponly' => true, 'samesite' => 'Lax' ) );
		wp_safe_redirect( add_query_arg( 'request', 'received', get_permalink( $page_id ) ) . '#booking-status', 303 );
		exit;
	}
	$GLOBALS['spridisi_request_state'] = $state;
	spridisi_request_browser_cookie();
}
add_action( 'template_redirect', 'spridisi_request_route' );

function spridisi_request_field( $key, $label, $type, $required, $attributes = array(), $help = '' ) {
	$state = $GLOBALS['spridisi_request_state'] ?? array( 'values' => array(), 'errors' => array() );
	$value = $state['values'][ $key ] ?? '';
	$error = $state['errors'][ $key ] ?? '';
	$id = 'request-' . $key;
	echo '<div class="request-field"><label for="' . esc_attr( $id ) . '">' . esc_html( $label );
	if ( $required ) {
		echo ' <span class="request-required">' . esc_html__( '(obligāti)', 'spridisi' ) . '</span>';
	}
	echo '</label>';
	$descriptions = array();
	if ( $help ) {
		$descriptions[] = $id . '-help';
	}
	if ( $error ) {
		$descriptions[] = $id . '-error';
	}
	$attributes = array_merge( array( 'id' => $id, 'name' => $key ), $attributes );
	if ( $required ) {
		$attributes['required'] = 'required';
	}
	if ( $descriptions ) {
		$attributes['aria-describedby'] = implode( ' ', $descriptions );
	}
	if ( $error ) {
		$attributes['aria-invalid'] = 'true';
	}
	$textarea = 'textarea' === $type;
	$select = 'event_type' === $key;
	echo $textarea ? '<textarea' : ( $select ? '<select' : '<input type="' . esc_attr( $type ) . '" value="' . esc_attr( $value ) . '"' );
	foreach ( $attributes as $attribute => $attribute_value ) {
		printf( ' %s="%s"', esc_attr( $attribute ), esc_attr( $attribute_value ) );
	}
	echo '>';
	if ( $textarea ) {
		echo esc_textarea( $value ) . '</textarea>';
	} elseif ( $select ) {
		echo '<option value="">' . esc_html__( 'Izvēlies pasākuma veidu', 'spridisi' ) . '</option>';
		foreach ( spridisi_request_events() as $option => $text ) {
			echo '<option value="' . esc_attr( $option ) . '"' . selected( $value, $option, false ) . '>' . esc_html( $text ) . '</option>';
		}
		echo '</select>';
	}
	if ( $help ) {
		echo '<p class="request-help" id="' . esc_attr( $id . '-help' ) . '">' . esc_html( $help ) . '</p>';
	}
	if ( $error ) {
		echo '<p class="request-error" id="' . esc_attr( $id . '-error' ) . '">' . esc_html( $error ) . '</p>';
	}
	echo '</div>';
}

function spridisi_request_form() {
	$page_id = get_queried_object_id();
	if ( isset( $_GET['request'] ) && 'received' === $_GET['request'] && spridisi_request_received( $page_id ) ) {
		echo '<div id="booking-status" class="request-status" role="status" tabindex="-1"><h2>' . esc_html__( 'Paldies! Pieprasījums ir saņemts.', 'spridisi' ) . '</h2><p>' . esc_html__( 'Pārbaudīsim norādīto informāciju un sazināsimies ar Tevi. Rezervācija būs apstiprināta tikai pēc atsevišķas vienošanās.', 'spridisi' ) . '</p></div>';
		return;
	}
	$state = $GLOBALS['spridisi_request_state'] ?? array( 'values' => array(), 'errors' => array() );
	$values = $state['values'];
	$errors = $state['errors'];
	if ( $errors ) {
		echo '<div id="booking-status" class="request-status request-status--error" role="alert" tabindex="-1"><h2>' . esc_html( spridisi_request_error_message() ) . '</h2><ul>';
		foreach ( $errors as $field => $error ) {
			echo '<li>' . ( 'form' === $field ? esc_html( $error ) : '<a href="#request-' . esc_attr( $field ) . '">' . esc_html( $error ) . '</a>' ) . '</li>';
		}
		echo '</ul></div>';
	}
	$ticket = spridisi_request_ticket( $page_id );
	$today = current_datetime()->format( 'Y-m-d' );
	echo '<form class="booking-request-form" method="post" action="' . esc_url( get_permalink( $page_id ) ) . '" data-today="' . esc_attr( $today ) . '">';
	wp_nonce_field( 'spridisi_request_' . $page_id, '_spridisi_nonce', false );
	foreach ( $ticket as $key => $value ) {
		printf( '<input type="hidden" name="request_%s" value="%s">', esc_attr( $key ), esc_attr( $value ) );
	}
	echo '<div class="request-trap" aria-hidden="true"><label for="request-website">' . esc_html__( 'Mājaslapa', 'spridisi' ) . '</label><input id="request-website" type="text" name="website" tabindex="-1" autocomplete="off"></div>';
	echo '<fieldset><legend>' . esc_html__( 'Kontaktinformācija', 'spridisi' ) . '</legend>';
	spridisi_request_field( 'name', __( 'Vārds, uzvārds', 'spridisi' ), 'text', true, array( 'autocomplete' => 'name', 'maxlength' => 150 ) );
	spridisi_request_field( 'email', __( 'E-pasts', 'spridisi' ), 'email', true, array( 'autocomplete' => 'email', 'maxlength' => 254 ) );
	spridisi_request_field( 'phone', __( 'Tālrunis', 'spridisi' ), 'tel', true, array( 'autocomplete' => 'tel', 'maxlength' => 60 ) );
	echo '</fieldset><fieldset><legend>' . esc_html__( 'Datumi un viesi', 'spridisi' ) . '</legend><div class="request-dates">';
	spridisi_request_field( 'start_date', __( 'Ierašanās vai pasākuma sākuma datums', 'spridisi' ), 'date', true, array( 'min' => $today ) );
	spridisi_request_field( 'end_date', __( 'Izbraukšanas vai pasākuma beigu datums', 'spridisi' ), 'date', true, array( 'min' => $today ) );
	echo '</div>';
	spridisi_request_field( 'guests', __( 'Plānotais viesu skaits', 'spridisi' ), 'number', true, array( 'min' => 1, 'step' => 1, 'inputmode' => 'numeric' ) );
	echo '</fieldset><fieldset><legend>' . esc_html__( 'Pasākums', 'spridisi' ) . '</legend>';
	spridisi_request_field( 'event_type', __( 'Pasākuma veids', 'spridisi' ), 'select', true );
	echo '<div class="request-other">';
	spridisi_request_field( 'event_other', __( 'Norādi pasākuma veidu', 'spridisi' ), 'text', false, array( 'maxlength' => 200 ), __( 'Jāaizpilda, ja izvēlēts pasākuma veids “Cits”.', 'spridisi' ) );
	echo '</div></fieldset><fieldset id="request-spaces" tabindex="-1"' . ( isset( $errors['spaces'] ) ? ' aria-describedby="request-spaces-error" aria-invalid="true"' : '' ) . '><legend>' . esc_html__( 'Vēlamā telpa vai piedāvājums', 'spridisi' ) . ' <span class="request-required">' . esc_html__( '(obligāti)', 'spridisi' ) . '</span></legend>';
	$hints = array( 'large_hall' => __( '220 m², līdz 80 viesiem.', 'spridisi' ), 'small_hall' => __( '50 m², piemērota aptuveni 10–15 cilvēkiem.', 'spridisi' ), 'stay' => __( 'līdz 40 viesiem.', 'spridisi' ) );
	foreach ( spridisi_request_spaces() as $key => $label ) {
		$id = 'request-space-' . $key;
		echo '<label class="request-choice" for="' . esc_attr( $id ) . '"><input type="checkbox" id="' . esc_attr( $id ) . '" name="spaces[]" value="' . esc_attr( $key ) . '"' . checked( in_array( $key, $values['spaces'] ?? array(), true ), true, false ) . '><span>' . esc_html( $label );
		if ( isset( $hints[ $key ] ) ) {
			echo '<small>' . esc_html( $hints[ $key ] ) . '</small>';
		}
		echo '</span></label>';
		if ( 'stay' === $key ) {
			echo '<div class="request-overnight-field"' . ( in_array( 'stay', $values['spaces'] ?? array(), true ) ? '' : ' hidden' ) . '>';
			spridisi_request_field( 'overnight_guests', __( 'Nakšņotāju skaits', 'spridisi' ), 'number', false, array( 'min' => 0, 'max' => 40, 'step' => 1, 'inputmode' => 'numeric' ), __( 'Norādi skaitu, ja izvēlēta nakšņošana.', 'spridisi' ) );
			echo '</div>';
		}
	}
	echo '<div class="request-estimate" hidden><div role="status" aria-live="polite" aria-atomic="true"><p class="request-estimate-total"><strong>' . esc_html__( 'Aptuvenā summa', 'spridisi' ) . '</strong>: <output id="request-estimate-amount">—</output></p></div><p>' . esc_html__( 'Summa ir aptuvena un tiks precizēta pēc rezervācijas apstiprināšanas.', 'spridisi' ) . '</p></div>';
	if ( isset( $errors['spaces'] ) ) {
		echo '<p class="request-error" id="request-spaces-error">' . esc_html( $errors['spaces'] ) . '</p>';
	}
	echo '</fieldset><fieldset><legend>' . esc_html__( 'Papildu informācija', 'spridisi' ) . '</legend>';
	spridisi_request_field( 'information', __( 'Papildu informācija', 'spridisi' ), 'textarea', false, array( 'maxlength' => 2000, 'rows' => 5 ), __( 'Pastāsti par plānoto pasākumu, vēlamo norises laiku vai citiem jautājumiem.', 'spridisi' ) );
	echo '</fieldset><fieldset><legend>' . esc_html__( 'Kā visērtāk ar Tevi sazināties?', 'spridisi' ) . '</legend>';
	foreach ( spridisi_request_contacts() as $key => $label ) {
		echo '<label class="request-choice"><input type="radio" name="contact_method" value="' . esc_attr( $key ) . '"' . checked( $values['contact_method'] ?? 'any', $key, false ) . '><span>' . esc_html( $label ) . '</span></label>';
	}
	echo '</fieldset><div class="request-consent"><label class="request-choice" for="request-consent"><input id="request-consent" type="checkbox" name="consent" value="1" required' . checked( $values['consent'] ?? false, true, false ) . ( isset( $errors['consent'] ) ? ' aria-invalid="true" aria-describedby="request-consent-error"' : '' ) . '><span>' . esc_html__( 'Piekrītu, ka Sprīdīši izmanto manus norādītos datus, lai apstrādātu rezervācijas pieprasījumu un sazinātos ar mani.', 'spridisi' ) . ' <span class="request-required">' . esc_html__( '(obligāti)', 'spridisi' ) . '</span></span></label>';
	$privacy = get_privacy_policy_url();
	if ( $privacy ) {
		echo '<p><a href="' . esc_url( $privacy ) . '">' . esc_html__( 'Privātuma politika', 'spridisi' ) . '</a></p>';
	}
	if ( isset( $errors['consent'] ) ) {
		echo '<p class="request-error" id="request-consent-error">' . esc_html( $errors['consent'] ) . '</p>';
	}
	echo '</div><button class="button request-submit" type="submit">' . esc_html( spridisi_reservation_cta_label() ) . '</button></form>';
}

function spridisi_request_page_content() {
	$blocks = parse_blocks( get_the_content() );
	$structured = false;
	foreach ( $blocks as $block ) {
		if ( ! spridisi_block_has_class( $block, 'spridisi-booking-content' ) ) {
			continue;
		}
		$structured = true;
		foreach ( $block['innerBlocks'] as $field ) {
			if ( spridisi_block_has_class( $field, 'spridisi-booking-availability' ) ) {
				continue;
			}
			if ( spridisi_block_has_class( $field, 'spridisi-field-title' ) ) {
				echo '<h1>' . esc_html( wp_strip_all_tags( render_block( $field ) ) ) . '</h1>';
			} else {
				echo render_block( $field );
			}
		}
	}
	if ( ! $structured ) {
		the_title( '<h1>', '</h1>' );
		the_content();
		echo '<p class="booking-notice">' . esc_html__( 'Pieprasījuma nosūtīšana vēl neapstiprina rezervāciju.', 'spridisi' ) . '</p>';
	}
}

/*
 * Future availability: WordPress availability layer <-> secure server-side Google
 * Calendar API <-> a dedicated availability-only calendar. Keep OAuth/service-account
 * credentials outside theme/Git; read free/busy only, cache results, tolerate outages,
 * use Europe/Riga, recheck server-side before any confirmation, prevent overlaps,
 * and retain manual approval. Never expose private event details or trust client data.
 */
function spridisi_request_starter_content() {
	$notice = spridisi_seed_text( 'notice', 'Pieprasījuma nosūtīšana vēl neapstiprina rezervāciju.' );
	$availability = spridisi_seed_group( 'spridisi-booking-availability', __( 'Future availability — editor only, not public', 'spridisi' ), array( spridisi_seed_text( 'title', 'Pieejamība', true ) ) );
	return serialize_blocks( array( spridisi_seed_group( 'spridisi-booking-content', __( 'Booking request page content', 'spridisi' ), array( spridisi_seed_text( 'eyebrow', 'REZERVĀCIJAS PIEPRASĪJUMS' ), spridisi_seed_text( 'title', 'Plāno svētkus Sprīdīšos', true ), spridisi_seed_text( 'intro', 'Norādi vēlamo datumu, viesu skaitu un pasākuma veidu. Pēc pieprasījuma saņemšanas pārbaudīsim pieejamību un sazināsimies ar Tevi.' ), $notice, $availability ) ) ) );
}

function spridisi_request_editor_settings( $settings, $context ) {
	if ( ! empty( $context->post ) && str_contains( $context->post->post_content, 'spridisi-booking-content' ) ) {
		$settings['canLockBlocks'] = false;
		$settings['codeEditingEnabled'] = false;
		$settings['templateLock'] = 'all';
	}
	return $settings;
}
add_filter( 'block_editor_settings_all', 'spridisi_request_editor_settings', 10, 2 );

function spridisi_request_setup_notice() {
	$page = spridisi_request_page();
	if ( ! current_user_can( 'manage_options' ) || ! current_user_can( 'publish_pages' ) || ( $page && 'page-templates/booking.php' === get_page_template_slug( $page ) ) ) {
		return;
	}
	echo '<div class="notice notice-info"><p>' . esc_html__( 'Sprīdīši: create or reuse the editable booking request page. Existing page content is preserved. Requests are reviewed manually; email delivery must be configured and tested before launch.', 'spridisi' ) . '</p><form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
	wp_nonce_field( 'spridisi_setup_request' );
	echo '<input type="hidden" name="action" value="spridisi_setup_request">';
	submit_button( __( 'Set up booking request page', 'spridisi' ), 'secondary', 'submit', false );
	echo '</form><p></p></div>';
}
add_action( 'admin_notices', 'spridisi_request_setup_notice' );

function spridisi_setup_request() {
	if ( ! current_user_can( 'manage_options' ) || ! current_user_can( 'publish_pages' ) ) {
		wp_die( esc_html__( 'You cannot set up this page.', 'spridisi' ) );
	}
	check_admin_referer( 'spridisi_setup_request' );
	$page = spridisi_request_page();
	$page = $page ? $page : get_page_by_path( 'rezervacija', OBJECT, 'page' );
	if ( $page && ! current_user_can( 'edit_post', $page->ID ) ) {
		wp_die( esc_html__( 'You cannot edit the existing page.', 'spridisi' ) );
	}
	if ( $page && ! in_array( get_page_template_slug( $page ), array( '', 'default', 'page-templates/booking.php' ), true ) ) {
		wp_die( esc_html__( 'The existing page uses a custom template. Review it and select the booking request template manually; its content has not been changed.', 'spridisi' ) );
	}
	if ( ! $page || '' === trim( $page->post_content ) ) {
		$data = array( 'post_type' => 'page', 'post_status' => $page ? $page->post_status : 'publish', 'post_name' => 'rezervacija', 'post_title' => $page ? $page->post_title : 'Rezervācija', 'post_content' => spridisi_request_starter_content() );
		if ( $page ) {
			$data['ID'] = $page->ID;
		}
		$id = wp_insert_post( wp_slash( $data ), true );
		if ( is_wp_error( $id ) ) {
			wp_die( esc_html( $id->get_error_message() ) );
		}
		$page = get_post( $id );
	}
	update_post_meta( $page->ID, '_wp_page_template', 'page-templates/booking.php' );
	wp_safe_redirect( get_edit_post_link( $page->ID, 'raw' ) );
	exit;
}
add_action( 'admin_post_spridisi_setup_request', 'spridisi_setup_request' );

function spridisi_request_assets() {
	if ( ! is_page_template( 'page-templates/booking.php' ) ) {
		return;
	}
	wp_enqueue_style( 'spridisi-booking', get_theme_file_uri( '/assets/css/booking.css' ), array( 'spridisi-main' ), (string) filemtime( get_theme_file_path( '/assets/css/booking.css' ) ) );
	wp_enqueue_script( 'spridisi-booking', get_theme_file_uri( '/assets/js/booking.js' ), array(), (string) filemtime( get_theme_file_path( '/assets/js/booking.js' ) ), array( 'strategy' => 'defer', 'in_footer' => true ) );
	wp_localize_script( 'spridisi-booking', 'spridisiRequest', array(
		'required' => __( 'Lūdzu, aizpildi šo lauku.', 'spridisi' ),
		'email' => __( 'Norādi derīgu e-pasta adresi.', 'spridisi' ),
		'phone' => __( 'Norādi derīgu tālruni, vajadzības gadījumā ar valsts kodu.', 'spridisi' ),
		'past' => __( 'Norādi šodienas vai vēlāku sākuma datumu.', 'spridisi' ),
		'end' => __( 'Beigu datums nedrīkst būt agrāks par sākuma datumu.', 'spridisi' ),
		'guests' => __( 'Norādi veselu viesu skaitu, kas nav mazāks par 1.', 'spridisi' ),
		'overnightGuests' => __( 'Norādi veselu nakšņotāju skaitu no 0 līdz 40.', 'spridisi' ),
		'overnightRequired' => __( 'Norādi nakšņotāju skaitu.', 'spridisi' ),
		'overnightStay' => __( 'Lai norādītu nakšņotāju skaitu, izvēlies nakšņošanu.', 'spridisi' ),
		'spaces' => __( 'Izvēlies vismaz vienu telpu vai piedāvājumu.', 'spridisi' ),
		'prices' => array( 'smallHall' => array( 'upTo15' => 150, 'upTo25' => 180 ), 'largeHall' => array( 'upTo30' => 250, 'upTo40' => 300, 'over40' => 350 ), 'wedding' => 400, 'sauna' => 80, 'overnight' => 12 ),
		'sending' => __( 'Nosūta…', 'spridisi' ),
		'submit' => spridisi_reservation_cta_label(),
	) );
}
add_action( 'wp_enqueue_scripts', 'spridisi_request_assets' );