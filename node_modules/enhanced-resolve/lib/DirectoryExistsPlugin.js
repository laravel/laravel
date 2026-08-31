/*
	MIT License http://www.opensource.org/licenses/mit-license.php
	Author Tobias Koppers @sokra
*/

"use strict";

/** @typedef {import("./Resolver")} Resolver */
/** @typedef {import("./Resolver").ResolveStepHook} ResolveStepHook */

module.exports = class DirectoryExistsPlugin {
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
		resolver
			.getHook(this.source)
			.tapAsync(
				"DirectoryExistsPlugin",
				(request, resolveContext, callback) => {
					const fs = resolver.fileSystem;
					const directory = request.path;
					if (!directory) return callback();
					fs.stat(directory, (err, stat) => {
						// Combine the two miss branches: a stat failure and a
						// "not a directory" result share the same handling — record
						// the path on `missingDependencies`, log the right reason,
						// then bail. The error-message ternary picks the wording
						// that matched the failing condition.
						if (err || !stat || !stat.isDirectory()) {
							if (resolveContext.missingDependencies) {
								resolveContext.missingDependencies.add(directory);
							}
							if (resolveContext.log) {
								resolveContext.log(
									err || !stat
										? `${directory} doesn't exist`
										: `${directory} is not a directory`,
								);
							}
							return callback();
						}
						if (resolveContext.fileDependencies) {
							resolveContext.fileDependencies.add(directory);
						}
						resolver.doResolve(
							target,
							request,
							`existing directory ${directory}`,
							resolveContext,
							callback,
						);
					});
				},
			);
	}
};
