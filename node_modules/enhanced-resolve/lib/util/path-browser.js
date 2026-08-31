/*
	MIT License http://www.opensource.org/licenses/mit-license.php
*/

"use strict";

// A browser shim for the Node `path` builtin, exposing only the subset used by
// `lib/util/path.js`: `posix.normalize`, `posix.dirname`, `win32.normalize`,
// `win32.dirname` and a (posix) `basename`. It is a faithful port of Node's
// `path` implementation and is wired in through the package `browser` field, so
// Node, Deno and Bun keep using their native `path` and only browser bundles
// use this. Inputs are always strings here, so Node's `validateString` guards
// are omitted.

const CHAR_UPPERCASE_A = 65;
const CHAR_UPPERCASE_Z = 90;
const CHAR_LOWERCASE_A = 97;
const CHAR_LOWERCASE_Z = 122;
const CHAR_DOT = 46;
const CHAR_FORWARD_SLASH = 47;
const CHAR_BACKWARD_SLASH = 92;
const CHAR_COLON = 58;

/**
 * @param {number} code char code
 * @returns {boolean} true for `/`
 */
const isPosixPathSeparator = (code) => code === CHAR_FORWARD_SLASH;

/**
 * @param {number} code char code
 * @returns {boolean} true for `/` or `\`
 */
const isPathSeparator = (code) =>
	code === CHAR_FORWARD_SLASH || code === CHAR_BACKWARD_SLASH;

/**
 * @param {number} code char code
 * @returns {boolean} true for an ASCII letter (a windows device root)
 */
const isWindowsDeviceRoot = (code) =>
	(code >= CHAR_UPPERCASE_A && code <= CHAR_UPPERCASE_Z) ||
	(code >= CHAR_LOWERCASE_A && code <= CHAR_LOWERCASE_Z);

/**
 * Resolve `.` and `..` segments in a path. Ported from Node's internal
 * `normalizeString`.
 * @param {string} path path
 * @param {boolean} allowAboveRoot whether leading `..` may be kept
 * @param {string} separator path separator to emit
 * @param {(code: number) => boolean} isSeparator separator predicate
 * @returns {string} normalized path string (without root)
 */
function normalizeString(path, allowAboveRoot, separator, isSeparator) {
	let res = "";
	let lastSegmentLength = 0;
	let lastSlash = -1;
	let dots = 0;
	let code = 0;
	for (let i = 0; i <= path.length; ++i) {
		if (i < path.length) {
			code = path.charCodeAt(i);
		} else if (isSeparator(code)) {
			break;
		} else {
			code = CHAR_FORWARD_SLASH;
		}

		if (isSeparator(code)) {
			if (lastSlash === i - 1 || dots === 1) {
				// NOOP
			} else if (dots === 2) {
				if (
					res.length < 2 ||
					lastSegmentLength !== 2 ||
					res.charCodeAt(res.length - 1) !== CHAR_DOT ||
					res.charCodeAt(res.length - 2) !== CHAR_DOT
				) {
					if (res.length > 2) {
						const lastSlashIndex = res.lastIndexOf(separator);
						if (lastSlashIndex === -1) {
							res = "";
							lastSegmentLength = 0;
						} else {
							res = res.slice(0, lastSlashIndex);
							lastSegmentLength = res.length - 1 - res.lastIndexOf(separator);
						}
						lastSlash = i;
						dots = 0;
						continue;
					} else if (res.length !== 0) {
						res = "";
						lastSegmentLength = 0;
						lastSlash = i;
						dots = 0;
						continue;
					}
				}
				if (allowAboveRoot) {
					res += res.length > 0 ? `${separator}..` : "..";
					lastSegmentLength = 2;
				}
			} else {
				if (res.length > 0) {
					res += `${separator}${path.slice(lastSlash + 1, i)}`;
				} else {
					res = path.slice(lastSlash + 1, i);
				}
				lastSegmentLength = i - lastSlash - 1;
			}
			lastSlash = i;
			dots = 0;
		} else if (code === CHAR_DOT && dots !== -1) {
			++dots;
		} else {
			dots = -1;
		}
	}
	return res;
}

/**
 * @param {string} path path
 * @returns {string} normalized posix path
 */
function posixNormalize(path) {
	if (path.length === 0) return ".";

	const isAbsolute = path.charCodeAt(0) === CHAR_FORWARD_SLASH;
	const trailingSeparator =
		path.charCodeAt(path.length - 1) === CHAR_FORWARD_SLASH;

	path = normalizeString(path, !isAbsolute, "/", isPosixPathSeparator);

	if (path.length === 0) {
		if (isAbsolute) return "/";
		return trailingSeparator ? "./" : ".";
	}
	if (trailingSeparator) path += "/";

	return isAbsolute ? `/${path}` : path;
}

/**
 * @param {string} path path
 * @returns {string} posix dirname
 */
function posixDirname(path) {
	if (path.length === 0) return ".";
	const hasRoot = path.charCodeAt(0) === CHAR_FORWARD_SLASH;
	let end = -1;
	let matchedSlash = true;
	for (let i = path.length - 1; i >= 1; --i) {
		if (path.charCodeAt(i) === CHAR_FORWARD_SLASH) {
			if (!matchedSlash) {
				end = i;
				break;
			}
		} else {
			matchedSlash = false;
		}
	}

	if (end === -1) return hasRoot ? "/" : ".";
	if (hasRoot && end === 1) return "//";
	return path.slice(0, end);
}

/**
 * Normalizes drive paths (`C:\…`), UNC paths (`\\server\share\…`), DOS device
 * paths (`\\.\…`, `\\?\…`) and relative/normal segments like Node's
 * `path.win32.normalize`, including the CVE-2024-36139 colon-segment guard.
 *
 * Scope note: reserved Windows device names (`CON`, `COM1`, `LPT1`, …) are not
 * special-cased, so e.g. `\\.\COM1:` differs from Node. Such names cannot occur
 * in a browser (the only place this shim is used) and the resolver never routes
 * them to `win32.normalize`, so this does not affect resolution.
 * @param {string} path path
 * @returns {string} normalized win32 path
 */
function win32Normalize(path) {
	const len = path.length;
	if (len === 0) return ".";
	let rootEnd = 0;
	/** @type {string | undefined} */
	let device;
	let isAbsolute = false;
	const code = path.charCodeAt(0);

	if (len === 1) {
		return isPosixPathSeparator(code) ? "\\" : path;
	}

	if (isPathSeparator(code)) {
		// Possible UNC root; an initial separator means an absolute path.
		isAbsolute = true;

		if (isPathSeparator(path.charCodeAt(1))) {
			// Matched double path separator at beginning
			let j = 2;
			let last = j;
			while (j < len && !isPathSeparator(path.charCodeAt(j))) j++;
			if (j < len && j !== last) {
				const firstPart = path.slice(last, j);
				last = j;
				while (j < len && isPathSeparator(path.charCodeAt(j))) j++;
				if (j < len && j !== last) {
					last = j;
					while (j < len && !isPathSeparator(path.charCodeAt(j))) j++;
					if (j === len || j !== last) {
						if (firstPart === "." || firstPart === "?") {
							// Device root, e.g. `\\.\pipe\…` or `\\?\C:\…`
							device = `\\\\${firstPart}`;
							rootEnd = 4;
						} else if (j === len) {
							// Matched a UNC root only
							return `\\\\${firstPart}\\${path.slice(last)}\\`;
						} else {
							// Matched a UNC root with leftovers
							device = `\\\\${firstPart}\\${path.slice(last, j)}`;
							rootEnd = j;
						}
					}
				}
			}
		} else {
			rootEnd = 1;
		}
	} else if (isWindowsDeviceRoot(code) && path.charCodeAt(1) === CHAR_COLON) {
		// Possible device root
		device = path.slice(0, 2);
		rootEnd = 2;
		if (len > 2 && isPathSeparator(path.charCodeAt(2))) {
			isAbsolute = true;
			rootEnd = 3;
		}
	}

	let tail =
		rootEnd < len
			? normalizeString(path.slice(rootEnd), !isAbsolute, "\\", isPathSeparator)
			: "";
	if (tail.length === 0 && !isAbsolute) tail = ".";
	if (tail.length > 0 && isPathSeparator(path.charCodeAt(len - 1))) {
		tail += "\\";
	}
	if (!isAbsolute && device === undefined && path.includes(":")) {
		// A relative path that wasn't resolved to a device must not turn into
		// something Windows could read as an absolute/drive path (CVE-2024-36139).
		if (
			tail.length >= 2 &&
			isWindowsDeviceRoot(tail.charCodeAt(0)) &&
			tail.charCodeAt(1) === CHAR_COLON
		) {
			return `.\\${tail}`;
		}
		let index = path.indexOf(":");
		do {
			if (index === len - 1 || isPathSeparator(path.charCodeAt(index + 1))) {
				return `.\\${tail}`;
			}
		} while ((index = path.indexOf(":", index + 1)) !== -1);
	}
	if (device === undefined) {
		return isAbsolute ? `\\${tail}` : tail;
	}
	return isAbsolute ? `${device}\\${tail}` : `${device}${tail}`;
}

/**
 * @param {string} path path
 * @returns {string} win32 dirname
 */
function win32Dirname(path) {
	const len = path.length;
	if (len === 0) return ".";
	let rootEnd = -1;
	let offset = 0;
	const code = path.charCodeAt(0);

	if (len === 1) {
		return isPathSeparator(code) ? path : ".";
	}

	if (isPathSeparator(code)) {
		// Possible UNC root
		rootEnd = offset = 1;

		if (isPathSeparator(path.charCodeAt(1))) {
			let j = 2;
			let last = j;
			while (j < len && !isPathSeparator(path.charCodeAt(j))) j++;
			if (j < len && j !== last) {
				last = j;
				while (j < len && isPathSeparator(path.charCodeAt(j))) j++;
				if (j < len && j !== last) {
					last = j;
					while (j < len && !isPathSeparator(path.charCodeAt(j))) j++;
					if (j === len) {
						// Matched a UNC root only
						return path;
					}
					if (j !== last) {
						// Matched a UNC root with leftovers
						rootEnd = offset = j + 1;
					}
				}
			}
		}
	} else if (isWindowsDeviceRoot(code) && path.charCodeAt(1) === CHAR_COLON) {
		rootEnd = len > 2 && isPathSeparator(path.charCodeAt(2)) ? 3 : 2;
		offset = rootEnd;
	}

	let end = -1;
	let matchedSlash = true;
	for (let i = len - 1; i >= offset; --i) {
		if (isPathSeparator(path.charCodeAt(i))) {
			if (!matchedSlash) {
				end = i;
				break;
			}
		} else {
			matchedSlash = false;
		}
	}

	if (end === -1) {
		if (rootEnd === -1) return ".";
		end = rootEnd;
	}
	return path.slice(0, end);
}

/**
 * Posix `basename` — the browser is treated as a posix platform, matching how
 * Node picks the posix variant for `path.basename` on non-Windows systems.
 * @param {string} path path
 * @param {string=} suffix optional suffix to strip
 * @returns {string} basename
 */
function basename(path, suffix) {
	let start = 0;
	let end = -1;
	let matchedSlash = true;

	if (
		suffix !== undefined &&
		suffix.length > 0 &&
		suffix.length <= path.length
	) {
		if (suffix === path) return "";
		let extIdx = suffix.length - 1;
		let firstNonSlashEnd = -1;
		for (let i = path.length - 1; i >= 0; --i) {
			const code = path.charCodeAt(i);
			if (code === CHAR_FORWARD_SLASH) {
				if (!matchedSlash) {
					start = i + 1;
					break;
				}
			} else {
				if (firstNonSlashEnd === -1) {
					matchedSlash = false;
					firstNonSlashEnd = i + 1;
				}
				if (extIdx >= 0) {
					if (code === suffix.charCodeAt(extIdx)) {
						if (--extIdx === -1) {
							end = i;
						}
					} else {
						extIdx = -1;
						end = firstNonSlashEnd;
					}
				}
			}
		}

		if (start === end) {
			end = firstNonSlashEnd;
		} else if (end === -1) {
			end = path.length;
		}
		return path.slice(start, end);
	}
	for (let i = path.length - 1; i >= 0; --i) {
		if (path.charCodeAt(i) === CHAR_FORWARD_SLASH) {
			if (!matchedSlash) {
				start = i + 1;
				break;
			}
		} else if (end === -1) {
			matchedSlash = false;
			end = i + 1;
		}
	}

	if (end === -1) return "";
	return path.slice(start, end);
}

module.exports = {
	basename,
	posix: { normalize: posixNormalize, dirname: posixDirname },
	win32: { normalize: win32Normalize, dirname: win32Dirname },
};
