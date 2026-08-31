/*!
* vue-router v5.2.0
* (c) 2026 Eduardo San Martin Morote
* @license MIT
*/
import { _ as isRouteComponent, c as diagnostics, g as isESModule, h as isArray, p as assign, r as matchedRouteKey, u as createRouterError } from "./useApi-CROJJdhE.js";
import { getCurrentInstance, inject, onActivated, onDeactivated, onUnmounted, watch } from "vue";
import { setupDevtoolsPlugin } from "@vue/devtools-api";
//#region src/utils/env.ts
const isBrowser = typeof document !== "undefined";
//#endregion
//#region src/encoding.ts
/**
* Encoding Rules (␣ = Space)
* - Path: ␣ " < > # ? { }
* - Query: ␣ " < > # & =
* - Hash: ␣ " < > `
*
* On top of that, the RFC3986 (https://tools.ietf.org/html/rfc3986#section-2.2)
* defines some extra characters to be encoded. Most browsers do not encode them
* in encodeURI https://github.com/whatwg/url/issues/369, so it may be safer to
* also encode `!'()*`. Leaving un-encoded only ASCII alphanumeric(`a-zA-Z0-9`)
* plus `-._~`. This extra safety should be applied to query by patching the
* string returned by encodeURIComponent encodeURI also encodes `[\]^`. `\`
* should be encoded to avoid ambiguity. Browsers (IE, FF, C) transform a `\`
* into a `/` if directly typed in. The _backtick_ (`````) should also be
* encoded everywhere because some browsers like FF encode it when directly
* written while others don't. Safari and IE don't encode ``"<>{}``` in hash.
*/
const HASH_RE = /#/g;
const AMPERSAND_RE = /&/g;
const SLASH_RE = /\//g;
const EQUAL_RE = /=/g;
const IM_RE = /\?/g;
const PLUS_RE = /\+/g;
/**
* NOTE: It's not clear to me if we should encode the + symbol in queries, it
* seems to be less flexible than not doing so and I can't find out the legacy
* systems requiring this for regular requests like text/html. In the standard,
* the encoding of the plus character is only mentioned for
* application/x-www-form-urlencoded
* (https://url.spec.whatwg.org/#urlencoded-parsing) and most browsers seems lo
* leave the plus character as is in queries. To be more flexible, we allow the
* plus character on the query, but it can also be manually encoded by the user.
*
* Resources:
* - https://url.spec.whatwg.org/#urlencoded-parsing
* - https://stackoverflow.com/questions/1634271/url-encoding-the-space-character-or-20
*/
const ENC_BRACKET_OPEN_RE = /%5B/g;
const ENC_BRACKET_CLOSE_RE = /%5D/g;
const ENC_CARET_RE = /%5E/g;
const ENC_BACKTICK_RE = /%60/g;
const ENC_CURLY_OPEN_RE = /%7B/g;
const ENC_PIPE_RE = /%7C/g;
const ENC_CURLY_CLOSE_RE = /%7D/g;
const ENC_SPACE_RE = /%20/g;
/**
* Encode characters that need to be encoded on the path, search and hash
* sections of the URL.
*
* @internal
* @param text - string to encode
* @returns encoded string
*/
function commonEncode(text) {
	return text == null ? "" : encodeURI("" + text).replace(ENC_PIPE_RE, "|").replace(ENC_BRACKET_OPEN_RE, "[").replace(ENC_BRACKET_CLOSE_RE, "]");
}
/**
* Encode characters that need to be encoded on the hash section of the URL.
*
* @param text - string to encode
* @returns encoded string
*/
function encodeHash(text) {
	return commonEncode(text).replace(ENC_CURLY_OPEN_RE, "{").replace(ENC_CURLY_CLOSE_RE, "}").replace(ENC_CARET_RE, "^");
}
/**
* Encode characters that need to be encoded query values on the query
* section of the URL.
*
* @param text - string to encode
* @returns encoded string
*/
function encodeQueryValue(text) {
	return commonEncode(text).replace(PLUS_RE, "%2B").replace(ENC_SPACE_RE, "+").replace(HASH_RE, "%23").replace(AMPERSAND_RE, "%26").replace(ENC_BACKTICK_RE, "`").replace(ENC_CURLY_OPEN_RE, "{").replace(ENC_CURLY_CLOSE_RE, "}").replace(ENC_CARET_RE, "^");
}
/**
* Like `encodeQueryValue` but also encodes the `=` character.
*
* @param text - string to encode
*/
function encodeQueryKey(text) {
	return encodeQueryValue(text).replace(EQUAL_RE, "%3D");
}
/**
* Encode characters that need to be encoded on the path section of the URL.
*
* @param text - string to encode
* @returns encoded string
*/
function encodePath(text) {
	return commonEncode(text).replace(HASH_RE, "%23").replace(IM_RE, "%3F");
}
/**
* Encode characters that need to be encoded on the path section of the URL as a
* param. This function encodes everything {@link encodePath} does plus the
* slash (`/`) character. If `text` is `null` or `undefined`, returns an empty
* string instead.
*
* @param text - string to encode
* @returns encoded string
*/
function encodeParam(text) {
	return encodePath(text).replace(SLASH_RE, "%2F");
}
function decode(text) {
	if (text == null) return null;
	try {
		return decodeURIComponent("" + text);
	} catch {
		process.env.NODE_ENV !== "production" && diagnostics.VUE_ROUTER_R0080({ text: "" + text });
	}
	return "" + text;
}
//#endregion
//#region src/location.ts
const TRAILING_SLASH_RE = /\/$/;
const removeTrailingSlash = (path) => path.replace(TRAILING_SLASH_RE, "");
/**
* Transforms a URI into a normalized history location
*
* @param parseQuery
* @param location - URI to normalize
* @param currentLocation - current absolute location. Allows resolving relative
* paths. Must start with `/`. Defaults to `/`
* @returns a normalized history location
*/
function parseURL(parseQuery, location, currentLocation = "/") {
	let path, query = {}, searchString = "", hash = "";
	const hashPos = location.indexOf("#");
	let searchPos = location.indexOf("?");
	searchPos = hashPos >= 0 && searchPos > hashPos ? -1 : searchPos;
	if (searchPos >= 0) {
		path = location.slice(0, searchPos);
		searchString = location.slice(searchPos, hashPos > 0 ? hashPos : location.length);
		query = parseQuery(searchString.slice(1));
	}
	if (hashPos >= 0) {
		path = path || location.slice(0, hashPos);
		hash = location.slice(hashPos, location.length);
	}
	path = resolveRelativePath(path != null ? path : location, currentLocation);
	return {
		fullPath: path + searchString + hash,
		path,
		query,
		hash: decode(hash)
	};
}
function NEW_stringifyURL(stringifyQuery, path, query, hash = "") {
	const searchText = stringifyQuery(query);
	return path + (searchText && "?") + searchText + encodeHash(hash);
}
/**
* Stringifies a URL object
*
* @param stringifyQuery
* @param location
*/
function stringifyURL(stringifyQuery, location) {
	const query = location.query ? stringifyQuery(location.query) : "";
	return location.path + (query && "?") + query + (location.hash || "");
}
/**
* Strips off the base from the beginning of a location.pathname in a non-case-sensitive way.
*
* @param pathname - location.pathname
* @param base - base to strip off
*/
function stripBase(pathname, base) {
	if (!base || !pathname.toLowerCase().startsWith(base.toLowerCase())) return pathname;
	return pathname.slice(base.length) || "/";
}
/**
* Checks if two RouteLocation are equal. This means that both locations are
* pointing towards the same {@link RouteRecord} and that all `params`, `query`
* parameters and `hash` are the same
*
* @param stringifyQuery - A function that takes a query object of type LocationQueryRaw and returns a string representation of it.
* @param a - first {@link RouteLocation}
* @param b - second {@link RouteLocation}
*/
function isSameRouteLocation(stringifyQuery, a, b) {
	const aLastIndex = a.matched.length - 1;
	const bLastIndex = b.matched.length - 1;
	return aLastIndex > -1 && aLastIndex === bLastIndex && isSameRouteRecord(a.matched[aLastIndex], b.matched[bLastIndex]) && isSameRouteLocationParams(a.params, b.params) && stringifyQuery(a.query) === stringifyQuery(b.query) && a.hash === b.hash;
}
/**
* Check if two `RouteRecords` are equal. Takes into account aliases: they are
* considered equal to the `RouteRecord` they are aliasing.
*
* @param a - first {@link RouteRecord}
* @param b - second {@link RouteRecord}
*/
function isSameRouteRecord(a, b) {
	return (a.aliasOf || a) === (b.aliasOf || b);
}
function isSameRouteLocationParams(a, b) {
	if (Object.keys(a).length !== Object.keys(b).length) return false;
	for (var key in a) if (!isSameRouteLocationParamsValue(a[key], b[key])) return false;
	return true;
}
function isSameRouteLocationParamsValue(a, b) {
	return isArray(a) ? isEquivalentArray(a, b) : isArray(b) ? isEquivalentArray(b, a) : (a && a.valueOf()) === (b && b.valueOf());
}
/**
* Check if two arrays are the same or if an array with one single entry is the
* same as another primitive value. Used to check query and parameters
*
* @param a - array of values
* @param b - array of values or a single value
*/
function isEquivalentArray(a, b) {
	return isArray(b) ? a.length === b.length && a.every((value, i) => value === b[i]) : a.length === 1 && a[0] === b;
}
/**
* Resolves a relative path that starts with `.`.
*
* @param to - path location we are resolving
* @param from - currentLocation.path, should start with `/`
*/
function resolveRelativePath(to, from) {
	if (to.startsWith("/")) return to;
	if (process.env.NODE_ENV !== "production" && !from.startsWith("/")) {
		diagnostics.VUE_ROUTER_R0070({
			to,
			from
		});
		return to;
	}
	if (!to) return from;
	const fromSegments = from.split("/");
	const toSegments = to.split("/");
	const lastToSegment = toSegments[toSegments.length - 1];
	if (lastToSegment === ".." || lastToSegment === ".") toSegments.push("");
	let position = fromSegments.length - 1;
	let toPosition;
	let segment;
	for (toPosition = 0; toPosition < toSegments.length; toPosition++) {
		segment = toSegments[toPosition];
		if (segment === ".") continue;
		if (segment === "..") {
			if (position > 1) position--;
		} else break;
	}
	return fromSegments.slice(0, position).join("/") + "/" + toSegments.slice(toPosition).join("/");
}
/**
* Initial route location where the router is. Can be used in navigation guards
* to differentiate the initial navigation.
*
* @example
* ```js
* import { START_LOCATION } from 'vue-router'
*
* router.beforeEach((to, from) => {
*   if (from === START_LOCATION) {
*     // initial navigation
*   }
* })
* ```
*/
const START_LOCATION_NORMALIZED = {
	path: "/",
	name: void 0,
	params: {},
	query: {},
	hash: "",
	fullPath: "/",
	matched: [],
	meta: {},
	redirectedFrom: void 0
};
//#endregion
//#region src/history/common.ts
/**
* Normalizes a base by removing any trailing slash and reading the base tag if
* present.
*
* @param base - base to normalize
*/
function normalizeBase(base) {
	if (!base) if (isBrowser) {
		const baseEl = document.querySelector("base");
		base = baseEl && baseEl.getAttribute("href") || "/";
		base = base.replace(/^\w+:\/\/[^/]+/, "");
	} else base = "/";
	if (base[0] !== "/" && base[0] !== "#") base = "/" + base;
	return removeTrailingSlash(base);
}
const BEFORE_HASH_RE = /^[^#]+#/;
function createHref(base, location) {
	return base.replace(BEFORE_HASH_RE, "#") + location;
}
//#endregion
//#region src/scrollBehavior.ts
function getElementPosition(el, offset) {
	const docRect = document.documentElement.getBoundingClientRect();
	const elRect = el.getBoundingClientRect();
	return {
		behavior: offset.behavior,
		left: elRect.left - docRect.left - (offset.left || 0),
		top: elRect.top - docRect.top - (offset.top || 0)
	};
}
const computeScrollPosition = () => ({
	left: window.scrollX,
	top: window.scrollY
});
function scrollToPosition(position) {
	let scrollToOptions;
	if ("el" in position) {
		const positionEl = position.el;
		const isIdSelector = typeof positionEl === "string" && positionEl.startsWith("#");
		/**
		* `id`s can accept pretty much any characters, including CSS combinators
		* like `>` or `~`. It's still possible to retrieve elements using
		* `document.getElementById('~')` but it needs to be escaped when using
		* `document.querySelector('#\\~')` for it to be valid. The only
		* requirements for `id`s are them to be unique on the page and to not be
		* empty (`id=""`). Because of that, when passing an id selector, it should
		* be properly escaped for it to work with `querySelector`. We could check
		* for the id selector to be simple (no CSS combinators `+ >~`) but that
		* would make things inconsistent since they are valid characters for an
		* `id` but would need to be escaped when using `querySelector`, breaking
		* their usage and ending up in no selector returned. Selectors need to be
		* escaped:
		*
		* - `#1-thing` becomes `#\31 -thing`
		* - `#with~symbols` becomes `#with\\~symbols`
		*
		* - More information about  the topic can be found at
		*   https://mathiasbynens.be/notes/html5-id-class.
		* - Practical example: https://mathiasbynens.be/demo/html5-id
		*/
		if (process.env.NODE_ENV !== "production" && typeof position.el === "string") {
			if (!isIdSelector || !document.getElementById(position.el.slice(1))) try {
				const foundEl = document.querySelector(position.el);
				if (isIdSelector && foundEl) {
					diagnostics.VUE_ROUTER_R0040({ el: position.el });
					return;
				}
			} catch {
				diagnostics.VUE_ROUTER_R0041({ el: position.el });
				return;
			}
		}
		const el = typeof positionEl === "string" ? isIdSelector ? document.getElementById(positionEl.slice(1)) : document.querySelector(positionEl) : positionEl;
		if (!el) {
			process.env.NODE_ENV !== "production" && diagnostics.VUE_ROUTER_R0042({ el: position.el });
			return;
		}
		scrollToOptions = getElementPosition(el, position);
	} else scrollToOptions = position;
	if ("scrollBehavior" in document.documentElement.style) window.scrollTo(scrollToOptions);
	else window.scrollTo(scrollToOptions.left != null ? scrollToOptions.left : window.scrollX, scrollToOptions.top != null ? scrollToOptions.top : window.scrollY);
}
function getScrollKey(path, delta) {
	return (history.state ? history.state.position - delta : -1) + path;
}
const scrollPositions = /* @__PURE__ */ new Map();
function saveScrollPosition(key, scrollPosition) {
	scrollPositions.set(key, scrollPosition);
}
function getSavedScrollPosition(key) {
	const scroll = scrollPositions.get(key);
	scrollPositions.delete(key);
	return scroll;
}
/**
* ScrollBehavior instance used by the router to compute and restore the scroll
* position when navigating.
*/
//#endregion
//#region src/types/typeGuards.ts
function isRouteLocation(route) {
	return typeof route === "string" || route && typeof route === "object";
}
function isRouteName(name) {
	return typeof name === "string" || typeof name === "symbol";
}
//#endregion
//#region src/query.ts
/**
* Transforms a queryString into a {@link LocationQuery} object. Accept both, a
* version with the leading `?` and without Should work as URLSearchParams

* @internal
*
* @param search - search string to parse
* @returns a query object
*/
function parseQuery(search) {
	const query = {};
	if (search === "" || search === "?") return query;
	const searchParams = (search[0] === "?" ? search.slice(1) : search).split("&");
	for (let i = 0; i < searchParams.length; ++i) {
		const searchParam = searchParams[i].replace(PLUS_RE, " ");
		const eqPos = searchParam.indexOf("=");
		const key = decode(eqPos < 0 ? searchParam : searchParam.slice(0, eqPos));
		const value = eqPos < 0 ? null : decode(searchParam.slice(eqPos + 1));
		if (key in query) {
			let currentValue = query[key];
			if (!isArray(currentValue)) currentValue = query[key] = [currentValue];
			currentValue.push(value);
		} else query[key] = value;
	}
	return query;
}
/**
* Stringifies a {@link LocationQueryRaw} object. Like `URLSearchParams`, it
* doesn't prepend a `?`
*
* @internal
*
* @param query - query object to stringify
* @returns string version of the query without the leading `?`
*/
function stringifyQuery(query) {
	let search = "";
	for (let key in query) {
		const value = query[key];
		key = encodeQueryKey(key);
		if (value == null) {
			if (value !== void 0) search += (search.length ? "&" : "") + key;
			continue;
		}
		(isArray(value) ? value.map((v) => v && encodeQueryValue(v)) : [value && encodeQueryValue(value)]).forEach((value) => {
			if (value !== void 0) {
				search += (search.length ? "&" : "") + key;
				if (value != null) search += "=" + value;
			}
		});
	}
	return search;
}
/**
* Transforms a {@link LocationQueryRaw} into a {@link LocationQuery} by casting
* numbers into strings, removing keys with an undefined value and replacing
* undefined with null in arrays
*
* @param query - query object to normalize
* @returns a normalized query object
*/
function normalizeQuery(query) {
	const normalizedQuery = {};
	for (const key in query) {
		const value = query[key];
		if (value !== void 0) normalizedQuery[key] = isArray(value) ? value.map((v) => v == null ? null : "" + v) : value == null ? value : "" + value;
	}
	return normalizedQuery;
}
//#endregion
//#region src/utils/callbacks.ts
/**
* Create a list of callbacks that can be reset. Used to create before and after navigation guards list
*/
function useCallbacks() {
	let handlers = [];
	function add(handler) {
		handlers.push(handler);
		return () => {
			const i = handlers.indexOf(handler);
			if (i > -1) handlers.splice(i, 1);
		};
	}
	function reset() {
		handlers = [];
	}
	return {
		add,
		list: () => handlers.slice(),
		reset
	};
}
//#endregion
//#region src/navigationGuards.ts
function registerGuard(activeRecordRef, name, guard) {
	const record = activeRecordRef.value;
	if (!record) {
		if (process.env.NODE_ENV !== "production") {
			const fnName = name === "updateGuards" ? "onBeforeRouteUpdate" : "onBeforeRouteLeave";
			diagnostics.VUE_ROUTER_R0020({ fn: fnName });
		}
		return;
	}
	let currentRecord = record;
	const removeFromList = () => {
		currentRecord[name].delete(guard);
	};
	onUnmounted(removeFromList);
	onDeactivated(removeFromList);
	onActivated(() => {
		const newRecord = activeRecordRef.value;
		if (process.env.NODE_ENV !== "production" && !newRecord) diagnostics.VUE_ROUTER_R0021();
		if (newRecord) currentRecord = newRecord;
		currentRecord[name].add(guard);
	});
	currentRecord[name].add(guard);
}
/**
* Add a navigation guard that triggers whenever the component for the current
* location is about to be left. Similar to {@link beforeRouteLeave} but can be
* used in any component. The guard is removed when the component is unmounted.
*
* @param leaveGuard - {@link NavigationGuard}
*/
function onBeforeRouteLeave(leaveGuard) {
	if (process.env.NODE_ENV !== "production" && !getCurrentInstance()) {
		diagnostics.VUE_ROUTER_R0022({ fn: "onBeforeRouteLeave" });
		return;
	}
	registerGuard(inject(matchedRouteKey, {}), "leaveGuards", leaveGuard);
}
/**
* Add a navigation guard that triggers whenever the current location is about
* to be updated. Similar to {@link beforeRouteUpdate} but can be used in any
* component. The guard is removed when the component is unmounted.
*
* @param updateGuard - {@link NavigationGuard}
*/
function onBeforeRouteUpdate(updateGuard) {
	if (process.env.NODE_ENV !== "production" && !getCurrentInstance()) {
		diagnostics.VUE_ROUTER_R0022({ fn: "onBeforeRouteUpdate" });
		return;
	}
	registerGuard(inject(matchedRouteKey, {}), "updateGuards", updateGuard);
}
function guardToPromiseFn(guard, to, from, record, name, runWithContext = (fn) => fn()) {
	const enterCallbackArray = record && (record.enterCallbacks[name] = record.enterCallbacks[name] || []);
	return () => new Promise((resolve, reject) => {
		const next = (valid) => {
			if (valid === false) reject(createRouterError(4, {
				from,
				to
			}));
			else if (valid instanceof Error) reject(valid);
			else if (isRouteLocation(valid)) reject(createRouterError(2, {
				from: to,
				to: valid
			}));
			else {
				if (enterCallbackArray && record.enterCallbacks[name] === enterCallbackArray && typeof valid === "function") enterCallbackArray.push(valid);
				resolve();
			}
		};
		const guardReturn = runWithContext(() => guard.call(record && record.instances[name], to, from, process.env.NODE_ENV !== "production" ? withDeprecationWarning(canOnlyBeCalledOnce(next, to, from)) : next));
		let guardCall = Promise.resolve(guardReturn);
		if (guard.length < 3) guardCall = guardCall.then(next);
		if (process.env.NODE_ENV !== "production" && guard.length > 2) {
			const guardInfo = {
				name: guard.name,
				guard: guard.toString()
			};
			if (typeof guardReturn === "object" && "then" in guardReturn) guardCall = guardCall.then((resolvedValue) => {
				if (!next._called) {
					diagnostics.VUE_ROUTER_R0023(guardInfo);
					return Promise.reject(/* @__PURE__ */ new Error("Invalid navigation guard"));
				}
				return resolvedValue;
			});
			else if (guardReturn !== void 0) {
				if (!next._called) {
					diagnostics.VUE_ROUTER_R0023(guardInfo);
					reject(/* @__PURE__ */ new Error("Invalid navigation guard"));
					return;
				}
			}
		}
		guardCall.catch((err) => reject(err));
	});
}
/**
* Wraps the next callback to warn when it is used. Dev-only: when __DEV__ is
* false (production builds), this branch is dead code and is stripped from the
* bundle.
*
* @internal
*/
function withDeprecationWarning(next) {
	let warned = false;
	return function() {
		if (!warned) {
			warned = true;
			diagnostics.VUE_ROUTER_R0025();
		}
		return next.apply(this, arguments);
	};
}
function canOnlyBeCalledOnce(next, to, from) {
	let called = 0;
	return function() {
		if (called++ === 1) diagnostics.VUE_ROUTER_R0024({
			from: from.fullPath,
			to: to.fullPath
		});
		next._called = true;
		if (called === 1) next.apply(null, arguments);
	};
}
function extractComponentsGuards(matched, guardType, to, from, runWithContext = (fn) => fn()) {
	const guards = [];
	for (const record of matched) {
		if (process.env.NODE_ENV !== "production" && !record.components && record.children && !record.children.length) diagnostics.VUE_ROUTER_R0026({ path: record.path });
		for (const name in record.components) {
			let rawComponent = record.components[name];
			if (process.env.NODE_ENV !== "production") {
				if (!rawComponent || typeof rawComponent !== "object" && typeof rawComponent !== "function") {
					diagnostics.VUE_ROUTER_R0027({
						name,
						path: record.path,
						received: String(rawComponent)
					});
					throw new Error("Invalid route component");
				} else if ("then" in rawComponent) {
					diagnostics.VUE_ROUTER_R0028({
						name,
						path: record.path
					});
					const promise = rawComponent;
					rawComponent = () => promise;
				} else if (rawComponent.__asyncLoader && !rawComponent.__warnedDefineAsync) {
					rawComponent.__warnedDefineAsync = true;
					diagnostics.VUE_ROUTER_R0029({
						name,
						path: record.path
					});
				}
			}
			if (guardType !== "beforeRouteEnter" && !record.instances[name]) continue;
			if (isRouteComponent(rawComponent)) {
				const guard = (rawComponent.__vccOpts || rawComponent)[guardType];
				guard && guards.push(guardToPromiseFn(guard, to, from, record, name, runWithContext));
			} else {
				let componentPromise = rawComponent();
				if (process.env.NODE_ENV !== "production" && !("catch" in componentPromise)) {
					diagnostics.VUE_ROUTER_R0030({
						name,
						path: record.path
					});
					componentPromise = Promise.resolve(componentPromise);
				}
				guards.push(() => componentPromise.then((resolved) => {
					if (!resolved) throw new Error(`Couldn't resolve component "${name}" at "${record.path}"`);
					const resolvedComponent = isESModule(resolved) ? resolved.default : resolved;
					record.mods[name] = resolved;
					record.components[name] = resolvedComponent;
					const guard = (resolvedComponent.__vccOpts || resolvedComponent)[guardType];
					return guard && guardToPromiseFn(guard, to, from, record, name, runWithContext)();
				}));
			}
		}
	}
	return guards;
}
/**
* Ensures a route is loaded, so it can be passed as o prop to `<RouterView>`.
*
* @param route - resolved route to load
*/
function loadRouteLocation(route) {
	return route.matched.every((record) => record.redirect) ? Promise.reject(/* @__PURE__ */ new Error("Cannot load a route that redirects.")) : Promise.all(route.matched.map((record) => record.components && Promise.all(Object.keys(record.components).reduce((promises, name) => {
		const rawComponent = record.components[name];
		if (typeof rawComponent === "function" && !("displayName" in rawComponent)) promises.push(rawComponent().then((resolved) => {
			if (!resolved) return Promise.reject(/* @__PURE__ */ new Error(`Couldn't resolve component "${name}" at "${record.path}". Ensure you passed a function that returns a promise.`));
			const resolvedComponent = isESModule(resolved) ? resolved.default : resolved;
			record.mods[name] = resolved;
			record.components[name] = resolvedComponent;
		}));
		return promises;
	}, [])))).then(() => route);
}
/**
* Split the leaving, updating, and entering records.
* @internal
*
* @param  to - Location we are navigating to
* @param from - Location we are navigating from
*/
function extractChangingRecords(to, from) {
	const leavingRecords = [];
	const updatingRecords = [];
	const enteringRecords = [];
	const len = Math.max(from.matched.length, to.matched.length);
	for (let i = 0; i < len; i++) {
		const recordFrom = from.matched[i];
		if (recordFrom) if (to.matched.find((record) => isSameRouteRecord(record, recordFrom))) updatingRecords.push(recordFrom);
		else leavingRecords.push(recordFrom);
		const recordTo = to.matched[i];
		if (recordTo) {
			if (!from.matched.find((record) => isSameRouteRecord(record, recordTo))) enteringRecords.push(recordTo);
		}
	}
	return [
		leavingRecords,
		updatingRecords,
		enteringRecords
	];
}
//#endregion
//#region src/devtools.ts
/**
* Copies a route location and removes any problematic properties that cannot be shown in devtools (e.g. Vue instances).
*
* @param routeLocation - routeLocation to format
* @param tooltip - optional tooltip
* @returns a copy of the routeLocation
*/
function formatRouteLocation(routeLocation, tooltip) {
	const copy = assign({}, routeLocation, { matched: routeLocation.matched.map((matched) => omit(matched, [
		"instances",
		"children",
		"aliasOf"
	])) });
	return { _custom: {
		type: null,
		readOnly: true,
		display: routeLocation.fullPath,
		tooltip,
		value: copy
	} };
}
function formatDisplay(display) {
	return { _custom: { display } };
}
let routerId = 0;
function addDevtools(app, router, matcher) {
	if (router.__hasDevtools) return;
	router.__hasDevtools = true;
	const id = routerId++;
	setupDevtoolsPlugin({
		id: "org.vuejs.router" + (id ? "." + id : ""),
		label: "Vue Router",
		packageName: "vue-router",
		homepage: "https://router.vuejs.org",
		logo: "https://router.vuejs.org/logo.png",
		componentStateTypes: ["Routing"],
		app
	}, (api) => {
		api.on.inspectComponent((payload) => {
			if (payload.instanceData) payload.instanceData.state.push({
				type: "Routing",
				key: "$route",
				editable: false,
				value: formatRouteLocation(router.currentRoute.value, "Current Route")
			});
		});
		api.on.visitComponentTree(({ treeNode: node, componentInstance }) => {
			if (componentInstance.__vrv_devtools) {
				const info = componentInstance.__vrv_devtools;
				node.tags.push({
					label: (info.name ? `${info.name.toString()}: ` : "") + info.path,
					textColor: 0,
					tooltip: "This component is rendered by &lt;router-view&gt;",
					backgroundColor: PINK_500
				});
			}
			if (isArray(componentInstance.__vrl_devtools)) {
				componentInstance.__devtoolsApi = api;
				componentInstance.__vrl_devtools.forEach((devtoolsData) => {
					let label = devtoolsData.route.path;
					let backgroundColor = ORANGE_400;
					let tooltip = "";
					let textColor = 0;
					if (devtoolsData.error) {
						label = devtoolsData.error;
						backgroundColor = RED_100;
						textColor = RED_700;
					} else if (devtoolsData.isExactActive) {
						backgroundColor = LIME_500;
						tooltip = "This is exactly active";
					} else if (devtoolsData.isActive) {
						backgroundColor = BLUE_600;
						tooltip = "This link is active";
					}
					node.tags.push({
						label,
						textColor,
						tooltip,
						backgroundColor
					});
				});
			}
		});
		watch(router.currentRoute, () => {
			refreshRoutesView();
			api.notifyComponentUpdate();
			api.sendInspectorTree(routerInspectorId);
			api.sendInspectorState(routerInspectorId);
		});
		const navigationsLayerId = "router:navigations:" + id;
		api.addTimelineLayer({
			id: navigationsLayerId,
			label: `Router${id ? " " + id : ""} Navigations`,
			color: 4237508
		});
		router.onError((error, to) => {
			api.addTimelineEvent({
				layerId: navigationsLayerId,
				event: {
					title: "Error during Navigation",
					subtitle: to.fullPath,
					logType: "error",
					time: api.now(),
					data: { error },
					groupId: to.meta.__navigationId
				}
			});
		});
		let navigationId = 0;
		router.beforeEach((to, from) => {
			const data = {
				guard: formatDisplay("beforeEach"),
				from: formatRouteLocation(from, "Current Location during this navigation"),
				to: formatRouteLocation(to, "Target location")
			};
			Object.defineProperty(to.meta, "__navigationId", { value: navigationId++ });
			api.addTimelineEvent({
				layerId: navigationsLayerId,
				event: {
					time: api.now(),
					title: "Start of navigation",
					subtitle: to.fullPath,
					data,
					groupId: to.meta.__navigationId
				}
			});
		});
		router.afterEach((to, from, failure) => {
			const data = { guard: formatDisplay("afterEach") };
			if (failure) {
				data.failure = { _custom: {
					type: Error,
					readOnly: true,
					display: failure ? failure.message : "",
					tooltip: "Navigation Failure",
					value: failure
				} };
				data.status = formatDisplay("❌");
			} else data.status = formatDisplay("✅");
			data.from = formatRouteLocation(from, "Current Location during this navigation");
			data.to = formatRouteLocation(to, "Target location");
			api.addTimelineEvent({
				layerId: navigationsLayerId,
				event: {
					title: "End of navigation",
					subtitle: to.fullPath,
					time: api.now(),
					data,
					logType: failure ? "warning" : "default",
					groupId: to.meta.__navigationId
				}
			});
		});
		/**
		* Inspector of Existing routes
		*/
		const routerInspectorId = "router-inspector:" + id;
		api.addInspector({
			id: routerInspectorId,
			label: "Routes" + (id ? " " + id : ""),
			icon: "book",
			treeFilterPlaceholder: "Search routes"
		});
		function refreshRoutesView() {
			if (!activeRoutesPayload) return;
			const payload = activeRoutesPayload;
			let routes = matcher.getRoutes().filter((route) => !route.parent || !route.parent.record.components);
			routes.forEach(resetMatchStateOnRouteRecord);
			if (payload.filter) routes = routes.filter((route) => isRouteMatching(route, payload.filter.toLowerCase()));
			routes.forEach((route) => markRouteRecordActive(route, router.currentRoute.value));
			payload.rootNodes = routes.map(formatRouteRecordForInspector);
		}
		let activeRoutesPayload;
		api.on.getInspectorTree((payload) => {
			activeRoutesPayload = payload;
			if (payload.app === app && payload.inspectorId === routerInspectorId) refreshRoutesView();
		});
		/**
		* Display information about the currently selected route record
		*/
		api.on.getInspectorState((payload) => {
			if (payload.app === app && payload.inspectorId === routerInspectorId) {
				const route = matcher.getRoutes().find((route) => route.record.__vd_id === payload.nodeId);
				if (route) payload.state = { options: formatRouteRecordMatcherForStateInspector(route) };
			}
		});
		api.sendInspectorTree(routerInspectorId);
		api.sendInspectorState(routerInspectorId);
	});
}
function modifierForKey(key) {
	if (key.optional) return key.repeatable ? "*" : "?";
	else return key.repeatable ? "+" : "";
}
function formatRouteRecordMatcherForStateInspector(route) {
	const { record } = route;
	const fields = [{
		editable: false,
		key: "path",
		value: record.path
	}];
	if (record.name != null) fields.push({
		editable: false,
		key: "name",
		value: record.name
	});
	fields.push({
		editable: false,
		key: "regexp",
		value: route.re
	});
	if (route.keys.length) fields.push({
		editable: false,
		key: "keys",
		value: { _custom: {
			type: null,
			readOnly: true,
			display: route.keys.map((key) => `${key.name}${modifierForKey(key)}`).join(" "),
			tooltip: "Param keys",
			value: route.keys
		} }
	});
	if (record.redirect != null) fields.push({
		editable: false,
		key: "redirect",
		value: record.redirect
	});
	if (route.alias.length) fields.push({
		editable: false,
		key: "aliases",
		value: route.alias.map((alias) => alias.record.path)
	});
	if (Object.keys(route.record.meta).length) fields.push({
		editable: false,
		key: "meta",
		value: route.record.meta
	});
	fields.push({
		key: "score",
		editable: false,
		value: { _custom: {
			type: null,
			readOnly: true,
			display: route.score.map((score) => score.join(", ")).join(" | "),
			tooltip: "Score used to sort routes",
			value: route.score
		} }
	});
	return fields;
}
/**
* Extracted from tailwind palette
*/
const PINK_500 = 15485081;
const BLUE_600 = 2450411;
const LIME_500 = 8702998;
const CYAN_400 = 2282478;
const ORANGE_400 = 16486972;
const DARK = 6710886;
const RED_100 = 16704226;
const RED_700 = 12131356;
function formatRouteRecordForInspector(route) {
	const tags = [];
	const { record } = route;
	if (record.name != null) tags.push({
		label: String(record.name),
		textColor: 0,
		backgroundColor: CYAN_400
	});
	if (record.aliasOf) tags.push({
		label: "alias",
		textColor: 0,
		backgroundColor: ORANGE_400
	});
	if (route.__vd_match) tags.push({
		label: "matches",
		textColor: 0,
		backgroundColor: PINK_500
	});
	if (route.__vd_exactActive) tags.push({
		label: "exact",
		textColor: 0,
		backgroundColor: LIME_500
	});
	if (route.__vd_active) tags.push({
		label: "active",
		textColor: 0,
		backgroundColor: BLUE_600
	});
	if (record.redirect) tags.push({
		label: typeof record.redirect === "string" ? `redirect: ${record.redirect}` : "redirects",
		textColor: 16777215,
		backgroundColor: DARK
	});
	let id = record.__vd_id;
	if (id == null) {
		id = String(routeRecordId++);
		record.__vd_id = id;
	}
	return {
		id,
		label: record.path,
		tags,
		children: route.children.map(formatRouteRecordForInspector)
	};
}
let routeRecordId = 0;
const EXTRACT_REGEXP_RE = /^\/(.*)\/([a-z]*)$/;
function markRouteRecordActive(route, currentRoute) {
	const isExactActive = currentRoute.matched.length && isSameRouteRecord(currentRoute.matched[currentRoute.matched.length - 1], route.record);
	route.__vd_exactActive = route.__vd_active = isExactActive;
	if (!isExactActive) route.__vd_active = currentRoute.matched.some((match) => isSameRouteRecord(match, route.record));
	route.children.forEach((childRoute) => markRouteRecordActive(childRoute, currentRoute));
}
function resetMatchStateOnRouteRecord(route) {
	route.__vd_match = false;
	route.children.forEach(resetMatchStateOnRouteRecord);
}
function isRouteMatching(route, filter) {
	const found = String(route.re).match(EXTRACT_REGEXP_RE);
	route.__vd_match = false;
	if (!found || found.length < 3) return false;
	if (new RegExp(found[1].replace(/\$$/, ""), found[2]).test(filter)) {
		route.children.forEach((child) => isRouteMatching(child, filter));
		if (route.record.path !== "/" || filter === "/") {
			route.__vd_match = route.re.test(filter);
			return true;
		}
		return false;
	}
	const path = route.record.path.toLowerCase();
	const decodedPath = decode(path);
	if (!filter.startsWith("/") && (decodedPath.includes(filter) || path.includes(filter))) return true;
	if (decodedPath.startsWith(filter) || path.startsWith(filter)) return true;
	if (route.record.name && String(route.record.name).includes(filter)) return true;
	return route.children.some((child) => isRouteMatching(child, filter));
}
function omit(obj, keys) {
	const ret = {};
	for (const key in obj) if (!keys.includes(key)) ret[key] = obj[key];
	return ret;
}
//#endregion
export { PLUS_RE as A, isSameRouteLocation as C, resolveRelativePath as D, parseURL as E, isBrowser as F, encodeHash as M, encodeParam as N, stringifyURL as O, encodePath as P, START_LOCATION_NORMALIZED as S, isSameRouteRecord as T, saveScrollPosition as _, loadRouteLocation as a, normalizeBase as b, useCallbacks as c, stringifyQuery as d, isRouteLocation as f, getScrollKey as g, getSavedScrollPosition as h, guardToPromiseFn as i, decode as j, stripBase as k, normalizeQuery as l, computeScrollPosition as m, extractChangingRecords as n, onBeforeRouteLeave as o, isRouteName as p, extractComponentsGuards as r, onBeforeRouteUpdate as s, addDevtools as t, parseQuery as u, scrollToPosition as v, isSameRouteLocationParams as w, NEW_stringifyURL as x, createHref as y };
