/*
	MIT License http://www.opensource.org/licenses/mit-license.php
	Author Tobias Koppers @sokra
*/

"use strict";

const DescriptionFileUtils = require("./DescriptionFileUtils");

/** @typedef {import("./Resolver")} Resolver */
/** @typedef {import("./Resolver").JsonObject} JsonObject */
/** @typedef {import("./Resolver").ResolveRequest} ResolveRequest */
/** @typedef {import("./Resolver").ResolveStepHook} ResolveStepHook */

/** @typedef {{ name: string | string[], forceRelative: boolean }} MainFieldOptions */

const alreadyTriedMainField = Symbol("alreadyTriedMainField");

// Sentinel cached for description files where the main field resolves to a
// value we cannot use (missing, non-string, ".", "./"). Cheaper to store and
// check than to re-walk the description file on every resolve.
const NO_MAIN = Symbol("NoMain");

module.exports = class MainFieldPlugin {
	/**
	 * @param {string | ResolveStepHook} source source
	 * @param {MainFieldOptions} options options
	 * @param {string | ResolveStepHook} target target
	 */
	constructor(source, options, target) {
		this.source = source;
		this.options = options;
		this.target = target;
		// Cache the resolved `mainModule` per description-file content. The
		// options (`name`, `forceRelative`) are fixed for this plugin
		// instance, so caching against content alone is safe. Stores either
		// the ready-to-use request string or the `NO_MAIN` sentinel.
		/** @type {WeakMap<JsonObject, string | typeof NO_MAIN>} */
		this._mainModuleCache = new WeakMap();
	}

	/**
	 * @param {Resolver} resolver the resolver
	 * @returns {void}
	 */
	apply(resolver) {
		const target = resolver.ensureHook(this.target);
		resolver
			.getHook(this.source)
			.tapAsync("MainFieldPlugin", (request, resolveContext, callback) => {
				if (
					request.path !== request.descriptionFileRoot ||
					/** @type {ResolveRequest & { [alreadyTriedMainField]?: string }} */
					(request)[alreadyTriedMainField] === request.descriptionFilePath ||
					!request.descriptionFilePath
				) {
					return callback();
				}
				const descFileData = /** @type {JsonObject} */ (
					request.descriptionFileData
				);
				let mainModule = this._mainModuleCache.get(descFileData);
				if (mainModule === undefined) {
					let raw =
						/** @type {string | null | undefined} */
						(DescriptionFileUtils.getField(descFileData, this.options.name));
					if (!raw || typeof raw !== "string" || raw === "." || raw === "./") {
						this._mainModuleCache.set(descFileData, NO_MAIN);
						return callback();
					}
					if (this.options.forceRelative && !/^\.\.?\//.test(raw)) {
						raw = `./${raw}`;
					}
					mainModule = raw;
					this._mainModuleCache.set(descFileData, mainModule);
				} else if (mainModule === NO_MAIN) {
					return callback();
				}
				const filename = resolver.basename(request.descriptionFilePath);
				/** @type {ResolveRequest & { [alreadyTriedMainField]?: string }} */
				const obj = {
					...request,
					request: mainModule,
					module: false,
					directory: mainModule.endsWith("/"),
					[alreadyTriedMainField]: request.descriptionFilePath,
				};
				return resolver.doResolve(
					target,
					obj,
					`use ${mainModule} from ${this.options.name} in ${filename}`,
					resolveContext,
					callback,
				);
			});
	}
};
