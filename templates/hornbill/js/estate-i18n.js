/* Horizon Homes - country/currency localisation helper.
 * Detects the visitor's country once (geo-IP with graceful offline fallback),
 * persists the choice in a cookie + localStorage and lets the user override
 * via the header switcher. Reloads once so server-rendered prices match. */
(function () {
	'use strict';

	var CODES = ['KE', 'UG', 'TZ', 'RW'];
	var COOKIE = 'estate_cc';
	var STORE = 'estate_cc';

	var select = document.getElementById('estate-cc-select');

	function readCookie(name) {
		var match = document.cookie.match('(?:^|;)\\s*' + name + '\\s*=\\s*([^;]+)');
		return match ? decodeURIComponent(match[1]) : '';
	}

	function storeValue(name, value, days) {
		var d = new Date();
		d.setTime(d.getTime() + days * 24 * 60 * 60 * 1000);
		document.cookie = name + '=' + encodeURIComponent(value) +
			'; expires=' + d.toUTCString() + '; path=/';
	}

	function storedPreference() {
		if (readCookie(COOKIE) !== '') {
			return readCookie(COOKIE);
		}
		try {
			return localStorage.getItem(STORE) || '';
		} catch (e) {
			return '';
		}
	}

	function apply(cc) {
		if (CODES.indexOf(cc) === -1) {
			return;
		}
		storeValue(COOKIE, cc, 365);
		try {
			localStorage.setItem(STORE, cc);
		} catch (e) {}
	}

	function choose(cc) {
		if (CODES.indexOf(cc) === -1) {
			return;
		}
		apply(cc);
		if (select) {
			select.value = cc;
		}
		window.location.reload();
	}

	if (select) {
		select.addEventListener('change', function () {
			choose(select.value);
		});
	}

	var pref = storedPreference();

	if (pref === '' && select) {
		var done = false;

		try {
			fetch('https://ipapi.co/json/')
				.then(function (response) {
					return response.json();
				})
				.then(function (data) {
					if (done) {
						return;
					}
					done = true;
					var cc = String(data.country_code || '').toUpperCase();
					if (CODES.indexOf(cc) !== -1 && storedPreference() === '') {
						choose(cc);
					}
				})
				.catch(function () {});
		} catch (e) {}

		// Timeout guard so a slow geo lookup never blocks the page.
		setTimeout(function () {
			done = true;
		}, 8000);
	}
})();