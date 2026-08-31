/*!
* vue-router v5.2.0
* (c) 2026 Eduardo San Martin Morote
* @license MIT
*/
import { c as diagnostics, n as useRouter, t as useRoute } from "../useApi-CROJJdhE.js";
import { a as assign, b as toLazyValue, c as setCurrentContext, d as ABORT_CONTROLLER_KEY, f as APP_KEY, g as LOADER_ENTRIES_KEY, h as IS_USE_DATA_LOADER_KEY, l as trackRoute, m as IS_SSR_KEY, n as NavigationResult, o as getCurrentContext, p as DATA_LOADERS_EFFECT_SCOPE_KEY, s as isSubsetOf, v as PENDING_LOCATION_KEY, y as STAGED_NO_VALUE } from "../navigation-guard-FVgy-neA.js";
import { shallowRef, watch } from "vue";
import { defineQuery, useQuery, useQueryCache } from "@pinia/colada";
//#region src/experimental/data-loaders/defineColadaLoader.ts
function defineColadaLoader(nameOrOptions, _options) {
	_options = _options || nameOrOptions;
	const loader = _options.query;
	const options = {
		...DEFAULT_DEFINE_LOADER_OPTIONS,
		..._options,
		commit: _options?.commit || "after-load"
	};
	let isInitial = true;
	const useDefinedQuery = defineQuery(() => {
		const router = useRouter();
		const entry = router[LOADER_ENTRIES_KEY].get(loader);
		return useQuery({
			...options,
			query: () => {
				const route = entry.route.value;
				const [trackedRoute, params, query, hash] = trackRoute(route);
				entry.tracked.set(joinKeys(serializeQueryKey(options.key, trackedRoute)), {
					ready: false,
					params,
					query,
					hash
				});
				return router[APP_KEY].runWithContext(() => loader(trackedRoute, { signal: route.meta[ABORT_CONTROLLER_KEY]?.signal }));
			},
			key: () => toValueWithParameters(options.key, entry.route.value)
		});
	});
	function load(to, router, from, parent, reload) {
		const entries = router[LOADER_ENTRIES_KEY];
		const isSSR = router[IS_SSR_KEY];
		const key = serializeQueryKey(options.key, to);
		if (!entries.has(loader)) {
			const route = shallowRef(to);
			entries.set(loader, {
				data: shallowRef(),
				isLoading: shallowRef(false),
				error: shallowRef(null),
				to,
				options,
				children: /* @__PURE__ */ new Set(),
				resetPending() {
					this.pendingTo = null;
					this.pendingLoad = null;
					this.isLoading.value = false;
				},
				staged: STAGED_NO_VALUE,
				stagedError: null,
				commit,
				tracked: /* @__PURE__ */ new Map(),
				ext: null,
				route,
				pendingTo: null,
				pendingLoad: null
			});
		}
		const entry = entries.get(loader);
		if (entry.pendingTo === to && entry.pendingLoad) return entry.pendingLoad;
		const currentContext = getCurrentContext();
		if (process.env.NODE_ENV !== "production") {
			if (parent !== currentContext[0]) diagnostics.VUE_ROUTER_R1001({ key: `[${key.join(",")}]` });
		}
		setCurrentContext([
			entry,
			router,
			to
		]);
		if (!entry.ext) {
			entry.ext = useDefinedQuery();
			useQueryCache().get(toValueWithParameters(options.key, to))?.deps.delete(router[DATA_LOADERS_EFFECT_SCOPE_KEY]);
			reload = false;
		}
		const { isLoading, data, error, ext } = entry;
		if (isInitial) {
			isInitial = false;
			if (ext.data.value !== void 0) {
				data.value = ext.data.value;
				setCurrentContext(currentContext);
				return entry.pendingLoad = Promise.resolve();
			}
		}
		if (entry.route.value !== to) {
			const tracked = entry.tracked.get(joinKeys(key));
			reload = !tracked || hasRouteChanged(to, tracked);
		}
		entry.route.value = entry.pendingTo = to;
		isLoading.value = true;
		entry.staged = STAGED_NO_VALUE;
		entry.stagedError = error.value;
		const currentLoad = ext[reload ? "refetch" : "refresh"]().then(() => {
			if (entry.pendingLoad === currentLoad) {
				const newError = ext.error.value;
				if (newError) {
					if (newError instanceof NavigationResult) {
						entry.pendingTo = null;
						throw newError;
					}
					entry.stagedError = newError;
					if (!toLazyValue(options.lazy, to, from) || isSSR) throw newError;
				} else {
					const newData = ext.data.value;
					if (newData instanceof NavigationResult) {
						if (process.env.NODE_ENV !== "production") diagnostics.VUE_ROUTER_R1002();
						entry.pendingTo = null;
						throw newData;
					} else {
						entry.staged = newData;
						entry.stagedError = null;
					}
				}
			}
		}).finally(() => {
			setCurrentContext(currentContext);
			if (entry.pendingLoad === currentLoad) {
				isLoading.value = false;
				if (options.commit === "immediate" || !router[PENDING_LOCATION_KEY]) entry.commit(to);
			}
		});
		setCurrentContext(currentContext);
		entry.pendingLoad = currentLoad;
		return currentLoad;
	}
	function commit(to) {
		const key = serializeQueryKey(options.key, to);
		if (this.pendingTo === to) {
			if (process.env.NODE_ENV !== "production") {
				if (this.staged === STAGED_NO_VALUE && this.stagedError === null) diagnostics.VUE_ROUTER_R1003({ key: String(key) });
			}
			if (this.staged !== STAGED_NO_VALUE) {
				this.data.value = this.staged;
				if (process.env.NODE_ENV !== "production" && !this.tracked.has(joinKeys(key))) {
					diagnostics.VUE_ROUTER_R1006({ key: key.join(", ") });
					return;
				}
				this.tracked.get(joinKeys(key)).ready = true;
			}
			this.error.value = this.stagedError;
			this.staged = STAGED_NO_VALUE;
			this.stagedError = this.error.value;
			this.to = to;
			this.pendingTo = null;
			for (const childEntry of this.children) childEntry.commit(to);
		}
	}
	const useDataLoader = () => {
		const currentContext = getCurrentContext();
		const [parentEntry, _router, _route] = currentContext;
		const router = _router || useRouter();
		const route = _route || useRoute();
		const app = router[APP_KEY];
		const entries = router[LOADER_ENTRIES_KEY];
		let entry = entries.get(loader);
		if (!entry || parentEntry && entry.pendingTo !== route || !entry.pendingLoad) app.runWithContext(() => load(route, router, void 0, parentEntry, true));
		entry = entries.get(loader);
		if (parentEntry) {
			if (parentEntry !== entry) parentEntry.children.add(entry);
		}
		const { data, error, isLoading } = entry;
		const ext = useDefinedQuery();
		useQueryCache().get(toValueWithParameters(options.key, route))?.deps.delete(router[DATA_LOADERS_EFFECT_SCOPE_KEY]);
		watch(ext.data, (newData) => {
			if (!router[PENDING_LOCATION_KEY]) data.value = newData;
		});
		watch(ext.isLoading, (isFetching) => {
			if (!router[PENDING_LOCATION_KEY]) isLoading.value = isFetching;
		});
		watch(ext.error, (newError) => {
			if (!router[PENDING_LOCATION_KEY]) error.value = newError;
		});
		const useDataLoaderResult = {
			data,
			error,
			isLoading,
			reload: (to = router.currentRoute.value) => app.runWithContext(() => load(to, router, void 0, void 0, true)).then(() => entry.commit(to)),
			refetch: (to = router.currentRoute.value) => app.runWithContext(() => load(to, router, void 0, void 0, true)).then(() => (entry.commit(to), entry.ext.state.value)),
			refresh: (to = router.currentRoute.value) => app.runWithContext(() => load(to, router)).then(() => (entry.commit(to), entry.ext.state.value)),
			status: ext.status,
			asyncStatus: ext.asyncStatus,
			state: ext.state,
			isPending: ext.isPending
		};
		const promise = entry.pendingLoad.then(() => entry.staged === STAGED_NO_VALUE ? ext.data.value : entry.staged).catch((e) => parentEntry ? Promise.reject(e) : null).finally(() => setCurrentContext(currentContext));
		setCurrentContext(currentContext);
		return assign(promise, useDataLoaderResult);
	};
	useDataLoader[IS_USE_DATA_LOADER_KEY] = true;
	useDataLoader._ = {
		load,
		options,
		getEntry(router) {
			return router[LOADER_ENTRIES_KEY].get(loader);
		}
	};
	return useDataLoader;
}
const joinKeys = (keys) => keys.join("|");
function hasRouteChanged(to, tracked) {
	return !tracked.ready || !isSubsetOf(tracked.params, to.params) || !isSubsetOf(tracked.query, to.query) || tracked.hash.v != null && tracked.hash.v !== to.hash;
}
const DEFAULT_DEFINE_LOADER_OPTIONS = {
	lazy: false,
	server: true,
	commit: "after-load"
};
const toValueWithParameters = (optionValue, arg) => {
	return typeof optionValue === "function" ? optionValue(arg) : optionValue;
};
/**
* Transform the key to a string array so it can be used as a key in caches.
*
* @param key - key to transform
* @param to - route to use
*/
function serializeQueryKey(keyOption, to) {
	const key = toValueWithParameters(keyOption, to);
	return (Array.isArray(key) ? key : [key]).map(stringifyFlatObject);
}
function stringifyFlatObject(obj) {
	return obj && typeof obj === "object" ? JSON.stringify(obj, Object.keys(obj).sort()) : String(obj);
}
//#endregion
export { defineColadaLoader };
