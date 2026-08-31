/*
	MIT License http://www.opensource.org/licenses/mit-license.php
	Author Tobias Koppers @sokra
*/

"use strict";

const { isRelativeRequest } = require("./util/path");

/** @typedef {import("./Resolver")} Resolver */
/** @typedef {import("./Resolver").ResolveRequest} ResolveRequest */
/** @typedef {import("./Resolver").ResolveStepHook} ResolveStepHook */
/** @typedef {import("./Resolver").ResolveContextYield} ResolveContextYield */
/** @typedef {{ [k: string]: undefined | ResolveRequest | ResolveRequest[] }} Cache */

/**
 * @param {string} relativePath relative path from package root
 * @param {string} request relative request
 * @param {Resolver} resolver resolver instance
 * @returns {string} normalized request with a preserved leading dot
 */
function joinRelativePreservingLeadingDot(relativePath, request, resolver) {
	const normalized = resolver.join(relativePath, request);
	return isRelativeRequest(normalized) ? normalized : `./${normalized}`;
}

/**
 * @param {ResolveRequest} request request
 * @returns {string | false | undefined} normalized path
 */
function getCachePath(request) {
	if (request.descriptionFileRoot && !request.module) {
		return request.descriptionFileRoot;
	}
	return request.path;
}

/**
 * @param {ResolveRequest} request request
 * @param {Resolver} resolver resolver instance
 * @returns {string | undefined} normalized request string
 */
function getCacheRequest(request, resolver) {
	const requestString = request.request;
	if (
		!requestString ||
		!request.relativePath ||
		!isRelativeRequest(requestString)
	) {
		return requestString;
	}
	return joinRelativePreservingLeadingDot(
		request.relativePath,
		requestString,
		resolver,
	);
}

// Cache-key separator: `\0` is safe because paths, requests, queries and
// fragments produced by `parseIdentifier` never contain a raw NUL (the
// \0-escape in identifier.js is decoded back to the original char), and the
// context, when included, is passed through `JSON.stringify`, which escapes
// any NUL to \u0000.
// const SEP = "\0";

/**
 * Build the cache id for a request. Called on every `described-resolve`
 * invocation when `unsafeCache` is on, so it's a hot path.
 *
 * Equivalent in meaning to the previous `JSON.stringify({ ... })` form, but
 * ~3–5× faster since we avoid the object allocation and JSON serializer for
 * the fields that are already plain strings.
 * @param {string} type type of cache
 * @param {ResolveRequest} request request
 * @param {boolean} withContext cache with context?
 * @param {Resolver} resolver resolver instance
 * @returns {string} cache id
 */
function getCacheId(type, request, withContext, resolver) {
	// TODO use it in the next major release, it is faster
	// const contextPart = withContext ? JSON.stringify(request.context) : "";
	// const path = getCachePath(request);
	// const cacheRequest = getCacheRequest(request, resolver);
	// return (
	// 	type +
	// 	SEP +
	// 	contextPart +
	// 	SEP +
	// 	(path || "") +
	// 	SEP +
	// 	(request.query || "") +
	// 	SEP +
	// 	(request.fragment || "") +
	// 	SEP +
	// 	(cacheRequest || "")
	// );

	return JSON.stringify({
		type,
		context: withContext ? request.context : "",
		path: getCachePath(request),
		query: request.query,
		fragment: request.fragment,
		request: getCacheRequest(request, resolver),
	});
}

module.exports = class UnsafeCachePlugin {
	/**
	 * @param {string | ResolveStepHook} source source
	 * @param {(request: ResolveRequest) => boolean} filterPredicate filterPredicate
	 * @param {Cache} cache cache
	 * @param {boolean} withContext withContext
	 * @param {string | ResolveStepHook} target target
	 */
	constructor(source, filterPredicate, cache, withContext, target) {
		this.source = source;
		this.filterPredicate = filterPredicate;
		this.withContext = withContext;
		this.cache = cache;
		this.target = target;
	}

	/**
	 * @param {Resolver} resolver the resolver
	 * @returns {void}
	 */
	apply(resolver) {
		const target = resolver.ensureHook(this.target);
		resolver
			.getHook(this.source)
			.tapAsync("UnsafeCachePlugin", (request, resolveContext, callback) => {
				if (!this.filterPredicate(request)) {
					return resolver.doResolve(
						target,
						request,
						null,
						resolveContext,
						callback,
					);
				}
				const isYield = typeof resolveContext.yield === "function";
				const cacheId = getCacheId(
					isYield ? "yield" : "default",
					request,
					this.withContext,
					resolver,
				);
				const cacheEntry = this.cache[cacheId];
				if (cacheEntry) {
					if (isYield) {
						const yield_ =
							/** @type {ResolveContextYield} */
							(resolveContext.yield);
						if (Array.isArray(cacheEntry)) {
							for (const result of cacheEntry) yield_(result);
						} else {
							yield_(cacheEntry);
						}
						return callback(null, null);
					}
					return callback(null, /** @type {ResolveRequest} */ (cacheEntry));
				}

				/** @type {ResolveContextYield | undefined} */
				let yieldFn;
				/** @type {ResolveContextYield | undefined} */
				let yield_;
				/** @type {ResolveRequest[]} */
				const yieldResult = [];
				if (isYield) {
					yieldFn = resolveContext.yield;
					yield_ = (result) => {
						yieldResult.push(result);
					};
				}

				resolver.doResolve(
					target,
					request,
					null,
					yield_ ? { ...resolveContext, yield: yield_ } : resolveContext,
					(err, result) => {
						if (err) return callback(err);
						if (isYield) {
							if (result) yieldResult.push(result);
							for (const result of yieldResult) {
								/** @type {ResolveContextYield} */
								(yieldFn)(result);
							}
							this.cache[cacheId] = yieldResult;
							return callback(null, null);
						}
						if (result) return callback(null, (this.cache[cacheId] = result));
						callback();
					},
				);
			});
	}
};
