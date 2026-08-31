/*
	MIT License http://www.opensource.org/licenses/mit-license.php
*/

"use strict";

// A runtime-agnostic port of Node's `url.fileURLToPath`, built on the global
// `URL` (available in Node, browsers, Deno and Bun) so the resolver does not
// depend on the Node `url` builtin. The platform branch can be forced via the
// `windows` option; otherwise it follows the host platform like Node does.

const CHAR_LOWERCASE_A = 97;
const CHAR_LOWERCASE_Z = 122;

const isWindows =
	typeof process !== "undefined" && process.platform === "win32";

const forwardSlashRegEx = /\//g;

/**
 * @param {URL} url file: URL instance
 * @returns {string} Windows filesystem path
 */
function getPathFromURLWin32(url) {
	const { hostname } = url;
	let { pathname } = url;
	for (let n = 0; n < pathname.length; n++) {
		if (pathname[n] === "%") {
			const third = (pathname.codePointAt(n + 2) || 0) | 0x20;
			if (
				(pathname[n + 1] === "2" && third === 102) || // 2f 2F (/)
				(pathname[n + 1] === "5" && third === 99) // 5c 5C (\)
			) {
				throw new TypeError(
					"File URL path must not include encoded \\ or / characters",
				);
			}
		}
	}
	pathname = pathname.replace(forwardSlashRegEx, "\\");
	pathname = decodeURIComponent(pathname);
	if (hostname !== "") {
		// UNC path: `\\host\share\…`. Node runs the host through
		// `domainToUnicode`; we keep the (already punycode) hostname as-is,
		// which is identical for ASCII hosts and only differs for rare IDN UNC.
		return `\\\\${hostname}${pathname}`;
	}
	const letter = (pathname.codePointAt(1) || 0) | 0x20;
	const sep = pathname.charAt(2);
	if (letter < CHAR_LOWERCASE_A || letter > CHAR_LOWERCASE_Z || sep !== ":") {
		throw new TypeError("File URL path must be absolute");
	}
	return pathname.slice(1);
}

/**
 * @param {URL} url file: URL instance
 * @returns {string} POSIX filesystem path
 */
function getPathFromURLPosix(url) {
	if (url.hostname !== "") {
		throw new TypeError('File URL host must be "localhost" or empty');
	}
	const { pathname } = url;
	for (let n = 0; n < pathname.length; n++) {
		if (pathname[n] === "%") {
			const third = (pathname.codePointAt(n + 2) || 0) | 0x20;
			if (pathname[n + 1] === "2" && third === 102) {
				throw new TypeError(
					"File URL path must not include encoded / characters",
				);
			}
		}
	}
	return decodeURIComponent(pathname);
}

/**
 * @param {string | URL} path a `file:` URL string or `URL` instance
 * @param {{ windows?: boolean }=} options force the platform branch
 * @returns {string} the filesystem path
 */
function fileURLToPath(path, options) {
	const url = typeof path === "string" ? new URL(path) : path;
	if (url.protocol !== "file:") {
		throw new TypeError("The URL must be of scheme file");
	}
	const windows =
		options && options.windows !== undefined ? options.windows : isWindows;
	return windows ? getPathFromURLWin32(url) : getPathFromURLPosix(url);
}

module.exports = fileURLToPath;
