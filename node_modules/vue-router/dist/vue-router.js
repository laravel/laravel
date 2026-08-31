/*!
* vue-router v5.2.0
* (c) 2026 Eduardo San Martin Morote
* @license MIT
*/
import { C as isSameRouteLocation, E as parseURL, F as isBrowser, M as encodeHash, N as encodeParam, O as stringifyURL, S as START_LOCATION_NORMALIZED, T as isSameRouteRecord, _ as saveScrollPosition, a as loadRouteLocation, b as normalizeBase, c as useCallbacks, d as stringifyQuery, f as isRouteLocation, g as getScrollKey, h as getSavedScrollPosition, i as guardToPromiseFn, j as decode, k as stripBase, l as normalizeQuery, m as computeScrollPosition, n as extractChangingRecords, o as onBeforeRouteLeave, p as isRouteName, r as extractComponentsGuards, s as onBeforeRouteUpdate, t as addDevtools, u as parseQuery, v as scrollToPosition, w as isSameRouteLocationParams, y as createHref } from "./devtools-Bpr7ZAVB.js";
import { a as routerKey, c as diagnostics, d as isNavigationFailure, f as applyToParams, h as isArray, i as routeLocationKey, l as NavigationFailureType, n as useRouter, o as routerViewLocationKey, p as assign, r as matchedRouteKey, s as viewDepthKey, t as useRoute, u as createRouterError, v as mergeOptions, y as noop } from "./useApi-CROJJdhE.js";
import { computed, defineComponent, getCurrentInstance, h, inject, nextTick, provide, reactive, ref, shallowReactive, shallowRef, unref, watch, watchEffect } from "vue";
//#region src/history/html5.ts
let createBaseLocation = () => location.protocol + "//" + location.host;
/**
* Creates a normalized history location from a window.location object
* @param base - The base path
* @param location - The window.location object
*/
function createCurrentLocation(base, location) {
	const { pathname, search, hash } = location;
	const hashPos = base.indexOf("#");
	if (hashPos > -1) {
		let slicePos = hash.includes(base.slice(hashPos)) ? base.slice(hashPos).length : 1;
		let pathFromHash = hash.slice(slicePos);
		if (pathFromHash[0] !== "/") pathFromHash = "/" + pathFromHash;
		return stripBase(pathFromHash, "");
	}
	return stripBase(pathname, base) + search + hash;
}
function useHistoryListeners(base, historyState, currentLocation, replace) {
	let listeners = [];
	let teardowns = [];
	let pauseState = null;
	const popStateHandler = ({ state }) => {
		const to = createCurrentLocation(base, location);
		const from = currentLocation.value;
		const fromState = historyState.value;
		let delta = 0;
		if (state) {
			currentLocation.value = to;
			historyState.value = state;
			if (pauseState && pauseState === from) {
				pauseState = null;
				return;
			}
			delta = fromState ? state.position - fromState.position : 0;
		} else replace(to);
		listeners.forEach((listener) => {
			listener(currentLocation.value, from, {
				delta,
				type: "pop",
				direction: delta ? delta > 0 ? "forward" : "back" : ""
			});
		});
	};
	function pauseListeners() {
		pauseState = currentLocation.value;
	}
	function listen(callback) {
		listeners.push(callback);
		const teardown = () => {
			const index = listeners.indexOf(callback);
			if (index > -1) listeners.splice(index, 1);
		};
		teardowns.push(teardown);
		return teardown;
	}
	function beforeUnloadListener() {
		if (document.visibilityState === "hidden") {
			const { history } = window;
			if (!history.state) return;
			history.replaceState(assign({}, history.state, { scroll: computeScrollPosition() }), "");
		}
	}
	function destroy() {
		for (const teardown of teardowns) teardown();
		teardowns = [];
		window.removeEventListener("popstate", popStateHandler);
		window.removeEventListener("pagehide", beforeUnloadListener);
		document.removeEventListener("visibilitychange", beforeUnloadListener);
	}
	window.addEventListener("popstate", popStateHandler);
	window.addEventListener("pagehide", beforeUnloadListener);
	document.addEventListener("visibilitychange", beforeUnloadListener);
	return {
		pauseListeners,
		listen,
		destroy
	};
}
/**
* Creates a state object
*/
function buildState(back, current, forward, replaced = false, computeScroll = false) {
	return {
		back,
		current,
		forward,
		replaced,
		position: window.history.length,
		scroll: computeScroll ? computeScrollPosition() : null
	};
}
function useHistoryStateNavigation(base) {
	const { history, location } = window;
	const currentLocation = { value: createCurrentLocation(base, location) };
	const historyState = { value: history.state };
	if (!historyState.value) changeLocation(currentLocation.value, {
		back: null,
		current: currentLocation.value,
		forward: null,
		position: history.length - 1,
		replaced: true,
		scroll: null
	}, true);
	function changeLocation(to, state, replace) {
		/**
		* if a base tag is provided, and we are on a normal domain, we have to
		* respect the provided `base` attribute because pushState() will use it and
		* potentially erase anything before the `#` like at
		* https://github.com/vuejs/router/issues/685 where a base of
		* `/folder/#` but a base of `/` would erase the `/folder/` section. If
		* there is no host, the `<base>` tag makes no sense and if there isn't a
		* base tag we can just use everything after the `#`.
		*/
		const hashIndex = base.indexOf("#");
		const url = hashIndex > -1 ? (location.host && document.querySelector("base") ? base : base.slice(hashIndex)) + to : createBaseLocation() + base + to;
		try {
			history[replace ? "replaceState" : "pushState"](state, "", url);
			historyState.value = state;
		} catch (err) {
			if (process.env.NODE_ENV !== "production") diagnostics.VUE_ROUTER_R0120({ cause: err });
			else console.error(err);
			location[replace ? "replace" : "assign"](url);
		}
	}
	function replace(to, data) {
		changeLocation(to, assign({}, history.state, buildState(historyState.value.back, to, historyState.value.forward, true), data, { position: historyState.value.position }), true);
		currentLocation.value = to;
	}
	function push(to, data) {
		const currentState = assign({}, historyState.value, history.state, {
			forward: to,
			scroll: computeScrollPosition()
		});
		if (process.env.NODE_ENV !== "production" && !history.state) diagnostics.VUE_ROUTER_R0121();
		changeLocation(currentState.current, currentState, true);
		changeLocation(to, assign({}, buildState(currentLocation.value, to, null), { position: currentState.position + 1 }, data), false);
		currentLocation.value = to;
	}
	return {
		location: currentLocation,
		state: historyState,
		push,
		replace
	};
}
/**
* Creates an HTML5 history. Most common history for single page applications.
*
* @param base -
*/
function createWebHistory(base) {
	base = normalizeBase(base);
	const historyNavigation = useHistoryStateNavigation(base);
	const historyListeners = useHistoryListeners(base, historyNavigation.state, historyNavigation.location, historyNavigation.replace);
	function go(delta, triggerListeners = true) {
		if (!triggerListeners) historyListeners.pauseListeners();
		history.go(delta);
	}
	const routerHistory = assign({
		location: "",
		base,
		go,
		createHref: createHref.bind(null, base)
	}, historyNavigation, historyListeners);
	Object.defineProperty(routerHistory, "location", {
		enumerable: true,
		get: () => historyNavigation.location.value
	});
	Object.defineProperty(routerHistory, "state", {
		enumerable: true,
		get: () => historyNavigation.state.value
	});
	return routerHistory;
}
//#endregion
//#region src/history/hash.ts
/**
* Creates a hash history. Useful for web applications with no host (e.g. `file://`) or when configuring a server to
* handle any URL is not possible.
*
* @param base - optional base to provide. Defaults to `location.pathname + location.search` If there is a `<base>` tag
* in the `head`, its value will be ignored in favor of this parameter **but note it affects all the history.pushState()
* calls**, meaning that if you use a `<base>` tag, it's `href` value **has to match this parameter** (ignoring anything
* after the `#`).
*
* @example
* ```js
* // at https://example.com/folder
* createWebHashHistory() // gives a url of `https://example.com/folder#`
* createWebHashHistory('/folder/') // gives a url of `https://example.com/folder/#`
* // if the `#` is provided in the base, it won't be added by `createWebHashHistory`
* createWebHashHistory('/folder/#/app/') // gives a url of `https://example.com/folder/#/app/`
* // you should avoid doing this because it changes the original url and breaks copying urls
* createWebHashHistory('/other-folder/') // gives a url of `https://example.com/other-folder/#`
*
* // at file:///usr/etc/folder/index.html
* // for locations with no `host`, the base is ignored
* createWebHashHistory('/iAmIgnored') // gives a url of `file:///usr/etc/folder/index.html#`
* ```
*/
function createWebHashHistory(base) {
	base = location.host ? base || location.pathname + location.search : "";
	if (!base.includes("#")) base += "#";
	if (process.env.NODE_ENV !== "production" && !base.endsWith("#/") && !base.endsWith("#")) diagnostics.VUE_ROUTER_R0110({
		base,
		suggestion: base.replace(/#.*$/, "#")
	});
	return createWebHistory(base);
}
//#endregion
//#region src/history/memory.ts
/**
* Creates an in-memory based history. The main purpose of this history is to handle SSR. It starts in a special location that is nowhere.
* It's up to the user to replace that location with the starter location by either calling `router.push` or `router.replace`.
*
* @param base - Base applied to all urls, defaults to '/'
* @returns a history object that can be passed to the router constructor
*/
function createMemoryHistory(base = "") {
	let listeners = [];
	let queue = [["", {}]];
	let position = 0;
	base = normalizeBase(base);
	function setLocation(location, state = {}) {
		position++;
		if (position !== queue.length) queue.splice(position);
		queue.push([location, state]);
	}
	function triggerListeners(to, from, { direction, delta }) {
		const info = {
			direction,
			delta,
			type: "pop"
		};
		for (const callback of listeners) callback(to, from, info);
	}
	const routerHistory = {
		location: "",
		state: {},
		base,
		createHref: createHref.bind(null, base),
		replace(to, state) {
			queue.splice(position--, 1);
			setLocation(to, state);
		},
		push(to, state) {
			setLocation(to, state);
		},
		listen(callback) {
			listeners.push(callback);
			return () => {
				const index = listeners.indexOf(callback);
				if (index > -1) listeners.splice(index, 1);
			};
		},
		destroy() {
			listeners = [];
			queue = [["", {}]];
			position = 0;
		},
		go(delta, shouldTrigger = true) {
			const from = this.location;
			const direction = delta < 0 ? "back" : "forward";
			position = Math.max(0, Math.min(position + delta, queue.length - 1));
			if (shouldTrigger) triggerListeners(this.location, from, {
				direction,
				delta
			});
		}
	};
	Object.defineProperty(routerHistory, "location", {
		enumerable: true,
		get: () => queue[position][0]
	});
	Object.defineProperty(routerHistory, "state", {
		enumerable: true,
		get: () => queue[position][1]
	});
	return routerHistory;
}
//#endregion
//#region src/matcher/pathTokenizer.ts
const ROOT_TOKEN = {
	type: 0,
	value: ""
};
const VALID_PARAM_RE = /[a-zA-Z0-9_]/;
function tokenizePath(path) {
	if (!path) return [[]];
	if (path === "/") return [[ROOT_TOKEN]];
	if (!path.startsWith("/")) throw new Error(process.env.NODE_ENV !== "production" ? `Route paths should start with a "/": "${path}" should be "/${path}".` : `Invalid path "${path}"`);
	function crash(message) {
		throw new Error(`ERR (${state})/"${buffer}": ${message}`);
	}
	let state = 0;
	let previousState = state;
	const tokens = [];
	let segment;
	function finalizeSegment() {
		if (segment) tokens.push(segment);
		segment = [];
	}
	let i = 0;
	let char;
	let buffer = "";
	let customRe = "";
	function consumeBuffer() {
		if (!buffer) return;
		if (state === 0) segment.push({
			type: 0,
			value: buffer
		});
		else if (state === 1 || state === 2 || state === 3) {
			if (segment.length > 1 && (char === "*" || char === "+")) crash(`A repeatable param (${buffer}) must be alone in its segment. eg: '/:ids+.`);
			segment.push({
				type: 1,
				value: buffer,
				regexp: customRe,
				repeatable: char === "*" || char === "+",
				optional: char === "*" || char === "?"
			});
		} else crash("Invalid state to consume buffer");
		buffer = "";
	}
	function addCharToBuffer() {
		buffer += char;
	}
	while (i < path.length) {
		char = path[i++];
		switch (state) {
			case 0:
				if (char === "\\") {
					previousState = state;
					state = 4;
				} else if (char === "/") {
					if (buffer) consumeBuffer();
					finalizeSegment();
				} else if (char === ":") {
					consumeBuffer();
					state = 1;
				} else addCharToBuffer();
				break;
			case 4:
				addCharToBuffer();
				state = previousState;
				break;
			case 1:
				if (char === "(") state = 2;
				else if (VALID_PARAM_RE.test(char)) addCharToBuffer();
				else {
					consumeBuffer();
					state = 0;
					if (char !== "*" && char !== "?" && char !== "+") i--;
				}
				break;
			case 2:
				if (char === ")") if (customRe[customRe.length - 1] == "\\") customRe = customRe.slice(0, -1) + char;
				else state = 3;
				else customRe += char;
				break;
			case 3:
				consumeBuffer();
				state = 0;
				if (char !== "*" && char !== "?" && char !== "+") i--;
				customRe = "";
				break;
			default:
				crash("Unknown state");
				break;
		}
	}
	if (state === 2) crash(`Unfinished custom RegExp for param "${buffer}"`);
	consumeBuffer();
	finalizeSegment();
	return tokens;
}
//#endregion
//#region src/matcher/pathParserRanker.ts
const BASE_PARAM_PATTERN = "[^/]+?";
const BASE_PATH_PARSER_OPTIONS = {
	sensitive: false,
	strict: false,
	start: true,
	end: true
};
const REGEX_CHARS_RE = /[.+*?^${}()[\]/\\]/g;
/**
* Creates a path parser from an array of Segments (a segment is an array of Tokens)
*
* @param segments - array of segments returned by tokenizePath
* @param extraOptions - optional options for the regexp
* @returns a PathParser
*/
function tokensToParser(segments, extraOptions) {
	const options = assign({}, BASE_PATH_PARSER_OPTIONS, extraOptions);
	const score = [];
	let pattern = options.start ? "^" : "";
	const keys = [];
	for (const segment of segments) {
		const segmentScores = segment.length ? [] : [90];
		if (options.strict && !segment.length) pattern += "/";
		for (let tokenIndex = 0; tokenIndex < segment.length; tokenIndex++) {
			const token = segment[tokenIndex];
			let subSegmentScore = 40 + (options.sensitive ? .25 : 0);
			if (token.type === 0) {
				if (!tokenIndex) pattern += "/";
				pattern += token.value.replace(REGEX_CHARS_RE, "\\$&");
				subSegmentScore += 40;
			} else if (token.type === 1) {
				const { value, repeatable, optional, regexp } = token;
				keys.push({
					name: value,
					repeatable,
					optional
				});
				const re = regexp ? regexp : BASE_PARAM_PATTERN;
				if (re !== BASE_PARAM_PATTERN) {
					subSegmentScore += 10;
					try {
						new RegExp(`(${re})`);
					} catch (err) {
						throw new Error(`Invalid custom RegExp for param "${value}" (${re}): ` + err.message);
					}
				}
				let subPattern = repeatable ? `((?:${re})(?:/(?:${re}))*)` : `(${re})`;
				if (!tokenIndex) subPattern = optional && segment.length < 2 ? `(?:/${subPattern})` : "/" + subPattern;
				if (optional) subPattern += "?";
				pattern += subPattern;
				subSegmentScore += 20;
				if (optional) subSegmentScore += -8;
				if (repeatable) subSegmentScore += -20;
				if (re === ".*") subSegmentScore += -50;
			}
			segmentScores.push(subSegmentScore);
		}
		score.push(segmentScores);
	}
	if (options.strict && options.end) {
		const i = score.length - 1;
		score[i][score[i].length - 1] += .7000000000000001;
	}
	if (!options.strict) pattern += "/?";
	if (options.end) pattern += "$";
	else if (options.strict && !pattern.endsWith("/")) pattern += "(?:/|$)";
	const re = new RegExp(pattern, options.sensitive ? "" : "i");
	function parse(path) {
		const match = path.match(re);
		const params = {};
		if (!match) return null;
		for (let i = 1; i < match.length; i++) {
			const value = match[i] || "";
			const key = keys[i - 1];
			params[key.name] = value && key.repeatable ? value.split("/") : value;
		}
		return params;
	}
	function stringify(params) {
		let path = "";
		let avoidDuplicatedSlash = false;
		for (const segment of segments) {
			if (!avoidDuplicatedSlash || !path.endsWith("/")) path += "/";
			avoidDuplicatedSlash = false;
			for (const token of segment) if (token.type === 0) path += token.value;
			else if (token.type === 1) {
				const { value, repeatable, optional } = token;
				const param = value in params ? params[value] : "";
				if (isArray(param) && !repeatable) throw new Error(`Provided param "${value}" is an array but it is not repeatable (* or + modifiers)`);
				const text = isArray(param) ? param.join("/") : param;
				if (!text) if (optional) {
					if (segment.length < 2) if (path.endsWith("/")) path = path.slice(0, -1);
					else avoidDuplicatedSlash = true;
				} else throw new Error(`Missing required param "${value}"`);
				path += text;
			}
		}
		return path || "/";
	}
	return {
		re,
		score,
		keys,
		parse,
		stringify
	};
}
/**
* Compares an array of numbers as used in PathParser.score and returns a
* number. This function can be used to `sort` an array
*
* @param a - first array of numbers
* @param b - second array of numbers
* @returns 0 if both are equal, < 0 if a should be sorted first, > 0 if b
* should be sorted first
*/
function compareScoreArray(a, b) {
	let i = 0;
	while (i < a.length && i < b.length) {
		const diff = b[i] - a[i];
		if (diff) return diff;
		i++;
	}
	if (a.length < b.length) return a.length === 1 && a[0] === 80 ? -1 : 1;
	else if (a.length > b.length) return b.length === 1 && b[0] === 80 ? 1 : -1;
	return 0;
}
/**
* Compare function that can be used with `sort` to sort an array of PathParser
*
* @param a - first PathParser
* @param b - second PathParser
* @returns 0 if both are equal, < 0 if a should be sorted first, > 0 if b
*/
function comparePathParserScore(a, b) {
	let i = 0;
	const aScore = a.score;
	const bScore = b.score;
	while (i < aScore.length && i < bScore.length) {
		const comp = compareScoreArray(aScore[i], bScore[i]);
		if (comp) return comp;
		i++;
	}
	if (Math.abs(bScore.length - aScore.length) === 1) {
		if (isLastScoreNegative(aScore)) return 1;
		if (isLastScoreNegative(bScore)) return -1;
	}
	return bScore.length - aScore.length;
}
/**
* This allows detecting splats at the end of a path: /home/:id(.*)*
*
* @param score - score to check
* @returns true if the last entry is negative
*/
function isLastScoreNegative(score) {
	const last = score[score.length - 1];
	return score.length > 0 && last[last.length - 1] < 0;
}
const PATH_PARSER_OPTIONS_DEFAULTS = {
	strict: false,
	end: true,
	sensitive: false
};
//#endregion
//#region src/matcher/pathMatcher.ts
function createRouteRecordMatcher(record, parent, options) {
	const parser = tokensToParser(tokenizePath(record.path), options);
	if (process.env.NODE_ENV !== "production") {
		const existingKeys = /* @__PURE__ */ new Set();
		for (const key of parser.keys) {
			if (existingKeys.has(key.name)) diagnostics.VUE_ROUTER_R0090({
				name: key.name,
				path: record.path
			});
			existingKeys.add(key.name);
		}
	}
	const matcher = assign(parser, {
		record,
		parent,
		children: [],
		alias: []
	});
	if (parent) {
		if (!matcher.record.aliasOf === !parent.record.aliasOf) parent.children.push(matcher);
	}
	return matcher;
}
//#endregion
//#region src/matcher/index.ts
/**
* Creates a Router Matcher.
*
* @internal
* @param routes - array of initial routes
* @param globalOptions - global route options
*/
function createRouterMatcher(routes, globalOptions) {
	const matchers = [];
	const matcherMap = /* @__PURE__ */ new Map();
	globalOptions = mergeOptions(PATH_PARSER_OPTIONS_DEFAULTS, globalOptions);
	function getRecordMatcher(name) {
		return matcherMap.get(name);
	}
	function addRoute(record, parent, originalRecord) {
		const isRootAdd = !originalRecord;
		const mainNormalizedRecord = normalizeRouteRecord(record);
		if (process.env.NODE_ENV !== "production") checkChildMissingNameWithEmptyPath(mainNormalizedRecord, parent);
		mainNormalizedRecord.aliasOf = originalRecord && originalRecord.record;
		const options = mergeOptions(globalOptions, record);
		const normalizedRecords = [mainNormalizedRecord];
		if ("alias" in record) {
			const aliases = typeof record.alias === "string" ? [record.alias] : record.alias;
			for (const alias of aliases) normalizedRecords.push(normalizeRouteRecord(assign({}, mainNormalizedRecord, {
				components: originalRecord ? originalRecord.record.components : mainNormalizedRecord.components,
				path: alias,
				aliasOf: originalRecord ? originalRecord.record : mainNormalizedRecord
			})));
		}
		let matcher;
		let originalMatcher;
		for (const normalizedRecord of normalizedRecords) {
			const { path } = normalizedRecord;
			if (parent && path[0] !== "/") {
				const parentPath = parent.record.path;
				const connectingSlash = parentPath[parentPath.length - 1] === "/" ? "" : "/";
				normalizedRecord.path = parent.record.path + (path && connectingSlash + path);
			}
			if (process.env.NODE_ENV !== "production" && normalizedRecord.path === "*") throw new Error("Catch all routes (\"*\") must now be defined using a param with a custom regexp.\nSee more at https://router.vuejs.org/guide/migration/#Removed-star-or-catch-all-routes.");
			matcher = createRouteRecordMatcher(normalizedRecord, parent, options);
			if (process.env.NODE_ENV !== "production" && parent && path[0] === "/") checkMissingParamsInAbsolutePath(matcher, parent);
			if (originalRecord) {
				originalRecord.alias.push(matcher);
				if (process.env.NODE_ENV !== "production") checkSameParams(originalRecord, matcher);
			} else {
				originalMatcher = originalMatcher || matcher;
				if (originalMatcher !== matcher) originalMatcher.alias.push(matcher);
				if (isRootAdd && record.name && !isAliasRecord(matcher)) {
					if (process.env.NODE_ENV !== "production") checkSameNameAsAncestor(record, parent);
					removeRoute(record.name);
				}
			}
			if (isMatchable(matcher)) insertMatcher(matcher);
			if (mainNormalizedRecord.children) {
				const children = mainNormalizedRecord.children;
				for (let i = 0; i < children.length; i++) addRoute(children[i], matcher, originalRecord && originalRecord.children[i]);
			}
			originalRecord = originalRecord || matcher;
		}
		return originalMatcher ? () => {
			removeRoute(originalMatcher);
		} : noop;
	}
	function removeRoute(matcherRef) {
		if (isRouteName(matcherRef)) {
			const matcher = matcherMap.get(matcherRef);
			if (matcher) {
				matcherMap.delete(matcherRef);
				matchers.splice(matchers.indexOf(matcher), 1);
				matcher.children.forEach(removeRoute);
				matcher.alias.forEach(removeRoute);
			}
		} else {
			const index = matchers.indexOf(matcherRef);
			if (index > -1) {
				matchers.splice(index, 1);
				if (matcherRef.record.name) matcherMap.delete(matcherRef.record.name);
				matcherRef.children.forEach(removeRoute);
				matcherRef.alias.forEach(removeRoute);
			}
		}
	}
	function getRoutes() {
		return matchers;
	}
	function insertMatcher(matcher) {
		const index = findInsertionIndex(matcher, matchers);
		matchers.splice(index, 0, matcher);
		if (matcher.record.name && !isAliasRecord(matcher)) matcherMap.set(matcher.record.name, matcher);
	}
	function resolve(location, currentLocation) {
		let matcher;
		let params = {};
		let path;
		let name;
		if ("name" in location && location.name) {
			matcher = matcherMap.get(location.name);
			if (!matcher) throw createRouterError(1, { location });
			if (process.env.NODE_ENV !== "production") {
				const invalidParams = Object.keys(location.params || {}).filter((paramName) => !matcher.keys.find((k) => k.name === paramName));
				if (invalidParams.length) {
					const isInherited = !matcher.keys.length && invalidParams.some((name) => name in currentLocation.params);
					diagnostics.VUE_ROUTER_R0100({
						params: invalidParams.join("\", \""),
						inherited: isInherited ? ` If you are using a catch-all route with a named redirect, pass an empty \`params\` object: \`redirect: { name: '...', params: {} }\`.` : ""
					});
				}
			}
			name = matcher.record.name;
			params = assign(pickParams(currentLocation.params, matcher.keys.filter((k) => !k.optional).concat(matcher.parent ? matcher.parent.keys.filter((k) => k.optional) : []).map((k) => k.name)), location.params && pickParams(location.params, matcher.keys.map((k) => k.name)));
			path = matcher.stringify(params);
		} else if (location.path != null) {
			path = location.path;
			if (process.env.NODE_ENV !== "production" && !path.startsWith("/")) diagnostics.VUE_ROUTER_R0101({ path });
			matcher = matchers.find((m) => m.re.test(path));
			if (matcher) {
				params = matcher.parse(path);
				name = matcher.record.name;
				matcher.keys.forEach((key) => {
					if (key.optional && !params[key.name]) delete params[key.name];
				});
			}
		} else {
			matcher = currentLocation.name ? matcherMap.get(currentLocation.name) : matchers.find((m) => m.re.test(currentLocation.path));
			if (!matcher) throw createRouterError(1, {
				location,
				currentLocation
			});
			name = matcher.record.name;
			params = assign({}, currentLocation.params, location.params);
			path = matcher.stringify(params);
		}
		const matched = [];
		let parentMatcher = matcher;
		while (parentMatcher) {
			matched.unshift(parentMatcher.record);
			parentMatcher = parentMatcher.parent;
		}
		return {
			name,
			path,
			params,
			matched,
			meta: mergeMetaFields(matched)
		};
	}
	routes.forEach((route) => addRoute(route));
	function clearRoutes() {
		matchers.length = 0;
		matcherMap.clear();
	}
	return {
		addRoute,
		resolve,
		removeRoute,
		clearRoutes,
		getRoutes,
		getRecordMatcher
	};
}
/**
* Picks an object param to contain only specified keys.
*
* @param params - params object to pick from
* @param keys - keys to pick
*/
function pickParams(params, keys) {
	const newParams = {};
	for (const key of keys) if (key in params) newParams[key] = params[key];
	return newParams;
}
/**
* Normalizes a RouteRecordRaw. Creates a copy
*
* @param record
* @returns the normalized version
*/
function normalizeRouteRecord(record) {
	const normalized = {
		path: record.path,
		redirect: record.redirect,
		name: record.name,
		meta: record.meta || {},
		aliasOf: record.aliasOf,
		beforeEnter: record.beforeEnter,
		props: normalizeRecordProps(record),
		children: record.children || [],
		instances: {},
		leaveGuards: /* @__PURE__ */ new Set(),
		updateGuards: /* @__PURE__ */ new Set(),
		enterCallbacks: {},
		components: "components" in record ? record.components || null : record.component && { default: record.component }
	};
	Object.defineProperty(normalized, "mods", { value: {} });
	return normalized;
}
/**
* Normalize the optional `props` in a record to always be an object similar to
* components. Also accept a boolean for components.
* @param record
*/
function normalizeRecordProps(record) {
	const propsObject = {};
	const props = record.props || false;
	if ("component" in record) propsObject.default = props;
	else for (const name in record.components) propsObject[name] = typeof props === "object" ? props[name] : props;
	return propsObject;
}
/**
* Checks if a record or any of its parent is an alias
* @param record
*/
function isAliasRecord(record) {
	while (record) {
		if (record.record.aliasOf) return true;
		record = record.parent;
	}
	return false;
}
/**
* Merge meta fields of an array of records
*
* @param matched - array of matched records
*/
function mergeMetaFields(matched) {
	return matched.reduce((meta, record) => assign(meta, record.meta), {});
}
function isSameParam(a, b) {
	return a.name === b.name && a.optional === b.optional && a.repeatable === b.repeatable;
}
/**
* Check if a path and its alias have the same required params
*
* @param a - original record
* @param b - alias record
*/
function checkSameParams(a, b) {
	for (const key of a.keys) if (!key.optional && !b.keys.find(isSameParam.bind(null, key))) {
		diagnostics.VUE_ROUTER_R0102({
			alias: b.record.path,
			original: a.record.path,
			name: key.name
		});
		return;
	}
	for (const key of b.keys) if (!key.optional && !a.keys.find(isSameParam.bind(null, key))) {
		diagnostics.VUE_ROUTER_R0102({
			alias: b.record.path,
			original: a.record.path,
			name: key.name
		});
		return;
	}
}
/**
* A route with a name and a child with an empty path without a name should warn when adding the route
*
* @param mainNormalizedRecord - RouteRecordNormalized
* @param parent - RouteRecordMatcher
*/
function checkChildMissingNameWithEmptyPath(mainNormalizedRecord, parent) {
	if (parent && parent.record.name && !mainNormalizedRecord.name && !mainNormalizedRecord.path && mainNormalizedRecord.children.length === 0) diagnostics.VUE_ROUTER_R0103({ name: String(parent.record.name) });
}
function checkSameNameAsAncestor(record, parent) {
	for (let ancestor = parent; ancestor; ancestor = ancestor.parent) if (ancestor.record.name === record.name) throw new Error(`A route named "${String(record.name)}" has been added as a ${parent === ancestor ? "child" : "descendant"} of a route with the same name. Route names must be unique and a nested route cannot use the same name as an ancestor.`);
}
function checkMissingParamsInAbsolutePath(record, parent) {
	for (const key of parent.keys) if (!record.keys.find(isSameParam.bind(null, key))) {
		diagnostics.VUE_ROUTER_R0104({
			path: record.record.path,
			name: key.name,
			parent: parent.record.path
		});
		return;
	}
}
/**
* Performs a binary search to find the correct insertion index for a new matcher.
*
* Matchers are primarily sorted by their score. If scores are tied then we also consider parent/child relationships,
* with descendants coming before ancestors. If there's still a tie, new routes are inserted after existing routes.
*
* @param matcher - new matcher to be inserted
* @param matchers - existing matchers
*/
function findInsertionIndex(matcher, matchers) {
	let lower = 0;
	let upper = matchers.length;
	while (lower !== upper) {
		const mid = lower + upper >> 1;
		if (comparePathParserScore(matcher, matchers[mid]) < 0) upper = mid;
		else lower = mid + 1;
	}
	const insertionAncestor = getInsertionAncestor(matcher);
	if (insertionAncestor) {
		upper = matchers.lastIndexOf(insertionAncestor, upper - 1);
		if (process.env.NODE_ENV !== "production" && upper < 0) diagnostics.VUE_ROUTER_R0105({
			ancestor: insertionAncestor.record.path,
			record: matcher.record.path
		});
	}
	return upper;
}
function getInsertionAncestor(matcher) {
	let ancestor = matcher;
	while (ancestor = ancestor.parent) if (isMatchable(ancestor) && comparePathParserScore(matcher, ancestor) === 0) return ancestor;
}
/**
* Checks if a matcher can be reachable. This means if it's possible to reach it as a route. For example, routes without
* a component, or name, or redirect, are just used to group other routes.
* @param matcher
* @param matcher.record record of the matcher
* @returns
*/
function isMatchable({ record }) {
	return !!(record.name || record.components && Object.keys(record.components).length || record.redirect);
}
//#endregion
//#region src/RouterLink.ts
/**
* Returns the internal behavior of a {@link RouterLink} without the rendering part.
*
* @param props - a `to` location and an optional `replace` flag
*/
function useLink(props) {
	const router = inject(routerKey);
	const currentRoute = inject(routeLocationKey);
	let hasPrevious = false;
	let previousTo = null;
	const route = computed(() => {
		const to = unref(props.to);
		if (process.env.NODE_ENV !== "production" && (!hasPrevious || to !== previousTo)) {
			if (!isRouteLocation(to)) diagnostics.VUE_ROUTER_R0050({ to });
			previousTo = to;
			hasPrevious = true;
		}
		return router.resolve(to);
	});
	const activeRecordIndex = computed(() => {
		const { matched } = route.value;
		const { length } = matched;
		const routeMatched = matched[length - 1];
		const currentMatched = currentRoute.matched;
		if (!routeMatched || !currentMatched.length) return -1;
		const index = currentMatched.findIndex(isSameRouteRecord.bind(null, routeMatched));
		if (index > -1) return index;
		const parentRecordPath = getOriginalPath(matched[length - 2]);
		return length > 1 && getOriginalPath(routeMatched) === parentRecordPath && currentMatched[currentMatched.length - 1].path !== parentRecordPath ? currentMatched.findIndex(isSameRouteRecord.bind(null, matched[length - 2])) : index;
	});
	const isActive = computed(() => activeRecordIndex.value > -1 && includesParams(currentRoute.params, route.value.params));
	const isExactActive = computed(() => activeRecordIndex.value > -1 && activeRecordIndex.value === currentRoute.matched.length - 1 && isSameRouteLocationParams(currentRoute.params, route.value.params));
	function navigate(e = {}) {
		if (guardEvent(e)) {
			const p = router[unref(props.replace) ? "replace" : "push"](unref(props.to)).catch(noop);
			if (props.viewTransition && typeof document !== "undefined" && "startViewTransition" in document) document.startViewTransition(() => p);
			return p;
		}
		return Promise.resolve();
	}
	if ((process.env.NODE_ENV !== "production" || __VUE_PROD_DEVTOOLS__) && isBrowser) {
		const instance = getCurrentInstance();
		if (instance) {
			const linkContextDevtools = {
				route: route.value,
				isActive: isActive.value,
				isExactActive: isExactActive.value,
				error: null
			};
			instance.__vrl_devtools = instance.__vrl_devtools || [];
			instance.__vrl_devtools.push(linkContextDevtools);
			watchEffect(() => {
				linkContextDevtools.route = route.value;
				linkContextDevtools.isActive = isActive.value;
				linkContextDevtools.isExactActive = isExactActive.value;
				linkContextDevtools.error = isRouteLocation(unref(props.to)) ? null : "Invalid \"to\" value";
			}, { flush: "post" });
		}
	}
	/**
	* NOTE: update {@link _RouterLinkI}'s `$slots` type when updating this
	*/
	return {
		route,
		href: computed(() => route.value.href),
		isActive,
		isExactActive,
		navigate
	};
}
function preferSingleVNode(vnodes) {
	return vnodes.length === 1 ? vnodes[0] : vnodes;
}
/**
* Component to render a link that triggers a navigation on click.
*/
const RouterLink = /* @__PURE__ */ defineComponent({
	name: "RouterLink",
	compatConfig: { MODE: 3 },
	props: {
		to: {
			type: [String, Object],
			required: true
		},
		replace: Boolean,
		activeClass: String,
		exactActiveClass: String,
		custom: Boolean,
		ariaCurrentValue: {
			type: String,
			default: "page"
		},
		viewTransition: Boolean
	},
	useLink,
	setup(props, { slots }) {
		const link = reactive(useLink(props));
		const { options } = inject(routerKey);
		const elClass = computed(() => ({
			[getLinkClass(props.activeClass, options.linkActiveClass, "router-link-active")]: link.isActive,
			[getLinkClass(props.exactActiveClass, options.linkExactActiveClass, "router-link-exact-active")]: link.isExactActive
		}));
		return () => {
			const children = slots.default && preferSingleVNode(slots.default(link));
			return props.custom ? children : h("a", {
				"aria-current": link.isExactActive ? props.ariaCurrentValue : null,
				href: link.href,
				onClick: link.navigate,
				class: elClass.value
			}, children);
		};
	}
});
function guardEvent(e) {
	if (e.metaKey || e.altKey || e.ctrlKey || e.shiftKey) return;
	if (e.defaultPrevented) return;
	if (e.button !== void 0 && e.button !== 0) return;
	if (e.currentTarget && e.currentTarget.getAttribute) {
		const target = e.currentTarget.getAttribute("target");
		if (/\b_blank\b/i.test(target)) return;
	}
	if (e.preventDefault) e.preventDefault();
	return true;
}
function includesParams(outer, inner) {
	for (const key in inner) {
		const innerValue = inner[key];
		const outerValue = outer[key];
		if (typeof innerValue === "string") {
			if (innerValue !== outerValue) return false;
		} else if (!isArray(outerValue) || outerValue.length !== innerValue.length || innerValue.some((value, i) => value.valueOf() !== outerValue[i].valueOf())) return false;
	}
	return true;
}
/**
* Get the original path value of a record by following its aliasOf
* @param record
*/
function getOriginalPath(record) {
	return record ? record.aliasOf ? record.aliasOf.path : record.path : "";
}
/**
* Utility class to get the active class based on defaults.
* @param propClass
* @param globalClass
* @param defaultClass
*/
const getLinkClass = (propClass, globalClass, defaultClass) => propClass != null ? propClass : globalClass != null ? globalClass : defaultClass;
//#endregion
//#region src/RouterView.ts
const RouterViewImpl = /*#__PURE__*/ defineComponent({
	name: "RouterView",
	inheritAttrs: false,
	props: {
		name: {
			type: String,
			default: "default"
		},
		route: Object
	},
	compatConfig: { MODE: 3 },
	setup(props, { attrs, slots }) {
		process.env.NODE_ENV !== "production" && warnDeprecatedUsage();
		const injectedRoute = inject(routerViewLocationKey);
		const routeToDisplay = computed(() => props.route || injectedRoute.value);
		const injectedDepth = inject(viewDepthKey, 0);
		const depth = computed(() => {
			let initialDepth = unref(injectedDepth);
			const { matched } = routeToDisplay.value;
			let matchedRoute;
			while ((matchedRoute = matched[initialDepth]) && !matchedRoute.components) initialDepth++;
			return initialDepth;
		});
		const matchedRouteRef = computed(() => routeToDisplay.value.matched[depth.value]);
		provide(viewDepthKey, computed(() => depth.value + 1));
		provide(matchedRouteKey, matchedRouteRef);
		provide(routerViewLocationKey, routeToDisplay);
		const viewRef = ref();
		watch(() => [
			viewRef.value,
			matchedRouteRef.value,
			props.name
		], ([instance, to, name], [oldInstance, from, _oldName]) => {
			if (to) {
				to.instances[name] = instance;
				if (from && from !== to && instance && instance === oldInstance) {
					if (!to.leaveGuards.size) to.leaveGuards = from.leaveGuards;
					if (!to.updateGuards.size) to.updateGuards = from.updateGuards;
				}
			}
			if (instance && to && (!from || !isSameRouteRecord(to, from) || !oldInstance)) (to.enterCallbacks[name] || []).forEach((callback) => callback(instance));
		}, { flush: "post" });
		return () => {
			const route = routeToDisplay.value;
			const currentName = props.name;
			const matchedRoute = matchedRouteRef.value;
			const ViewComponent = matchedRoute && matchedRoute.components[currentName];
			if (!ViewComponent) return normalizeSlot(slots.default, {
				Component: ViewComponent,
				route
			});
			const routePropsOption = matchedRoute.props[currentName];
			const routeProps = routePropsOption ? routePropsOption === true ? route.params : typeof routePropsOption === "function" ? routePropsOption(route) : routePropsOption : null;
			const onVnodeUnmounted = (vnode) => {
				if (vnode.component.isUnmounted) matchedRoute.instances[currentName] = null;
			};
			const component = h(ViewComponent, assign({}, routeProps, attrs, {
				onVnodeUnmounted,
				ref: viewRef
			}));
			if ((process.env.NODE_ENV !== "production" || __VUE_PROD_DEVTOOLS__) && isBrowser && component.ref) {
				const info = {
					depth: depth.value,
					name: matchedRoute.name,
					path: matchedRoute.path,
					meta: matchedRoute.meta
				};
				(isArray(component.ref) ? component.ref.map((r) => r.i) : [component.ref.i]).forEach((instance) => {
					instance.__vrv_devtools = info;
				});
			}
			return normalizeSlot(slots.default, {
				Component: component,
				route
			}) || component;
		};
	}
});
function normalizeSlot(slot, data) {
	if (!slot) return null;
	const slotContent = slot(data);
	return slotContent.length === 1 ? slotContent[0] : slotContent;
}
/**
* Component to display the current route the user is at.
*/
const RouterView = RouterViewImpl;
function warnDeprecatedUsage() {
	const instance = getCurrentInstance();
	const parentName = instance.parent && instance.parent.type.name;
	const parentSubTreeType = instance.parent && instance.parent.subTree && instance.parent.subTree.type;
	if (parentName && (parentName === "KeepAlive" || parentName.includes("Transition")) && typeof parentSubTreeType === "object" && parentSubTreeType.name === "RouterView") {
		const comp = parentName === "KeepAlive" ? "keep-alive" : "transition";
		diagnostics.VUE_ROUTER_R0060({ comp });
	}
}
//#endregion
//#region src/router.ts
/**
* Creates a Router instance that can be used by a Vue app.
*
* @param options - {@link RouterOptions}
*/
function createRouter(options) {
	const matcher = createRouterMatcher(options.routes, options);
	const parseQuery$1 = options.parseQuery || parseQuery;
	const stringifyQuery$1 = options.stringifyQuery || stringifyQuery;
	const routerHistory = options.history;
	if (process.env.NODE_ENV !== "production" && !routerHistory) throw new Error("Provide the \"history\" option when calling \"createRouter()\": https://router.vuejs.org/api/interfaces/RouterOptions.html#history");
	const beforeGuards = useCallbacks();
	const beforeResolveGuards = useCallbacks();
	const afterGuards = useCallbacks();
	const currentRoute = shallowRef(START_LOCATION_NORMALIZED);
	let pendingLocation = START_LOCATION_NORMALIZED;
	if (isBrowser && options.scrollBehavior && "scrollRestoration" in history) history.scrollRestoration = "manual";
	const normalizeParams = applyToParams.bind(null, (paramValue) => "" + paramValue);
	const encodeParams = applyToParams.bind(null, encodeParam);
	const decodeParams = applyToParams.bind(null, decode);
	function addRoute(parentOrRoute, route) {
		let parent;
		let record;
		if (isRouteName(parentOrRoute)) {
			parent = matcher.getRecordMatcher(parentOrRoute);
			if (process.env.NODE_ENV !== "production" && !parent) diagnostics.VUE_ROUTER_R0001({ name: String(parentOrRoute) });
			record = route;
		} else record = parentOrRoute;
		return matcher.addRoute(record, parent);
	}
	function removeRoute(name) {
		const recordMatcher = matcher.getRecordMatcher(name);
		if (recordMatcher) matcher.removeRoute(recordMatcher);
		else if (process.env.NODE_ENV !== "production") diagnostics.VUE_ROUTER_R0002({ name: String(name) });
	}
	function getRoutes() {
		return matcher.getRoutes().map((routeMatcher) => routeMatcher.record);
	}
	function hasRoute(name) {
		return !!matcher.getRecordMatcher(name);
	}
	function resolve(rawLocation, currentLocation) {
		currentLocation = assign({}, currentLocation || currentRoute.value);
		if (typeof rawLocation === "string") {
			const locationNormalized = parseURL(parseQuery$1, rawLocation, currentLocation.path);
			const matchedRoute = matcher.resolve({ path: locationNormalized.path }, currentLocation);
			const href = routerHistory.createHref(locationNormalized.fullPath);
			if (process.env.NODE_ENV !== "production") {
				if (href.startsWith("//")) diagnostics.VUE_ROUTER_R0003({
					location: rawLocation,
					href
				});
				else if (!matchedRoute.matched.length) diagnostics.VUE_ROUTER_R0004({ path: rawLocation });
			}
			return assign(locationNormalized, matchedRoute, {
				params: decodeParams(matchedRoute.params),
				redirectedFrom: void 0,
				href
			});
		}
		if (process.env.NODE_ENV !== "production" && !isRouteLocation(rawLocation)) {
			diagnostics.VUE_ROUTER_R0005({ rawLocation });
			return resolve({});
		}
		let matcherLocation;
		if (rawLocation.path != null) {
			if (process.env.NODE_ENV !== "production" && "params" in rawLocation && !("name" in rawLocation) && Object.keys(rawLocation.params).length) diagnostics.VUE_ROUTER_R0006({ path: rawLocation.path });
			matcherLocation = assign({}, rawLocation, { path: parseURL(parseQuery$1, rawLocation.path, currentLocation.path).path });
		} else {
			const targetParams = assign({}, rawLocation.params);
			for (const key in targetParams) if (targetParams[key] == null) delete targetParams[key];
			matcherLocation = assign({}, rawLocation, { params: encodeParams(targetParams) });
			currentLocation.params = encodeParams(currentLocation.params);
		}
		const matchedRoute = matcher.resolve(matcherLocation, currentLocation);
		const hash = rawLocation.hash || "";
		if (process.env.NODE_ENV !== "production" && hash && !hash.startsWith("#")) diagnostics.VUE_ROUTER_R0007({ hash });
		matchedRoute.params = normalizeParams(decodeParams(matchedRoute.params));
		const fullPath = stringifyURL(stringifyQuery$1, assign({}, rawLocation, {
			hash: encodeHash(hash),
			path: matchedRoute.path
		}));
		const href = routerHistory.createHref(fullPath);
		if (process.env.NODE_ENV !== "production") {
			if (href.startsWith("//")) diagnostics.VUE_ROUTER_R0003({
				location: rawLocation,
				href
			});
			else if (!matchedRoute.matched.length) diagnostics.VUE_ROUTER_R0004({ path: rawLocation.path != null ? rawLocation.path : rawLocation });
		}
		return assign({
			fullPath,
			hash,
			query: stringifyQuery$1 === stringifyQuery ? normalizeQuery(rawLocation.query) : rawLocation.query || {}
		}, matchedRoute, {
			redirectedFrom: void 0,
			href
		});
	}
	function locationAsObject(to) {
		return typeof to === "string" ? parseURL(parseQuery$1, to, currentRoute.value.path) : assign({}, to);
	}
	function checkCanceledNavigation(to, from) {
		if (pendingLocation !== to) return createRouterError(8, {
			from,
			to
		});
	}
	function push(to) {
		return pushWithRedirect(to);
	}
	function replace(to) {
		return push(assign(locationAsObject(to), { replace: true }));
	}
	function handleRedirectRecord(to, from) {
		const lastMatched = to.matched[to.matched.length - 1];
		if (lastMatched && lastMatched.redirect) {
			const { redirect } = lastMatched;
			let newTargetLocation = typeof redirect === "function" ? redirect(to, from) : redirect;
			if (typeof newTargetLocation === "string") {
				newTargetLocation = newTargetLocation.includes("?") || newTargetLocation.includes("#") ? newTargetLocation = locationAsObject(newTargetLocation) : { path: newTargetLocation };
				newTargetLocation.params = {};
			}
			if (process.env.NODE_ENV !== "production" && newTargetLocation.path == null && !("name" in newTargetLocation)) {
				diagnostics.VUE_ROUTER_R0008({
					target: JSON.stringify(newTargetLocation, null, 2),
					to: to.fullPath
				});
				throw new Error("Invalid redirect");
			}
			return assign({
				query: to.query,
				hash: to.hash,
				params: newTargetLocation.path != null ? {} : to.params
			}, newTargetLocation);
		}
	}
	function pushWithRedirect(to, redirectedFrom) {
		const targetLocation = pendingLocation = resolve(to);
		const from = currentRoute.value;
		const data = to.state;
		const force = to.force;
		const replace = to.replace === true;
		const shouldRedirect = handleRedirectRecord(targetLocation, from);
		if (shouldRedirect) return pushWithRedirect(assign(locationAsObject(shouldRedirect), {
			state: typeof shouldRedirect === "object" ? assign({}, data, shouldRedirect.state) : data,
			force,
			replace
		}), redirectedFrom || targetLocation);
		const toLocation = targetLocation;
		toLocation.redirectedFrom = redirectedFrom;
		let failure;
		if (!force && isSameRouteLocation(stringifyQuery$1, from, targetLocation)) {
			failure = createRouterError(16, {
				to: toLocation,
				from
			});
			handleScroll(from, from, true, false);
		}
		return (failure ? Promise.resolve(failure) : navigate(toLocation, from)).catch((error) => isNavigationFailure(error) ? isNavigationFailure(error, 2) ? error : markAsReady(error) : triggerError(error, toLocation, from)).then((failure) => {
			if (failure) {
				if (isNavigationFailure(failure, 2)) {
					if (process.env.NODE_ENV !== "production" && isSameRouteLocation(stringifyQuery$1, resolve(failure.to), toLocation) && redirectedFrom && (redirectedFrom._count = redirectedFrom._count ? redirectedFrom._count + 1 : 1) > 30) {
						diagnostics.VUE_ROUTER_R0009({
							from: from.fullPath,
							to: toLocation.fullPath
						});
						return Promise.reject(/* @__PURE__ */ new Error("Infinite redirect in navigation guard"));
					}
					return pushWithRedirect(assign({ replace }, locationAsObject(failure.to), {
						state: typeof failure.to === "object" ? assign({}, data, failure.to.state) : data,
						force
					}), redirectedFrom || toLocation);
				}
			} else failure = finalizeNavigation(toLocation, from, true, replace, data);
			triggerAfterEach(toLocation, from, failure);
			return failure;
		});
	}
	/**
	* Helper to reject and skip all navigation guards if a new navigation happened
	* @param to
	* @param from
	*/
	function checkCanceledNavigationAndReject(to, from) {
		const error = checkCanceledNavigation(to, from);
		return error ? Promise.reject(error) : Promise.resolve();
	}
	function runWithContext(fn) {
		const app = installedApps.values().next().value;
		return app && typeof app.runWithContext === "function" ? app.runWithContext(fn) : fn();
	}
	function navigate(to, from) {
		let guards;
		const [leavingRecords, updatingRecords, enteringRecords] = extractChangingRecords(to, from);
		guards = extractComponentsGuards(leavingRecords.reverse(), "beforeRouteLeave", to, from);
		for (const record of leavingRecords) record.leaveGuards.forEach((guard) => {
			guards.push(guardToPromiseFn(guard, to, from));
		});
		const canceledNavigationCheck = checkCanceledNavigationAndReject.bind(null, to, from);
		guards.push(canceledNavigationCheck);
		return runGuardQueue(guards).then(() => {
			guards = [];
			for (const guard of beforeGuards.list()) guards.push(guardToPromiseFn(guard, to, from));
			guards.push(canceledNavigationCheck);
			return runGuardQueue(guards);
		}).then(() => {
			guards = extractComponentsGuards(updatingRecords, "beforeRouteUpdate", to, from);
			for (const record of updatingRecords) record.updateGuards.forEach((guard) => {
				guards.push(guardToPromiseFn(guard, to, from));
			});
			guards.push(canceledNavigationCheck);
			return runGuardQueue(guards);
		}).then(() => {
			guards = [];
			for (const record of enteringRecords) if (record.beforeEnter) if (isArray(record.beforeEnter)) for (const beforeEnter of record.beforeEnter) guards.push(guardToPromiseFn(beforeEnter, to, from));
			else guards.push(guardToPromiseFn(record.beforeEnter, to, from));
			guards.push(canceledNavigationCheck);
			return runGuardQueue(guards);
		}).then(() => {
			to.matched.forEach((record) => record.enterCallbacks = {});
			guards = extractComponentsGuards(enteringRecords, "beforeRouteEnter", to, from, runWithContext);
			guards.push(canceledNavigationCheck);
			return runGuardQueue(guards);
		}).then(() => {
			guards = [];
			for (const guard of beforeResolveGuards.list()) guards.push(guardToPromiseFn(guard, to, from));
			guards.push(canceledNavigationCheck);
			return runGuardQueue(guards);
		}).catch((err) => isNavigationFailure(err, 8) ? err : Promise.reject(err));
	}
	function triggerAfterEach(to, from, failure) {
		afterGuards.list().forEach((guard) => runWithContext(() => guard(to, from, failure)));
	}
	/**
	* - Cleans up any navigation guards
	* - Changes the url if necessary
	* - Calls the scrollBehavior
	*/
	function finalizeNavigation(toLocation, from, isPush, replace, data) {
		const error = checkCanceledNavigation(toLocation, from);
		if (error) return error;
		const isFirstNavigation = from === START_LOCATION_NORMALIZED;
		const state = !isBrowser ? {} : history.state;
		if (isPush) if (replace || isFirstNavigation) routerHistory.replace(toLocation.fullPath, assign({ scroll: isFirstNavigation && state && state.scroll }, data));
		else routerHistory.push(toLocation.fullPath, data);
		currentRoute.value = toLocation;
		handleScroll(toLocation, from, isPush, isFirstNavigation);
		markAsReady();
	}
	let removeHistoryListener;
	function setupListeners() {
		if (removeHistoryListener) return;
		removeHistoryListener = routerHistory.listen((to, _from, info) => {
			if (!router.listening) return;
			const toLocation = resolve(to);
			const shouldRedirect = handleRedirectRecord(toLocation, router.currentRoute.value);
			if (shouldRedirect) {
				pushWithRedirect(assign(shouldRedirect, {
					replace: true,
					force: true
				}), toLocation).catch(noop);
				return;
			}
			pendingLocation = toLocation;
			const from = currentRoute.value;
			if (isBrowser) saveScrollPosition(getScrollKey(from.fullPath, info.delta), computeScrollPosition());
			navigate(toLocation, from).catch((error) => {
				if (isNavigationFailure(error, 12)) return error;
				if (isNavigationFailure(error, 2)) {
					pushWithRedirect(assign(locationAsObject(error.to), { force: true }), toLocation).then((failure) => {
						if (isNavigationFailure(failure, 20) && !info.delta && info.type === "pop") routerHistory.go(-1, false);
					}).catch(noop);
					return Promise.reject();
				}
				if (info.delta) routerHistory.go(-info.delta, false);
				return triggerError(error, toLocation, from);
			}).then((failure) => {
				failure = failure || finalizeNavigation(toLocation, from, false);
				if (failure) {
					if (info.delta && !isNavigationFailure(failure, 8)) routerHistory.go(-info.delta, false);
					else if (info.type === "pop" && isNavigationFailure(failure, 20)) routerHistory.go(-1, false);
				}
				triggerAfterEach(toLocation, from, failure);
			}).catch(noop);
		});
	}
	let readyHandlers = useCallbacks();
	let errorListeners = useCallbacks();
	let ready;
	/**
	* Trigger errorListeners added via onError and throws the error as well
	*
	* @param error - error to throw
	* @param to - location we were navigating to when the error happened
	* @param from - location we were navigating from when the error happened
	* @returns the error as a rejected promise
	*/
	function triggerError(error, to, from) {
		markAsReady(error);
		const list = errorListeners.list();
		if (list.length) list.forEach((handler) => handler(error, to, from));
		else {
			if (process.env.NODE_ENV !== "production") diagnostics.VUE_ROUTER_R0010();
			console.error(error);
		}
		return Promise.reject(error);
	}
	function isReady() {
		if (ready && currentRoute.value !== START_LOCATION_NORMALIZED) return Promise.resolve();
		return new Promise((resolve, reject) => {
			readyHandlers.add([resolve, reject]);
		});
	}
	function markAsReady(err) {
		if (!ready) {
			ready = !err;
			setupListeners();
			readyHandlers.list().forEach(([resolve, reject]) => err ? reject(err) : resolve());
			readyHandlers.reset();
		}
		return err;
	}
	function handleScroll(to, from, isPush, isFirstNavigation) {
		const { scrollBehavior } = options;
		if (!isBrowser || !scrollBehavior) return Promise.resolve();
		const scrollPosition = !isPush && getSavedScrollPosition(getScrollKey(to.fullPath, 0)) || (isFirstNavigation || !isPush) && history.state && history.state.scroll || null;
		return nextTick().then(() => scrollBehavior(to, from, scrollPosition)).then((position) => to === currentRoute.value && position && scrollToPosition(position)).catch((err) => to === currentRoute.value && triggerError(err, to, from));
	}
	const go = (delta) => routerHistory.go(delta);
	let started;
	const installedApps = /* @__PURE__ */ new Set();
	const router = {
		currentRoute,
		listening: true,
		addRoute,
		removeRoute,
		clearRoutes: matcher.clearRoutes,
		hasRoute,
		getRoutes,
		resolve,
		options,
		push,
		replace,
		go,
		back: () => go(-1),
		forward: () => go(1),
		beforeEach: beforeGuards.add,
		beforeResolve: beforeResolveGuards.add,
		afterEach: afterGuards.add,
		onError: errorListeners.add,
		isReady,
		install(app) {
			app.component("RouterLink", RouterLink);
			app.component("RouterView", RouterView);
			app.config.globalProperties.$router = router;
			Object.defineProperty(app.config.globalProperties, "$route", {
				enumerable: true,
				get: () => unref(currentRoute)
			});
			if (isBrowser && !started && currentRoute.value === START_LOCATION_NORMALIZED) {
				started = true;
				push(routerHistory.location).catch((err) => {
					if (process.env.NODE_ENV !== "production") diagnostics.VUE_ROUTER_R0011({ cause: err });
				});
			}
			const reactiveRoute = {};
			for (const key in START_LOCATION_NORMALIZED) Object.defineProperty(reactiveRoute, key, {
				get: () => currentRoute.value[key],
				enumerable: true
			});
			app.provide(routerKey, router);
			app.provide(routeLocationKey, shallowReactive(reactiveRoute));
			app.provide(routerViewLocationKey, currentRoute);
			const unmountApp = app.unmount;
			installedApps.add(app);
			app.unmount = function() {
				installedApps.delete(app);
				if (installedApps.size < 1) {
					pendingLocation = START_LOCATION_NORMALIZED;
					removeHistoryListener && removeHistoryListener();
					removeHistoryListener = null;
					currentRoute.value = START_LOCATION_NORMALIZED;
					started = false;
					ready = false;
				}
				unmountApp();
			};
			if ((process.env.NODE_ENV !== "production" || __VUE_PROD_DEVTOOLS__) && isBrowser && true) addDevtools(app, router, matcher);
		}
	};
	function runGuardQueue(guards) {
		return guards.reduce((promise, guard) => promise.then(() => runWithContext(guard)), Promise.resolve());
	}
	return router;
}
//#endregion
export { NavigationFailureType, RouterLink, RouterView, START_LOCATION_NORMALIZED as START_LOCATION, createMemoryHistory, createRouter, createRouterMatcher, createWebHashHistory, createWebHistory, isNavigationFailure, loadRouteLocation, matchedRouteKey, onBeforeRouteLeave, onBeforeRouteUpdate, parseQuery, routeLocationKey, routerKey, routerViewLocationKey, stringifyQuery, useLink, useRoute, useRouter, viewDepthKey };
