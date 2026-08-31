/*!
* vue-router v5.2.0
* (c) 2026 Eduardo San Martin Morote
* @license MIT
*/
import { c as diagnostics, d as isNavigationFailure } from "./useApi-CROJJdhE.js";
import { effectScope, inject, shallowRef } from "vue";
//#region src/experimental/data-loaders/createDataLoader.ts
const toLazyValue = (lazy, to, from) => typeof lazy === "function" ? lazy(to, from) : lazy;
//#endregion
//#region src/experimental/data-loaders/symbols.ts
/**
* Retrieves the internal version of loaders.
* @internal
*/
const LOADER_SET_KEY = Symbol("loaders");
/**
* Retrieves the internal version of loader entries.
* @internal
*/
const LOADER_ENTRIES_KEY = Symbol("loaderEntries");
/**
* Added to the loaders returned by `defineLoader()` to identify them.
* Allows to extract exported useData() from a component.
* @internal
*/
const IS_USE_DATA_LOADER_KEY = Symbol();
/**
* Symbol used to save the pending location on the router.
* @internal
*/
const PENDING_LOCATION_KEY = Symbol();
/**
* Symbol used to know there is no value staged for the loader and that commit should be skipped.
* @internal
*/
const STAGED_NO_VALUE = Symbol();
/**
* Gives access to the current app and it's `runWithContext` method.
* @internal
*/
const APP_KEY = Symbol();
/**
* Gives access to an AbortController that aborts when the navigation is canceled.
* @internal
*/
const ABORT_CONTROLLER_KEY = Symbol();
/**
* Symbol used to save the initial data on the router.
* @internal
*/
const IS_SSR_KEY = Symbol();
/**
* Symbol used to get the effect scope used for data loaders.
* @internal
*/
const DATA_LOADERS_EFFECT_SCOPE_KEY = Symbol();
//#endregion
//#region src/experimental/data-loaders/utils.ts
/**
* Check if a value is a `DataLoader`.
*
* @param loader - the object to check
*/
function isDataLoader(loader) {
	return loader && loader[IS_USE_DATA_LOADER_KEY];
}
/**
* @internal: data loaders authoring only. Use `getCurrentContext` instead.
*/
let currentContext;
function getCurrentContext() {
	return currentContext || [];
}
/**
* Sets the current context for data loaders. This allows for nested loaders to be aware of their parent context.
* INTERNAL ONLY.
*
* @param context - the context to set
* @internal
*/
function setCurrentContext(context) {
	currentContext = context ? context.length ? context : null : null;
}
/**
* Restore the current context after a promise is resolved.
* @param promise - promise to wrap
*/
function withLoaderContext(promise) {
	const context = currentContext;
	return promise.finally(() => currentContext = context);
}
const assign = Object.assign;
/**
* Track the reads of a route and its properties
* @internal
* @param route - route to track
*/
function trackRoute(route) {
	const [params, paramReads] = trackObjectReads(route.params);
	const [query, queryReads] = trackObjectReads(route.query);
	let hash = { v: null };
	return [
		{
			...route,
			get hash() {
				return hash.v = route.hash;
			},
			params,
			query
		},
		paramReads,
		queryReads,
		hash
	];
}
/**
*  Track the reads of an object (that doesn't change) and add the read properties to an object
* @internal
* @param obj - object to track
*/
function trackObjectReads(obj) {
	const reads = {};
	return [new Proxy(obj, { get(target, p, receiver) {
		const value = Reflect.get(target, p, receiver);
		reads[p] = value;
		return value;
	} }), reads];
}
/**
* Returns `true` if `inner` is a subset of `outer`. Used to check if a tr
*
* @internal
* @param outer - the bigger params
* @param inner - the smaller params
*/
function isSubsetOf(inner, outer) {
	for (const key in inner) {
		const innerValue = inner[key];
		const outerValue = outer[key];
		if (typeof innerValue === "string") {
			if (innerValue !== outerValue) return false;
		} else if (!innerValue || !outerValue) {
			if (innerValue !== outerValue) return false;
		} else if (!Array.isArray(outerValue) || outerValue.length !== innerValue.length || innerValue.some((value, i) => value !== outerValue[i])) return false;
	}
	return true;
}
//#endregion
//#region src/experimental/data-loaders/navigation-guard.ts
/**
* Key to inject the global loading state for loaders used in `useIsDataLoading`.
* @internal
*/
const IS_DATA_LOADING_KEY = Symbol();
/**
* TODO: export functions that allow preloading outside of a navigation guard
*/
/**
* Setups the different Navigation Guards to collect the data loaders from the route records and then to execute them.
* @internal used by the `DataLoaderPlugin`
* @see {@link DataLoaderPlugin}
*
* @param router - the router instance
* @returns
*/
function setupLoaderGuard({ router, app, effect: scope, isSSR, errors: globalErrors = [] }) {
	if (router[LOADER_ENTRIES_KEY] != null) {
		if (process.env.NODE_ENV !== "production") diagnostics.VUE_ROUTER_R1007();
		return () => {};
	}
	if (process.env.NODE_ENV === "development" && !isSSR) diagnostics.VUE_ROUTER_R1008();
	router[LOADER_ENTRIES_KEY] = /* @__PURE__ */ new WeakMap();
	router[APP_KEY] = app;
	router[DATA_LOADERS_EFFECT_SCOPE_KEY] = scope;
	router[IS_SSR_KEY] = !!isSSR;
	const isDataLoading = scope.run(() => shallowRef(false));
	app.provide(IS_DATA_LOADING_KEY, isDataLoading);
	const removeLoaderGuard = router.beforeEach((to) => {
		if (router[PENDING_LOCATION_KEY]) router[PENDING_LOCATION_KEY].meta[ABORT_CONTROLLER_KEY]?.abort();
		router[PENDING_LOCATION_KEY] = to;
		to.meta[LOADER_SET_KEY] = /* @__PURE__ */ new Set();
		to.meta[ABORT_CONTROLLER_KEY] = new AbortController();
		for (const record of to.matched) record.meta[LOADER_SET_KEY] ??= new Set(record.meta.loaders || []);
	});
	const removeDataLoaderGuard = router.beforeResolve((to, from) => {
		for (const record of to.matched) {
			for (const loader of record.meta[LOADER_SET_KEY]) to.meta[LOADER_SET_KEY].add(loader);
			for (const componentName in record.mods) {
				const viewModule = record.mods[componentName];
				for (const exportName in viewModule) {
					const exportValue = viewModule[exportName];
					if (isDataLoader(exportValue)) to.meta[LOADER_SET_KEY].add(exportValue);
				}
				const component = record.components?.[componentName];
				if (component && Array.isArray(component.__loaders)) {
					for (const loader of component.__loaders) if (isDataLoader(loader)) to.meta[LOADER_SET_KEY].add(loader);
				}
			}
		}
		const loaders = Array.from(to.meta[LOADER_SET_KEY]);
		const { signal } = to.meta[ABORT_CONTROLLER_KEY];
		setCurrentContext([]);
		isDataLoading.value = true;
		return Promise.all(loaders.map((loader) => {
			const { server, lazy, errors } = loader._.options;
			if (!server && isSSR) return;
			const ret = scope.run(() => app.runWithContext(() => loader._.load(to, router, from)));
			return !isSSR && toLazyValue(lazy, to, from) ? void 0 : ret.catch((reason) => {
				if (errors === true) {
					if (Array.isArray(globalErrors) ? globalErrors.some((Err) => reason instanceof Err) : globalErrors(reason)) return;
				} else if (errors && (Array.isArray(errors) ? errors.some((Err) => reason instanceof Err) : errors(reason))) return;
				throw reason;
			});
		})).then((results) => {
			if (process.env.NODE_ENV !== "production") {
				for (const result of results) if (result instanceof NavigationResult) {
					diagnostics.VUE_ROUTER_R1009();
					throw result;
				}
			}
		}).catch((error) => error instanceof NavigationResult ? error.value : signal.aborted && error === signal.reason ? false : Promise.reject(error)).finally(() => {
			setCurrentContext([]);
			isDataLoading.value = false;
		});
	});
	const removeAfterEach = router.afterEach((to, from, failure) => {
		if (failure) {
			to.meta[ABORT_CONTROLLER_KEY]?.abort(failure);
			if (isNavigationFailure(failure, 16)) for (const loader of to.meta[LOADER_SET_KEY]) loader._.getEntry(router).resetPending();
		} else for (const loader of to.meta[LOADER_SET_KEY]) {
			const { commit, lazy } = loader._.options;
			if (commit === "after-load") {
				const entry = loader._.getEntry(router);
				if (entry && (!toLazyValue(lazy, to, from) || !entry.isLoading.value)) entry.commit(to);
			}
		}
		if (router[PENDING_LOCATION_KEY] === to) router[PENDING_LOCATION_KEY] = null;
	});
	const removeOnError = router.onError((error, to) => {
		to.meta[ABORT_CONTROLLER_KEY]?.abort(error);
		if (router[PENDING_LOCATION_KEY] === to) router[PENDING_LOCATION_KEY] = null;
	});
	return () => {
		delete router[LOADER_ENTRIES_KEY];
		delete router[APP_KEY];
		removeLoaderGuard();
		removeDataLoaderGuard();
		removeAfterEach();
		removeOnError();
	};
}
/**
* Allows data loaders to change navigation. Called by {@link reroute}.
*
* @internal
*/
var NavigationResult = class {
	value;
	constructor(value) {
		this.value = value;
	}
};
/**
* Changes the navigation from within a data loader. Accepts the same values as a navigation
* guard return: a route location to redirect to, or `false` to cancel the navigation.
*
* @example
* ```ts
* export const useUserData = defineBasicLoader(async (to) => {
*   const user = await fetchUser(to.params.id)
*   if (!user) {
*     reroute('/404')
*   }
*   return user
* })
* ```
*/
function reroute(to) {
	throw new NavigationResult(to);
}
/**
* Data Loader plugin to add data loading support to Vue Router.
*
* @example
* ```ts
* const router = createRouter({
*   routes,
*   history: createWebHistory(),
* })
*
* const app = createApp({})
* app.use(DataLoaderPlugin, { router })
* app.use(router)
* ```
*/
function DataLoaderPlugin(app, options) {
	const effect = effectScope(true);
	const removeGuards = setupLoaderGuard(assign({
		app,
		effect
	}, options));
	app.onUnmount(() => {
		effect.stop();
		removeGuards();
	});
}
/**
* Return a ref that reflects the global loading state of all loaders within a navigation.
* This state doesn't update if `refresh()` is manually called.
*/
function useIsDataLoading() {
	return inject(IS_DATA_LOADING_KEY);
}
//#endregion
export { LOADER_SET_KEY as _, assign as a, toLazyValue as b, setCurrentContext as c, ABORT_CONTROLLER_KEY as d, APP_KEY as f, LOADER_ENTRIES_KEY as g, IS_USE_DATA_LOADER_KEY as h, useIsDataLoading as i, trackRoute as l, IS_SSR_KEY as m, NavigationResult as n, getCurrentContext as o, DATA_LOADERS_EFFECT_SCOPE_KEY as p, reroute as r, isSubsetOf as s, DataLoaderPlugin as t, withLoaderContext as u, PENDING_LOCATION_KEY as v, STAGED_NO_VALUE as y };
