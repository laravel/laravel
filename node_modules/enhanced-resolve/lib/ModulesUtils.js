/*
	MIT License http://www.opensource.org/licenses/mit-license.php
	Author Tobias Koppers @sokra
*/

"use strict";

const forEachBail = require("./forEachBail");
const { getPathsCached } = require("./getPaths");

/** @typedef {import("./Resolver")} Resolver */
/** @typedef {import("./Resolver").ResolveRequest} ResolveRequest */
/** @typedef {import("./Resolver").ResolveStepHook} ResolveStepHook */
/** @typedef {import("./Resolver").ResolveContext} ResolveContext */
/** @typedef {(err?: null | Error, result?: null | ResolveRequest) => void} InnerCallback */

/**
 * Per-(directories-array) cache of the flat `addrs` list produced for a given
 * `request.path`. For a fixed directories configuration the fan-out of
 * `ancestor × directory` is deterministic per request.path, and many resolves
 * share the same starting directory (sibling files in one project, loops over
 * a batch of imports, etc.) — caching avoids the `getPaths` regex split plus
 * `len(paths) × len(directories)` join calls per resolve.
 *
 * The outer map is keyed on the directories array reference (plugin-owned,
 * stable for the lifetime of the resolver), and the inner map on the
 * starting `request.path`. Kept private to this module (rather than hung off
 * `resolver.pathCache`) so the pathCache's hidden-class shape is unchanged —
 * that avoids perturbing the interpreter-mode IC state for the
 * `resolver.pathCache.{join,dirname,basename}.fn(...)` accesses that run on
 * every resolve, which the CodSpeed instruction-count harness is sensitive to.
 * @type {WeakMap<string[], Map<string, string[]>>}
 */
const _addrsCacheByDirs = new WeakMap();

/**
 * @param {Resolver} resolver resolver
 * @param {string[]} directories directories
 * @param {ResolveStepHook} target target
 * @param {ResolveRequest} request request
 * @param {ResolveContext} resolveContext resolve context
 * @param {InnerCallback} callback callback
 * @returns {void}
 */
function modulesResolveHandler(
	resolver,
	directories,
	target,
	request,
	resolveContext,
	callback,
) {
	const fs = resolver.fileSystem;
	const requestPath = /** @type {string} */ (request.path);
	// Compute-or-reuse the flat `addrs` list. Inlined (rather than a helper
	// function) so the cache-hit path — which is the vast majority of
	// invocations — stays a single WeakMap + Map lookup with no function-call
	// overhead. See `_addrsCacheByDirs` above for caching rationale.
	let addrs;
	let perPath = _addrsCacheByDirs.get(directories);
	if (perPath === undefined) {
		perPath = new Map();
		_addrsCacheByDirs.set(directories, perPath);
	} else {
		addrs = perPath.get(requestPath);
	}
	if (addrs === undefined) {
		const { paths } = getPathsCached(fs, requestPath);
		const pathsLen = paths.length;
		const dirsLen = directories.length;
		// Pre-size the flat array rather than going through `map().reduce()`
		// with intermediate arrays + spreads.
		// eslint-disable-next-line unicorn/no-new-array
		addrs = new Array(pathsLen * dirsLen);
		let idx = 0;
		const joinFn = resolver.pathCache.join.fn;
		for (let pi = 0; pi < pathsLen; pi++) {
			const pathItem = paths[pi];
			for (let di = 0; di < dirsLen; di++) {
				addrs[idx++] = joinFn(pathItem, directories[di]);
			}
		}
		perPath.set(requestPath, addrs);
	}
	// Hoist the dot-prefixed request out of the per-addr iterator. `addrs`
	// can have up to `paths.length × directories.length` entries (e.g. 36
	// for an 8-deep source dir × 4-module config), and concatenating the
	// same `./${request.request}` string on every iteration is wasted
	// work — it's constant for the whole fan-out.
	const relRequest = `./${request.request}`;
	forEachBail(
		addrs,
		/**
		 * @param {string} addr addr
		 * @param {(err?: null | Error, result?: null | ResolveRequest) => void} callback callback
		 * @returns {void}
		 */
		(addr, callback) => {
			fs.stat(addr, (err, stat) => {
				if (!err && stat && stat.isDirectory()) {
					/** @type {ResolveRequest} */
					const obj = {
						...request,
						path: addr,
						request: relRequest,
						module: false,
					};
					const message = `looking for modules in ${addr}`;
					return resolver.doResolve(
						target,
						obj,
						message,
						resolveContext,
						callback,
					);
				}
				if (resolveContext.log) {
					resolveContext.log(`${addr} doesn't exist or is not a directory`);
				}
				if (resolveContext.missingDependencies) {
					resolveContext.missingDependencies.add(addr);
				}
				return callback();
			});
		},
		callback,
	);
}

module.exports = {
	modulesResolveHandler,
};
