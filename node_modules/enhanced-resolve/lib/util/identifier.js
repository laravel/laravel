/*
	MIT License http://www.opensource.org/licenses/mit-license.php
	Author Ivan Kopeykin @vankop
*/

"use strict";

const { fileURLToPath } = require("url");

const PATH_QUERY_FRAGMENT_REGEXP =
	/^(#?(?:\0.|[^?#\0])*)(\?(?:\0.|[^#\0])*)?(#.*)?$/;
const ZERO_ESCAPE_REGEXP = /\0(.)/g;
const FILE_REG_EXP = /file:/i;

/**
 * Index past a DOS device path prefix (`\\?\…` or `\\.\…`), or 0. Kept
 * out of `parseIdentifier` on purpose: inlining it back bloats the caller
 * beyond the size where V8's interpreter and JIT both handle it well
 * (the cause of the description-files-multi CodSpeed regression).
 * @param {string} identifier identifier known to start with `\`
 * @returns {number} 4 if identifier starts with a DOS device prefix, else 0
 */
function dosPrefixEnd(identifier) {
	if (
		identifier.length >= 4 &&
		identifier.charCodeAt(1) === 92 &&
		identifier.charCodeAt(3) === 92
	) {
		const c2 = identifier.charCodeAt(2);
		if (c2 === 63 || c2 === 46) return 4;
	}
	return 0;
}

/**
 * @param {string} identifier identifier
 * @returns {[string, string, string] | null} parsed identifier
 */
function parseIdentifier(identifier) {
	if (!identifier) {
		return null;
	}

	if (FILE_REG_EXP.test(identifier)) {
		identifier = fileURLToPath(identifier);
	}

	const firstEscape = identifier.indexOf("\0");

	// Handle `\0`
	if (firstEscape !== -1) {
		const match = PATH_QUERY_FRAGMENT_REGEXP.exec(identifier);

		if (!match) return null;

		return [
			match[1].replace(ZERO_ESCAPE_REGEXP, "$1"),
			match[2] ? match[2].replace(ZERO_ESCAPE_REGEXP, "$1") : "",
			match[3] || "",
		];
	}

	// Fast path for inputs that don't use \0 escaping. DOS device paths
	// (`\\?\…`, `\\.\…`) embed a literal `?` / `.` that must not be read
	// as a query separator; skip past the prefix when the input actually
	// starts with `\`. Gate is a single char-code compare so this function
	// stays inside V8's inline budget for its hot callers (resolver parse).
	const scanStart =
		identifier.charCodeAt(0) === 92 ? dosPrefixEnd(identifier) : 0;
	const queryStart = identifier.indexOf("?", scanStart);
	// Start at index 1 (or past a DOS prefix) to ignore a possible leading hash.
	const fragmentStart = identifier.indexOf("#", scanStart || 1);

	if (fragmentStart < 0) {
		if (queryStart < 0) {
			// No fragment, no query
			return [identifier, "", ""];
		}

		// Query, no fragment
		return [identifier.slice(0, queryStart), identifier.slice(queryStart), ""];
	}

	if (queryStart < 0 || fragmentStart < queryStart) {
		// Fragment, no query
		return [
			identifier.slice(0, fragmentStart),
			"",
			identifier.slice(fragmentStart),
		];
	}

	// Query and fragment
	return [
		identifier.slice(0, queryStart),
		identifier.slice(queryStart, fragmentStart),
		identifier.slice(fragmentStart),
	];
}

module.exports.parseIdentifier = parseIdentifier;
