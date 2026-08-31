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
let _vue_devtools_shared = require("@vue/devtools-shared");
let perfect_debounce = require("perfect-debounce");
let hookable = require("hookable");
let birpc = require("birpc");
//#region src/compat/index.ts
function onLegacyDevToolsPluginApiAvailable(cb) {
	if (_vue_devtools_shared.target.__VUE_DEVTOOLS_PLUGIN_API_AVAILABLE__) {
		cb();
		return;
	}
	Object.defineProperty(_vue_devtools_shared.target, "__VUE_DEVTOOLS_PLUGIN_API_AVAILABLE__", {
		set(value) {
			if (value) cb();
		},
		configurable: true
	});
}
//#endregion
//#region src/core/component/utils/index.ts
function getComponentTypeName(options) {
	if (typeof options === "function") return options.displayName || options.name || options.__VUE_DEVTOOLS_COMPONENT_GUSSED_NAME__ || "";
	const name = options.name || options._componentTag || options.__VUE_DEVTOOLS_COMPONENT_GUSSED_NAME__ || options.__name;
	if (name === "index" && options.__file?.endsWith("index.vue")) return "";
	return name;
}
function getComponentFileName(options) {
	const file = options.__file;
	if (file) return (0, _vue_devtools_shared.classify)((0, _vue_devtools_shared.basename)(file, ".vue"));
}
function getComponentName(options) {
	const name = options.displayName || options.name || options._componentTag;
	if (name) return name;
	return getComponentFileName(options);
}
function saveComponentGussedName(instance, name) {
	instance.type.__VUE_DEVTOOLS_COMPONENT_GUSSED_NAME__ = name;
	return name;
}
function getAppRecord(instance) {
	if (instance.__VUE_DEVTOOLS_NEXT_APP_RECORD__) return instance.__VUE_DEVTOOLS_NEXT_APP_RECORD__;
	else if (instance.root) return instance.appContext.app.__VUE_DEVTOOLS_NEXT_APP_RECORD__;
}
async function getComponentId(options) {
	const { app, uid, instance } = options;
	try {
		if (instance.__VUE_DEVTOOLS_NEXT_UID__) return instance.__VUE_DEVTOOLS_NEXT_UID__;
		const appRecord = await getAppRecord(app);
		if (!appRecord) return null;
		const isRoot = appRecord.rootInstance === instance;
		return `${appRecord.id}:${isRoot ? "root" : uid}`;
	} catch (e) {}
}
function isFragment(instance) {
	const subTreeType = instance.subTree?.type;
	const appRecord = getAppRecord(instance);
	if (appRecord) return appRecord?.types?.Fragment === subTreeType;
	return false;
}
function isBeingDestroyed(instance) {
	return instance._isBeingDestroyed || instance.isUnmounted;
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
function getRenderKey(value) {
	if (value == null) return "";
	if (typeof value === "number") return value;
	else if (typeof value === "string") return `'${value}'`;
	else if (Array.isArray(value)) return "Array";
	else return "Object";
}
function returnError(cb) {
	try {
		return cb();
	} catch (e) {
		return e;
	}
}
function getComponentInstance(appRecord, instanceId) {
	instanceId = instanceId || `${appRecord.id}:root`;
	return appRecord.instanceMap.get(instanceId) || appRecord.instanceMap.get(":root");
}
function ensurePropertyExists(obj, key, skipObjCheck = false) {
	return skipObjCheck ? key in obj : typeof obj === "object" && obj !== null ? key in obj : false;
}
//#endregion
//#region src/core/component/state/bounding-rect.ts
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
//#region src/core/component/tree/el.ts
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
//#region src/core/component-highlighter/index.ts
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
//#region src/core/component-inspector/index.ts
_vue_devtools_shared.target.__VUE_DEVTOOLS_COMPONENT_INSPECTOR_ENABLED__ ??= true;
function toggleComponentInspectorEnabled(enabled) {
	_vue_devtools_shared.target.__VUE_DEVTOOLS_COMPONENT_INSPECTOR_ENABLED__ = enabled;
}
function waitForInspectorInit(cb) {
	let total = 0;
	const timer = setInterval(() => {
		if (_vue_devtools_shared.target.__VUE_INSPECTOR__) {
			clearInterval(timer);
			total += 30;
			cb();
		}
		if (total >= 5e3) clearInterval(timer);
	}, 30);
}
function setupInspector() {
	const inspector = _vue_devtools_shared.target.__VUE_INSPECTOR__;
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
			resolve(_vue_devtools_shared.target.__VUE_INSPECTOR__);
		}
		if (!_vue_devtools_shared.target.__VUE_INSPECTOR__) waitForInspectorInit(() => {
			setup();
		});
		else setup();
	});
}
//#endregion
//#region src/shared/stub-vue.ts
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
function isReactive$1(value) {
	if (isReadonly(value)) return isReactive$1(value[ReactiveFlags.RAW]);
	return !!(value && value[ReactiveFlags.IS_REACTIVE]);
}
function isRef$1(r) {
	return !!(r && r.__v_isRef === true);
}
/**
* @from [@vue/reactivity](https://github.com/vuejs/core/blob/1c3327a0fa5983aa9078e3f7bb2330f572435425/packages/reactivity/src/reactive.ts#L372-L375)
*/
function toRaw$1(observed) {
	const raw = observed && observed[ReactiveFlags.RAW];
	return raw ? toRaw$1(raw) : observed;
}
//#endregion
//#region src/core/component/state/editor.ts
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
			else if (toRaw$1(object) instanceof Map) object.delete(field);
			else if (toRaw$1(object) instanceof Set) object.delete(Array.from(object.values())[field]);
			else Reflect.deleteProperty(object, field);
			if (!state.remove) {
				const target = object[state.newKey || field];
				if (this.refEditor.isRef(target)) this.refEditor.set(target, value);
				else if (toRaw$1(object) instanceof Map) object.set(state.newKey || field, value);
				else if (toRaw$1(object) instanceof Set) object.add(value);
				else object[state.newKey || field] = value;
			}
		};
	}
};
var RefStateEditor = class {
	set(ref, value) {
		if (isRef$1(ref)) ref.value = value;
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
		return isRef$1(ref) ? ref.value : ref;
	}
	isRef(ref) {
		return isRef$1(ref) || isReactive$1(ref);
	}
};
async function editComponentState(payload, stateEditor) {
	const { path, nodeId, state, type } = payload;
	const instance = getComponentInstance(activeAppRecord.value, nodeId);
	if (!instance) return;
	const targetPath = path.slice();
	let target;
	if (Object.keys(instance.props).includes(path[0])) target = instance.props;
	else if (instance.devtoolsRawSetupState && Object.keys(instance.devtoolsRawSetupState).includes(path[0])) target = instance.devtoolsRawSetupState;
	else if (instance.data && Object.keys(instance.data).includes(path[0])) target = instance.data;
	else target = instance.proxy;
	if (target && targetPath) {
		if (state.type === "object" && type === "reactive") {}
		stateEditor.set(target, targetPath, state.value, stateEditor.createDefaultSetCallback(state));
	}
}
const stateEditor = new StateEditor();
async function editState(payload) {
	editComponentState(payload, stateEditor);
}
//#endregion
//#region src/core/timeline/storage.ts
const TIMELINE_LAYERS_STATE_STORAGE_ID = "__VUE_DEVTOOLS_KIT_TIMELINE_LAYERS_STATE__";
function addTimelineLayersStateToStorage(state) {
	if (!_vue_devtools_shared.isBrowser || typeof localStorage === "undefined" || localStorage === null) return;
	localStorage.setItem(TIMELINE_LAYERS_STATE_STORAGE_ID, JSON.stringify(state));
}
function getTimelineLayersStateFromStorage() {
	if (typeof window === "undefined" || !_vue_devtools_shared.isBrowser || typeof localStorage === "undefined" || localStorage === null) return {
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
//#region src/ctx/timeline.ts
_vue_devtools_shared.target.__VUE_DEVTOOLS_KIT_TIMELINE_LAYERS ??= [];
const devtoolsTimelineLayers = new Proxy(_vue_devtools_shared.target.__VUE_DEVTOOLS_KIT_TIMELINE_LAYERS, { get(target, prop, receiver) {
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
function updateTimelineLayersState(state) {
	const updatedState = {
		...devtoolsState.timelineLayersState,
		...state
	};
	addTimelineLayersStateToStorage(updatedState);
	updateDevToolsState({ timelineLayersState: updatedState });
}
//#endregion
//#region src/ctx/inspector.ts
_vue_devtools_shared.target.__VUE_DEVTOOLS_KIT_INSPECTOR__ ??= [];
const devtoolsInspector = new Proxy(_vue_devtools_shared.target.__VUE_DEVTOOLS_KIT_INSPECTOR__, { get(target, prop, receiver) {
	return Reflect.get(target, prop, receiver);
} });
const callInspectorUpdatedHook = (0, perfect_debounce.debounce)(() => {
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
function getInspectorInfo(id) {
	const inspector = getInspector(id, activeAppRecord.value.app);
	if (!inspector) return;
	const descriptor = inspector.descriptor;
	const options = inspector.options;
	const timelineLayers = devtoolsTimelineLayers.filter((layer) => layer.descriptorId === descriptor.id).map((item) => ({
		id: item.id,
		label: item.label,
		color: item.color
	}));
	return {
		id: options.id,
		label: options.label,
		logo: descriptor.logo,
		packageName: descriptor.packageName,
		homepage: descriptor.homepage,
		timelineLayers,
		treeFilterPlaceholder: inspector.treeFilterPlaceholder,
		stateFilterPlaceholder: inspector.stateFilterPlaceholder
	};
}
function getInspector(id, app) {
	return devtoolsInspector.find((inspector) => inspector.options.id === id && (app ? inspector.descriptor.app === app : true));
}
function getInspectorActions(id) {
	return getInspector(id)?.options.actions;
}
function getInspectorNodeActions(id) {
	return getInspector(id)?.options.nodeActions;
}
//#endregion
//#region src/ctx/hook.ts
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
	const hooks = (0, hookable.createHooks)();
	hooks.hook(DevToolsContextHookKeys.ADD_INSPECTOR, ({ inspector, plugin }) => {
		addInspector(inspector, plugin.descriptor);
	});
	const debounceSendInspectorTree = (0, perfect_debounce.debounce)(async ({ inspectorId, plugin }) => {
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
	const debounceSendInspectorState = (0, perfect_debounce.debounce)(async ({ inspectorId, plugin }) => {
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
//#region src/ctx/state.ts
_vue_devtools_shared.target.__VUE_DEVTOOLS_KIT_APP_RECORDS__ ??= [];
_vue_devtools_shared.target.__VUE_DEVTOOLS_KIT_ACTIVE_APP_RECORD__ ??= {};
_vue_devtools_shared.target.__VUE_DEVTOOLS_KIT_ACTIVE_APP_RECORD_ID__ ??= "";
_vue_devtools_shared.target.__VUE_DEVTOOLS_KIT_CUSTOM_TABS__ ??= [];
_vue_devtools_shared.target.__VUE_DEVTOOLS_KIT_CUSTOM_COMMANDS__ ??= [];
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
_vue_devtools_shared.target[STATE_KEY] ??= initStateFactory();
const callStateUpdatedHook = (0, perfect_debounce.debounce)((state) => {
	devtoolsContext.hooks.callHook(DevToolsMessagingHookKeys.DEVTOOLS_STATE_UPDATED, { state });
});
const callConnectedUpdatedHook = (0, perfect_debounce.debounce)((state, oldState) => {
	devtoolsContext.hooks.callHook(DevToolsMessagingHookKeys.DEVTOOLS_CONNECTED_UPDATED, {
		state,
		oldState
	});
});
const devtoolsAppRecords = new Proxy(_vue_devtools_shared.target.__VUE_DEVTOOLS_KIT_APP_RECORDS__, { get(_target, prop, receiver) {
	if (prop === "value") return _vue_devtools_shared.target.__VUE_DEVTOOLS_KIT_APP_RECORDS__;
	return _vue_devtools_shared.target.__VUE_DEVTOOLS_KIT_APP_RECORDS__[prop];
} });
const addDevToolsAppRecord = (app) => {
	_vue_devtools_shared.target.__VUE_DEVTOOLS_KIT_APP_RECORDS__ = [..._vue_devtools_shared.target.__VUE_DEVTOOLS_KIT_APP_RECORDS__, app];
};
const removeDevToolsAppRecord = (app) => {
	_vue_devtools_shared.target.__VUE_DEVTOOLS_KIT_APP_RECORDS__ = devtoolsAppRecords.value.filter((record) => record.app !== app);
};
const activeAppRecord = new Proxy(_vue_devtools_shared.target.__VUE_DEVTOOLS_KIT_ACTIVE_APP_RECORD__, { get(_target, prop, receiver) {
	if (prop === "value") return _vue_devtools_shared.target.__VUE_DEVTOOLS_KIT_ACTIVE_APP_RECORD__;
	else if (prop === "id") return _vue_devtools_shared.target.__VUE_DEVTOOLS_KIT_ACTIVE_APP_RECORD_ID__;
	return _vue_devtools_shared.target.__VUE_DEVTOOLS_KIT_ACTIVE_APP_RECORD__[prop];
} });
function updateAllStates() {
	callStateUpdatedHook({
		..._vue_devtools_shared.target[STATE_KEY],
		appRecords: devtoolsAppRecords.value,
		activeAppRecordId: activeAppRecord.id,
		tabs: _vue_devtools_shared.target.__VUE_DEVTOOLS_KIT_CUSTOM_TABS__,
		commands: _vue_devtools_shared.target.__VUE_DEVTOOLS_KIT_CUSTOM_COMMANDS__
	});
}
function setActiveAppRecord(app) {
	_vue_devtools_shared.target.__VUE_DEVTOOLS_KIT_ACTIVE_APP_RECORD__ = app;
	updateAllStates();
}
function setActiveAppRecordId(id) {
	_vue_devtools_shared.target.__VUE_DEVTOOLS_KIT_ACTIVE_APP_RECORD_ID__ = id;
	updateAllStates();
}
const devtoolsState = new Proxy(_vue_devtools_shared.target[STATE_KEY], {
	get(target, property) {
		if (property === "appRecords") return devtoolsAppRecords;
		else if (property === "activeAppRecordId") return activeAppRecord.id;
		else if (property === "tabs") return _vue_devtools_shared.target.__VUE_DEVTOOLS_KIT_CUSTOM_TABS__;
		else if (property === "commands") return _vue_devtools_shared.target.__VUE_DEVTOOLS_KIT_CUSTOM_COMMANDS__;
		return _vue_devtools_shared.target[STATE_KEY][property];
	},
	deleteProperty(target, property) {
		delete target[property];
		return true;
	},
	set(target, property, value) {
		target[property] = value;
		_vue_devtools_shared.target[STATE_KEY][property] = value;
		return true;
	}
});
function resetDevToolsState() {
	Object.assign(_vue_devtools_shared.target[STATE_KEY], initStateFactory());
}
function updateDevToolsState(state) {
	const oldState = {
		..._vue_devtools_shared.target[STATE_KEY],
		appRecords: devtoolsAppRecords.value,
		activeAppRecordId: activeAppRecord.id
	};
	if (oldState.connected !== state.connected && state.connected || oldState.clientConnected !== state.clientConnected && state.clientConnected) callConnectedUpdatedHook(_vue_devtools_shared.target[STATE_KEY], oldState);
	Object.assign(_vue_devtools_shared.target[STATE_KEY], state);
	updateAllStates();
}
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
	if (icon.startsWith("i-") || (0, _vue_devtools_shared.isUrlString)(icon)) return icon;
	return `custom-ic-baseline-${icon}`;
};
function addCustomTab(tab) {
	const tabs = _vue_devtools_shared.target.__VUE_DEVTOOLS_KIT_CUSTOM_TABS__;
	if (tabs.some((t) => t.name === tab.name)) return;
	tabs.push({
		...tab,
		icon: resolveIcon(tab.icon)
	});
	updateAllStates();
}
function addCustomCommand(action) {
	const commands = _vue_devtools_shared.target.__VUE_DEVTOOLS_KIT_CUSTOM_COMMANDS__;
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
	const commands = _vue_devtools_shared.target.__VUE_DEVTOOLS_KIT_CUSTOM_COMMANDS__;
	const index = commands.findIndex((t) => t.id === actionId);
	if (index === -1) return;
	commands.splice(index, 1);
	updateAllStates();
}
function toggleClientConnected(state) {
	updateDevToolsState({ clientConnected: state });
}
//#endregion
//#region src/core/open-in-editor/index.ts
function setOpenInEditorBaseUrl(url) {
	_vue_devtools_shared.target.__VUE_DEVTOOLS_OPEN_IN_EDITOR_BASE_URL__ = url;
}
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
			const _baseUrl = _vue_devtools_shared.target.__VUE_DEVTOOLS_OPEN_IN_EDITOR_BASE_URL__ ?? baseUrl;
			_vue_devtools_shared.target.__VUE_INSPECTOR__.openInEditor(_baseUrl, file, line, column);
		}
	}
}
//#endregion
//#region src/ctx/plugin.ts
_vue_devtools_shared.target.__VUE_DEVTOOLS_KIT_PLUGIN_BUFFER__ ??= [];
const devtoolsPluginBuffer = new Proxy(_vue_devtools_shared.target.__VUE_DEVTOOLS_KIT_PLUGIN_BUFFER__, { get(target, prop, receiver) {
	return Reflect.get(target, prop, receiver);
} });
function addDevToolsPluginToBuffer(pluginDescriptor, setupFn) {
	devtoolsPluginBuffer.push([pluginDescriptor, setupFn]);
}
//#endregion
//#region src/core/plugin/plugin-settings.ts
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
//#region src/types/hook.ts
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
//#region src/hook/index.ts
const devtoolsHooks = _vue_devtools_shared.target.__VUE_DEVTOOLS_HOOK ??= (0, hookable.createHooks)();
const on = {
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
};
function createDevToolsHook() {
	return {
		id: "vue-devtools-next",
		devtoolsVersion: "7.0",
		enabled: false,
		appRecords: [],
		apps: [],
		events: /* @__PURE__ */ new Map(),
		on(event, fn) {
			if (!this.events.has(event)) this.events.set(event, []);
			this.events.get(event)?.push(fn);
			return () => this.off(event, fn);
		},
		once(event, fn) {
			const onceFn = (...args) => {
				this.off(event, onceFn);
				fn(...args);
			};
			this.on(event, onceFn);
			return [event, onceFn];
		},
		off(event, fn) {
			if (this.events.has(event)) {
				const eventCallbacks = this.events.get(event);
				const index = eventCallbacks.indexOf(fn);
				if (index !== -1) eventCallbacks.splice(index, 1);
			}
		},
		emit(event, ...payload) {
			if (this.events.has(event)) this.events.get(event).forEach((fn) => fn(...payload));
		}
	};
}
function subscribeDevToolsHook(hook) {
	hook.on(DevToolsHooks.APP_INIT, (app, version, types) => {
		if (app?._instance?.type?.devtools?.hide) return;
		devtoolsHooks.callHook(DevToolsHooks.APP_INIT, app, version, types);
	});
	hook.on(DevToolsHooks.APP_UNMOUNT, (app) => {
		devtoolsHooks.callHook(DevToolsHooks.APP_UNMOUNT, app);
	});
	hook.on(DevToolsHooks.COMPONENT_ADDED, async (app, uid, parentUid, component) => {
		if (app?._instance?.type?.devtools?.hide || devtoolsState.highPerfModeEnabled) return;
		if (!app || typeof uid !== "number" && !uid || !component) return;
		devtoolsHooks.callHook(DevToolsHooks.COMPONENT_ADDED, app, uid, parentUid, component);
	});
	hook.on(DevToolsHooks.COMPONENT_UPDATED, (app, uid, parentUid, component) => {
		if (!app || typeof uid !== "number" && !uid || !component || devtoolsState.highPerfModeEnabled) return;
		devtoolsHooks.callHook(DevToolsHooks.COMPONENT_UPDATED, app, uid, parentUid, component);
	});
	hook.on(DevToolsHooks.COMPONENT_REMOVED, async (app, uid, parentUid, component) => {
		if (!app || typeof uid !== "number" && !uid || !component || devtoolsState.highPerfModeEnabled) return;
		devtoolsHooks.callHook(DevToolsHooks.COMPONENT_REMOVED, app, uid, parentUid, component);
	});
	hook.on(DevToolsHooks.COMPONENT_EMIT, async (app, instance, event, params) => {
		if (!app || !instance || devtoolsState.highPerfModeEnabled) return;
		devtoolsHooks.callHook(DevToolsHooks.COMPONENT_EMIT, app, instance, event, params);
	});
	hook.on(DevToolsHooks.PERFORMANCE_START, (app, uid, vm, type, time) => {
		if (!app || devtoolsState.highPerfModeEnabled) return;
		devtoolsHooks.callHook(DevToolsHooks.PERFORMANCE_START, app, uid, vm, type, time);
	});
	hook.on(DevToolsHooks.PERFORMANCE_END, (app, uid, vm, type, time) => {
		if (!app || devtoolsState.highPerfModeEnabled) return;
		devtoolsHooks.callHook(DevToolsHooks.PERFORMANCE_END, app, uid, vm, type, time);
	});
	hook.on(DevToolsHooks.SETUP_DEVTOOLS_PLUGIN, (pluginDescriptor, setupFn, options) => {
		if (options?.target === "legacy") return;
		devtoolsHooks.callHook(DevToolsHooks.SETUP_DEVTOOLS_PLUGIN, pluginDescriptor, setupFn);
	});
}
const hook = {
	on,
	setupDevToolsPlugin(pluginDescriptor, setupFn) {
		return devtoolsHooks.callHook(DevToolsHooks.SETUP_DEVTOOLS_PLUGIN, pluginDescriptor, setupFn);
	}
};
//#endregion
//#region src/api/v6/index.ts
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
//#region src/api/index.ts
const DevToolsPluginAPI = DevToolsV6PluginAPI;
//#endregion
//#region src/core/component/state/constants.ts
const vueBuiltins = new Set([
	"nextTick",
	"defineComponent",
	"defineAsyncComponent",
	"defineCustomElement",
	"ref",
	"computed",
	"reactive",
	"readonly",
	"watchEffect",
	"watchPostEffect",
	"watchSyncEffect",
	"watch",
	"isRef",
	"unref",
	"toRef",
	"toRefs",
	"isProxy",
	"isReactive",
	"isReadonly",
	"shallowRef",
	"triggerRef",
	"customRef",
	"shallowReactive",
	"shallowReadonly",
	"toRaw",
	"markRaw",
	"effectScope",
	"getCurrentScope",
	"onScopeDispose",
	"onMounted",
	"onUpdated",
	"onUnmounted",
	"onBeforeMount",
	"onBeforeUpdate",
	"onBeforeUnmount",
	"onErrorCaptured",
	"onRenderTracked",
	"onRenderTriggered",
	"onActivated",
	"onDeactivated",
	"onServerPrefetch",
	"provide",
	"inject",
	"h",
	"mergeProps",
	"cloneVNode",
	"isVNode",
	"resolveComponent",
	"resolveDirective",
	"withDirectives",
	"withModifiers"
]);
const symbolRE = /^\[native Symbol Symbol\((.*)\)\]$/;
const rawTypeRE = /^\[object (\w+)\]$/;
const specialTypeRE = /^\[native (\w+) (.*?)(<>(([\s\S])*))?\]$/;
const fnTypeRE = /^(?:function|class) (\w+)/;
const MAX_STRING_SIZE = 1e4;
const MAX_ARRAY_SIZE = 5e3;
const UNDEFINED = "__vue_devtool_undefined__";
const INFINITY = "__vue_devtool_infinity__";
const NEGATIVE_INFINITY = "__vue_devtool_negative_infinity__";
const NAN = "__vue_devtool_nan__";
const ESC = {
	"<": "&lt;",
	">": "&gt;",
	"\"": "&quot;",
	"&": "&amp;"
};
//#endregion
//#region src/core/component/state/is.ts
function isVueInstance(value) {
	if (!ensurePropertyExists(value, "_")) return false;
	if (!isPlainObject(value._)) return false;
	return Object.keys(value._).includes("vnode");
}
function isPlainObject(obj) {
	return Object.prototype.toString.call(obj) === "[object Object]";
}
function isPrimitive$1(data) {
	if (data == null) return true;
	const type = typeof data;
	return type === "string" || type === "number" || type === "boolean";
}
function isRef(raw) {
	return !!raw.__v_isRef;
}
function isComputed(raw) {
	return isRef(raw) && !!raw.effect;
}
function isReactive(raw) {
	return !!raw.__v_isReactive;
}
function isReadOnly(raw) {
	return !!raw.__v_isReadonly;
}
//#endregion
//#region src/core/component/state/util.ts
const tokenMap = {
	[UNDEFINED]: "undefined",
	[NAN]: "NaN",
	[INFINITY]: "Infinity",
	[NEGATIVE_INFINITY]: "-Infinity"
};
const reversedTokenMap = Object.entries(tokenMap).reduce((acc, [key, value]) => {
	acc[value] = key;
	return acc;
}, {});
function internalStateTokenToString(value) {
	if (value === null) return "null";
	return typeof value === "string" && tokenMap[value] || false;
}
function replaceTokenToString(value) {
	const replaceRegex = new RegExp(`"(${Object.keys(tokenMap).join("|")})"`, "g");
	return value.replace(replaceRegex, (_, g1) => tokenMap[g1]);
}
function replaceStringToToken(value) {
	const literalValue = reversedTokenMap[value.trim()];
	if (literalValue) return `"${literalValue}"`;
	const replaceRegex = new RegExp(`:\\s*(${Object.keys(reversedTokenMap).join("|")})`, "g");
	return value.replace(replaceRegex, (_, g1) => `:"${reversedTokenMap[g1]}"`);
}
/**
* Convert prop type constructor to string.
*/
function getPropType(type) {
	if (Array.isArray(type)) return type.map((t) => getPropType(t)).join(" or ");
	if (type == null) return "null";
	const match = type.toString().match(fnTypeRE);
	return typeof type === "function" ? match && match[1] || "any" : "any";
}
/**
* Sanitize data to be posted to the other side.
* Since the message posted is sent with structured clone,
* we need to filter out any types that might cause an error.
*/
function sanitize(data) {
	if (!isPrimitive$1(data) && !Array.isArray(data) && !isPlainObject(data)) return Object.prototype.toString.call(data);
	else return data;
}
function getSetupStateType(raw) {
	try {
		return {
			ref: isRef(raw),
			computed: isComputed(raw),
			reactive: isReactive(raw),
			readonly: isReadOnly(raw)
		};
	} catch {
		return {
			ref: false,
			computed: false,
			reactive: false,
			readonly: false
		};
	}
}
function toRaw(value) {
	if (value?.__v_raw) return value.__v_raw;
	return value;
}
function escape(s) {
	return s.replace(/[<>"&]/g, (s) => {
		return ESC[s] || s;
	});
}
//#endregion
//#region src/core/component/state/process.ts
function mergeOptions(to, from, instance) {
	if (typeof from === "function") from = from.options;
	if (!from) return to;
	const { mixins, extends: extendsOptions } = from;
	extendsOptions && mergeOptions(to, extendsOptions, instance);
	mixins && mixins.forEach((m) => mergeOptions(to, m, instance));
	for (const key of ["computed", "inject"]) if (Object.prototype.hasOwnProperty.call(from, key)) {
		to[key] ??= {};
		Object.assign(to[key], from[key]);
	}
	return to;
}
function resolveMergedOptions(instance) {
	const raw = instance?.type;
	if (!raw) return {};
	const { mixins, extends: extendsOptions } = raw;
	const globalMixins = instance.appContext.mixins;
	if (!globalMixins.length && !mixins && !extendsOptions) return raw;
	const options = {};
	globalMixins.forEach((m) => mergeOptions(options, m, instance));
	mergeOptions(options, raw, instance);
	return options;
}
/**
* Process the props of an instance.
* Make sure return a plain object because window.postMessage()
* will throw an Error if the passed object contains Functions.
*
*/
function processProps(instance) {
	const props = [];
	const propDefinitions = instance?.type?.props;
	for (const key in instance?.props) {
		const propDefinition = propDefinitions ? propDefinitions[key] : null;
		const camelizeKey = (0, _vue_devtools_shared.camelize)(key);
		props.push({
			type: "props",
			key: camelizeKey,
			value: returnError(() => instance.props[key]),
			editable: true,
			meta: propDefinition ? {
				type: propDefinition.type ? getPropType(propDefinition.type) : "any",
				required: !!propDefinition.required,
				...propDefinition.default ? { default: propDefinition.default.toString() } : {}
			} : { type: "invalid" }
		});
	}
	return props;
}
/**
* Process state, filtering out props and "clean" the result
* with a JSON dance. This removes functions which can cause
* errors during structured clone used by window.postMessage.
*
*/
function processState(instance) {
	const type = instance.type;
	const props = type?.props;
	const getters = type.vuex && type.vuex.getters;
	const computedDefs = type.computed;
	const data = {
		...instance.data,
		...instance.renderContext
	};
	return Object.keys(data).filter((key) => !(props && key in props) && !(getters && key in getters) && !(computedDefs && key in computedDefs)).map((key) => ({
		key,
		type: "data",
		value: returnError(() => data[key]),
		editable: true
	}));
}
function getStateTypeAndName(info) {
	const stateType = info.computed ? "computed" : info.ref ? "ref" : info.reactive ? "reactive" : null;
	return {
		stateType,
		stateTypeName: stateType ? `${stateType.charAt(0).toUpperCase()}${stateType.slice(1)}` : null
	};
}
function processSetupState(instance) {
	const raw = instance.devtoolsRawSetupState || {};
	return Object.keys(instance.setupState).filter((key) => !vueBuiltins.has(key) && key.split(/(?=[A-Z])/)[0] !== "use").map((key) => {
		const value = returnError(() => toRaw(instance.setupState[key]));
		const accessError = value instanceof Error;
		const rawData = raw[key];
		let result;
		let isOtherType = accessError || typeof value === "function" || ensurePropertyExists(value, "render") && typeof value.render === "function" || ensurePropertyExists(value, "__asyncLoader") && typeof value.__asyncLoader === "function" || typeof value === "object" && value && ("setup" in value || "props" in value) || /^v[A-Z]/.test(key);
		if (rawData && !accessError) {
			const info = getSetupStateType(rawData);
			const { stateType, stateTypeName } = getStateTypeAndName(info);
			const isState = info.ref || info.computed || info.reactive;
			const raw = ensurePropertyExists(rawData, "effect") ? rawData.effect?.raw?.toString() || rawData.effect?.fn?.toString() : null;
			if (stateType) isOtherType = false;
			result = {
				...stateType ? {
					stateType,
					stateTypeName
				} : {},
				...raw ? { raw } : {},
				editable: isState && !info.readonly
			};
		}
		return {
			key,
			value,
			type: isOtherType ? "setup (other)" : "setup",
			...result
		};
	});
}
/**
* Process the computed properties of an instance.
*/
function processComputed(instance, mergedType) {
	const type = mergedType;
	const computed = [];
	const defs = type.computed || {};
	for (const key in defs) {
		const def = defs[key];
		const type = typeof def === "function" && def.vuex ? "vuex bindings" : "computed";
		computed.push({
			type,
			key,
			value: returnError(() => instance?.proxy?.[key]),
			editable: typeof def.set === "function"
		});
	}
	return computed;
}
function processAttrs(instance) {
	return Object.keys(instance.attrs).map((key) => ({
		type: "attrs",
		key,
		value: returnError(() => instance.attrs[key])
	}));
}
function processProvide(instance) {
	return Reflect.ownKeys(instance.provides).map((key) => ({
		type: "provided",
		key: key.toString(),
		value: returnError(() => instance.provides[key])
	}));
}
function processInject(instance, mergedType) {
	if (!mergedType?.inject) return [];
	let keys = [];
	let defaultValue;
	if (Array.isArray(mergedType.inject)) keys = mergedType.inject.map((key) => ({
		key,
		originalKey: key
	}));
	else keys = Reflect.ownKeys(mergedType.inject).map((key) => {
		const value = mergedType.inject[key];
		let originalKey;
		if (typeof value === "string" || typeof value === "symbol") originalKey = value;
		else {
			originalKey = value.from;
			defaultValue = value.default;
		}
		return {
			key,
			originalKey
		};
	});
	return keys.map(({ key, originalKey }) => ({
		type: "injected",
		key: originalKey && key !== originalKey ? `${originalKey.toString()} ➞ ${key.toString()}` : key.toString(),
		value: returnError(() => instance.ctx.hasOwnProperty(key) ? instance.ctx[key] : instance.provides.hasOwnProperty(originalKey) ? instance.provides[originalKey] : defaultValue)
	}));
}
function processRefs(instance) {
	return Object.keys(instance.refs).map((key) => ({
		type: "template refs",
		key,
		value: returnError(() => instance.refs[key])
	}));
}
const vnodeEvents = new Set([
	"vnode-before-mount",
	"vnode-mounted",
	"vnode-before-update",
	"vnode-updated",
	"vnode-before-unmount",
	"vnode-unmounted"
]);
function processEventListeners(instance) {
	const emitsDefinition = instance.type.emits;
	const declaredEmits = Array.isArray(emitsDefinition) ? emitsDefinition : Object.keys(emitsDefinition ?? {});
	const keys = Object.keys(instance?.vnode?.props ?? {});
	const result = [];
	for (const key of keys) {
		const [prefix, ...eventNameParts] = key.split(/(?=[A-Z])/);
		if (prefix === "on") {
			const eventName = eventNameParts.join("-").toLowerCase();
			const isBuiltIn = vnodeEvents.has(eventName);
			const isDeclared = declaredEmits.includes(eventName) || declaredEmits.includes((0, _vue_devtools_shared.camelize)(eventName));
			const text = isBuiltIn ? "✅ Built-in" : isDeclared ? "✅ Declared" : "⚠️ Not declared";
			result.push({
				type: "event listeners",
				key: eventName,
				value: { _custom: {
					displayText: text,
					key: text,
					value: text,
					tooltipText: isBuiltIn ? `The event <code>${escape(eventName)}</code> is part of Vue and doesn't need to be declared by the component` : !isDeclared ? `The event <code>${escape(eventName)}</code> is not declared in the <code>emits</code> option. It will leak into the component's attributes (<code>$attrs</code>).` : null
				} }
			});
		}
	}
	return result;
}
function processInstanceState(instance) {
	const mergedType = resolveMergedOptions(instance);
	return processProps(instance).concat(processState(instance), processSetupState(instance), processComputed(instance, mergedType), processAttrs(instance), processProvide(instance), processInject(instance, mergedType), processRefs(instance), processEventListeners(instance));
}
//#endregion
//#region src/core/component/state/index.ts
function getInstanceState(params) {
	const instance = getComponentInstance(activeAppRecord.value, params.instanceId);
	return {
		id: getUniqueComponentId(instance),
		name: getInstanceName(instance),
		file: instance?.type?.__file,
		state: processInstanceState(instance),
		instance
	};
}
//#endregion
//#region src/core/component/tree/filter.ts
var ComponentFilter = class {
	constructor(filter) {
		this.filter = filter || "";
	}
	/**
	* Check if an instance is qualified.
	*
	* @param {Vue|Vnode} instance
	* @return {boolean}
	*/
	isQualified(instance) {
		const name = getInstanceName(instance);
		return (0, _vue_devtools_shared.classify)(name).toLowerCase().includes(this.filter) || (0, _vue_devtools_shared.kebabize)(name).toLowerCase().includes(this.filter);
	}
};
function createComponentFilter(filterText) {
	return new ComponentFilter(filterText);
}
//#endregion
//#region src/core/component/tree/walker.ts
var ComponentWalker = class {
	constructor(options) {
		this.captureIds = /* @__PURE__ */ new Map();
		const { filterText = "", maxDepth, recursively, api } = options;
		this.componentFilter = createComponentFilter(filterText);
		this.maxDepth = maxDepth;
		this.recursively = recursively;
		this.api = api;
	}
	getComponentTree(instance) {
		this.captureIds = /* @__PURE__ */ new Map();
		return this.findQualifiedChildren(instance, 0);
	}
	getComponentParents(instance) {
		this.captureIds = /* @__PURE__ */ new Map();
		const parents = [];
		this.captureId(instance);
		let parent = instance;
		while (parent = parent.parent) {
			this.captureId(parent);
			parents.push(parent);
		}
		return parents;
	}
	captureId(instance) {
		if (!instance) return null;
		const id = instance.__VUE_DEVTOOLS_NEXT_UID__ != null ? instance.__VUE_DEVTOOLS_NEXT_UID__ : getUniqueComponentId(instance);
		instance.__VUE_DEVTOOLS_NEXT_UID__ = id;
		if (this.captureIds.has(id)) return null;
		else this.captureIds.set(id, void 0);
		this.mark(instance);
		return id;
	}
	/**
	* Capture the meta information of an instance. (recursive)
	*
	* @param {Vue} instance
	* @return {object}
	*/
	async capture(instance, depth) {
		if (!instance) return null;
		const id = this.captureId(instance);
		const name = getInstanceName(instance);
		const children = this.getInternalInstanceChildren(instance.subTree).filter((child) => !isBeingDestroyed(child));
		const parents = this.getComponentParents(instance) || [];
		const inactive = !!instance.isDeactivated || parents.some((parent) => parent.isDeactivated);
		const treeNode = {
			uid: instance.uid,
			id,
			name,
			renderKey: getRenderKey(instance.vnode ? instance.vnode.key : null),
			inactive,
			children: [],
			hasChildren: !!children.length,
			isFragment: isFragment(instance),
			tags: typeof instance.type !== "function" ? [] : [{
				label: "functional",
				textColor: 5592405,
				backgroundColor: 15658734
			}],
			autoOpen: this.recursively,
			file: instance.type.__file || ""
		};
		if (depth < this.maxDepth || instance.type.__isKeepAlive || parents.some((parent) => parent.type.__isKeepAlive)) treeNode.children = await Promise.all(children.map((child) => this.capture(child, depth + 1)).filter(Boolean));
		if (this.isKeepAlive(instance)) {
			const cachedComponents = this.getKeepAliveCachedInstances(instance);
			const childrenIds = children.map((child) => child.__VUE_DEVTOOLS_NEXT_UID__);
			for (const cachedChild of cachedComponents) if (!childrenIds.includes(cachedChild.__VUE_DEVTOOLS_NEXT_UID__)) {
				const node = await this.capture({
					...cachedChild,
					isDeactivated: true
				}, depth + 1);
				if (node) treeNode.children.push(node);
			}
		}
		const firstElement = getRootElementsFromComponentInstance(instance)[0];
		if (firstElement?.parentElement) {
			const parentInstance = instance.parent;
			const parentRootElements = parentInstance ? getRootElementsFromComponentInstance(parentInstance) : [];
			let el = firstElement;
			const indexList = [];
			do {
				indexList.push(Array.from(el.parentElement.childNodes).indexOf(el));
				el = el.parentElement;
			} while (el.parentElement && parentRootElements.length && !parentRootElements.includes(el));
			treeNode.domOrder = indexList.reverse();
		} else treeNode.domOrder = [-1];
		if (instance.suspense?.suspenseKey) {
			treeNode.tags.push({
				label: instance.suspense.suspenseKey,
				backgroundColor: 14979812,
				textColor: 16777215
			});
			this.mark(instance, true);
		}
		this.api.visitComponentTree({
			treeNode,
			componentInstance: instance,
			app: instance.appContext.app,
			filter: this.componentFilter.filter
		});
		return treeNode;
	}
	/**
	* Find qualified children from a single instance.
	* If the instance itself is qualified, just return itself.
	* This is ok because [].concat works in both cases.
	*
	* @param {Vue|Vnode} instance
	* @return {Vue|Array}
	*/
	async findQualifiedChildren(instance, depth) {
		if (this.componentFilter.isQualified(instance) && !instance.type.devtools?.hide) return [await this.capture(instance, depth)];
		else if (instance.subTree) {
			const list = this.isKeepAlive(instance) ? this.getKeepAliveCachedInstances(instance) : this.getInternalInstanceChildren(instance.subTree);
			return this.findQualifiedChildrenFromList(list, depth);
		} else return [];
	}
	/**
	* Iterate through an array of instances and flatten it into
	* an array of qualified instances. This is a depth-first
	* traversal - e.g. if an instance is not matched, we will
	* recursively go deeper until a qualified child is found.
	*
	* @param {Array} instances
	* @return {Array}
	*/
	async findQualifiedChildrenFromList(instances, depth) {
		instances = instances.filter((child) => !isBeingDestroyed(child) && !child.type.devtools?.hide);
		if (!this.componentFilter.filter) return Promise.all(instances.map((child) => this.capture(child, depth)));
		else return Array.prototype.concat.apply([], await Promise.all(instances.map((i) => this.findQualifiedChildren(i, depth))));
	}
	/**
	* Get children from a component instance.
	*/
	getInternalInstanceChildren(subTree, suspense = null) {
		const list = [];
		if (subTree) {
			if (subTree.component) !suspense ? list.push(subTree.component) : list.push({
				...subTree.component,
				suspense
			});
			else if (subTree.suspense) {
				const suspenseKey = !subTree.suspense.isInFallback ? "suspense default" : "suspense fallback";
				list.push(...this.getInternalInstanceChildren(subTree.suspense.activeBranch, {
					...subTree.suspense,
					suspenseKey
				}));
			} else if (Array.isArray(subTree.children)) subTree.children.forEach((childSubTree) => {
				if (childSubTree.component) !suspense ? list.push(childSubTree.component) : list.push({
					...childSubTree.component,
					suspense
				});
				else list.push(...this.getInternalInstanceChildren(childSubTree, suspense));
			});
		}
		return list.filter((child) => !isBeingDestroyed(child) && !child.type.devtools?.hide);
	}
	/**
	* Mark an instance as captured and store it in the instance map.
	*
	* @param {Vue} instance
	*/
	mark(instance, force = false) {
		const instanceMap = getAppRecord(instance).instanceMap;
		if (force || !instanceMap.has(instance.__VUE_DEVTOOLS_NEXT_UID__)) {
			instanceMap.set(instance.__VUE_DEVTOOLS_NEXT_UID__, instance);
			activeAppRecord.value.instanceMap = instanceMap;
		}
	}
	isKeepAlive(instance) {
		return instance.type.__isKeepAlive && instance.__v_cache;
	}
	getKeepAliveCachedInstances(instance) {
		return Array.from(instance.__v_cache.values()).map((vnode) => vnode.component).filter(Boolean);
	}
};
//#endregion
//#region src/core/timeline/perf.ts
const markEndQueue = /* @__PURE__ */ new Map();
const PERFORMANCE_EVENT_LAYER_ID = "performance";
async function performanceMarkStart(api, app, uid, vm, type, time) {
	const appRecord = await getAppRecord(app);
	if (!appRecord) return;
	const componentName = getInstanceName(vm) || "Unknown Component";
	const groupId = devtoolsState.perfUniqueGroupId++;
	const groupKey = `${uid}-${type}`;
	appRecord.perfGroupIds.set(groupKey, {
		groupId,
		time
	});
	await api.addTimelineEvent({
		layerId: PERFORMANCE_EVENT_LAYER_ID,
		event: {
			time: Date.now(),
			data: {
				component: componentName,
				type,
				measure: "start"
			},
			title: componentName,
			subtitle: type,
			groupId
		}
	});
	if (markEndQueue.has(groupKey)) {
		const { app, uid, instance, type, time } = markEndQueue.get(groupKey);
		markEndQueue.delete(groupKey);
		await performanceMarkEnd(api, app, uid, instance, type, time);
	}
}
function performanceMarkEnd(api, app, uid, vm, type, time) {
	const appRecord = getAppRecord(app);
	if (!appRecord) return;
	const componentName = getInstanceName(vm) || "Unknown Component";
	const groupKey = `${uid}-${type}`;
	const groupInfo = appRecord.perfGroupIds.get(groupKey);
	if (groupInfo) {
		const groupId = groupInfo.groupId;
		const duration = time - groupInfo.time;
		api.addTimelineEvent({
			layerId: PERFORMANCE_EVENT_LAYER_ID,
			event: {
				time: Date.now(),
				data: {
					component: componentName,
					type,
					measure: "end",
					duration: { _custom: {
						type: "Duration",
						value: duration,
						display: `${duration} ms`
					} }
				},
				title: componentName,
				subtitle: type,
				groupId
			}
		});
	} else markEndQueue.set(groupKey, {
		app,
		uid,
		instance: vm,
		type,
		time
	});
}
//#endregion
//#region src/core/timeline/index.ts
const COMPONENT_EVENT_LAYER_ID = "component-event";
function setupBuiltinTimelineLayers(api) {
	if (!_vue_devtools_shared.isBrowser) return;
	api.addTimelineLayer({
		id: "mouse",
		label: "Mouse",
		color: 10768815
	});
	[
		"mousedown",
		"mouseup",
		"click",
		"dblclick"
	].forEach((eventType) => {
		if (!devtoolsState.timelineLayersState.recordingState || !devtoolsState.timelineLayersState.mouseEventEnabled) return;
		window.addEventListener(eventType, async (event) => {
			await api.addTimelineEvent({
				layerId: "mouse",
				event: {
					time: Date.now(),
					data: {
						type: eventType,
						x: event.clientX,
						y: event.clientY
					},
					title: eventType
				}
			});
		}, {
			capture: true,
			passive: true
		});
	});
	api.addTimelineLayer({
		id: "keyboard",
		label: "Keyboard",
		color: 8475055
	});
	[
		"keyup",
		"keydown",
		"keypress"
	].forEach((eventType) => {
		window.addEventListener(eventType, async (event) => {
			if (!devtoolsState.timelineLayersState.recordingState || !devtoolsState.timelineLayersState.keyboardEventEnabled) return;
			await api.addTimelineEvent({
				layerId: "keyboard",
				event: {
					time: Date.now(),
					data: {
						type: eventType,
						key: event.key,
						ctrlKey: event.ctrlKey,
						shiftKey: event.shiftKey,
						altKey: event.altKey,
						metaKey: event.metaKey
					},
					title: event.key
				}
			});
		}, {
			capture: true,
			passive: true
		});
	});
	api.addTimelineLayer({
		id: COMPONENT_EVENT_LAYER_ID,
		label: "Component events",
		color: 5226637
	});
	hook.on.componentEmit(async (app, instance, event, params) => {
		if (!devtoolsState.timelineLayersState.recordingState || !devtoolsState.timelineLayersState.componentEventEnabled) return;
		const appRecord = await getAppRecord(app);
		if (!appRecord) return;
		const componentId = `${appRecord.id}:${instance.uid}`;
		const componentName = getInstanceName(instance) || "Unknown Component";
		api.addTimelineEvent({
			layerId: COMPONENT_EVENT_LAYER_ID,
			event: {
				time: Date.now(),
				data: {
					component: { _custom: {
						type: "component-definition",
						display: componentName
					} },
					event,
					params
				},
				title: event,
				subtitle: `by ${componentName}`,
				meta: { componentId }
			}
		});
	});
	api.addTimelineLayer({
		id: "performance",
		label: PERFORMANCE_EVENT_LAYER_ID,
		color: 4307050
	});
	hook.on.perfStart((app, uid, vm, type, time) => {
		if (!devtoolsState.timelineLayersState.recordingState || !devtoolsState.timelineLayersState.performanceEventEnabled) return;
		performanceMarkStart(api, app, uid, vm, type, time);
	});
	hook.on.perfEnd((app, uid, vm, type, time) => {
		if (!devtoolsState.timelineLayersState.recordingState || !devtoolsState.timelineLayersState.performanceEventEnabled) return;
		performanceMarkEnd(api, app, uid, vm, type, time);
	});
}
//#endregion
//#region src/core/vm/index.ts
const MAX_$VM = 10;
const $vmQueue = [];
function exposeInstanceToWindow(componentInstance) {
	if (typeof window === "undefined") return;
	const win = window;
	if (!componentInstance) return;
	win.$vm = componentInstance;
	if ($vmQueue[0] !== componentInstance) {
		if ($vmQueue.length >= MAX_$VM) $vmQueue.pop();
		for (let i = $vmQueue.length; i > 0; i--) win[`$vm${i}`] = $vmQueue[i] = $vmQueue[i - 1];
		win.$vm0 = $vmQueue[0] = componentInstance;
	}
}
//#endregion
//#region src/core/plugin/components.ts
const INSPECTOR_ID = "components";
function createComponentsDevToolsPlugin(app) {
	const descriptor = {
		id: INSPECTOR_ID,
		label: "Components",
		app
	};
	const setupFn = (api) => {
		api.addInspector({
			id: INSPECTOR_ID,
			label: "Components",
			treeFilterPlaceholder: "Search components"
		});
		setupBuiltinTimelineLayers(api);
		api.on.getInspectorTree(async (payload) => {
			if (payload.app === app && payload.inspectorId === INSPECTOR_ID) {
				const instance = getComponentInstance(activeAppRecord.value, payload.instanceId);
				if (instance) payload.rootNodes = await new ComponentWalker({
					filterText: payload.filter,
					maxDepth: 100,
					recursively: false,
					api
				}).getComponentTree(instance);
			}
		});
		api.on.getInspectorState(async (payload) => {
			if (payload.app === app && payload.inspectorId === INSPECTOR_ID) {
				const result = getInstanceState({ instanceId: payload.nodeId });
				const componentInstance = result.instance;
				const _payload = {
					componentInstance,
					app: result.instance?.appContext.app,
					instanceData: result
				};
				devtoolsContext.hooks.callHookWith((callbacks) => {
					callbacks.forEach((cb) => cb(_payload));
				}, DevToolsV6PluginAPIHookKeys.INSPECT_COMPONENT);
				payload.state = result;
				exposeInstanceToWindow(componentInstance);
			}
		});
		api.on.editInspectorState(async (payload) => {
			if (payload.app === app && payload.inspectorId === INSPECTOR_ID) {
				editState(payload);
				await api.sendInspectorState("components");
			}
		});
		const debounceSendInspectorTree = (0, perfect_debounce.debounce)(() => {
			api.sendInspectorTree(INSPECTOR_ID);
		}, 120);
		const debounceSendInspectorState = (0, perfect_debounce.debounce)(() => {
			api.sendInspectorState(INSPECTOR_ID);
		}, 120);
		hook.on.componentAdded(async (app, uid, parentUid, component) => {
			if (devtoolsState.highPerfModeEnabled) return;
			if (app?._instance?.type?.devtools?.hide) return;
			if (!app || typeof uid !== "number" && !uid || !component) return;
			const id = await getComponentId({
				app,
				uid,
				instance: component
			});
			const appRecord = await getAppRecord(app);
			if (component) {
				if (component.__VUE_DEVTOOLS_NEXT_UID__ == null) component.__VUE_DEVTOOLS_NEXT_UID__ = id;
				if (!appRecord?.instanceMap.has(id)) {
					appRecord?.instanceMap.set(id, component);
					if (activeAppRecord.value.id === appRecord?.id) activeAppRecord.value.instanceMap = appRecord.instanceMap;
				}
			}
			if (!appRecord) return;
			debounceSendInspectorTree();
		});
		hook.on.componentUpdated(async (app, uid, parentUid, component) => {
			if (devtoolsState.highPerfModeEnabled) return;
			if (app?._instance?.type?.devtools?.hide) return;
			if (!app || typeof uid !== "number" && !uid || !component) return;
			const id = await getComponentId({
				app,
				uid,
				instance: component
			});
			const appRecord = await getAppRecord(app);
			if (component) {
				if (component.__VUE_DEVTOOLS_NEXT_UID__ == null) component.__VUE_DEVTOOLS_NEXT_UID__ = id;
				if (!appRecord?.instanceMap.has(id)) {
					appRecord?.instanceMap.set(id, component);
					if (activeAppRecord.value.id === appRecord?.id) activeAppRecord.value.instanceMap = appRecord.instanceMap;
				}
			}
			if (!appRecord) return;
			debounceSendInspectorTree();
			debounceSendInspectorState();
		});
		hook.on.componentRemoved(async (app, uid, parentUid, component) => {
			if (devtoolsState.highPerfModeEnabled) return;
			if (app?._instance?.type?.devtools?.hide) return;
			if (!app || typeof uid !== "number" && !uid || !component) return;
			const appRecord = await getAppRecord(app);
			if (!appRecord) return;
			const id = await getComponentId({
				app,
				uid,
				instance: component
			});
			appRecord?.instanceMap.delete(id);
			if (activeAppRecord.value.id === appRecord?.id) activeAppRecord.value.instanceMap = appRecord.instanceMap;
			debounceSendInspectorTree();
		});
	};
	return [descriptor, setupFn];
}
//#endregion
//#region src/core/plugin/index.ts
_vue_devtools_shared.target.__VUE_DEVTOOLS_KIT__REGISTERED_PLUGIN_APPS__ ??= /* @__PURE__ */ new Set();
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
function removeRegisteredPluginApp(app) {
	_vue_devtools_shared.target.__VUE_DEVTOOLS_KIT__REGISTERED_PLUGIN_APPS__.delete(app);
}
function registerDevToolsPlugin(app, options) {
	if (_vue_devtools_shared.target.__VUE_DEVTOOLS_KIT__REGISTERED_PLUGIN_APPS__.has(app)) return;
	if (devtoolsState.highPerfModeEnabled && !options?.inspectingComponent) return;
	_vue_devtools_shared.target.__VUE_DEVTOOLS_KIT__REGISTERED_PLUGIN_APPS__.add(app);
	devtoolsPluginBuffer.forEach((plugin) => {
		callDevToolsPluginSetupFn(plugin, app);
	});
}
//#endregion
//#region src/ctx/router.ts
const ROUTER_KEY = "__VUE_DEVTOOLS_ROUTER__";
const ROUTER_INFO_KEY = "__VUE_DEVTOOLS_ROUTER_INFO__";
_vue_devtools_shared.target[ROUTER_INFO_KEY] ??= {
	currentRoute: null,
	routes: []
};
_vue_devtools_shared.target[ROUTER_KEY] ??= {};
const devtoolsRouterInfo = new Proxy(_vue_devtools_shared.target[ROUTER_INFO_KEY], { get(target, property) {
	return _vue_devtools_shared.target[ROUTER_INFO_KEY][property];
} });
const devtoolsRouter = new Proxy(_vue_devtools_shared.target[ROUTER_KEY], { get(target, property) {
	if (property === "value") return _vue_devtools_shared.target[ROUTER_KEY];
} });
//#endregion
//#region src/core/router/index.ts
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
		_vue_devtools_shared.target[ROUTER_INFO_KEY] = {
			currentRoute: currentRoute ? (0, _vue_devtools_shared.deepClone)(currentRoute) : {},
			routes: (0, _vue_devtools_shared.deepClone)(routes)
		};
		_vue_devtools_shared.target[ROUTER_KEY] = router;
		console.warn = c;
	}
	init();
	hook.on.componentUpdated((0, perfect_debounce.debounce)(() => {
		if (activeAppRecord.value?.app !== appRecord.app) return;
		init();
		if (devtoolsState.highPerfModeEnabled) return;
		devtoolsContext.hooks.callHook(DevToolsMessagingHookKeys.ROUTER_INFO_UPDATED, { state: _vue_devtools_shared.target[ROUTER_INFO_KEY] });
	}, 200));
}
//#endregion
//#region src/ctx/api.ts
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
				if (el) _vue_devtools_shared.target.__VUE_DEVTOOLS_INSPECT_DOM_TARGET__ = el;
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
//#region src/ctx/env.ts
_vue_devtools_shared.target.__VUE_DEVTOOLS_ENV__ ??= { vitePluginDetected: false };
function getDevToolsEnv() {
	return _vue_devtools_shared.target.__VUE_DEVTOOLS_ENV__;
}
function setDevToolsEnv(env) {
	_vue_devtools_shared.target.__VUE_DEVTOOLS_ENV__ = {
		..._vue_devtools_shared.target.__VUE_DEVTOOLS_ENV__,
		...env
	};
}
//#endregion
//#region src/ctx/index.ts
const hooks = createDevToolsCtxHooks();
_vue_devtools_shared.target.__VUE_DEVTOOLS_KIT_CONTEXT__ ??= {
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
const devtoolsContext = _vue_devtools_shared.target.__VUE_DEVTOOLS_KIT_CONTEXT__;
//#endregion
//#region ../../node_modules/.pnpm/speakingurl@14.0.1/node_modules/speakingurl/lib/speakingurl.js
var require_speakingurl$1 = /* @__PURE__ */ __commonJSMin(((exports, module) => {
	(function(root) {
		"use strict";
		/**
		* charMap
		* @type {Object}
		*/
		var charMap = {
			"À": "A",
			"Á": "A",
			"Â": "A",
			"Ã": "A",
			"Ä": "Ae",
			"Å": "A",
			"Æ": "AE",
			"Ç": "C",
			"È": "E",
			"É": "E",
			"Ê": "E",
			"Ë": "E",
			"Ì": "I",
			"Í": "I",
			"Î": "I",
			"Ï": "I",
			"Ð": "D",
			"Ñ": "N",
			"Ò": "O",
			"Ó": "O",
			"Ô": "O",
			"Õ": "O",
			"Ö": "Oe",
			"Ő": "O",
			"Ø": "O",
			"Ù": "U",
			"Ú": "U",
			"Û": "U",
			"Ü": "Ue",
			"Ű": "U",
			"Ý": "Y",
			"Þ": "TH",
			"ß": "ss",
			"à": "a",
			"á": "a",
			"â": "a",
			"ã": "a",
			"ä": "ae",
			"å": "a",
			"æ": "ae",
			"ç": "c",
			"è": "e",
			"é": "e",
			"ê": "e",
			"ë": "e",
			"ì": "i",
			"í": "i",
			"î": "i",
			"ï": "i",
			"ð": "d",
			"ñ": "n",
			"ò": "o",
			"ó": "o",
			"ô": "o",
			"õ": "o",
			"ö": "oe",
			"ő": "o",
			"ø": "o",
			"ù": "u",
			"ú": "u",
			"û": "u",
			"ü": "ue",
			"ű": "u",
			"ý": "y",
			"þ": "th",
			"ÿ": "y",
			"ẞ": "SS",
			"ا": "a",
			"أ": "a",
			"إ": "i",
			"آ": "aa",
			"ؤ": "u",
			"ئ": "e",
			"ء": "a",
			"ب": "b",
			"ت": "t",
			"ث": "th",
			"ج": "j",
			"ح": "h",
			"خ": "kh",
			"د": "d",
			"ذ": "th",
			"ر": "r",
			"ز": "z",
			"س": "s",
			"ش": "sh",
			"ص": "s",
			"ض": "dh",
			"ط": "t",
			"ظ": "z",
			"ع": "a",
			"غ": "gh",
			"ف": "f",
			"ق": "q",
			"ك": "k",
			"ل": "l",
			"م": "m",
			"ن": "n",
			"ه": "h",
			"و": "w",
			"ي": "y",
			"ى": "a",
			"ة": "h",
			"ﻻ": "la",
			"ﻷ": "laa",
			"ﻹ": "lai",
			"ﻵ": "laa",
			"گ": "g",
			"چ": "ch",
			"پ": "p",
			"ژ": "zh",
			"ک": "k",
			"ی": "y",
			"َ": "a",
			"ً": "an",
			"ِ": "e",
			"ٍ": "en",
			"ُ": "u",
			"ٌ": "on",
			"ْ": "",
			"٠": "0",
			"١": "1",
			"٢": "2",
			"٣": "3",
			"٤": "4",
			"٥": "5",
			"٦": "6",
			"٧": "7",
			"٨": "8",
			"٩": "9",
			"۰": "0",
			"۱": "1",
			"۲": "2",
			"۳": "3",
			"۴": "4",
			"۵": "5",
			"۶": "6",
			"۷": "7",
			"۸": "8",
			"۹": "9",
			"က": "k",
			"ခ": "kh",
			"ဂ": "g",
			"ဃ": "ga",
			"င": "ng",
			"စ": "s",
			"ဆ": "sa",
			"ဇ": "z",
			"စျ": "za",
			"ည": "ny",
			"ဋ": "t",
			"ဌ": "ta",
			"ဍ": "d",
			"ဎ": "da",
			"ဏ": "na",
			"တ": "t",
			"ထ": "ta",
			"ဒ": "d",
			"ဓ": "da",
			"န": "n",
			"ပ": "p",
			"ဖ": "pa",
			"ဗ": "b",
			"ဘ": "ba",
			"မ": "m",
			"ယ": "y",
			"ရ": "ya",
			"လ": "l",
			"ဝ": "w",
			"သ": "th",
			"ဟ": "h",
			"ဠ": "la",
			"အ": "a",
			"ြ": "y",
			"ျ": "ya",
			"ွ": "w",
			"ြွ": "yw",
			"ျွ": "ywa",
			"ှ": "h",
			"ဧ": "e",
			"၏": "-e",
			"ဣ": "i",
			"ဤ": "-i",
			"ဉ": "u",
			"ဦ": "-u",
			"ဩ": "aw",
			"သြော": "aw",
			"ဪ": "aw",
			"၀": "0",
			"၁": "1",
			"၂": "2",
			"၃": "3",
			"၄": "4",
			"၅": "5",
			"၆": "6",
			"၇": "7",
			"၈": "8",
			"၉": "9",
			"္": "",
			"့": "",
			"း": "",
			"č": "c",
			"ď": "d",
			"ě": "e",
			"ň": "n",
			"ř": "r",
			"š": "s",
			"ť": "t",
			"ů": "u",
			"ž": "z",
			"Č": "C",
			"Ď": "D",
			"Ě": "E",
			"Ň": "N",
			"Ř": "R",
			"Š": "S",
			"Ť": "T",
			"Ů": "U",
			"Ž": "Z",
			"ހ": "h",
			"ށ": "sh",
			"ނ": "n",
			"ރ": "r",
			"ބ": "b",
			"ޅ": "lh",
			"ކ": "k",
			"އ": "a",
			"ވ": "v",
			"މ": "m",
			"ފ": "f",
			"ދ": "dh",
			"ތ": "th",
			"ލ": "l",
			"ގ": "g",
			"ޏ": "gn",
			"ސ": "s",
			"ޑ": "d",
			"ޒ": "z",
			"ޓ": "t",
			"ޔ": "y",
			"ޕ": "p",
			"ޖ": "j",
			"ޗ": "ch",
			"ޘ": "tt",
			"ޙ": "hh",
			"ޚ": "kh",
			"ޛ": "th",
			"ޜ": "z",
			"ޝ": "sh",
			"ޞ": "s",
			"ޟ": "d",
			"ޠ": "t",
			"ޡ": "z",
			"ޢ": "a",
			"ޣ": "gh",
			"ޤ": "q",
			"ޥ": "w",
			"ަ": "a",
			"ާ": "aa",
			"ި": "i",
			"ީ": "ee",
			"ު": "u",
			"ޫ": "oo",
			"ެ": "e",
			"ޭ": "ey",
			"ޮ": "o",
			"ޯ": "oa",
			"ް": "",
			"ა": "a",
			"ბ": "b",
			"გ": "g",
			"დ": "d",
			"ე": "e",
			"ვ": "v",
			"ზ": "z",
			"თ": "t",
			"ი": "i",
			"კ": "k",
			"ლ": "l",
			"მ": "m",
			"ნ": "n",
			"ო": "o",
			"პ": "p",
			"ჟ": "zh",
			"რ": "r",
			"ს": "s",
			"ტ": "t",
			"უ": "u",
			"ფ": "p",
			"ქ": "k",
			"ღ": "gh",
			"ყ": "q",
			"შ": "sh",
			"ჩ": "ch",
			"ც": "ts",
			"ძ": "dz",
			"წ": "ts",
			"ჭ": "ch",
			"ხ": "kh",
			"ჯ": "j",
			"ჰ": "h",
			"α": "a",
			"β": "v",
			"γ": "g",
			"δ": "d",
			"ε": "e",
			"ζ": "z",
			"η": "i",
			"θ": "th",
			"ι": "i",
			"κ": "k",
			"λ": "l",
			"μ": "m",
			"ν": "n",
			"ξ": "ks",
			"ο": "o",
			"π": "p",
			"ρ": "r",
			"σ": "s",
			"τ": "t",
			"υ": "y",
			"φ": "f",
			"χ": "x",
			"ψ": "ps",
			"ω": "o",
			"ά": "a",
			"έ": "e",
			"ί": "i",
			"ό": "o",
			"ύ": "y",
			"ή": "i",
			"ώ": "o",
			"ς": "s",
			"ϊ": "i",
			"ΰ": "y",
			"ϋ": "y",
			"ΐ": "i",
			"Α": "A",
			"Β": "B",
			"Γ": "G",
			"Δ": "D",
			"Ε": "E",
			"Ζ": "Z",
			"Η": "I",
			"Θ": "TH",
			"Ι": "I",
			"Κ": "K",
			"Λ": "L",
			"Μ": "M",
			"Ν": "N",
			"Ξ": "KS",
			"Ο": "O",
			"Π": "P",
			"Ρ": "R",
			"Σ": "S",
			"Τ": "T",
			"Υ": "Y",
			"Φ": "F",
			"Χ": "X",
			"Ψ": "PS",
			"Ω": "O",
			"Ά": "A",
			"Έ": "E",
			"Ί": "I",
			"Ό": "O",
			"Ύ": "Y",
			"Ή": "I",
			"Ώ": "O",
			"Ϊ": "I",
			"Ϋ": "Y",
			"ā": "a",
			"ē": "e",
			"ģ": "g",
			"ī": "i",
			"ķ": "k",
			"ļ": "l",
			"ņ": "n",
			"ū": "u",
			"Ā": "A",
			"Ē": "E",
			"Ģ": "G",
			"Ī": "I",
			"Ķ": "k",
			"Ļ": "L",
			"Ņ": "N",
			"Ū": "U",
			"Ќ": "Kj",
			"ќ": "kj",
			"Љ": "Lj",
			"љ": "lj",
			"Њ": "Nj",
			"њ": "nj",
			"Тс": "Ts",
			"тс": "ts",
			"ą": "a",
			"ć": "c",
			"ę": "e",
			"ł": "l",
			"ń": "n",
			"ś": "s",
			"ź": "z",
			"ż": "z",
			"Ą": "A",
			"Ć": "C",
			"Ę": "E",
			"Ł": "L",
			"Ń": "N",
			"Ś": "S",
			"Ź": "Z",
			"Ż": "Z",
			"Є": "Ye",
			"І": "I",
			"Ї": "Yi",
			"Ґ": "G",
			"є": "ye",
			"і": "i",
			"ї": "yi",
			"ґ": "g",
			"ă": "a",
			"Ă": "A",
			"ș": "s",
			"Ș": "S",
			"ț": "t",
			"Ț": "T",
			"ţ": "t",
			"Ţ": "T",
			"а": "a",
			"б": "b",
			"в": "v",
			"г": "g",
			"д": "d",
			"е": "e",
			"ё": "yo",
			"ж": "zh",
			"з": "z",
			"и": "i",
			"й": "i",
			"к": "k",
			"л": "l",
			"м": "m",
			"н": "n",
			"о": "o",
			"п": "p",
			"р": "r",
			"с": "s",
			"т": "t",
			"у": "u",
			"ф": "f",
			"х": "kh",
			"ц": "c",
			"ч": "ch",
			"ш": "sh",
			"щ": "sh",
			"ъ": "",
			"ы": "y",
			"ь": "",
			"э": "e",
			"ю": "yu",
			"я": "ya",
			"А": "A",
			"Б": "B",
			"В": "V",
			"Г": "G",
			"Д": "D",
			"Е": "E",
			"Ё": "Yo",
			"Ж": "Zh",
			"З": "Z",
			"И": "I",
			"Й": "I",
			"К": "K",
			"Л": "L",
			"М": "M",
			"Н": "N",
			"О": "O",
			"П": "P",
			"Р": "R",
			"С": "S",
			"Т": "T",
			"У": "U",
			"Ф": "F",
			"Х": "Kh",
			"Ц": "C",
			"Ч": "Ch",
			"Ш": "Sh",
			"Щ": "Sh",
			"Ъ": "",
			"Ы": "Y",
			"Ь": "",
			"Э": "E",
			"Ю": "Yu",
			"Я": "Ya",
			"ђ": "dj",
			"ј": "j",
			"ћ": "c",
			"џ": "dz",
			"Ђ": "Dj",
			"Ј": "j",
			"Ћ": "C",
			"Џ": "Dz",
			"ľ": "l",
			"ĺ": "l",
			"ŕ": "r",
			"Ľ": "L",
			"Ĺ": "L",
			"Ŕ": "R",
			"ş": "s",
			"Ş": "S",
			"ı": "i",
			"İ": "I",
			"ğ": "g",
			"Ğ": "G",
			"ả": "a",
			"Ả": "A",
			"ẳ": "a",
			"Ẳ": "A",
			"ẩ": "a",
			"Ẩ": "A",
			"đ": "d",
			"Đ": "D",
			"ẹ": "e",
			"Ẹ": "E",
			"ẽ": "e",
			"Ẽ": "E",
			"ẻ": "e",
			"Ẻ": "E",
			"ế": "e",
			"Ế": "E",
			"ề": "e",
			"Ề": "E",
			"ệ": "e",
			"Ệ": "E",
			"ễ": "e",
			"Ễ": "E",
			"ể": "e",
			"Ể": "E",
			"ỏ": "o",
			"ọ": "o",
			"Ọ": "o",
			"ố": "o",
			"Ố": "O",
			"ồ": "o",
			"Ồ": "O",
			"ổ": "o",
			"Ổ": "O",
			"ộ": "o",
			"Ộ": "O",
			"ỗ": "o",
			"Ỗ": "O",
			"ơ": "o",
			"Ơ": "O",
			"ớ": "o",
			"Ớ": "O",
			"ờ": "o",
			"Ờ": "O",
			"ợ": "o",
			"Ợ": "O",
			"ỡ": "o",
			"Ỡ": "O",
			"Ở": "o",
			"ở": "o",
			"ị": "i",
			"Ị": "I",
			"ĩ": "i",
			"Ĩ": "I",
			"ỉ": "i",
			"Ỉ": "i",
			"ủ": "u",
			"Ủ": "U",
			"ụ": "u",
			"Ụ": "U",
			"ũ": "u",
			"Ũ": "U",
			"ư": "u",
			"Ư": "U",
			"ứ": "u",
			"Ứ": "U",
			"ừ": "u",
			"Ừ": "U",
			"ự": "u",
			"Ự": "U",
			"ữ": "u",
			"Ữ": "U",
			"ử": "u",
			"Ử": "ư",
			"ỷ": "y",
			"Ỷ": "y",
			"ỳ": "y",
			"Ỳ": "Y",
			"ỵ": "y",
			"Ỵ": "Y",
			"ỹ": "y",
			"Ỹ": "Y",
			"ạ": "a",
			"Ạ": "A",
			"ấ": "a",
			"Ấ": "A",
			"ầ": "a",
			"Ầ": "A",
			"ậ": "a",
			"Ậ": "A",
			"ẫ": "a",
			"Ẫ": "A",
			"ắ": "a",
			"Ắ": "A",
			"ằ": "a",
			"Ằ": "A",
			"ặ": "a",
			"Ặ": "A",
			"ẵ": "a",
			"Ẵ": "A",
			"⓪": "0",
			"①": "1",
			"②": "2",
			"③": "3",
			"④": "4",
			"⑤": "5",
			"⑥": "6",
			"⑦": "7",
			"⑧": "8",
			"⑨": "9",
			"⑩": "10",
			"⑪": "11",
			"⑫": "12",
			"⑬": "13",
			"⑭": "14",
			"⑮": "15",
			"⑯": "16",
			"⑰": "17",
			"⑱": "18",
			"⑲": "18",
			"⑳": "18",
			"⓵": "1",
			"⓶": "2",
			"⓷": "3",
			"⓸": "4",
			"⓹": "5",
			"⓺": "6",
			"⓻": "7",
			"⓼": "8",
			"⓽": "9",
			"⓾": "10",
			"⓿": "0",
			"⓫": "11",
			"⓬": "12",
			"⓭": "13",
			"⓮": "14",
			"⓯": "15",
			"⓰": "16",
			"⓱": "17",
			"⓲": "18",
			"⓳": "19",
			"⓴": "20",
			"Ⓐ": "A",
			"Ⓑ": "B",
			"Ⓒ": "C",
			"Ⓓ": "D",
			"Ⓔ": "E",
			"Ⓕ": "F",
			"Ⓖ": "G",
			"Ⓗ": "H",
			"Ⓘ": "I",
			"Ⓙ": "J",
			"Ⓚ": "K",
			"Ⓛ": "L",
			"Ⓜ": "M",
			"Ⓝ": "N",
			"Ⓞ": "O",
			"Ⓟ": "P",
			"Ⓠ": "Q",
			"Ⓡ": "R",
			"Ⓢ": "S",
			"Ⓣ": "T",
			"Ⓤ": "U",
			"Ⓥ": "V",
			"Ⓦ": "W",
			"Ⓧ": "X",
			"Ⓨ": "Y",
			"Ⓩ": "Z",
			"ⓐ": "a",
			"ⓑ": "b",
			"ⓒ": "c",
			"ⓓ": "d",
			"ⓔ": "e",
			"ⓕ": "f",
			"ⓖ": "g",
			"ⓗ": "h",
			"ⓘ": "i",
			"ⓙ": "j",
			"ⓚ": "k",
			"ⓛ": "l",
			"ⓜ": "m",
			"ⓝ": "n",
			"ⓞ": "o",
			"ⓟ": "p",
			"ⓠ": "q",
			"ⓡ": "r",
			"ⓢ": "s",
			"ⓣ": "t",
			"ⓤ": "u",
			"ⓦ": "v",
			"ⓥ": "w",
			"ⓧ": "x",
			"ⓨ": "y",
			"ⓩ": "z",
			"“": "\"",
			"”": "\"",
			"‘": "'",
			"’": "'",
			"∂": "d",
			"ƒ": "f",
			"™": "(TM)",
			"©": "(C)",
			"œ": "oe",
			"Œ": "OE",
			"®": "(R)",
			"†": "+",
			"℠": "(SM)",
			"…": "...",
			"˚": "o",
			"º": "o",
			"ª": "a",
			"•": "*",
			"၊": ",",
			"။": ".",
			"$": "USD",
			"€": "EUR",
			"₢": "BRN",
			"₣": "FRF",
			"£": "GBP",
			"₤": "ITL",
			"₦": "NGN",
			"₧": "ESP",
			"₩": "KRW",
			"₪": "ILS",
			"₫": "VND",
			"₭": "LAK",
			"₮": "MNT",
			"₯": "GRD",
			"₱": "ARS",
			"₲": "PYG",
			"₳": "ARA",
			"₴": "UAH",
			"₵": "GHS",
			"¢": "cent",
			"¥": "CNY",
			"元": "CNY",
			"円": "YEN",
			"﷼": "IRR",
			"₠": "EWE",
			"฿": "THB",
			"₨": "INR",
			"₹": "INR",
			"₰": "PF",
			"₺": "TRY",
			"؋": "AFN",
			"₼": "AZN",
			"лв": "BGN",
			"៛": "KHR",
			"₡": "CRC",
			"₸": "KZT",
			"ден": "MKD",
			"zł": "PLN",
			"₽": "RUB",
			"₾": "GEL"
		};
		/**
		* special look ahead character array
		* These characters form with consonants to become 'single'/consonant combo
		* @type [Array]
		*/
		var lookAheadCharArray = ["်", "ް"];
		/**
		* diatricMap for languages where transliteration changes entirely as more diatrics are added
		* @type {Object}
		*/
		var diatricMap = {
			"ာ": "a",
			"ါ": "a",
			"ေ": "e",
			"ဲ": "e",
			"ိ": "i",
			"ီ": "i",
			"ို": "o",
			"ု": "u",
			"ူ": "u",
			"ေါင်": "aung",
			"ော": "aw",
			"ော်": "aw",
			"ေါ": "aw",
			"ေါ်": "aw",
			"်": "်",
			"က်": "et",
			"ိုက်": "aik",
			"ောက်": "auk",
			"င်": "in",
			"ိုင်": "aing",
			"ောင်": "aung",
			"စ်": "it",
			"ည်": "i",
			"တ်": "at",
			"ိတ်": "eik",
			"ုတ်": "ok",
			"ွတ်": "ut",
			"ေတ်": "it",
			"ဒ်": "d",
			"ိုဒ်": "ok",
			"ုဒ်": "ait",
			"န်": "an",
			"ာန်": "an",
			"ိန်": "ein",
			"ုန်": "on",
			"ွန်": "un",
			"ပ်": "at",
			"ိပ်": "eik",
			"ုပ်": "ok",
			"ွပ်": "ut",
			"န်ုပ်": "nub",
			"မ်": "an",
			"ိမ်": "ein",
			"ုမ်": "on",
			"ွမ်": "un",
			"ယ်": "e",
			"ိုလ်": "ol",
			"ဉ်": "in",
			"ံ": "an",
			"ိံ": "ein",
			"ုံ": "on",
			"ައް": "ah",
			"ަށް": "ah"
		};
		/**
		* langCharMap language specific characters translations
		* @type   {Object}
		*/
		var langCharMap = {
			"en": {},
			"az": {
				"ç": "c",
				"ə": "e",
				"ğ": "g",
				"ı": "i",
				"ö": "o",
				"ş": "s",
				"ü": "u",
				"Ç": "C",
				"Ə": "E",
				"Ğ": "G",
				"İ": "I",
				"Ö": "O",
				"Ş": "S",
				"Ü": "U"
			},
			"cs": {
				"č": "c",
				"ď": "d",
				"ě": "e",
				"ň": "n",
				"ř": "r",
				"š": "s",
				"ť": "t",
				"ů": "u",
				"ž": "z",
				"Č": "C",
				"Ď": "D",
				"Ě": "E",
				"Ň": "N",
				"Ř": "R",
				"Š": "S",
				"Ť": "T",
				"Ů": "U",
				"Ž": "Z"
			},
			"fi": {
				"ä": "a",
				"Ä": "A",
				"ö": "o",
				"Ö": "O"
			},
			"hu": {
				"ä": "a",
				"Ä": "A",
				"ö": "o",
				"Ö": "O",
				"ü": "u",
				"Ü": "U",
				"ű": "u",
				"Ű": "U"
			},
			"lt": {
				"ą": "a",
				"č": "c",
				"ę": "e",
				"ė": "e",
				"į": "i",
				"š": "s",
				"ų": "u",
				"ū": "u",
				"ž": "z",
				"Ą": "A",
				"Č": "C",
				"Ę": "E",
				"Ė": "E",
				"Į": "I",
				"Š": "S",
				"Ų": "U",
				"Ū": "U"
			},
			"lv": {
				"ā": "a",
				"č": "c",
				"ē": "e",
				"ģ": "g",
				"ī": "i",
				"ķ": "k",
				"ļ": "l",
				"ņ": "n",
				"š": "s",
				"ū": "u",
				"ž": "z",
				"Ā": "A",
				"Č": "C",
				"Ē": "E",
				"Ģ": "G",
				"Ī": "i",
				"Ķ": "k",
				"Ļ": "L",
				"Ņ": "N",
				"Š": "S",
				"Ū": "u",
				"Ž": "Z"
			},
			"pl": {
				"ą": "a",
				"ć": "c",
				"ę": "e",
				"ł": "l",
				"ń": "n",
				"ó": "o",
				"ś": "s",
				"ź": "z",
				"ż": "z",
				"Ą": "A",
				"Ć": "C",
				"Ę": "e",
				"Ł": "L",
				"Ń": "N",
				"Ó": "O",
				"Ś": "S",
				"Ź": "Z",
				"Ż": "Z"
			},
			"sv": {
				"ä": "a",
				"Ä": "A",
				"ö": "o",
				"Ö": "O"
			},
			"sk": {
				"ä": "a",
				"Ä": "A"
			},
			"sr": {
				"љ": "lj",
				"њ": "nj",
				"Љ": "Lj",
				"Њ": "Nj",
				"đ": "dj",
				"Đ": "Dj"
			},
			"tr": {
				"Ü": "U",
				"Ö": "O",
				"ü": "u",
				"ö": "o"
			}
		};
		/**
		* symbolMap language specific symbol translations
		* translations must be transliterated already
		* @type   {Object}
		*/
		var symbolMap = {
			"ar": {
				"∆": "delta",
				"∞": "la-nihaya",
				"♥": "hob",
				"&": "wa",
				"|": "aw",
				"<": "aqal-men",
				">": "akbar-men",
				"∑": "majmou",
				"¤": "omla"
			},
			"az": {},
			"ca": {
				"∆": "delta",
				"∞": "infinit",
				"♥": "amor",
				"&": "i",
				"|": "o",
				"<": "menys que",
				">": "mes que",
				"∑": "suma dels",
				"¤": "moneda"
			},
			"cs": {
				"∆": "delta",
				"∞": "nekonecno",
				"♥": "laska",
				"&": "a",
				"|": "nebo",
				"<": "mensi nez",
				">": "vetsi nez",
				"∑": "soucet",
				"¤": "mena"
			},
			"de": {
				"∆": "delta",
				"∞": "unendlich",
				"♥": "Liebe",
				"&": "und",
				"|": "oder",
				"<": "kleiner als",
				">": "groesser als",
				"∑": "Summe von",
				"¤": "Waehrung"
			},
			"dv": {
				"∆": "delta",
				"∞": "kolunulaa",
				"♥": "loabi",
				"&": "aai",
				"|": "noonee",
				"<": "ah vure kuda",
				">": "ah vure bodu",
				"∑": "jumula",
				"¤": "faisaa"
			},
			"en": {
				"∆": "delta",
				"∞": "infinity",
				"♥": "love",
				"&": "and",
				"|": "or",
				"<": "less than",
				">": "greater than",
				"∑": "sum",
				"¤": "currency"
			},
			"es": {
				"∆": "delta",
				"∞": "infinito",
				"♥": "amor",
				"&": "y",
				"|": "u",
				"<": "menos que",
				">": "mas que",
				"∑": "suma de los",
				"¤": "moneda"
			},
			"fa": {
				"∆": "delta",
				"∞": "bi-nahayat",
				"♥": "eshgh",
				"&": "va",
				"|": "ya",
				"<": "kamtar-az",
				">": "bishtar-az",
				"∑": "majmooe",
				"¤": "vahed"
			},
			"fi": {
				"∆": "delta",
				"∞": "aarettomyys",
				"♥": "rakkaus",
				"&": "ja",
				"|": "tai",
				"<": "pienempi kuin",
				">": "suurempi kuin",
				"∑": "summa",
				"¤": "valuutta"
			},
			"fr": {
				"∆": "delta",
				"∞": "infiniment",
				"♥": "Amour",
				"&": "et",
				"|": "ou",
				"<": "moins que",
				">": "superieure a",
				"∑": "somme des",
				"¤": "monnaie"
			},
			"ge": {
				"∆": "delta",
				"∞": "usasruloba",
				"♥": "siqvaruli",
				"&": "da",
				"|": "an",
				"<": "naklebi",
				">": "meti",
				"∑": "jami",
				"¤": "valuta"
			},
			"gr": {},
			"hu": {
				"∆": "delta",
				"∞": "vegtelen",
				"♥": "szerelem",
				"&": "es",
				"|": "vagy",
				"<": "kisebb mint",
				">": "nagyobb mint",
				"∑": "szumma",
				"¤": "penznem"
			},
			"it": {
				"∆": "delta",
				"∞": "infinito",
				"♥": "amore",
				"&": "e",
				"|": "o",
				"<": "minore di",
				">": "maggiore di",
				"∑": "somma",
				"¤": "moneta"
			},
			"lt": {
				"∆": "delta",
				"∞": "begalybe",
				"♥": "meile",
				"&": "ir",
				"|": "ar",
				"<": "maziau nei",
				">": "daugiau nei",
				"∑": "suma",
				"¤": "valiuta"
			},
			"lv": {
				"∆": "delta",
				"∞": "bezgaliba",
				"♥": "milestiba",
				"&": "un",
				"|": "vai",
				"<": "mazak neka",
				">": "lielaks neka",
				"∑": "summa",
				"¤": "valuta"
			},
			"my": {
				"∆": "kwahkhyaet",
				"∞": "asaonasme",
				"♥": "akhyait",
				"&": "nhin",
				"|": "tho",
				"<": "ngethaw",
				">": "kyithaw",
				"∑": "paungld",
				"¤": "ngwekye"
			},
			"mk": {},
			"nl": {
				"∆": "delta",
				"∞": "oneindig",
				"♥": "liefde",
				"&": "en",
				"|": "of",
				"<": "kleiner dan",
				">": "groter dan",
				"∑": "som",
				"¤": "valuta"
			},
			"pl": {
				"∆": "delta",
				"∞": "nieskonczonosc",
				"♥": "milosc",
				"&": "i",
				"|": "lub",
				"<": "mniejsze niz",
				">": "wieksze niz",
				"∑": "suma",
				"¤": "waluta"
			},
			"pt": {
				"∆": "delta",
				"∞": "infinito",
				"♥": "amor",
				"&": "e",
				"|": "ou",
				"<": "menor que",
				">": "maior que",
				"∑": "soma",
				"¤": "moeda"
			},
			"ro": {
				"∆": "delta",
				"∞": "infinit",
				"♥": "dragoste",
				"&": "si",
				"|": "sau",
				"<": "mai mic ca",
				">": "mai mare ca",
				"∑": "suma",
				"¤": "valuta"
			},
			"ru": {
				"∆": "delta",
				"∞": "beskonechno",
				"♥": "lubov",
				"&": "i",
				"|": "ili",
				"<": "menshe",
				">": "bolshe",
				"∑": "summa",
				"¤": "valjuta"
			},
			"sk": {
				"∆": "delta",
				"∞": "nekonecno",
				"♥": "laska",
				"&": "a",
				"|": "alebo",
				"<": "menej ako",
				">": "viac ako",
				"∑": "sucet",
				"¤": "mena"
			},
			"sr": {},
			"tr": {
				"∆": "delta",
				"∞": "sonsuzluk",
				"♥": "ask",
				"&": "ve",
				"|": "veya",
				"<": "kucuktur",
				">": "buyuktur",
				"∑": "toplam",
				"¤": "para birimi"
			},
			"uk": {
				"∆": "delta",
				"∞": "bezkinechnist",
				"♥": "lubov",
				"&": "i",
				"|": "abo",
				"<": "menshe",
				">": "bilshe",
				"∑": "suma",
				"¤": "valjuta"
			},
			"vn": {
				"∆": "delta",
				"∞": "vo cuc",
				"♥": "yeu",
				"&": "va",
				"|": "hoac",
				"<": "nho hon",
				">": "lon hon",
				"∑": "tong",
				"¤": "tien te"
			}
		};
		var uricChars = [
			";",
			"?",
			":",
			"@",
			"&",
			"=",
			"+",
			"$",
			",",
			"/"
		].join("");
		var uricNoSlashChars = [
			";",
			"?",
			":",
			"@",
			"&",
			"=",
			"+",
			"$",
			","
		].join("");
		var markChars = [
			".",
			"!",
			"~",
			"*",
			"'",
			"(",
			")"
		].join("");
		/**
		* getSlug
		* @param  {string} input input string
		* @param  {object|string} opts config object or separator string/char
		* @api    public
		* @return {string}  sluggified string
		*/
		var getSlug = function getSlug(input, opts) {
			var separator = "-";
			var result = "";
			var diatricString = "";
			var convertSymbols = true;
			var customReplacements = {};
			var maintainCase;
			var titleCase;
			var truncate;
			var uricFlag;
			var uricNoSlashFlag;
			var markFlag;
			var symbol;
			var langChar;
			var lucky;
			var i;
			var ch;
			var l;
			var lastCharWasSymbol;
			var lastCharWasDiatric;
			var allowedChars = "";
			if (typeof input !== "string") return "";
			if (typeof opts === "string") separator = opts;
			symbol = symbolMap.en;
			langChar = langCharMap.en;
			if (typeof opts === "object") {
				maintainCase = opts.maintainCase || false;
				customReplacements = opts.custom && typeof opts.custom === "object" ? opts.custom : customReplacements;
				truncate = +opts.truncate > 1 && opts.truncate || false;
				uricFlag = opts.uric || false;
				uricNoSlashFlag = opts.uricNoSlash || false;
				markFlag = opts.mark || false;
				convertSymbols = opts.symbols === false || opts.lang === false ? false : true;
				separator = opts.separator || separator;
				if (uricFlag) allowedChars += uricChars;
				if (uricNoSlashFlag) allowedChars += uricNoSlashChars;
				if (markFlag) allowedChars += markChars;
				symbol = opts.lang && symbolMap[opts.lang] && convertSymbols ? symbolMap[opts.lang] : convertSymbols ? symbolMap.en : {};
				langChar = opts.lang && langCharMap[opts.lang] ? langCharMap[opts.lang] : opts.lang === false || opts.lang === true ? {} : langCharMap.en;
				if (opts.titleCase && typeof opts.titleCase.length === "number" && Array.prototype.toString.call(opts.titleCase)) {
					opts.titleCase.forEach(function(v) {
						customReplacements[v + ""] = v + "";
					});
					titleCase = true;
				} else titleCase = !!opts.titleCase;
				if (opts.custom && typeof opts.custom.length === "number" && Array.prototype.toString.call(opts.custom)) opts.custom.forEach(function(v) {
					customReplacements[v + ""] = v + "";
				});
				Object.keys(customReplacements).forEach(function(v) {
					var r;
					if (v.length > 1) r = new RegExp("\\b" + escapeChars(v) + "\\b", "gi");
					else r = new RegExp(escapeChars(v), "gi");
					input = input.replace(r, customReplacements[v]);
				});
				for (ch in customReplacements) allowedChars += ch;
			}
			allowedChars += separator;
			allowedChars = escapeChars(allowedChars);
			input = input.replace(/(^\s+|\s+$)/g, "");
			lastCharWasSymbol = false;
			lastCharWasDiatric = false;
			for (i = 0, l = input.length; i < l; i++) {
				ch = input[i];
				if (isReplacedCustomChar(ch, customReplacements)) lastCharWasSymbol = false;
				else if (langChar[ch]) {
					ch = lastCharWasSymbol && langChar[ch].match(/[A-Za-z0-9]/) ? " " + langChar[ch] : langChar[ch];
					lastCharWasSymbol = false;
				} else if (ch in charMap) {
					if (i + 1 < l && lookAheadCharArray.indexOf(input[i + 1]) >= 0) {
						diatricString += ch;
						ch = "";
					} else if (lastCharWasDiatric === true) {
						ch = diatricMap[diatricString] + charMap[ch];
						diatricString = "";
					} else ch = lastCharWasSymbol && charMap[ch].match(/[A-Za-z0-9]/) ? " " + charMap[ch] : charMap[ch];
					lastCharWasSymbol = false;
					lastCharWasDiatric = false;
				} else if (ch in diatricMap) {
					diatricString += ch;
					ch = "";
					if (i === l - 1) ch = diatricMap[diatricString];
					lastCharWasDiatric = true;
				} else if (symbol[ch] && !(uricFlag && uricChars.indexOf(ch) !== -1) && !(uricNoSlashFlag && uricNoSlashChars.indexOf(ch) !== -1)) {
					ch = lastCharWasSymbol || result.substr(-1).match(/[A-Za-z0-9]/) ? separator + symbol[ch] : symbol[ch];
					ch += input[i + 1] !== void 0 && input[i + 1].match(/[A-Za-z0-9]/) ? separator : "";
					lastCharWasSymbol = true;
				} else {
					if (lastCharWasDiatric === true) {
						ch = diatricMap[diatricString] + ch;
						diatricString = "";
						lastCharWasDiatric = false;
					} else if (lastCharWasSymbol && (/[A-Za-z0-9]/.test(ch) || result.substr(-1).match(/A-Za-z0-9]/))) ch = " " + ch;
					lastCharWasSymbol = false;
				}
				result += ch.replace(new RegExp("[^\\w\\s" + allowedChars + "_-]", "g"), separator);
			}
			if (titleCase) result = result.replace(/(\w)(\S*)/g, function(_, i, r) {
				var j = i.toUpperCase() + (r !== null ? r : "");
				return Object.keys(customReplacements).indexOf(j.toLowerCase()) < 0 ? j : j.toLowerCase();
			});
			result = result.replace(/\s+/g, separator).replace(new RegExp("\\" + separator + "+", "g"), separator).replace(new RegExp("(^\\" + separator + "+|\\" + separator + "+$)", "g"), "");
			if (truncate && result.length > truncate) {
				lucky = result.charAt(truncate) === separator;
				result = result.slice(0, truncate);
				if (!lucky) result = result.slice(0, result.lastIndexOf(separator));
			}
			if (!maintainCase && !titleCase) result = result.toLowerCase();
			return result;
		};
		/**
		* createSlug curried(opts)(input)
		* @param   {object|string} opts config object or input string
		* @return  {Function} function getSlugWithConfig()
		**/
		var createSlug = function createSlug(opts) {
			/**
			* getSlugWithConfig
			* @param   {string} input string
			* @return  {string} slug string
			*/
			return function getSlugWithConfig(input) {
				return getSlug(input, opts);
			};
		};
		/**
		* escape Chars
		* @param   {string} input string
		*/
		var escapeChars = function escapeChars(input) {
			return input.replace(/[-\\^$*+?.()|[\]{}\/]/g, "\\$&");
		};
		/**
		* check if the char is an already converted char from custom list
		* @param   {char} ch character to check
		* @param   {object} customReplacements custom translation map
		*/
		var isReplacedCustomChar = function(ch, customReplacements) {
			for (var c in customReplacements) if (customReplacements[c] === ch) return true;
		};
		if (typeof module !== "undefined" && module.exports) {
			module.exports = getSlug;
			module.exports.createSlug = createSlug;
		} else if (typeof define !== "undefined" && define.amd) define([], function() {
			return getSlug;
		});
		else try {
			if (root.getSlug || root.createSlug) throw "speakingurl: globals exists /(getSlug|createSlug)/";
			else {
				root.getSlug = getSlug;
				root.createSlug = createSlug;
			}
		} catch (e) {}
	})(exports);
}));
//#endregion
//#region src/core/app/index.ts
var import_speakingurl = /* @__PURE__ */ __toESM((/* @__PURE__ */ __commonJSMin(((exports, module) => {
	module.exports = require_speakingurl$1();
})))(), 1);
const appRecordInfo = _vue_devtools_shared.target.__VUE_DEVTOOLS_NEXT_APP_RECORD_INFO__ ??= {
	id: 0,
	appIds: /* @__PURE__ */ new Set()
};
function getAppRecordName(app, fallbackName) {
	return app?._component?.name || `App ${fallbackName}`;
}
function getAppRootInstance(app) {
	if (app._instance) return app._instance;
	else if (app._container?._vnode?.component) return app._container?._vnode?.component;
}
function removeAppRecordId(app) {
	const id = app.__VUE_DEVTOOLS_NEXT_APP_RECORD_ID__;
	if (id != null) {
		appRecordInfo.appIds.delete(id);
		appRecordInfo.id--;
	}
}
function getAppRecordId(app, defaultId) {
	if (app.__VUE_DEVTOOLS_NEXT_APP_RECORD_ID__ != null) return app.__VUE_DEVTOOLS_NEXT_APP_RECORD_ID__;
	let id = defaultId ?? (appRecordInfo.id++).toString();
	if (defaultId && appRecordInfo.appIds.has(id)) {
		let count = 1;
		while (appRecordInfo.appIds.has(`${defaultId}_${count}`)) count++;
		id = `${defaultId}_${count}`;
	}
	appRecordInfo.appIds.add(id);
	app.__VUE_DEVTOOLS_NEXT_APP_RECORD_ID__ = id;
	return id;
}
function createAppRecord(app, types) {
	const rootInstance = getAppRootInstance(app);
	if (rootInstance) {
		appRecordInfo.id++;
		const name = getAppRecordName(app, appRecordInfo.id.toString());
		const id = getAppRecordId(app, (0, import_speakingurl.default)(name));
		const [el] = getRootElementsFromComponentInstance(rootInstance);
		const record = {
			id,
			name,
			types,
			instanceMap: /* @__PURE__ */ new Map(),
			perfGroupIds: /* @__PURE__ */ new Map(),
			rootInstance,
			iframe: _vue_devtools_shared.isBrowser && document !== el?.ownerDocument ? el?.ownerDocument?.location?.pathname : void 0
		};
		app.__VUE_DEVTOOLS_NEXT_APP_RECORD__ = record;
		const rootId = `${record.id}:root`;
		record.instanceMap.set(rootId, record.rootInstance);
		record.rootInstance.__VUE_DEVTOOLS_NEXT_UID__ = rootId;
		return record;
	} else return {};
}
//#endregion
//#region src/core/iframe/index.ts
function detectIframeApp(target, inIframe = false) {
	if (inIframe) {
		function sendEventToParent(cb) {
			try {
				const hook = window.parent.__VUE_DEVTOOLS_GLOBAL_HOOK__;
				if (hook) cb(hook);
			} catch (e) {}
		}
		const hook = {
			id: "vue-devtools-next",
			devtoolsVersion: "7.0",
			on: (event, cb) => {
				sendEventToParent((hook) => {
					hook.on(event, cb);
				});
			},
			once: (event, cb) => {
				sendEventToParent((hook) => {
					hook.once(event, cb);
				});
			},
			off: (event, cb) => {
				sendEventToParent((hook) => {
					hook.off(event, cb);
				});
			},
			emit: (event, ...payload) => {
				sendEventToParent((hook) => {
					hook.emit(event, ...payload);
				});
			}
		};
		Object.defineProperty(target, "__VUE_DEVTOOLS_GLOBAL_HOOK__", {
			get() {
				return hook;
			},
			configurable: true
		});
	}
	function injectVueHookToIframe(iframe) {
		if (iframe.__vdevtools__injected) return;
		try {
			iframe.__vdevtools__injected = true;
			const inject = () => {
				try {
					iframe.contentWindow.__VUE_DEVTOOLS_IFRAME__ = iframe;
					const script = iframe.contentDocument.createElement("script");
					script.textContent = `;(${detectIframeApp.toString()})(window, true)`;
					iframe.contentDocument.documentElement.appendChild(script);
					script.parentNode.removeChild(script);
				} catch (e) {}
			};
			inject();
			iframe.addEventListener("load", () => inject());
		} catch (e) {}
	}
	function injectVueHookToIframes() {
		if (typeof window === "undefined") return;
		const iframes = Array.from(document.querySelectorAll("iframe:not([data-vue-devtools-ignore])"));
		for (const iframe of iframes) injectVueHookToIframe(iframe);
	}
	injectVueHookToIframes();
	let iframeAppChecks = 0;
	const iframeAppCheckTimer = setInterval(() => {
		injectVueHookToIframes();
		iframeAppChecks++;
		if (iframeAppChecks >= 5) clearInterval(iframeAppCheckTimer);
	}, 1e3);
}
//#endregion
//#region src/core/index.ts
function initDevTools() {
	detectIframeApp(_vue_devtools_shared.target);
	updateDevToolsState({ vitePluginDetected: getDevToolsEnv().vitePluginDetected });
	const isDevToolsNext = _vue_devtools_shared.target.__VUE_DEVTOOLS_GLOBAL_HOOK__?.id === "vue-devtools-next";
	if (_vue_devtools_shared.target.__VUE_DEVTOOLS_GLOBAL_HOOK__ && isDevToolsNext) return;
	const _devtoolsHook = createDevToolsHook();
	if (_vue_devtools_shared.target.__VUE_DEVTOOLS_HOOK_REPLAY__) try {
		_vue_devtools_shared.target.__VUE_DEVTOOLS_HOOK_REPLAY__.forEach((cb) => cb(_devtoolsHook));
		_vue_devtools_shared.target.__VUE_DEVTOOLS_HOOK_REPLAY__ = [];
	} catch (e) {
		console.error("[vue-devtools] Error during hook replay", e);
	}
	_devtoolsHook.once("init", (Vue) => {
		_vue_devtools_shared.target.__VUE_DEVTOOLS_VUE2_APP_DETECTED__ = true;
		console.log("%c[_____Vue DevTools v7 log_____]", "color: red; font-bold: 600; font-size: 16px;");
		console.log("%cVue DevTools v7 detected in your Vue2 project. v7 only supports Vue3 and will not work.", "font-bold: 500; font-size: 14px;");
		const legacyChromeUrl = "https://chromewebstore.google.com/detail/vuejs-devtools/iaajmlceplecbljialhhkmedjlpdblhp";
		const legacyFirefoxUrl = "https://addons.mozilla.org/firefox/addon/vue-js-devtools-v6-legacy";
		console.log(`%cThe legacy version of chrome extension that supports both Vue 2 and Vue 3 has been moved to %c ${legacyChromeUrl}`, "font-size: 14px;", "text-decoration: underline; cursor: pointer;font-size: 14px;");
		console.log(`%cThe legacy version of firefox extension that supports both Vue 2 and Vue 3 has been moved to %c ${legacyFirefoxUrl}`, "font-size: 14px;", "text-decoration: underline; cursor: pointer;font-size: 14px;");
		console.log("%cPlease install and enable only the legacy version for your Vue2 app.", "font-bold: 500; font-size: 14px;");
		console.log("%c[_____Vue DevTools v7 log_____]", "color: red; font-bold: 600; font-size: 16px;");
	});
	hook.on.setupDevtoolsPlugin((pluginDescriptor, setupFn) => {
		addDevToolsPluginToBuffer(pluginDescriptor, setupFn);
		const { app } = activeAppRecord ?? {};
		if (pluginDescriptor.settings) initPluginSettings(pluginDescriptor.id, pluginDescriptor.settings);
		if (!app) return;
		callDevToolsPluginSetupFn([pluginDescriptor, setupFn], app);
	});
	onLegacyDevToolsPluginApiAvailable(() => {
		devtoolsPluginBuffer.filter(([item]) => item.id !== "components").forEach(([pluginDescriptor, setupFn]) => {
			_devtoolsHook.emit(DevToolsHooks.SETUP_DEVTOOLS_PLUGIN, pluginDescriptor, setupFn, { target: "legacy" });
		});
	});
	hook.on.vueAppInit(async (app, version, types) => {
		const normalizedAppRecord = {
			...createAppRecord(app, types),
			app,
			version
		};
		addDevToolsAppRecord(normalizedAppRecord);
		if (devtoolsAppRecords.value.length === 1) {
			setActiveAppRecord(normalizedAppRecord);
			setActiveAppRecordId(normalizedAppRecord.id);
			normalizeRouterInfo(normalizedAppRecord, activeAppRecord);
			registerDevToolsPlugin(normalizedAppRecord.app);
		}
		setupDevToolsPlugin(...createComponentsDevToolsPlugin(normalizedAppRecord.app));
		updateDevToolsState({ connected: true });
		_devtoolsHook.apps.push(app);
	});
	hook.on.vueAppUnmount(async (app) => {
		const activeRecords = devtoolsAppRecords.value.filter((appRecord) => appRecord.app !== app);
		if (activeRecords.length === 0) updateDevToolsState({ connected: false });
		removeDevToolsAppRecord(app);
		removeAppRecordId(app);
		if (activeAppRecord.value.app === app) {
			setActiveAppRecord(activeRecords[0]);
			devtoolsContext.hooks.callHook(DevToolsMessagingHookKeys.SEND_ACTIVE_APP_UNMOUNTED_TO_CLIENT);
		}
		_vue_devtools_shared.target.__VUE_DEVTOOLS_GLOBAL_HOOK__.apps.splice(_vue_devtools_shared.target.__VUE_DEVTOOLS_GLOBAL_HOOK__.apps.indexOf(app), 1);
		removeRegisteredPluginApp(app);
	});
	subscribeDevToolsHook(_devtoolsHook);
	if (!_vue_devtools_shared.target.__VUE_DEVTOOLS_GLOBAL_HOOK__) Object.defineProperty(_vue_devtools_shared.target, "__VUE_DEVTOOLS_GLOBAL_HOOK__", {
		get() {
			return _devtoolsHook;
		},
		configurable: true
	});
	else if (!_vue_devtools_shared.isNuxtApp) Object.assign(__VUE_DEVTOOLS_GLOBAL_HOOK__, _devtoolsHook);
}
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
//#region src/core/high-perf-mode/index.ts
function toggleHighPerfMode(state) {
	devtoolsState.highPerfModeEnabled = state ?? !devtoolsState.highPerfModeEnabled;
	if (!state && activeAppRecord.value) registerDevToolsPlugin(activeAppRecord.value.app);
}
//#endregion
//#region src/core/component/state/reviver.ts
function reviveSet(val) {
	const result = /* @__PURE__ */ new Set();
	const list = val._custom.value;
	for (let i = 0; i < list.length; i++) {
		const value = list[i];
		result.add(revive(value));
	}
	return result;
}
function reviveMap(val) {
	const result = /* @__PURE__ */ new Map();
	const list = val._custom.value;
	for (let i = 0; i < list.length; i++) {
		const { key, value } = list[i];
		result.set(key, revive(value));
	}
	return result;
}
function revive(val) {
	if (val === "__vue_devtool_undefined__") return;
	else if (val === "__vue_devtool_infinity__") return Number.POSITIVE_INFINITY;
	else if (val === "__vue_devtool_negative_infinity__") return Number.NEGATIVE_INFINITY;
	else if (val === "__vue_devtool_nan__") return NaN;
	else if (val && val._custom) {
		const { _custom: custom } = val;
		if (custom.type === "component") return activeAppRecord.value.instanceMap.get(custom.id);
		else if (custom.type === "map") return reviveMap(val);
		else if (custom.type === "set") return reviveSet(val);
		else if (custom.type === "bigint") return BigInt(custom.value);
		else return revive(custom.value);
	} else if (symbolRE.test(val)) {
		const [, string] = symbolRE.exec(val);
		return Symbol.for(string);
	} else if (specialTypeRE.test(val)) {
		const [, type, string, , details] = specialTypeRE.exec(val);
		const result = new _vue_devtools_shared.target[type](string);
		if (type === "Error" && details) result.stack = details;
		return result;
	} else return val;
}
function reviver(key, value) {
	return revive(value);
}
//#endregion
//#region src/core/component/state/format.ts
function getInspectorStateValueType(value, raw = true) {
	const type = typeof value;
	if (value == null || value === "__vue_devtool_undefined__" || value === "undefined") return "null";
	else if (type === "boolean" || type === "number" || value === "__vue_devtool_infinity__" || value === "__vue_devtool_negative_infinity__" || value === "__vue_devtool_nan__") return "literal";
	else if (value?._custom) if (raw || value._custom.display != null || value._custom.displayText != null) return "custom";
	else return getInspectorStateValueType(value._custom.value);
	else if (typeof value === "string") {
		const typeMatch = specialTypeRE.exec(value);
		if (typeMatch) {
			const [, type] = typeMatch;
			return `native ${type}`;
		} else return "string";
	} else if (Array.isArray(value) || value?._isArray) return "array";
	else if (isPlainObject(value)) return "plain-object";
	else return "unknown";
}
function formatInspectorStateValue(value, quotes = false, options) {
	const { customClass } = options ?? {};
	let result;
	const type = getInspectorStateValueType(value, false);
	if (type !== "custom" && value?._custom) value = value._custom.value;
	if (result = internalStateTokenToString(value)) return result;
	else if (type === "custom") return value._custom.value?._custom && formatInspectorStateValue(value._custom.value, quotes, options) || value._custom.displayText || value._custom.display;
	else if (type === "array") return `Array[${value.length}]`;
	else if (type === "plain-object") return `Object${Object.keys(value).length ? "" : " (empty)"}`;
	else if (type?.includes("native")) return escape(specialTypeRE.exec(value)?.[2]);
	else if (typeof value === "string") {
		const typeMatch = value.match(rawTypeRE);
		if (typeMatch) value = escapeString(typeMatch[1]);
		else if (quotes) value = `<span>"</span>${customClass?.string ? `<span class=${customClass.string}>${escapeString(value)}</span>` : escapeString(value)}<span>"</span>`;
		else value = customClass?.string ? `<span class="${customClass?.string ?? ""}">${escapeString(value)}</span>` : escapeString(value);
	}
	return value;
}
function escapeString(value) {
	return escape(value).replace(/ /g, "&nbsp;").replace(/\n/g, "<span>\\n</span>");
}
function getRaw(value) {
	let customType;
	const isCustom = getInspectorStateValueType(value) === "custom";
	let inherit = {};
	if (isCustom) {
		const data = value;
		const customValue = data._custom?.value;
		const currentCustomType = data._custom?.type;
		const nestedCustom = typeof customValue === "object" && customValue !== null && "_custom" in customValue ? getRaw(customValue) : {
			inherit: void 0,
			value: void 0,
			customType: void 0
		};
		inherit = nestedCustom.inherit || data._custom?.fields || {};
		value = nestedCustom.value || customValue;
		customType = nestedCustom.customType || currentCustomType;
	}
	if (value && value._isArray) value = value.items;
	return {
		value,
		inherit,
		customType
	};
}
function toEdit(value, customType) {
	if (customType === "bigint") return value;
	if (customType === "date") return value;
	return replaceTokenToString(JSON.stringify(value));
}
function toSubmit(value, customType) {
	if (customType === "bigint") return BigInt(value);
	if (customType === "date") return new Date(value);
	return JSON.parse(replaceStringToToken(value), reviver);
}
//#endregion
//#region src/core/devtools-client/detected.ts
function updateDevToolsClientDetected(params) {
	devtoolsState.devtoolsClientDetected = {
		...devtoolsState.devtoolsClientDetected,
		...params
	};
	toggleHighPerfMode(!Object.values(devtoolsState.devtoolsClientDetected).some(Boolean));
}
_vue_devtools_shared.target.__VUE_DEVTOOLS_UPDATE_CLIENT_DETECTED__ ??= updateDevToolsClientDetected;
//#endregion
//#region ../../node_modules/.pnpm/superjson@2.2.2/node_modules/superjson/dist/double-indexed-kv.js
var DoubleIndexedKV = class {
	constructor() {
		this.keyToValue = /* @__PURE__ */ new Map();
		this.valueToKey = /* @__PURE__ */ new Map();
	}
	set(key, value) {
		this.keyToValue.set(key, value);
		this.valueToKey.set(value, key);
	}
	getByKey(key) {
		return this.keyToValue.get(key);
	}
	getByValue(value) {
		return this.valueToKey.get(value);
	}
	clear() {
		this.keyToValue.clear();
		this.valueToKey.clear();
	}
};
//#endregion
//#region ../../node_modules/.pnpm/superjson@2.2.2/node_modules/superjson/dist/registry.js
var Registry = class {
	constructor(generateIdentifier) {
		this.generateIdentifier = generateIdentifier;
		this.kv = new DoubleIndexedKV();
	}
	register(value, identifier) {
		if (this.kv.getByValue(value)) return;
		if (!identifier) identifier = this.generateIdentifier(value);
		this.kv.set(identifier, value);
	}
	clear() {
		this.kv.clear();
	}
	getIdentifier(value) {
		return this.kv.getByValue(value);
	}
	getValue(identifier) {
		return this.kv.getByKey(identifier);
	}
};
//#endregion
//#region ../../node_modules/.pnpm/superjson@2.2.2/node_modules/superjson/dist/class-registry.js
var ClassRegistry = class extends Registry {
	constructor() {
		super((c) => c.name);
		this.classToAllowedProps = /* @__PURE__ */ new Map();
	}
	register(value, options) {
		if (typeof options === "object") {
			if (options.allowProps) this.classToAllowedProps.set(value, options.allowProps);
			super.register(value, options.identifier);
		} else super.register(value, options);
	}
	getAllowedProps(value) {
		return this.classToAllowedProps.get(value);
	}
};
//#endregion
//#region ../../node_modules/.pnpm/superjson@2.2.2/node_modules/superjson/dist/util.js
function valuesOfObj(record) {
	if ("values" in Object) return Object.values(record);
	const values = [];
	for (const key in record) if (record.hasOwnProperty(key)) values.push(record[key]);
	return values;
}
function find(record, predicate) {
	const values = valuesOfObj(record);
	if ("find" in values) return values.find(predicate);
	const valuesNotNever = values;
	for (let i = 0; i < valuesNotNever.length; i++) {
		const value = valuesNotNever[i];
		if (predicate(value)) return value;
	}
}
function forEach(record, run) {
	Object.entries(record).forEach(([key, value]) => run(value, key));
}
function includes(arr, value) {
	return arr.indexOf(value) !== -1;
}
function findArr(record, predicate) {
	for (let i = 0; i < record.length; i++) {
		const value = record[i];
		if (predicate(value)) return value;
	}
}
//#endregion
//#region ../../node_modules/.pnpm/superjson@2.2.2/node_modules/superjson/dist/custom-transformer-registry.js
var CustomTransformerRegistry = class {
	constructor() {
		this.transfomers = {};
	}
	register(transformer) {
		this.transfomers[transformer.name] = transformer;
	}
	findApplicable(v) {
		return find(this.transfomers, (transformer) => transformer.isApplicable(v));
	}
	findByName(name) {
		return this.transfomers[name];
	}
};
//#endregion
//#region ../../node_modules/.pnpm/superjson@2.2.2/node_modules/superjson/dist/is.js
const getType$1 = (payload) => Object.prototype.toString.call(payload).slice(8, -1);
const isUndefined$1 = (payload) => typeof payload === "undefined";
const isNull$1 = (payload) => payload === null;
const isPlainObject$2 = (payload) => {
	if (typeof payload !== "object" || payload === null) return false;
	if (payload === Object.prototype) return false;
	if (Object.getPrototypeOf(payload) === null) return true;
	return Object.getPrototypeOf(payload) === Object.prototype;
};
const isEmptyObject = (payload) => isPlainObject$2(payload) && Object.keys(payload).length === 0;
const isArray$2 = (payload) => Array.isArray(payload);
const isString = (payload) => typeof payload === "string";
const isNumber = (payload) => typeof payload === "number" && !isNaN(payload);
const isBoolean = (payload) => typeof payload === "boolean";
const isRegExp = (payload) => payload instanceof RegExp;
const isMap = (payload) => payload instanceof Map;
const isSet = (payload) => payload instanceof Set;
const isSymbol = (payload) => getType$1(payload) === "Symbol";
const isDate = (payload) => payload instanceof Date && !isNaN(payload.valueOf());
const isError = (payload) => payload instanceof Error;
const isNaNValue = (payload) => typeof payload === "number" && isNaN(payload);
const isPrimitive = (payload) => isBoolean(payload) || isNull$1(payload) || isUndefined$1(payload) || isNumber(payload) || isString(payload) || isSymbol(payload);
const isBigint = (payload) => typeof payload === "bigint";
const isInfinite = (payload) => payload === Infinity || payload === -Infinity;
const isTypedArray = (payload) => ArrayBuffer.isView(payload) && !(payload instanceof DataView);
const isURL = (payload) => payload instanceof URL;
//#endregion
//#region ../../node_modules/.pnpm/superjson@2.2.2/node_modules/superjson/dist/pathstringifier.js
const escapeKey = (key) => key.replace(/\./g, "\\.");
const stringifyPath = (path) => path.map(String).map(escapeKey).join(".");
const parsePath = (string) => {
	const result = [];
	let segment = "";
	for (let i = 0; i < string.length; i++) {
		let char = string.charAt(i);
		if (char === "\\" && string.charAt(i + 1) === ".") {
			segment += ".";
			i++;
			continue;
		}
		if (char === ".") {
			result.push(segment);
			segment = "";
			continue;
		}
		segment += char;
	}
	const lastSegment = segment;
	result.push(lastSegment);
	return result;
};
//#endregion
//#region ../../node_modules/.pnpm/superjson@2.2.2/node_modules/superjson/dist/transformer.js
function simpleTransformation(isApplicable, annotation, transform, untransform) {
	return {
		isApplicable,
		annotation,
		transform,
		untransform
	};
}
const simpleRules = [
	simpleTransformation(isUndefined$1, "undefined", () => null, () => void 0),
	simpleTransformation(isBigint, "bigint", (v) => v.toString(), (v) => {
		if (typeof BigInt !== "undefined") return BigInt(v);
		console.error("Please add a BigInt polyfill.");
		return v;
	}),
	simpleTransformation(isDate, "Date", (v) => v.toISOString(), (v) => new Date(v)),
	simpleTransformation(isError, "Error", (v, superJson) => {
		const baseError = {
			name: v.name,
			message: v.message
		};
		superJson.allowedErrorProps.forEach((prop) => {
			baseError[prop] = v[prop];
		});
		return baseError;
	}, (v, superJson) => {
		const e = new Error(v.message);
		e.name = v.name;
		e.stack = v.stack;
		superJson.allowedErrorProps.forEach((prop) => {
			e[prop] = v[prop];
		});
		return e;
	}),
	simpleTransformation(isRegExp, "regexp", (v) => "" + v, (regex) => {
		const body = regex.slice(1, regex.lastIndexOf("/"));
		const flags = regex.slice(regex.lastIndexOf("/") + 1);
		return new RegExp(body, flags);
	}),
	simpleTransformation(isSet, "set", (v) => [...v.values()], (v) => new Set(v)),
	simpleTransformation(isMap, "map", (v) => [...v.entries()], (v) => new Map(v)),
	simpleTransformation((v) => isNaNValue(v) || isInfinite(v), "number", (v) => {
		if (isNaNValue(v)) return "NaN";
		if (v > 0) return "Infinity";
		else return "-Infinity";
	}, Number),
	simpleTransformation((v) => v === 0 && 1 / v === -Infinity, "number", () => {
		return "-0";
	}, Number),
	simpleTransformation(isURL, "URL", (v) => v.toString(), (v) => new URL(v))
];
function compositeTransformation(isApplicable, annotation, transform, untransform) {
	return {
		isApplicable,
		annotation,
		transform,
		untransform
	};
}
const symbolRule = compositeTransformation((s, superJson) => {
	if (isSymbol(s)) return !!superJson.symbolRegistry.getIdentifier(s);
	return false;
}, (s, superJson) => {
	return ["symbol", superJson.symbolRegistry.getIdentifier(s)];
}, (v) => v.description, (_, a, superJson) => {
	const value = superJson.symbolRegistry.getValue(a[1]);
	if (!value) throw new Error("Trying to deserialize unknown symbol");
	return value;
});
const constructorToName = [
	Int8Array,
	Uint8Array,
	Int16Array,
	Uint16Array,
	Int32Array,
	Uint32Array,
	Float32Array,
	Float64Array,
	Uint8ClampedArray
].reduce((obj, ctor) => {
	obj[ctor.name] = ctor;
	return obj;
}, {});
const typedArrayRule = compositeTransformation(isTypedArray, (v) => ["typed-array", v.constructor.name], (v) => [...v], (v, a) => {
	const ctor = constructorToName[a[1]];
	if (!ctor) throw new Error("Trying to deserialize unknown typed array");
	return new ctor(v);
});
function isInstanceOfRegisteredClass(potentialClass, superJson) {
	if (potentialClass?.constructor) return !!superJson.classRegistry.getIdentifier(potentialClass.constructor);
	return false;
}
const classRule = compositeTransformation(isInstanceOfRegisteredClass, (clazz, superJson) => {
	return ["class", superJson.classRegistry.getIdentifier(clazz.constructor)];
}, (clazz, superJson) => {
	const allowedProps = superJson.classRegistry.getAllowedProps(clazz.constructor);
	if (!allowedProps) return { ...clazz };
	const result = {};
	allowedProps.forEach((prop) => {
		result[prop] = clazz[prop];
	});
	return result;
}, (v, a, superJson) => {
	const clazz = superJson.classRegistry.getValue(a[1]);
	if (!clazz) throw new Error(`Trying to deserialize unknown class '${a[1]}' - check https://github.com/blitz-js/superjson/issues/116#issuecomment-773996564`);
	return Object.assign(Object.create(clazz.prototype), v);
});
const customRule = compositeTransformation((value, superJson) => {
	return !!superJson.customTransformerRegistry.findApplicable(value);
}, (value, superJson) => {
	return ["custom", superJson.customTransformerRegistry.findApplicable(value).name];
}, (value, superJson) => {
	return superJson.customTransformerRegistry.findApplicable(value).serialize(value);
}, (v, a, superJson) => {
	const transformer = superJson.customTransformerRegistry.findByName(a[1]);
	if (!transformer) throw new Error("Trying to deserialize unknown custom value");
	return transformer.deserialize(v);
});
const compositeRules = [
	classRule,
	symbolRule,
	customRule,
	typedArrayRule
];
const transformValue = (value, superJson) => {
	const applicableCompositeRule = findArr(compositeRules, (rule) => rule.isApplicable(value, superJson));
	if (applicableCompositeRule) return {
		value: applicableCompositeRule.transform(value, superJson),
		type: applicableCompositeRule.annotation(value, superJson)
	};
	const applicableSimpleRule = findArr(simpleRules, (rule) => rule.isApplicable(value, superJson));
	if (applicableSimpleRule) return {
		value: applicableSimpleRule.transform(value, superJson),
		type: applicableSimpleRule.annotation
	};
};
const simpleRulesByAnnotation = {};
simpleRules.forEach((rule) => {
	simpleRulesByAnnotation[rule.annotation] = rule;
});
const untransformValue = (json, type, superJson) => {
	if (isArray$2(type)) switch (type[0]) {
		case "symbol": return symbolRule.untransform(json, type, superJson);
		case "class": return classRule.untransform(json, type, superJson);
		case "custom": return customRule.untransform(json, type, superJson);
		case "typed-array": return typedArrayRule.untransform(json, type, superJson);
		default: throw new Error("Unknown transformation: " + type);
	}
	else {
		const transformation = simpleRulesByAnnotation[type];
		if (!transformation) throw new Error("Unknown transformation: " + type);
		return transformation.untransform(json, superJson);
	}
};
//#endregion
//#region ../../node_modules/.pnpm/superjson@2.2.2/node_modules/superjson/dist/accessDeep.js
const getNthKey = (value, n) => {
	if (n > value.size) throw new Error("index out of bounds");
	const keys = value.keys();
	while (n > 0) {
		keys.next();
		n--;
	}
	return keys.next().value;
};
function validatePath(path) {
	if (includes(path, "__proto__")) throw new Error("__proto__ is not allowed as a property");
	if (includes(path, "prototype")) throw new Error("prototype is not allowed as a property");
	if (includes(path, "constructor")) throw new Error("constructor is not allowed as a property");
}
const getDeep = (object, path) => {
	validatePath(path);
	for (let i = 0; i < path.length; i++) {
		const key = path[i];
		if (isSet(object)) object = getNthKey(object, +key);
		else if (isMap(object)) {
			const row = +key;
			const type = +path[++i] === 0 ? "key" : "value";
			const keyOfRow = getNthKey(object, row);
			switch (type) {
				case "key":
					object = keyOfRow;
					break;
				case "value":
					object = object.get(keyOfRow);
					break;
			}
		} else object = object[key];
	}
	return object;
};
const setDeep = (object, path, mapper) => {
	validatePath(path);
	if (path.length === 0) return mapper(object);
	let parent = object;
	for (let i = 0; i < path.length - 1; i++) {
		const key = path[i];
		if (isArray$2(parent)) {
			const index = +key;
			parent = parent[index];
		} else if (isPlainObject$2(parent)) parent = parent[key];
		else if (isSet(parent)) {
			const row = +key;
			parent = getNthKey(parent, row);
		} else if (isMap(parent)) {
			if (i === path.length - 2) break;
			const row = +key;
			const type = +path[++i] === 0 ? "key" : "value";
			const keyOfRow = getNthKey(parent, row);
			switch (type) {
				case "key":
					parent = keyOfRow;
					break;
				case "value":
					parent = parent.get(keyOfRow);
					break;
			}
		}
	}
	const lastKey = path[path.length - 1];
	if (isArray$2(parent)) parent[+lastKey] = mapper(parent[+lastKey]);
	else if (isPlainObject$2(parent)) parent[lastKey] = mapper(parent[lastKey]);
	if (isSet(parent)) {
		const oldValue = getNthKey(parent, +lastKey);
		const newValue = mapper(oldValue);
		if (oldValue !== newValue) {
			parent.delete(oldValue);
			parent.add(newValue);
		}
	}
	if (isMap(parent)) {
		const row = +path[path.length - 2];
		const keyToRow = getNthKey(parent, row);
		switch (+lastKey === 0 ? "key" : "value") {
			case "key": {
				const newKey = mapper(keyToRow);
				parent.set(newKey, parent.get(keyToRow));
				if (newKey !== keyToRow) parent.delete(keyToRow);
				break;
			}
			case "value":
				parent.set(keyToRow, mapper(parent.get(keyToRow)));
				break;
		}
	}
	return object;
};
//#endregion
//#region ../../node_modules/.pnpm/superjson@2.2.2/node_modules/superjson/dist/plainer.js
function traverse(tree, walker, origin = []) {
	if (!tree) return;
	if (!isArray$2(tree)) {
		forEach(tree, (subtree, key) => traverse(subtree, walker, [...origin, ...parsePath(key)]));
		return;
	}
	const [nodeValue, children] = tree;
	if (children) forEach(children, (child, key) => {
		traverse(child, walker, [...origin, ...parsePath(key)]);
	});
	walker(nodeValue, origin);
}
function applyValueAnnotations(plain, annotations, superJson) {
	traverse(annotations, (type, path) => {
		plain = setDeep(plain, path, (v) => untransformValue(v, type, superJson));
	});
	return plain;
}
function applyReferentialEqualityAnnotations(plain, annotations) {
	function apply(identicalPaths, path) {
		const object = getDeep(plain, parsePath(path));
		identicalPaths.map(parsePath).forEach((identicalObjectPath) => {
			plain = setDeep(plain, identicalObjectPath, () => object);
		});
	}
	if (isArray$2(annotations)) {
		const [root, other] = annotations;
		root.forEach((identicalPath) => {
			plain = setDeep(plain, parsePath(identicalPath), () => plain);
		});
		if (other) forEach(other, apply);
	} else forEach(annotations, apply);
	return plain;
}
const isDeep = (object, superJson) => isPlainObject$2(object) || isArray$2(object) || isMap(object) || isSet(object) || isInstanceOfRegisteredClass(object, superJson);
function addIdentity(object, path, identities) {
	const existingSet = identities.get(object);
	if (existingSet) existingSet.push(path);
	else identities.set(object, [path]);
}
function generateReferentialEqualityAnnotations(identitites, dedupe) {
	const result = {};
	let rootEqualityPaths = void 0;
	identitites.forEach((paths) => {
		if (paths.length <= 1) return;
		if (!dedupe) paths = paths.map((path) => path.map(String)).sort((a, b) => a.length - b.length);
		const [representativePath, ...identicalPaths] = paths;
		if (representativePath.length === 0) rootEqualityPaths = identicalPaths.map(stringifyPath);
		else result[stringifyPath(representativePath)] = identicalPaths.map(stringifyPath);
	});
	if (rootEqualityPaths) if (isEmptyObject(result)) return [rootEqualityPaths];
	else return [rootEqualityPaths, result];
	else return isEmptyObject(result) ? void 0 : result;
}
const walker = (object, identities, superJson, dedupe, path = [], objectsInThisPath = [], seenObjects = /* @__PURE__ */ new Map()) => {
	const primitive = isPrimitive(object);
	if (!primitive) {
		addIdentity(object, path, identities);
		const seen = seenObjects.get(object);
		if (seen) return dedupe ? { transformedValue: null } : seen;
	}
	if (!isDeep(object, superJson)) {
		const transformed = transformValue(object, superJson);
		const result = transformed ? {
			transformedValue: transformed.value,
			annotations: [transformed.type]
		} : { transformedValue: object };
		if (!primitive) seenObjects.set(object, result);
		return result;
	}
	if (includes(objectsInThisPath, object)) return { transformedValue: null };
	const transformationResult = transformValue(object, superJson);
	const transformed = transformationResult?.value ?? object;
	const transformedValue = isArray$2(transformed) ? [] : {};
	const innerAnnotations = {};
	forEach(transformed, (value, index) => {
		if (index === "__proto__" || index === "constructor" || index === "prototype") throw new Error(`Detected property ${index}. This is a prototype pollution risk, please remove it from your object.`);
		const recursiveResult = walker(value, identities, superJson, dedupe, [...path, index], [...objectsInThisPath, object], seenObjects);
		transformedValue[index] = recursiveResult.transformedValue;
		if (isArray$2(recursiveResult.annotations)) innerAnnotations[index] = recursiveResult.annotations;
		else if (isPlainObject$2(recursiveResult.annotations)) forEach(recursiveResult.annotations, (tree, key) => {
			innerAnnotations[escapeKey(index) + "." + key] = tree;
		});
	});
	const result = isEmptyObject(innerAnnotations) ? {
		transformedValue,
		annotations: !!transformationResult ? [transformationResult.type] : void 0
	} : {
		transformedValue,
		annotations: !!transformationResult ? [transformationResult.type, innerAnnotations] : innerAnnotations
	};
	if (!primitive) seenObjects.set(object, result);
	return result;
};
//#endregion
//#region ../../node_modules/.pnpm/is-what@4.1.16/node_modules/is-what/dist/index.js
function getType(payload) {
	return Object.prototype.toString.call(payload).slice(8, -1);
}
function isArray$1(payload) {
	return getType(payload) === "Array";
}
function isPlainObject$1(payload) {
	if (getType(payload) !== "Object") return false;
	const prototype = Object.getPrototypeOf(payload);
	return !!prototype && prototype.constructor === Object && prototype === Object.prototype;
}
function isNull(payload) {
	return getType(payload) === "Null";
}
function isOneOf(a, b, c, d, e) {
	return (value) => a(value) || b(value) || !!c && c(value) || !!d && d(value) || !!e && e(value);
}
function isUndefined(payload) {
	return getType(payload) === "Undefined";
}
isOneOf(isNull, isUndefined);
//#endregion
//#region ../../node_modules/.pnpm/copy-anything@3.0.5/node_modules/copy-anything/dist/index.js
function assignProp(carry, key, newVal, originalObject, includeNonenumerable) {
	const propType = {}.propertyIsEnumerable.call(originalObject, key) ? "enumerable" : "nonenumerable";
	if (propType === "enumerable") carry[key] = newVal;
	if (includeNonenumerable && propType === "nonenumerable") Object.defineProperty(carry, key, {
		value: newVal,
		enumerable: false,
		writable: true,
		configurable: true
	});
}
function copy(target, options = {}) {
	if (isArray$1(target)) return target.map((item) => copy(item, options));
	if (!isPlainObject$1(target)) return target;
	const props = Object.getOwnPropertyNames(target);
	const symbols = Object.getOwnPropertySymbols(target);
	return [...props, ...symbols].reduce((carry, key) => {
		if (isArray$1(options.props) && !options.props.includes(key)) return carry;
		const val = target[key];
		assignProp(carry, key, copy(val, options), target, options.nonenumerable);
		return carry;
	}, {});
}
//#endregion
//#region ../../node_modules/.pnpm/superjson@2.2.2/node_modules/superjson/dist/index.js
var SuperJSON = class {
	/**
	* @param dedupeReferentialEqualities  If true, SuperJSON will make sure only one instance of referentially equal objects are serialized and the rest are replaced with `null`.
	*/
	constructor({ dedupe = false } = {}) {
		this.classRegistry = new ClassRegistry();
		this.symbolRegistry = new Registry((s) => s.description ?? "");
		this.customTransformerRegistry = new CustomTransformerRegistry();
		this.allowedErrorProps = [];
		this.dedupe = dedupe;
	}
	serialize(object) {
		const identities = /* @__PURE__ */ new Map();
		const output = walker(object, identities, this, this.dedupe);
		const res = { json: output.transformedValue };
		if (output.annotations) res.meta = {
			...res.meta,
			values: output.annotations
		};
		const equalityAnnotations = generateReferentialEqualityAnnotations(identities, this.dedupe);
		if (equalityAnnotations) res.meta = {
			...res.meta,
			referentialEqualities: equalityAnnotations
		};
		return res;
	}
	deserialize(payload) {
		const { json, meta } = payload;
		let result = copy(json);
		if (meta?.values) result = applyValueAnnotations(result, meta.values, this);
		if (meta?.referentialEqualities) result = applyReferentialEqualityAnnotations(result, meta.referentialEqualities);
		return result;
	}
	stringify(object) {
		return JSON.stringify(this.serialize(object));
	}
	parse(string) {
		return this.deserialize(JSON.parse(string));
	}
	registerClass(v, options) {
		this.classRegistry.register(v, options);
	}
	registerSymbol(v, identifier) {
		this.symbolRegistry.register(v, identifier);
	}
	registerCustom(transformer, name) {
		this.customTransformerRegistry.register({
			name,
			...transformer
		});
	}
	allowErrorProps(...props) {
		this.allowedErrorProps.push(...props);
	}
};
SuperJSON.defaultInstance = new SuperJSON();
SuperJSON.serialize = SuperJSON.defaultInstance.serialize.bind(SuperJSON.defaultInstance);
SuperJSON.deserialize = SuperJSON.defaultInstance.deserialize.bind(SuperJSON.defaultInstance);
SuperJSON.stringify = SuperJSON.defaultInstance.stringify.bind(SuperJSON.defaultInstance);
SuperJSON.parse = SuperJSON.defaultInstance.parse.bind(SuperJSON.defaultInstance);
SuperJSON.registerClass = SuperJSON.defaultInstance.registerClass.bind(SuperJSON.defaultInstance);
SuperJSON.registerSymbol = SuperJSON.defaultInstance.registerSymbol.bind(SuperJSON.defaultInstance);
SuperJSON.registerCustom = SuperJSON.defaultInstance.registerCustom.bind(SuperJSON.defaultInstance);
SuperJSON.allowErrorProps = SuperJSON.defaultInstance.allowErrorProps.bind(SuperJSON.defaultInstance);
SuperJSON.serialize;
SuperJSON.deserialize;
SuperJSON.stringify;
SuperJSON.parse;
SuperJSON.registerClass;
SuperJSON.registerCustom;
SuperJSON.registerSymbol;
SuperJSON.allowErrorProps;
//#endregion
//#region src/messaging/presets/broadcast-channel/context.ts
const __DEVTOOLS_KIT_BROADCAST_MESSAGING_EVENT_KEY = "__devtools-kit-broadcast-messaging-event-key__";
//#endregion
//#region src/messaging/presets/broadcast-channel/index.ts
const BROADCAST_CHANNEL_NAME = "__devtools-kit:broadcast-channel__";
function createBroadcastChannel() {
	const channel = new BroadcastChannel(BROADCAST_CHANNEL_NAME);
	return {
		post: (data) => {
			channel.postMessage(SuperJSON.stringify({
				event: __DEVTOOLS_KIT_BROADCAST_MESSAGING_EVENT_KEY,
				data
			}));
		},
		on: (handler) => {
			channel.onmessage = (event) => {
				const parsed = SuperJSON.parse(event.data);
				if (parsed.event === "__devtools-kit-broadcast-messaging-event-key__") handler(parsed.data);
			};
		}
	};
}
//#endregion
//#region src/messaging/presets/electron/context.ts
const __ELECTRON_CLIENT_CONTEXT__ = "electron:client-context";
const __ELECTRON_RPOXY_CONTEXT__ = "electron:proxy-context";
const __ELECTRON_SERVER_CONTEXT__ = "electron:server-context";
const __DEVTOOLS_KIT_ELECTRON_MESSAGING_EVENT_KEY__ = {
	CLIENT_TO_PROXY: "client->proxy",
	PROXY_TO_CLIENT: "proxy->client",
	PROXY_TO_SERVER: "proxy->server",
	SERVER_TO_PROXY: "server->proxy"
};
function getElectronClientContext() {
	return _vue_devtools_shared.target[__ELECTRON_CLIENT_CONTEXT__];
}
function setElectronClientContext(context) {
	_vue_devtools_shared.target[__ELECTRON_CLIENT_CONTEXT__] = context;
}
function getElectronProxyContext() {
	return _vue_devtools_shared.target[__ELECTRON_RPOXY_CONTEXT__];
}
function setElectronProxyContext(context) {
	_vue_devtools_shared.target[__ELECTRON_RPOXY_CONTEXT__] = context;
}
function getElectronServerContext() {
	return _vue_devtools_shared.target[__ELECTRON_SERVER_CONTEXT__];
}
function setElectronServerContext(context) {
	_vue_devtools_shared.target[__ELECTRON_SERVER_CONTEXT__] = context;
}
//#endregion
//#region src/messaging/presets/electron/client.ts
function createElectronClientChannel() {
	const socket = getElectronClientContext();
	return {
		post: (data) => {
			socket.emit(__DEVTOOLS_KIT_ELECTRON_MESSAGING_EVENT_KEY__.CLIENT_TO_PROXY, SuperJSON.stringify(data));
		},
		on: (handler) => {
			socket.on(__DEVTOOLS_KIT_ELECTRON_MESSAGING_EVENT_KEY__.PROXY_TO_CLIENT, (e) => {
				handler(SuperJSON.parse(e));
			});
		}
	};
}
//#endregion
//#region src/messaging/presets/electron/proxy.ts
function createElectronProxyChannel() {
	const socket = getElectronProxyContext();
	return {
		post: (data) => {},
		on: (handler) => {
			socket.on(__DEVTOOLS_KIT_ELECTRON_MESSAGING_EVENT_KEY__.SERVER_TO_PROXY, (data) => {
				socket.broadcast.emit(__DEVTOOLS_KIT_ELECTRON_MESSAGING_EVENT_KEY__.PROXY_TO_CLIENT, data);
			});
			socket.on(__DEVTOOLS_KIT_ELECTRON_MESSAGING_EVENT_KEY__.CLIENT_TO_PROXY, (data) => {
				socket.broadcast.emit(__DEVTOOLS_KIT_ELECTRON_MESSAGING_EVENT_KEY__.PROXY_TO_SERVER, data);
			});
		}
	};
}
//#endregion
//#region src/messaging/presets/electron/server.ts
function createElectronServerChannel() {
	const socket = getElectronServerContext();
	return {
		post: (data) => {
			socket.emit(__DEVTOOLS_KIT_ELECTRON_MESSAGING_EVENT_KEY__.SERVER_TO_PROXY, SuperJSON.stringify(data));
		},
		on: (handler) => {
			socket.on(__DEVTOOLS_KIT_ELECTRON_MESSAGING_EVENT_KEY__.PROXY_TO_SERVER, (data) => {
				handler(SuperJSON.parse(data));
			});
		}
	};
}
//#endregion
//#region src/messaging/presets/extension/context.ts
const __EXTENSION_CLIENT_CONTEXT__ = "electron:client-context";
const __DEVTOOLS_KIT_EXTENSION_MESSAGING_EVENT_KEY__ = {
	CLIENT_TO_PROXY: "client->proxy",
	PROXY_TO_CLIENT: "proxy->client",
	PROXY_TO_SERVER: "proxy->server",
	SERVER_TO_PROXY: "server->proxy"
};
function getExtensionClientContext() {
	return _vue_devtools_shared.target[__EXTENSION_CLIENT_CONTEXT__];
}
function setExtensionClientContext(context) {
	_vue_devtools_shared.target[__EXTENSION_CLIENT_CONTEXT__] = context;
}
//#endregion
//#region src/messaging/presets/extension/client.ts
function createExtensionClientChannel() {
	let disconnected = false;
	let port = null;
	let reconnectTimer = null;
	let onMessageHandler = null;
	function connect() {
		try {
			clearTimeout(reconnectTimer);
			port = chrome.runtime.connect({ name: `${chrome.devtools.inspectedWindow.tabId}` });
			setExtensionClientContext(port);
			disconnected = false;
			port?.onMessage.addListener(onMessageHandler);
			port.onDisconnect.addListener(() => {
				disconnected = true;
				port?.onMessage.removeListener(onMessageHandler);
				reconnectTimer = setTimeout(connect, 1e3);
			});
		} catch (e) {
			disconnected = true;
		}
	}
	connect();
	return {
		post: (data) => {
			if (disconnected) return;
			port?.postMessage(SuperJSON.stringify(data));
		},
		on: (handler) => {
			onMessageHandler = (data) => {
				if (disconnected) return;
				handler(SuperJSON.parse(data));
			};
			port?.onMessage.addListener(onMessageHandler);
		}
	};
}
//#endregion
//#region src/messaging/presets/extension/proxy.ts
function createExtensionProxyChannel() {
	const port = chrome.runtime.connect({ name: "content-script" });
	function sendMessageToUserApp(payload) {
		window.postMessage({
			source: __DEVTOOLS_KIT_EXTENSION_MESSAGING_EVENT_KEY__.PROXY_TO_SERVER,
			payload
		}, "*");
	}
	function sendMessageToDevToolsClient(e) {
		if (e.data && e.data.source === __DEVTOOLS_KIT_EXTENSION_MESSAGING_EVENT_KEY__.SERVER_TO_PROXY) try {
			port.postMessage(e.data.payload);
		} catch (e) {}
	}
	port.onMessage.addListener(sendMessageToUserApp);
	window.addEventListener("message", sendMessageToDevToolsClient);
	port.onDisconnect.addListener(() => {
		window.removeEventListener("message", sendMessageToDevToolsClient);
		sendMessageToUserApp(SuperJSON.stringify({ event: "shutdown" }));
	});
	sendMessageToUserApp(SuperJSON.stringify({ event: "init" }));
	return {
		post: (data) => {},
		on: (handler) => {}
	};
}
//#endregion
//#region src/messaging/presets/extension/server.ts
function createExtensionServerChannel() {
	return {
		post: (data) => {
			window.postMessage({
				source: __DEVTOOLS_KIT_EXTENSION_MESSAGING_EVENT_KEY__.SERVER_TO_PROXY,
				payload: SuperJSON.stringify(data)
			}, "*");
		},
		on: (handler) => {
			const listener = (event) => {
				if (event.data.source === __DEVTOOLS_KIT_EXTENSION_MESSAGING_EVENT_KEY__.PROXY_TO_SERVER && event.data.payload) handler(SuperJSON.parse(event.data.payload));
			};
			window.addEventListener("message", listener);
			return () => {
				window.removeEventListener("message", listener);
			};
		}
	};
}
//#endregion
//#region src/messaging/presets/iframe/context.ts
const __DEVTOOLS_KIT_IFRAME_MESSAGING_EVENT_KEY = "__devtools-kit-iframe-messaging-event-key__";
const __IFRAME_SERVER_CONTEXT__ = "iframe:server-context";
function getIframeServerContext() {
	return _vue_devtools_shared.target[__IFRAME_SERVER_CONTEXT__];
}
function setIframeServerContext(context) {
	_vue_devtools_shared.target[__IFRAME_SERVER_CONTEXT__] = context;
}
//#endregion
//#region src/messaging/presets/iframe/client.ts
function createIframeClientChannel() {
	if (!_vue_devtools_shared.isBrowser) return {
		post: (data) => {},
		on: (handler) => {}
	};
	return {
		post: (data) => window.parent.postMessage(SuperJSON.stringify({
			event: __DEVTOOLS_KIT_IFRAME_MESSAGING_EVENT_KEY,
			data
		}), "*"),
		on: (handler) => window.addEventListener("message", (event) => {
			try {
				const parsed = SuperJSON.parse(event.data);
				if (event.source === window.parent && parsed.event === "__devtools-kit-iframe-messaging-event-key__") handler(parsed.data);
			} catch (e) {}
		})
	};
}
//#endregion
//#region src/messaging/presets/iframe/server.ts
function createIframeServerChannel() {
	if (!_vue_devtools_shared.isBrowser) return {
		post: (data) => {},
		on: (handler) => {}
	};
	return {
		post: (data) => {
			getIframeServerContext()?.contentWindow?.postMessage(SuperJSON.stringify({
				event: __DEVTOOLS_KIT_IFRAME_MESSAGING_EVENT_KEY,
				data
			}), "*");
		},
		on: (handler) => {
			window.addEventListener("message", (event) => {
				const iframe = getIframeServerContext();
				try {
					const parsed = SuperJSON.parse(event.data);
					if (event.source === iframe?.contentWindow && parsed.event === "__devtools-kit-iframe-messaging-event-key__") handler(parsed.data);
				} catch (e) {}
			});
		}
	};
}
//#endregion
//#region src/messaging/presets/vite/context.ts
const __DEVTOOLS_KIT_VITE_MESSAGING_EVENT_KEY = "__devtools-kit-vite-messaging-event-key__";
const __VITE_CLIENT_CONTEXT__ = "vite:client-context";
const __VITE_SERVER_CONTEXT__ = "vite:server-context";
function getViteClientContext() {
	return _vue_devtools_shared.target[__VITE_CLIENT_CONTEXT__];
}
function setViteClientContext(context) {
	_vue_devtools_shared.target[__VITE_CLIENT_CONTEXT__] = context;
}
function getViteServerContext() {
	return _vue_devtools_shared.target[__VITE_SERVER_CONTEXT__];
}
function setViteServerContext(context) {
	_vue_devtools_shared.target[__VITE_SERVER_CONTEXT__] = context;
}
//#endregion
//#region src/messaging/presets/vite/client.ts
function createViteClientChannel() {
	const client = getViteClientContext();
	return {
		post: (data) => {
			client?.send(__DEVTOOLS_KIT_VITE_MESSAGING_EVENT_KEY, SuperJSON.stringify(data));
		},
		on: (handler) => {
			client?.on(__DEVTOOLS_KIT_VITE_MESSAGING_EVENT_KEY, (event) => {
				handler(SuperJSON.parse(event));
			});
		}
	};
}
//#endregion
//#region src/messaging/presets/vite/server.ts
function createViteServerChannel() {
	const viteServer = getViteServerContext();
	const ws = viteServer.hot ?? viteServer.ws;
	return {
		post: (data) => ws?.send(__DEVTOOLS_KIT_VITE_MESSAGING_EVENT_KEY, SuperJSON.stringify(data)),
		on: (handler) => ws?.on(__DEVTOOLS_KIT_VITE_MESSAGING_EVENT_KEY, (event) => {
			handler(SuperJSON.parse(event));
		})
	};
}
//#endregion
//#region src/messaging/index.ts
_vue_devtools_shared.target.__VUE_DEVTOOLS_KIT_MESSAGE_CHANNELS__ ??= [];
_vue_devtools_shared.target.__VUE_DEVTOOLS_KIT_RPC_CLIENT__ ??= null;
_vue_devtools_shared.target.__VUE_DEVTOOLS_KIT_RPC_SERVER__ ??= null;
_vue_devtools_shared.target.__VUE_DEVTOOLS_KIT_VITE_RPC_CLIENT__ ??= null;
_vue_devtools_shared.target.__VUE_DEVTOOLS_KIT_VITE_RPC_SERVER__ ??= null;
_vue_devtools_shared.target.__VUE_DEVTOOLS_KIT_BROADCAST_RPC_SERVER__ ??= null;
function setRpcClientToGlobal(rpc) {
	_vue_devtools_shared.target.__VUE_DEVTOOLS_KIT_RPC_CLIENT__ = rpc;
}
function setRpcServerToGlobal(rpc) {
	_vue_devtools_shared.target.__VUE_DEVTOOLS_KIT_RPC_SERVER__ = rpc;
}
function getRpcClient() {
	return _vue_devtools_shared.target.__VUE_DEVTOOLS_KIT_RPC_CLIENT__;
}
function getRpcServer() {
	return _vue_devtools_shared.target.__VUE_DEVTOOLS_KIT_RPC_SERVER__;
}
function setViteRpcClientToGlobal(rpc) {
	_vue_devtools_shared.target.__VUE_DEVTOOLS_KIT_VITE_RPC_CLIENT__ = rpc;
}
function setViteRpcServerToGlobal(rpc) {
	_vue_devtools_shared.target.__VUE_DEVTOOLS_KIT_VITE_RPC_SERVER__ = rpc;
}
function getViteRpcClient() {
	return _vue_devtools_shared.target.__VUE_DEVTOOLS_KIT_VITE_RPC_CLIENT__;
}
function getViteRpcServer() {
	return _vue_devtools_shared.target.__VUE_DEVTOOLS_KIT_VITE_RPC_SERVER__;
}
function getChannel(preset, host = "client") {
	const channel = {
		iframe: {
			client: createIframeClientChannel,
			server: createIframeServerChannel
		}[host],
		electron: {
			client: createElectronClientChannel,
			proxy: createElectronProxyChannel,
			server: createElectronServerChannel
		}[host],
		vite: {
			client: createViteClientChannel,
			server: createViteServerChannel
		}[host],
		broadcast: {
			client: createBroadcastChannel,
			server: createBroadcastChannel
		}[host],
		extension: {
			client: createExtensionClientChannel,
			proxy: createExtensionProxyChannel,
			server: createExtensionServerChannel
		}[host]
	}[preset];
	return channel();
}
function createRpcClient(functions, options = {}) {
	const { channel: _channel, options: _options, preset } = options;
	const channel = preset ? getChannel(preset) : _channel;
	const rpc = (0, birpc.createBirpc)(functions, {
		..._options,
		...channel,
		timeout: -1
	});
	if (preset === "vite") {
		setViteRpcClientToGlobal(rpc);
		return;
	}
	setRpcClientToGlobal(rpc);
	return rpc;
}
function createRpcServer(functions, options = {}) {
	const { channel: _channel, options: _options, preset } = options;
	const channel = preset ? getChannel(preset, "server") : _channel;
	const rpcServer = getRpcServer();
	if (!rpcServer) {
		const group = (0, birpc.createBirpcGroup)(functions, [channel], {
			..._options,
			timeout: -1
		});
		if (preset === "vite") {
			setViteRpcServerToGlobal(group);
			return;
		}
		setRpcServerToGlobal(group);
	} else rpcServer.updateChannels((channels) => {
		channels.push(channel);
	});
}
function createRpcProxy(options = {}) {
	const { channel: _channel, options: _options, preset } = options;
	const channel = preset ? getChannel(preset, "proxy") : _channel;
	return (0, birpc.createBirpc)({}, {
		..._options,
		...channel,
		timeout: -1
	});
}
//#endregion
//#region src/core/component/state/custom.ts
function getFunctionDetails(func) {
	let string = "";
	let matches = null;
	try {
		string = Function.prototype.toString.call(func);
		matches = String.prototype.match.call(string, /\([\s\S]*?\)/);
	} catch (e) {}
	const match = matches && matches[0];
	const args = typeof match === "string" ? match : "(?)";
	return { _custom: {
		type: "function",
		displayText: `<span style="opacity:.8;margin-right:5px;">function</span> <span style="white-space:nowrap;">${escape(typeof func.name === "string" ? func.name : "")}${args}</span>`,
		tooltipText: string.trim() ? `<pre>${escape(string)}</pre>` : null
	} };
}
function getBigIntDetails(val) {
	const stringifiedBigInt = BigInt.prototype.toString.call(val);
	return { _custom: {
		type: "bigint",
		displayText: `BigInt(${stringifiedBigInt})`,
		value: stringifiedBigInt
	} };
}
function getDateDetails(val) {
	const date = new Date(val.getTime());
	date.setMinutes(date.getMinutes() - date.getTimezoneOffset());
	return { _custom: {
		type: "date",
		displayText: Date.prototype.toString.call(val),
		value: date.toISOString().slice(0, -1)
	} };
}
function getMapDetails(val) {
	return { _custom: {
		type: "map",
		displayText: "Map",
		value: Object.fromEntries(val),
		readOnly: true,
		fields: { abstract: true }
	} };
}
function getSetDetails(val) {
	const list = Array.from(val);
	return { _custom: {
		type: "set",
		displayText: `Set[${list.length}]`,
		value: list,
		readOnly: true
	} };
}
function getCaughtGetters(store) {
	const getters = {};
	const origGetters = store.getters || {};
	const keys = Object.keys(origGetters);
	for (let i = 0; i < keys.length; i++) {
		const key = keys[i];
		Object.defineProperty(getters, key, {
			enumerable: true,
			get: () => {
				try {
					return origGetters[key];
				} catch (e) {
					return e;
				}
			}
		});
	}
	return getters;
}
function reduceStateList(list) {
	if (!list.length) return void 0;
	return list.reduce((map, item) => {
		const key = item.type || "data";
		const obj = map[key] = map[key] || {};
		obj[item.key] = item.value;
		return map;
	}, {});
}
function namedNodeMapToObject(map) {
	const result = {};
	const l = map.length;
	for (let i = 0; i < l; i++) {
		const node = map.item(i);
		result[node.name] = node.value;
	}
	return result;
}
function getStoreDetails(store) {
	return { _custom: {
		type: "store",
		displayText: "Store",
		value: {
			state: store.state,
			getters: getCaughtGetters(store)
		},
		fields: { abstract: true }
	} };
}
function getInstanceDetails(instance) {
	if (instance._) instance = instance._;
	const state = processInstanceState(instance);
	return { _custom: {
		type: "component",
		id: instance.__VUE_DEVTOOLS_NEXT_UID__,
		displayText: getInstanceName(instance),
		tooltipText: "Component instance",
		value: reduceStateList(state),
		fields: { abstract: true }
	} };
}
function getComponentDefinitionDetails(definition) {
	let display = getComponentName(definition);
	if (display) {
		if (definition.name && definition.__file) display += ` <span>(${definition.__file})</span>`;
	} else display = "<i>Unknown Component</i>";
	return { _custom: {
		type: "component-definition",
		displayText: display,
		tooltipText: "Component definition",
		...definition.__file ? { file: definition.__file } : {}
	} };
}
function getHTMLElementDetails(value) {
	try {
		return { _custom: {
			type: "HTMLElement",
			displayText: `<span class="opacity-30">&lt;</span><span class="text-blue-500">${value.tagName.toLowerCase()}</span><span class="opacity-30">&gt;</span>`,
			value: namedNodeMapToObject(value.attributes)
		} };
	} catch (e) {
		return { _custom: {
			type: "HTMLElement",
			displayText: `<span class="text-blue-500">${String(value)}</span>`
		} };
	}
}
/**
* - ObjectRefImpl, toRef({ foo: 'foo' }, 'foo'), `_value` is the actual value, (since 3.5.0)
* - GetterRefImpl, toRef(() => state.foo), `_value` is the actual value, (since 3.5.0)
* - RefImpl, ref('foo') / computed(() => 'foo'), `_value` is the actual value
*/
function tryGetRefValue(ref) {
	if (ensurePropertyExists(ref, "_value", true)) return ref._value;
	if (ensurePropertyExists(ref, "value", true)) return ref.value;
}
function getObjectDetails(object) {
	const info = getSetupStateType(object);
	if (info.ref || info.computed || info.reactive) {
		const stateTypeName = info.computed ? "Computed" : info.ref ? "Ref" : info.reactive ? "Reactive" : null;
		const value = toRaw(info.reactive ? object : tryGetRefValue(object));
		const raw = ensurePropertyExists(object, "effect") ? object.effect?.raw?.toString() || object.effect?.fn?.toString() : null;
		return { _custom: {
			type: stateTypeName?.toLowerCase(),
			stateTypeName,
			value,
			...raw ? { tooltipText: `<pre>${escape(raw)}</pre>` } : {}
		} };
	}
	if (ensurePropertyExists(object, "__asyncLoader") && typeof object.__asyncLoader === "function") return { _custom: {
		type: "component-definition",
		display: "Async component definition"
	} };
}
//#endregion
//#region src/core/component/state/replacer.ts
function stringifyReplacer(key, _value, depth, seenInstance) {
	if (key === "compilerOptions") return;
	const val = this[key];
	const type = typeof val;
	if (Array.isArray(val)) {
		const l = val.length;
		if (l > 5e3) return {
			_isArray: true,
			length: l,
			items: val.slice(0, MAX_ARRAY_SIZE)
		};
		return val;
	} else if (typeof val === "string") if (val.length > 1e4) return `${val.substring(0, MAX_STRING_SIZE)}... (${val.length} total length)`;
	else return val;
	else if (type === "undefined") return UNDEFINED;
	else if (val === Number.POSITIVE_INFINITY) return INFINITY;
	else if (val === Number.NEGATIVE_INFINITY) return NEGATIVE_INFINITY;
	else if (typeof val === "function") return getFunctionDetails(val);
	else if (type === "symbol") return `[native Symbol ${Symbol.prototype.toString.call(val)}]`;
	else if (typeof val === "bigint") return getBigIntDetails(val);
	else if (val !== null && typeof val === "object") {
		const proto = Object.prototype.toString.call(val);
		if (proto === "[object Map]") return getMapDetails(val);
		else if (proto === "[object Set]") return getSetDetails(val);
		else if (proto === "[object RegExp]") return `[native RegExp ${RegExp.prototype.toString.call(val)}]`;
		else if (proto === "[object Date]") return getDateDetails(val);
		else if (proto === "[object Error]") return `[native Error ${val.message}<>${val.stack}]`;
		else if (ensurePropertyExists(val, "state", true) && ensurePropertyExists(val, "_vm", true)) return getStoreDetails(val);
		else if (isVueInstance(val)) {
			const componentVal = getInstanceDetails(val);
			const parentInstanceDepth = seenInstance?.get(val);
			if (parentInstanceDepth && parentInstanceDepth < depth) return `[[CircularRef]] <${componentVal._custom.displayText}>`;
			seenInstance?.set(val, depth);
			return componentVal;
		} else if (ensurePropertyExists(val, "render", true) && typeof val.render === "function") return getComponentDefinitionDetails(val);
		else if (val.constructor && val.constructor.name === "VNode") return `[native VNode <${val.tag}>]`;
		else if (typeof HTMLElement !== "undefined" && val instanceof HTMLElement) return getHTMLElementDetails(val);
		else if (val.constructor?.name === "Store" && "_wrappedGetters" in val) return "[object Store]";
		const customDetails = getObjectDetails(val);
		if (customDetails != null) return customDetails;
	} else if (Number.isNaN(val)) return NAN;
	return sanitize(val);
}
//#endregion
//#region src/shared/transfer.ts
const MAX_SERIALIZED_SIZE = 2 * 1024 * 1024;
function isObject(_data, proto) {
	return proto === "[object Object]";
}
function isArray(_data, proto) {
	return proto === "[object Array]";
}
function isVueReactiveLinkNode(node) {
	const constructorName = node?.constructor?.name;
	return constructorName === "Dep" && "activeLink" in node || constructorName === "Link" && "dep" in node;
}
/**
* This function is used to serialize object with handling circular references.
*
* ```ts
* const obj = { a: 1, b: { c: 2 }, d: obj }
* const result = stringifyCircularAutoChunks(obj) // call `encode` inside
* console.log(result) // [{"a":1,"b":2,"d":0},1,{"c":4},2]
* ```
*
* Each object is stored in a list and the index is used to reference the object.
* With seen map, we can check if the object is already stored in the list to avoid circular references.
*
* Note: here we have a special case for Vue instance.
* We check if a vue instance includes itself in its properties and skip it
* by using `seenVueInstance` and `depth` to avoid infinite loop.
*/
function encode(data, replacer, list, seen, depth = 0, seenVueInstance = /* @__PURE__ */ new Map()) {
	let stored;
	let key;
	let value;
	let i;
	let l;
	const seenIndex = seen.get(data);
	if (seenIndex != null) return seenIndex;
	const index = list.length;
	const proto = Object.prototype.toString.call(data);
	if (isObject(data, proto)) {
		if (isVueReactiveLinkNode(data)) return index;
		stored = {};
		seen.set(data, index);
		list.push(stored);
		const keys = Object.keys(data);
		for (i = 0, l = keys.length; i < l; i++) {
			key = keys[i];
			if (key === "compilerOptions") return index;
			value = data[key];
			const isVm = value != null && isObject(value, Object.prototype.toString.call(data)) && isVueInstance(value);
			try {
				if (replacer) value = replacer.call(data, key, value, depth, seenVueInstance);
			} catch (e) {
				value = e;
			}
			stored[key] = encode(value, replacer, list, seen, depth + 1, seenVueInstance);
			if (isVm) seenVueInstance.delete(value);
		}
	} else if (isArray(data, proto)) {
		stored = [];
		seen.set(data, index);
		list.push(stored);
		for (i = 0, l = data.length; i < l; i++) {
			try {
				value = data[i];
				if (replacer) value = replacer.call(data, i, value, depth, seenVueInstance);
			} catch (e) {
				value = e;
			}
			stored[i] = encode(value, replacer, list, seen, depth + 1, seenVueInstance);
		}
	} else list.push(data);
	return index;
}
function decode(list, reviver = null) {
	let i = list.length;
	let j, k, data, key, value, proto;
	while (i--) {
		data = list[i];
		proto = Object.prototype.toString.call(data);
		if (proto === "[object Object]") {
			const keys = Object.keys(data);
			for (j = 0, k = keys.length; j < k; j++) {
				key = keys[j];
				value = list[data[key]];
				if (reviver) value = reviver.call(data, key, value);
				data[key] = value;
			}
		} else if (proto === "[object Array]") for (j = 0, k = data.length; j < k; j++) {
			value = list[data[j]];
			if (reviver) value = reviver.call(data, j, value);
			data[j] = value;
		}
	}
}
function stringifyCircularAutoChunks(data, replacer = null, space = null) {
	let result;
	try {
		result = arguments.length === 1 ? JSON.stringify(data) : JSON.stringify(data, (k, v) => replacer?.(k, v)?.call(this), space);
	} catch (e) {
		result = stringifyStrictCircularAutoChunks(data, replacer, space);
	}
	if (result.length > MAX_SERIALIZED_SIZE) {
		const chunkCount = Math.ceil(result.length / MAX_SERIALIZED_SIZE);
		const chunks = [];
		for (let i = 0; i < chunkCount; i++) chunks.push(result.slice(i * MAX_SERIALIZED_SIZE, (i + 1) * MAX_SERIALIZED_SIZE));
		return chunks;
	}
	return result;
}
function stringifyStrictCircularAutoChunks(data, replacer = null, space = null) {
	const list = [];
	encode(data, replacer, list, /* @__PURE__ */ new Map());
	return space ? ` ${JSON.stringify(list, null, space)}` : ` ${JSON.stringify(list)}`;
}
function parseCircularAutoChunks(data, reviver = null) {
	if (Array.isArray(data)) data = data.join("");
	if (!/^\s/.test(data)) return arguments.length === 1 ? JSON.parse(data) : JSON.parse(data, reviver);
	else {
		const list = JSON.parse(data);
		decode(list, reviver);
		return list[0];
	}
}
//#endregion
//#region src/shared/util.ts
function stringify(data) {
	return stringifyCircularAutoChunks(data, stringifyReplacer);
}
function parse(data, revive = false) {
	if (data == void 0) return {};
	return revive ? parseCircularAutoChunks(data, reviver) : parseCircularAutoChunks(data);
}
//#endregion
//#region src/index.ts
const devtools = {
	hook,
	init: () => {
		initDevTools();
	},
	get ctx() {
		return devtoolsContext;
	},
	get api() {
		return devtoolsContext.api;
	}
};
//#endregion
exports.DevToolsContextHookKeys = DevToolsContextHookKeys;
exports.DevToolsMessagingHookKeys = DevToolsMessagingHookKeys;
exports.DevToolsV6PluginAPIHookKeys = DevToolsV6PluginAPIHookKeys;
exports.INFINITY = INFINITY;
exports.NAN = NAN;
exports.NEGATIVE_INFINITY = NEGATIVE_INFINITY;
exports.ROUTER_INFO_KEY = ROUTER_INFO_KEY;
exports.ROUTER_KEY = ROUTER_KEY;
exports.UNDEFINED = UNDEFINED;
exports.activeAppRecord = activeAppRecord;
exports.addCustomCommand = addCustomCommand;
exports.addCustomTab = addCustomTab;
exports.addDevToolsAppRecord = addDevToolsAppRecord;
exports.addDevToolsPluginToBuffer = addDevToolsPluginToBuffer;
exports.addInspector = addInspector;
exports.callConnectedUpdatedHook = callConnectedUpdatedHook;
exports.callDevToolsPluginSetupFn = callDevToolsPluginSetupFn;
exports.callInspectorUpdatedHook = callInspectorUpdatedHook;
exports.callStateUpdatedHook = callStateUpdatedHook;
exports.createComponentsDevToolsPlugin = createComponentsDevToolsPlugin;
exports.createDevToolsApi = createDevToolsApi;
exports.createDevToolsCtxHooks = createDevToolsCtxHooks;
exports.createRpcClient = createRpcClient;
exports.createRpcProxy = createRpcProxy;
exports.createRpcServer = createRpcServer;
exports.devtools = devtools;
exports.devtoolsAppRecords = devtoolsAppRecords;
exports.devtoolsContext = devtoolsContext;
exports.devtoolsInspector = devtoolsInspector;
exports.devtoolsPluginBuffer = devtoolsPluginBuffer;
exports.devtoolsRouter = devtoolsRouter;
exports.devtoolsRouterInfo = devtoolsRouterInfo;
exports.devtoolsState = devtoolsState;
exports.escape = escape;
exports.formatInspectorStateValue = formatInspectorStateValue;
exports.getActiveInspectors = getActiveInspectors;
exports.getDevToolsEnv = getDevToolsEnv;
exports.getExtensionClientContext = getExtensionClientContext;
exports.getInspector = getInspector;
exports.getInspectorActions = getInspectorActions;
exports.getInspectorInfo = getInspectorInfo;
exports.getInspectorNodeActions = getInspectorNodeActions;
exports.getInspectorStateValueType = getInspectorStateValueType;
exports.getRaw = getRaw;
exports.getRpcClient = getRpcClient;
exports.getRpcServer = getRpcServer;
exports.getViteRpcClient = getViteRpcClient;
exports.getViteRpcServer = getViteRpcServer;
exports.initDevTools = initDevTools;
exports.isPlainObject = isPlainObject;
exports.onDevToolsClientConnected = onDevToolsClientConnected;
exports.onDevToolsConnected = onDevToolsConnected;
exports.parse = parse;
exports.registerDevToolsPlugin = registerDevToolsPlugin;
exports.removeCustomCommand = removeCustomCommand;
exports.removeDevToolsAppRecord = removeDevToolsAppRecord;
exports.removeRegisteredPluginApp = removeRegisteredPluginApp;
exports.resetDevToolsState = resetDevToolsState;
exports.setActiveAppRecord = setActiveAppRecord;
exports.setActiveAppRecordId = setActiveAppRecordId;
exports.setDevToolsEnv = setDevToolsEnv;
exports.setElectronClientContext = setElectronClientContext;
exports.setElectronProxyContext = setElectronProxyContext;
exports.setElectronServerContext = setElectronServerContext;
exports.setExtensionClientContext = setExtensionClientContext;
exports.setIframeServerContext = setIframeServerContext;
exports.setOpenInEditorBaseUrl = setOpenInEditorBaseUrl;
exports.setRpcServerToGlobal = setRpcServerToGlobal;
exports.setViteClientContext = setViteClientContext;
exports.setViteRpcClientToGlobal = setViteRpcClientToGlobal;
exports.setViteRpcServerToGlobal = setViteRpcServerToGlobal;
exports.setViteServerContext = setViteServerContext;
exports.setupDevToolsPlugin = setupDevToolsPlugin;
exports.stringify = stringify;
exports.toEdit = toEdit;
exports.toSubmit = toSubmit;
exports.toggleClientConnected = toggleClientConnected;
exports.toggleComponentInspectorEnabled = toggleComponentInspectorEnabled;
exports.toggleHighPerfMode = toggleHighPerfMode;
exports.updateDevToolsClientDetected = updateDevToolsClientDetected;
exports.updateDevToolsState = updateDevToolsState;
exports.updateTimelineLayersState = updateTimelineLayersState;
