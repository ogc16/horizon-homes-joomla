import test from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';
import vm from 'node:vm';
import fc from 'fast-check';

const here = dirname(fileURLToPath(import.meta.url));
const srcPath = join(here, '..', '..', 'templates', 'hornbill', 'js', 'estate-util.js');
const src = readFileSync(srcPath, 'utf8');

vm.runInThisContext(src, { filename: 'estate-util.js' });
const EstateUtil = globalThis.EstateUtil;

const positiveIds = fc.uniqueArray(fc.integer({ min: 1, max: 1000 }), { maxLength: 12 });
const anyValue = fc.anything();

test('sanitizeIds keeps only positive numbers', () => {
	fc.assert(fc.property(fc.array(anyValue), (arr) => {
		const out = EstateUtil.sanitizeIds(arr);
		assert.ok(out.every((v) => typeof v === 'number' && v > 0));
	}));
});

test('sanitizeIds returns [] for non-arrays', () => {
	fc.assert(fc.property(fc.anything(), (v) => {
		if (!Array.isArray(v)) {
			assert.deepEqual(EstateUtil.sanitizeIds(v), []);
		}
	}));
});

test('sanitizeIds is idempotent', () => {
	fc.assert(fc.property(fc.array(anyValue), (arr) => {
		const once = EstateUtil.sanitizeIds(arr);
		assert.deepEqual(EstateUtil.sanitizeIds(once), once);
	}));
});

test('toggleId adds when absent and within capacity', () => {
	fc.assert(fc.property(positiveIds, fc.integer({ min: 1, max: 1000 }), fc.integer({ min: 1, max: 12 }), (ids, id, max) => {
		const present = ids.indexOf(id) !== -1;
		const res = EstateUtil.toggleId(ids, id, max);
		if (!present && ids.length < max) {
			assert.equal(res.ok, true);
			assert.ok(res.ids.indexOf(id) !== -1);
			assert.equal(res.ids.length, ids.length + 1);
		}
		if (present) {
			assert.equal(res.ok, true);
			assert.equal(res.ids.indexOf(id), -1);
		}
	}));
});

test('toggleId refuses to exceed capacity', () => {
	fc.assert(fc.property(positiveIds, fc.integer({ min: 0, max: 12 }), (ids, max) => {
		const missing = 999999;
		if (ids.length >= max) {
			const res = EstateUtil.toggleId(ids, missing, max);
			assert.equal(res.ok, false);
			assert.deepEqual(res.ids, ids);
		}
	}));
});

test('toggleId never grows past max', () => {
	fc.assert(fc.property(positiveIds, fc.integer({ min: 1, max: 1000 }), fc.integer({ min: 0, max: 12 }), (ids, id, max) => {
		const res = EstateUtil.toggleId(ids, id, max);
		assert.ok(res.ids.length <= Math.max(max, ids.length));
		assert.ok(res.ids.length <= ids.length + 1);
	}));
});

test('removeId removes every occurrence and is idempotent', () => {
	fc.assert(fc.property(positiveIds, fc.integer({ min: 1, max: 1000 }), (ids, id) => {
		const out = EstateUtil.removeId(ids, id);
		assert.ok(out.every((v) => v !== id));
		assert.deepEqual(EstateUtil.removeId(out, id), out);
	}));
});

test('compareUrl is empty for empty selection and joins ids otherwise', () => {
	fc.assert(fc.property(fc.string(), positiveIds, (base, ids) => {
		if (ids.length === 0) {
			assert.equal(EstateUtil.compareUrl(base, ids), '');
		} else {
			const expected = base + 'index.php?option=com_estate&view=listings&task=listings.compare&ids=' + ids.join(',');
			assert.equal(EstateUtil.compareUrl(base, ids), expected);
		}
	}));
});
