/*
	MIT License http://www.opensource.org/licenses/mit-license.php
	Author Ivan Kopeykin @vankop
*/

"use strict";

const { parseIdentifier } = require("./identifier");

/** @typedef {string | (string | ConditionalMapping)[]} DirectMapping */
/** @typedef {{ [k: string]: MappingValue }} ConditionalMapping */
/** @typedef {ConditionalMapping | DirectMapping | null} MappingValue */
/** @typedef {Record<string, MappingValue> | ConditionalMapping | DirectMapping} ExportsField */
/** @typedef {Record<string, MappingValue>} ImportsField */

/**
 * Processing exports/imports field
 * @callback FieldProcessor
 * @param {string} request request
 * @param {Set<string>} conditionNames condition names
 * @returns {[string[], string | null]} resolved paths with used field
 */

/*
Example exports field:
{
  ".": "./main.js",
  "./feature": {
    "browser": "./feature-browser.js",
    "default": "./feature.js"
  }
}
Terminology:

Enhanced-resolve name keys ("." and "./feature") as exports field keys.

If value is string or string[], mapping is called as a direct mapping
and value called as a direct export.

If value is key-value object, mapping is called as a conditional mapping
and value called as a conditional export.

Key in conditional mapping is called condition name.

Conditional mapping nested in another conditional mapping is called nested mapping.

----------

Example imports field:
{
  "#a": "./main.js",
  "#moment": {
    "browser": "./moment/index.js",
    "default": "moment"
  },
  "#moment/": {
    "browser": "./moment/",
    "default": "moment/"
  }
}
Terminology:

Enhanced-resolve name keys ("#a" and "#moment/", "#moment") as imports field keys.

If value is string or string[], mapping is called as a direct mapping
and value called as a direct export.

If value is key-value object, mapping is called as a conditional mapping
and value called as a conditional export.

Key in conditional mapping is called condition name.

Conditional mapping nested in another conditional mapping is called nested mapping.

*/

const slashCode = "/".charCodeAt(0);
const dotCode = ".".charCodeAt(0);
const hashCode = "#".charCodeAt(0);
const patternRegEx = /\*/g;
const DOLLAR_ESCAPE_RE = /\$/g;

/** @typedef {Record<string, MappingValue>} RecordMapping */

/**
 * Cached `Object.keys()` for objects whose shape does not change after the
 * first observation — i.e. parsed `package.json` fields and the nested
 * conditional mappings inside them. `Object.keys` allocates a fresh array
 * on every call; since `findMatch` / `conditionalMapping` run on every
 * bare-specifier resolve, the allocation adds up quickly.
 * @type {WeakMap<RecordMapping, string[]>}
 */
const _keysCache = new WeakMap();

/**
 * @param {RecordMapping} obj object to read keys from
 * @returns {string[]} cached keys array (DO NOT mutate)
 */
function cachedKeys(obj) {
	let keys = _keysCache.get(obj);
	if (keys === undefined) {
		keys = Object.keys(obj);
		_keysCache.set(obj, keys);
	}
	return keys;
}

/**
 * Per-key precomputed info used by `findMatch`. Equivalent to what the
 * previous implementation recomputed inline on every resolve.
 * @typedef {object} FieldKeyInfo
 * @property {string} key the original key
 * @property {number} patternIndex position of the single "*" in the key, or -1 when absent
 * @property {string} wildcardPrefix substring before "*" (empty when patternIndex === -1)
 * @property {string} wildcardSuffix substring after "*" (empty when patternIndex === -1)
 * @property {boolean} isLegacySubpath true when key is a legacy `./foo/`-style folder key with no "*"
 * @property {boolean} isPattern true when key contains "*"
 * @property {boolean} isSubpathMapping true when key ends with "/"
 * @property {boolean} isValidPattern true when key has at most one "*"
 */

/**
 * Cached per-field key metadata, keyed by the exports/imports field
 * object. Computed lazily on first `findMatch` call and reused forever.
 * Safe because `package.json` fields are immutable JSON values.
 * @type {WeakMap<RecordMapping, FieldKeyInfo[]>}
 */
const _fieldKeyInfoCache = new WeakMap();

/**
 * @param {ExportsField | ImportsField} field field object
 * @returns {FieldKeyInfo[]} precomputed per-key info
 */
function getFieldKeyInfos(field) {
	const fieldKey = /** @type {RecordMapping} */ (field);
	let infos = _fieldKeyInfoCache.get(fieldKey);
	if (infos !== undefined) return infos;
	const keys = Object.getOwnPropertyNames(field);
	infos = Array.from({ length: keys.length });
	for (let i = 0; i < keys.length; i++) {
		const key = keys[i];
		const patternIndex = key.indexOf("*");
		// `isValidPattern` is true when the key has at most one `*`. Searching
		// from `patternIndex + 1` stops as soon as a second `*` is found, so
		// we avoid the full-string scan that `lastIndexOf` would do — and the
		// single-star common case finishes in one pass.
		const isValidPattern =
			patternIndex === -1 || !key.includes("*", patternIndex + 1);
		const keyLen = key.length;
		const endsWithSlash =
			keyLen > 0 && key.charCodeAt(keyLen - 1) === slashCode;
		infos[i] = {
			key,
			patternIndex,
			wildcardPrefix: patternIndex === -1 ? "" : key.slice(0, patternIndex),
			wildcardSuffix: patternIndex === -1 ? "" : key.slice(patternIndex + 1),
			isLegacySubpath: patternIndex === -1 && endsWithSlash,
			isPattern: patternIndex !== -1,
			isSubpathMapping: endsWithSlash,
			isValidPattern,
		};
	}
	_fieldKeyInfoCache.set(fieldKey, infos);
	return infos;
}

/**
 * @param {string} a first string
 * @param {string} b second string
 * @returns {number} compare result
 */
function patternKeyCompare(a, b) {
	const aPatternIndex = a.indexOf("*");
	const bPatternIndex = b.indexOf("*");
	const baseLenA = aPatternIndex === -1 ? a.length : aPatternIndex + 1;
	const baseLenB = bPatternIndex === -1 ? b.length : bPatternIndex + 1;

	if (baseLenA > baseLenB) return -1;
	if (baseLenB > baseLenA) return 1;
	if (aPatternIndex === -1) return 1;
	if (bPatternIndex === -1) return -1;
	if (a.length > b.length) return -1;
	if (b.length > a.length) return 1;

	return 0;
}

/** @typedef {[MappingValue, string, boolean, boolean, string] | null} MatchTuple */

/**
 * Per-field memoization of `findMatch(request, field)`. For a given field
 * the result depends only on the `request` string (it does NOT depend on
 * `conditionNames` — that's applied separately by `conditionalMapping`),
 * so we can cache the tuple keyed by request.
 *
 * Typical build traffic runs the same request through the resolver
 * repeatedly (same import re-resolved from different source files, module
 * graph traversals that revisit a package, etc.), and every one of those
 * hits walks the same key list and allocates the same tuple. Caching the
 * tuple turns the second-and-onward call into a single Map lookup.
 *
 * Keyed on the field object via a module-level `WeakMap`, so the cache
 * is freed automatically when the owning description file is GC'd.
 * @type {WeakMap<RecordMapping, Map<string, MatchTuple>>}
 */
const _findMatchCache = new WeakMap();

/**
 * @param {string} request request
 * @param {ExportsField | ImportsField} field exports or import field
 * @returns {MatchTuple} match result (uncached)
 */
function computeFindMatch(request, field) {
	const requestLen = request.length;
	const requestEndsWithSlash =
		requestLen > 0 && request.charCodeAt(requestLen - 1) === slashCode;
	const requestHasStar = request.includes("*");

	if (
		!requestHasStar &&
		!requestEndsWithSlash &&
		Object.prototype.hasOwnProperty.call(field, request)
	) {
		const target = /** @type {{ [k: string]: MappingValue }} */ (field)[
			request
		];

		return [target, "", false, false, request];
	}

	/** @type {string} */
	let bestMatch = "";
	/** @type {FieldKeyInfo | null} */
	let bestMatchInfo = null;
	/** @type {string | undefined} */
	let bestMatchSubpath;

	const infos = getFieldKeyInfos(field);

	for (let i = 0; i < infos.length; i++) {
		const info = infos[i];
		const { key, patternIndex } = info;

		if (patternIndex !== -1) {
			if (
				!info.isValidPattern ||
				!request.startsWith(info.wildcardPrefix) ||
				requestLen < key.length ||
				!request.endsWith(info.wildcardSuffix) ||
				patternKeyCompare(bestMatch, key) !== 1
			) {
				continue;
			}
			bestMatch = key;
			bestMatchInfo = info;
			bestMatchSubpath = request.slice(
				patternIndex,
				requestLen - info.wildcardSuffix.length,
			);
		} else if (
			info.isLegacySubpath &&
			request.startsWith(key) &&
			patternKeyCompare(bestMatch, key) === 1
		) {
			bestMatch = key;
			bestMatchInfo = info;
			bestMatchSubpath = request.slice(key.length);
		}
	}

	if (bestMatch === "") return null;

	const target =
		/** @type {{ [k: string]: MappingValue }} */
		(field)[bestMatch];

	return [
		target,
		/** @type {string} */ (bestMatchSubpath),
		/** @type {FieldKeyInfo} */ (bestMatchInfo).isSubpathMapping,
		/** @type {FieldKeyInfo} */ (bestMatchInfo).isPattern,
		bestMatch,
	];
}

/**
 * Trying to match request to field
 * @param {string} request request
 * @param {ExportsField | ImportsField} field exports or import field
 * @returns {MatchTuple} match or null, number is negative and one less when it's a folder mapping, number is request.length + 1 for direct mappings
 */
function findMatch(request, field) {
	const fieldKey = /** @type {RecordMapping} */ (field);
	let perRequest = _findMatchCache.get(fieldKey);
	if (perRequest === undefined) {
		perRequest = new Map();
		_findMatchCache.set(fieldKey, perRequest);
	} else {
		// `computeFindMatch` only ever returns `MatchTuple | null` — never
		// `undefined` — and `Map.set(k, null)` then `Map.get(k)` returns
		// `null`, not `undefined`. So `get(...) === undefined` already
		// unambiguously means "not cached yet"; one Map lookup is enough,
		// no follow-up `has` needed to disambiguate "cached null".
		const cached = perRequest.get(request);
		if (cached !== undefined) return cached;
	}

	const result = computeFindMatch(request, field);
	perRequest.set(request, result);
	return result;
}

/**
 * Sentinel stored in the conditional-mapping cache for inputs whose walk
 * returns `null` ("no condition matched"). Using a non-null marker lets the
 * cache-hit path be a single `WeakMap.get()` — we distinguish
 * "cached null" from "not cached yet" without a second `has` call.
 */
const NULL_RESULT = Symbol("NULL_RESULT");

/**
 * Memoization of `conditionalMapping(mapping, conditionNames)`. The result
 * depends only on the mapping object (immutable — owned by a parsed
 * `package.json`) and the `conditionNames` Set (owned by the resolver's
 * options and stable for its lifetime), so it is safe to cache per (mapping,
 * conditionNames) pair.
 *
 * A conditional `exports` entry that appears inside a `directMapping` array
 * (the common `"browser": [...fallback list...]` shape, plus nested
 * conditions) gets walked on every resolve that traverses the parent entry.
 * Without this cache each of those walks re-reads `Object.keys` on the
 * mapping and re-visits every condition until one matches, even though the
 * inputs are identical.
 *
 * Outer key is the conditional mapping itself; inner key is the condition
 * Set. Both are object references, so WeakMap-of-WeakMap lets both levels
 * be collected automatically when the description file or resolver go away.
 * @type {WeakMap<ConditionalMapping, WeakMap<Set<string>, DirectMapping | typeof NULL_RESULT>>}
 */
const _conditionalMappingCache = new WeakMap();

/**
 * @param {ConditionalMapping} conditionalMapping_ conditional mapping
 * @param {Set<string>} conditionNames condition names
 * @returns {DirectMapping | null} direct mapping if found (uncached)
 */
function computeConditionalMapping(conditionalMapping_, conditionNames) {
	/** @type {[ConditionalMapping, string[], number][]} */
	const lookup = [[conditionalMapping_, cachedKeys(conditionalMapping_), 0]];

	loop: while (lookup.length > 0) {
		const top = lookup[lookup.length - 1];
		const [mapping, conditions, j] = top;

		for (let i = j; i < conditions.length; i++) {
			const condition = conditions[i];

			if (condition === "default" || conditionNames.has(condition)) {
				const innerMapping = mapping[condition];
				if (
					innerMapping !== null &&
					typeof innerMapping === "object" &&
					!Array.isArray(innerMapping)
				) {
					const nested = /** @type {ConditionalMapping} */ (innerMapping);
					top[2] = i + 1;
					lookup.push([nested, cachedKeys(nested), 0]);
					continue loop;
				}

				return /** @type {DirectMapping} */ (innerMapping);
			}
		}

		lookup.pop();
	}

	return null;
}

/**
 * @param {ConditionalMapping} conditionalMapping_ conditional mapping
 * @param {Set<string>} conditionNames condition names
 * @returns {DirectMapping | null} direct mapping if found
 */
function conditionalMapping(conditionalMapping_, conditionNames) {
	let perSet = _conditionalMappingCache.get(conditionalMapping_);
	if (perSet !== undefined) {
		const cached = perSet.get(conditionNames);
		if (cached !== undefined) {
			return cached === NULL_RESULT
				? null
				: /** @type {DirectMapping} */ (cached);
		}
	} else {
		perSet = new WeakMap();
		_conditionalMappingCache.set(conditionalMapping_, perSet);
	}
	const result = computeConditionalMapping(conditionalMapping_, conditionNames);
	perSet.set(conditionNames, result === null ? NULL_RESULT : result);
	return result;
}

/**
 * @param {string | undefined} remainingRequest remaining request when folder mapping, undefined for file mappings
 * @param {boolean} isPattern true, if mapping is a pattern (contains "*")
 * @param {boolean} isSubpathMapping true, for subpath mappings
 * @param {string} mappingTarget direct export
 * @param {(d: string, f: boolean) => void} assert asserting direct value
 * @returns {string} mapping result
 */
function targetMapping(
	remainingRequest,
	isPattern,
	isSubpathMapping,
	mappingTarget,
	assert,
) {
	if (remainingRequest === undefined) {
		assert(mappingTarget, false);

		return mappingTarget;
	}

	if (isSubpathMapping) {
		assert(mappingTarget, true);

		return mappingTarget + remainingRequest;
	}

	assert(mappingTarget, false);

	let result = mappingTarget;

	if (isPattern) {
		const escapedRemainder = remainingRequest.includes("$")
			? remainingRequest.replace(DOLLAR_ESCAPE_RE, "$$")
			: remainingRequest;
		result = result.replace(patternRegEx, escapedRemainder);
	}

	return result;
}

/**
 * @param {string | undefined} remainingRequest remaining request when folder mapping, undefined for file mappings
 * @param {boolean} isPattern true, if mapping is a pattern (contains "*")
 * @param {boolean} isSubpathMapping true, for subpath mappings
 * @param {DirectMapping | null} mappingTarget direct export
 * @param {Set<string>} conditionNames condition names
 * @param {(d: string, f: boolean) => void} assert asserting direct value
 * @returns {string[]} mapping result
 */
function directMapping(
	remainingRequest,
	isPattern,
	isSubpathMapping,
	mappingTarget,
	conditionNames,
	assert,
) {
	if (mappingTarget === null) return [];

	if (typeof mappingTarget === "string") {
		return [
			targetMapping(
				remainingRequest,
				isPattern,
				isSubpathMapping,
				mappingTarget,
				assert,
			),
		];
	}

	/** @type {string[]} */
	const targets = [];

	for (let i = 0, len = mappingTarget.length; i < len; i++) {
		const exp = mappingTarget[i];
		if (typeof exp === "string") {
			targets.push(
				targetMapping(
					remainingRequest,
					isPattern,
					isSubpathMapping,
					exp,
					assert,
				),
			);
			continue;
		}

		const mapping = conditionalMapping(exp, conditionNames);
		if (!mapping) continue;
		const innerExports = directMapping(
			remainingRequest,
			isPattern,
			isSubpathMapping,
			mapping,
			conditionNames,
			assert,
		);
		for (let j = 0, innerLen = innerExports.length; j < innerLen; j++) {
			targets.push(innerExports[j]);
		}
	}

	return targets;
}

/** @type {[string[], null]} */
const EMPTY_NO_MATCH = /** @type {[string[], null]} */ ([[], null]);

/**
 * @param {ExportsField | ImportsField} field root
 * @param {(s: string) => string} normalizeRequest Normalize request, for `imports` field it adds `#`, for `exports` field it adds `.` or `./`
 * @param {(s: string) => string} assertRequest assertRequest
 * @param {(s: string, f: boolean) => void} assertTarget assertTarget
 * @returns {FieldProcessor} field processor
 */
function createFieldProcessor(
	field,
	normalizeRequest,
	assertRequest,
	assertTarget,
) {
	return function fieldProcessor(request, conditionNames) {
		const match = findMatch(normalizeRequest(assertRequest(request)), field);

		if (match === null) return EMPTY_NO_MATCH;

		const [mapping, remainingRequest, isSubpathMapping, isPattern, usedField] =
			match;

		/** @type {DirectMapping | null} */
		let direct;
		if (
			mapping !== null &&
			typeof mapping === "object" &&
			!Array.isArray(mapping)
		) {
			direct = conditionalMapping(
				/** @type {ConditionalMapping} */ (mapping),
				conditionNames,
			);
			if (direct === null) return EMPTY_NO_MATCH;
		} else {
			direct = /** @type {DirectMapping} */ (mapping);
		}

		return [
			directMapping(
				remainingRequest,
				isPattern,
				isSubpathMapping,
				direct,
				conditionNames,
				assertTarget,
			),
			usedField,
		];
	};
}

/**
 * @param {string} request request
 * @returns {string} updated request
 */
function assertExportsFieldRequest(request) {
	if (request.charCodeAt(0) !== dotCode) {
		throw new Error('Request should be relative path and start with "."');
	}
	if (request.length === 1) return "";
	if (request.charCodeAt(1) !== slashCode) {
		throw new Error('Request should be relative path and start with "./"');
	}
	if (request.charCodeAt(request.length - 1) === slashCode) {
		throw new Error("Only requesting file allowed");
	}

	return request.slice(2);
}

/**
 * @param {ExportsField} field exports field
 * @returns {ExportsField} normalized exports field
 */
function buildExportsField(field) {
	// handle syntax sugar, if exports field is direct mapping for "."
	if (typeof field === "string" || Array.isArray(field)) {
		return { ".": field };
	}

	const keys = Object.keys(field);

	for (let i = 0; i < keys.length; i++) {
		const key = keys[i];

		if (key.charCodeAt(0) !== dotCode) {
			// handle syntax sugar, if exports field is conditional mapping for "."
			if (i === 0) {
				while (i < keys.length) {
					const charCode = keys[i].charCodeAt(0);
					if (charCode === dotCode || charCode === slashCode) {
						throw new Error(
							`Exports field key should be relative path and start with "." (key: ${JSON.stringify(
								key,
							)})`,
						);
					}
					i++;
				}

				return { ".": field };
			}

			throw new Error(
				`Exports field key should be relative path and start with "." (key: ${JSON.stringify(
					key,
				)})`,
			);
		}

		if (key.length === 1) {
			continue;
		}

		if (key.charCodeAt(1) !== slashCode) {
			throw new Error(
				`Exports field key should be relative path and start with "./" (key: ${JSON.stringify(
					key,
				)})`,
			);
		}
	}

	return field;
}

/**
 * @param {string} exp export target
 * @param {boolean} expectFolder is folder expected
 */
function assertExportTarget(exp, expectFolder) {
	const parsedIdentifier = parseIdentifier(exp);

	if (!parsedIdentifier) {
		return;
	}

	const [relativePath] = parsedIdentifier;
	const isFolder =
		relativePath.charCodeAt(relativePath.length - 1) === slashCode;

	if (isFolder !== expectFolder) {
		throw new Error(
			expectFolder
				? `Expecting folder to folder mapping. ${JSON.stringify(
						exp,
					)} should end with "/"`
				: `Expecting file to file mapping. ${JSON.stringify(
						exp,
					)} should not end with "/"`,
		);
	}
}

/**
 * @param {ExportsField} exportsField the exports field
 * @returns {FieldProcessor} process callback
 */
module.exports.processExportsField = function processExportsField(
	exportsField,
) {
	return createFieldProcessor(
		buildExportsField(exportsField),
		(request) => (request.length === 0 ? "." : `./${request}`),
		assertExportsFieldRequest,
		assertExportTarget,
	);
};

/**
 * @param {string} request request
 * @returns {string} updated request
 */
function assertImportsFieldRequest(request) {
	if (request.charCodeAt(0) !== hashCode) {
		throw new Error('Request should start with "#"');
	}
	if (request.length === 1) {
		throw new Error("Request should have at least 2 characters");
	}
	// Note: #/ patterns are now allowed per Node.js PR #60864
	// https://github.com/nodejs/node/pull/60864
	if (request.charCodeAt(request.length - 1) === slashCode) {
		throw new Error("Only requesting file allowed");
	}

	return request.slice(1);
}

/**
 * @param {string} imp import target
 * @param {boolean} expectFolder is folder expected
 */
function assertImportTarget(imp, expectFolder) {
	const parsedIdentifier = parseIdentifier(imp);

	if (!parsedIdentifier) {
		return;
	}

	const [relativePath] = parsedIdentifier;
	const isFolder =
		relativePath.charCodeAt(relativePath.length - 1) === slashCode;

	if (isFolder !== expectFolder) {
		throw new Error(
			expectFolder
				? `Expecting folder to folder mapping. ${JSON.stringify(
						imp,
					)} should end with "/"`
				: `Expecting file to file mapping. ${JSON.stringify(
						imp,
					)} should not end with "/"`,
		);
	}
}

/**
 * @param {ImportsField} importsField the exports field
 * @returns {FieldProcessor} process callback
 */
module.exports.processImportsField = function processImportsField(
	importsField,
) {
	return createFieldProcessor(
		importsField,
		(request) => `#${request}`,
		assertImportsFieldRequest,
		assertImportTarget,
	);
};
