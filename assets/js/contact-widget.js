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
	const delay = parseInt( widget.dataset.delay ) || 7000;

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

	// Close menu when clicking outside
	document.addEventListener( 'click', function( e ) {
		if ( ! widget.contains( e.target ) && ! menu.hasAttribute( 'hidden' ) ) {
			menu.setAttribute( 'hidden', '' );
			toggle.setAttribute( 'aria-expanded', 'false' );
		}
	} );
} );
