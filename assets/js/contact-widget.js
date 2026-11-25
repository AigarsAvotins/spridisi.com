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
	const formToggle = widget.querySelector( '.contact-widget__form-toggle' );
	const form = widget.querySelector( '.contact-widget__form' );
	const optionsContent = widget.querySelector( '.contact-widget__content' );
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

	// Toggle form
	formToggle.addEventListener( 'click', function() {
		const isFormVisible = ! form.hasAttribute( 'hidden' );
		if ( isFormVisible ) {
			form.setAttribute( 'hidden', '' );
			optionsContent.removeAttribute( 'hidden' );
		} else {
			optionsContent.setAttribute( 'hidden', '' );
			form.removeAttribute( 'hidden' );
		}
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
