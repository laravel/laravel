/*
	MIT License http://www.opensource.org/licenses/mit-license.php
	Author Tobias Koppers @sokra
*/

"use strict";

const forEachBail = require("./forEachBail");
const { getPathsCached } = require("./getPaths");
const { PathType, getType } = require("./util/path");

/** @typedef {import("./Resolver")} Resolver */
/** @typedef {import("./Resolver").ResolveRequest} ResolveRequest */
/** @typedef {import("./Resolver").ResolveStepHook} ResolveStepHook */

module.exports = class SymlinkPlugin {
	/**
	 * @param {string | ResolveStepHook} source source
	 * @param {string | ResolveStepHook} target target
	 */
	constructor(source, target) {
		this.source = source;
		this.target = target;
	}

	/**
	 * @param {Resolver} resolver the resolver
	 * @returns {void}
	 */
	apply(resolver) {
		const target = resolver.ensureHook(this.target);
		const fs = resolver.fileSystem;
		resolver
			.getHook(this.source)
			.tapAsync("SymlinkPlugin", (request, resolveContext, callback) => {
				if (request.ignoreSymlinks) return callback();
				const pathsResult = getPathsCached(
					fs,
					/** @type {string} */ (request.path),
				);
				const { paths, segments } = pathsResult;
				// `pathsResult.segments` is shared across callers via the cache.
				// The only place we need to mutate is `pathSegments[idx] = result`
				// when `fs.readlink` succeeds — which is rare (the vast majority
				// of paths contain no symlinks, e.g. every resolve on
				// `cache-predicate`'s no-symlink fixture). Defer the copy until
				// we actually see a symlink so the common no-symlink path stays
				// allocation-free.
				/** @type {string[] | null} */
				let pathSegments = null;

				let containsSymlink = false;
				let idx = -1;
				forEachBail(
					paths,
					/**
					 * @param {string} path path
					 * @param {(err?: null | Error, result?: null | number) => void} callback callback
					 * @returns {void}
					 */
					(path, callback) => {
						idx++;
						if (resolveContext.fileDependencies) {
							resolveContext.fileDependencies.add(path);
						}
						fs.readlink(path, (err, result) => {
							if (!err && result) {
								// First symlink seen — take our own copy now, so
								// the cached `segments` array stays pristine for
								// sibling resolves.
								if (pathSegments === null) {
									pathSegments = [...segments];
								}
								pathSegments[idx] = /** @type {string} */ (result);
								containsSymlink = true;
								// Shortcut when absolute symlink found
								const resultType = getType(result.toString());
								if (
									resultType === PathType.AbsoluteWin ||
									resultType === PathType.AbsolutePosix
								) {
									return callback(null, idx);
								}
							}
							callback();
						});
					},
					/**
					 * @param {null | Error=} err error
					 * @param {null | number=} idx result
					 * @returns {void}
					 */
					(err, idx) => {
						if (!containsSymlink) return callback();
						// `containsSymlink === true` implies we took a copy in
						// `pathSegments` already, so it's non-null. The copy is
						// our own, so `slice` to trim is fine and spreading to
						// "unshare" is no longer necessary.
						const own = /** @type {string[]} */ (pathSegments);
						const resultSegments =
							typeof idx === "number" ? own.slice(0, idx + 1) : own;
						const result = resultSegments.reduceRight((a, b) =>
							resolver.join(a, b),
						);
						/** @type {ResolveRequest} */
						const obj = {
							...request,
							path: result,
						};
						resolver.doResolve(
							target,
							obj,
							`resolved symlink to ${result}`,
							resolveContext,
							(err, innerResult) => {
								if (err) return callback(err);
								// The symlink-resolved (real) path is authoritative. If
								// resolving it produced a result, use it. If it did not —
								// e.g. a `restrictions` rule rejected the real target —
								// stop here with no result instead of letting the next
								// plugin report the original in-root symlink path, which
								// would leave the symlink unresolved and bypass
								// `restrictions`.
								if (innerResult) return callback(null, innerResult);
								return callback(null, null);
							},
						);
					},
				);
			});
	}
};
