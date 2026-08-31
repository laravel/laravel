import { babelParse, getLang, isFunctionType, isLiteralType, resolveObjectKey, resolveString } from "ast-kit";
import { createFilter as createFilter$1, createFilter as createRollupFilter, normalizePath } from "unplugin-utils";
import { parse, walkIdentifiers } from "@vue/compiler-sfc";

export * from "magic-string-ast"

export * from "ast-kit"

//#region src/ast.ts
function checkInvalidScopeReference(node, method, setupBindings) {
	if (!node) return;
	walkIdentifiers(node, (id) => {
		if (setupBindings.includes(id.name)) throw new SyntaxError(`\`${method}()\` in <script setup> cannot reference locally declared variables (${id.name}) because it will be hoisted outside of the setup() function.`);
	});
}
function isStaticExpression(node, options = {}) {
	const { magicComment, fn, object, objectMethod, array, unary, regex } = options;
	if (magicComment && node.leadingComments?.some((comment) => comment.value.trim() === magicComment)) return true;
	else if (fn && isFunctionType(node)) return true;
	switch (node.type) {
		case "UnaryExpression": return !!unary && isStaticExpression(node.argument, options);
		case "LogicalExpression":
		case "BinaryExpression": return isStaticExpression(node.left, options) && isStaticExpression(node.right, options);
		case "ConditionalExpression": return isStaticExpression(node.test, options) && isStaticExpression(node.consequent, options) && isStaticExpression(node.alternate, options);
		case "SequenceExpression":
		case "TemplateLiteral": return node.expressions.every((expr) => isStaticExpression(expr, options));
		case "ArrayExpression": return !!array && node.elements.every((element) => element && isStaticExpression(element, options));
		case "ObjectExpression": return !!object && node.properties.every((prop) => {
			if (prop.type === "SpreadElement") return prop.argument.type === "ObjectExpression" && isStaticExpression(prop.argument, options);
			else if (!isLiteralType(prop.key) && prop.computed) return false;
			else if (prop.type === "ObjectProperty" && !isStaticExpression(prop.value, options)) return false;
			if (prop.type === "ObjectMethod" && !objectMethod) return false;
			return true;
		});
		case "ParenthesizedExpression":
		case "TSNonNullExpression":
		case "TSAsExpression":
		case "TSTypeAssertion":
		case "TSSatisfiesExpression": return isStaticExpression(node.expression, options);
		case "RegExpLiteral": return !!regex;
	}
	if (isLiteralType(node)) return true;
	return false;
}
function isStaticObjectKey(node) {
	return node.properties.every((prop) => {
		if (prop.type === "SpreadElement") return prop.argument.type === "ObjectExpression" && isStaticObjectKey(prop.argument);
		return !prop.computed || isLiteralType(prop.key);
	});
}
/**
* @param node must be a static expression, SpreadElement is not supported
*/
function resolveObjectExpression(node) {
	const maps = Object.create(null);
	for (const property of node.properties) if (property.type === "SpreadElement") {
		if (property.argument.type !== "ObjectExpression") return void 0;
		Object.assign(maps, resolveObjectExpression(property.argument));
	} else {
		const key = resolveObjectKey(property);
		maps[key] = property;
	}
	return maps;
}
const importedMap = /* @__PURE__ */ new WeakMap();
const HELPER_PREFIX = "__MACROS_";
function importHelperFn(s, offset, imported, local = imported, from = "vue", prefix = HELPER_PREFIX) {
	const cacheKey = `${from}@${imported}@${prefix}@${local}`;
	if (!importedMap.get(s)?.has(cacheKey)) {
		s.appendLeft(offset, `\nimport ${imported === "default" ? prefix + local : `{ ${imported} as ${prefix + local} }`} from ${JSON.stringify(from)};`);
		if (importedMap.has(s)) importedMap.get(s).add(cacheKey);
		else importedMap.set(s, new Set([cacheKey]));
	}
	return `${prefix}${local}`;
}

//#endregion
//#region src/constants.ts
const DEFINE_PROPS = "defineProps";
const DEFINE_PROPS_DOLLAR = "$defineProps";
const DEFINE_PROPS_REFS = "definePropsRefs";
const DEFINE_EMITS = "defineEmits";
const WITH_DEFAULTS = "withDefaults";
const DEFINE_OPTIONS = "defineOptions";
const DEFINE_MODELS = "defineModels";
const DEFINE_MODELS_DOLLAR = "$defineModels";
const DEFINE_SETUP_COMPONENT = "defineSetupComponent";
const DEFINE_RENDER = "defineRender";
const DEFINE_SLOTS = "defineSlots";
const DEFINE_STYLEX = "defineStyleX";
const DEFINE_PROP = "defineProp";
const DEFINE_PROP_DOLLAR = "$defineProp";
const DEFINE_EMIT = "defineEmit";
const REPO_ISSUE_URL = `https://github.com/vue-macros/vue-macros/issues`;
const REGEX_SRC_FILE = /\.[cm]?[jt]sx?$/;
const REGEX_SETUP_SFC = /\.setup\.[cm]?[jt]sx?$/;
const REGEX_SETUP_SFC_SUB = /\.setup\.[cm]?[jt]sx?((?!(?<!definePage&)vue&).)*$/;
const REGEX_VUE_SFC = /\.vue$/;
/** webpack only */
const REGEX_VUE_SUB = /\.vue(\.[tj]sx?)?\?vue&type=script/;
/** webpack only */
const REGEX_VUE_SUB_SETUP = /\.vue(\.[tj]sx?)?\?vue&type=script\b.+\bsetup=true/;
const REGEX_NODE_MODULES = /node_modules/;
const REGEX_SUPPORTED_EXT = /\.([cm]?[jt]sx?|vue)$/;
const VIRTUAL_ID_PREFIX = "/vue-macros";

//#endregion
//#region src/dep.ts
let require;
function getRequire() {
	if (require) return require;
	try {
		if (globalThis.process?.getBuiltinModule) {
			const module = process.getBuiltinModule("node:module");
			if (module?.createRequire) return require = module.createRequire(import.meta.url);
		}
	} catch {}
}
function detectVueVersion(root, defaultVersion = 3.5) {
	const require$1 = getRequire();
	if (!require$1) {
		console.warn(`Cannot detect Vue version. Default to Vue ${defaultVersion}`);
		return defaultVersion;
	}
	const { resolve } = require$1("node:path");
	const { statSync } = require$1("node:fs");
	const { getPackageInfoSync } = require$1("local-pkg");
	root ||= process.cwd();
	let isFile = false;
	try {
		isFile = statSync(root).isFile();
	} catch {}
	const paths = [root];
	if (!isFile) paths.unshift(resolve(root, "_index.js"));
	const vuePkg = getPackageInfoSync("vue", { paths });
	if (vuePkg && vuePkg.version) {
		const version = Number.parseFloat(vuePkg.version);
		return version >= 2 && version < 3 ? Math.trunc(version) : version;
	} else return defaultVersion;
}

//#endregion
//#region src/error.ts
var TransformError = class extends SyntaxError {
	constructor(message) {
		super(message);
		this.message = message;
		this.name = "TransformError";
	}
};

//#endregion
//#region src/unplugin.ts
function createFilter(options) {
	return createFilter$1(options.include, options.exclude);
}
const VUE_PLUGINS = new Set(["vite:vue", "unplugin-vue"]);
function getVuePluginApi(plugins) {
	const vuePlugin = (plugins || []).find((p) => VUE_PLUGINS.has(p.name));
	if (!vuePlugin) throw new Error("Cannot find Vue plugin (@vitejs/plugin-vue or unplugin-vue). Please make sure to add it before using Vue Macros.");
	const api = vuePlugin.api;
	if (!api?.version) throw new Error("The Vue plugin is not supported (@vitejs/plugin-vue or unplugin-vue). Please make sure version > 4.3.4.");
	return api;
}
let FilterFileType = /* @__PURE__ */ function(FilterFileType$1) {
	/** Vue SFC */
	FilterFileType$1[FilterFileType$1["VUE_SFC"] = 0] = "VUE_SFC";
	/** Vue SFC with `<script setup>` */
	FilterFileType$1[FilterFileType$1["VUE_SFC_WITH_SETUP"] = 1] = "VUE_SFC_WITH_SETUP";
	/** foo.setup.tsx */
	FilterFileType$1[FilterFileType$1["SETUP_SFC"] = 2] = "SETUP_SFC";
	/** Source files */
	FilterFileType$1[FilterFileType$1["SRC_FILE"] = 3] = "SRC_FILE";
	return FilterFileType$1;
}({});
function getFilterPattern(types, framework) {
	const filter = [];
	const isWebpackLike = framework === "webpack" || framework === "rspack";
	if (types.includes(FilterFileType.VUE_SFC)) filter.push(isWebpackLike ? REGEX_VUE_SUB : REGEX_VUE_SFC);
	if (types.includes(FilterFileType.VUE_SFC_WITH_SETUP)) filter.push(isWebpackLike ? REGEX_VUE_SUB_SETUP : REGEX_VUE_SFC);
	if (types.includes(FilterFileType.SETUP_SFC)) filter.push(REGEX_SETUP_SFC);
	if (types.includes(FilterFileType.SRC_FILE)) filter.push(REGEX_SRC_FILE);
	return filter;
}
function hackViteHMR(ctx, filter, callback) {
	if (!filter(ctx.file)) return;
	const { read } = ctx;
	ctx.read = async () => {
		const code = await read();
		return (await callback(code, ctx.file))?.code || code;
	};
}

//#endregion
//#region src/vue.ts
function parseSFC(code, id) {
	const sfc = parse(code, { filename: id });
	const { descriptor, errors } = sfc;
	const scriptLang = sfc.descriptor.script?.lang;
	const scriptSetupLang = sfc.descriptor.scriptSetup?.lang;
	if (sfc.descriptor.script && sfc.descriptor.scriptSetup && (scriptLang || "js") !== (scriptSetupLang || "js")) throw new Error(`[vue-macros] <script> and <script setup> must have the same language type.`);
	const lang = scriptLang || scriptSetupLang;
	return Object.assign({}, descriptor, {
		sfc,
		lang,
		errors,
		offset: descriptor.scriptSetup?.loc.start.offset ?? 0,
		getSetupAst() {
			if (!descriptor.scriptSetup) return;
			return babelParse(descriptor.scriptSetup.content, lang, {
				plugins: [["importAttributes", { deprecatedAssertSyntax: true }]],
				cache: true
			});
		},
		getScriptAst() {
			if (!descriptor.script) return;
			return babelParse(descriptor.script.content, lang, {
				plugins: [["importAttributes", { deprecatedAssertSyntax: true }]],
				cache: true
			});
		}
	});
}
function getFileCodeAndLang(code, id) {
	if (!REGEX_VUE_SFC.test(id)) return {
		code,
		lang: getLang(id)
	};
	const sfc = parseSFC(code, id);
	const scriptCode = sfc.script?.content ?? "";
	return {
		code: sfc.scriptSetup ? `${scriptCode}\n;\n${sfc.scriptSetup.content}` : scriptCode,
		lang: sfc.lang ?? "js"
	};
}
function addNormalScript({ script, lang }, s) {
	return {
		start() {
			if (script) return script.loc.end.offset;
			const attrs = lang ? ` lang="${lang}"` : "";
			s.prependLeft(0, `<script${attrs}>`);
			return 0;
		},
		end() {
			if (!script) s.appendRight(0, `\n<\/script>\n`);
		}
	};
}
function removeMacroImport(node, s, offset) {
	if (node.type === "ImportDeclaration" && node.attributes?.some((attr) => resolveString(attr.key) === "type" && attr.value.value === "macro")) {
		s.removeNode(node, { offset });
		return true;
	}
}

//#endregion
export { DEFINE_EMIT, DEFINE_EMITS, DEFINE_MODELS, DEFINE_MODELS_DOLLAR, DEFINE_OPTIONS, DEFINE_PROP, DEFINE_PROPS, DEFINE_PROPS_DOLLAR, DEFINE_PROPS_REFS, DEFINE_PROP_DOLLAR, DEFINE_RENDER, DEFINE_SETUP_COMPONENT, DEFINE_SLOTS, DEFINE_STYLEX, FilterFileType, HELPER_PREFIX, REGEX_NODE_MODULES, REGEX_SETUP_SFC, REGEX_SETUP_SFC_SUB, REGEX_SRC_FILE, REGEX_SUPPORTED_EXT, REGEX_VUE_SFC, REGEX_VUE_SUB, REGEX_VUE_SUB_SETUP, REPO_ISSUE_URL, TransformError, VIRTUAL_ID_PREFIX, WITH_DEFAULTS, addNormalScript, checkInvalidScopeReference, createFilter, createRollupFilter, detectVueVersion, getFileCodeAndLang, getFilterPattern, getVuePluginApi, hackViteHMR, importHelperFn, isStaticExpression, isStaticObjectKey, normalizePath, parseSFC, removeMacroImport, resolveObjectExpression };