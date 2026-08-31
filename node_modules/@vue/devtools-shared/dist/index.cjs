Object.defineProperty(exports, Symbol.toStringTag, { value: "Module" });
//#region \0rolldown/runtime.js
var __create = Object.create;
var __defProp = Object.defineProperty;
var __getOwnPropDesc = Object.getOwnPropertyDescriptor;
var __getOwnPropNames = Object.getOwnPropertyNames;
var __getProtoOf = Object.getPrototypeOf;
var __hasOwnProp = Object.prototype.hasOwnProperty;
var __commonJSMin = (cb, mod) => () => (mod || cb((mod = { exports: {} }).exports, mod), mod.exports);
var __copyProps = (to, from, except, desc) => {
	if (from && typeof from === "object" || typeof from === "function") for (var keys = __getOwnPropNames(from), i = 0, n = keys.length, key; i < n; i++) {
		key = keys[i];
		if (!__hasOwnProp.call(to, key) && key !== except) __defProp(to, key, {
			get: ((k) => from[k]).bind(null, key),
			enumerable: !(desc = __getOwnPropDesc(from, key)) || desc.enumerable
		});
	}
	return to;
};
var __toESM = (mod, isNodeMode, target) => (target = mod != null ? __create(__getProtoOf(mod)) : {}, __copyProps(isNodeMode || !mod || !mod.__esModule ? __defProp(target, "default", {
	value: mod,
	enumerable: true
}) : target, mod));
//#endregion
//#region src/constants.ts
const VIEW_MODE_STORAGE_KEY = "__vue-devtools-view-mode__";
const VITE_PLUGIN_DETECTED_STORAGE_KEY = "__vue-devtools-vite-plugin-detected__";
const VITE_PLUGIN_CLIENT_URL_STORAGE_KEY = "__vue-devtools-vite-plugin-client-url__";
const BROADCAST_CHANNEL_NAME = "__vue-devtools-broadcast-channel__";
//#endregion
//#region src/env.ts
const isBrowser = typeof navigator !== "undefined";
const target = typeof window !== "undefined" ? window : typeof globalThis !== "undefined" ? globalThis : typeof global !== "undefined" ? global : {};
const isInChromePanel = typeof target.chrome !== "undefined" && !!target.chrome.devtools;
const isInIframe = isBrowser && target.self !== target.top;
const isInElectron = typeof navigator !== "undefined" && navigator.userAgent?.toLowerCase().includes("electron");
const isNuxtApp = typeof window !== "undefined" && !!window.__NUXT__;
const isInSeparateWindow = !isInIframe && !isInChromePanel && !isInElectron;
//#endregion
//#region src/general.ts
var import_rfdc = /* @__PURE__ */ __toESM((/* @__PURE__ */ __commonJSMin(((exports, module) => {
	module.exports = rfdc;
	function copyBuffer(cur) {
		if (cur instanceof Buffer) return Buffer.from(cur);
		return new cur.constructor(cur.buffer.slice(), cur.byteOffset, cur.length);
	}
	function rfdc(opts) {
		opts = opts || {};
		if (opts.circles) return rfdcCircles(opts);
		const constructorHandlers = /* @__PURE__ */ new Map();
		constructorHandlers.set(Date, (o) => new Date(o));
		constructorHandlers.set(Map, (o, fn) => new Map(cloneArray(Array.from(o), fn)));
		constructorHandlers.set(Set, (o, fn) => new Set(cloneArray(Array.from(o), fn)));
		if (opts.constructorHandlers) for (const handler of opts.constructorHandlers) constructorHandlers.set(handler[0], handler[1]);
		let handler = null;
		return opts.proto ? cloneProto : clone;
		function cloneArray(a, fn) {
			const keys = Object.keys(a);
			const a2 = new Array(keys.length);
			for (let i = 0; i < keys.length; i++) {
				const k = keys[i];
				const cur = a[k];
				if (typeof cur !== "object" || cur === null) a2[k] = cur;
				else if (cur.constructor !== Object && (handler = constructorHandlers.get(cur.constructor))) a2[k] = handler(cur, fn);
				else if (ArrayBuffer.isView(cur)) a2[k] = copyBuffer(cur);
				else a2[k] = fn(cur);
			}
			return a2;
		}
		function clone(o) {
			if (typeof o !== "object" || o === null) return o;
			if (Array.isArray(o)) return cloneArray(o, clone);
			if (o.constructor !== Object && (handler = constructorHandlers.get(o.constructor))) return handler(o, clone);
			const o2 = {};
			for (const k in o) {
				if (Object.hasOwnProperty.call(o, k) === false) continue;
				const cur = o[k];
				if (typeof cur !== "object" || cur === null) o2[k] = cur;
				else if (cur.constructor !== Object && (handler = constructorHandlers.get(cur.constructor))) o2[k] = handler(cur, clone);
				else if (ArrayBuffer.isView(cur)) o2[k] = copyBuffer(cur);
				else o2[k] = clone(cur);
			}
			return o2;
		}
		function cloneProto(o) {
			if (typeof o !== "object" || o === null) return o;
			if (Array.isArray(o)) return cloneArray(o, cloneProto);
			if (o.constructor !== Object && (handler = constructorHandlers.get(o.constructor))) return handler(o, cloneProto);
			const o2 = {};
			for (const k in o) {
				const cur = o[k];
				if (typeof cur !== "object" || cur === null) o2[k] = cur;
				else if (cur.constructor !== Object && (handler = constructorHandlers.get(cur.constructor))) o2[k] = handler(cur, cloneProto);
				else if (ArrayBuffer.isView(cur)) o2[k] = copyBuffer(cur);
				else o2[k] = cloneProto(cur);
			}
			return o2;
		}
	}
	function rfdcCircles(opts) {
		const refs = [];
		const refsNew = [];
		const constructorHandlers = /* @__PURE__ */ new Map();
		constructorHandlers.set(Date, (o) => new Date(o));
		constructorHandlers.set(Map, (o, fn) => new Map(cloneArray(Array.from(o), fn)));
		constructorHandlers.set(Set, (o, fn) => new Set(cloneArray(Array.from(o), fn)));
		if (opts.constructorHandlers) for (const handler of opts.constructorHandlers) constructorHandlers.set(handler[0], handler[1]);
		let handler = null;
		return opts.proto ? cloneProto : clone;
		function cloneArray(a, fn) {
			const keys = Object.keys(a);
			const a2 = new Array(keys.length);
			for (let i = 0; i < keys.length; i++) {
				const k = keys[i];
				const cur = a[k];
				if (typeof cur !== "object" || cur === null) a2[k] = cur;
				else if (cur.constructor !== Object && (handler = constructorHandlers.get(cur.constructor))) a2[k] = handler(cur, fn);
				else if (ArrayBuffer.isView(cur)) a2[k] = copyBuffer(cur);
				else {
					const index = refs.indexOf(cur);
					if (index !== -1) a2[k] = refsNew[index];
					else a2[k] = fn(cur);
				}
			}
			return a2;
		}
		function clone(o) {
			if (typeof o !== "object" || o === null) return o;
			if (Array.isArray(o)) return cloneArray(o, clone);
			if (o.constructor !== Object && (handler = constructorHandlers.get(o.constructor))) return handler(o, clone);
			const o2 = {};
			refs.push(o);
			refsNew.push(o2);
			for (const k in o) {
				if (Object.hasOwnProperty.call(o, k) === false) continue;
				const cur = o[k];
				if (typeof cur !== "object" || cur === null) o2[k] = cur;
				else if (cur.constructor !== Object && (handler = constructorHandlers.get(cur.constructor))) o2[k] = handler(cur, clone);
				else if (ArrayBuffer.isView(cur)) o2[k] = copyBuffer(cur);
				else {
					const i = refs.indexOf(cur);
					if (i !== -1) o2[k] = refsNew[i];
					else o2[k] = clone(cur);
				}
			}
			refs.pop();
			refsNew.pop();
			return o2;
		}
		function cloneProto(o) {
			if (typeof o !== "object" || o === null) return o;
			if (Array.isArray(o)) return cloneArray(o, cloneProto);
			if (o.constructor !== Object && (handler = constructorHandlers.get(o.constructor))) return handler(o, cloneProto);
			const o2 = {};
			refs.push(o);
			refsNew.push(o2);
			for (const k in o) {
				const cur = o[k];
				if (typeof cur !== "object" || cur === null) o2[k] = cur;
				else if (cur.constructor !== Object && (handler = constructorHandlers.get(cur.constructor))) o2[k] = handler(cur, cloneProto);
				else if (ArrayBuffer.isView(cur)) o2[k] = copyBuffer(cur);
				else {
					const i = refs.indexOf(cur);
					if (i !== -1) o2[k] = refsNew[i];
					else o2[k] = cloneProto(cur);
				}
			}
			refs.pop();
			refsNew.pop();
			return o2;
		}
	}
})))(), 1);
function NOOP() {}
const isNumeric = (str) => `${+str}` === str;
const isMacOS = () => navigator?.platform ? navigator?.platform.toLowerCase().includes("mac") : /Macintosh/.test(navigator.userAgent);
const classifyRE = /(?:^|[-_/])(\w)/g;
const camelizeRE = /-(\w)/g;
const kebabizeRE = /([a-z0-9])([A-Z])/g;
function toUpper(_, c) {
	return c ? c.toUpperCase() : "";
}
function classify(str) {
	return str && `${str}`.replace(classifyRE, toUpper);
}
function camelize(str) {
	return str && str.replace(camelizeRE, toUpper);
}
function kebabize(str) {
	return str && str.replace(kebabizeRE, (_, lowerCaseCharacter, upperCaseLetter) => {
		return `${lowerCaseCharacter}-${upperCaseLetter}`;
	}).toLowerCase();
}
function basename(filename, ext) {
	let normalizedFilename = filename.replace(/^[a-z]:/i, "").replace(/\\/g, "/");
	if (normalizedFilename.endsWith(`index${ext}`)) normalizedFilename = normalizedFilename.replace(`/index${ext}`, ext);
	const lastSlashIndex = normalizedFilename.lastIndexOf("/");
	const baseNameWithExt = normalizedFilename.substring(lastSlashIndex + 1);
	if (ext) {
		const extIndex = baseNameWithExt.lastIndexOf(ext);
		return baseNameWithExt.substring(0, extIndex);
	}
	return "";
}
function sortByKey(state) {
	return state && state.slice().sort((a, b) => {
		if (a.key < b.key) return -1;
		if (a.key > b.key) return 1;
		return 0;
	});
}
const HTTP_URL_RE = /^https?:\/\//;
/**
* Check a string is start with `/` or a valid http url
*/
function isUrlString(str) {
	return str.startsWith("/") || HTTP_URL_RE.test(str);
}
/**
* @copyright [rfdc](https://github.com/davidmarkclements/rfdc)
* @description A really fast deep clone alternative
*/
const deepClone = (0, import_rfdc.default)({ circles: true });
function randomStr() {
	return Math.random().toString(36).slice(2);
}
function isObject(value) {
	return typeof value === "object" && !Array.isArray(value) && value !== null;
}
function isArray(value) {
	return Array.isArray(value);
}
function isSet(value) {
	return value instanceof Set;
}
function isMap(value) {
	return value instanceof Map;
}
//#endregion
exports.BROADCAST_CHANNEL_NAME = BROADCAST_CHANNEL_NAME;
exports.NOOP = NOOP;
exports.VIEW_MODE_STORAGE_KEY = VIEW_MODE_STORAGE_KEY;
exports.VITE_PLUGIN_CLIENT_URL_STORAGE_KEY = VITE_PLUGIN_CLIENT_URL_STORAGE_KEY;
exports.VITE_PLUGIN_DETECTED_STORAGE_KEY = VITE_PLUGIN_DETECTED_STORAGE_KEY;
exports.basename = basename;
exports.camelize = camelize;
exports.classify = classify;
exports.deepClone = deepClone;
exports.isArray = isArray;
exports.isBrowser = isBrowser;
exports.isInChromePanel = isInChromePanel;
exports.isInElectron = isInElectron;
exports.isInIframe = isInIframe;
exports.isInSeparateWindow = isInSeparateWindow;
exports.isMacOS = isMacOS;
exports.isMap = isMap;
exports.isNumeric = isNumeric;
exports.isNuxtApp = isNuxtApp;
exports.isObject = isObject;
exports.isSet = isSet;
exports.isUrlString = isUrlString;
exports.kebabize = kebabize;
exports.randomStr = randomStr;
exports.sortByKey = sortByKey;
exports.target = target;
