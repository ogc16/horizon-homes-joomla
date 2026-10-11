/**
 * Horizon Homes - pure helpers for the property Compare feature.
 *
 * Kept free of DOM/localStorage so the logic can be unit- and
 * property-tested under Node (see tests/js). Loaded before estate-compare.js.
 */
(function (root, factory) {
	'use strict';
	var api = factory();
	if (typeof module === 'object' && module.exports) {
		module.exports = api;
	}
	if (root) {
		root.EstateUtil = api;
	}
}(typeof globalThis !== 'undefined' ? globalThis : this, function () {
	'use strict';

	/**
	 * Keep only valid property ids: numbers strictly greater than zero.
	 * Mirrors the guard historically applied when reading from storage.
	 */
	function sanitizeIds(value) {
		if (!Array.isArray(value)) {
			return [];
		}
		return value.filter(function (v) {
			return typeof v === 'number' && v > 0;
		});
	}

	/**
	 * Toggle an id in a list, enforcing a maximum size.
	 * Returns { ok, ids }: ok is false when adding would exceed max.
	 */
	function toggleId(ids, id, max) {
		var next = ids.slice();
		var idx = next.indexOf(id);
		if (idx === -1) {
			if (next.length >= max) {
				return { ok: false, ids: next };
			}
			next.push(id);
		} else {
			next.splice(idx, 1);
		}
		return { ok: true, ids: next };
	}

	/**
	 * Return a new list without the given id.
	 */
	function removeId(ids, id) {
		return ids.filter(function (v) {
			return v !== id;
		});
	}

	/**
	 * Build the compare view URL for the given selection.
	 */
	function compareUrl(base, ids) {
		if (!ids.length) {
			return '';
		}
		return base + 'index.php?option=com_estate&view=listings&task=listings.compare&ids=' + ids.join(',');
	}

	return {
		sanitizeIds: sanitizeIds,
		toggleId: toggleId,
		removeId: removeId,
		compareUrl: compareUrl
	};
}));
