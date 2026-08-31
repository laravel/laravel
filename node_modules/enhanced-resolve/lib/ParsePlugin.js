/*
	MIT License http://www.opensource.org/licenses/mit-license.php
	Author Tobias Koppers @sokra
*/

"use strict";

/** @typedef {import("./Resolver")} Resolver */
/** @typedef {import("./Resolver").ResolveRequest} ResolveRequest */
/** @typedef {import("./Resolver").ResolveStepHook} ResolveStepHook */

module.exports = class ParsePlugin {
	/**
	 * @param {string | ResolveStepHook} source source
	 * @param {Partial<ResolveRequest>} requestOptions request options
	 * @param {string | ResolveStepHook} target target
	 */
	constructor(source, requestOptions, target) {
		this.source = source;
		this.requestOptions = requestOptions;
		this.target = target;
	}

	/**
	 * @param {Resolver} resolver the resolver
	 * @returns {void}
	 */
	apply(resolver) {
		const target = resolver.ensureHook(this.target);
		const { requestOptions } = this;
		resolver
			.getHook(this.source)
			.tapAsync("ParsePlugin", (request, resolveContext, callback) => {
				const parsed = resolver.parse(/** @type {string} */ (request.request));
				/** @type {ResolveRequest} */
				// Single spread + direct field assignment instead of
				// `{ ...request, ...parsed, ...requestOptions }` — avoids
				// two extra property enumerations on every resolve.
				// Keep in sync with Resolver.parse() output shape.
				const obj = { ...request };
				obj.request = parsed.request;
				obj.query = parsed.query || request.query || "";
				obj.fragment = parsed.fragment || request.fragment || "";
				obj.module = parsed.module;
				obj.directory = parsed.directory;
				obj.file = parsed.file;
				obj.internal = parsed.internal;
				Object.assign(obj, requestOptions);
				if (parsed && resolveContext.log) {
					if (parsed.module) resolveContext.log("Parsed request is a module");
					if (parsed.directory) {
						resolveContext.log("Parsed request is a directory");
					}
				}
				// There is an edge-case where a request with # can be a path or a fragment -> try both
				if (obj.request && !obj.query && obj.fragment) {
					const directory = obj.fragment.endsWith("/");
					/** @type {ResolveRequest} */
					const alternative = {
						...obj,
						directory,
						request:
							obj.request +
							(obj.directory ? "/" : "") +
							(directory ? obj.fragment.slice(0, -1) : obj.fragment),
						fragment: "",
					};
					resolver.doResolve(
						target,
						alternative,
						null,
						resolveContext,
						(err, result) => {
							if (err) return callback(err);
							if (result) return callback(null, result);
							resolver.doResolve(target, obj, null, resolveContext, callback);
						},
					);
					return;
				}
				resolver.doResolve(target, obj, null, resolveContext, callback);
			});
	}
};
