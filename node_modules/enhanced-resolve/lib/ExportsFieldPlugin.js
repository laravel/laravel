/*
	MIT License http://www.opensource.org/licenses/mit-license.php
	Author Ivan Kopeykin @vankop
*/

"use strict";

const DescriptionFileUtils = require("./DescriptionFileUtils");
const forEachBail = require("./forEachBail");
const { processExportsField } = require("./util/entrypoints");
const { parseIdentifier } = require("./util/identifier");
const {
	deprecatedInvalidSegmentRegEx,
	invalidSegmentRegEx,
} = require("./util/path");

/** @typedef {import("./Resolver")} Resolver */
/** @typedef {import("./Resolver").JsonObject} JsonObject */
/** @typedef {import("./Resolver").ResolveRequest} ResolveRequest */
/** @typedef {import("./Resolver").ResolveStepHook} ResolveStepHook */
/** @typedef {import("./util/entrypoints").ExportsField} ExportsField */
/** @typedef {import("./util/entrypoints").FieldProcessor} FieldProcessor */

module.exports = class ExportsFieldPlugin {
	/**
	 * @param {string | ResolveStepHook} source source
	 * @param {Set<string>} conditionNames condition names
	 * @param {string | string[]} fieldNamePath name path
	 * @param {string | ResolveStepHook} target target
	 * @param {boolean=} restrictions whether `restrictions` are configured (enables exports-target fallback when a target is filtered out)
	 */
	constructor(source, conditionNames, fieldNamePath, target, restrictions) {
		this.source = source;
		this.target = target;
		this.conditionNames = conditionNames;
		this.fieldName = fieldNamePath;
		this.restrictions = Boolean(restrictions);
		// `null` is cached for description files that have no exports field,
		// so subsequent resolves against the same package.json skip the
		// `DescriptionFileUtils.getField` walk entirely.
		/** @type {WeakMap<JsonObject, FieldProcessor | null>} */
		this._fieldProcessorCache = new WeakMap();
	}

	/**
	 * @param {Resolver} resolver the resolver
	 * @returns {void}
	 */
	apply(resolver) {
		const target = resolver.ensureHook(this.target);
		resolver
			.getHook(this.source)
			.tapAsync("ExportsFieldPlugin", (request, resolveContext, callback) => {
				// When there is no description file, abort
				if (!request.descriptionFileData) return callback();
				if (
					// When the description file is inherited from parent, abort
					// (There is no description file inside of this package)
					request.relativePath !== "." ||
					request.request === undefined
				) {
					return callback();
				}

				const { descriptionFileData } = request;
				const remainingRequest =
					request.query || request.fragment
						? (request.request === "." ? "./" : request.request) +
							request.query +
							request.fragment
						: request.request;

				/** @type {string[]} */
				let paths;
				/** @type {string | null} */
				let usedField;

				try {
					// Look up the cached processor first. On a cache hit we
					// avoid re-walking the description file for the exports
					// field — and `null` is cached for description files that
					// have no exports field at all, so those skip the read
					// entirely. `processExportsField` can throw on a malformed
					// `exports` map (e.g. a key without a leading `.`), so
					// building the processor must stay inside this try/catch.
					let fieldProcessor =
						this._fieldProcessorCache.get(descriptionFileData);
					if (
						fieldProcessor === undefined &&
						!this._fieldProcessorCache.has(descriptionFileData)
					) {
						const exportsField =
							/** @type {ExportsField | null | undefined} */
							(
								DescriptionFileUtils.getField(
									descriptionFileData,
									this.fieldName,
								)
							);
						fieldProcessor = exportsField
							? processExportsField(exportsField)
							: null;
						this._fieldProcessorCache.set(descriptionFileData, fieldProcessor);
					}
					if (!fieldProcessor) return callback();

					if (request.directory) {
						return callback(
							new Error(
								`Resolving to directories is not possible with the exports field (request was ${remainingRequest}/)`,
							),
						);
					}

					[paths, usedField] = fieldProcessor(
						remainingRequest,
						this.conditionNames,
					);
				} catch (/** @type {unknown} */ err) {
					if (resolveContext.log) {
						resolveContext.log(
							`Exports field in ${request.descriptionFilePath} can't be processed: ${err}`,
						);
					}
					return callback(/** @type {Error} */ (err));
				}

				if (paths.length === 0) {
					const conditions = [...this.conditionNames];
					const conditionsStr =
						conditions.length === 1
							? `the condition "${conditions[0]}"`
							: `the conditions ${JSON.stringify(conditions)}`;
					return callback(
						new Error(
							`"${remainingRequest}" is not exported under ${conditionsStr} from package ${request.descriptionFileRoot} (see exports field in ${request.descriptionFilePath})`,
						),
					);
				}

				// When `restrictions` are configured, share a marker down the
				// chain so RestrictionsPlugin can tell us it filtered out an
				// otherwise-valid target — then we fall back instead of erroring.
				const restrictionsMarker = this.restrictions
					? { blocked: false }
					: undefined;

				forEachBail(
					paths,
					/**
					 * @param {string} path path
					 * @param {(err?: null | Error, result?: null | ResolveRequest) => void} callback callback
					 * @param {number} i index
					 * @returns {void}
					 */
					(path, callback, i) => {
						const parsedIdentifier = parseIdentifier(path);

						if (!parsedIdentifier) return callback();

						const [relativePath, query, fragment] = parsedIdentifier;

						if (!relativePath.startsWith("./")) {
							if (paths.length === i) {
								return callback(
									new Error(
										`Invalid "exports" target "${path}" defined for "${usedField}" in the package config ${request.descriptionFilePath}, targets must start with "./"`,
									),
								);
							}

							return callback();
						}

						const withoutDotSlash = relativePath.slice(2);
						if (
							invalidSegmentRegEx.test(withoutDotSlash) &&
							deprecatedInvalidSegmentRegEx.test(withoutDotSlash)
						) {
							if (paths.length === i) {
								return callback(
									new Error(
										`Invalid "exports" target "${path}" defined for "${usedField}" in the package config ${request.descriptionFilePath}, targets must start with "./"`,
									),
								);
							}

							return callback();
						}

						/** @type {ResolveRequest} */
						const obj = {
							...request,
							request: undefined,
							path: resolver.join(
								/** @type {string} */ (request.descriptionFileRoot),
								relativePath,
							),
							relativePath,
							query,
							fragment,
						};
						// Attach the marker only when restrictions are configured, so
						// resolves without restrictions keep their request shape and
						// never leak the property onto the result.
						if (restrictionsMarker) {
							obj.__restrictionsMarker = restrictionsMarker;
						}

						resolver.doResolve(
							target,
							obj,
							`using exports field: ${path}`,
							resolveContext,
							(err, result) => {
								if (err) return callback(err);
								// Don't allow to continue - https://github.com/webpack/enhanced-resolve/issues/400
								if (result === undefined) return callback(null, null);
								callback(null, result);
							},
						);
					},
					/**
					 * @param {(null | Error)=} err error
					 * @param {(null | ResolveRequest)=} result result
					 * @returns {void}
					 */
					(err, result) => {
						if (err) return callback(err);
						// When an exports field match was found but the target file doesn't exist,
						// return an error to prevent fallback to parent node_modules directories.
						// Per the Node.js ESM spec, a matched exports entry that fails to resolve
						// is a hard error, not a signal to continue searching up the directory tree.
						// See: https://github.com/webpack/enhanced-resolve/issues/399
						if (!result) {
							// Exception: the target existed but `restrictions` filtered it
							// out — return no result so the next `modules` entry is tried.
							if (restrictionsMarker && restrictionsMarker.blocked) {
								return callback(null, null);
							}
							return callback(
								new Error(
									`Package path ${remainingRequest} is exported from package ${request.descriptionFileRoot}, but no valid target file was found (see exports field in ${request.descriptionFilePath})`,
								),
							);
						}
						// Drop the internal marker before it reaches the result.
						if (restrictionsMarker) delete result.__restrictionsMarker;
						callback(null, result);
					},
				);
			});
	}
};
