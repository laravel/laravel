/*
	MIT License http://www.opensource.org/licenses/mit-license.php
	Author Tobias Koppers @sokra
*/

"use strict";

/** @typedef {import("./Resolver")} Resolver */
/** @typedef {import("./Resolver").ResolveStepHook} ResolveStepHook */
/** @typedef {string | string[] | false} Alias */
/** @typedef {{ alias: Alias, name: string, onlyModule?: boolean }} AliasOption */

const { aliasResolveHandler, compileAliasOptions } = require("./AliasUtils");

/**
 * When `alias` is given as an array, the targets are tried in priority
 * order and the first matching one wins. Tried-and-failed higher-priority
 * targets are recorded on `resolveContext.missingDependencies` (via the
 * downstream `FileExistsPlugin`) so that a consumer's watcher can
 * invalidate the resolve once one of them appears. The winning target is
 * recorded on `resolveContext.fileDependencies`; its removal triggers
 * re-resolution, at which point the fallback target is returned.
 *
 * Callers that cache successful resolves (e.g. webpack's `unsafeCache`)
 * are responsible for invalidating those entries when the tracked
 * dependencies change -- otherwise a stale path may survive across
 * rebuilds even though this plugin itself would return the correct
 * fallback on a fresh resolve.
 */
module.exports = class AliasPlugin {
	/**
	 * @param {string | ResolveStepHook} source source
	 * @param {AliasOption | AliasOption[]} options options
	 * @param {string | ResolveStepHook} target target
	 */
	constructor(source, options, target) {
		this.source = source;
		this.options = Array.isArray(options) ? options : [options];
		this.target = target;
	}

	/**
	 * @param {Resolver} resolver the resolver
	 * @returns {void}
	 */
	apply(resolver) {
		const target = resolver.ensureHook(this.target);
		const compiled = compileAliasOptions(resolver, this.options);

		resolver
			.getHook(this.source)
			.tapAsync("AliasPlugin", (request, resolveContext, callback) => {
				aliasResolveHandler(
					resolver,
					compiled,
					target,
					request,
					resolveContext,
					callback,
				);
			});
	}
};
