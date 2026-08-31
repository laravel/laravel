/*
	MIT License http://www.opensource.org/licenses/mit-license.php
	Author Tobias Koppers @sokra
*/

"use strict";

/** @typedef {import("./Resolver").ResolveContext} ResolveContext */

/**
 * Build the `ResolveContext` passed into the next hook in the chain.
 *
 * The caller — `Resolver.doResolve` — runs on every resolve step, so we
 * want to allocate as little as possible here. Previously the caller
 * constructed a temporary `{ log, yield, fileDependencies, ... }` literal
 * and handed it to this helper, which then copied those same fields into
 * a second fresh object. That's two allocations per step for what is
 * effectively a struct copy with one mutated field (`stack`) and one
 * optionally-wrapped field (`log`). Taking the parent context and the
 * two things we actually want to change (stack, message) as separate
 * arguments lets us allocate exactly one inner context.
 * @param {ResolveContext} parent parent resolve context to inherit dependency sets / yield from
 * @param {ResolveContext["stack"]} stack new stack tip for the nested call
 * @param {null | string} message log message prefix for this step
 * @returns {ResolveContext} inner context
 */
module.exports = function createInnerContext(parent, stack, message) {
	const parentLog = parent.log;
	let innerLog;
	if (parentLog) {
		if (message) {
			let messageReported = false;
			/**
			 * @param {string} msg message
			 */
			innerLog = (msg) => {
				if (!messageReported) {
					parentLog(message);
					messageReported = true;
				}
				parentLog(`  ${msg}`);
			};
		} else {
			innerLog = parentLog;
		}
	}

	return {
		log: innerLog,
		yield: parent.yield,
		fileDependencies: parent.fileDependencies,
		contextDependencies: parent.contextDependencies,
		missingDependencies: parent.missingDependencies,
		stack,
	};
};
