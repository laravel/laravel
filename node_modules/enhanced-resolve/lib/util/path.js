/*
	MIT License http://www.opensource.org/licenses/mit-license.php
	Author Tobias Koppers @sokra
*/

"use strict";

const path = require("path");
const { fileURLToPath } = require("url");

const CHAR_HASH = "#".charCodeAt(0);
const CHAR_SLASH = "/".charCodeAt(0);
const CHAR_BACKSLASH = "\\".charCodeAt(0);
const CHAR_A = "A".charCodeAt(0);
const CHAR_Z = "Z".charCodeAt(0);
const CHAR_LOWER_A = "a".charCodeAt(0);
const CHAR_LOWER_Z = "z".charCodeAt(0);
const CHAR_DOT = ".".charCodeAt(0);
const CHAR_COLON = ":".charCodeAt(0);
const CHAR_QUESTION = "?".charCodeAt(0);

const posixNormalize = path.posix.normalize;
const winNormalize = path.win32.normalize;

/**
 * @enum {number}
 */
const PathType = Object.freeze({
	Empty: 0,
	Normal: 1,
	Relative: 2,
	AbsoluteWin: 3,
	AbsolutePosix: 4,
	Internal: 5,
});

const deprecatedInvalidSegmentRegEx =
	/(^|\\|\/)((\.|%2e)(\.|%2e)?|(n|%6e|%4e)(o|%6f|%4f)(d|%64|%44)(e|%65|%45)(_|%5f)(m|%6d|%4d)(o|%6f|%4f)(d|%64|%44)(u|%75|%55)(l|%6c|%4c)(e|%65|%45)(s|%73|%53))(\\|\/|$)/i;

const invalidSegmentRegEx =
	/(^|\\|\/)((\.|%2e)(\.|%2e)?|(n|%6e|%4e)(o|%6f|%4f)(d|%64|%44)(e|%65|%45)(_|%5f)(m|%6d|%4d)(o|%6f|%4f)(d|%64|%44)(u|%75|%55)(l|%6c|%4c)(e|%65|%45)(s|%73|%53))?(\\|\/|$)/i;

/**
 * @param {string} maybePath a path known to start with `\\`
 * @returns {PathType} AbsoluteWin for `\\?\…` / `\\.\…`, otherwise Normal
 */
const getDosDeviceType = (maybePath) => {
	if (maybePath.length >= 4 && maybePath.charCodeAt(3) === CHAR_BACKSLASH) {
		const c2 = maybePath.charCodeAt(2);
		if (c2 === CHAR_QUESTION || c2 === CHAR_DOT) {
			return PathType.AbsoluteWin;
		}
	}
	return PathType.Normal;
};

/**
 * @param {string} maybePath a path
 * @returns {PathType} type of path
 */
const getType = (maybePath) => {
	switch (maybePath.length) {
		case 0:
			return PathType.Empty;
		case 1: {
			const c0 = maybePath.charCodeAt(0);
			switch (c0) {
				case CHAR_DOT:
					return PathType.Relative;
				case CHAR_SLASH:
					return PathType.AbsolutePosix;
				case CHAR_HASH:
					return PathType.Internal;
			}
			return PathType.Normal;
		}
		case 2: {
			const c0 = maybePath.charCodeAt(0);
			switch (c0) {
				case CHAR_DOT: {
					const c1 = maybePath.charCodeAt(1);
					switch (c1) {
						case CHAR_DOT:
						case CHAR_SLASH:
							return PathType.Relative;
					}
					return PathType.Normal;
				}
				case CHAR_SLASH:
					return PathType.AbsolutePosix;
				case CHAR_HASH:
					return PathType.Internal;
			}
			const c1 = maybePath.charCodeAt(1);
			if (
				c1 === CHAR_COLON &&
				((c0 >= CHAR_A && c0 <= CHAR_Z) ||
					(c0 >= CHAR_LOWER_A && c0 <= CHAR_LOWER_Z))
			) {
				return PathType.AbsoluteWin;
			}
			return PathType.Normal;
		}
	}
	const c0 = maybePath.charCodeAt(0);
	switch (c0) {
		case CHAR_DOT: {
			const c1 = maybePath.charCodeAt(1);
			switch (c1) {
				case CHAR_SLASH:
					return PathType.Relative;
				case CHAR_DOT: {
					const c2 = maybePath.charCodeAt(2);
					if (c2 === CHAR_SLASH) return PathType.Relative;
					return PathType.Normal;
				}
			}
			return PathType.Normal;
		}
		case CHAR_SLASH:
			return PathType.AbsolutePosix;
		case CHAR_HASH:
			return PathType.Internal;
	}
	const c1 = maybePath.charCodeAt(1);
	if (c1 === CHAR_COLON) {
		const c2 = maybePath.charCodeAt(2);
		if (
			(c2 === CHAR_BACKSLASH || c2 === CHAR_SLASH) &&
			((c0 >= CHAR_A && c0 <= CHAR_Z) ||
				(c0 >= CHAR_LOWER_A && c0 <= CHAR_LOWER_Z))
		) {
			return PathType.AbsoluteWin;
		}
	}
	// DOS device paths (`\\?\…`, `\\.\…`) are handled in a cold helper so
	// this function stays small — inlining the full check here regressed
	// `description-files-multi` under `--no-opt` interpretation. Here we
	// only pay the two-byte gate for non-DOS inputs.
	if (c0 === CHAR_BACKSLASH && c1 === CHAR_BACKSLASH) {
		return getDosDeviceType(maybePath);
	}
	return PathType.Normal;
};

/**
 * @param {string} maybePath a path
 * @returns {string} the normalized path
 */
const normalize = (maybePath) => {
	switch (getType(maybePath)) {
		case PathType.Empty:
			return maybePath;
		case PathType.AbsoluteWin:
			return winNormalize(maybePath);
		case PathType.Relative: {
			const r = posixNormalize(maybePath);
			return getType(r) === PathType.Relative ? r : `./${r}`;
		}
	}
	return posixNormalize(maybePath);
};

/**
 * @param {string} rootPath the root path
 * @param {string | undefined} request the request path
 * @returns {string} the joined path
 */
const join = (rootPath, request) => {
	if (!request) return normalize(rootPath);
	const requestType = getType(request);
	switch (requestType) {
		case PathType.AbsolutePosix:
			return posixNormalize(request);
		case PathType.AbsoluteWin:
			return winNormalize(request);
	}
	switch (getType(rootPath)) {
		case PathType.Normal:
		case PathType.Relative:
		case PathType.AbsolutePosix:
			return posixNormalize(`${rootPath}/${request}`);
		case PathType.AbsoluteWin:
			return winNormalize(`${rootPath}\\${request}`);
	}
	switch (requestType) {
		case PathType.Empty:
			return rootPath;
		case PathType.Relative: {
			const r = posixNormalize(rootPath);
			return getType(r) === PathType.Relative ? r : `./${r}`;
		}
	}
	return posixNormalize(rootPath);
};

/**
 * @param {string} maybePath a path
 * @returns {string} the directory name
 */
const dirname = (maybePath) => {
	switch (getType(maybePath)) {
		case PathType.AbsoluteWin:
			return path.win32.dirname(maybePath);
	}
	return path.posix.dirname(maybePath);
};

/** @typedef {{ fn: (rootPath: string, request: string) => string, cache: Map<string, Map<string, string | undefined>> }} CachedJoin */

/**
 * @returns {CachedJoin} cached join
 */
const createCachedJoin = () => {
	/** @type {CachedJoin["cache"]} */
	const cache = new Map();
	/** @type {CachedJoin["fn"]} */
	const fn = (rootPath, request) => {
		/** @type {string | undefined} */
		let cacheEntry;
		let inner = cache.get(rootPath);
		if (inner === undefined) {
			cache.set(rootPath, (inner = new Map()));
		} else {
			cacheEntry = inner.get(request);
			if (cacheEntry !== undefined) return cacheEntry;
		}
		cacheEntry = join(rootPath, request);
		inner.set(request, cacheEntry);
		return cacheEntry;
	};
	return { fn, cache };
};

/** @typedef {{ fn: (maybePath: string) => string, cache: Map<string, string> }} CachedDirname */

/**
 * @returns {CachedDirname} cached dirname
 */
const createCachedDirname = () => {
	/** @type {CachedDirname["cache"]} */
	const cache = new Map();
	/** @type {CachedDirname["fn"]} */
	const fn = (maybePath) => {
		const cacheEntry = cache.get(maybePath);
		if (cacheEntry !== undefined) return cacheEntry;
		const result = dirname(maybePath);
		cache.set(maybePath, result);
		return result;
	};
	return { fn, cache };
};

/** @typedef {{ fn: (maybePath: string, suffix?: string) => string, cache: Map<string, Map<string | undefined, string | undefined>> }} CachedBasename */

/**
 * @returns {CachedBasename} cached basename
 */
const createCachedBasename = () => {
	/** @type {CachedBasename["cache"]} */
	const cache = new Map();
	/** @type {CachedBasename["fn"]} */
	const fn = (maybePath, suffix) => {
		/** @type {string | undefined} */
		let cacheEntry;
		let inner = cache.get(maybePath);
		if (inner === undefined) {
			cache.set(maybePath, (inner = new Map()));
		} else {
			cacheEntry = inner.get(suffix);
			if (cacheEntry !== undefined) return cacheEntry;
		}
		cacheEntry = path.basename(maybePath, suffix);
		inner.set(suffix, cacheEntry);
		return cacheEntry;
	};
	return { fn, cache };
};

/**
 * Whether `request` is a relative request — i.e. matches `^\.\.?(?:\/|$)`.
 *
 * This is called on every `doResolve` via `UnsafeCachePlugin` and
 * `getInnerRequest`, so the char-code form is meaningfully faster than the
 * equivalent regex test: no regex state machine, no string object churn.
 * @param {string} request request string
 * @returns {boolean} true if request is relative
 */
const isRelativeRequest = (request) => {
	const len = request.length;
	if (len === 0 || request.charCodeAt(0) !== CHAR_DOT) return false;
	if (len === 1) return true; // "."
	const c1 = request.charCodeAt(1);
	if (c1 === CHAR_SLASH) return true; // "./..."
	if (c1 !== CHAR_DOT) return false; // ".x..."
	if (len === 2) return true; // ".."
	return request.charCodeAt(2) === CHAR_SLASH; // "../..."
};

/**
 * Check if childPath is a subdirectory of parentPath.
 *
 * Called from `TsconfigPathsPlugin._selectPathsDataForContext` inside a loop
 * over every tsconfig-paths context on every resolve, so it's worth keeping
 * cheap. Compared to the previous `startsWith(normalize(parent + "/"))`
 * version, this: checks the last char with `charCodeAt` instead of two
 * `endsWith` calls; and skips `normalize()` entirely in the common case
 * (parent has no trailing separator), since all we really need is the same
 * anchoring effect — a cheap `startsWith` plus a separator char check on the
 * byte immediately after `parentPath.length`.
 * @param {string} parentPath parent directory path
 * @param {string} childPath child path to check
 * @returns {boolean} true if childPath is under parentPath
 */
const isSubPath = (parentPath, childPath) => {
	const parentLen = parentPath.length;
	if (parentLen === 0) {
		// Match the old `normalize("" + "/") === "/"` fallback: an empty
		// parent only "contains" a child that starts with a forward slash.
		return childPath.length > 0 && childPath.charCodeAt(0) === CHAR_SLASH;
	}
	const lastChar = parentPath.charCodeAt(parentLen - 1);
	if (lastChar === CHAR_SLASH || lastChar === CHAR_BACKSLASH) {
		// Parent already ends with a separator — a plain prefix test is enough.
		return childPath.startsWith(parentPath);
	}
	if (childPath.length <= parentLen) return false;
	if (!childPath.startsWith(parentPath)) return false;
	// Must be followed by a separator so "/app" doesn't match "/app-other".
	const nextChar = childPath.charCodeAt(parentLen);
	return nextChar === CHAR_SLASH || nextChar === CHAR_BACKSLASH;
};

/**
 * Convert a `file:` `URL` instance to a filesystem path; any other input
 * (including plain strings) is returned unchanged. Mirrors Node's `fs`, which
 * treats strings as literal paths and only `URL` objects as URLs (see
 * nodejs/node#17658) — so a directory literally named `file:` is never
 * mistaken for a URL.
 * @param {string | URL} maybeURL a path string or a `file:` `URL` instance
 * @returns {string} a filesystem path
 */
const toPath = (maybeURL) =>
	maybeURL instanceof URL ? fileURLToPath(maybeURL) : maybeURL;

module.exports.PathType = PathType;
module.exports.createCachedBasename = createCachedBasename;
module.exports.createCachedDirname = createCachedDirname;
module.exports.createCachedJoin = createCachedJoin;
module.exports.deprecatedInvalidSegmentRegEx = deprecatedInvalidSegmentRegEx;
module.exports.dirname = dirname;
module.exports.getType = getType;
module.exports.invalidSegmentRegEx = invalidSegmentRegEx;
module.exports.isRelativeRequest = isRelativeRequest;
module.exports.isSubPath = isSubPath;
module.exports.join = join;
module.exports.normalize = normalize;
module.exports.toPath = toPath;
