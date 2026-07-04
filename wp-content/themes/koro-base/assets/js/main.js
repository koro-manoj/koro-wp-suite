(function () {
	'use strict';

	const toggle = document.querySelector('.nav-toggle');
	const nav = document.querySelector('.site-nav');

	if (toggle && nav) {
		toggle.addEventListener('click', function () {
			const expanded = toggle.getAttribute('aria-expanded') === 'true';
			toggle.setAttribute('aria-expanded', expanded ? 'false' : 'true');
			nav.classList.toggle('is-open');
		});
	}

	document.addEventListener('koro:cart-updated', function (event) {
		const countEl = document.querySelector('[data-koro-cart-count]');
		if (countEl && event.detail && typeof event.detail.count !== 'undefined') {
			countEl.textContent = String(event.detail.count);
		}
	});
})();
