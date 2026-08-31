/*
	MIT License http://www.opensource.org/licenses/mit-license.php
	Author Tobias Koppers @sokra
*/

"use strict";

const forEachBail = require("./forEachBail");
const { PathType, getType } = require("./util/path");

/** @typedef {import("./Resolver")} Resolver */
/** @typedef {import("./Resolver").ResolveRequest} ResolveRequest */
/** @typedef {import("./Resolver").ResolveContext} ResolveContext */
/** @typedef {import("./Resolver").ResolveStepHook} ResolveStepHook */
/** @typedef {import("./Resolver").ResolveCallback} ResolveCallback */
/** @typedef {string | string[] | false} Alias */
/** @typedef {{ alias: Alias, name: string, onlyModule?: boolean }} AliasOption */

/**
 * @typedef {object} CompiledAliasOption
 * @property {string} name original alias name
 * @property {string} nameWithSlash name + "/" — precomputed to avoid per-resolve concat
 * @property {Alias} alias alias target(s)
 * @property {boolean} onlyModule normalized onlyModule flag
 * @property {string | null} absolutePath absolute form of `name` (with slash ending), null when not absolute
 * @property {string | null} wildcardPrefix substring before the single "*" in `name`, null when no wildcard
 * @property {string | null} wildcardSuffix substring after the single "*" in `name`, null when no wildcard
 * @property {number} firstCharCode first character code of `name` — used as a cheap screen on the hot path. `-1` indicates "matches any first char" (empty wildcard prefix).
 * @property {boolean} arrayAlias true when `alias` is an array — precomputed so the hot path skips `Array.isArray`
 */

/**
 * Bucketed view of compiled options used by `aliasResolveHandler` to avoid
 * walking the full option list on every resolve. The `all` array preserves
 * the legacy linear order (declaration order) for the fallback path. The
 * `byFirstChar` map buckets options by the first char code of their `name`
 * — each bucket preserves declaration order among its members. The
 * `hasAnyFirstChar` flag is true when at least one option matches any
 * first char (`firstCharCode === -1`), in which case resolve-time scans
 * fall back to `all` to keep declaration-order semantics across buckets.
 * The `useBuckets` flag is true only when bucketing would actually help —
 * i.e. there are at least 2 distinct first chars AND no empty-prefix
 * wildcard. When false, the resolve hot path skips the `Map.get` and
 * iterates `all` directly with the per-option first-char-code screen
 * (matching the pre-bucketing behavior). This avoids paying for `Map.get`
 * on degenerate single-bucket lists like a long chain of aliases that
 * all share one first char — the bucket lookup adds overhead without
 * narrowing the candidate set, which showed up as a transient-memory
 * regression on `pathological-deep-stack`.
 * @typedef {object} CompiledAliasOptions
 * @property {CompiledAliasOption[]} all declaration-ordered list
 * @property {Map<number, CompiledAliasOption[]>} byFirstChar bucketed by first char code
 * @property {boolean} hasAnyFirstChar true when an empty-prefix wildcard is present
 * @property {boolean} useBuckets true when the bucket fast-path should be used at resolve time
 */

const EMPTY_LIST = /** @type {CompiledAliasOption[]} */ ([]);
const EMPTY_COMPILED_OPTIONS = /** @type {CompiledAliasOptions} */ ({
	all: EMPTY_LIST,
	byFirstChar: new Map(),
	hasAnyFirstChar: false,
	useBuckets: false,
});

/**
 * Precompute per-option strings used on every resolve so the hot path in
 * `aliasResolveHandler` does no string concatenation / split work per entry.
 * Called once per plugin apply — the returned structure is stable for the
 * lifetime of the resolver.
 *
 * Beyond the per-option precompute step, this also partitions the list into
 * a `byFirstChar` map so that, when no "empty-prefix" wildcards are
 * present, the resolve-time scan only walks options whose `name` starts
 * with the same char as the current request. For large alias lists (300+
 * entries) this turns an O(N) screen into O(K) where K is the bucket size
 * for the request's first char.
 * @param {Resolver} resolver resolver
 * @param {AliasOption[]} options options
 * @returns {CompiledAliasOptions} compiled options
 */
function compileAliasOptions(resolver, options) {
	if (options.length === 0) return EMPTY_COMPILED_OPTIONS;
	const all = /** @type {CompiledAliasOption[]} */ (
		Array.from({ length: options.length })
	);
	/** @type {Map<number, CompiledAliasOption[]>} */
	const byFirstChar = new Map();
	let hasAnyFirstChar = false;
	for (let i = 0; i < options.length; i++) {
		const item = options[i];
		const { name } = item;
		let absolutePath = null;
		const type = getType(name);
		if (type === PathType.AbsolutePosix || type === PathType.AbsoluteWin) {
			absolutePath = resolver.join(name, "_").slice(0, -1);
		}
		const firstStar = name.indexOf("*");
		let wildcardPrefix = null;
		let wildcardSuffix = null;
		if (firstStar !== -1 && !name.includes("*", firstStar + 1)) {
			wildcardPrefix = name.slice(0, firstStar);
			wildcardSuffix = name.slice(firstStar + 1);
		}
		// firstCharCode: used by `aliasResolveHandler` to quickly skip aliases
		// whose name can't possibly match the current innerRequest. For a plain
		// alias (no wildcard) the first char of the name is also the first char
		// of `nameWithSlash` and of `absolutePath` (since the latter is derived
		// from name via `resolver.join(name, "_")`, which only appends). For a
		// wildcard with a non-empty prefix, the first char of that prefix is
		// also the first char of name. Only the `name === "*"` case (empty
		// wildcard prefix) can match arbitrary first chars — encode that as -1.
		let firstCharCode;
		if (wildcardPrefix !== null && wildcardPrefix.length === 0) {
			firstCharCode = -1;
		} else {
			firstCharCode = name.length > 0 ? name.charCodeAt(0) : -1;
		}
		const compiled = {
			name,
			nameWithSlash: `${name}/`,
			alias: item.alias,
			onlyModule: Boolean(item.onlyModule),
			absolutePath,
			wildcardPrefix,
			wildcardSuffix,
			firstCharCode,
			arrayAlias: Array.isArray(item.alias),
		};
		all[i] = compiled;
		if (firstCharCode === -1) {
			hasAnyFirstChar = true;
		} else {
			let bucket = byFirstChar.get(firstCharCode);
			if (bucket === undefined) {
				bucket = [];
				byFirstChar.set(firstCharCode, bucket);
			}
			bucket.push(compiled);
		}
	}
	// Only enable the bucket fast-path when it would actually help. With
	// a single bucket (all aliases share one first char, e.g. a chain of
	// `chain-0 -> chain-1 -> …` rewrites), the resolve-time `Map.get`
	// does no discrimination — every request lands in that one bucket
	// or in nothing — and the lookup is overhead compared to walking
	// `all` with the per-option first-char-code screen. Requiring 2+
	// distinct first chars matches the cases where bucketing has
	// measurable benefit (huge-alias-* / large-alias-list / stack-churn).
	const useBuckets = !hasAnyFirstChar && byFirstChar.size >= 2;
	return { all, byFirstChar, hasAnyFirstChar, useBuckets };
}

/** @typedef {(err?: null | Error, result?: null | ResolveRequest) => void} InnerCallback */
/**
 * @param {Resolver} resolver resolver
 * @param {CompiledAliasOptions} options compiled options
 * @param {ResolveStepHook} target target
 * @param {ResolveRequest} request request
 * @param {ResolveContext} resolveContext resolve context
 * @param {InnerCallback} callback callback
 * @returns {void}
 */
function aliasResolveHandler(
	resolver,
	options,
	target,
	request,
	resolveContext,
	callback,
) {
	if (options.all.length === 0) return callback();
	const innerRequest = request.request || request.path;
	if (!innerRequest) return callback();

	// Precompute values used in the inner scan loop so we don't recompute
	// them per option. This is meaningful when `options` has hundreds of
	// entries (e.g. monorepos with generated alias lists) — see the
	// `huge-alias-list` / `huge-alias-miss` benchmarks.
	const innerFirstCharCode = innerRequest.charCodeAt(0);
	const hasRequestString = Boolean(request.request);

	// Dispatch through the first-char-code bucket when it actually
	// narrows the candidate set (`useBuckets` requires 2+ distinct
	// first chars and no empty-prefix wildcard). When the field has
	// only one first-char bucket — e.g. a long chain of `chain-N`
	// aliases that all start with the same char — every request lands
	// in that one bucket or nothing, so `Map.get` is overhead vs. just
	// walking `all` with the per-option char-code screen. Walking
	// `all` also matches the pre-bucketing behavior and keeps the
	// `pathological-deep-stack` allocation profile flat.
	let scan;
	if (options.useBuckets) {
		const bucket = options.byFirstChar.get(innerFirstCharCode);
		if (bucket === undefined) return callback();
		scan = bucket;
	} else {
		scan = options.all;
	}

	forEachBail(
		scan,
		(item, callback) => {
			// Char-code screen left in for the fallback (`options.all`) path
			// where the bucket dispatch above wasn't usable. In the bucket
			// path this is always true and folds into a no-op.
			const { firstCharCode } = item;
			if (firstCharCode !== -1 && firstCharCode !== innerFirstCharCode) {
				return callback();
			}

			/** @type {boolean} */
			let shouldStop = false;

			// For absolute-name aliases, accept the normalized
			// `absolutePath` form as well as the raw `nameWithSlash`.
			// `nameWithSlash` unconditionally appends `/`, so a raw
			// windows request with native backslashes
			// (e.g. `C:\\abs\\foo\\baz` against `name: "C:\\abs\\foo"`)
			// otherwise fails `startsWith("C:\\abs\\foo/")` and is
			// silently skipped. Mirroring the `absolutePath` check in
			// both branches closes the gap without changing any
			// existing matches.
			const { absolutePath } = item;
			const matchRequest =
				innerRequest === item.name ||
				(!item.onlyModule &&
					((hasRequestString && innerRequest.startsWith(item.nameWithSlash)) ||
						(absolutePath !== null && innerRequest.startsWith(absolutePath))));

			const matchWildcard = !item.onlyModule && item.wildcardPrefix !== null;

			if (matchRequest || matchWildcard) {
				/**
				 * @param {Alias} alias alias
				 * @param {(err?: null | Error, result?: null | ResolveRequest) => void} callback callback
				 * @returns {void}
				 */
				const resolveWithAlias = (alias, callback) => {
					if (alias === false) {
						/** @type {ResolveRequest} */
						const ignoreObj = {
							...request,
							path: false,
						};
						if (typeof resolveContext.yield === "function") {
							resolveContext.yield(ignoreObj);
							return callback(null, null);
						}
						return callback(null, ignoreObj);
					}

					let newRequestStr;

					if (
						matchWildcard &&
						innerRequest.startsWith(
							/** @type {string} */ (item.wildcardPrefix),
						) &&
						innerRequest.endsWith(/** @type {string} */ (item.wildcardSuffix))
					) {
						const match = innerRequest.slice(
							/** @type {string} */ (item.wildcardPrefix).length,
							innerRequest.length -
								/** @type {string} */ (item.wildcardSuffix).length,
						);
						newRequestStr = alias.toString().replace("*", match);
					}

					if (
						matchRequest &&
						innerRequest !== alias &&
						!innerRequest.startsWith(`${alias}/`)
					) {
						/** @type {string} */
						const remainingRequest = innerRequest.slice(item.name.length);
						newRequestStr = alias + remainingRequest;
					}

					if (newRequestStr !== undefined) {
						shouldStop = true;
						/** @type {ResolveRequest} */
						const obj = {
							...request,
							request: newRequestStr,
							fullySpecified: false,
						};
						return resolver.doResolve(
							target,
							obj,
							`aliased with mapping '${item.name}': '${alias}' to '${newRequestStr}'`,
							resolveContext,
							(err, result) => {
								if (err) return callback(err);
								if (result) return callback(null, result);
								return callback();
							},
						);
					}
					return callback();
				};

				/**
				 * @param {(null | Error)=} err error
				 * @param {(null | ResolveRequest)=} result result
				 * @returns {void}
				 */
				const stoppingCallback = (err, result) => {
					if (err) return callback(err);

					if (result) return callback(null, result);
					// Don't allow other aliasing or raw request
					if (shouldStop) return callback(null, null);
					return callback();
				};

				if (item.arrayAlias) {
					return forEachBail(
						/** @type {string[]} */ (item.alias),
						resolveWithAlias,
						stoppingCallback,
					);
				}
				return resolveWithAlias(item.alias, stoppingCallback);
			}

			return callback();
		},
		callback,
	);
}

module.exports.aliasResolveHandler = aliasResolveHandler;
module.exports.compileAliasOptions = compileAliasOptions;
