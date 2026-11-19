'use strict';

(() => {
	const form = document.querySelector('.booking-request-form');
	const status = document.querySelector('#booking-status');
	if (status) status.focus({ preventScroll: false });
	if (!form || typeof spridisiRequest === 'undefined') return;
	const fields = [...form.querySelectorAll('input:not([type="hidden"]), select, textarea')];
	const event = form.elements.event_type;
	const other = form.elements.event_other;
	const start = form.elements.start_date;
	const end = form.elements.end_date;
	const spaces = [...form.querySelectorAll('input[name="spaces[]"]')];
	const submit = form.querySelector('[type="submit"]');
	form.noValidate = true;
	const synchronize = () => {
		const required = event.value === 'other';
		other.required = required;
		other.disabled = !required;
		form.querySelector('.request-other').hidden = !required;
		end.min = start.value && start.value > form.dataset.today ? start.value : form.dataset.today;
	};
	const validate = () => {
		fields.forEach(field => field.setCustomValidity(''));
		fields.forEach(field => {
			if (field.disabled) return;
			if (field.validity.valueMissing) field.setCustomValidity(spridisiRequest.required);
			else if (field.type === 'email' && field.validity.typeMismatch) field.setCustomValidity(spridisiRequest.email);
		});
		const phone = form.elements.phone.value.trim();
		const digits = phone.replace(/\D/g, '');
		if (phone && (!/^[+0-9().\s-]+$/.test(phone) || digits.length < 7 || digits.length > 20)) form.elements.phone.setCustomValidity(spridisiRequest.phone);
		if (start.value && start.value < form.dataset.today) start.setCustomValidity(spridisiRequest.past);
		if (start.value && end.value && end.value < start.value) end.setCustomValidity(spridisiRequest.end);
		if (form.elements.guests.value && !/^[1-9]\d*$/.test(form.elements.guests.value)) form.elements.guests.setCustomValidity(spridisiRequest.guests);
		if (!spaces.some(field => field.checked)) spaces[0].setCustomValidity(spridisiRequest.spaces);
	};
	form.addEventListener('change', () => { synchronize(); validate(); });
	form.addEventListener('input', eventObject => {
		synchronize();
		validate();
		if (eventObject.target.validity?.valid) eventObject.target.removeAttribute('aria-invalid');
	});
	form.addEventListener('submit', eventObject => {
		validate();
		if (!form.checkValidity()) {
			eventObject.preventDefault();
			fields.filter(field => !field.disabled && !field.validity.valid).forEach(field => field.setAttribute('aria-invalid', 'true'));
			form.reportValidity();
			return;
		}
		submit.disabled = true;
		submit.textContent = spridisiRequest.sending;
	});
	window.addEventListener('pageshow', () => { submit.disabled = false; submit.textContent = spridisiRequest.submit; });
	synchronize();
})();