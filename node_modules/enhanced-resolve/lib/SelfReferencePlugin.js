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

const slashCode = "/".charCodeAt(0);

// Sentinel stored in `_nameCache` when the description file either has no
// exports field (so self-reference can't apply) or no string `name`.
const NO_SELF_REF = Symbol("NoSelfRef");

module.exports = class SelfReferencePlugin {
	/**
	 * @param {string | ResolveStepHook} source source
	 * @param {string | string[]} fieldNamePath name path
	 * @param {string | ResolveStepHook} target target
	 */
	constructor(source, fieldNamePath, target) {
		this.source = source;
		this.target = target;
		this.fieldName = fieldNamePath;
		// Self-reference needs both an exports field and a `"name"` string.
		// Both are stable per description-file content, so cache the decision
		// in one WeakMap: the resolved name when self-reference is possible,
		// or `NO_SELF_REF` when it isn't. This skips the two per-resolve
		// `DescriptionFileUtils.getField` walks for hot packages.
		/** @type {WeakMap<JsonObject, string | typeof NO_SELF_REF>} */
		this._nameCache = new WeakMap();
	}

	/**
	 * @param {Resolver} resolver the resolver
	 * @returns {void}
	 */
	apply(resolver) {
		const target = resolver.ensureHook(this.target);
		resolver
			.getHook(this.source)
			.tapAsync("SelfReferencePlugin", (request, resolveContext, callback) => {
				if (!request.descriptionFileData) return callback();

				const req = request.request;
				if (!req) return callback();

				const { descriptionFileData } = request;
				let name = this._nameCache.get(descriptionFileData);
				if (name === undefined) {
					// Feature is only enabled when an exports field is present
					const exportsField = DescriptionFileUtils.getField(
						descriptionFileData,
						this.fieldName,
					);
					if (!exportsField) {
						this._nameCache.set(descriptionFileData, NO_SELF_REF);
						return callback();
					}
					const rawName = DescriptionFileUtils.getField(
						descriptionFileData,
						"name",
					);
					if (typeof rawName !== "string") {
						this._nameCache.set(descriptionFileData, NO_SELF_REF);
						return callback();
					}
					name = rawName;
					this._nameCache.set(descriptionFileData, name);
				} else if (name === NO_SELF_REF) {
					return callback();
				}

				if (
					req.startsWith(name) &&
					(req.length === name.length ||
						req.charCodeAt(name.length) === slashCode)
				) {
					const remainingRequest = `.${req.slice(name.length)}`;
					/** @type {ResolveRequest} */
					const obj = {
						...request,
						request: remainingRequest,
						path: /** @type {string} */ (request.descriptionFileRoot),
						relativePath: ".",
					};

					resolver.doResolve(
						target,
						obj,
						"self reference",
						resolveContext,
						callback,
					);
				} else {
					return callback();
				}
			});
	}
};
