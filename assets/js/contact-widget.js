/**
 * Contact Widget Functionality
 */

document.addEventListener( 'DOMContentLoaded', function() {
	// Header contact booking button
	const headerBookingBtn = document.querySelector( '.header-contact__booking' );
	if ( headerBookingBtn ) {
		headerBookingBtn.addEventListener( 'click', function() {
			const widget = document.getElementById( 'contact-widget' );
			if ( widget ) {
				const toggle = widget.querySelector( '.contact-widget__toggle' );
				const menu = widget.querySelector( '.contact-widget__menu' );
				
				// Show widget menu
				toggle.setAttribute( 'aria-expanded', 'true' );
				menu.removeAttribute( 'hidden' );
				
				// Scroll to widget if on mobile/tablet
				if ( window.innerWidth < 768 ) {
					widget.scrollIntoView( { behavior: 'smooth', block: 'nearest' } );
				}
			}
		} );
	}

	const widget = document.getElementById( 'contact-widget' );
	if ( ! widget ) {
		return;
	}

	const toggle = widget.querySelector( '.contact-widget__toggle' );
	const menu = widget.querySelector( '.contact-widget__menu' );
	const closeBtn = widget.querySelector( '.contact-widget__close' );
	const delay = parseInt( widget.dataset.delay ) || 7000;

	// Show widget after delay
	setTimeout( function() {
		widget.style.animation = 'slideUp 400ms ease';
	}, delay );

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

	// Handle form submission
	const bookingForm = document.getElementById( 'widget-booking-form' );
	if ( bookingForm ) {
		bookingForm.addEventListener( 'submit', function( e ) {
			e.preventDefault();
			const formData = new FormData( bookingForm );
			const data = Object.fromEntries( formData );
			
			// Send to WhatsApp with form data
			const phone = data.phone || '+37126344495';
			const message = `Sveiki! Mani sauc ${data.name}. Mēs gribam apmeklēt Sprīdīši.\n\nE-pasts: ${data.email}\nTālrunis: ${data.phone}\nVēlamie datumi: ${data.dates}\n\nPapildu info: ${data.message}`;
			const whatsappUrl = `https://wa.me/${phone.replace( /\D/g, '' )}?text=${encodeURIComponent( message )}`;
			window.open( whatsappUrl, '_blank' );
			
			// Reset form
			bookingForm.reset();
			form.setAttribute( 'hidden', '' );
			optionsContent.removeAttribute( 'hidden' );
		} );
	}
} );
