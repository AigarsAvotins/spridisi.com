'use strict';

(() => {
	const overlay = document.querySelector('.wp-lightbox-overlay');
	if (!overlay || !document.querySelector('.gallery-page')) return;
	let trigger = null;
	let opened = false;
	let previousOverflow = '';

	document.addEventListener('click', (event) => {
		const container = event.target.closest('.gallery-page .wp-lightbox-container');
		if (container) trigger = container.querySelector('button');
	}, true);

	const synchronize = () => {
		const active = overlay.classList.contains('active');
		if (active === opened) return;
		opened = active;
		if (active) {
			previousOverflow = document.documentElement.style.overflow;
			document.documentElement.style.overflow = 'hidden';
			overlay.focus({ preventScroll: true });
		} else {
			document.documentElement.style.overflow = previousOverflow;
			if (trigger?.isConnected) trigger.focus({ preventScroll: true });
		}
	};
	new MutationObserver(synchronize).observe(overlay, { attributes: true, attributeFilter: ['class'] });

	document.addEventListener('keydown', (event) => {
		if (!opened) return;
		const selectors = {
			Escape: '.wp-lightbox-close-button',
			ArrowLeft: '.wp-lightbox-navigation-button-prev',
			ArrowRight: '.wp-lightbox-navigation-button-next',
		};
		const selector = selectors[event.key];
		if (!selector) return;
		const control = overlay.querySelector(selector);
		if (!control || control.hidden || control.disabled) return;
		event.preventDefault();
		event.stopImmediatePropagation();
		control.click();
	}, true);
	window.addEventListener('pagehide', () => {
		if (opened) document.documentElement.style.overflow = previousOverflow;
	});
	synchronize();
})();