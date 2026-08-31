/*
	MIT License http://www.opensource.org/licenses/mit-license.php
	Author Tobias Koppers @sokra
*/

"use strict";

const DescriptionFileUtils = require("./DescriptionFileUtils");
const getInnerRequest = require("./getInnerRequest");

/** @typedef {import("./Resolver")} Resolver */
/** @typedef {import("./Resolver").JsonPrimitive} JsonPrimitive */
/** @typedef {import("./Resolver").ResolveRequest} ResolveRequest */
/** @typedef {import("./Resolver").ResolveStepHook} ResolveStepHook */

// Sentinel stored in `_fieldDataCache` when a description file does not
// contain a usable alias field object. Lets us distinguish "not cached yet"
// from "no valid field" without calling back into `getField`.
const NO_FIELD_OBJECT = Symbol("NoFieldObject");

module.exports = class AliasFieldPlugin {
	/**
	 * @param {string | ResolveStepHook} source source
	 * @param {string | string[]} field field
	 * @param {string | ResolveStepHook} target target
	 */
	constructor(source, field, target) {
		this.source = source;
		this.field = field;
		this.target = target;
		// `this.field` is fixed for the plugin's lifetime, so caching
		// per description-file content is safe. The cached value is either
		// the resolved alias-map object or the `NO_FIELD_OBJECT` sentinel
		// meaning "description file has no usable alias field".
		/** @type {WeakMap<import("./Resolver").JsonObject, { [k: string]: JsonPrimitive } | typeof NO_FIELD_OBJECT>} */
		this._fieldDataCache = new WeakMap();
	}

	/**
	 * @param {Resolver} resolver the resolver
	 * @returns {void}
	 */
	apply(resolver) {
		const target = resolver.ensureHook(this.target);
		resolver
			.getHook(this.source)
			.tapAsync("AliasFieldPlugin", (request, resolveContext, callback) => {
				if (!request.descriptionFileData) return callback();
				const innerRequest = getInnerRequest(resolver, request);
				if (!innerRequest) return callback();
				const { descriptionFileData } = request;
				let fieldData = this._fieldDataCache.get(descriptionFileData);
				if (fieldData === undefined) {
					const raw = DescriptionFileUtils.getField(
						descriptionFileData,
						this.field,
					);
					fieldData =
						raw === null || typeof raw !== "object"
							? NO_FIELD_OBJECT
							: /** @type {{ [k: string]: JsonPrimitive }} */ (raw);
					this._fieldDataCache.set(descriptionFileData, fieldData);
				}
				if (fieldData === NO_FIELD_OBJECT) {
					if (resolveContext.log) {
						resolveContext.log(
							`Field '${this.field}' doesn't contain a valid alias configuration`,
						);
					}
					return callback();
				}
				/** @type {JsonPrimitive | undefined} */
				const data = Object.prototype.hasOwnProperty.call(
					fieldData,
					innerRequest,
				)
					? /** @type {{ [Key in string]: JsonPrimitive }} */ (fieldData)[
							innerRequest
						]
					: innerRequest.startsWith("./")
						? /** @type {{ [Key in string]: JsonPrimitive }} */ (fieldData)[
								innerRequest.slice(2)
							]
						: undefined;
				if (data === innerRequest) return callback();
				if (data === undefined) return callback();
				if (data === false) {
					/** @type {ResolveRequest} */
					const ignoreObj = {
						...request,
						path: false,
					};
					if (typeof resolveContext.yield === "function") {
						resolveContext.yield(ignoreObj);
						return callback(null, null);
					}
					return callback(null, ignoreObj);
				}
				/** @type {ResolveRequest} */
				const obj = {
					...request,
					path: /** @type {string} */ (request.descriptionFileRoot),
					request: /** @type {string} */ (data),
					fullySpecified: false,
				};
				resolver.doResolve(
					target,
					obj,
					`aliased from description file ${
						request.descriptionFilePath
					} with mapping '${innerRequest}' to '${/** @type {string} */ data}'`,
					resolveContext,
					(err, result) => {
						if (err) return callback(err);

						// Don't allow other aliasing or raw request
						if (result === undefined) return callback(null, null);
						callback(null, result);
					},
				);
			});
	}
};
