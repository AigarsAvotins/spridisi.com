/**
 * Contact Widget Functionality
 */

document.addEventListener( 'DOMContentLoaded', function() {
	const widget = document.getElementById( 'contact-widget' );
	if ( ! widget ) {
		return;
	}

	const toggle = widget.querySelector( '.contact-widget__toggle' );
	const menu = widget.querySelector( '.contact-widget__menu' );
	const closeBtn = widget.querySelector( '.contact-widget__close' );
	const messageToggle = widget.querySelector( '.contact-widget__message-toggle' );
	const messageForm = widget.querySelector( '.contact-widget__message-form' );
	const messageClose = widget.querySelector( '.contact-widget__message-close' );
	const delay = parseInt( widget.dataset.delay ) || 7000;
	const siteEmail = widget.dataset.email;

	// Hide widget initially
	widget.setAttribute( 'hidden', '' );

	// Show widget after delay
	setTimeout( function() {
		widget.removeAttribute( 'hidden' );
		widget.style.animation = 'slideUp 400ms ease';
	}, delay );

	// Header contact booking button
	const headerBookingBtn = document.querySelector( '.header-contact__booking' );
	if ( headerBookingBtn ) {
		headerBookingBtn.addEventListener( 'click', function() {
			// Show widget menu
			toggle.setAttribute( 'aria-expanded', 'true' );
			menu.removeAttribute( 'hidden' );
			
			// Scroll to widget if on mobile/tablet
			if ( window.innerWidth < 768 ) {
				widget.scrollIntoView( { behavior: 'smooth', block: 'nearest' } );
			}
		} );
	}

	// Toggle menu
	toggle.addEventListener( 'click', function() {
		const isExpanded = toggle.getAttribute( 'aria-expanded' ) === 'true';
		toggle.setAttribute( 'aria-expanded', ! isExpanded );
		if ( isExpanded ) {
			menu.setAttribute( 'hidden', '' );
		} else {
			menu.removeAttribute( 'hidden' );
		}
	} );

	// Close menu
	closeBtn.addEventListener( 'click', function() {
		menu.setAttribute( 'hidden', '' );
		toggle.setAttribute( 'aria-expanded', 'false' );
	} );

	// Toggle message form
	messageToggle.addEventListener( 'click', function() {
		const isExpanded = messageToggle.getAttribute( 'aria-expanded' ) === 'true';
		messageToggle.setAttribute( 'aria-expanded', ! isExpanded );
		if ( isExpanded ) {
			messageForm.setAttribute( 'hidden', '' );
		} else {
			messageForm.removeAttribute( 'hidden' );
		}
	} );

	// Close message form
	messageClose.addEventListener( 'click', function() {
		messageForm.setAttribute( 'hidden', '' );
		messageToggle.setAttribute( 'aria-expanded', 'false' );
	} );

	// Handle message form submission
	const messageFormElement = document.getElementById( 'widget-message-form' );
	if ( messageFormElement ) {
		messageFormElement.addEventListener( 'submit', function( e ) {
			e.preventDefault();
			const formData = new FormData( messageFormElement );
			const data = Object.fromEntries( formData );

			// Send email via AJAX
			fetch( window.location.href, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded',
				},
				body: new URLSearchParams( {
					action: 'send_contact_message',
					name: data.name,
					email: data.email,
					message: data.message,
					nonce: window.spridisiNonce || '',
				} )
			} )
			.then( function( response ) {
				return response.text();
			} )
			.then( function( result ) {
				alert( 'Ziņa nosūtīta! Mēs ar jums sazināsimies drīz.' );
				messageFormElement.reset();
				messageForm.setAttribute( 'hidden', '' );
				messageToggle.setAttribute( 'aria-expanded', 'false' );
			} )
			.catch( function( error ) {
				alert( 'Kļūda sūtot ziņu. Mēģiniet vēlreiz.' );
				console.error( error );
			} );
		} );
	}

	// Close menu when clicking outside
	document.addEventListener( 'click', function( e ) {
		if ( ! widget.contains( e.target ) && ! menu.hasAttribute( 'hidden' ) ) {
			menu.setAttribute( 'hidden', '' );
			toggle.setAttribute( 'aria-expanded', 'false' );
		}
	} );
} );
