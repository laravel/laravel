/*
	MIT License http://www.opensource.org/licenses/mit-license.php
	Author Tobias Koppers @sokra
*/

"use strict";

const forEachBail = require("./forEachBail");
const { decodeText } = require("./util/fs");

/** @typedef {import("./Resolver")} Resolver */
/** @typedef {import("./Resolver").JsonObject} JsonObject */
/** @typedef {import("./Resolver").JsonValue} JsonValue */
/** @typedef {import("./Resolver").ResolveContext} ResolveContext */
/** @typedef {import("./Resolver").ResolveRequest} ResolveRequest */

/**
 * @typedef {object} DescriptionFileInfo
 * @property {JsonObject=} content content
 * @property {string} path path
 * @property {string} directory directory
 */

/**
 * @callback ErrorFirstCallback
 * @param {Error | null=} error
 * @param {DescriptionFileInfo=} result
 */

/**
 * @typedef {object} Result
 * @property {string} path path to description file
 * @property {string} directory directory of description file
 * @property {JsonObject} content content of description file
 */

const CHAR_SLASH = 47;
const CHAR_BACKSLASH = 92;

/**
 * Walk up one directory. Called once per package-root candidate and once per
 * `described-resolve` (to find the enclosing description file), so it's on
 * the resolver's hot path.
 *
 * Previous implementation called `lastIndexOf("/")` and `lastIndexOf("\\")`
 * separately and then picked the larger. For any non-trivial directory
 * string on POSIX, `lastIndexOf("\\")` scans the full string just to return
 * -1. A single reverse char-code scan does the same work in one pass.
 *
 * Any single-character directory is treated as a root — `directory.length
 * <= 1` collapses the `"/"`, `"\\"` and `""` branches into one compare.
 * Without the `"\\"` case, `cdUp("\\")` (reached from a UNC root or a DOS
 * device path like `\\?\…`) would return itself via `slice(0, i || 1)`
 * and trap `loadDescriptionFile` in an infinite loop. Once single-char
 * roots are filtered up front, the reverse scan always produces a
 * strictly shorter string.
 * @param {string} directory directory
 * @returns {string | null} parent directory or null
 */
function cdUp(directory) {
	if (directory.length <= 1) return null;
	for (let i = directory.length - 1; i >= 0; i--) {
		const code = directory.charCodeAt(i);
		if (code === CHAR_SLASH || code === CHAR_BACKSLASH) {
			return directory.slice(0, i || 1);
		}
	}
	return null;
}

/**
 * @param {Resolver} resolver resolver
 * @param {string} directory directory
 * @param {string[]} filenames filenames
 * @param {DescriptionFileInfo | undefined} oldInfo oldInfo
 * @param {ResolveContext} resolveContext resolveContext
 * @param {ErrorFirstCallback} callback callback
 */
function loadDescriptionFile(
	resolver,
	directory,
	filenames,
	oldInfo,
	resolveContext,
	callback,
) {
	// Hoist the per-filename iterator and the per-level done callback out
	// of `findDescriptionFile`. They both close over `directory`, which we
	// reassign as we walk up the tree, so the same closures keep working
	// across every level — the previous implementation re-allocated both
	// arrows on every recursion step, which adds up on deep walks (multiple
	// `DescriptionFilePlugin` taps per resolve, each climbing several
	// directories looking for `package.json`).
	/**
	 * @param {string} filename filename
	 * @param {(err?: null | Error, result?: null | Result) => void} iterCallback callback
	 * @returns {void}
	 */
	const iterFilename = (filename, iterCallback) => {
		const descriptionFilePath = resolver.join(directory, filename);

		/**
		 * @param {(null | Error)=} err error
		 * @param {JsonObject=} resolvedContent content
		 * @returns {void}
		 */
		function onJson(err, resolvedContent) {
			if (err) {
				if (resolveContext.log) {
					resolveContext.log(
						`${descriptionFilePath} (directory description file): ${err}`,
					);
				} else {
					err.message = `${descriptionFilePath} (directory description file): ${err}`;
				}
				return iterCallback(err);
			}
			iterCallback(null, {
				content: /** @type {JsonObject} */ (resolvedContent),
				directory,
				path: descriptionFilePath,
			});
		}

		if (resolver.fileSystem.readJson) {
			resolver.fileSystem.readJson(descriptionFilePath, (err, content) => {
				if (err) {
					if (
						typeof (/** @type {NodeJS.ErrnoException} */ (err).code) !==
						"undefined"
					) {
						if (resolveContext.missingDependencies) {
							resolveContext.missingDependencies.add(descriptionFilePath);
						}
						return iterCallback();
					}
					if (resolveContext.fileDependencies) {
						resolveContext.fileDependencies.add(descriptionFilePath);
					}
					return onJson(err);
				}
				if (resolveContext.fileDependencies) {
					resolveContext.fileDependencies.add(descriptionFilePath);
				}
				onJson(null, content);
			});
		} else {
			resolver.fileSystem.readFile(descriptionFilePath, (err, content) => {
				if (err) {
					if (resolveContext.missingDependencies) {
						resolveContext.missingDependencies.add(descriptionFilePath);
					}
					return iterCallback();
				}
				if (resolveContext.fileDependencies) {
					resolveContext.fileDependencies.add(descriptionFilePath);
				}

				/** @type {JsonObject | undefined} */
				let json;

				if (content) {
					try {
						json = JSON.parse(decodeText(content));
					} catch (/** @type {unknown} */ err_) {
						return onJson(/** @type {Error} */ (err_));
					}
				} else {
					return onJson(new Error("No content in file"));
				}

				onJson(null, json);
			});
		}
	};
	// Forward-declared so the helpers below can reference each other
	// without falling foul of `no-use-before-define`.
	/** @type {() => void} */
	let findDescriptionFile;
	/**
	 * @param {(null | Error)=} err error
	 * @param {(null | Result)=} result result
	 * @returns {void}
	 */
	const onLevelDone = (err, result) => {
		if (err) return callback(err);
		if (result) return callback(null, result);
		const dir = cdUp(directory);
		if (!dir) {
			return callback();
		}
		directory = dir;
		return findDescriptionFile();
	};
	findDescriptionFile = () => {
		if (oldInfo && oldInfo.directory === directory) {
			// We already have info for this directory and can reuse it
			return callback(null, oldInfo);
		}
		forEachBail(filenames, iterFilename, onLevelDone);
	};
	findDescriptionFile();
}

/**
 * @param {JsonObject} content content
 * @param {string | string[]} field field
 * @returns {JsonValue | undefined} field data
 */
function getField(content, field) {
	if (!content) return undefined;
	if (Array.isArray(field)) {
		/** @type {JsonValue} */
		let current = content;
		for (let j = 0; j < field.length; j++) {
			if (current === null || typeof current !== "object") {
				current = null;
				break;
			}
			current = /** @type {JsonValue} */ (
				/** @type {JsonObject} */
				(current)[field[j]]
			);
		}
		return current;
	}
	return content[field];
}

module.exports.cdUp = cdUp;
module.exports.getField = getField;
module.exports.loadDescriptionFile = loadDescriptionFile;
