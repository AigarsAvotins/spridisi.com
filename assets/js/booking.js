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
	const spaces = [...form.querySelectorAll('input[name="spaces[]"]')];
	const estimateAmount = form.querySelector('#request-estimate-amount');
	const estimateNote = form.querySelector('#request-estimate-note');
	const submit = form.querySelector('[type="submit"]');
	form.noValidate = true;
	const synchronize = () => {
		const required = event.value === 'other';
		other.required = required;
		other.disabled = !required;
		form.querySelector('.request-other').hidden = !required;
		const staying = spaces.some(field => field.checked && field.value === 'stay');
		overnight.disabled = !staying;
		overnight.closest('.request-field').hidden = !staying;
		end.min = start.value && start.value > form.dataset.today ? start.value : form.dataset.today;
		updateEstimate();
	};
	const updateEstimate = () => {
		if (!estimateAmount || !estimateNote) return;
		const prices = spridisiRequest.prices;
		const guestCount = Number.parseInt(form.elements.guests.value, 10);
		const smallHall = spaces.some(field => field.checked && field.value === 'small_hall');
		const largeHall = spaces.some(field => field.checked && field.value === 'large_hall');
		const sauna = spaces.some(field => field.checked && field.value === 'sauna');
		const hasHall = smallHall || largeHall;
		const staying = spaces.some(field => field.checked && field.value === 'stay');
		let hallPrice = 0;
		let smallHallNeedsReview = false;
		if (event.value === 'wedding' && hasHall) hallPrice = prices.wedding;
		else if (hasHall) {
			if (!Number.isInteger(guestCount)) {
				estimateAmount.textContent = '—';
				estimateNote.textContent = spridisiRequest.priceNeedGuests;
				return;
			}
			if (smallHall) {
				if (guestCount <= 15) hallPrice += prices.smallHall.upTo15;
				else if (guestCount <= 25) hallPrice += prices.smallHall.upTo25;
				else smallHallNeedsReview = true;
			}
			if (largeHall) {
				if (guestCount <= 30) hallPrice += prices.largeHall.upTo30;
				else if (guestCount <= 40) hallPrice += prices.largeHall.upTo40;
				else hallPrice += prices.largeHall.over40;
			}
		}
		if (!hasHall && !staying && !sauna) {
			estimateAmount.textContent = '—';
			estimateNote.textContent = spridisiRequest.priceNoSelection;
			return;
		}
		if (smallHallNeedsReview) {
			estimateAmount.textContent = '—';
			estimateNote.textContent = spridisiRequest.priceSmallLimit;
			return;
		}
		const overnightCount = staying ? Number.parseInt(overnight.value, 10) || 0 : 0;
		const total = hallPrice + (sauna ? prices.sauna : 0) + overnightCount * prices.overnight;
		estimateAmount.textContent = `${new Intl.NumberFormat(document.documentElement.lang || 'lv-LV').format(total)} EUR`;
		estimateNote.textContent = spridisiRequest.priceDetails;
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
		if (overnight.value && !/^(0|[1-9]\d*)$/.test(overnight.value)) overnight.setCustomValidity(spridisiRequest.overnightGuests);
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