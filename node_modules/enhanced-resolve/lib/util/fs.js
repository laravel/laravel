/*
	MIT License http://www.opensource.org/licenses/mit-license.php
	Author Natsu @xiaoxiaojx
*/

"use strict";

const memoize = require("./memoize");
const stripJsonComments = require("./strip-json-comments");

/** @typedef {import("../Resolver").FileSystem} FileSystem */
/** @typedef {import("../Resolver").JsonObject} JsonObject */

/**
 * @typedef {object} ReadJsonOptions
 * @property {boolean=} stripComments Whether to strip JSONC comments
 */

/** @type {WeakMap<Buffer | Uint8Array, JsonObject>} */
const _stripCommentsCache = new WeakMap();

// Only constructed for non-Buffer input: on Node the `Buffer.isBuffer` branch
// in `decodeText` handles decoding, so the global `TextDecoder` (Node 11+,
// always present in browsers/Deno/Bun) is only reached off the Buffer path.
// `ignoreBOM: true` keeps a leading BOM in the output, matching
// `Buffer.toString("utf8")` so both decode paths behave identically.
// eslint-disable-next-line n/no-unsupported-features/node-builtins
const getDecoder = memoize(() => new TextDecoder("utf-8", { ignoreBOM: true }));

/**
 * Decode a file's raw contents to text without assuming a Node runtime. A
 * `Buffer` (Node) uses its fast native `toString`; any other binary input
 * (`Uint8Array` from a browser/Deno/Bun file system) goes through
 * `TextDecoder`, and strings are returned as-is.
 * @param {string | Buffer | Uint8Array} data raw file contents
 * @returns {string} decoded text
 */
const decodeText = (data) => {
	if (typeof data === "string") return data;
	if (typeof Buffer !== "undefined" && Buffer.isBuffer(data)) {
		return data.toString("utf8");
	}
	return getDecoder().decode(data);
};

/**
 * Read and parse JSON file (supports JSONC with comments).
 * Callback-based so a synchronous `fileSystem` stays synchronous all the
 * way through — Promise wrapping would defer resolution by a Promise tick
 * and break `resolveSync` when `tsconfig` is used together with
 * `useSyncFileSystemCalls: true`.
 * @param {FileSystem} fileSystem the file system
 * @param {string} jsonFilePath absolute path to JSON file
 * @param {ReadJsonOptions} options Options
 * @param {(err: NodeJS.ErrnoException | Error | null, content?: JsonObject) => void} callback callback
 * @returns {void}
 */
function readJson(fileSystem, jsonFilePath, options, callback) {
	const { stripComments = false } = options;
	const { readJson: fsReadJson } = fileSystem;
	if (fsReadJson && !stripComments) {
		fsReadJson(jsonFilePath, (err, content) => {
			if (err) return callback(err);
			callback(null, /** @type {JsonObject} */ (content));
		});
		return;
	}

	fileSystem.readFile(jsonFilePath, (err, data) => {
		if (err) return callback(err);
		const buf = /** @type {Buffer | Uint8Array | string} */ (data);

		// The strip-comments cache is keyed by the file-contents object; a file
		// system may hand back a plain string, which cannot be a WeakMap key, so
		// only cache when the contents are an object.
		const cacheable = stripComments && typeof buf === "object";

		if (cacheable) {
			const cached = _stripCommentsCache.get(buf);
			if (cached !== undefined) return callback(null, cached);
		}

		let result;
		try {
			const jsonText = decodeText(buf);
			const jsonWithoutComments = stripComments
				? stripJsonComments(jsonText, {
						trailingCommas: true,
						whitespace: true,
					})
				: jsonText;
			result = JSON.parse(jsonWithoutComments);
		} catch (parseErr) {
			return callback(/** @type {Error} */ (parseErr));
		}

		if (cacheable) {
			_stripCommentsCache.set(buf, result);
		}

		callback(null, result);
	});
}

module.exports.decodeText = decodeText;
module.exports.readJson = readJson;
