/*
	MIT License http://www.opensource.org/licenses/mit-license.php
	Author Ivan Kopeykin @vankop
*/

"use strict";

const DescriptionFileUtils = require("./DescriptionFileUtils");
const forEachBail = require("./forEachBail");
const { processImportsField } = require("./util/entrypoints");
const { parseIdentifier } = require("./util/identifier");
const { invalidSegmentRegEx } = require("./util/path");

/** @typedef {import("./Resolver")} Resolver */
/** @typedef {import("./Resolver").JsonObject} JsonObject */
/** @typedef {import("./Resolver").ResolveRequest} ResolveRequest */
/** @typedef {import("./Resolver").ResolveStepHook} ResolveStepHook */
/** @typedef {import("./util/entrypoints").FieldProcessor} FieldProcessor */
/** @typedef {import("./util/entrypoints").ImportsField} ImportsField */

const dotCode = ".".charCodeAt(0);

module.exports = class ImportsFieldPlugin {
	/**
	 * @param {string | ResolveStepHook} source source
	 * @param {Set<string>} conditionNames condition names
	 * @param {string | string[]} fieldNamePath name path
	 * @param {string | ResolveStepHook} targetFile target file
	 * @param {string | ResolveStepHook} targetPackage target package
	 */
	constructor(
		source,
		conditionNames,
		fieldNamePath,
		targetFile,
		targetPackage,
	) {
		this.source = source;
		this.targetFile = targetFile;
		this.targetPackage = targetPackage;
		this.conditionNames = conditionNames;
		this.fieldName = fieldNamePath;
		// `null` is cached for description files that have no imports field,
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
		const targetFile = resolver.ensureHook(this.targetFile);
		const targetPackage = resolver.ensureHook(this.targetPackage);

		resolver
			.getHook(this.source)
			.tapAsync("ImportsFieldPlugin", (request, resolveContext, callback) => {
				// When there is no description file, abort
				if (!request.descriptionFileData || request.request === undefined) {
					return callback();
				}

				const { descriptionFileData } = request;
				// Skip the concat when there's nothing to append — the common
				// case has empty query/fragment, so this avoids an allocation
				// per resolve. Mirrors the pattern in ExportsFieldPlugin.
				const remainingRequest =
					request.query || request.fragment
						? request.request + request.query + request.fragment
						: request.request;

				/** @type {string[]} */
				let paths;
				/** @type {string | null} */
				let usedField;

				try {
					// Look up the cached processor first. On a cache hit we
					// avoid re-walking the description file for the imports
					// field — and `null` is cached for description files that
					// have no imports field at all, so those skip the read
					// entirely. `processImportsField` can throw on a
					// malformed `imports` map, so building the processor must
					// stay inside this try/catch.
					let fieldProcessor =
						this._fieldProcessorCache.get(descriptionFileData);
					if (
						fieldProcessor === undefined &&
						!this._fieldProcessorCache.has(descriptionFileData)
					) {
						const importsField =
							/** @type {ImportsField | null | undefined} */
							(
								DescriptionFileUtils.getField(
									descriptionFileData,
									this.fieldName,
								)
							);
						fieldProcessor = importsField
							? processImportsField(importsField)
							: null;
						this._fieldProcessorCache.set(descriptionFileData, fieldProcessor);
					}
					if (!fieldProcessor) return callback();

					if (request.directory) {
						return callback(
							new Error(
								`Resolving to directories is not possible with the imports field (request was ${remainingRequest}/)`,
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
							`Imports field in ${request.descriptionFilePath} can't be processed: ${err}`,
						);
					}
					return callback(/** @type {Error} */ (err));
				}

				if (paths.length === 0) {
					return callback(
						new Error(
							`Package import ${remainingRequest} is not imported from package ${request.descriptionFileRoot} (see imports field in ${request.descriptionFilePath})`,
						),
					);
				}

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

						const [path_, query, fragment] = parsedIdentifier;

						switch (path_.charCodeAt(0)) {
							// should be relative
							case dotCode: {
								const withoutDotSlash = path_.slice(2);
								if (invalidSegmentRegEx.test(withoutDotSlash)) {
									if (paths.length === i) {
										return callback(
											new Error(
												`Invalid "imports" target "${path}" defined for "${usedField}" in the package config ${request.descriptionFilePath}, targets must start with "./"`,
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
										path_,
									),
									relativePath: path_,
									query,
									fragment,
								};

								resolver.doResolve(
									targetFile,
									obj,
									`using imports field: ${path}`,
									resolveContext,
									(err, result) => {
										if (err) return callback(err);
										// Don't allow to continue - https://github.com/webpack/enhanced-resolve/issues/400
										if (result === undefined) return callback(null, null);
										callback(null, result);
									},
								);
								break;
							}

							// package resolving
							default: {
								/** @type {ResolveRequest} */
								const obj = {
									...request,
									request: path_,
									relativePath: path_,
									fullySpecified: true,
									query,
									fragment,
								};

								resolver.doResolve(
									targetPackage,
									obj,
									`using imports field: ${path}`,
									resolveContext,
									(err, result) => {
										if (err) return callback(err);
										// Don't allow to continue - https://github.com/webpack/enhanced-resolve/issues/400
										if (result === undefined) return callback(null, null);
										callback(null, result);
									},
								);
							}
						}
					},
					/**
					 * @param {null | Error=} err error
					 * @param {null | ResolveRequest=} result result
					 * @returns {void}
					 */
					(err, result) => callback(err, result || null),
				);
			});
	}
};
