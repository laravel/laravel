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
//#region ../shared/src/env.ts
const isBrowser = typeof navigator !== "undefined";
const target = typeof window !== "undefined" ? window : typeof globalThis !== "undefined" ? globalThis : typeof global !== "undefined" ? global : {};
typeof target.chrome !== "undefined" && target.chrome.devtools;
isBrowser && (target.self, target.top);
typeof navigator !== "undefined" && navigator.userAgent?.toLowerCase().includes("electron");
typeof window !== "undefined" && window.__NUXT__;
//#endregion
//#region ../shared/src/general.ts
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
const classifyRE = /(?:^|[-_/])(\w)/g;
function toUpper(_, c) {
	return c ? c.toUpperCase() : "";
}
function classify(str) {
	return str && `${str}`.replace(classifyRE, toUpper);
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
//#endregion
//#region ../devtools-kit/src/core/component/utils/index.ts
function getComponentTypeName(options) {
	if (typeof options === "function") return options.displayName || options.name || options.__VUE_DEVTOOLS_COMPONENT_GUSSED_NAME__ || "";
	const name = options.name || options._componentTag || options.__VUE_DEVTOOLS_COMPONENT_GUSSED_NAME__ || options.__name;
	if (name === "index" && options.__file?.endsWith("index.vue")) return "";
	return name;
}
function getComponentFileName(options) {
	const file = options.__file;
	if (file) return classify(basename(file, ".vue"));
}
function saveComponentGussedName(instance, name) {
	instance.type.__VUE_DEVTOOLS_COMPONENT_GUSSED_NAME__ = name;
	return name;
}
function getAppRecord(instance) {
	if (instance.__VUE_DEVTOOLS_NEXT_APP_RECORD__) return instance.__VUE_DEVTOOLS_NEXT_APP_RECORD__;
	else if (instance.root) return instance.appContext.app.__VUE_DEVTOOLS_NEXT_APP_RECORD__;
}
function isFragment(instance) {
	const subTreeType = instance.subTree?.type;
	const appRecord = getAppRecord(instance);
	if (appRecord) return appRecord?.types?.Fragment === subTreeType;
	return false;
}
/**
* Get the appropriate display name for an instance.
*
* @param {Vue} instance
* @return {string}
*/
function getInstanceName(instance) {
	const name = getComponentTypeName(instance?.type || {});
	if (name) return name;
	if (instance?.root === instance) return "Root";
	for (const key in instance.parent?.type?.components) if (instance.parent.type.components[key] === instance?.type) return saveComponentGussedName(instance, key);
	for (const key in instance.appContext?.components) if (instance.appContext.components[key] === instance?.type) return saveComponentGussedName(instance, key);
	const fileName = getComponentFileName(instance?.type || {});
	if (fileName) return fileName;
	return "Anonymous Component";
}
/**
* Returns a devtools unique id for instance.
* @param {Vue} instance
*/
function getUniqueComponentId(instance) {
	return `${instance?.appContext?.app?.__VUE_DEVTOOLS_NEXT_APP_RECORD_ID__ ?? 0}:${instance === instance?.root ? "root" : instance.uid}`;
}
function getComponentInstance(appRecord, instanceId) {
	instanceId = instanceId || `${appRecord.id}:root`;
	return appRecord.instanceMap.get(instanceId) || appRecord.instanceMap.get(":root");
}
//#endregion
//#region ../devtools-kit/src/core/component/state/bounding-rect.ts
function createRect() {
	const rect = {
		top: 0,
		bottom: 0,
		left: 0,
		right: 0,
		get width() {
			return rect.right - rect.left;
		},
		get height() {
			return rect.bottom - rect.top;
		}
	};
	return rect;
}
let range;
function getTextRect(node) {
	if (!range) range = document.createRange();
	range.selectNode(node);
	return range.getBoundingClientRect();
}
function getFragmentRect(vnode) {
	const rect = createRect();
	if (!vnode.children) return rect;
	for (let i = 0, l = vnode.children.length; i < l; i++) {
		const childVnode = vnode.children[i];
		let childRect;
		if (childVnode.component) childRect = getComponentBoundingRect(childVnode.component);
		else if (childVnode.el) {
			const el = childVnode.el;
			if (el.nodeType === 1 || el.getBoundingClientRect) childRect = el.getBoundingClientRect();
			else if (el.nodeType === 3 && el.data.trim()) childRect = getTextRect(el);
		}
		if (childRect) mergeRects(rect, childRect);
	}
	return rect;
}
function mergeRects(a, b) {
	if (!a.top || b.top < a.top) a.top = b.top;
	if (!a.bottom || b.bottom > a.bottom) a.bottom = b.bottom;
	if (!a.left || b.left < a.left) a.left = b.left;
	if (!a.right || b.right > a.right) a.right = b.right;
	return a;
}
const DEFAULT_RECT = {
	top: 0,
	left: 0,
	right: 0,
	bottom: 0,
	width: 0,
	height: 0
};
function getComponentBoundingRect(instance) {
	const el = instance.subTree.el;
	if (typeof window === "undefined") return DEFAULT_RECT;
	if (isFragment(instance)) return getFragmentRect(instance.subTree);
	else if (el?.nodeType === 1) return el?.getBoundingClientRect();
	else if (instance.subTree.component) return getComponentBoundingRect(instance.subTree.component);
	else return DEFAULT_RECT;
}
//#endregion
//#region ../devtools-kit/src/core/component/tree/el.ts
function getRootElementsFromComponentInstance(instance) {
	if (isFragment(instance)) return getFragmentRootElements(instance.subTree);
	if (!instance.subTree) return [];
	return [instance.subTree.el];
}
function getFragmentRootElements(vnode) {
	if (!vnode.children) return [];
	const list = [];
	vnode.children.forEach((childVnode) => {
		if (childVnode.component) list.push(...getRootElementsFromComponentInstance(childVnode.component));
		else if (childVnode?.el) list.push(childVnode.el);
	});
	return list;
}
//#endregion
//#region ../devtools-kit/src/core/component-highlighter/index.ts
const CONTAINER_ELEMENT_ID = "__vue-devtools-component-inspector__";
const CARD_ELEMENT_ID = "__vue-devtools-component-inspector__card__";
const COMPONENT_NAME_ELEMENT_ID = "__vue-devtools-component-inspector__name__";
const INDICATOR_ELEMENT_ID = "__vue-devtools-component-inspector__indicator__";
const containerStyles = {
	display: "block",
	zIndex: 2147483640,
	position: "fixed",
	backgroundColor: "#42b88325",
	border: "1px solid #42b88350",
	borderRadius: "5px",
	transition: "all 0.1s ease-in",
	pointerEvents: "none"
};
const cardStyles = {
	fontFamily: "Arial, Helvetica, sans-serif",
	padding: "5px 8px",
	borderRadius: "4px",
	textAlign: "left",
	position: "absolute",
	left: 0,
	color: "#e9e9e9",
	fontSize: "14px",
	fontWeight: 600,
	lineHeight: "24px",
	backgroundColor: "#42b883",
	boxShadow: "0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.1)"
};
const indicatorStyles = {
	display: "inline-block",
	fontWeight: 400,
	fontStyle: "normal",
	fontSize: "12px",
	opacity: .7
};
function getContainerElement() {
	return document.getElementById(CONTAINER_ELEMENT_ID);
}
function getCardElement() {
	return document.getElementById(CARD_ELEMENT_ID);
}
function getIndicatorElement() {
	return document.getElementById(INDICATOR_ELEMENT_ID);
}
function getNameElement() {
	return document.getElementById(COMPONENT_NAME_ELEMENT_ID);
}
function getStyles(bounds) {
	return {
		left: `${Math.round(bounds.left * 100) / 100}px`,
		top: `${Math.round(bounds.top * 100) / 100}px`,
		width: `${Math.round(bounds.width * 100) / 100}px`,
		height: `${Math.round(bounds.height * 100) / 100}px`
	};
}
function create(options) {
	const containerEl = document.createElement("div");
	containerEl.id = options.elementId ?? CONTAINER_ELEMENT_ID;
	Object.assign(containerEl.style, {
		...containerStyles,
		...getStyles(options.bounds),
		...options.style
	});
	const cardEl = document.createElement("span");
	cardEl.id = CARD_ELEMENT_ID;
	Object.assign(cardEl.style, {
		...cardStyles,
		top: options.bounds.top < 35 ? 0 : "-35px"
	});
	const nameEl = document.createElement("span");
	nameEl.id = COMPONENT_NAME_ELEMENT_ID;
	nameEl.innerHTML = `&lt;${options.name}&gt;&nbsp;&nbsp;`;
	const indicatorEl = document.createElement("i");
	indicatorEl.id = INDICATOR_ELEMENT_ID;
	indicatorEl.innerHTML = `${Math.round(options.bounds.width * 100) / 100} x ${Math.round(options.bounds.height * 100) / 100}`;
	Object.assign(indicatorEl.style, indicatorStyles);
	cardEl.appendChild(nameEl);
	cardEl.appendChild(indicatorEl);
	containerEl.appendChild(cardEl);
	document.body.appendChild(containerEl);
	return containerEl;
}
function update(options) {
	const containerEl = getContainerElement();
	const cardEl = getCardElement();
	const nameEl = getNameElement();
	const indicatorEl = getIndicatorElement();
	if (containerEl) {
		Object.assign(containerEl.style, {
			...containerStyles,
			...getStyles(options.bounds)
		});
		Object.assign(cardEl.style, { top: options.bounds.top < 35 ? 0 : "-35px" });
		nameEl.innerHTML = `&lt;${options.name}&gt;&nbsp;&nbsp;`;
		indicatorEl.innerHTML = `${Math.round(options.bounds.width * 100) / 100} x ${Math.round(options.bounds.height * 100) / 100}`;
	}
}
function highlight(instance) {
	const bounds = getComponentBoundingRect(instance);
	if (!bounds.width && !bounds.height) return;
	const name = getInstanceName(instance);
	getContainerElement() ? update({
		bounds,
		name
	}) : create({
		bounds,
		name
	});
}
function unhighlight() {
	const el = getContainerElement();
	if (el) el.style.display = "none";
}
let inspectInstance = null;
function inspectFn(e) {
	const target = e.target;
	if (target) {
		const instance = target.__vueParentComponent;
		if (instance) {
			inspectInstance = instance;
			if (instance.vnode.el) {
				const bounds = getComponentBoundingRect(instance);
				const name = getInstanceName(instance);
				getContainerElement() ? update({
					bounds,
					name
				}) : create({
					bounds,
					name
				});
			}
		}
	}
}
function selectComponentFn(e, cb) {
	e.preventDefault();
	e.stopPropagation();
	if (inspectInstance) cb(getUniqueComponentId(inspectInstance));
}
let inspectComponentHighLighterSelectFn = null;
function cancelInspectComponentHighLighter() {
	unhighlight();
	window.removeEventListener("mouseover", inspectFn);
	window.removeEventListener("click", inspectComponentHighLighterSelectFn, true);
	inspectComponentHighLighterSelectFn = null;
}
function inspectComponentHighLighter() {
	window.addEventListener("mouseover", inspectFn);
	return new Promise((resolve) => {
		function onSelect(e) {
			e.preventDefault();
			e.stopPropagation();
			selectComponentFn(e, (id) => {
				window.removeEventListener("click", onSelect, true);
				inspectComponentHighLighterSelectFn = null;
				window.removeEventListener("mouseover", inspectFn);
				const el = getContainerElement();
				if (el) el.style.display = "none";
				resolve(JSON.stringify({ id }));
			});
		}
		inspectComponentHighLighterSelectFn = onSelect;
		window.addEventListener("click", onSelect, true);
	});
}
function scrollToComponent(options) {
	const instance = getComponentInstance(activeAppRecord.value, options.id);
	if (instance) {
		const [el] = getRootElementsFromComponentInstance(instance);
		if (typeof el.scrollIntoView === "function") el.scrollIntoView({ behavior: "smooth" });
		else {
			const bounds = getComponentBoundingRect(instance);
			const scrollTarget = document.createElement("div");
			const styles = {
				...getStyles(bounds),
				position: "absolute"
			};
			Object.assign(scrollTarget.style, styles);
			document.body.appendChild(scrollTarget);
			scrollTarget.scrollIntoView({ behavior: "smooth" });
			setTimeout(() => {
				document.body.removeChild(scrollTarget);
			}, 2e3);
		}
		setTimeout(() => {
			const bounds = getComponentBoundingRect(instance);
			if (bounds.width || bounds.height) {
				const name = getInstanceName(instance);
				const el = getContainerElement();
				el ? update({
					...options,
					name,
					bounds
				}) : create({
					...options,
					name,
					bounds
				});
				setTimeout(() => {
					if (el) el.style.display = "none";
				}, 1500);
			}
		}, 1200);
	}
}
//#endregion
//#region ../devtools-kit/src/core/component-inspector/index.ts
target.__VUE_DEVTOOLS_COMPONENT_INSPECTOR_ENABLED__ ??= true;
function waitForInspectorInit(cb) {
	let total = 0;
	const timer = setInterval(() => {
		if (target.__VUE_INSPECTOR__) {
			clearInterval(timer);
			total += 30;
			cb();
		}
		if (total >= 5e3) clearInterval(timer);
	}, 30);
}
function setupInspector() {
	const inspector = target.__VUE_INSPECTOR__;
	const _openInEditor = inspector.openInEditor;
	inspector.openInEditor = async (...params) => {
		inspector.disable();
		_openInEditor(...params);
	};
}
function getComponentInspector() {
	return new Promise((resolve) => {
		function setup() {
			setupInspector();
			resolve(target.__VUE_INSPECTOR__);
		}
		if (!target.__VUE_INSPECTOR__) waitForInspectorInit(() => {
			setup();
		});
		else setup();
	});
}
//#endregion
//#region ../devtools-kit/src/shared/stub-vue.ts
/**
* To prevent include a **HUGE** vue package in the final bundle of chrome ext / electron
* we stub the necessary vue module.
* This implementation is based on the 1c3327a0fa5983aa9078e3f7bb2330f572435425 commit
*/
/**
* @from [@vue/reactivity](https://github.com/vuejs/core/blob/1c3327a0fa5983aa9078e3f7bb2330f572435425/packages/reactivity/src/constants.ts#L17-L23)
*/
let ReactiveFlags = /* @__PURE__ */ function(ReactiveFlags) {
	ReactiveFlags["SKIP"] = "__v_skip";
	ReactiveFlags["IS_REACTIVE"] = "__v_isReactive";
	ReactiveFlags["IS_READONLY"] = "__v_isReadonly";
	ReactiveFlags["IS_SHALLOW"] = "__v_isShallow";
	ReactiveFlags["RAW"] = "__v_raw";
	return ReactiveFlags;
}({});
/**
* @from [@vue/reactivity](https://github.com/vuejs/core/blob/1c3327a0fa5983aa9078e3f7bb2330f572435425/packages/reactivity/src/reactive.ts#L330-L332)
*/
function isReadonly(value) {
	return !!(value && value[ReactiveFlags.IS_READONLY]);
}
/**
* @from [@vue/reactivity](https://github.com/vuejs/core/blob/1c3327a0fa5983aa9078e3f7bb2330f572435425/packages/reactivity/src/reactive.ts#L312-L317)
*/
function isReactive(value) {
	if (isReadonly(value)) return isReactive(value[ReactiveFlags.RAW]);
	return !!(value && value[ReactiveFlags.IS_REACTIVE]);
}
function isRef(r) {
	return !!(r && r.__v_isRef === true);
}
/**
* @from [@vue/reactivity](https://github.com/vuejs/core/blob/1c3327a0fa5983aa9078e3f7bb2330f572435425/packages/reactivity/src/reactive.ts#L372-L375)
*/
function toRaw(observed) {
	const raw = observed && observed[ReactiveFlags.RAW];
	return raw ? toRaw(raw) : observed;
}
//#endregion
//#region ../devtools-kit/src/core/component/state/editor.ts
var StateEditor = class {
	constructor() {
		this.refEditor = new RefStateEditor();
	}
	set(object, path, value, cb) {
		const sections = Array.isArray(path) ? path : path.split(".");
		while (sections.length > 1) {
			const section = sections.shift();
			if (object instanceof Map) object = object.get(section);
			else if (object instanceof Set) object = Array.from(object.values())[section];
			else object = object[section];
			if (this.refEditor.isRef(object)) object = this.refEditor.get(object);
		}
		const field = sections[0];
		const item = this.refEditor.get(object)[field];
		if (cb) cb(object, field, value);
		else if (this.refEditor.isRef(item)) this.refEditor.set(item, value);
		else object[field] = value;
	}
	get(object, path) {
		const sections = Array.isArray(path) ? path : path.split(".");
		for (let i = 0; i < sections.length; i++) {
			if (object instanceof Map) object = object.get(sections[i]);
			else object = object[sections[i]];
			if (this.refEditor.isRef(object)) object = this.refEditor.get(object);
			if (!object) return void 0;
		}
		return object;
	}
	has(object, path, parent = false) {
		if (typeof object === "undefined") return false;
		const sections = Array.isArray(path) ? path.slice() : path.split(".");
		const size = !parent ? 1 : 2;
		while (object && sections.length > size) {
			const section = sections.shift();
			object = object[section];
			if (this.refEditor.isRef(object)) object = this.refEditor.get(object);
		}
		return object != null && Object.prototype.hasOwnProperty.call(object, sections[0]);
	}
	createDefaultSetCallback(state) {
		return (object, field, value) => {
			if (state.remove || state.newKey) if (Array.isArray(object)) object.splice(field, 1);
			else if (toRaw(object) instanceof Map) object.delete(field);
			else if (toRaw(object) instanceof Set) object.delete(Array.from(object.values())[field]);
			else Reflect.deleteProperty(object, field);
			if (!state.remove) {
				const target = object[state.newKey || field];
				if (this.refEditor.isRef(target)) this.refEditor.set(target, value);
				else if (toRaw(object) instanceof Map) object.set(state.newKey || field, value);
				else if (toRaw(object) instanceof Set) object.add(value);
				else object[state.newKey || field] = value;
			}
		};
	}
};
var RefStateEditor = class {
	set(ref, value) {
		if (isRef(ref)) ref.value = value;
		else {
			if (ref instanceof Set && Array.isArray(value)) {
				ref.clear();
				value.forEach((v) => ref.add(v));
				return;
			}
			const currentKeys = Object.keys(value);
			if (ref instanceof Map) {
				const previousKeysSet = new Set(ref.keys());
				currentKeys.forEach((key) => {
					ref.set(key, Reflect.get(value, key));
					previousKeysSet.delete(key);
				});
				previousKeysSet.forEach((key) => ref.delete(key));
				return;
			}
			const previousKeysSet = new Set(Object.keys(ref));
			currentKeys.forEach((key) => {
				Reflect.set(ref, key, Reflect.get(value, key));
				previousKeysSet.delete(key);
			});
			previousKeysSet.forEach((key) => Reflect.deleteProperty(ref, key));
		}
	}
	get(ref) {
		return isRef(ref) ? ref.value : ref;
	}
	isRef(ref) {
		return isRef(ref) || isReactive(ref);
	}
};
new StateEditor();
//#endregion
//#region ../../node_modules/.pnpm/perfect-debounce@2.0.0/node_modules/perfect-debounce/dist/index.mjs
const DEBOUNCE_DEFAULTS = { trailing: true };
/**
Debounce functions
@param fn - Promise-returning/async function to debounce.
@param wait - Milliseconds to wait before calling `fn`. Default value is 25ms
@returns A function that delays calling `fn` until after `wait` milliseconds have elapsed since the last time it was called.
@example
```
import { debounce } from 'perfect-debounce';
const expensiveCall = async input => input;
const debouncedFn = debounce(expensiveCall, 200);
for (const number of [1, 2, 3]) {
console.log(await debouncedFn(number));
}
//=> 1
//=> 2
//=> 3
```
*/
function debounce(fn, wait = 25, options = {}) {
	options = {
		...DEBOUNCE_DEFAULTS,
		...options
	};
	if (!Number.isFinite(wait)) throw new TypeError("Expected `wait` to be a finite number");
	let leadingValue;
	let timeout;
	let resolveList = [];
	let currentPromise;
	let trailingArgs;
	const applyFn = (_this, args) => {
		currentPromise = _applyPromised(fn, _this, args);
		currentPromise.finally(() => {
			currentPromise = null;
			if (options.trailing && trailingArgs && !timeout) {
				const promise = applyFn(_this, trailingArgs);
				trailingArgs = null;
				return promise;
			}
		});
		return currentPromise;
	};
	const debounced = function(...args) {
		if (options.trailing) trailingArgs = args;
		if (currentPromise) return currentPromise;
		return new Promise((resolve) => {
			const shouldCallNow = !timeout && options.leading;
			clearTimeout(timeout);
			timeout = setTimeout(() => {
				timeout = null;
				const promise = options.leading ? leadingValue : applyFn(this, args);
				trailingArgs = null;
				for (const _resolve of resolveList) _resolve(promise);
				resolveList = [];
			}, wait);
			if (shouldCallNow) {
				leadingValue = applyFn(this, args);
				resolve(leadingValue);
			} else resolveList.push(resolve);
		});
	};
	const _clearTimeout = (timer) => {
		if (timer) {
			clearTimeout(timer);
			timeout = null;
		}
	};
	debounced.isPending = () => !!timeout;
	debounced.cancel = () => {
		_clearTimeout(timeout);
		resolveList = [];
		trailingArgs = null;
	};
	debounced.flush = () => {
		_clearTimeout(timeout);
		if (!trailingArgs || currentPromise) return;
		const args = trailingArgs;
		trailingArgs = null;
		return applyFn(this, args);
	};
	return debounced;
}
async function _applyPromised(fn, _this, args) {
	return await fn.apply(_this, args);
}
//#endregion
//#region ../devtools-kit/src/core/timeline/storage.ts
const TIMELINE_LAYERS_STATE_STORAGE_ID = "__VUE_DEVTOOLS_KIT_TIMELINE_LAYERS_STATE__";
function getTimelineLayersStateFromStorage() {
	if (typeof window === "undefined" || !isBrowser || typeof localStorage === "undefined" || localStorage === null) return {
		recordingState: false,
		mouseEventEnabled: false,
		keyboardEventEnabled: false,
		componentEventEnabled: false,
		performanceEventEnabled: false,
		selected: ""
	};
	const state = typeof localStorage.getItem !== "undefined" ? localStorage.getItem(TIMELINE_LAYERS_STATE_STORAGE_ID) : null;
	return state ? JSON.parse(state) : {
		recordingState: false,
		mouseEventEnabled: false,
		keyboardEventEnabled: false,
		componentEventEnabled: false,
		performanceEventEnabled: false,
		selected: ""
	};
}
//#endregion
//#region ../../node_modules/.pnpm/hookable@5.5.3/node_modules/hookable/dist/index.mjs
function flatHooks(configHooks, hooks = {}, parentName) {
	for (const key in configHooks) {
		const subHook = configHooks[key];
		const name = parentName ? `${parentName}:${key}` : key;
		if (typeof subHook === "object" && subHook !== null) flatHooks(subHook, hooks, name);
		else if (typeof subHook === "function") hooks[name] = subHook;
	}
	return hooks;
}
const defaultTask = { run: (function_) => function_() };
const _createTask = () => defaultTask;
const createTask = typeof console.createTask !== "undefined" ? console.createTask : _createTask;
function serialTaskCaller(hooks, args) {
	const task = createTask(args.shift());
	return hooks.reduce((promise, hookFunction) => promise.then(() => task.run(() => hookFunction(...args))), Promise.resolve());
}
function parallelTaskCaller(hooks, args) {
	const task = createTask(args.shift());
	return Promise.all(hooks.map((hook) => task.run(() => hook(...args))));
}
function callEachWith(callbacks, arg0) {
	for (const callback of [...callbacks]) callback(arg0);
}
var Hookable = class {
	constructor() {
		this._hooks = {};
		this._before = void 0;
		this._after = void 0;
		this._deprecatedMessages = void 0;
		this._deprecatedHooks = {};
		this.hook = this.hook.bind(this);
		this.callHook = this.callHook.bind(this);
		this.callHookWith = this.callHookWith.bind(this);
	}
	hook(name, function_, options = {}) {
		if (!name || typeof function_ !== "function") return () => {};
		const originalName = name;
		let dep;
		while (this._deprecatedHooks[name]) {
			dep = this._deprecatedHooks[name];
			name = dep.to;
		}
		if (dep && !options.allowDeprecated) {
			let message = dep.message;
			if (!message) message = `${originalName} hook has been deprecated` + (dep.to ? `, please use ${dep.to}` : "");
			if (!this._deprecatedMessages) this._deprecatedMessages = /* @__PURE__ */ new Set();
			if (!this._deprecatedMessages.has(message)) {
				console.warn(message);
				this._deprecatedMessages.add(message);
			}
		}
		if (!function_.name) try {
			Object.defineProperty(function_, "name", {
				get: () => "_" + name.replace(/\W+/g, "_") + "_hook_cb",
				configurable: true
			});
		} catch {}
		this._hooks[name] = this._hooks[name] || [];
		this._hooks[name].push(function_);
		return () => {
			if (function_) {
				this.removeHook(name, function_);
				function_ = void 0;
			}
		};
	}
	hookOnce(name, function_) {
		let _unreg;
		let _function = (...arguments_) => {
			if (typeof _unreg === "function") _unreg();
			_unreg = void 0;
			_function = void 0;
			return function_(...arguments_);
		};
		_unreg = this.hook(name, _function);
		return _unreg;
	}
	removeHook(name, function_) {
		if (this._hooks[name]) {
			const index = this._hooks[name].indexOf(function_);
			if (index !== -1) this._hooks[name].splice(index, 1);
			if (this._hooks[name].length === 0) delete this._hooks[name];
		}
	}
	deprecateHook(name, deprecated) {
		this._deprecatedHooks[name] = typeof deprecated === "string" ? { to: deprecated } : deprecated;
		const _hooks = this._hooks[name] || [];
		delete this._hooks[name];
		for (const hook of _hooks) this.hook(name, hook);
	}
	deprecateHooks(deprecatedHooks) {
		Object.assign(this._deprecatedHooks, deprecatedHooks);
		for (const name in deprecatedHooks) this.deprecateHook(name, deprecatedHooks[name]);
	}
	addHooks(configHooks) {
		const hooks = flatHooks(configHooks);
		const removeFns = Object.keys(hooks).map((key) => this.hook(key, hooks[key]));
		return () => {
			for (const unreg of removeFns.splice(0, removeFns.length)) unreg();
		};
	}
	removeHooks(configHooks) {
		const hooks = flatHooks(configHooks);
		for (const key in hooks) this.removeHook(key, hooks[key]);
	}
	removeAllHooks() {
		for (const key in this._hooks) delete this._hooks[key];
	}
	callHook(name, ...arguments_) {
		arguments_.unshift(name);
		return this.callHookWith(serialTaskCaller, name, ...arguments_);
	}
	callHookParallel(name, ...arguments_) {
		arguments_.unshift(name);
		return this.callHookWith(parallelTaskCaller, name, ...arguments_);
	}
	callHookWith(caller, name, ...arguments_) {
		const event = this._before || this._after ? {
			name,
			args: arguments_,
			context: {}
		} : void 0;
		if (this._before) callEachWith(this._before, event);
		const result = caller(name in this._hooks ? [...this._hooks[name]] : [], arguments_);
		if (result instanceof Promise) return result.finally(() => {
			if (this._after && event) callEachWith(this._after, event);
		});
		if (this._after && event) callEachWith(this._after, event);
		return result;
	}
	beforeEach(function_) {
		this._before = this._before || [];
		this._before.push(function_);
		return () => {
			if (this._before !== void 0) {
				const index = this._before.indexOf(function_);
				if (index !== -1) this._before.splice(index, 1);
			}
		};
	}
	afterEach(function_) {
		this._after = this._after || [];
		this._after.push(function_);
		return () => {
			if (this._after !== void 0) {
				const index = this._after.indexOf(function_);
				if (index !== -1) this._after.splice(index, 1);
			}
		};
	}
};
function createHooks() {
	return new Hookable();
}
//#endregion
//#region ../devtools-kit/src/ctx/timeline.ts
target.__VUE_DEVTOOLS_KIT_TIMELINE_LAYERS ??= [];
const devtoolsTimelineLayers = new Proxy(target.__VUE_DEVTOOLS_KIT_TIMELINE_LAYERS, { get(target, prop, receiver) {
	return Reflect.get(target, prop, receiver);
} });
function addTimelineLayer(options, descriptor) {
	devtoolsState.timelineLayersState[descriptor.id] = false;
	devtoolsTimelineLayers.push({
		...options,
		descriptorId: descriptor.id,
		appRecord: getAppRecord(descriptor.app)
	});
}
//#endregion
//#region ../devtools-kit/src/ctx/inspector.ts
target.__VUE_DEVTOOLS_KIT_INSPECTOR__ ??= [];
const devtoolsInspector = new Proxy(target.__VUE_DEVTOOLS_KIT_INSPECTOR__, { get(target, prop, receiver) {
	return Reflect.get(target, prop, receiver);
} });
const callInspectorUpdatedHook = debounce(() => {
	devtoolsContext.hooks.callHook(DevToolsMessagingHookKeys.SEND_INSPECTOR_TO_CLIENT, getActiveInspectors());
});
function addInspector(inspector, descriptor) {
	devtoolsInspector.push({
		options: inspector,
		descriptor,
		treeFilterPlaceholder: inspector.treeFilterPlaceholder ?? "Search tree...",
		stateFilterPlaceholder: inspector.stateFilterPlaceholder ?? "Search state...",
		treeFilter: "",
		selectedNodeId: "",
		appRecord: getAppRecord(descriptor.app)
	});
	callInspectorUpdatedHook();
}
function getActiveInspectors() {
	return devtoolsInspector.filter((inspector) => inspector.descriptor.app === activeAppRecord.value.app).filter((inspector) => inspector.descriptor.id !== "components").map((inspector) => {
		const descriptor = inspector.descriptor;
		const options = inspector.options;
		return {
			id: options.id,
			label: options.label,
			logo: descriptor.logo,
			icon: `custom-ic-baseline-${options?.icon?.replace(/_/g, "-")}`,
			packageName: descriptor.packageName,
			homepage: descriptor.homepage,
			pluginId: descriptor.id
		};
	});
}
function getInspector(id, app) {
	return devtoolsInspector.find((inspector) => inspector.options.id === id && (app ? inspector.descriptor.app === app : true));
}
//#endregion
//#region ../devtools-kit/src/ctx/hook.ts
let DevToolsV6PluginAPIHookKeys = /* @__PURE__ */ function(DevToolsV6PluginAPIHookKeys) {
	DevToolsV6PluginAPIHookKeys["VISIT_COMPONENT_TREE"] = "visitComponentTree";
	DevToolsV6PluginAPIHookKeys["INSPECT_COMPONENT"] = "inspectComponent";
	DevToolsV6PluginAPIHookKeys["EDIT_COMPONENT_STATE"] = "editComponentState";
	DevToolsV6PluginAPIHookKeys["GET_INSPECTOR_TREE"] = "getInspectorTree";
	DevToolsV6PluginAPIHookKeys["GET_INSPECTOR_STATE"] = "getInspectorState";
	DevToolsV6PluginAPIHookKeys["EDIT_INSPECTOR_STATE"] = "editInspectorState";
	DevToolsV6PluginAPIHookKeys["INSPECT_TIMELINE_EVENT"] = "inspectTimelineEvent";
	DevToolsV6PluginAPIHookKeys["TIMELINE_CLEARED"] = "timelineCleared";
	DevToolsV6PluginAPIHookKeys["SET_PLUGIN_SETTINGS"] = "setPluginSettings";
	return DevToolsV6PluginAPIHookKeys;
}({});
let DevToolsContextHookKeys = /* @__PURE__ */ function(DevToolsContextHookKeys) {
	DevToolsContextHookKeys["ADD_INSPECTOR"] = "addInspector";
	DevToolsContextHookKeys["SEND_INSPECTOR_TREE"] = "sendInspectorTree";
	DevToolsContextHookKeys["SEND_INSPECTOR_STATE"] = "sendInspectorState";
	DevToolsContextHookKeys["CUSTOM_INSPECTOR_SELECT_NODE"] = "customInspectorSelectNode";
	DevToolsContextHookKeys["TIMELINE_LAYER_ADDED"] = "timelineLayerAdded";
	DevToolsContextHookKeys["TIMELINE_EVENT_ADDED"] = "timelineEventAdded";
	DevToolsContextHookKeys["GET_COMPONENT_INSTANCES"] = "getComponentInstances";
	DevToolsContextHookKeys["GET_COMPONENT_BOUNDS"] = "getComponentBounds";
	DevToolsContextHookKeys["GET_COMPONENT_NAME"] = "getComponentName";
	DevToolsContextHookKeys["COMPONENT_HIGHLIGHT"] = "componentHighlight";
	DevToolsContextHookKeys["COMPONENT_UNHIGHLIGHT"] = "componentUnhighlight";
	return DevToolsContextHookKeys;
}({});
let DevToolsMessagingHookKeys = /* @__PURE__ */ function(DevToolsMessagingHookKeys) {
	DevToolsMessagingHookKeys["SEND_INSPECTOR_TREE_TO_CLIENT"] = "sendInspectorTreeToClient";
	DevToolsMessagingHookKeys["SEND_INSPECTOR_STATE_TO_CLIENT"] = "sendInspectorStateToClient";
	DevToolsMessagingHookKeys["SEND_TIMELINE_EVENT_TO_CLIENT"] = "sendTimelineEventToClient";
	DevToolsMessagingHookKeys["SEND_INSPECTOR_TO_CLIENT"] = "sendInspectorToClient";
	DevToolsMessagingHookKeys["SEND_ACTIVE_APP_UNMOUNTED_TO_CLIENT"] = "sendActiveAppUpdatedToClient";
	DevToolsMessagingHookKeys["DEVTOOLS_STATE_UPDATED"] = "devtoolsStateUpdated";
	DevToolsMessagingHookKeys["DEVTOOLS_CONNECTED_UPDATED"] = "devtoolsConnectedUpdated";
	DevToolsMessagingHookKeys["ROUTER_INFO_UPDATED"] = "routerInfoUpdated";
	return DevToolsMessagingHookKeys;
}({});
function createDevToolsCtxHooks() {
	const hooks = createHooks();
	hooks.hook(DevToolsContextHookKeys.ADD_INSPECTOR, ({ inspector, plugin }) => {
		addInspector(inspector, plugin.descriptor);
	});
	const debounceSendInspectorTree = debounce(async ({ inspectorId, plugin }) => {
		if (!inspectorId || !plugin?.descriptor?.app || devtoolsState.highPerfModeEnabled) return;
		const inspector = getInspector(inspectorId, plugin.descriptor.app);
		const _payload = {
			app: plugin.descriptor.app,
			inspectorId,
			filter: inspector?.treeFilter || "",
			rootNodes: []
		};
		await new Promise((resolve) => {
			hooks.callHookWith(async (callbacks) => {
				await Promise.all(callbacks.map((cb) => cb(_payload)));
				resolve();
			}, DevToolsV6PluginAPIHookKeys.GET_INSPECTOR_TREE);
		});
		hooks.callHookWith(async (callbacks) => {
			await Promise.all(callbacks.map((cb) => cb({
				inspectorId,
				rootNodes: _payload.rootNodes
			})));
		}, DevToolsMessagingHookKeys.SEND_INSPECTOR_TREE_TO_CLIENT);
	}, 120);
	hooks.hook(DevToolsContextHookKeys.SEND_INSPECTOR_TREE, debounceSendInspectorTree);
	const debounceSendInspectorState = debounce(async ({ inspectorId, plugin }) => {
		if (!inspectorId || !plugin?.descriptor?.app || devtoolsState.highPerfModeEnabled) return;
		const inspector = getInspector(inspectorId, plugin.descriptor.app);
		const _payload = {
			app: plugin.descriptor.app,
			inspectorId,
			nodeId: inspector?.selectedNodeId || "",
			state: null
		};
		const ctx = { currentTab: `custom-inspector:${inspectorId}` };
		if (_payload.nodeId) await new Promise((resolve) => {
			hooks.callHookWith(async (callbacks) => {
				await Promise.all(callbacks.map((cb) => cb(_payload, ctx)));
				resolve();
			}, DevToolsV6PluginAPIHookKeys.GET_INSPECTOR_STATE);
		});
		hooks.callHookWith(async (callbacks) => {
			await Promise.all(callbacks.map((cb) => cb({
				inspectorId,
				nodeId: _payload.nodeId,
				state: _payload.state
			})));
		}, DevToolsMessagingHookKeys.SEND_INSPECTOR_STATE_TO_CLIENT);
	}, 120);
	hooks.hook(DevToolsContextHookKeys.SEND_INSPECTOR_STATE, debounceSendInspectorState);
	hooks.hook(DevToolsContextHookKeys.CUSTOM_INSPECTOR_SELECT_NODE, ({ inspectorId, nodeId, plugin }) => {
		const inspector = getInspector(inspectorId, plugin.descriptor.app);
		if (!inspector) return;
		inspector.selectedNodeId = nodeId;
	});
	hooks.hook(DevToolsContextHookKeys.TIMELINE_LAYER_ADDED, ({ options, plugin }) => {
		addTimelineLayer(options, plugin.descriptor);
	});
	hooks.hook(DevToolsContextHookKeys.TIMELINE_EVENT_ADDED, ({ options, plugin }) => {
		if (devtoolsState.highPerfModeEnabled || !devtoolsState.timelineLayersState?.[plugin.descriptor.id] && ![
			"performance",
			"component-event",
			"keyboard",
			"mouse"
		].includes(options.layerId)) return;
		hooks.callHookWith(async (callbacks) => {
			await Promise.all(callbacks.map((cb) => cb(options)));
		}, DevToolsMessagingHookKeys.SEND_TIMELINE_EVENT_TO_CLIENT);
	});
	hooks.hook(DevToolsContextHookKeys.GET_COMPONENT_INSTANCES, async ({ app }) => {
		const appRecord = app.__VUE_DEVTOOLS_NEXT_APP_RECORD__;
		if (!appRecord) return null;
		const appId = appRecord.id.toString();
		return [...appRecord.instanceMap].filter(([key]) => key.split(":")[0] === appId).map(([, instance]) => instance);
	});
	hooks.hook(DevToolsContextHookKeys.GET_COMPONENT_BOUNDS, async ({ instance }) => {
		return getComponentBoundingRect(instance);
	});
	hooks.hook(DevToolsContextHookKeys.GET_COMPONENT_NAME, ({ instance }) => {
		return getInstanceName(instance);
	});
	hooks.hook(DevToolsContextHookKeys.COMPONENT_HIGHLIGHT, ({ uid }) => {
		const instance = activeAppRecord.value.instanceMap.get(uid);
		if (instance) highlight(instance);
	});
	hooks.hook(DevToolsContextHookKeys.COMPONENT_UNHIGHLIGHT, () => {
		unhighlight();
	});
	return hooks;
}
//#endregion
//#region ../devtools-kit/src/ctx/state.ts
target.__VUE_DEVTOOLS_KIT_APP_RECORDS__ ??= [];
target.__VUE_DEVTOOLS_KIT_ACTIVE_APP_RECORD__ ??= {};
target.__VUE_DEVTOOLS_KIT_ACTIVE_APP_RECORD_ID__ ??= "";
target.__VUE_DEVTOOLS_KIT_CUSTOM_TABS__ ??= [];
target.__VUE_DEVTOOLS_KIT_CUSTOM_COMMANDS__ ??= [];
const STATE_KEY = "__VUE_DEVTOOLS_KIT_GLOBAL_STATE__";
function initStateFactory() {
	return {
		connected: false,
		clientConnected: false,
		vitePluginDetected: true,
		appRecords: [],
		activeAppRecordId: "",
		tabs: [],
		commands: [],
		highPerfModeEnabled: true,
		devtoolsClientDetected: {},
		perfUniqueGroupId: 0,
		timelineLayersState: getTimelineLayersStateFromStorage()
	};
}
target[STATE_KEY] ??= initStateFactory();
const callStateUpdatedHook = debounce((state) => {
	devtoolsContext.hooks.callHook(DevToolsMessagingHookKeys.DEVTOOLS_STATE_UPDATED, { state });
});
debounce((state, oldState) => {
	devtoolsContext.hooks.callHook(DevToolsMessagingHookKeys.DEVTOOLS_CONNECTED_UPDATED, {
		state,
		oldState
	});
});
const devtoolsAppRecords = new Proxy(target.__VUE_DEVTOOLS_KIT_APP_RECORDS__, { get(_target, prop, receiver) {
	if (prop === "value") return target.__VUE_DEVTOOLS_KIT_APP_RECORDS__;
	return target.__VUE_DEVTOOLS_KIT_APP_RECORDS__[prop];
} });
const activeAppRecord = new Proxy(target.__VUE_DEVTOOLS_KIT_ACTIVE_APP_RECORD__, { get(_target, prop, receiver) {
	if (prop === "value") return target.__VUE_DEVTOOLS_KIT_ACTIVE_APP_RECORD__;
	else if (prop === "id") return target.__VUE_DEVTOOLS_KIT_ACTIVE_APP_RECORD_ID__;
	return target.__VUE_DEVTOOLS_KIT_ACTIVE_APP_RECORD__[prop];
} });
function updateAllStates() {
	callStateUpdatedHook({
		...target[STATE_KEY],
		appRecords: devtoolsAppRecords.value,
		activeAppRecordId: activeAppRecord.id,
		tabs: target.__VUE_DEVTOOLS_KIT_CUSTOM_TABS__,
		commands: target.__VUE_DEVTOOLS_KIT_CUSTOM_COMMANDS__
	});
}
function setActiveAppRecord(app) {
	target.__VUE_DEVTOOLS_KIT_ACTIVE_APP_RECORD__ = app;
	updateAllStates();
}
function setActiveAppRecordId(id) {
	target.__VUE_DEVTOOLS_KIT_ACTIVE_APP_RECORD_ID__ = id;
	updateAllStates();
}
const devtoolsState = new Proxy(target[STATE_KEY], {
	get(target$3, property) {
		if (property === "appRecords") return devtoolsAppRecords;
		else if (property === "activeAppRecordId") return activeAppRecord.id;
		else if (property === "tabs") return target.__VUE_DEVTOOLS_KIT_CUSTOM_TABS__;
		else if (property === "commands") return target.__VUE_DEVTOOLS_KIT_CUSTOM_COMMANDS__;
		return target[STATE_KEY][property];
	},
	deleteProperty(target, property) {
		delete target[property];
		return true;
	},
	set(target$4, property, value) {
		target$4[property] = value;
		target[STATE_KEY][property] = value;
		return true;
	}
});
function onDevToolsConnected(fn) {
	return new Promise((resolve) => {
		if (devtoolsState.connected) {
			fn();
			resolve();
		}
		devtoolsContext.hooks.hook(DevToolsMessagingHookKeys.DEVTOOLS_CONNECTED_UPDATED, ({ state }) => {
			if (state.connected) {
				fn();
				resolve();
			}
		});
	});
}
const resolveIcon = (icon) => {
	if (!icon) return;
	if (icon.startsWith("baseline-")) return `custom-ic-${icon}`;
	if (icon.startsWith("i-") || isUrlString(icon)) return icon;
	return `custom-ic-baseline-${icon}`;
};
function addCustomTab(tab) {
	const tabs = target.__VUE_DEVTOOLS_KIT_CUSTOM_TABS__;
	if (tabs.some((t) => t.name === tab.name)) return;
	tabs.push({
		...tab,
		icon: resolveIcon(tab.icon)
	});
	updateAllStates();
}
function addCustomCommand(action) {
	const commands = target.__VUE_DEVTOOLS_KIT_CUSTOM_COMMANDS__;
	if (commands.some((t) => t.id === action.id)) return;
	commands.push({
		...action,
		icon: resolveIcon(action.icon),
		children: action.children ? action.children.map((child) => ({
			...child,
			icon: resolveIcon(child.icon)
		})) : void 0
	});
	updateAllStates();
}
function removeCustomCommand(actionId) {
	const commands = target.__VUE_DEVTOOLS_KIT_CUSTOM_COMMANDS__;
	const index = commands.findIndex((t) => t.id === actionId);
	if (index === -1) return;
	commands.splice(index, 1);
	updateAllStates();
}
//#endregion
//#region ../devtools-kit/src/core/open-in-editor/index.ts
function openInEditor(options = {}) {
	const { file, host, baseUrl = window.location.origin, line = 0, column = 0 } = options;
	if (file) {
		if (host === "chrome-extension") {
			const fileName = file.replace(/\\/g, "\\\\");
			const _baseUrl = window.VUE_DEVTOOLS_CONFIG?.openInEditorHost ?? "/";
			fetch(`${_baseUrl}__open-in-editor?file=${encodeURI(file)}`).then((response) => {
				if (!response.ok) {
					const msg = `Opening component ${fileName} failed`;
					console.log(`%c${msg}`, "color:red");
				}
			});
		} else if (devtoolsState.vitePluginDetected) {
			const _baseUrl = target.__VUE_DEVTOOLS_OPEN_IN_EDITOR_BASE_URL__ ?? baseUrl;
			target.__VUE_INSPECTOR__.openInEditor(_baseUrl, file, line, column);
		}
	}
}
//#endregion
//#region ../devtools-kit/src/ctx/plugin.ts
target.__VUE_DEVTOOLS_KIT_PLUGIN_BUFFER__ ??= [];
const devtoolsPluginBuffer = new Proxy(target.__VUE_DEVTOOLS_KIT_PLUGIN_BUFFER__, { get(target, prop, receiver) {
	return Reflect.get(target, prop, receiver);
} });
//#endregion
//#region ../devtools-kit/src/core/plugin/plugin-settings.ts
function _getSettings(settings) {
	const _settings = {};
	Object.keys(settings).forEach((key) => {
		_settings[key] = settings[key].defaultValue;
	});
	return _settings;
}
function getPluginLocalKey(pluginId) {
	return `__VUE_DEVTOOLS_NEXT_PLUGIN_SETTINGS__${pluginId}__`;
}
function getPluginSettingsOptions(pluginId) {
	return (devtoolsPluginBuffer.find((item) => item[0].id === pluginId && !!item[0]?.settings)?.[0] ?? null)?.settings ?? null;
}
function getPluginSettings(pluginId, fallbackValue) {
	const localKey = getPluginLocalKey(pluginId);
	if (localKey) {
		const localSettings = localStorage.getItem(localKey);
		if (localSettings) return JSON.parse(localSettings);
	}
	if (pluginId) return _getSettings((devtoolsPluginBuffer.find((item) => item[0].id === pluginId)?.[0] ?? null)?.settings ?? {});
	return _getSettings(fallbackValue);
}
function initPluginSettings(pluginId, settings) {
	const localKey = getPluginLocalKey(pluginId);
	if (!localStorage.getItem(localKey)) localStorage.setItem(localKey, JSON.stringify(_getSettings(settings)));
}
function setPluginSettings(pluginId, key, value) {
	const localKey = getPluginLocalKey(pluginId);
	const localSettings = localStorage.getItem(localKey);
	const parsedLocalSettings = JSON.parse(localSettings || "{}");
	const updated = {
		...parsedLocalSettings,
		[key]: value
	};
	localStorage.setItem(localKey, JSON.stringify(updated));
	devtoolsContext.hooks.callHookWith((callbacks) => {
		callbacks.forEach((cb) => cb({
			pluginId,
			key,
			oldValue: parsedLocalSettings[key],
			newValue: value,
			settings: updated
		}));
	}, DevToolsV6PluginAPIHookKeys.SET_PLUGIN_SETTINGS);
}
//#endregion
//#region ../devtools-kit/src/types/hook.ts
let DevToolsHooks = /* @__PURE__ */ function(DevToolsHooks) {
	DevToolsHooks["APP_INIT"] = "app:init";
	DevToolsHooks["APP_UNMOUNT"] = "app:unmount";
	DevToolsHooks["COMPONENT_UPDATED"] = "component:updated";
	DevToolsHooks["COMPONENT_ADDED"] = "component:added";
	DevToolsHooks["COMPONENT_REMOVED"] = "component:removed";
	DevToolsHooks["COMPONENT_EMIT"] = "component:emit";
	DevToolsHooks["PERFORMANCE_START"] = "perf:start";
	DevToolsHooks["PERFORMANCE_END"] = "perf:end";
	DevToolsHooks["ADD_ROUTE"] = "router:add-route";
	DevToolsHooks["REMOVE_ROUTE"] = "router:remove-route";
	DevToolsHooks["RENDER_TRACKED"] = "render:tracked";
	DevToolsHooks["RENDER_TRIGGERED"] = "render:triggered";
	DevToolsHooks["APP_CONNECTED"] = "app:connected";
	DevToolsHooks["SETUP_DEVTOOLS_PLUGIN"] = "devtools-plugin:setup";
	return DevToolsHooks;
}({});
//#endregion
//#region ../devtools-kit/src/hook/index.ts
const devtoolsHooks = target.__VUE_DEVTOOLS_HOOK ??= createHooks();
const hook = {
	on: {
		vueAppInit(fn) {
			devtoolsHooks.hook(DevToolsHooks.APP_INIT, fn);
		},
		vueAppUnmount(fn) {
			devtoolsHooks.hook(DevToolsHooks.APP_UNMOUNT, fn);
		},
		vueAppConnected(fn) {
			devtoolsHooks.hook(DevToolsHooks.APP_CONNECTED, fn);
		},
		componentAdded(fn) {
			return devtoolsHooks.hook(DevToolsHooks.COMPONENT_ADDED, fn);
		},
		componentEmit(fn) {
			return devtoolsHooks.hook(DevToolsHooks.COMPONENT_EMIT, fn);
		},
		componentUpdated(fn) {
			return devtoolsHooks.hook(DevToolsHooks.COMPONENT_UPDATED, fn);
		},
		componentRemoved(fn) {
			return devtoolsHooks.hook(DevToolsHooks.COMPONENT_REMOVED, fn);
		},
		setupDevtoolsPlugin(fn) {
			devtoolsHooks.hook(DevToolsHooks.SETUP_DEVTOOLS_PLUGIN, fn);
		},
		perfStart(fn) {
			return devtoolsHooks.hook(DevToolsHooks.PERFORMANCE_START, fn);
		},
		perfEnd(fn) {
			return devtoolsHooks.hook(DevToolsHooks.PERFORMANCE_END, fn);
		}
	},
	setupDevToolsPlugin(pluginDescriptor, setupFn) {
		return devtoolsHooks.callHook(DevToolsHooks.SETUP_DEVTOOLS_PLUGIN, pluginDescriptor, setupFn);
	}
};
//#endregion
//#region ../devtools-kit/src/api/v6/index.ts
var DevToolsV6PluginAPI = class {
	constructor({ plugin, ctx }) {
		this.hooks = ctx.hooks;
		this.plugin = plugin;
	}
	get on() {
		return {
			visitComponentTree: (handler) => {
				this.hooks.hook(DevToolsV6PluginAPIHookKeys.VISIT_COMPONENT_TREE, handler);
			},
			inspectComponent: (handler) => {
				this.hooks.hook(DevToolsV6PluginAPIHookKeys.INSPECT_COMPONENT, handler);
			},
			editComponentState: (handler) => {
				this.hooks.hook(DevToolsV6PluginAPIHookKeys.EDIT_COMPONENT_STATE, handler);
			},
			getInspectorTree: (handler) => {
				this.hooks.hook(DevToolsV6PluginAPIHookKeys.GET_INSPECTOR_TREE, handler);
			},
			getInspectorState: (handler) => {
				this.hooks.hook(DevToolsV6PluginAPIHookKeys.GET_INSPECTOR_STATE, handler);
			},
			editInspectorState: (handler) => {
				this.hooks.hook(DevToolsV6PluginAPIHookKeys.EDIT_INSPECTOR_STATE, handler);
			},
			inspectTimelineEvent: (handler) => {
				this.hooks.hook(DevToolsV6PluginAPIHookKeys.INSPECT_TIMELINE_EVENT, handler);
			},
			timelineCleared: (handler) => {
				this.hooks.hook(DevToolsV6PluginAPIHookKeys.TIMELINE_CLEARED, handler);
			},
			setPluginSettings: (handler) => {
				this.hooks.hook(DevToolsV6PluginAPIHookKeys.SET_PLUGIN_SETTINGS, handler);
			}
		};
	}
	notifyComponentUpdate(instance) {
		if (devtoolsState.highPerfModeEnabled) return;
		const inspector = getActiveInspectors().find((i) => i.packageName === this.plugin.descriptor.packageName);
		if (inspector?.id) {
			if (instance) {
				const args = [
					instance.appContext.app,
					instance.uid,
					instance.parent?.uid,
					instance
				];
				devtoolsHooks.callHook(DevToolsHooks.COMPONENT_UPDATED, ...args);
			} else devtoolsHooks.callHook(DevToolsHooks.COMPONENT_UPDATED);
			this.hooks.callHook(DevToolsContextHookKeys.SEND_INSPECTOR_STATE, {
				inspectorId: inspector.id,
				plugin: this.plugin
			});
		}
	}
	addInspector(options) {
		this.hooks.callHook(DevToolsContextHookKeys.ADD_INSPECTOR, {
			inspector: options,
			plugin: this.plugin
		});
		if (this.plugin.descriptor.settings) initPluginSettings(options.id, this.plugin.descriptor.settings);
	}
	sendInspectorTree(inspectorId) {
		if (devtoolsState.highPerfModeEnabled) return;
		this.hooks.callHook(DevToolsContextHookKeys.SEND_INSPECTOR_TREE, {
			inspectorId,
			plugin: this.plugin
		});
	}
	sendInspectorState(inspectorId) {
		if (devtoolsState.highPerfModeEnabled) return;
		this.hooks.callHook(DevToolsContextHookKeys.SEND_INSPECTOR_STATE, {
			inspectorId,
			plugin: this.plugin
		});
	}
	selectInspectorNode(inspectorId, nodeId) {
		this.hooks.callHook(DevToolsContextHookKeys.CUSTOM_INSPECTOR_SELECT_NODE, {
			inspectorId,
			nodeId,
			plugin: this.plugin
		});
	}
	visitComponentTree(payload) {
		return this.hooks.callHook(DevToolsV6PluginAPIHookKeys.VISIT_COMPONENT_TREE, payload);
	}
	now() {
		if (devtoolsState.highPerfModeEnabled) return 0;
		return Date.now();
	}
	addTimelineLayer(options) {
		this.hooks.callHook(DevToolsContextHookKeys.TIMELINE_LAYER_ADDED, {
			options,
			plugin: this.plugin
		});
	}
	addTimelineEvent(options) {
		if (devtoolsState.highPerfModeEnabled) return;
		this.hooks.callHook(DevToolsContextHookKeys.TIMELINE_EVENT_ADDED, {
			options,
			plugin: this.plugin
		});
	}
	getSettings(pluginId) {
		return getPluginSettings(pluginId ?? this.plugin.descriptor.id, this.plugin.descriptor.settings);
	}
	getComponentInstances(app) {
		return this.hooks.callHook(DevToolsContextHookKeys.GET_COMPONENT_INSTANCES, { app });
	}
	getComponentBounds(instance) {
		return this.hooks.callHook(DevToolsContextHookKeys.GET_COMPONENT_BOUNDS, { instance });
	}
	getComponentName(instance) {
		return this.hooks.callHook(DevToolsContextHookKeys.GET_COMPONENT_NAME, { instance });
	}
	highlightElement(instance) {
		const uid = instance.__VUE_DEVTOOLS_NEXT_UID__;
		return this.hooks.callHook(DevToolsContextHookKeys.COMPONENT_HIGHLIGHT, { uid });
	}
	unhighlightElement() {
		return this.hooks.callHook(DevToolsContextHookKeys.COMPONENT_UNHIGHLIGHT);
	}
};
//#endregion
//#region ../devtools-kit/src/api/index.ts
const DevToolsPluginAPI = DevToolsV6PluginAPI;
//#endregion
//#region ../devtools-kit/src/core/plugin/index.ts
target.__VUE_DEVTOOLS_KIT__REGISTERED_PLUGIN_APPS__ ??= /* @__PURE__ */ new Set();
function setupDevToolsPlugin(pluginDescriptor, setupFn) {
	return hook.setupDevToolsPlugin(pluginDescriptor, setupFn);
}
function callDevToolsPluginSetupFn(plugin, app) {
	const [pluginDescriptor, setupFn] = plugin;
	if (pluginDescriptor.app !== app) return;
	const api = new DevToolsPluginAPI({
		plugin: {
			setupFn,
			descriptor: pluginDescriptor
		},
		ctx: devtoolsContext
	});
	if (pluginDescriptor.packageName === "vuex") api.on.editInspectorState((payload) => {
		api.sendInspectorState(payload.inspectorId);
	});
	setupFn(api);
}
function registerDevToolsPlugin(app, options) {
	if (target.__VUE_DEVTOOLS_KIT__REGISTERED_PLUGIN_APPS__.has(app)) return;
	if (devtoolsState.highPerfModeEnabled && !options?.inspectingComponent) return;
	target.__VUE_DEVTOOLS_KIT__REGISTERED_PLUGIN_APPS__.add(app);
	devtoolsPluginBuffer.forEach((plugin) => {
		callDevToolsPluginSetupFn(plugin, app);
	});
}
//#endregion
//#region ../devtools-kit/src/ctx/router.ts
const ROUTER_KEY = "__VUE_DEVTOOLS_ROUTER__";
const ROUTER_INFO_KEY = "__VUE_DEVTOOLS_ROUTER_INFO__";
target[ROUTER_INFO_KEY] ??= {
	currentRoute: null,
	routes: []
};
target[ROUTER_KEY] ??= {};
new Proxy(target[ROUTER_INFO_KEY], { get(target$1, property) {
	return target[ROUTER_INFO_KEY][property];
} });
new Proxy(target[ROUTER_KEY], { get(target$2, property) {
	if (property === "value") return target[ROUTER_KEY];
} });
//#endregion
//#region ../devtools-kit/src/core/router/index.ts
function getRoutes(router) {
	const routesMap = /* @__PURE__ */ new Map();
	return (router?.getRoutes() || []).filter((i) => !routesMap.has(i.path) && routesMap.set(i.path, 1));
}
function filterRoutes(routes) {
	return routes.map((item) => {
		let { path, name, children, meta } = item;
		if (children?.length) children = filterRoutes(children);
		return {
			path,
			name,
			children,
			meta
		};
	});
}
function filterCurrentRoute(route) {
	if (route) {
		const { fullPath, hash, href, path, name, matched, params, query } = route;
		return {
			fullPath,
			hash,
			href,
			path,
			name,
			params,
			query,
			matched: filterRoutes(matched)
		};
	}
	return route;
}
function normalizeRouterInfo(appRecord, activeAppRecord) {
	function init() {
		const router = appRecord.app?.config.globalProperties.$router;
		const currentRoute = filterCurrentRoute(router?.currentRoute.value);
		const routes = filterRoutes(getRoutes(router));
		const c = console.warn;
		console.warn = () => {};
		target[ROUTER_INFO_KEY] = {
			currentRoute: currentRoute ? deepClone(currentRoute) : {},
			routes: deepClone(routes)
		};
		target[ROUTER_KEY] = router;
		console.warn = c;
	}
	init();
	hook.on.componentUpdated(debounce(() => {
		if (activeAppRecord.value?.app !== appRecord.app) return;
		init();
		if (devtoolsState.highPerfModeEnabled) return;
		devtoolsContext.hooks.callHook(DevToolsMessagingHookKeys.ROUTER_INFO_UPDATED, { state: target[ROUTER_INFO_KEY] });
	}, 200));
}
//#endregion
//#region ../devtools-kit/src/ctx/api.ts
function createDevToolsApi(hooks) {
	return {
		async getInspectorTree(payload) {
			const _payload = {
				...payload,
				app: activeAppRecord.value.app,
				rootNodes: []
			};
			await new Promise((resolve) => {
				hooks.callHookWith(async (callbacks) => {
					await Promise.all(callbacks.map((cb) => cb(_payload)));
					resolve();
				}, DevToolsV6PluginAPIHookKeys.GET_INSPECTOR_TREE);
			});
			return _payload.rootNodes;
		},
		async getInspectorState(payload) {
			const _payload = {
				...payload,
				app: activeAppRecord.value.app,
				state: null
			};
			const ctx = { currentTab: `custom-inspector:${payload.inspectorId}` };
			await new Promise((resolve) => {
				hooks.callHookWith(async (callbacks) => {
					await Promise.all(callbacks.map((cb) => cb(_payload, ctx)));
					resolve();
				}, DevToolsV6PluginAPIHookKeys.GET_INSPECTOR_STATE);
			});
			return _payload.state;
		},
		editInspectorState(payload) {
			const stateEditor = new StateEditor();
			const _payload = {
				...payload,
				app: activeAppRecord.value.app,
				set: (obj, path = payload.path, value = payload.state.value, cb) => {
					stateEditor.set(obj, path, value, cb || stateEditor.createDefaultSetCallback(payload.state));
				}
			};
			hooks.callHookWith((callbacks) => {
				callbacks.forEach((cb) => cb(_payload));
			}, DevToolsV6PluginAPIHookKeys.EDIT_INSPECTOR_STATE);
		},
		sendInspectorState(inspectorId) {
			const inspector = getInspector(inspectorId);
			hooks.callHook(DevToolsContextHookKeys.SEND_INSPECTOR_STATE, {
				inspectorId,
				plugin: {
					descriptor: inspector.descriptor,
					setupFn: () => ({})
				}
			});
		},
		inspectComponentInspector() {
			return inspectComponentHighLighter();
		},
		cancelInspectComponentInspector() {
			return cancelInspectComponentHighLighter();
		},
		getComponentRenderCode(id) {
			const instance = getComponentInstance(activeAppRecord.value, id);
			if (instance) return !(typeof instance?.type === "function") ? instance.render.toString() : instance.type.toString();
		},
		scrollToComponent(id) {
			return scrollToComponent({ id });
		},
		openInEditor,
		getVueInspector: getComponentInspector,
		toggleApp(id, options) {
			const appRecord = devtoolsAppRecords.value.find((record) => record.id === id);
			if (appRecord) {
				setActiveAppRecordId(id);
				setActiveAppRecord(appRecord);
				normalizeRouterInfo(appRecord, activeAppRecord);
				callInspectorUpdatedHook();
				registerDevToolsPlugin(appRecord.app, options);
			}
		},
		inspectDOM(instanceId) {
			const instance = getComponentInstance(activeAppRecord.value, instanceId);
			if (instance) {
				const [el] = getRootElementsFromComponentInstance(instance);
				if (el) target.__VUE_DEVTOOLS_INSPECT_DOM_TARGET__ = el;
			}
		},
		updatePluginSettings(pluginId, key, value) {
			setPluginSettings(pluginId, key, value);
		},
		getPluginSettings(pluginId) {
			return {
				options: getPluginSettingsOptions(pluginId),
				values: getPluginSettings(pluginId)
			};
		}
	};
}
//#endregion
//#region ../devtools-kit/src/ctx/index.ts
const hooks = createDevToolsCtxHooks();
target.__VUE_DEVTOOLS_KIT_CONTEXT__ ??= {
	hooks,
	get state() {
		return {
			...devtoolsState,
			activeAppRecordId: activeAppRecord.id,
			activeAppRecord: activeAppRecord.value,
			appRecords: devtoolsAppRecords.value
		};
	},
	api: createDevToolsApi(hooks)
};
const devtoolsContext = target.__VUE_DEVTOOLS_KIT_CONTEXT__;
//#endregion
//#region ../devtools-kit/src/core/index.ts
function onDevToolsClientConnected(fn) {
	return new Promise((resolve) => {
		if (devtoolsState.connected && devtoolsState.clientConnected) {
			fn();
			resolve();
			return;
		}
		devtoolsContext.hooks.hook(DevToolsMessagingHookKeys.DEVTOOLS_CONNECTED_UPDATED, ({ state }) => {
			if (state.connected && state.clientConnected) {
				fn();
				resolve();
			}
		});
	});
}
//#endregion
export { addCustomCommand, addCustomTab, onDevToolsClientConnected, onDevToolsConnected, removeCustomCommand, setupDevToolsPlugin, setupDevToolsPlugin as setupDevtoolsPlugin };
