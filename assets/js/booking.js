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
	const overnight = form.elements.overnight_guests;
	const overnightField = form.querySelector('.request-overnight-field');
	const spaces = [...form.querySelectorAll('input[name="spaces[]"]')];
	const estimate = form.querySelector('.request-estimate');
	const estimateAmount = form.querySelector('#request-estimate-amount');
	const submit = form.querySelector('[type="submit"]');
	form.noValidate = true;
	const synchronize = () => {
		const isOtherEvent = event.value === 'other';
		other.required = isOtherEvent;
		other.disabled = !isOtherEvent;
		form.querySelector('.request-other').hidden = !isOtherEvent;
		
		const guestsField = form.elements.guests;
		guestsField.required = !isOtherEvent;
		
		const staying = spaces.some(field => field.checked && field.value === 'stay');
		overnight.required = staying;
		overnightField.hidden = !staying;
		if (!staying) overnight.value = '';
		end.min = start.value && start.value > form.dataset.today ? start.value : form.dataset.today;
		updateEstimate();
	};
	const updateEstimate = () => {
		if (!estimate || !estimateAmount) return;
		const prices = spridisiRequest.prices;
		const guestCount = Number.parseInt(form.elements.guests.value, 10);
		const smallHall = spaces.some(field => field.checked && field.value === 'small_hall');
		const largeHall = spaces.some(field => field.checked && field.value === 'large_hall');
		const sauna = spaces.some(field => field.checked && field.value === 'sauna');
		const hasHall = smallHall || largeHall;
		const staying = spaces.some(field => field.checked && field.value === 'stay');
		const overnightValid = /^(0|[1-9]\d*)$/.test(overnight.value);
		const overnightCount = overnightValid ? Number.parseInt(overnight.value, 10) : 0;
		const startDate = new Date(start.value);
		const endDate = new Date(end.value);
		const dayCount = (endDate - startDate) / (1000 * 60 * 60 * 24);
		
		if (!event.value || (!hasHall && !staying && !sauna) || (staying && !overnightValid) || (overnightCount > 0 && !staying)) {
			estimate.hidden = true;
			return;
		}
		
		if (hasHall && (!Number.isInteger(guestCount) || guestCount < 1)) {
			estimate.hidden = true;
			return;
		}
		
		let hallPrice = 0;
		if (hasHall && dayCount > 0) {
			if (smallHall) hallPrice += prices.smallHall * dayCount;
			if (largeHall) hallPrice += prices.largeHall * dayCount;
		}
		
		let overnightPrice = 0;
		if (staying && overnightCount > 0) {
			let pricePerNight = prices.overnight.over15;
			if (overnightCount <= 5) pricePerNight = prices.overnight.upTo5;
			else if (overnightCount <= 10) pricePerNight = prices.overnight.upTo10;
			else if (overnightCount <= 15) pricePerNight = prices.overnight.upTo15;
			overnightPrice = overnightCount * pricePerNight * dayCount;
		}
		
		const saunaPrice = sauna ? prices.sauna * dayCount : 0;
		const total = hallPrice + overnightPrice + saunaPrice;
		
		estimate.hidden = false;
		estimateAmount.textContent = `${new Intl.NumberFormat(document.documentElement.lang || 'lv-LV').format(total)} EUR`;
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
		if (overnight.value && (!/^(0|[1-9]\d*)$/.test(overnight.value) || Number.parseInt(overnight.value, 10) > 40)) overnight.setCustomValidity(spridisiRequest.overnightGuests);
		const staying = spaces.some(field => field.checked && field.value === 'stay');
		if (staying && !overnight.value) overnight.setCustomValidity(spridisiRequest.overnightRequired);
		if (Number.parseInt(overnight.value, 10) > 0 && !staying) overnight.setCustomValidity(spridisiRequest.overnightStay);
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
		if (spridisiRequest.turnstileConfigured) {
			const challengeResponse = form.querySelector('input[name="cf-turnstile-response"]');
			const challengeError = form.querySelector('#request-turnstile-error');
			if (!challengeResponse?.value) {
				eventObject.preventDefault();
				if (challengeError) {
					challengeError.textContent = spridisiRequest.turnstileRequired;
					challengeError.hidden = false;
				}
				return;
			}
			if (challengeError) challengeError.hidden = true;
		}
		submit.disabled = true;
		submit.textContent = spridisiRequest.sending;
	});
	window.addEventListener('pageshow', () => { submit.disabled = false; submit.textContent = spridisiRequest.submit; });
	synchronize();
})();