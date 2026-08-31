/*!
 * pinia v4.0.2
 * (c) 2026 Eduardo San Martin Morote
 * @license MIT
 */
var Pinia = (function(exports, vue) {
	Object.defineProperty(exports, Symbol.toStringTag, { value: "Module" });
	//#region src/env.ts
	const IS_CLIENT = typeof window !== "undefined";
	//#endregion
	//#region ../../node_modules/.pnpm/nostics@1.1.4/node_modules/nostics/dist/index.mjs
	/**
	* Renders a diagnostic into a multi-line, unicode-decorated string suitable
	* for terminal output. The first line is `[<name>] <message>`; optional
	* details (`fix`, `sources`, `docs`) follow with `├▶`/`╰▶` connectors.
	*/
	function formatDiagnostic(diagnostic) {
		const header = `[${diagnostic.name}] ${diagnostic.message}`;
		const details = [];
		if (diagnostic.fix) details.push(`fix: ${diagnostic.fix}`);
		if (diagnostic.sources?.length) details.push(`sources: ${diagnostic.sources.join(", ")}`);
		if (diagnostic.docs) details.push(`see: ${diagnostic.docs}`);
		if (details.length === 0) return header;
		return [header, ...details.map((detail, i) => {
			return `${i < details.length - 1 ? "├▶" : "╰▶"} ${detail}`;
		})].join("\n");
	}
	/**
	* Transforms a value or a function that returns a value to a value.
	*
	* @param valFn either a value or a function that returns a value
	* @param args  arguments to pass to the function if `valFn` is a function
	*
	* @internal
	*/
	function toValueWithArgs(valFn, ...args) {
		return typeof valFn === "function" ? valFn(...args) : valFn;
	}
	/**
	* Creates a console reporter that renders each diagnostic with `formatter` and
	* prints the result via `console[method]`. Both default sensibly (`'warn'` and
	* {@link formatDiagnostic}); `method` can also be overridden per call through
	* the reporter options.
	*/
	/* @__NO_SIDE_EFFECTS__ */
	function createConsoleReporter({ method: defaultMethod = "warn", formatter = formatDiagnostic } = {}) {
		return (diagnostic, { method = defaultMethod } = {}) => {
			console[method](formatter(diagnostic));
		};
	}
	const captureStackTrace = Error.captureStackTrace;
	var Diagnostic = class Diagnostic extends Error {
		name = "Diagnostic";
		/**
		* URL to extended documentation for this diagnostic code.
		* Auto-generated from {@link DefineDiagnosticsOptions.docsBase}.
		*/
		docs;
		/**
		* Optional actionable instructions on how to resolve the problem.
		*/
		fix;
		/**
		* Locations in user code that contributed to this diagnostic, in
		* `file:line:column` format. Relevant when the stack trace doesn't reflect
		* the user's source (e.g. compilers, bundlers), otherwise redundant with the
		* stack and should be omitted.
		*/
		sources;
		/**
		* Alias for {@link Error.message}: the reason this diagnostic was raised.
		*/
		get why() {
			return this.message;
		}
		/**
		* @param init        structured initializer; `why` is required
		* @param captureFrom V8 stack-cutoff frame. Defaults to {@link Diagnostic}
		* so the top of the trace is the `new Diagnostic(...)` call site.
		* `defineDiagnostics` passes its action method to strip its own frames too.
		* Ignored on engines without `Error.captureStackTrace`.
		*/
		constructor(init, captureFrom = Diagnostic) {
			super(init.why, { cause: init.cause });
			this.fix = init.fix;
			this.docs = init.docs;
			this.sources = init.sources;
			captureStackTrace?.(this, captureFrom);
		}
		/**
		* Converts the diagnostic into a serializable structured object.
		*/
		toJSON() {
			return {
				name: this.name,
				why: this.why,
				fix: this.fix,
				docs: this.docs,
				sources: this.sources,
				cause: this.cause,
				stack: this.stack
			};
		}
	};
	/**
	* Resolves the docs URL for a code from a `docsBase` (string template or
	* resolver function). Shared by {@link defineDiagnostics} and
	* {@link defineProdDiagnostics}. Per-code `docs` overrides are handled by the
	* caller; this only covers the `docsBase`-derived case.
	*
	* @internal
	*/
	function deriveDocs(docsBase, code) {
		return typeof docsBase === "string" ? `${docsBase}/${code.toLowerCase()}` : docsBase?.(code);
	}
	/**
	* Creates a typed diagnostics object from a set of code definitions. Each
	* code becomes a callable {@link DiagnosticHandle}: invoke to report, or
	* `throw` the result to raise. No `new` required, no proxy.
	*/
	/* @__NO_SIDE_EFFECTS__ */
	function defineDiagnostics(options) {
		const reporters = options.reporters ?? [];
		const result = {};
		const { docsBase } = options;
		for (const code of Object.keys(options.codes)) {
			const def = options.codes[code];
			const docs = def.docs === false ? void 0 : def.docs || deriveDocs(docsBase, code);
			const handle = (params = {}, reporterOptions = {}) => {
				const diagnostic = new Diagnostic({
					why: toValueWithArgs(def.why, params),
					fix: toValueWithArgs(def.fix, params),
					docs,
					cause: params.cause,
					sources: params.sources
				}, handle);
				diagnostic.name = code;
				for (const reporter of reporters) reporter(diagnostic, reporterOptions);
				return diagnostic;
			};
			result[code] = handle;
		}
		return result;
	}
	//#endregion
	//#region src/diagnostics.ts
	/**
	* Catalog of user-facing Pinia diagnostics. Each handle builds a diagnostic
	* and runs the reporters. All call sites are dev-only (`__DEV__` guarded or
	* HMR), so production builds drop the calls and tree-shake this catalog.
	*/
	const diagnostics = /*#__PURE__*/ defineDiagnostics({
		reporters: [/*#__PURE__*/ createConsoleReporter()],
		codes: {
			PINIA_R1001: {
				why: "Directly pass all stores to \"mapStores()\" without putting them in an array. This will fail in production.",
				fix: "Replace mapStores([useAuthStore, useCartStore]) with mapStores(useAuthStore, useCartStore).",
				docs: "https://pinia.vuejs.org/cookbook/options-api.html#Giving-access-to-the-whole-store"
			},
			PINIA_R1002: {
				why: (p) => `A getter cannot have the same name as another state property. Found "${p.name}" in store "${p.id}".`,
				fix: "Rename either the getter or the state property.",
				docs: "https://pinia.vuejs.org/core-concepts/getters.html#Accessing-other-getters"
			},
			PINIA_R1003: {
				why: (p) => `The "state" must be a plain object. Found in store "${p.id}".`,
				fix: "Return a plain object, e.g. avoid state: () => new MyClass().",
				docs: "https://pinia.vuejs.org/core-concepts/state.html#State"
			},
			PINIA_R1004: {
				why: "Pinia instance not found in context. This falls back to the global activePinia, which exposes you to cross-request pollution on the server.",
				fix: "\"useStore()\" is a composable and follows the same rules: call it at the top of setup() (or another composable), or pass the pinia instance explicitly when used outside of a component.",
				docs: "https://pinia.vuejs.org/ssr/#Using-the-store-outside-of-setup-"
			},
			PINIA_R1005: {
				why: (p) => `The store id changed from "${p.from}" to "${p.to}", forcing a reload.`,
				docs: "https://pinia.vuejs.org/cookbook/hot-module-replacement.html#HMR-Hot-Module-Replacement-"
			},
			PINIA_R1006: {
				why: (p) => `Property "${p.key}" of store "${p.id}" is not reactive (not a ref, reactive object, or shallowRef), so storeToRefs() ignores it.`,
				fix: "If it should be reactive state, wrap it with ref(), reactive(), or shallowRef(). If it is an intentional non-reactive property, wrap it with markRaw() so storeToRefs() skips it explicitly.",
				docs: "https://pinia.vuejs.org/core-concepts/plugins.html#Adding-new-external-properties"
			},
			PINIA_R1007: {
				why: (p) => `The same callback was passed to "$subscribe()" of store "${p.id}" more than once. Subscriptions are deduplicated, so the duplicate is ignored.`,
				fix: "Subscribe each callback only once. If you need to resubscribe, call the returned function to remove the previous subscription first, or create a new function.",
				docs: "https://pinia.vuejs.org/core-concepts/state.html#Subscribing-to-the-state"
			}
		}
	});
	//#endregion
	//#region src/rootStore.ts
	/**
	* setActivePinia must be called to handle SSR at the top of functions like
	* `fetch`, `setup`, `serverPrefetch` and others
	*/
	let activePinia;
	/**
	* Sets or unsets the active pinia. Used in SSR and internally when calling
	* actions and getters
	*
	* @param pinia - Pinia instance
	*/
	const setActivePinia = (pinia) => activePinia = pinia;
	/**
	* Get the currently active pinia if there is any.
	*/
	const getActivePinia = () => {
		const pinia = (0, vue.hasInjectionContext)() && (0, vue.inject)(piniaSymbol);
		if (!pinia && !IS_CLIENT) diagnostics.PINIA_R1004({}, { method: "error" });
		return pinia || activePinia;
	};
	/**
	* Symbol used to provide/inject the pinia instance in the app. Used internally
	* and exposed for testing purposes and edge cases like storybook. Could break
	* in a minor, **USE AT YOUR OWN RISK**.
	*
	* For context, see:
	* - https://github.com/vuejs/pinia/issues/870
	* - https://github.com/vuejs/pinia/pull/2973
	*
	* @internal
	*/
	const piniaSymbol = Symbol("pinia");
	//#endregion
	//#region src/types.ts
	function isPlainObject(o) {
		return o && typeof o === "object" && Object.prototype.toString.call(o) === "[object Object]" && typeof o.toJSON !== "function";
	}
	/**
	* Possible types for SubscriptionCallback
	*/
	let MutationType = /* @__PURE__ */ function(MutationType) {
		/**
		* Direct mutation of the state:
		*
		* - `store.name = 'new name'`
		* - `store.$state.name = 'new name'`
		* - `store.list.push('new item')`
		*/
		MutationType["direct"] = "direct";
		/**
		* Mutated the state with `$patch` and an object
		*
		* - `store.$patch({ name: 'newName' })`
		*/
		MutationType["patchObject"] = "patch object";
		/**
		* Mutated the state with `$patch` and a function
		*
		* - `store.$patch(state => state.name = 'newName')`
		*/
		MutationType["patchFunction"] = "patch function";
		return MutationType;
	}({});
	//#endregion
	//#region src/createPinia.ts
	/**
	* Creates a Pinia instance to be used by the application
	*/
	function createPinia() {
		const scope = (0, vue.effectScope)(true);
		const state = scope.run(() => (0, vue.ref)({}));
		let _p = [];
		let toBeInstalled = [];
		const pinia = (0, vue.markRaw)({
			install(app) {
				setActivePinia(pinia);
				pinia._a = app;
				app.provide(piniaSymbol, pinia);
				app.config.globalProperties.$pinia = pinia;
				toBeInstalled.forEach((plugin) => _p.push(plugin));
				toBeInstalled = [];
			},
			use(plugin) {
				if (!this._a) toBeInstalled.push(plugin);
				else _p.push(plugin);
				return this;
			},
			_p,
			_a: null,
			_e: scope,
			_s: /* @__PURE__ */ new Map(),
			state
		});
		return pinia;
	}
	/**
	* Dispose a Pinia instance by stopping its effectScope and removing the state, plugins and stores. This is mostly
	* useful in tests, with both a testing pinia or a regular pinia and in applications that use multiple pinia instances.
	* Once disposed, the pinia instance cannot be used anymore.
	*
	* @param pinia - pinia instance
	*/
	function disposePinia(pinia) {
		pinia._e.stop();
		pinia._s.clear();
		pinia._p.splice(0);
		pinia.state.value = {};
		pinia._a = null;
	}
	//#endregion
	//#region src/hmr.ts
	/**
	* Checks if a function is a `StoreDefinition`.
	*
	* @param fn - object to test
	* @returns true if `fn` is a StoreDefinition
	*/
	const isUseStore = (fn) => {
		return typeof fn === "function" && typeof fn.$id === "string";
	};
	/**
	* Mutates in place `newState` with `oldState` to _hot update_ it. It will
	* remove any key not existing in `newState` and recursively merge plain
	* objects.
	*
	* @param newState - new state object to be patched
	* @param oldState - old state that should be used to patch newState
	* @returns - newState
	*/
	function patchObject(newState, oldState) {
		for (const key in oldState) {
			const subPatch = oldState[key];
			if (!(key in newState)) continue;
			const targetValue = newState[key];
			if (isPlainObject(targetValue) && isPlainObject(subPatch) && !(0, vue.isRef)(subPatch) && !(0, vue.isReactive)(subPatch)) newState[key] = patchObject(targetValue, subPatch);
			else newState[key] = subPatch;
		}
		return newState;
	}
	/**
	* Creates an _accept_ function to pass to `import.meta.hot` in Vite applications.
	*
	* @example
	* ```js
	* const useUser = defineStore(...)
	* if (import.meta.hot) {
	*   import.meta.hot.accept(acceptHMRUpdate(useUser, import.meta.hot))
	* }
	* ```
	*
	* @param initialUseStore - return of the defineStore to hot update
	* @param hot - `import.meta.hot`
	*/
	function acceptHMRUpdate(initialUseStore, hot) {
		return (newModule) => {
			const pinia = hot.data.pinia || initialUseStore._pinia;
			if (!pinia) return;
			hot.data.pinia = pinia;
			for (const exportName in newModule) {
				const useStore = newModule[exportName];
				if (isUseStore(useStore) && pinia._s.has(useStore.$id)) {
					const id = useStore.$id;
					if (id !== initialUseStore.$id) {
						diagnostics.PINIA_R1005({
							from: initialUseStore.$id,
							to: id
						});
						return hot.invalidate();
					}
					const existingStore = pinia._s.get(id);
					if (!existingStore) {
						console.log(`[Pinia]: skipping hmr because store doesn't exist yet`);
						return;
					}
					useStore(pinia, existingStore);
				}
			}
		};
	}
	//#endregion
	//#region src/subscriptions.ts
	const noop = () => {};
	function addSubscription(subscriptions, callback, detached, onCleanup = noop) {
		subscriptions.add(callback);
		const removeSubscription = () => {
			subscriptions.delete(callback) && onCleanup();
		};
		if (!detached && (0, vue.getCurrentScope)()) (0, vue.onScopeDispose)(removeSubscription);
		return removeSubscription;
	}
	function triggerSubscriptions(subscriptions, ...args) {
		subscriptions.forEach((callback) => {
			callback(...args);
		});
	}
	//#endregion
	//#region src/store.ts
	const fallbackRunWithContext = (fn) => fn();
	/**
	* Marks a function as an action for `$onAction`
	* @internal
	*/
	const ACTION_MARKER = Symbol();
	/**
	* Action name symbol. Allows to add a name to an action after defining it
	* @internal
	*/
	const ACTION_NAME = Symbol();
	function mergeReactiveObjects(target, patchToApply) {
		if (target instanceof Map && patchToApply instanceof Map) patchToApply.forEach((value, key) => target.set(key, value));
		else if (target instanceof Set && patchToApply instanceof Set) patchToApply.forEach(target.add, target);
		for (const key in patchToApply) {
			if (!Object.hasOwn(patchToApply, key)) continue;
			const subPatch = patchToApply[key];
			const targetValue = target[key];
			if (isPlainObject(targetValue) && isPlainObject(subPatch) && Object.hasOwn(target, key) && !(0, vue.isRef)(subPatch) && !(0, vue.isReactive)(subPatch)) target[key] = mergeReactiveObjects(targetValue, subPatch);
			else target[key] = subPatch;
		}
		return target;
	}
	const skipHydrateSymbol = Symbol("pinia:skipHydration");
	/**
	* Tells Pinia to skip the hydration process of a given object. This is useful in setup stores (only) when you return a
	* stateful object in the store but it isn't really state. e.g. returning a router instance in a setup store.
	*
	* @param obj - target object
	* @returns obj
	*/
	function skipHydrate(obj) {
		return Object.defineProperty(obj, skipHydrateSymbol, {});
	}
	/**
	* Returns whether a value should be hydrated
	*
	* @param obj - target variable
	* @returns true if `obj` should be hydrated
	*/
	function shouldHydrate(obj) {
		return !obj || typeof obj !== "object" || !Object.hasOwn(obj, skipHydrateSymbol);
	}
	const { assign } = Object;
	function isComputed(o) {
		return !!((0, vue.isRef)(o) && o.effect);
	}
	function createOptionsStore(id, options, pinia, hot) {
		const { state, actions, getters } = options;
		const initialState = pinia.state.value[id];
		let store;
		function setup() {
			if (!initialState && !hot)
 /* istanbul ignore if */
			pinia.state.value[id] = state ? state() : {};
			const localState = hot ? (0, vue.toRefs)((0, vue.ref)(state ? state() : {}).value) : (0, vue.toRefs)(pinia.state.value[id]);
			return assign(localState, actions, Object.keys(getters || {}).reduce((computedGetters, name) => {
				if (name in localState) diagnostics.PINIA_R1002({
					name,
					id
				});
				computedGetters[name] = (0, vue.markRaw)((0, vue.computed)(() => {
					setActivePinia(pinia);
					const store = pinia._s.get(id);
					return getters[name].call(store, store);
				}));
				return computedGetters;
			}, {}));
		}
		store = createSetupStore(id, setup, options, pinia, hot, true);
		return store;
	}
	function createSetupStore($id, setup, options = {}, pinia, hot, isOptionsStore) {
		let scope;
		const optionsForPlugin = assign({ actions: {} }, options);
		/* istanbul ignore if */
		if (!pinia._e.active) throw new Error("Pinia destroyed");
		const $subscribeOptions = { deep: true };
		$subscribeOptions.onTrigger = (event) => {
			/* istanbul ignore else */
			if (isListening) debuggerEvents = event;
			else if (isListening === false && !store._hotUpdating)
 /* istanbul ignore else */
			if (Array.isArray(debuggerEvents)) debuggerEvents.push(event);
			else console.error("🍍 debuggerEvents should be an array. This is most likely an internal Pinia bug.");
		};
		let isListening;
		let isSyncListening;
		let subscriptions = /* @__PURE__ */ new Set();
		let actionSubscriptions = /* @__PURE__ */ new Set();
		let debuggerEvents;
		const initialState = pinia.state.value[$id];
		if (!isOptionsStore && !initialState && !hot)
 /* istanbul ignore if */
		pinia.state.value[$id] = {};
		const hotState = /*#__PURE__*/ (0, vue.ref)({});
		let activeListener;
		function $patch(partialStateOrMutator) {
			let subscriptionMutation;
			isListening = isSyncListening = false;
			debuggerEvents = [];
			if (typeof partialStateOrMutator === "function") {
				partialStateOrMutator(pinia.state.value[$id]);
				subscriptionMutation = {
					type: "patch function",
					storeId: $id,
					events: debuggerEvents
				};
			} else {
				mergeReactiveObjects(pinia.state.value[$id], partialStateOrMutator);
				subscriptionMutation = {
					type: "patch object",
					payload: partialStateOrMutator,
					storeId: $id,
					events: debuggerEvents
				};
			}
			const myListenerId = activeListener = Symbol();
			(0, vue.nextTick)().then(() => {
				if (activeListener === myListenerId) isListening = true;
			});
			isSyncListening = true;
			triggerSubscriptions(subscriptions, subscriptionMutation, pinia.state.value[$id]);
		}
		const $reset = isOptionsStore ? function $reset() {
			const { state } = options;
			const newState = state ? state() : {};
			this.$patch(($state) => {
				assign($state, newState);
			});
		} : () => {
			throw new Error(`🍍: Store "${$id}" is built using the setup syntax and does not implement $reset().`);
		};
		function $dispose() {
			scope.stop();
			subscriptions.clear();
			actionSubscriptions.clear();
			pinia._s.delete($id);
		}
		/**
		* Helper that wraps function so it can be tracked with $onAction
		* @param fn - action to wrap
		* @param name - name of the action
		*/
		const action = (fn, name = "") => {
			if (ACTION_MARKER in fn) {
				fn[ACTION_NAME] = name;
				return fn;
			}
			const wrappedAction = function() {
				setActivePinia(pinia);
				const args = Array.from(arguments);
				const afterCallbackSet = /* @__PURE__ */ new Set();
				const onErrorCallbackSet = /* @__PURE__ */ new Set();
				function after(callback) {
					afterCallbackSet.add(callback);
				}
				function onError(callback) {
					onErrorCallbackSet.add(callback);
				}
				triggerSubscriptions(actionSubscriptions, {
					args,
					name: wrappedAction[ACTION_NAME],
					store,
					after,
					onError
				});
				let ret;
				try {
					ret = fn.apply(this && this.$id === $id ? this : store, args);
				} catch (error) {
					triggerSubscriptions(onErrorCallbackSet, error);
					throw error;
				}
				if (ret instanceof Promise) return ret.then((value) => {
					triggerSubscriptions(afterCallbackSet, value);
					return value;
				}).catch((error) => {
					triggerSubscriptions(onErrorCallbackSet, error);
					return Promise.reject(error);
				});
				triggerSubscriptions(afterCallbackSet, ret);
				return ret;
			};
			wrappedAction[ACTION_MARKER] = true;
			wrappedAction[ACTION_NAME] = name;
			return wrappedAction;
		};
		const _hmrPayload = /*#__PURE__*/ (0, vue.markRaw)({
			actions: {},
			getters: {},
			state: [],
			hotState
		});
		const partialStore = {
			_p: pinia,
			$id,
			$onAction: addSubscription.bind(null, actionSubscriptions),
			$patch,
			$reset,
			$subscribe(callback, options = {}) {
				if (subscriptions.has(callback)) {
					diagnostics.PINIA_R1007({ id: $id });
					return noop;
				}
				const removeSubscription = addSubscription(subscriptions, callback, options.detached, () => stopWatcher());
				const stopWatcher = scope.run(() => (0, vue.watch)(() => pinia.state.value[$id], (state) => {
					if (options.flush === "sync" ? isSyncListening : isListening) callback({
						storeId: $id,
						type: "direct",
						events: debuggerEvents
					}, state);
				}, assign({}, $subscribeOptions, options)));
				return removeSubscription;
			},
			$dispose
		};
		const store = (0, vue.reactive)(assign({
			_hmrPayload,
			_customProperties: (0, vue.markRaw)(/* @__PURE__ */ new Set())
		}, partialStore));
		pinia._s.set($id, store);
		const setupStore = (pinia._a && pinia._a.runWithContext || fallbackRunWithContext)(() => pinia._e.run(() => (scope = (0, vue.effectScope)()).run(() => setup({ action }))));
		for (const key in setupStore) {
			const prop = setupStore[key];
			if ((0, vue.isRef)(prop) && !isComputed(prop) || (0, vue.isReactive)(prop)) {
				if (hot) hotState.value[key] = (0, vue.toRef)(setupStore, key);
				else if (!isOptionsStore) {
					if (initialState && shouldHydrate(prop)) if ((0, vue.isRef)(prop)) prop.value = initialState[key];
					else mergeReactiveObjects(prop, initialState[key]);
					pinia.state.value[$id][key] = prop;
				}
				_hmrPayload.state.push(key);
			} else if (typeof prop === "function") {
				setupStore[key] = hot ? prop : action(prop, key);
				_hmrPayload.actions[key] = prop;
				optionsForPlugin.actions[key] = prop;
			} else if (isComputed(prop)) {
				_hmrPayload.getters[key] = isOptionsStore ? options.getters[key] : prop;
				if (IS_CLIENT) (setupStore._getters || (setupStore._getters = (0, vue.markRaw)([]))).push(key);
			}
		}
		/* istanbul ignore if */
		assign(store, setupStore);
		assign((0, vue.toRaw)(store), setupStore);
		Object.defineProperty(store, "$state", {
			get: () => hot ? hotState.value : pinia.state.value[$id],
			set: (state) => {
				/* istanbul ignore if */
				if (hot) throw new Error("cannot set hotState");
				$patch(($state) => {
					assign($state, state);
				});
			}
		});
		store._hotUpdate = (0, vue.markRaw)((newStore) => {
			store._hotUpdating = true;
			newStore._hmrPayload.state.forEach((stateKey) => {
				if (stateKey in store.$state) {
					const newStateTarget = newStore.$state[stateKey];
					const oldStateSource = store.$state[stateKey];
					if (isOptionsStore && typeof newStateTarget === "object" && isPlainObject(newStateTarget) && isPlainObject(oldStateSource)) patchObject(newStateTarget, oldStateSource);
					else newStore.$state[stateKey] = oldStateSource;
				}
				store[stateKey] = (0, vue.toRef)(newStore.$state, stateKey);
			});
			Object.keys(store.$state).forEach((stateKey) => {
				if (!(stateKey in newStore.$state)) delete store[stateKey];
			});
			isListening = false;
			isSyncListening = false;
			pinia.state.value[$id] = (0, vue.toRef)(newStore._hmrPayload, "hotState");
			isSyncListening = true;
			(0, vue.nextTick)().then(() => {
				isListening = true;
			});
			for (const actionName in newStore._hmrPayload.actions) {
				const actionFn = newStore[actionName];
				store[actionName] = action(actionFn, actionName);
			}
			for (const getterName in newStore._hmrPayload.getters) {
				const getter = newStore._hmrPayload.getters[getterName];
				const getterValue = isOptionsStore ? (0, vue.computed)(() => {
					setActivePinia(pinia);
					return getter.call(store, store);
				}) : getter;
				store[getterName] = getterValue;
			}
			Object.keys(store._hmrPayload.getters).forEach((key) => {
				if (!(key in newStore._hmrPayload.getters)) delete store[key];
			});
			Object.keys(store._hmrPayload.actions).forEach((key) => {
				if (!(key in newStore._hmrPayload.actions)) delete store[key];
			});
			store._hmrPayload = newStore._hmrPayload;
			store._getters = newStore._getters;
			store._hotUpdating = false;
		});
		pinia._p.forEach((extender) => {
			const extensions = scope.run(() => extender({
				store,
				app: pinia._a,
				pinia,
				options: optionsForPlugin
			}));
			for (const key in extensions) {
				const value = extensions[key];
				if (typeof value === "object" && !(0, vue.isRef)(value) && !(0, vue.isReactive)(value) && !value?.__v_skip) diagnostics.PINIA_R1006({
					key,
					id: $id
				});
			}
			assign(store, extensions);
		});
		if (store.$state && typeof store.$state === "object" && typeof store.$state.constructor === "function" && !store.$state.constructor.toString().includes("[native code]")) diagnostics.PINIA_R1003({ id: store.$id });
		if (initialState && isOptionsStore && options.hydrate) options.hydrate(store.$state, initialState);
		isListening = true;
		isSyncListening = true;
		return store;
	}
	/*! #__NO_SIDE_EFFECTS__ */
	function defineStore(id, setup, setupOptions) {
		let options;
		const isSetupStore = typeof setup === "function";
		options = isSetupStore ? setupOptions : setup;
		function useStore(pinia, hot) {
			const hasContext = (0, vue.hasInjectionContext)();
			pinia = pinia || (hasContext ? (0, vue.inject)(piniaSymbol, null) : null);
			if (pinia) setActivePinia(pinia);
			if (!activePinia) throw new Error("[🍍]: \"getActivePinia()\" was called but there was no active Pinia. Are you trying to use a store before calling \"app.use(pinia)\"?\nSee https://pinia.vuejs.org/core-concepts/outside-component-usage.html for help.\nThis will fail in production.");
			pinia = activePinia;
			if (!pinia._s.has(id)) {
				if (isSetupStore) createSetupStore(id, setup, options, pinia);
				else createOptionsStore(id, options, pinia);
				useStore._pinia = pinia;
			}
			const store = pinia._s.get(id);
			if (hot) {
				const hotId = "__hot:" + id;
				const newStore = isSetupStore ? createSetupStore(hotId, setup, options, pinia, true) : createOptionsStore(hotId, assign({}, options), pinia, true);
				hot._hotUpdate(newStore);
				delete pinia.state.value[hotId];
				pinia._s.delete(hotId);
			}
			if (IS_CLIENT) {
				const currentInstance = (0, vue.getCurrentInstance)();
				if (currentInstance && currentInstance.proxy && !hot) {
					const vm = currentInstance.proxy;
					const cache = "_pStores" in vm ? vm._pStores : vm._pStores = {};
					cache[id] = store;
				}
			}
			return store;
		}
		useStore.$id = id;
		return useStore;
	}
	//#endregion
	//#region src/mapHelpers.ts
	let mapStoreSuffix = "Store";
	/**
	* Changes the suffix added by `mapStores()`. Can be set to an empty string.
	* Defaults to `"Store"`. Make sure to extend the MapStoresCustomization
	* interface if you are using TypeScript.
	*
	* @param suffix - new suffix
	*/
	function setMapStoreSuffix(suffix) {
		mapStoreSuffix = suffix;
	}
	/**
	* Allows using stores without the composition API (`setup()`) by generating an
	* object to be spread in the `computed` field of a component. It accepts a list
	* of store definitions.
	*
	* @example
	* ```js
	* export default {
	*   computed: {
	*     // other computed properties
	*     ...mapStores(useUserStore, useCartStore)
	*   },
	*
	*   created() {
	*     this.userStore // store with id "user"
	*     this.cartStore // store with id "cart"
	*   }
	* }
	* ```
	*
	* @param stores - list of stores to map to an object
	*/
	function mapStores(...stores) {
		if (Array.isArray(stores[0])) {
			diagnostics.PINIA_R1001();
			stores = stores[0];
		}
		return stores.reduce((reduced, useStore) => {
			reduced[useStore.$id + mapStoreSuffix] = function() {
				return useStore(this.$pinia);
			};
			return reduced;
		}, {});
	}
	/**
	* Allows using state and getters from one store without using the composition
	* API (`setup()`) by generating an object to be spread in the `computed` field
	* of a component.
	*
	* @param useStore - store to map from
	* @param keysOrMapper - array or object
	*/
	function mapState(useStore, keysOrMapper) {
		return Array.isArray(keysOrMapper) ? keysOrMapper.reduce((reduced, key) => {
			reduced[key] = function() {
				return useStore(this.$pinia)[key];
			};
			return reduced;
		}, {}) : Object.keys(keysOrMapper).reduce((reduced, key) => {
			reduced[key] = function() {
				const store = useStore(this.$pinia);
				const storeKey = keysOrMapper[key];
				return typeof storeKey === "function" ? storeKey.call(this, store) : store[storeKey];
			};
			return reduced;
		}, {});
	}
	/**
	* Alias for `mapState()`. You should use `mapState()` instead.
	* @deprecated use `mapState()` instead.
	*/
	const mapGetters = mapState;
	/**
	* Allows directly using actions from your store without using the composition
	* API (`setup()`) by generating an object to be spread in the `methods` field
	* of a component.
	*
	* @param useStore - store to map from
	* @param keysOrMapper - array or object
	*/
	function mapActions(useStore, keysOrMapper) {
		return Array.isArray(keysOrMapper) ? keysOrMapper.reduce((reduced, key) => {
			reduced[key] = function(...args) {
				return useStore(this.$pinia)[key](...args);
			};
			return reduced;
		}, {}) : Object.keys(keysOrMapper).reduce((reduced, key) => {
			reduced[key] = function(...args) {
				return useStore(this.$pinia)[keysOrMapper[key]](...args);
			};
			return reduced;
		}, {});
	}
	/**
	* Allows using state and getters from one store without using the composition
	* API (`setup()`) by generating an object to be spread in the `computed` field
	* of a component.
	*
	* @param useStore - store to map from
	* @param keysOrMapper - array or object
	*/
	function mapWritableState(useStore, keysOrMapper) {
		return Array.isArray(keysOrMapper) ? keysOrMapper.reduce((reduced, key) => {
			reduced[key] = {
				get() {
					return useStore(this.$pinia)[key];
				},
				set(value) {
					return useStore(this.$pinia)[key] = value;
				}
			};
			return reduced;
		}, {}) : Object.keys(keysOrMapper).reduce((reduced, key) => {
			reduced[key] = {
				get() {
					return useStore(this.$pinia)[keysOrMapper[key]];
				},
				set(value) {
					return useStore(this.$pinia)[keysOrMapper[key]] = value;
				}
			};
			return reduced;
		}, {});
	}
	//#endregion
	//#region src/storeToRefs.ts
	/**
	* Creates an object of references with all the state, getters, and plugin-added
	* state properties of the store. Similar to `toRefs()` but specifically
	* designed for Pinia stores so methods and non reactive properties are
	* completely ignored.
	*
	* @param store - store to extract the refs from
	*/
	function storeToRefs(store) {
		const rawStore = (0, vue.toRaw)(store);
		const refs = {};
		for (const key in rawStore) {
			const value = rawStore[key];
			if (value?.effect) refs[key] = (0, vue.computed)({
				get: () => store[key],
				set(value) {
					store[key] = value;
				}
			});
			else if ((0, vue.isRef)(value) || (0, vue.isReactive)(value)) refs[key] = (0, vue.toRef)(store, key);
		}
		return refs;
	}
	//#endregion
	exports.MutationType = MutationType;
	exports.acceptHMRUpdate = acceptHMRUpdate;
	exports.createPinia = createPinia;
	exports.defineStore = defineStore;
	exports.disposePinia = disposePinia;
	exports.getActivePinia = getActivePinia;
	exports.mapActions = mapActions;
	exports.mapGetters = mapGetters;
	exports.mapState = mapState;
	exports.mapStores = mapStores;
	exports.mapWritableState = mapWritableState;
	exports.piniaSymbol = piniaSymbol;
	exports.setActivePinia = setActivePinia;
	exports.setMapStoreSuffix = setMapStoreSuffix;
	exports.shouldHydrate = shouldHydrate;
	exports.skipHydrate = skipHydrate;
	exports.storeToRefs = storeToRefs;
	return exports;
})({}, Vue);
