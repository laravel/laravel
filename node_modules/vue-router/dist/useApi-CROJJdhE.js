/*!
* vue-router v5.2.0
* (c) 2026 Eduardo San Martin Morote
* @license MIT
*/
import { createConsoleReporter, defineDiagnostics } from "nostics";
import { inject } from "vue";
//#region src/utils/index.ts
/**
* Identity function that returns the value as is.
*
* @param v - the value to return
*
* @internal
*/
const identityFn = (v) => v;
/**
* Allows differentiating lazy components from functional components and vue-class-component
* @internal
*
* @param component
*/
function isRouteComponent(component) {
	return typeof component === "object" || "displayName" in component || "props" in component || "__vccOpts" in component;
}
function isESModule(obj) {
	return obj.__esModule || obj[Symbol.toStringTag] === "Module" || obj.default && isRouteComponent(obj.default);
}
const assign = Object.assign;
function applyToParams(fn, params) {
	const newParams = {};
	for (const key in params) {
		const value = params[key];
		newParams[key] = isArray(value) ? value.map(fn) : fn(value);
	}
	return newParams;
}
const noop = () => {};
/**
* Typesafe alternative to Array.isArray
* https://github.com/microsoft/TypeScript/pull/48228
*
* @internal
*/
const isArray = Array.isArray;
function mergeOptions(defaults, partialOptions) {
	const options = {};
	for (const key in defaults) options[key] = key in partialOptions ? partialOptions[key] : defaults[key];
	return options;
}
//#endregion
//#region src/errors.ts
const NavigationFailureSymbol = Symbol(process.env.NODE_ENV !== "production" ? "navigation failure" : "");
/**
* Enumeration with all possible types for navigation failures. Can be passed to
* {@link isNavigationFailure} to check for specific failures.
*/
let NavigationFailureType = /* @__PURE__ */ function(NavigationFailureType) {
	/**
	* An aborted navigation is a navigation that failed because a navigation
	* guard returned `false` or called `next(false)`
	*/
	NavigationFailureType[NavigationFailureType["aborted"] = 4] = "aborted";
	/**
	* A cancelled navigation is a navigation that failed because a more recent
	* navigation finished started (not necessarily finished).
	*/
	NavigationFailureType[NavigationFailureType["cancelled"] = 8] = "cancelled";
	/**
	* A duplicated navigation is a navigation that failed because it was
	* initiated while already being at the exact same location.
	*/
	NavigationFailureType[NavigationFailureType["duplicated"] = 16] = "duplicated";
	return NavigationFailureType;
}({});
const ErrorTypeMessages = {
	[1]({ location, currentLocation }) {
		return `No match for\n ${JSON.stringify(location)}${currentLocation ? "\nwhile being at\n" + JSON.stringify(currentLocation) : ""}`;
	},
	[2]({ from, to }) {
		return `Redirected from "${from.fullPath}" to "${stringifyRoute(to)}" via a navigation guard.`;
	},
	[4]({ from, to }) {
		return `Navigation aborted from "${from.fullPath}" to "${to.fullPath}" via a navigation guard.`;
	},
	[8]({ from, to }) {
		return `Navigation cancelled from "${from.fullPath}" to "${to.fullPath}" with a new navigation.`;
	},
	[16]({ from, to: _to }) {
		return `Avoided redundant navigation to current location: "${from.fullPath}".`;
	}
};
/**
* Creates a typed NavigationFailure object.
* @internal
* @param type - NavigationFailureType
* @param params - { from, to }
*/
function createRouterError(type, params) {
	if (process.env.NODE_ENV !== "production" || false) return assign(new Error(ErrorTypeMessages[type](params)), {
		type,
		[NavigationFailureSymbol]: true
	}, params);
	else return assign(/* @__PURE__ */ new Error(), {
		type,
		[NavigationFailureSymbol]: true
	}, params);
}
function isNavigationFailure(error, type) {
	return error instanceof Error && NavigationFailureSymbol in error && (type == null || !!(error.type & type));
}
const propertiesToLog = [
	"params",
	"query",
	"hash"
];
/**
* Stringifies a raw location for display in dev warnings.
*
* @internal
*/
function stringifyRoute(to) {
	if (!to || typeof to === "string") return to;
	if (to.path != null) return to.path;
	const location = {};
	for (const key of propertiesToLog) if (key in to) location[key] = to[key];
	return JSON.stringify(location, null, 2);
}
//#endregion
//#region src/diagnostics.ts
/**
* Runtime diagnostics catalog for Vue Router.
*
* Every entry has a stable `VUE_ROUTER_R####` code, a `why` that states the problem
* (the diagnosis only, never the remedy) and a `fix` that states the remedy
* (only, never the diagnosis). They are complementary: the reporter prints
* both, so neither repeats the other. The diagnosis substrings asserted by the
* warning tests stay in `why`. All call sites stay behind the existing `__DEV__` (or
* `process.env.NODE_ENV !== 'production'`) guards and remain bare expression
* statements so they tree-shake out of production builds.
*
* Codes are permanent: never rename or reuse one.
* - `VUE_ROUTER_R0###` core runtime warnings
* - `VUE_ROUTER_R1###` experimental data-loaders
*/
const diagnostics = /*#__PURE__*/ defineDiagnostics({
	reporters: [/*#__PURE__*/ createConsoleReporter()],
	codes: {
		VUE_ROUTER_R0001: {
			why: (p) => `Parent route "${p.name}" not found when adding child route`,
			fix: "Add the parent route before its children, or check the parent name for typos.",
			docs: "https://router.vuejs.org/guide/advanced/dynamic-routing.html#Adding-nested-routes"
		},
		VUE_ROUTER_R0002: {
			why: (p) => `Cannot remove non-existent route "${p.name}"`,
			fix: "Check the route name; it may already have been removed or was never added.",
			docs: "https://router.vuejs.org/guide/advanced/dynamic-routing.html#Removing-routes"
		},
		VUE_ROUTER_R0003: {
			why: (p) => `Location "${stringifyRoute(p.location)}" resolved to "${p.href}". A resolved location cannot start with multiple slashes.`,
			fix: "Remove the leading slashes from the location or fix the route configuration."
		},
		VUE_ROUTER_R0004: {
			why: (p) => `No match found for location with path "${stringifyRoute(p.path)}"`,
			fix: "Add a route matching this path or check for typos in the location.",
			docs: "https://router.vuejs.org/guide/essentials/dynamic-matching.html#Catch-all-404-Not-found-Route"
		},
		VUE_ROUTER_R0005: {
			why: (p) => `router.resolve() was passed an invalid location. This will fail in production.\nLocation: ${stringifyRoute(p.rawLocation)}`,
			fix: "Pass a valid route location: a string path or an object with `path` or `name`."
		},
		VUE_ROUTER_R0006: {
			why: (p) => `Path "${p.path}" was passed with params but they will be ignored because a "path" was passed.`,
			fix: "Use a named route `{ name, params }` instead of `{ path, params }`.",
			docs: "https://router.vuejs.org/guide/essentials/navigation.html#Navigate-to-a-different-location"
		},
		VUE_ROUTER_R0007: {
			why: (p) => `A \`hash\` should always start with the character "#" but received "${p.hash}".`,
			fix: (p) => `Prepend "#" to the hash in your route location: use "#${p.hash}".`
		},
		VUE_ROUTER_R0008: {
			why: (p) => `Invalid redirect found:\n${p.target}\n when navigating to "${p.to}".\nThis will break in production.`,
			fix: "A redirect must resolve to a location with a `name` or `path`; return one of those (or a string path) from `redirect`.",
			docs: "https://router.vuejs.org/guide/essentials/redirect-and-alias.html#Redirect"
		},
		VUE_ROUTER_R0009: {
			why: (p) => `Detected a possibly infinite redirection in a navigation guard when going from "${p.from}" to "${p.to}". Aborting to avoid a Stack Overflow. This might break in production if not fixed.`,
			fix: "A guard is returning a new location on every call; make that return conditional so it only redirects when actually needed.",
			docs: "https://router.vuejs.org/guide/advanced/navigation-guards.html#Global-Before-Guards"
		},
		VUE_ROUTER_R0010: {
			why: "Uncaught error during route navigation",
			fix: "Register an error handler with `router.onError()` to handle navigation errors."
		},
		VUE_ROUTER_R0011: {
			why: "Unexpected error when starting the router:",
			fix: "Inspect the actual cause; a navigation guard or async component likely threw during the initial navigation."
		},
		VUE_ROUTER_R0020: {
			why: (p) => `No active route record was found when calling \`${p.fn}()\`. Maybe you called it inside of App.vue?`,
			fix: "Call it from a component rendered inside <router-view> (a page component or one of its children), not from App.vue.",
			docs: "https://router.vuejs.org/guide/advanced/composition-api.html#Navigation-Guards"
		},
		VUE_ROUTER_R0021: {
			why: "No active route record was found when reactivating component with navigation guard. This is likely a bug in vue-router.",
			fix: "Report with a minimal reproduction at https://github.com/vuejs/router/issues/new/choose."
		},
		VUE_ROUTER_R0022: {
			why: (p) => `${p.fn}() was called outside of component setup but it must be called at the top of a setup function`,
			fix: "Call it synchronously at the top of `setup()`, before any `await`.",
			docs: "https://router.vuejs.org/guide/advanced/composition-api.html#Navigation-Guards"
		},
		VUE_ROUTER_R0023: {
			why: (p) => `The "next" callback was never called inside of ${p.name ? `"${p.name}"` : ""}:\n${p.guard}`,
			fix: "Make sure `next()` runs on every branch, including early returns and async paths, or drop the `next` parameter and return the value instead.",
			docs: "https://router.vuejs.org/guide/advanced/navigation-guards.html#Optional-third-argument-next"
		},
		VUE_ROUTER_R0024: {
			why: (p) => `The "next" callback was called more than once in one navigation guard when going from "${p.from}" to "${p.to}". This will fail in production.`,
			fix: "Call `next()` exactly once per guard: remove the extra call, or migrate to returning the value you passed to `next()`.",
			docs: "https://router.vuejs.org/guide/advanced/navigation-guards.html#Optional-third-argument-next"
		},
		VUE_ROUTER_R0025: {
			why: "The `next()` callback in navigation guards is deprecated.",
			fix: "Return the value instead: `next()` becomes `return`, `next(false)` becomes `return false`, `next(\"/path\")` becomes `return \"/path\"`.",
			docs: "https://router.vuejs.org/guide/advanced/navigation-guards.html#Optional-third-argument-next"
		},
		VUE_ROUTER_R0026: {
			why: (p) => `Record with path "${p.path}" is either missing a "component(s)" or "children" property.`,
			fix: "Add a `component`, `components`, or `children` to the route record.",
			docs: "https://router.vuejs.org/guide/essentials/nested-routes.html"
		},
		VUE_ROUTER_R0027: {
			why: (p) => `Component "${p.name}" in record with path "${p.path}" is not a valid component. Received "${p.received}".`,
			fix: "Pass a component or a function returning a Promise that resolves to one."
		},
		VUE_ROUTER_R0028: {
			why: (p) => `Component "${p.name}" in record with path "${p.path}" is a Promise instead of a function that returns a Promise. This will break in production if not fixed.`,
			fix: `Defer the import in an arrow function so it loads lazily: write "() => import('./MyPage.vue')", not "import('./MyPage.vue')".`,
			docs: "https://router.vuejs.org/guide/advanced/lazy-loading.html"
		},
		VUE_ROUTER_R0029: {
			why: (p) => `Component "${p.name}" in record with path "${p.path}" is defined using "defineAsyncComponent()".`,
			fix: `Drop the wrapper and pass "() => import('./MyPage.vue')" directly; the router handles lazy components itself.`,
			docs: "https://router.vuejs.org/guide/advanced/lazy-loading.html#Relationship-to-async-components"
		},
		VUE_ROUTER_R0030: {
			why: (p) => `Component "${p.name}" in record with path "${p.path}" is a function that does not return a Promise. This will break in production if not fixed.`,
			fix: "Return a dynamic import (`() => import(\"./MyPage.vue\")`) from the function, or add a `displayName` if it is a functional component.",
			docs: "https://router.vuejs.org/guide/advanced/lazy-loading.html"
		},
		VUE_ROUTER_R0040: {
			why: (p) => `Because "${p.el}" starts with "#", scrollBehavior resolves it as an element id via document.getElementById("${p.el.slice(1)}"), not as a CSS selector. No element has that id, but "${p.el}" does match an element with document.querySelector().`,
			fix: (p) => `Resolve the element yourself and return the node: el: document.querySelector('${p.el}').`,
			docs: "https://router.vuejs.org/guide/advanced/scroll-behavior.html"
		},
		VUE_ROUTER_R0041: {
			why: (p) => `The selector "${p.el}" is invalid. See https://mathiasbynens.be/notes/css-escapes or CSS.escape (https://developer.mozilla.org/en-US/docs/Web/API/CSS/escape) for the escaping rules.`,
			fix: "Build an id selector as `#${CSS.escape(id)}` so special characters in the id are escaped.",
			docs: "https://router.vuejs.org/guide/advanced/scroll-behavior.html"
		},
		VUE_ROUTER_R0042: {
			why: (p) => `Couldn't find element using selector "${p.el}" returned by scrollBehavior.`,
			fix: "Return a selector that matches an existing element, or guard against missing elements.",
			docs: "https://router.vuejs.org/guide/advanced/scroll-behavior.html"
		},
		VUE_ROUTER_R0050: {
			why: (p) => {
				let to;
				try {
					to = p.to === void 0 ? "undefined" : JSON.stringify(p.to);
				} catch {
					to = String(p.to);
				}
				return `Invalid value for prop "to" in useLink()\n- to: ${to}`;
			},
			fix: "Pass a valid route location (a string path or an object) to the \"to\" prop."
		},
		VUE_ROUTER_R0060: {
			why: (p) => `<router-view> can no longer be used directly inside <${p.comp}>.`,
			fix: (p) => `Wrap the slot's resolved component with <${p.comp}> instead of nesting <router-view> in it:\n\n<router-view v-slot="{ Component }">\n  <${p.comp}>\n    <component :is="Component" />\n  </${p.comp}>\n</router-view>`,
			docs: "https://router.vuejs.org/guide/advanced/router-view-slot.html#KeepAlive-Transition"
		},
		VUE_ROUTER_R0070: {
			why: (p) => `Cannot resolve a relative location without an absolute path. Trying to resolve "${p.to}" from "${p.from}".`,
			fix: (p) => `Resolve from an absolute \`from\` path that starts with "/", e.g. "/${p.from}".`
		},
		VUE_ROUTER_R0080: {
			why: (p) => `Error decoding "${p.text}". Using original value`,
			fix: "Ensure the value is correctly percent-encoded."
		},
		VUE_ROUTER_R0090: {
			why: (p) => `Found duplicated params with name "${p.name}" for path "${p.path}". Only the last one will be available on "$route.params".`,
			fix: "Give each param a unique name within the path.",
			docs: "https://router.vuejs.org/guide/essentials/route-matching-syntax.html"
		},
		VUE_ROUTER_R0100: {
			why: (p) => `Discarded invalid param(s) "${p.params}" when navigating.` + p.inherited + ` See https://github.com/vuejs/router/commit/e887570 for more details.`,
			fix: "Only pass params that exist on the target route."
		},
		VUE_ROUTER_R0101: {
			why: (p) => `The Matcher cannot resolve relative paths but received "${p.path}". Unless you directly called \`matcher.resolve("${p.path}")\`, this is probably a bug in vue-router. Please open an issue at https://github.com/vuejs/router/issues/new/choose.`,
			fix: "Pass an absolute path (starting with \"/\") to the matcher."
		},
		VUE_ROUTER_R0102: {
			why: (p) => `Alias "${p.alias}" and the original record: "${p.original}" must have the exact same param named "${p.name}"`,
			fix: "Use the same param names in the alias as in the original route.",
			docs: "https://router.vuejs.org/guide/essentials/redirect-and-alias.html#Alias"
		},
		VUE_ROUTER_R0103: {
			why: (p) => `The route named "${p.name}" has a child without a name, an empty path, and no children. Using that name won't render the empty path child, so this is probably a mistake.`,
			fix: "Move the `name` onto the empty-path child; or, if intentional, give the child its own name to silence this.",
			docs: "https://router.vuejs.org/guide/essentials/nested-routes.html#Nested-Named-Routes"
		},
		VUE_ROUTER_R0104: {
			why: (p) => `Absolute path "${p.path}" must have the exact same param named "${p.name}" as its parent "${p.parent}".`,
			fix: "Include the parent route params in the absolute child path.",
			docs: "https://router.vuejs.org/guide/essentials/nested-routes.html"
		},
		VUE_ROUTER_R0105: {
			why: (p) => `Finding ancestor route "${p.ancestor}" failed for "${p.record}"`,
			fix: "Report a reproduction at https://github.com/vuejs/router/issues/new/choose."
		},
		VUE_ROUTER_R0110: {
			why: `A hash base must end with a "#"`,
			fix: (p) => `Append "#" to the "base" argument passed to "createWebHashHistory()": "${p.base}" should be "${p.suggestion}".`
		},
		VUE_ROUTER_R0120: {
			why: "Error with push/replace State",
			fix: "The browser rejected the history API call; check for cross-origin or rate-limit issues."
		},
		VUE_ROUTER_R0121: {
			why: "history.state seems to have been manually replaced without preserving the necessary values.\nYou can find more information at https://router.vuejs.org/guide/migration/#Usage-of-history-state",
			fix: "Merge the router's state into your own when calling it manually: `history.replaceState({ ...history.state, ...yourState }, '', url)`.",
			docs: "https://router.vuejs.org/guide/migration.html#Usage-of-history-state"
		},
		VUE_ROUTER_R1001: {
			why: (p) => `Data loader "${String(p.key)}" has a different parent than the current context. This shouldn't be happening.`,
			fix: "Report a bug with a minimal reproduction at https://github.com/vuejs/router/."
		},
		VUE_ROUTER_R1002: {
			why: "Returning a NavigationResult is deprecated.",
			fix: "Replace `return new NavigationResult(to)` with `reroute(to)`, which throws internally to reroute.",
			docs: "https://router.vuejs.org/data-loaders/navigation-aware.html#Controlling-the-navigation-with-reroute-"
		},
		VUE_ROUTER_R1003: {
			why: (p) => `Loader "${p.key}"'s "commit()" was called but there is no staged data.`,
			fix: "Ensure the loader resolved before calling `commit()`.",
			docs: "https://router.vuejs.org/data-loaders/defining-loaders.html#Delaying-data-updates-with-commit"
		},
		VUE_ROUTER_R1004: {
			why: (p) => "A loader returned a NavigationResult but is not registered on the route." + p.key,
			fix: "Export the loader from the page component so it gets registered, e.g. `export const useUserData = defineLoader(...)`.",
			docs: "https://router.vuejs.org/data-loaders/organization.html"
		},
		VUE_ROUTER_R1005: {
			why: (p) => `Data loader "${p.key}" has itself as parent. This shouldn't be happening.`,
			fix: "Report a bug with a minimal reproduction at https://github.com/vuejs/router/."
		},
		VUE_ROUTER_R1006: {
			why: (p) => `A query was defined with the same key as the loader "[${p.key}]".\nSee https://pinia-colada.esm.dev/#TODO`,
			fix: "If the key is meant to match, use the data loader directly; otherwise rename the `useQuery()` key so it no longer collides.",
			docs: "https://router.vuejs.org/data-loaders/colada.html"
		},
		VUE_ROUTER_R1007: {
			why: "Data Loader was setup twice.",
			fix: "Register `DataLoaderPlugin` a single time via `app.use()`.",
			docs: "https://router.vuejs.org/data-loaders.html#Installation"
		},
		VUE_ROUTER_R1008: {
			why: "Data Loader is experimental and subject to breaking changes in the future.",
			docs: "https://router.vuejs.org/data-loaders.html"
		},
		VUE_ROUTER_R1009: {
			why: "Returning a NavigationResult from a loader is deprecated.",
			fix: "Call `reroute(to)` inside the loader instead of returning `new NavigationResult(to)`; it throws internally to reroute.",
			docs: "https://router.vuejs.org/data-loaders/navigation-aware.html#Controlling-the-navigation-with-reroute-"
		}
	}
});
//#endregion
//#region src/injectionSymbols.ts
/**
* RouteRecord being rendered by the closest ancestor Router View. Used for
* `onBeforeRouteUpdate` and `onBeforeRouteLeave`. rvlm stands for Router View
* Location Matched
*
* @internal
*/
const matchedRouteKey = Symbol(process.env.NODE_ENV !== "production" ? "router view location matched" : "");
/**
* Allows overriding the router view depth to control which component in
* `matched` is rendered. rvd stands for Router View Depth
*
* @internal
*/
const viewDepthKey = Symbol(process.env.NODE_ENV !== "production" ? "router view depth" : "");
/**
* Allows overriding the router instance returned by `useRouter` in tests. r
* stands for router
*
* @internal
*/
const routerKey = Symbol(process.env.NODE_ENV !== "production" ? "router" : "");
/**
* Allows overriding the current route returned by `useRoute` in tests. rl
* stands for route location
*
* @internal
*/
const routeLocationKey = Symbol(process.env.NODE_ENV !== "production" ? "route location" : "");
/**
* Allows overriding the current route used by router-view. Internally this is
* used when the `route` prop is passed.
*
* @internal
*/
const routerViewLocationKey = Symbol(process.env.NODE_ENV !== "production" ? "router view location" : "");
//#endregion
//#region src/useApi.ts
/**
* Returns the router instance. Equivalent to using `$router` inside
* templates.
*/
function useRouter() {
	return inject(routerKey);
}
/**
* Returns the current route location. Equivalent to using `$route` inside
* templates.
*/
function useRoute(_name) {
	return inject(routeLocationKey);
}
//#endregion
export { isRouteComponent as _, routerKey as a, diagnostics as c, isNavigationFailure as d, applyToParams as f, isESModule as g, isArray as h, routeLocationKey as i, NavigationFailureType as l, identityFn as m, useRouter as n, routerViewLocationKey as o, assign as p, matchedRouteKey as r, viewDepthKey as s, useRoute as t, createRouterError as u, mergeOptions as v, noop as y };
