/*
	MIT License http://www.opensource.org/licenses/mit-license.php
	Author Ivan Kopeykin @vankop
*/

"use strict";

const forEachBail = require("./forEachBail");

/** @typedef {import("./Resolver")} Resolver */
/** @typedef {import("./Resolver").ResolveRequest} ResolveRequest */
/** @typedef {import("./Resolver").ResolveStepHook} ResolveStepHook */
/** @typedef {{ alias: string | string[], extension: string }} ExtensionAliasOption */

module.exports = class ExtensionAliasPlugin {
	/**
	 * @param {string | ResolveStepHook} source source
	 * @param {ExtensionAliasOption} options options
	 * @param {string | ResolveStepHook} target target
	 */
	constructor(source, options, target) {
		this.source = source;
		this.options = options;
		this.target = target;
	}

	/**
	 * @param {Resolver} resolver the resolver
	 * @returns {void}
	 */
	apply(resolver) {
		const target = resolver.ensureHook(this.target);
		const { extension, alias } = this.options;
		resolver
			.getHook(this.source)
			.tapAsync("ExtensionAliasPlugin", (request, resolveContext, callback) => {
				// Two modes of operation:
				// - "request" mode: original request specifier still carries the
				//   extension (e.g. user wrote `./foo.js`). We swap the extension
				//   on `request.request` and re-resolve.
				// - "path" mode: the specifier has already been joined into an
				//   absolute `request.path` (e.g. produced by the imports field).
				//   We swap the extension on `request.path` and `request.relativePath`.
				const useRequest = request.request !== undefined;
				const source = useRequest
					? /** @type {string} */ (request.request)
					: request.path;
				if (!source || !source.endsWith(extension)) return callback();
				const isAliasString = typeof alias === "string";
				// Hoist the base (everything before the old extension) out of the
				// per-alias `resolve` callback. For an array `alias`, the callback
				// runs once per candidate extension; the base does not change
				// between iterations, so there's no reason to recompute it.
				const sourceBase = source.slice(0, -extension.length);
				const relativePathBase =
					!useRequest &&
					request.relativePath &&
					request.relativePath.endsWith(extension)
						? request.relativePath.slice(0, -extension.length)
						: null;
				/**
				 * @param {string} alias extension alias
				 * @param {(err?: null | Error, result?: null | ResolveRequest) => void} callback callback
				 * @param {number=} index index
				 * @returns {void}
				 */
				const resolve = (alias, callback, index) => {
					const newValue = `${sourceBase}${alias}`;
					const nextRequest = useRequest
						? {
								...request,
								request: newValue,
								fullySpecified: true,
							}
						: {
								...request,
								path: newValue,
								relativePath:
									relativePathBase !== null
										? `${relativePathBase}${alias}`
										: request.relativePath,
								fullySpecified: true,
							};

					return resolver.doResolve(
						target,
						nextRequest,
						`aliased from extension alias with mapping '${extension}' to '${alias}'`,
						resolveContext,
						(err, result) => {
							// Throw error if we are on the last alias (for multiple aliases) and it failed, always throw if we are not an array or we have only one alias
							if (!isAliasString && index) {
								if (index !== this.options.alias.length) {
									if (resolveContext.log) {
										resolveContext.log(
											`Failed to alias from extension alias with mapping '${extension}' to '${alias}' for '${newValue}': ${err}`,
										);
									}

									return callback(null, result);
								}

								return callback(err, result);
							}
							callback(err, result);
						},
					);
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
					return callback(null, null);
				};
				if (isAliasString) {
					resolve(alias, stoppingCallback);
				} else if (alias.length > 1) {
					forEachBail(alias, resolve, stoppingCallback);
				} else {
					resolve(alias[0], stoppingCallback);
				}
			});
	}
};
