/*
	MIT License http://www.opensource.org/licenses/mit-license.php
	Author Tobias Koppers @sokra
*/

"use strict";

/** @typedef {import("./Resolver").FileSystem} FileSystem */
/** @typedef {{ paths: string[], segments: string[] }} GetPathsResult */

/**
 * Walk `path` from tip to root, returning every ancestor directory (plus the
 * input itself) in `paths`, and each corresponding segment name in `segments`.
 *
 * The return value may be shared across callers via `getPathsCached` — treat
 * it as read-only. Callers that need to mutate (currently only
 * `SymlinkPlugin`) should `slice()` the arrays locally before writing.
 * @param {string} path path
 * @returns {GetPathsResult} paths and segments
 */
function getPaths(path) {
	if (path === "/") return { paths: ["/"], segments: [""] };
	const parts = path.split(/(.*?[\\/]+)/);
	const paths = [path];
	const segments = [parts[parts.length - 1]];
	let part = parts[parts.length - 1];
	path = path.slice(0, Math.max(0, path.length - part.length - 1));
	for (let i = parts.length - 2; i > 2; i -= 2) {
		paths.push(path);
		part = parts[i];
		path = path.slice(0, Math.max(0, path.length - part.length)) || "/";
		segments.push(part.slice(0, -1));
	}
	[, part] = parts;
	segments.push(part);
	paths.push(part);
	return {
		paths,
		segments,
	};
}

/**
 * Per-filesystem memoization of `getPaths`. Kept in a standalone WeakMap
 * rather than being hung off `resolver.pathCache` so that adding this cache
 * does not change the hidden-class shape of `pathCache` — which is accessed
 * on the hot path of every resolve as `resolver.pathCache.{join,dirname,
 * basename}.fn(...)`. CodSpeed caught that shape change as a ~1–2%
 * instruction-count regression on `cache-predicate`, so we keep pathCache
 * shape-stable by owning this cache here instead.
 *
 * The cache lifetime is tied to the filesystem object (same invariant as
 * `_pathCacheByFs` in `Resolver.js`): when the user swaps filesystems the
 * entries become unreachable and get collected.
 * @type {WeakMap<FileSystem, Map<string, GetPathsResult>>}
 */
const _getPathsCacheByFs = new WeakMap();

/**
 * Memoized `getPaths`. The returned object is shared across callers — do
 * not mutate the `paths` or `segments` arrays in-place; `slice()` first if
 * you need a mutable copy.
 * @param {FileSystem} fileSystem filesystem used as the cache namespace
 * @param {string} path path
 * @returns {GetPathsResult} paths and segments
 */
function getPathsCached(fileSystem, path) {
	let cache = _getPathsCacheByFs.get(fileSystem);
	if (cache === undefined) {
		cache = new Map();
		_getPathsCacheByFs.set(fileSystem, cache);
	} else {
		const cached = cache.get(path);
		if (cached !== undefined) return cached;
	}
	const result = getPaths(path);
	cache.set(path, result);
	return result;
}

module.exports = getPaths;
module.exports.getPathsCached = getPathsCached;
