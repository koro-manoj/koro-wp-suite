(function () {
	'use strict';

	document.addEventListener('DOMContentLoaded', function () {
		var count = document.querySelector('[data-koro-cart-count]');
		if (!count) {
			return;
		}

		document.dispatchEvent(
			new CustomEvent('koro:cart-updated', {
				detail: { count: parseInt(count.textContent, 10) || 0 },
			})
		);
	});
})();
