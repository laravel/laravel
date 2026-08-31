/*!
 * pinia v4.0.2
 * (c) 2026 Eduardo San Martin Morote
 * @license MIT
 */
import { computed, effectScope, getCurrentInstance, getCurrentScope, hasInjectionContext, inject, isReactive, isReadonly, isRef, markRaw, nextTick, onScopeDispose, reactive, ref, toRaw, toRef, toRefs, unref, watch } from "vue";
import { createConsoleReporter, defineDiagnostics } from "nostics";
import { setupDevtoolsPlugin } from "@vue/devtools-api";
//#region src/env.ts
const IS_CLIENT = typeof window !== "undefined";
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
const getActivePinia = process.env.NODE_ENV !== "production" ? () => {
	const pinia = hasInjectionContext() && inject(piniaSymbol);
	if (!pinia && !IS_CLIENT) diagnostics.PINIA_R1004({}, { method: "error" });
	return pinia || activePinia;
} : () => hasInjectionContext() && inject(piniaSymbol) || activePinia;
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
const piniaSymbol = process.env.NODE_ENV !== "production" ? Symbol("pinia") : /* istanbul ignore next */ Symbol();
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
//#region src/devtools/file-saver.ts
const _global = /*#__PURE__*/ (() => typeof window === "object" && window.window === window ? window : typeof self === "object" && self.self === self ? self : typeof global === "object" && global.global === global ? global : typeof globalThis === "object" ? globalThis : { HTMLElement: null })();
function bom(blob, { autoBom = false } = {}) {
	if (autoBom && /^\s*(?:text\/\S*|application\/xml|\S*\/\S*\+xml)\s*;.*charset\s*=\s*utf-8/i.test(blob.type)) return new Blob([String.fromCharCode(65279), blob], { type: blob.type });
	return blob;
}
function download(url, name, opts) {
	const xhr = new XMLHttpRequest();
	xhr.open("GET", url);
	xhr.responseType = "blob";
	xhr.onload = function() {
		saveAs(xhr.response, name, opts);
	};
	xhr.onerror = function() {
		console.error("could not download file");
	};
	xhr.send();
}
function corsEnabled(url) {
	const xhr = new XMLHttpRequest();
	xhr.open("HEAD", url, false);
	try {
		xhr.send();
	} catch (e) {}
	return xhr.status >= 200 && xhr.status <= 299;
}
function click(node) {
	try {
		node.dispatchEvent(new MouseEvent("click"));
	} catch (e) {
		const evt = new MouseEvent("click", {
			bubbles: true,
			cancelable: true,
			view: window,
			detail: 0,
			screenX: 80,
			screenY: 20,
			clientX: 80,
			clientY: 20,
			ctrlKey: false,
			altKey: false,
			shiftKey: false,
			metaKey: false,
			button: 0,
			relatedTarget: null
		});
		node.dispatchEvent(evt);
	}
}
const _navigator = typeof navigator === "object" ? navigator : { userAgent: "" };
const isMacOSWebView = /*#__PURE__*/ (() => /Macintosh/.test(_navigator.userAgent) && /AppleWebKit/.test(_navigator.userAgent) && !/Safari/.test(_navigator.userAgent))();
const saveAs = !IS_CLIENT ? () => {} : typeof HTMLAnchorElement !== "undefined" && "download" in HTMLAnchorElement.prototype && !isMacOSWebView ? downloadSaveAs : "msSaveOrOpenBlob" in _navigator ? msSaveAs : fileSaverSaveAs;
function downloadSaveAs(blob, name = "download", opts) {
	const a = document.createElement("a");
	a.download = name;
	a.rel = "noopener";
	if (typeof blob === "string") {
		a.href = blob;
		if (a.origin !== location.origin) if (corsEnabled(a.href)) download(blob, name, opts);
		else {
			a.target = "_blank";
			click(a);
		}
		else click(a);
	} else {
		a.href = URL.createObjectURL(blob);
		setTimeout(function() {
			URL.revokeObjectURL(a.href);
		}, 4e4);
		setTimeout(function() {
			click(a);
		}, 0);
	}
}
function msSaveAs(blob, name = "download", opts) {
	if (typeof blob === "string") if (corsEnabled(blob)) download(blob, name, opts);
	else {
		const a = document.createElement("a");
		a.href = blob;
		a.target = "_blank";
		setTimeout(function() {
			click(a);
		});
	}
	else navigator.msSaveOrOpenBlob(bom(blob, opts), name);
}
function fileSaverSaveAs(blob, name, opts, popup) {
	popup = popup || open("", "_blank");
	if (popup) popup.document.title = popup.document.body.innerText = "downloading...";
	if (typeof blob === "string") return download(blob, name, opts);
	const force = blob.type === "application/octet-stream";
	const isSafari = /constructor/i.test(String(_global.HTMLElement)) || "safari" in _global;
	const isChromeIOS = /CriOS\/[\d]+/.test(navigator.userAgent);
	if ((isChromeIOS || force && isSafari || isMacOSWebView) && typeof FileReader !== "undefined") {
		const reader = new FileReader();
		reader.onloadend = function() {
			let url = reader.result;
			if (typeof url !== "string") {
				popup = null;
				throw new Error("Wrong reader.result type");
			}
			url = isChromeIOS ? url : url.replace(/^data:[^;]*;/, "data:attachment/file;");
			if (popup) popup.location.href = url;
			else location.assign(url);
			popup = null;
		};
		reader.readAsDataURL(blob);
	} else {
		const url = URL.createObjectURL(blob);
		if (popup) popup.location.assign(url);
		else location.href = url;
		popup = null;
		setTimeout(function() {
			URL.revokeObjectURL(url);
		}, 4e4);
	}
}
//#endregion
//#region src/devtools/utils.ts
/**
* Shows a toast or console.log
*
* @param message - message to log
* @param type - different color of the tooltip
*/
function toastMessage(message, type) {
	const piniaMessage = "🍍 " + message;
	if (type === "error") console.error(piniaMessage);
	else if (type === "warn") console.warn(piniaMessage);
	else console.debug(piniaMessage);
}
function isPinia(o) {
	return "_a" in o && "install" in o;
}
/**
* Detects if a store getter is a writable computed (has a setter) so it can be
* edited from devtools.
*/
function isWritableComputed(store, key) {
	const rawProp = toRaw(store)[key];
	return isRef(rawProp) && !isReadonly(rawProp);
}
//#endregion
//#region src/devtools/actions.ts
/**
* This file contain devtools actions, they are not Pinia actions.
*/
function checkClipboardAccess() {
	if (!("clipboard" in navigator)) {
		toastMessage(`Your browser doesn't support the Clipboard API`, "error");
		return true;
	}
}
function checkNotFocusedError(error) {
	if (error instanceof Error && error.message.toLowerCase().includes("document is not focused")) {
		toastMessage("You need to activate the \"Emulate a focused page\" setting in the \"Rendering\" panel of devtools.", "warn");
		return true;
	}
	return false;
}
async function actionGlobalCopyState(pinia) {
	if (checkClipboardAccess()) return;
	try {
		await navigator.clipboard.writeText(JSON.stringify(pinia.state.value));
		toastMessage("Global state copied to clipboard.");
	} catch (error) {
		if (checkNotFocusedError(error)) return;
		toastMessage(`Failed to serialize the state. Check the console for more details.`, "error");
		console.error(error);
	}
}
async function actionGlobalPasteState(pinia) {
	if (checkClipboardAccess()) return;
	try {
		loadStoresState(pinia, JSON.parse(await navigator.clipboard.readText()));
		toastMessage("Global state pasted from clipboard.");
	} catch (error) {
		if (checkNotFocusedError(error)) return;
		toastMessage(`Failed to deserialize the state from clipboard. Check the console for more details.`, "error");
		console.error(error);
	}
}
async function actionGlobalSaveState(pinia) {
	try {
		saveAs(new Blob([JSON.stringify(pinia.state.value)], { type: "text/plain;charset=utf-8" }), "pinia-state.json");
	} catch (error) {
		toastMessage(`Failed to export the state as JSON. Check the console for more details.`, "error");
		console.error(error);
	}
}
let fileInput;
function getFileOpener() {
	if (!fileInput) {
		fileInput = document.createElement("input");
		fileInput.type = "file";
		fileInput.accept = ".json";
	}
	function openFile() {
		return new Promise((resolve, reject) => {
			fileInput.onchange = async () => {
				const files = fileInput.files;
				if (!files) return resolve(null);
				const file = files.item(0);
				if (!file) return resolve(null);
				return resolve({
					text: await file.text(),
					file
				});
			};
			fileInput.oncancel = () => resolve(null);
			fileInput.onerror = reject;
			fileInput.click();
		});
	}
	return openFile;
}
async function actionGlobalOpenStateFile(pinia) {
	try {
		const result = await getFileOpener()();
		if (!result) return;
		const { text, file } = result;
		loadStoresState(pinia, JSON.parse(text));
		toastMessage(`Global state imported from "${file.name}".`);
	} catch (error) {
		toastMessage(`Failed to import the state from JSON. Check the console for more details.`, "error");
		console.error(error);
	}
}
function loadStoresState(pinia, state) {
	for (const key in state) {
		const storeState = pinia.state.value[key];
		if (storeState) Object.assign(storeState, state[key]);
		else pinia.state.value[key] = state[key];
	}
}
//#endregion
//#region src/devtools/formatting.ts
function formatDisplay(display) {
	return { _custom: { display } };
}
const PINIA_ROOT_LABEL = "🍍 Pinia (root)";
const PINIA_ROOT_ID = "_root";
function formatStoreForInspectorTree(store) {
	return isPinia(store) ? {
		id: PINIA_ROOT_ID,
		label: PINIA_ROOT_LABEL
	} : {
		id: store.$id,
		label: store.$id
	};
}
function formatStoreForInspectorState(store) {
	if (isPinia(store)) {
		const storeNames = Array.from(store._s.keys());
		const storeMap = store._s;
		return {
			state: storeNames.map((storeId) => ({
				editable: true,
				key: storeId,
				value: store.state.value[storeId]
			})),
			getters: storeNames.filter((id) => storeMap.get(id)._getters).map((id) => {
				const store = storeMap.get(id);
				return {
					editable: false,
					key: id,
					value: store._getters.reduce((getters, key) => {
						getters[key] = store[key];
						return getters;
					}, {})
				};
			})
		};
	}
	const state = { state: Object.keys(store.$state).map((key) => ({
		editable: true,
		key,
		value: store.$state[key]
	})) };
	if (store._getters && store._getters.length) state.getters = store._getters.map((getterName) => ({
		editable: isWritableComputed(store, getterName),
		key: getterName,
		value: store[getterName]
	}));
	if (store._customProperties.size) state.customProperties = Array.from(store._customProperties).map((key) => ({
		editable: true,
		key,
		value: store[key]
	}));
	return state;
}
function formatEventData(events) {
	if (!events) return {};
	if (Array.isArray(events)) return events.reduce((data, event) => {
		data.keys.push(event.key);
		data.operations.push(event.type);
		data.oldValue[event.key] = event.oldValue;
		data.newValue[event.key] = event.newValue;
		return data;
	}, {
		oldValue: {},
		keys: [],
		operations: [],
		newValue: {}
	});
	else return {
		operation: formatDisplay(events.type),
		key: formatDisplay(events.key),
		oldValue: events.oldValue,
		newValue: events.newValue
	};
}
function formatMutationType(type) {
	switch (type) {
		case "direct": return "mutation";
		case "patch function": return "$patch";
		case "patch object": return "$patch";
		default: return "unknown";
	}
}
//#endregion
//#region src/devtools/plugin.ts
let isTimelineActive = true;
const componentStateTypes = [];
const MUTATIONS_LAYER_ID = "pinia:mutations";
const INSPECTOR_ID = "pinia";
const { assign: assign$1 } = Object;
/**
* Gets the displayed name of a store in devtools
*
* @param id - id of the store
* @returns a formatted string
*/
const getStoreType = (id) => "🍍 " + id;
/**
* Add the pinia plugin without any store. Allows displaying a Pinia plugin tab
* as soon as it is added to the application.
*
* @param app - Vue application
* @param pinia - pinia instance
*/
function registerPiniaDevtools(app, pinia) {
	setupDevtoolsPlugin({
		id: "dev.esm.pinia",
		label: "Pinia 🍍",
		logo: "https://pinia.vuejs.org/logo.svg",
		packageName: "pinia",
		homepage: "https://pinia.vuejs.org",
		componentStateTypes,
		app
	}, (api) => {
		if (typeof api.now !== "function") toastMessage("You seem to be using an outdated version of Vue Devtools. Are you still using the Beta release instead of the stable one? You can find the links at https://devtools.vuejs.org/guide/installation.html.");
		api.addTimelineLayer({
			id: MUTATIONS_LAYER_ID,
			label: `Pinia 🍍`,
			color: 15064968
		});
		api.addInspector({
			id: INSPECTOR_ID,
			label: "Pinia 🍍",
			icon: "storage",
			treeFilterPlaceholder: "Search stores",
			actions: [
				{
					icon: "content_copy",
					action: () => {
						actionGlobalCopyState(pinia);
					},
					tooltip: "Serialize and copy the state"
				},
				{
					icon: "content_paste",
					action: async () => {
						await actionGlobalPasteState(pinia);
						api.sendInspectorTree(INSPECTOR_ID);
						api.sendInspectorState(INSPECTOR_ID);
					},
					tooltip: "Replace the state with the content of your clipboard"
				},
				{
					icon: "save",
					action: () => {
						actionGlobalSaveState(pinia);
					},
					tooltip: "Save the state as a JSON file"
				},
				{
					icon: "folder_open",
					action: async () => {
						await actionGlobalOpenStateFile(pinia);
						api.sendInspectorTree(INSPECTOR_ID);
						api.sendInspectorState(INSPECTOR_ID);
					},
					tooltip: "Import the state from a JSON file"
				}
			],
			nodeActions: [{
				icon: "restore",
				tooltip: "Reset the state (with \"$reset\")",
				action: (nodeId) => {
					const store = pinia._s.get(nodeId);
					if (!store) toastMessage(`Cannot reset "${nodeId}" store because it wasn't found.`, "warn");
					else if (typeof store.$reset !== "function") toastMessage(`Cannot reset "${nodeId}" store because it doesn't have a "$reset" method implemented.`, "warn");
					else {
						store.$reset();
						toastMessage(`Store "${nodeId}" reset.`);
					}
				}
			}]
		});
		api.on.inspectComponent((payload) => {
			const proxy = payload.componentInstance && payload.componentInstance.proxy;
			if (proxy && proxy._pStores) {
				const piniaStores = payload.componentInstance.proxy._pStores;
				Object.values(piniaStores).forEach((store) => {
					payload.instanceData.state.push({
						type: getStoreType(store.$id),
						key: "state",
						editable: true,
						value: store._isOptionsAPI ? { _custom: {
							value: toRaw(store.$state),
							actions: [{
								icon: "restore",
								tooltip: "Reset the state of this store",
								action: () => store.$reset()
							}]
						} } : Object.keys(store.$state).reduce((state, key) => {
							state[key] = store.$state[key];
							return state;
						}, {})
					});
					if (store._getters && store._getters.length) payload.instanceData.state.push({
						type: getStoreType(store.$id),
						key: "getters",
						editable: false,
						value: store._getters.reduce((getters, key) => {
							try {
								getters[key] = store[key];
							} catch (error) {
								getters[key] = error;
							}
							return getters;
						}, {})
					});
				});
			}
		});
		api.on.getInspectorTree((payload) => {
			if (payload.app === app && payload.inspectorId === INSPECTOR_ID) {
				let stores = [pinia];
				stores = stores.concat(Array.from(pinia._s.values()));
				payload.rootNodes = (payload.filter ? stores.filter((store) => "$id" in store ? store.$id.toLowerCase().includes(payload.filter.toLowerCase()) : PINIA_ROOT_LABEL.toLowerCase().includes(payload.filter.toLowerCase())) : stores).map(formatStoreForInspectorTree);
			}
		});
		globalThis.$pinia = pinia;
		api.on.getInspectorState((payload) => {
			if (payload.app === app && payload.inspectorId === INSPECTOR_ID) {
				const inspectedStore = payload.nodeId === "_root" ? pinia : pinia._s.get(payload.nodeId);
				if (!inspectedStore) return;
				if (inspectedStore) {
					if (payload.nodeId !== "_root") globalThis.$store = toRaw(inspectedStore);
					payload.state = formatStoreForInspectorState(inspectedStore);
				}
			}
		});
		api.on.editInspectorState((payload) => {
			if (payload.app === app && payload.inspectorId === INSPECTOR_ID) {
				const inspectedStore = payload.nodeId === "_root" ? pinia : pinia._s.get(payload.nodeId);
				if (!inspectedStore) return toastMessage(`store "${payload.nodeId}" not found`, "error");
				const { path } = payload;
				if (!isPinia(inspectedStore)) {
					if (path.length !== 1 || !inspectedStore._customProperties.has(path[0]) && !isWritableComputed(inspectedStore, path[0]) || path[0] in inspectedStore.$state) path.unshift("$state");
				} else path.unshift("state");
				isTimelineActive = false;
				payload.set(inspectedStore, path, payload.state.value);
				isTimelineActive = true;
			}
		});
		api.on.editComponentState((payload) => {
			if (payload.type.startsWith("🍍")) {
				const storeId = payload.type.replace(/^🍍\s*/, "");
				const store = pinia._s.get(storeId);
				if (!store) return toastMessage(`store "${storeId}" not found`, "error");
				const { path } = payload;
				if (path[0] !== "state") return toastMessage(`Invalid path for store "${storeId}":\n${path}\nOnly state can be modified.`);
				path[0] = "$state";
				isTimelineActive = false;
				payload.set(store, path, payload.state.value);
				isTimelineActive = true;
			}
		});
	});
}
function addStoreToDevtools(app, store) {
	if (!componentStateTypes.includes(getStoreType(store.$id))) componentStateTypes.push(getStoreType(store.$id));
	setupDevtoolsPlugin({
		id: "dev.esm.pinia",
		label: "Pinia 🍍",
		logo: "https://pinia.vuejs.org/logo.svg",
		packageName: "pinia",
		homepage: "https://pinia.vuejs.org",
		componentStateTypes,
		app,
		settings: { logStoreChanges: {
			label: "Notify about new/deleted stores",
			type: "boolean",
			defaultValue: true
		} }
	}, (api) => {
		const now = typeof api.now === "function" ? api.now.bind(api) : Date.now;
		store.$onAction(({ after, onError, name, args }) => {
			const groupId = runningActionId++;
			api.addTimelineEvent({
				layerId: MUTATIONS_LAYER_ID,
				event: {
					time: now(),
					title: "🛫 " + name,
					subtitle: "start",
					data: {
						store: formatDisplay(store.$id),
						action: formatDisplay(name),
						args
					},
					groupId
				}
			});
			after((result) => {
				activeAction = void 0;
				api.addTimelineEvent({
					layerId: MUTATIONS_LAYER_ID,
					event: {
						time: now(),
						title: "🛬 " + name,
						subtitle: "end",
						data: {
							store: formatDisplay(store.$id),
							action: formatDisplay(name),
							args,
							result
						},
						groupId
					}
				});
			});
			onError((error) => {
				activeAction = void 0;
				api.addTimelineEvent({
					layerId: MUTATIONS_LAYER_ID,
					event: {
						time: now(),
						logType: "error",
						title: "💥 " + name,
						subtitle: "end",
						data: {
							store: formatDisplay(store.$id),
							action: formatDisplay(name),
							args,
							error
						},
						groupId
					}
				});
			});
		}, true);
		store._customProperties.forEach((name) => {
			watch(() => unref(store[name]), (newValue, oldValue) => {
				api.notifyComponentUpdate();
				api.sendInspectorState(INSPECTOR_ID);
				if (isTimelineActive) api.addTimelineEvent({
					layerId: MUTATIONS_LAYER_ID,
					event: {
						time: now(),
						title: "Change",
						subtitle: name,
						data: {
							newValue,
							oldValue
						},
						groupId: activeAction
					}
				});
			}, { deep: true });
		});
		store.$subscribe(({ events, type }, state) => {
			api.notifyComponentUpdate();
			api.sendInspectorState(INSPECTOR_ID);
			if (!isTimelineActive) return;
			const eventData = {
				time: now(),
				title: formatMutationType(type),
				data: assign$1({ store: formatDisplay(store.$id) }, formatEventData(events)),
				groupId: activeAction
			};
			if (type === "patch function") eventData.subtitle = "⤵️";
			else if (type === "patch object") eventData.subtitle = "🧩";
			else if (events && !Array.isArray(events)) eventData.subtitle = events.type;
			if (events) eventData.data["rawEvent(s)"] = { _custom: {
				display: "DebuggerEvent",
				type: "object",
				tooltip: "raw DebuggerEvent[]",
				value: events
			} };
			api.addTimelineEvent({
				layerId: MUTATIONS_LAYER_ID,
				event: eventData
			});
		}, {
			detached: true,
			flush: "sync"
		});
		const hotUpdate = store._hotUpdate;
		store._hotUpdate = markRaw((newStore) => {
			hotUpdate(newStore);
			api.addTimelineEvent({
				layerId: MUTATIONS_LAYER_ID,
				event: {
					time: now(),
					title: "🔥 " + store.$id,
					subtitle: "HMR update",
					data: {
						store: formatDisplay(store.$id),
						info: formatDisplay(`HMR update`)
					}
				}
			});
			api.notifyComponentUpdate();
			api.sendInspectorTree(INSPECTOR_ID);
			api.sendInspectorState(INSPECTOR_ID);
		});
		const { $dispose } = store;
		store.$dispose = () => {
			$dispose();
			api.notifyComponentUpdate();
			api.sendInspectorTree(INSPECTOR_ID);
			api.sendInspectorState(INSPECTOR_ID);
			api.getSettings().logStoreChanges && toastMessage(`Disposed "${store.$id}" store 🗑`);
		};
		api.notifyComponentUpdate();
		api.sendInspectorTree(INSPECTOR_ID);
		api.sendInspectorState(INSPECTOR_ID);
		api.getSettings().logStoreChanges && toastMessage(`"${store.$id}" store installed 🆕`);
	});
}
let runningActionId = 0;
let activeAction;
/**
* Patches a store to enable action grouping in devtools by wrapping the store with a Proxy that is passed as the
* context of all actions, allowing us to set `runningAction` on each access and effectively associating any state
* mutation to the action.
*
* @param store - store to patch
* @param actionNames - list of actionst to patch
*/
function patchActionForGrouping(store, actionNames, wrapWithProxy) {
	const actions = actionNames.reduce((storeActions, actionName) => {
		storeActions[actionName] = toRaw(store)[actionName];
		return storeActions;
	}, {});
	for (const actionName in actions) store[actionName] = function() {
		const _actionId = runningActionId;
		const trackedStore = wrapWithProxy ? new Proxy(store, {
			get(...args) {
				activeAction = _actionId;
				return Reflect.get(...args);
			},
			set(...args) {
				activeAction = _actionId;
				return Reflect.set(...args);
			}
		}) : store;
		activeAction = _actionId;
		const retValue = actions[actionName].apply(trackedStore, arguments);
		activeAction = void 0;
		return retValue;
	};
}
/**
* pinia.use(devtoolsPlugin)
*/
function devtoolsPlugin({ app, store, options }) {
	if (store.$id.startsWith("__hot:")) return;
	store._isOptionsAPI = !!options.state;
	if (!store._p._testing) {
		patchActionForGrouping(store, Object.keys(options.actions), store._isOptionsAPI);
		const originalHotUpdate = store._hotUpdate;
		toRaw(store)._hotUpdate = function(newStore) {
			originalHotUpdate.apply(this, arguments);
			patchActionForGrouping(store, Object.keys(newStore._hmrPayload.actions), !!store._isOptionsAPI);
		};
	}
	addStoreToDevtools(app, store);
}
//#endregion
//#region src/createPinia.ts
/**
* Creates a Pinia instance to be used by the application
*/
function createPinia() {
	const scope = effectScope(true);
	const state = scope.run(() => ref({}));
	let _p = [];
	let toBeInstalled = [];
	const pinia = markRaw({
		install(app) {
			setActivePinia(pinia);
			pinia._a = app;
			app.provide(piniaSymbol, pinia);
			app.config.globalProperties.$pinia = pinia;
			/* istanbul ignore else */
			if ((process.env.NODE_ENV !== "production" || __VUE_PROD_DEVTOOLS__) && !(process.env.NODE_ENV === "test") && IS_CLIENT) registerPiniaDevtools(app, pinia);
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
	if ((process.env.NODE_ENV !== "production" || __VUE_PROD_DEVTOOLS__) && !(process.env.NODE_ENV === "test") && IS_CLIENT && typeof Proxy !== "undefined") pinia.use(devtoolsPlugin);
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
		if (isPlainObject(targetValue) && isPlainObject(subPatch) && !isRef(subPatch) && !isReactive(subPatch)) newState[key] = patchObject(targetValue, subPatch);
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
	if (!(process.env.NODE_ENV !== "production")) return () => {};
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
	if (!detached && getCurrentScope()) onScopeDispose(removeSubscription);
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
		if (isPlainObject(targetValue) && isPlainObject(subPatch) && Object.hasOwn(target, key) && !isRef(subPatch) && !isReactive(subPatch)) target[key] = mergeReactiveObjects(targetValue, subPatch);
		else target[key] = subPatch;
	}
	return target;
}
const skipHydrateSymbol = process.env.NODE_ENV !== "production" ? Symbol("pinia:skipHydration") : /* istanbul ignore next */ Symbol();
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
	return !!(isRef(o) && o.effect);
}
function createOptionsStore(id, options, pinia, hot) {
	const { state, actions, getters } = options;
	const initialState = pinia.state.value[id];
	let store;
	function setup() {
		if (!initialState && (!(process.env.NODE_ENV !== "production") || !hot))
 /* istanbul ignore if */
		pinia.state.value[id] = state ? state() : {};
		const localState = process.env.NODE_ENV !== "production" && hot ? toRefs(ref(state ? state() : {}).value) : toRefs(pinia.state.value[id]);
		return assign(localState, actions, Object.keys(getters || {}).reduce((computedGetters, name) => {
			if (process.env.NODE_ENV !== "production" && name in localState) diagnostics.PINIA_R1002({
				name,
				id
			});
			computedGetters[name] = markRaw(computed(() => {
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
	if (process.env.NODE_ENV !== "production" && !pinia._e.active) throw new Error("Pinia destroyed");
	const $subscribeOptions = { deep: true };
	/* istanbul ignore else */
	if (process.env.NODE_ENV !== "production") $subscribeOptions.onTrigger = (event) => {
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
	if (!isOptionsStore && !initialState && (!(process.env.NODE_ENV !== "production") || !hot))
 /* istanbul ignore if */
	pinia.state.value[$id] = {};
	const hotState = /*#__PURE__*/ ref({});
	let activeListener;
	function $patch(partialStateOrMutator) {
		let subscriptionMutation;
		isListening = isSyncListening = false;
		/* istanbul ignore else */
		if (process.env.NODE_ENV !== "production") debuggerEvents = [];
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
		nextTick().then(() => {
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
	} : process.env.NODE_ENV !== "production" ? () => {
		throw new Error(`🍍: Store "${$id}" is built using the setup syntax and does not implement $reset().`);
	} : noop;
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
	const _hmrPayload = /*#__PURE__*/ markRaw({
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
				if (process.env.NODE_ENV !== "production") diagnostics.PINIA_R1007({ id: $id });
				return noop;
			}
			const removeSubscription = addSubscription(subscriptions, callback, options.detached, () => stopWatcher());
			const stopWatcher = scope.run(() => watch(() => pinia.state.value[$id], (state) => {
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
	const store = reactive(process.env.NODE_ENV !== "production" || (process.env.NODE_ENV !== "production" || __VUE_PROD_DEVTOOLS__) && !(process.env.NODE_ENV === "test") && IS_CLIENT ? assign({
		_hmrPayload,
		_customProperties: markRaw(/* @__PURE__ */ new Set())
	}, partialStore) : partialStore);
	pinia._s.set($id, store);
	const setupStore = (pinia._a && pinia._a.runWithContext || fallbackRunWithContext)(() => pinia._e.run(() => (scope = effectScope()).run(() => setup({ action }))));
	for (const key in setupStore) {
		const prop = setupStore[key];
		if (isRef(prop) && !isComputed(prop) || isReactive(prop)) {
			if (process.env.NODE_ENV !== "production" && hot) hotState.value[key] = toRef(setupStore, key);
			else if (!isOptionsStore) {
				if (initialState && shouldHydrate(prop)) if (isRef(prop)) prop.value = initialState[key];
				else mergeReactiveObjects(prop, initialState[key]);
				pinia.state.value[$id][key] = prop;
			}
			/* istanbul ignore else */
			if (process.env.NODE_ENV !== "production") _hmrPayload.state.push(key);
		} else if (typeof prop === "function") {
			setupStore[key] = process.env.NODE_ENV !== "production" && hot ? prop : action(prop, key);
			/* istanbul ignore else */
			if (process.env.NODE_ENV !== "production") _hmrPayload.actions[key] = prop;
			optionsForPlugin.actions[key] = prop;
		} else if (process.env.NODE_ENV !== "production") {
			if (isComputed(prop)) {
				_hmrPayload.getters[key] = isOptionsStore ? options.getters[key] : prop;
				if (IS_CLIENT) (setupStore._getters || (setupStore._getters = markRaw([]))).push(key);
			}
		}
	}
	/* istanbul ignore if */
	assign(store, setupStore);
	assign(toRaw(store), setupStore);
	Object.defineProperty(store, "$state", {
		get: () => process.env.NODE_ENV !== "production" && hot ? hotState.value : pinia.state.value[$id],
		set: (state) => {
			/* istanbul ignore if */
			if (process.env.NODE_ENV !== "production" && hot) throw new Error("cannot set hotState");
			$patch(($state) => {
				assign($state, state);
			});
		}
	});
	/* istanbul ignore else */
	if (process.env.NODE_ENV !== "production") store._hotUpdate = markRaw((newStore) => {
		store._hotUpdating = true;
		newStore._hmrPayload.state.forEach((stateKey) => {
			if (stateKey in store.$state) {
				const newStateTarget = newStore.$state[stateKey];
				const oldStateSource = store.$state[stateKey];
				if (isOptionsStore && typeof newStateTarget === "object" && isPlainObject(newStateTarget) && isPlainObject(oldStateSource)) patchObject(newStateTarget, oldStateSource);
				else newStore.$state[stateKey] = oldStateSource;
			}
			store[stateKey] = toRef(newStore.$state, stateKey);
		});
		Object.keys(store.$state).forEach((stateKey) => {
			if (!(stateKey in newStore.$state)) delete store[stateKey];
		});
		isListening = false;
		isSyncListening = false;
		pinia.state.value[$id] = toRef(newStore._hmrPayload, "hotState");
		isSyncListening = true;
		nextTick().then(() => {
			isListening = true;
		});
		for (const actionName in newStore._hmrPayload.actions) {
			const actionFn = newStore[actionName];
			store[actionName] = action(actionFn, actionName);
		}
		for (const getterName in newStore._hmrPayload.getters) {
			const getter = newStore._hmrPayload.getters[getterName];
			const getterValue = isOptionsStore ? computed(() => {
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
	if ((process.env.NODE_ENV !== "production" || __VUE_PROD_DEVTOOLS__) && !(process.env.NODE_ENV === "test") && IS_CLIENT) {
		const nonEnumerable = {
			writable: true,
			configurable: true,
			enumerable: false
		};
		[
			"_p",
			"_hmrPayload",
			"_getters",
			"_customProperties"
		].forEach((p) => {
			Object.defineProperty(store, p, assign({ value: store[p] }, nonEnumerable));
		});
	}
	pinia._p.forEach((extender) => {
		const extensions = scope.run(() => extender({
			store,
			app: pinia._a,
			pinia,
			options: optionsForPlugin
		}));
		/* istanbul ignore else */
		if ((process.env.NODE_ENV !== "production" || __VUE_PROD_DEVTOOLS__) && !(process.env.NODE_ENV === "test") && IS_CLIENT) Object.keys(extensions || {}).forEach((key) => store._customProperties.add(key));
		if (process.env.NODE_ENV !== "production") for (const key in extensions) {
			const value = extensions[key];
			if (typeof value === "object" && !isRef(value) && !isReactive(value) && !value?.__v_skip) diagnostics.PINIA_R1006({
				key,
				id: $id
			});
		}
		assign(store, extensions);
	});
	if (process.env.NODE_ENV !== "production" && store.$state && typeof store.$state === "object" && typeof store.$state.constructor === "function" && !store.$state.constructor.toString().includes("[native code]")) diagnostics.PINIA_R1003({ id: store.$id });
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
		const hasContext = hasInjectionContext();
		pinia = (process.env.NODE_ENV === "test" && activePinia && activePinia._testing ? null : pinia) || (hasContext ? inject(piniaSymbol, null) : null);
		if (pinia) setActivePinia(pinia);
		if (process.env.NODE_ENV !== "production" && !activePinia) throw new Error("[🍍]: \"getActivePinia()\" was called but there was no active Pinia. Are you trying to use a store before calling \"app.use(pinia)\"?\nSee https://pinia.vuejs.org/core-concepts/outside-component-usage.html for help.\nThis will fail in production.");
		pinia = activePinia;
		if (!pinia._s.has(id)) {
			if (isSetupStore) createSetupStore(id, setup, options, pinia);
			else createOptionsStore(id, options, pinia);
			/* istanbul ignore else */
			if (process.env.NODE_ENV !== "production") useStore._pinia = pinia;
		}
		const store = pinia._s.get(id);
		if (process.env.NODE_ENV !== "production" && hot) {
			const hotId = "__hot:" + id;
			const newStore = isSetupStore ? createSetupStore(hotId, setup, options, pinia, true) : createOptionsStore(hotId, assign({}, options), pinia, true);
			hot._hotUpdate(newStore);
			delete pinia.state.value[hotId];
			pinia._s.delete(hotId);
		}
		if (process.env.NODE_ENV !== "production" && IS_CLIENT) {
			const currentInstance = getCurrentInstance();
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
	if (process.env.NODE_ENV !== "production" && Array.isArray(stores[0])) {
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
	const rawStore = toRaw(store);
	const refs = {};
	for (const key in rawStore) {
		const value = rawStore[key];
		if (value?.effect) refs[key] = computed({
			get: () => store[key],
			set(value) {
				store[key] = value;
			}
		});
		else if (isRef(value) || isReactive(value)) refs[key] = toRef(store, key);
	}
	return refs;
}
//#endregion
export { MutationType, acceptHMRUpdate, createPinia, defineStore, disposePinia, getActivePinia, mapActions, mapGetters, mapState, mapStores, mapWritableState, piniaSymbol, setActivePinia, setMapStoreSuffix, shouldHydrate, skipHydrate, storeToRefs };
