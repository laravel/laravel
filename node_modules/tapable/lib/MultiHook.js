/*
	MIT License http://www.opensource.org/licenses/mit-license.php
	Author Tobias Koppers @sokra
*/
"use strict";

class MultiHook {
	constructor(hooks, name = undefined) {
		this.hooks = hooks;
		this.name = name;
	}

	tap(options, fn) {
		const { hooks } = this;
		for (let i = 0; i < hooks.length; i++) {
			hooks[i].tap(options, fn);
		}
	}

	tapAsync(options, fn) {
		const { hooks } = this;
		for (let i = 0; i < hooks.length; i++) {
			hooks[i].tapAsync(options, fn);
		}
	}

	tapPromise(options, fn) {
		const { hooks } = this;
		for (let i = 0; i < hooks.length; i++) {
			hooks[i].tapPromise(options, fn);
		}
	}

	isUsed() {
		const { hooks } = this;
		for (let i = 0; i < hooks.length; i++) {
			if (hooks[i].isUsed()) return true;
		}
		return false;
	}

	intercept(interceptor) {
		const { hooks } = this;
		for (let i = 0; i < hooks.length; i++) {
			hooks[i].intercept(interceptor);
		}
	}

	withOptions(options) {
		return new MultiHook(
			this.hooks.map((hook) => hook.withOptions(options)),
			this.name
		);
	}
}

module.exports = MultiHook;
