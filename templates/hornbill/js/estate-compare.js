/**
 * Horizon Homes — Property Compare
 *
 * Manages a localStorage-backed "compare" selection across listing pages.
 * Max 4 properties. Renders a sticky bottom tray with a Compare button.
 */
(function () {
	'use strict';

	var KEY = 'estate_compare';
	var MAX = 4;
	var base = (document.querySelector('base') || {}).href || '';

	function getIds() {
		try {
			var raw = localStorage.getItem(KEY);
			var arr = raw ? JSON.parse(raw) : [];
			return Array.isArray(arr) ? arr.filter(function (v) { return typeof v === 'number' && v > 0; }) : [];
		} catch (e) {
			return [];
		}
	}

	function saveIds(arr) {
		try {
			localStorage.setItem(KEY, JSON.stringify(arr.slice(0, MAX)));
		} catch (e) { /* noop */ }
	}

	function toggleId(id) {
		var ids = getIds();
		var idx = ids.indexOf(id);
		if (idx === -1) {
			if (ids.length >= MAX) { return false; }
			ids.push(id);
		} else {
			ids.splice(idx, 1);
		}
		saveIds(ids);
		return true;
	}

	function removeId(id) {
		var ids = getIds().filter(function (v) { return v !== id; });
		saveIds(ids);
	}

	function clearAll() {
		saveIds([]);
	}

	function syncCheckboxes() {
		var ids = getIds();
		var boxes = document.querySelectorAll('[data-compare-id]');
		for (var i = 0; i < boxes.length; i++) {
			var el = boxes[i];
			var id = parseInt(el.getAttribute('data-compare-id'), 10);
			el.checked = ids.indexOf(id) !== -1;
			if (ids.length >= MAX && !el.checked) {
				el.disabled = true;
			} else {
				el.disabled = false;
			}
		}
	}

	function compareUrl() {
		var ids = getIds();
		if (!ids.length) { return ''; }
		return base + 'index.php?option=com_estate&view=listings&task=listings.compare&ids=' + ids.join(',');
	}

	/* ---------- Tray ---------- */

	function renderTray() {
		var ids = getIds();
		var tray = document.getElementById('estate-compare-tray');

		if (!ids.length) {
			if (tray) { tray.remove(); }
			return;
		}

		if (!tray) {
			tray = document.createElement('div');
			tray.id = 'estate-compare-tray';
			tray.className = 'estate-compare-tray';
			document.body.appendChild(tray);
		}

		var url = compareUrl();
		var cards = document.querySelectorAll('.estate-card[data-id]');
		var names = [];
		var cardsById = {};
		for (var i = 0; i < cards.length; i++) {
			cardsById[parseInt(cards[i].getAttribute('data-id'), 10)] = cards[i];
		}

		var html = '<div class="estate-compare-tray__inner">';
		html += '<span class="estate-compare-tray__count">' + ids.length + ' of ' + MAX + ' selected</span>';
		html += '<div class="estate-compare-tray__items">';
		for (var j = 0; j < ids.length; j++) {
			var cid = ids[j];
			var card = cardsById[cid];
			var title = '';
			var img = '';
			if (card) {
				var titleEl = card.querySelector('.estate-card__title');
				title = titleEl ? titleEl.textContent.trim() : 'Property #' + cid;
				var imgEl = card.querySelector('img');
				img = imgEl ? imgEl.src : '';
			} else {
				title = 'Property #' + cid;
			}
			html += '<div class="estate-compare-tray__item">';
			if (img) { html += '<img src="' + img + '" alt="" />'; }
			html += '<span>' + escapeHtml(title) + '</span>';
			html += '<button type="button" class="estate-compare-tray__remove" data-remove="' + cid + '" aria-label="Remove">&times;</button>';
			html += '</div>';
		}
		html += '</div>';
		html += '<div class="estate-compare-tray__actions">';
		if (url) { html += '<a href="' + url + '" class="btn-primary btn-primary--sm estate-compare-tray__btn">Compare Now</a>'; }
		html += '<button type="button" class="btn-ghost btn-ghost--sm estate-compare-tray__clear">Clear All</button>';
		html += '</div>';
		html += '</div>';

		tray.innerHTML = html;

		/* wire events */
		var removeBtns = tray.querySelectorAll('[data-remove]');
		for (var k = 0; k < removeBtns.length; k++) {
			removeBtns[k].addEventListener('click', function () {
				removeId(parseInt(this.getAttribute('data-remove'), 10));
				syncCheckboxes();
				renderTray();
			});
		}

		var clearBtn = tray.querySelector('.estate-compare-tray__clear');
		if (clearBtn) {
			clearBtn.addEventListener('click', function () {
				clearAll();
				syncCheckboxes();
				renderTray();
			});
		}
	}

	function escapeHtml(str) {
		var d = document.createElement('div');
		d.appendChild(document.createTextNode(str));
		return d.innerHTML;
	}

	/* ---------- Init ---------- */

	function init() {
		/* wire card checkboxes */
		var boxes = document.querySelectorAll('[data-compare-id]');
		for (var i = 0; i < boxes.length; i++) {
			boxes[i].addEventListener('change', function () {
				var id = parseInt(this.getAttribute('data-compare-id'), 10);
				var ok = toggleId(id);
				if (!ok) {
					this.checked = false;
					return;
				}
				syncCheckboxes();
				renderTray();
			});
		}

		/* wire detail-page button */
		var detailBtn = document.querySelector('[data-compare-add]');
		if (detailBtn) {
			var detailId = parseInt(detailBtn.getAttribute('data-compare-add'), 10);
			var ids = getIds();
			detailBtn.textContent = ids.indexOf(detailId) !== -1 ? 'Remove from Compare' : 'Add to Compare';
			detailBtn.classList.toggle('estate-compare-btn--active', ids.indexOf(detailId) !== -1);
			detailBtn.addEventListener('click', function () {
				var id = parseInt(this.getAttribute('data-compare-add'), 10);
				toggleId(id);
				var nowIds = getIds();
				this.textContent = nowIds.indexOf(id) !== -1 ? 'Remove from Compare' : 'Add to Compare';
				this.classList.toggle('estate-compare-btn--active', nowIds.indexOf(id) !== -1);
				syncCheckboxes();
				renderTray();
			});
		}

		syncCheckboxes();
		renderTray();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
