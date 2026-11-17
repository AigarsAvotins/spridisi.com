'use strict';

(() => {
	const header = document.querySelector('.site-header');
	const toggle = document.querySelector('.menu-toggle');
	const navigation = document.querySelector('#primary-menu');
	const mobile = window.matchMedia('(max-width: 67.99rem)');
	const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

	if (header && toggle && navigation) {
		header.classList.add('has-navigation-js');
		const closeMenu = (restoreFocus = false) => {
			toggle.setAttribute('aria-expanded', 'false');
			header.classList.remove('menu-open');
			navigation.hidden = mobile.matches;
			if (restoreFocus) toggle.focus();
		};
		const syncMenu = () => {
			toggle.hidden = !mobile.matches;
			closeMenu(navigation.contains(document.activeElement) && mobile.matches);
		};
		toggle.addEventListener('click', () => {
			const open = toggle.getAttribute('aria-expanded') !== 'true';
			toggle.setAttribute('aria-expanded', String(open));
			header.classList.toggle('menu-open', open);
			navigation.hidden = !open;
		});
		document.addEventListener('keydown', (event) => {
			if (event.key === 'Escape' && toggle.getAttribute('aria-expanded') === 'true') closeMenu(true);
		});
		document.addEventListener('click', (event) => {
			if (!header.contains(event.target) && toggle.getAttribute('aria-expanded') === 'true') closeMenu();
		});
		header.addEventListener('focusout', (event) => {
			if (event.relatedTarget && !header.contains(event.relatedTarget)) closeMenu();
		});
		navigation.addEventListener('click', (event) => {
			if (event.target.closest('a') && mobile.matches) closeMenu();
		});
		mobile.addEventListener('change', syncMenu);
		syncMenu();
		if ('ResizeObserver' in window) {
			const headerObserver = new ResizeObserver(() => {
				if (!header.classList.contains('menu-open')) {
					document.documentElement.style.setProperty('--spridisi-header-height', `${header.offsetHeight}px`);
				}
			});
			headerObserver.observe(header);
		}
		let scheduled = false;
		const updateHeader = () => {
			header.classList.toggle('is-scrolled', window.scrollY > 24);
			scheduled = false;
		};
		window.addEventListener('scroll', () => {
			if (!scheduled) {
				scheduled = true;
				window.requestAnimationFrame(updateHeader);
			}
		}, { passive: true });
		updateHeader();
	}

	document.addEventListener('click', (event) => {
		const link = event.target.closest('a[href]');
		if (!link || event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey || link.target || link.hasAttribute('download')) return;
		const url = new URL(link.href, window.location.href);
		if (url.origin !== window.location.origin || url.pathname !== window.location.pathname || url.search !== window.location.search || !url.hash) return;
		let target;
		try { target = document.getElementById(decodeURIComponent(url.hash.slice(1))); } catch { return; }
		if (!target) return;
		event.preventDefault();
		if (!target.hasAttribute('tabindex')) target.setAttribute('tabindex', '-1');
		target.focus({ preventScroll: true });
		target.scrollIntoView({ behavior: reducedMotion.matches ? 'instant' : 'smooth', block: 'start' });
		window.history.pushState(null, '', url.hash);
	});

	if ('IntersectionObserver' in window && !reducedMotion.matches) {
		const observer = new IntersectionObserver((entries) => {
			entries.forEach((entry) => {
				if (entry.isIntersecting) {
					entry.target.classList.add('is-revealed');
					observer.unobserve(entry.target);
				}
			});
		}, { threshold: 0.08 });
		document.querySelectorAll('[data-reveal]').forEach((element) => observer.observe(element));
	}
})();