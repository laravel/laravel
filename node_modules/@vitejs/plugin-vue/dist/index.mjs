import { createRequire } from "node:module";
import fs from "node:fs";
import { createFilter, formatPostcssSourceMap, isCSSRequest, normalizePath, transformWithEsbuild } from "vite";
import { computed, shallowRef } from "vue";
import { exactRegex, makeIdFiltersToMatchWithQuery } from "@rolldown/pluginutils";
import path from "node:path";
import crypto from "node:crypto";
import { isatty } from "node:tty";
import { formatWithOptions, inspect } from "node:util";
//#region package.json
var version = "6.0.8";
//#endregion
//#region src/compiler.ts
function resolveCompiler(root) {
	const compiler = tryResolveCompiler(root) || tryResolveCompiler();
	if (!compiler) throw new Error("Failed to resolve vue/compiler-sfc.\n@vitejs/plugin-vue requires vue (>=3.2.25) to be present in the dependency tree.");
	return compiler;
}
function tryResolveCompiler(root) {
	const vueMeta = tryRequire("vue/package.json", root);
	if (vueMeta && vueMeta.version.split(".")[0] >= 3) return tryRequire("vue/compiler-sfc", root);
}
const _require = createRequire(import.meta.url);
function tryRequire(id, from) {
	try {
		return from ? _require(_require.resolve(id, { paths: [from] })) : _require(id);
	} catch (e) {}
}
//#endregion
//#region src/utils/query.ts
function parseVueRequest(id) {
	const [filename, rawQuery] = id.split(`?`, 2);
	const query = Object.fromEntries(new URLSearchParams(rawQuery));
	if (query.vue != null) query.vue = true;
	if (query.index != null) query.index = Number(query.index);
	if (query.raw != null) query.raw = true;
	if (query.url != null) query.url = true;
	if (query.scoped != null) query.scoped = true;
	return {
		filename,
		query
	};
}
//#endregion
//#region src/utils/descriptorCache.ts
const cache = /* @__PURE__ */ new Map();
const hmrCache = /* @__PURE__ */ new Map();
const prevCache = /* @__PURE__ */ new Map();
function createDescriptor(filename, source, { root, isProduction, sourceMap, compiler, template, features }, hmr = false) {
	const { descriptor, errors } = compiler.parse(source, {
		filename,
		sourceMap,
		templateParseOptions: template?.compilerOptions
	});
	const normalizedPath = normalizePath(path.relative(root, filename));
	const componentIdGenerator = features?.componentIdGenerator;
	if (componentIdGenerator === "filepath") descriptor.id = getHash(normalizedPath);
	else if (componentIdGenerator === "filepath-source") descriptor.id = getHash(normalizedPath + source);
	else if (typeof componentIdGenerator === "function") descriptor.id = componentIdGenerator(normalizedPath, source, isProduction, getHash);
	else descriptor.id = getHash(normalizedPath + (isProduction ? source : ""));
	(hmr ? hmrCache : cache).set(filename, descriptor);
	return {
		descriptor,
		errors
	};
}
function getPrevDescriptor(filename) {
	return prevCache.get(filename);
}
function invalidateDescriptor(filename, hmr = false) {
	const _cache = hmr ? hmrCache : cache;
	const prev = _cache.get(filename);
	_cache.delete(filename);
	if (prev) prevCache.set(filename, prev);
}
function getDescriptor(filename, options, createIfNotFound = true, hmr = false, code) {
	const _cache = hmr ? hmrCache : cache;
	if (_cache.has(filename)) return _cache.get(filename);
	if (createIfNotFound) {
		const { descriptor, errors } = createDescriptor(filename, code ?? fs.readFileSync(filename, "utf-8"), options, hmr);
		if (errors.length && !hmr) throw errors[0];
		return descriptor;
	}
}
function getSrcDescriptor(filename, query) {
	if (query.scoped) return cache.get(`${filename}?src=${query.src}`);
	return cache.get(filename);
}
function getTempSrcDescriptor(filename, query) {
	return {
		filename,
		id: query.id || "",
		styles: [{
			scoped: query.scoped,
			loc: { start: {
				line: 0,
				column: 0
			} }
		}],
		isTemp: true
	};
}
function setSrcDescriptor(filename, entry, scoped) {
	if (scoped) {
		cache.set(`${filename}?src=${entry.id}`, entry);
		return;
	}
	cache.set(filename, entry);
}
function getHash(text) {
	return crypto.hash("sha256", text, "hex").substring(0, 8);
}
//#endregion
//#region ../../node_modules/.pnpm/slash@5.1.0/node_modules/slash/index.js
function slash(path) {
	if (path.startsWith("\\\\?\\")) return path;
	return path.replace(/\\/g, "/");
}
//#endregion
//#region src/utils/error.ts
function createRollupError(id, error) {
	const { message, name, stack } = error;
	const rollupError = {
		id,
		plugin: "vue",
		message,
		name,
		stack
	};
	if ("code" in error && error.loc) rollupError.loc = {
		file: id,
		line: error.loc.start.line,
		column: error.loc.start.column
	};
	return rollupError;
}
//#endregion
//#region src/utils/vapor.ts
/**
* Resolve the effective Vapor mode for an SFC handled by this plugin.
*
* `descriptor.vapor` is the per-file opt-in marker. `features.vapor` is the
* plugin-level force switch for SFCs that can be forced into Vapor mode.
*/
function isVaporMode(descriptor, options) {
	if (descriptor.vapor) return true;
	if (options.features?.vapor) return canForceVaporMode(descriptor);
	return false;
}
/**
* `features.vapor` can force template-only SFCs, `<script setup>` SFCs, and
* non-`.vue` transformed SFCs into Vapor mode. It cannot force `.vue` SFCs
* with only a normal `<script>` because Vapor SFC support requires
* `<script setup>`.
*/
function canForceVaporMode(descriptor) {
	if (descriptor.filename.endsWith(".vue")) {
		if (descriptor.scriptSetup) return true;
		if (descriptor.script) return false;
	}
	return true;
}
//#endregion
//#region src/template.ts
async function transformTemplateAsModule(code, filename, descriptor, options, pluginContext, ssr, customElement) {
	const result = compile(code, filename, descriptor, options, pluginContext, ssr, customElement);
	let returnCode = result.code;
	returnCode += `\nexport const multiRoot = ${JSON.stringify(result.multiRoot)}`;
	if (options.devServer && options.devServer.config.server.hmr !== false && !ssr && !options.isProduction) returnCode += `\nimport.meta.hot.accept(({ render }) => {
      __VUE_HMR_RUNTIME__.rerender(${JSON.stringify(descriptor.id)}, render)
    })`;
	return {
		code: returnCode,
		map: result.map
	};
}
/**
* transform the template directly in the main SFC module
*/
function transformTemplateInMain(code, descriptor, options, pluginContext, ssr, customElement) {
	const result = compile(code, descriptor.filename, descriptor, options, pluginContext, ssr, customElement);
	return {
		...result,
		code: result.code.replace(/\nexport (function|const) (render|ssrRender)/, "\n$1 _sfc_$2")
	};
}
function compile(code, filename, descriptor, options, pluginContext, ssr, customElement) {
	resolveScript(descriptor, options, ssr, customElement);
	const result = options.compiler.compileTemplate({
		...resolveTemplateCompilerOptions(descriptor, options, filename, ssr),
		source: code
	});
	if (result.errors.length) result.errors.forEach((error) => pluginContext.error(typeof error === "string" ? {
		id: filename,
		message: error
	} : createRollupError(filename, error)));
	if (result.tips.length) result.tips.forEach((tip) => pluginContext.warn({
		id: filename,
		message: tip
	}));
	return result;
}
function resolveTemplateCompilerOptions(descriptor, options, filename, ssr) {
	const block = descriptor.template;
	if (!block) return;
	const resolvedScript = getResolvedScript(descriptor, ssr);
	const hasScoped = descriptor.styles.some((s) => s.scoped);
	const { id, cssVars } = descriptor;
	let transformAssetUrls = options.template?.transformAssetUrls;
	let assetUrlOptions;
	if (transformAssetUrls === false) {} else if (options.devServer) {
		if (filename.startsWith(options.root)) {
			const devBase = options.devServer.config.base;
			assetUrlOptions = {
				base: (options.devServer.config.server?.origin ?? "") + devBase + slash(path.relative(options.root, path.dirname(filename))),
				includeAbsolute: !!devBase
			};
		}
	} else assetUrlOptions = { includeAbsolute: true };
	if (transformAssetUrls && typeof transformAssetUrls === "object") if (Object.values(transformAssetUrls).some((val) => Array.isArray(val))) transformAssetUrls = {
		...assetUrlOptions,
		tags: transformAssetUrls
	};
	else transformAssetUrls = {
		...assetUrlOptions,
		...transformAssetUrls
	};
	else transformAssetUrls = assetUrlOptions;
	let preprocessOptions = block.lang && options.template?.preprocessOptions;
	if (block.lang === "pug") preprocessOptions = {
		doctype: "html",
		...preprocessOptions
	};
	const expressionPlugins = options.template?.compilerOptions?.expressionPlugins || [];
	const lang = descriptor.scriptSetup?.lang || descriptor.script?.lang;
	if (lang && /tsx?$/.test(lang) && !expressionPlugins.includes("typescript")) expressionPlugins.push("typescript");
	return {
		...options.template,
		vapor: isVaporMode(descriptor, options),
		id,
		ast: canReuseAST(options.compiler.version) ? descriptor.template?.ast : void 0,
		filename,
		scoped: hasScoped,
		slotted: descriptor.slotted,
		isProd: options.isProduction,
		inMap: block.src ? void 0 : block.map,
		ssr,
		ssrCssVars: cssVars,
		transformAssetUrls,
		preprocessLang: block.lang === "html" ? void 0 : block.lang,
		preprocessOptions,
		compilerOptions: {
			...options.template?.compilerOptions,
			scopeId: hasScoped ? `data-v-${id}` : void 0,
			bindingMetadata: resolvedScript ? resolvedScript.bindings : void 0,
			expressionPlugins,
			sourceMap: options.sourceMap
		}
	};
}
/**
* Versions before 3.4.3 have issues when the user has passed additional
* template parse options e.g. `isCustomElement`.
*/
function canReuseAST(version) {
	if (version) {
		const [_, minor, patch] = version.split(".").map(Number);
		if (minor >= 4 && patch >= 3) return true;
	}
	return false;
}
//#endregion
//#region src/script.ts
let clientCache = /* @__PURE__ */ new WeakMap();
let ssrCache = /* @__PURE__ */ new WeakMap();
const typeDepToSFCMap = /* @__PURE__ */ new Map();
function invalidateScript(filename) {
	const desc = cache.get(filename);
	if (desc) {
		clientCache.delete(desc);
		ssrCache.delete(desc);
	}
}
function getResolvedScript(descriptor, ssr) {
	return (ssr ? ssrCache : clientCache).get(descriptor);
}
function setResolvedScript(descriptor, script, ssr) {
	(ssr ? ssrCache : clientCache).set(descriptor, script);
}
function clearScriptCache() {
	clientCache = /* @__PURE__ */ new WeakMap();
	ssrCache = /* @__PURE__ */ new WeakMap();
}
function isUseInlineTemplate(descriptor, options) {
	return !options.devServer && !options.devToolsEnabled && !!descriptor.scriptSetup && !descriptor.template?.src;
}
const scriptIdentifier = `_sfc_main`;
function resolveScript(descriptor, options, ssr, customElement) {
	if (!descriptor.script && !descriptor.scriptSetup) return null;
	const cached = getResolvedScript(descriptor, ssr);
	if (cached) return cached;
	const resolved = options.compiler.compileScript(descriptor, {
		...options.script,
		id: descriptor.id,
		isProd: options.isProduction,
		inlineTemplate: isUseInlineTemplate(descriptor, options),
		templateOptions: resolveTemplateCompilerOptions(descriptor, options, descriptor.filename, ssr),
		sourceMap: options.sourceMap,
		genDefaultAs: canInlineMain(descriptor, options) ? scriptIdentifier : void 0,
		customElement,
		vapor: isVaporMode(descriptor, options),
		propsDestructure: options.features?.propsDestructure ?? options.script?.propsDestructure
	});
	if (!options.isProduction && resolved?.deps) {
		for (const [key, sfcs] of typeDepToSFCMap) if (sfcs.has(descriptor.filename) && !resolved.deps.includes(key)) sfcs.delete(descriptor.filename);
		for (const dep of resolved.deps) {
			const existingSet = typeDepToSFCMap.get(dep);
			if (!existingSet) typeDepToSFCMap.set(dep, /* @__PURE__ */ new Set([descriptor.filename]));
			else existingSet.add(descriptor.filename);
		}
	}
	setResolvedScript(descriptor, resolved, ssr);
	return resolved;
}
function canInlineMain(descriptor, options) {
	if (descriptor.script?.src || descriptor.scriptSetup?.src) return false;
	const lang = descriptor.script?.lang || descriptor.scriptSetup?.lang;
	if (!lang || lang === "js") return true;
	if (lang === "ts" && options.devServer) return true;
	return false;
}
//#endregion
//#region ../../node_modules/.pnpm/@jridgewell+sourcemap-codec@1.5.5/node_modules/@jridgewell/sourcemap-codec/dist/sourcemap-codec.mjs
var comma = ",".charCodeAt(0);
var semicolon = ";".charCodeAt(0);
var chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
var intToChar = /* @__PURE__ */ new Uint8Array(64);
var charToInt = /* @__PURE__ */ new Uint8Array(128);
for (let i = 0; i < chars.length; i++) {
	const c = chars.charCodeAt(i);
	intToChar[i] = c;
	charToInt[c] = i;
}
function decodeInteger(reader, relative) {
	let value = 0;
	let shift = 0;
	let integer = 0;
	do {
		integer = charToInt[reader.next()];
		value |= (integer & 31) << shift;
		shift += 5;
	} while (integer & 32);
	const shouldNegate = value & 1;
	value >>>= 1;
	if (shouldNegate) value = -2147483648 | -value;
	return relative + value;
}
function encodeInteger(builder, num, relative) {
	let delta = num - relative;
	delta = delta < 0 ? -delta << 1 | 1 : delta << 1;
	do {
		let clamped = delta & 31;
		delta >>>= 5;
		if (delta > 0) clamped |= 32;
		builder.write(intToChar[clamped]);
	} while (delta > 0);
	return num;
}
function hasMoreVlq(reader, max) {
	if (reader.pos >= max) return false;
	return reader.peek() !== comma;
}
var bufLength = 1024 * 16;
var td = typeof TextDecoder !== "undefined" ? /* @__PURE__ */ new TextDecoder() : typeof Buffer !== "undefined" ? { decode(buf) {
	return Buffer.from(buf.buffer, buf.byteOffset, buf.byteLength).toString();
} } : { decode(buf) {
	let out = "";
	for (let i = 0; i < buf.length; i++) out += String.fromCharCode(buf[i]);
	return out;
} };
var StringWriter = class {
	constructor() {
		this.pos = 0;
		this.out = "";
		this.buffer = new Uint8Array(bufLength);
	}
	write(v) {
		const { buffer } = this;
		buffer[this.pos++] = v;
		if (this.pos === bufLength) {
			this.out += td.decode(buffer);
			this.pos = 0;
		}
	}
	flush() {
		const { buffer, out, pos } = this;
		return pos > 0 ? out + td.decode(buffer.subarray(0, pos)) : out;
	}
};
var StringReader = class {
	constructor(buffer) {
		this.pos = 0;
		this.buffer = buffer;
	}
	next() {
		return this.buffer.charCodeAt(this.pos++);
	}
	peek() {
		return this.buffer.charCodeAt(this.pos);
	}
	indexOf(char) {
		const { buffer, pos } = this;
		const idx = buffer.indexOf(char, pos);
		return idx === -1 ? buffer.length : idx;
	}
};
function decode(mappings) {
	const { length } = mappings;
	const reader = new StringReader(mappings);
	const decoded = [];
	let genColumn = 0;
	let sourcesIndex = 0;
	let sourceLine = 0;
	let sourceColumn = 0;
	let namesIndex = 0;
	do {
		const semi = reader.indexOf(";");
		const line = [];
		let sorted = true;
		let lastCol = 0;
		genColumn = 0;
		while (reader.pos < semi) {
			let seg;
			genColumn = decodeInteger(reader, genColumn);
			if (genColumn < lastCol) sorted = false;
			lastCol = genColumn;
			if (hasMoreVlq(reader, semi)) {
				sourcesIndex = decodeInteger(reader, sourcesIndex);
				sourceLine = decodeInteger(reader, sourceLine);
				sourceColumn = decodeInteger(reader, sourceColumn);
				if (hasMoreVlq(reader, semi)) {
					namesIndex = decodeInteger(reader, namesIndex);
					seg = [
						genColumn,
						sourcesIndex,
						sourceLine,
						sourceColumn,
						namesIndex
					];
				} else seg = [
					genColumn,
					sourcesIndex,
					sourceLine,
					sourceColumn
				];
			} else seg = [genColumn];
			line.push(seg);
			reader.pos++;
		}
		if (!sorted) sort(line);
		decoded.push(line);
		reader.pos = semi + 1;
	} while (reader.pos <= length);
	return decoded;
}
function sort(line) {
	line.sort(sortComparator$1);
}
function sortComparator$1(a, b) {
	return a[0] - b[0];
}
function encode(decoded) {
	const writer = new StringWriter();
	let sourcesIndex = 0;
	let sourceLine = 0;
	let sourceColumn = 0;
	let namesIndex = 0;
	for (let i = 0; i < decoded.length; i++) {
		const line = decoded[i];
		if (i > 0) writer.write(semicolon);
		if (line.length === 0) continue;
		let genColumn = 0;
		for (let j = 0; j < line.length; j++) {
			const segment = line[j];
			if (j > 0) writer.write(comma);
			genColumn = encodeInteger(writer, segment[0], genColumn);
			if (segment.length === 1) continue;
			sourcesIndex = encodeInteger(writer, segment[1], sourcesIndex);
			sourceLine = encodeInteger(writer, segment[2], sourceLine);
			sourceColumn = encodeInteger(writer, segment[3], sourceColumn);
			if (segment.length === 4) continue;
			namesIndex = encodeInteger(writer, segment[4], namesIndex);
		}
	}
	return writer.flush();
}
//#endregion
//#region ../../node_modules/.pnpm/@jridgewell+resolve-uri@3.1.2/node_modules/@jridgewell/resolve-uri/dist/resolve-uri.mjs
const schemeRegex = /^[\w+.-]+:\/\//;
/**
* Matches the parts of a URL:
* 1. Scheme, including ":", guaranteed.
* 2. User/password, including "@", optional.
* 3. Host, guaranteed.
* 4. Port, including ":", optional.
* 5. Path, including "/", optional.
* 6. Query, including "?", optional.
* 7. Hash, including "#", optional.
*/
const urlRegex = /^([\w+.-]+:)\/\/([^@/#?]*@)?([^:/#?]*)(:\d+)?(\/[^#?]*)?(\?[^#]*)?(#.*)?/;
/**
* File URLs are weird. They dont' need the regular `//` in the scheme, they may or may not start
* with a leading `/`, they can have a domain (but only if they don't start with a Windows drive).
*
* 1. Host, optional.
* 2. Path, which may include "/", guaranteed.
* 3. Query, including "?", optional.
* 4. Hash, including "#", optional.
*/
const fileRegex = /^file:(?:\/\/((?![a-z]:)[^/#?]*)?)?(\/?[^#?]*)(\?[^#]*)?(#.*)?/i;
function isAbsoluteUrl(input) {
	return schemeRegex.test(input);
}
function isSchemeRelativeUrl(input) {
	return input.startsWith("//");
}
function isAbsolutePath(input) {
	return input.startsWith("/");
}
function isFileUrl(input) {
	return input.startsWith("file:");
}
function isRelative(input) {
	return /^[.?#]/.test(input);
}
function parseAbsoluteUrl(input) {
	const match = urlRegex.exec(input);
	return makeUrl(match[1], match[2] || "", match[3], match[4] || "", match[5] || "/", match[6] || "", match[7] || "");
}
function parseFileUrl(input) {
	const match = fileRegex.exec(input);
	const path = match[2];
	return makeUrl("file:", "", match[1] || "", "", isAbsolutePath(path) ? path : "/" + path, match[3] || "", match[4] || "");
}
function makeUrl(scheme, user, host, port, path, query, hash) {
	return {
		scheme,
		user,
		host,
		port,
		path,
		query,
		hash,
		type: 7
	};
}
function parseUrl(input) {
	if (isSchemeRelativeUrl(input)) {
		const url = parseAbsoluteUrl("http:" + input);
		url.scheme = "";
		url.type = 6;
		return url;
	}
	if (isAbsolutePath(input)) {
		const url = parseAbsoluteUrl("http://foo.com" + input);
		url.scheme = "";
		url.host = "";
		url.type = 5;
		return url;
	}
	if (isFileUrl(input)) return parseFileUrl(input);
	if (isAbsoluteUrl(input)) return parseAbsoluteUrl(input);
	const url = parseAbsoluteUrl("http://foo.com/" + input);
	url.scheme = "";
	url.host = "";
	url.type = input ? input.startsWith("?") ? 3 : input.startsWith("#") ? 2 : 4 : 1;
	return url;
}
function stripPathFilename(path) {
	if (path.endsWith("/..")) return path;
	const index = path.lastIndexOf("/");
	return path.slice(0, index + 1);
}
function mergePaths(url, base) {
	normalizePath$1(base, base.type);
	if (url.path === "/") url.path = base.path;
	else url.path = stripPathFilename(base.path) + url.path;
}
/**
* The path can have empty directories "//", unneeded parents "foo/..", or current directory
* "foo/.". We need to normalize to a standard representation.
*/
function normalizePath$1(url, type) {
	const rel = type <= 4;
	const pieces = url.path.split("/");
	let pointer = 1;
	let positive = 0;
	let addTrailingSlash = false;
	for (let i = 1; i < pieces.length; i++) {
		const piece = pieces[i];
		if (!piece) {
			addTrailingSlash = true;
			continue;
		}
		addTrailingSlash = false;
		if (piece === ".") continue;
		if (piece === "..") {
			if (positive) {
				addTrailingSlash = true;
				positive--;
				pointer--;
			} else if (rel) pieces[pointer++] = piece;
			continue;
		}
		pieces[pointer++] = piece;
		positive++;
	}
	let path = "";
	for (let i = 1; i < pointer; i++) path += "/" + pieces[i];
	if (!path || addTrailingSlash && !path.endsWith("/..")) path += "/";
	url.path = path;
}
/**
* Attempts to resolve `input` URL/path relative to `base`.
*/
function resolve(input, base) {
	if (!input && !base) return "";
	const url = parseUrl(input);
	let inputType = url.type;
	if (base && inputType !== 7) {
		const baseUrl = parseUrl(base);
		const baseType = baseUrl.type;
		switch (inputType) {
			case 1: url.hash = baseUrl.hash;
			case 2: url.query = baseUrl.query;
			case 3:
			case 4: mergePaths(url, baseUrl);
			case 5:
				url.user = baseUrl.user;
				url.host = baseUrl.host;
				url.port = baseUrl.port;
			case 6: url.scheme = baseUrl.scheme;
		}
		if (baseType > inputType) inputType = baseType;
	}
	normalizePath$1(url, inputType);
	const queryHash = url.query + url.hash;
	switch (inputType) {
		case 2:
		case 3: return queryHash;
		case 4: {
			const path = url.path.slice(1);
			if (!path) return queryHash || ".";
			if (isRelative(base || input) && !isRelative(path)) return "./" + path + queryHash;
			return path + queryHash;
		}
		case 5: return url.path + queryHash;
		default: return url.scheme + "//" + url.user + url.host + url.port + url.path + queryHash;
	}
}
//#endregion
//#region ../../node_modules/.pnpm/@jridgewell+trace-mapping@0.3.31/node_modules/@jridgewell/trace-mapping/dist/trace-mapping.mjs
function stripFilename(path) {
	if (!path) return "";
	const index = path.lastIndexOf("/");
	return path.slice(0, index + 1);
}
function resolver(mapUrl, sourceRoot) {
	const from = stripFilename(mapUrl);
	const prefix = sourceRoot ? sourceRoot + "/" : "";
	return (source) => resolve(prefix + (source || ""), from);
}
var COLUMN$1 = 0;
function maybeSort(mappings, owned) {
	const unsortedIndex = nextUnsortedSegmentLine(mappings, 0);
	if (unsortedIndex === mappings.length) return mappings;
	if (!owned) mappings = mappings.slice();
	for (let i = unsortedIndex; i < mappings.length; i = nextUnsortedSegmentLine(mappings, i + 1)) mappings[i] = sortSegments(mappings[i], owned);
	return mappings;
}
function nextUnsortedSegmentLine(mappings, start) {
	for (let i = start; i < mappings.length; i++) if (!isSorted(mappings[i])) return i;
	return mappings.length;
}
function isSorted(line) {
	for (let j = 1; j < line.length; j++) if (line[j][COLUMN$1] < line[j - 1][COLUMN$1]) return false;
	return true;
}
function sortSegments(line, owned) {
	if (!owned) line = line.slice();
	return line.sort(sortComparator);
}
function sortComparator(a, b) {
	return a[COLUMN$1] - b[COLUMN$1];
}
function memoizedState() {
	return {
		lastKey: -1,
		lastNeedle: -1,
		lastIndex: -1
	};
}
function parse(map) {
	return typeof map === "string" ? JSON.parse(map) : map;
}
var TraceMap = class {
	constructor(map, mapUrl) {
		const isString = typeof map === "string";
		if (!isString && map._decodedMemo) return map;
		const parsed = parse(map);
		const { version, file, names, sourceRoot, sources, sourcesContent } = parsed;
		this.version = version;
		this.file = file;
		this.names = names || [];
		this.sourceRoot = sourceRoot;
		this.sources = sources;
		this.sourcesContent = sourcesContent;
		this.ignoreList = parsed.ignoreList || parsed.x_google_ignoreList || void 0;
		const resolve = resolver(mapUrl, sourceRoot);
		this.resolvedSources = sources.map(resolve);
		const { mappings } = parsed;
		if (typeof mappings === "string") {
			this._encoded = mappings;
			this._decoded = void 0;
		} else if (Array.isArray(mappings)) {
			this._encoded = void 0;
			this._decoded = maybeSort(mappings, isString);
		} else if (parsed.sections) throw new Error(`TraceMap passed sectioned source map, please use FlattenMap export instead`);
		else throw new Error(`invalid source map: ${JSON.stringify(parsed)}`);
		this._decodedMemo = memoizedState();
		this._bySources = void 0;
		this._bySourceMemos = void 0;
	}
};
function cast$1(map) {
	return map;
}
function decodedMappings(map) {
	var _a;
	return (_a = cast$1(map))._decoded || (_a._decoded = decode(cast$1(map)._encoded));
}
function eachMapping(map, cb) {
	const decoded = decodedMappings(map);
	const { names, resolvedSources } = map;
	for (let i = 0; i < decoded.length; i++) {
		const line = decoded[i];
		for (let j = 0; j < line.length; j++) {
			const seg = line[j];
			const generatedLine = i + 1;
			const generatedColumn = seg[0];
			let source = null;
			let originalLine = null;
			let originalColumn = null;
			let name = null;
			if (seg.length !== 1) {
				source = resolvedSources[seg[1]];
				originalLine = seg[2] + 1;
				originalColumn = seg[3];
			}
			if (seg.length === 5) name = names[seg[4]];
			cb({
				generatedLine,
				generatedColumn,
				source,
				originalLine,
				originalColumn,
				name
			});
		}
	}
}
//#endregion
//#region ../../node_modules/.pnpm/@jridgewell+gen-mapping@0.3.13/node_modules/@jridgewell/gen-mapping/dist/gen-mapping.mjs
var SetArray = class {
	constructor() {
		this._indexes = { __proto__: null };
		this.array = [];
	}
};
function cast(set) {
	return set;
}
function get(setarr, key) {
	return cast(setarr)._indexes[key];
}
function put(setarr, key) {
	const index = get(setarr, key);
	if (index !== void 0) return index;
	const { array, _indexes: indexes } = cast(setarr);
	return indexes[key] = array.push(key) - 1;
}
var COLUMN = 0;
var SOURCES_INDEX = 1;
var SOURCE_LINE = 2;
var SOURCE_COLUMN = 3;
var NAMES_INDEX = 4;
var NO_NAME = -1;
var GenMapping = class {
	constructor({ file, sourceRoot } = {}) {
		this._names = new SetArray();
		this._sources = new SetArray();
		this._sourcesContent = [];
		this._mappings = [];
		this.file = file;
		this.sourceRoot = sourceRoot;
		this._ignoreList = new SetArray();
	}
};
function cast2(map) {
	return map;
}
function addMapping(map, mapping) {
	return addMappingInternal(false, map, mapping);
}
function toDecodedMap(map) {
	const { _mappings: mappings, _sources: sources, _sourcesContent: sourcesContent, _names: names, _ignoreList: ignoreList } = cast2(map);
	removeEmptyFinalLines(mappings);
	return {
		version: 3,
		file: map.file || void 0,
		names: names.array,
		sourceRoot: map.sourceRoot || void 0,
		sources: sources.array,
		sourcesContent,
		mappings,
		ignoreList: ignoreList.array
	};
}
function toEncodedMap(map) {
	const decoded = toDecodedMap(map);
	return Object.assign({}, decoded, { mappings: encode(decoded.mappings) });
}
function fromMap(input) {
	const map = new TraceMap(input);
	const gen = new GenMapping({
		file: map.file,
		sourceRoot: map.sourceRoot
	});
	putAll(cast2(gen)._names, map.names);
	putAll(cast2(gen)._sources, map.sources);
	cast2(gen)._sourcesContent = map.sourcesContent || map.sources.map(() => null);
	cast2(gen)._mappings = decodedMappings(map);
	if (map.ignoreList) putAll(cast2(gen)._ignoreList, map.ignoreList);
	return gen;
}
function addSegmentInternal(skipable, map, genLine, genColumn, source, sourceLine, sourceColumn, name, content) {
	const { _mappings: mappings, _sources: sources, _sourcesContent: sourcesContent, _names: names } = cast2(map);
	const line = getIndex(mappings, genLine);
	const index = getColumnIndex(line, genColumn);
	if (!source) {
		if (skipable && skipSourceless(line, index)) return;
		return insert(line, index, [genColumn]);
	}
	const sourcesIndex = put(sources, source);
	const namesIndex = name ? put(names, name) : NO_NAME;
	if (sourcesIndex === sourcesContent.length) sourcesContent[sourcesIndex] = content != null ? content : null;
	if (skipable && skipSource(line, index, sourcesIndex, sourceLine, sourceColumn, namesIndex)) return;
	return insert(line, index, name ? [
		genColumn,
		sourcesIndex,
		sourceLine,
		sourceColumn,
		namesIndex
	] : [
		genColumn,
		sourcesIndex,
		sourceLine,
		sourceColumn
	]);
}
function getIndex(arr, index) {
	for (let i = arr.length; i <= index; i++) arr[i] = [];
	return arr[index];
}
function getColumnIndex(line, genColumn) {
	let index = line.length;
	for (let i = index - 1; i >= 0; index = i--) if (genColumn >= line[i][COLUMN]) break;
	return index;
}
function insert(array, index, value) {
	for (let i = array.length; i > index; i--) array[i] = array[i - 1];
	array[index] = value;
}
function removeEmptyFinalLines(mappings) {
	const { length } = mappings;
	let len = length;
	for (let i = len - 1; i >= 0; len = i, i--) if (mappings[i].length > 0) break;
	if (len < length) mappings.length = len;
}
function putAll(setarr, array) {
	for (let i = 0; i < array.length; i++) put(setarr, array[i]);
}
function skipSourceless(line, index) {
	if (index === 0) return true;
	return line[index - 1].length === 1;
}
function skipSource(line, index, sourcesIndex, sourceLine, sourceColumn, namesIndex) {
	if (index === 0) return false;
	const prev = line[index - 1];
	if (prev.length === 1) return false;
	return sourcesIndex === prev[SOURCES_INDEX] && sourceLine === prev[SOURCE_LINE] && sourceColumn === prev[SOURCE_COLUMN] && namesIndex === (prev.length === 5 ? prev[NAMES_INDEX] : NO_NAME);
}
function addMappingInternal(skipable, map, mapping) {
	const { generated, source, original, name, content } = mapping;
	if (!source) return addSegmentInternal(skipable, map, generated.line - 1, generated.column, null, null, null, null, null);
	return addSegmentInternal(skipable, map, generated.line - 1, generated.column, source, original.line - 1, original.column, name, content);
}
//#endregion
//#region ../../node_modules/.pnpm/obug@2.1.3/node_modules/obug/dist/core.js
/**
* Coerce `value`.
*/
function coerce(value) {
	if (value instanceof Error) return value.stack || value.message;
	return value;
}
/**
* Selects a color for a debug namespace
* @return An ANSI color code for the given namespace
*/
function selectColor(colors, namespace) {
	let hash = 0;
	for (let i = 0; i < namespace.length; i++) {
		hash = (hash << 5) - hash + namespace.charCodeAt(i);
		hash |= 0;
	}
	return colors[Math.abs(hash) % colors.length];
}
/**
* Checks if the given string matches a namespace template, honoring
* asterisks as wildcards.
*/
function matchesTemplate(search, template) {
	let searchIndex = 0;
	let templateIndex = 0;
	let starIndex = -1;
	let matchIndex = 0;
	while (searchIndex < search.length) if (templateIndex < template.length && (template[templateIndex] === search[searchIndex] || template[templateIndex] === "*")) if (template[templateIndex] === "*") {
		starIndex = templateIndex;
		matchIndex = searchIndex;
		templateIndex++;
	} else {
		searchIndex++;
		templateIndex++;
	}
	else if (starIndex !== -1) {
		templateIndex = starIndex + 1;
		matchIndex++;
		searchIndex = matchIndex;
	} else return false;
	while (templateIndex < template.length && template[templateIndex] === "*") templateIndex++;
	return templateIndex === template.length;
}
function humanize(value) {
	if (value >= 1e3) return `${(value / 1e3).toFixed(1)}s`;
	return `${value}ms`;
}
let globalNamespaces = "";
function createDebug$1(namespace, options) {
	let prevTime;
	let enableOverride;
	let namespacesCache;
	let enabledCache;
	const debug = (...args) => {
		if (!debug.enabled) return;
		const curr = Date.now();
		const diff = curr - (prevTime || curr);
		prevTime = curr;
		args[0] = coerce(args[0]);
		if (typeof args[0] !== "string") args.unshift("%O");
		let index = 0;
		args[0] = args[0].replace(/%([a-z%])/gi, (match, format) => {
			if (match === "%%") return "%";
			index++;
			const formatter = options.formatters[format];
			if (typeof formatter === "function") {
				const value = args[index];
				match = formatter.call(debug, value);
				args.splice(index, 1);
				index--;
			}
			return match;
		});
		options.formatArgs.call(debug, diff, args);
		debug.log(...args);
	};
	debug.extend = function(namespace, delimiter = ":") {
		return createDebug$1(this.namespace + delimiter + namespace, {
			useColors: this.useColors,
			color: this.color,
			formatArgs: this.formatArgs,
			formatters: this.formatters,
			inspectOpts: this.inspectOpts,
			log: this.log,
			humanize: this.humanize
		});
	};
	Object.assign(debug, options);
	debug.namespace = namespace;
	Object.defineProperty(debug, "enabled", {
		enumerable: true,
		configurable: false,
		get: () => {
			if (enableOverride != null) return enableOverride;
			if (namespacesCache !== globalNamespaces) {
				namespacesCache = globalNamespaces;
				enabledCache = enabled(namespace);
			}
			return enabledCache;
		},
		set: (v) => {
			enableOverride = v;
		}
	});
	return debug;
}
let names = [];
let skips = [];
function enable(namespaces) {
	globalNamespaces = namespaces;
	names = [];
	skips = [];
	const split = globalNamespaces.trim().replace(/\s+/g, ",").split(",").filter(Boolean);
	for (const ns of split) if (ns[0] === "-") skips.push(ns.slice(1));
	else names.push(ns);
}
/**
* Returns true if the given mode name is enabled, false otherwise.
*/
function enabled(name) {
	for (const skip of skips) if (matchesTemplate(name, skip)) return false;
	for (const ns of names) if (matchesTemplate(name, ns)) return true;
	return false;
}
//#endregion
//#region ../../node_modules/.pnpm/obug@2.1.3/node_modules/obug/dist/node.js
const colors = process.stderr.getColorDepth && process.stderr.getColorDepth() > 2 ? [
	20,
	21,
	26,
	27,
	32,
	33,
	38,
	39,
	40,
	41,
	42,
	43,
	44,
	45,
	56,
	57,
	62,
	63,
	68,
	69,
	74,
	75,
	76,
	77,
	78,
	79,
	80,
	81,
	92,
	93,
	98,
	99,
	112,
	113,
	128,
	129,
	134,
	135,
	148,
	149,
	160,
	161,
	162,
	163,
	164,
	165,
	166,
	167,
	168,
	169,
	170,
	171,
	172,
	173,
	178,
	179,
	184,
	185,
	196,
	197,
	198,
	199,
	200,
	201,
	202,
	203,
	204,
	205,
	206,
	207,
	208,
	209,
	214,
	215,
	220,
	221
] : [
	6,
	2,
	3,
	4,
	5,
	1
];
const inspectOpts = Object.keys(process.env).filter((key) => /^debug_/i.test(key)).reduce((obj, key) => {
	const prop = key.slice(6).toLowerCase().replace(/_([a-z])/g, (_, k) => k.toUpperCase());
	let value = process.env[key];
	const lowerCase = typeof value === "string" && value.toLowerCase();
	if (value === "null") value = null;
	else if (lowerCase === "yes" || lowerCase === "on" || lowerCase === "true" || lowerCase === "enabled") value = true;
	else if (lowerCase === "no" || lowerCase === "off" || lowerCase === "false" || lowerCase === "disabled") value = false;
	else value = Number(value);
	obj[prop] = value;
	return obj;
}, Object.create(null));
/**
* Is stdout a TTY? Colored output is enabled when `true`.
*/
function useColors() {
	return "colors" in inspectOpts ? Boolean(inspectOpts.colors) : isatty(process.stderr.fd);
}
function getDate() {
	if (inspectOpts.hideDate) return "";
	return `${(/* @__PURE__ */ new Date()).toISOString()} `;
}
/**
* Adds ANSI color escape codes if enabled.
*/
function formatArgs(diff, args) {
	const { namespace: name, useColors } = this;
	if (useColors) {
		const c = this.color;
		const colorCode = `\u001B[3${c < 8 ? c : `8;5;${c}`}`;
		const prefix = `  ${colorCode};1m${name} \u001B[0m`;
		args[0] = prefix + args[0].split("\n").join(`\n${prefix}`);
		args.push(`${colorCode}m+${this.humanize(diff)}\u001B[0m`);
	} else args[0] = `${getDate()}${name} ${args[0]}`;
}
function log(...args) {
	process.stderr.write(`${formatWithOptions(this.inspectOpts, ...args)}\n`);
}
const defaultOptions = {
	useColors: useColors(),
	formatArgs,
	formatters: {
		/**
		* Map %o to `util.inspect()`, all on a single line.
		*/
		o(v) {
			this.inspectOpts.colors = this.useColors;
			return inspect(v, this.inspectOpts).split("\n").map((str) => str.trim()).join(" ");
		},
		/**
		* Map %O to `util.inspect()`, allowing multiple lines if needed.
		*/
		O(v) {
			this.inspectOpts.colors = this.useColors;
			return inspect(v, this.inspectOpts);
		}
	},
	inspectOpts,
	log,
	humanize
};
function createDebug(namespace, options) {
	var _ref;
	const color = (_ref = options && options.color) !== null && _ref !== void 0 ? _ref : selectColor(colors, namespace);
	return createDebug$1(namespace, Object.assign(defaultOptions, { color }, options));
}
enable(process.env.DEBUG || "");
//#endregion
//#region src/handleHotUpdate.ts
const debug = createDebug("vite:hmr");
/**
* Vite-specific HMR handling
*/
async function handleHotUpdate({ file, modules, read }, options, customElement, typeDepModules) {
	const prevDescriptor = getDescriptor(file, options, false, true);
	if (!prevDescriptor) return;
	const { descriptor } = createDescriptor(file, await read(), options, true);
	let needRerender = false;
	const nonJsModules = modules.filter((m) => m.type !== "js");
	const jsModules = modules.filter((m) => m.type === "js");
	const affectedModules = new Set(nonJsModules);
	const mainModule = getMainModule(jsModules);
	const templateModule = jsModules.find((m) => /type=template/.test(m.url));
	resolveScript(descriptor, options, false, customElement);
	const scriptChanged = hasScriptChanged(prevDescriptor, descriptor);
	if (scriptChanged) affectedModules.add(getScriptModule(jsModules) || mainModule);
	if (!isEqualBlock(descriptor.template, prevDescriptor.template)) {
		if (!scriptChanged) setResolvedScript(descriptor, getResolvedScript(prevDescriptor, false), false);
		affectedModules.add(templateModule);
		needRerender = true;
	}
	let didUpdateStyle = false;
	const prevStyles = prevDescriptor.styles || [];
	const nextStyles = descriptor.styles || [];
	if (prevDescriptor.cssVars.join("") !== descriptor.cssVars.join("")) affectedModules.add(mainModule);
	if (prevStyles.some((s) => s.scoped) !== nextStyles.some((s) => s.scoped)) {
		affectedModules.add(templateModule);
		affectedModules.add(mainModule);
	}
	for (let i = 0; i < nextStyles.length; i++) {
		const prev = prevStyles[i];
		const next = nextStyles[i];
		if (!prev || !isEqualBlock(prev, next)) {
			didUpdateStyle = true;
			const mod = jsModules.find((m) => m.url.includes(`type=style&index=${i}`) && m.url.endsWith(`.${next.lang || "css"}`));
			if (mod) {
				affectedModules.add(mod);
				if (mod.url.includes("&inline")) affectedModules.add(mainModule);
			} else affectedModules.add(mainModule);
		}
	}
	if (prevStyles.length > nextStyles.length) affectedModules.add(mainModule);
	const prevCustoms = prevDescriptor.customBlocks || [];
	const nextCustoms = descriptor.customBlocks || [];
	if (prevCustoms.length !== nextCustoms.length) affectedModules.add(mainModule);
	else for (let i = 0; i < nextCustoms.length; i++) {
		const prev = prevCustoms[i];
		const next = nextCustoms[i];
		if (!prev || !isEqualBlock(prev, next)) {
			const mod = jsModules.find((m) => m.url.includes(`type=${prev.type}&index=${i}`));
			if (mod) affectedModules.add(mod);
			else affectedModules.add(mainModule);
		}
	}
	const updateType = [];
	if (needRerender) {
		updateType.push(`template`);
		if (!templateModule) affectedModules.add(mainModule);
		else if (mainModule && !affectedModules.has(mainModule)) [...mainModule.importers].filter((m) => isCSSRequest(m.url)).forEach((m) => affectedModules.add(m));
	}
	if (didUpdateStyle) updateType.push(`style`);
	if (updateType.length) {
		if (file.endsWith(".vue")) invalidateDescriptor(file);
		else cache.set(file, descriptor);
		debug(`[vue:update(${updateType.join("&")})] ${file}`);
	}
	return [...affectedModules, ...typeDepModules || []].filter(Boolean);
}
function isEqualBlock(a, b) {
	if (!a && !b) return true;
	if (!a || !b) return false;
	if (a.src && b.src && a.src === b.src) return true;
	if (a.content !== b.content) return false;
	const keysA = Object.keys(a.attrs);
	const keysB = Object.keys(b.attrs);
	if (keysA.length !== keysB.length) return false;
	return keysA.every((key) => a.attrs[key] === b.attrs[key]);
}
function isOnlyTemplateChanged(prev, next) {
	return !hasScriptChanged(prev, next) && prev.styles.length === next.styles.length && prev.styles.every((s, i) => isEqualBlock(s, next.styles[i])) && prev.customBlocks.length === next.customBlocks.length && prev.customBlocks.every((s, i) => isEqualBlock(s, next.customBlocks[i]));
}
function deepEqual(obj1, obj2, excludeProps = [], deepParentsOfObj1 = []) {
	if (typeof obj1 !== typeof obj2) return false;
	if (obj1 == null || obj2 == null || typeof obj1 !== "object" || deepParentsOfObj1.includes(obj1)) return obj1 === obj2;
	const keys1 = Object.keys(obj1);
	const keys2 = Object.keys(obj2);
	if (keys1.length !== keys2.length) return false;
	for (const key of keys1) {
		if (excludeProps.includes(key)) continue;
		if (!deepEqual(obj1[key], obj2[key], excludeProps, [...deepParentsOfObj1, obj1])) return false;
	}
	return true;
}
function isEqualAst(prev, next) {
	if (typeof prev === "undefined" || typeof next === "undefined") return prev === next;
	if (prev.length !== next.length) return false;
	for (let i = 0; i < prev.length; i++) {
		const prevNode = prev[i];
		const nextNode = next[i];
		if (!deepEqual(prevNode, nextNode, [
			"start",
			"end",
			"loc",
			"range",
			"leadingComments",
			"trailingComments",
			"innerComments",
			"_ownerScope",
			"_resolvedReference",
			"_resolvedElements"
		])) return false;
	}
	return true;
}
function hasScriptChanged(prev, next) {
	if (prev.vapor !== next.vapor) return true;
	const prevScript = getResolvedScript(prev, false);
	const nextScript = getResolvedScript(next, false);
	if (!isEqualBlock(prev.script, next.script) && !isEqualAst(prevScript?.scriptAst, nextScript?.scriptAst)) return true;
	if (!isEqualBlock(prev.scriptSetup, next.scriptSetup) && !isEqualAst(prevScript?.scriptSetupAst, nextScript?.scriptSetupAst)) return true;
	const prevImports = getResolvedScript(prev, false)?.imports;
	if (prevImports) return !next.template || next.shouldForceReload(prevImports);
	return false;
}
function getMainModule(jsModules) {
	return jsModules.filter((m) => !/type=/.test(m.url) || /type=script/.test(m.url)).sort((m1, m2) => {
		return m1.url.length - m2.url.length;
	})[0];
}
function getScriptModule(jsModules) {
	return jsModules.find((m) => /type=script.*&lang\.\w+$/.test(m.url));
}
function handleTypeDepChange(affectedComponents, { modules, server: { moduleGraph } }) {
	const affected = /* @__PURE__ */ new Set();
	for (const file of affectedComponents) {
		invalidateScript(file);
		const mods = moduleGraph.getModulesByFile(file);
		if (mods) {
			const arr = [...mods];
			affected.add(getScriptModule(arr) || getMainModule(arr));
		}
	}
	return [...modules, ...affected];
}
//#endregion
//#region src/helper.ts
const EXPORT_HELPER_ID = "\0plugin-vue:export-helper";
const helperCode = `
export default (sfc, props) => {
  const target = sfc.__vccOpts || sfc;
  for (const [key, val] of props) {
    target[key] = val;
  }
  return target;
}
`;
//#endregion
//#region src/main.ts
async function transformMain(code, filename, options, pluginContext, ssr, customElement) {
	const { devServer, isProduction, devToolsEnabled } = options;
	const prevDescriptor = getPrevDescriptor(filename);
	const { descriptor, errors } = createDescriptor(filename, code, options);
	if (fs.existsSync(filename)) getDescriptor(filename, options, true, true, filename.endsWith(".vue") ? void 0 : code);
	if (errors.length) {
		errors.forEach((error) => pluginContext.error(createRollupError(filename, error)));
		return null;
	}
	const attachedProps = [];
	const hasScoped = descriptor.styles.some((s) => s.scoped);
	const isTemplateOnlyVapor = !descriptor.script && !descriptor.scriptSetup && isVaporMode(descriptor, options);
	const { code: scriptCode, map: scriptMap } = await genScriptCode(descriptor, options, pluginContext, ssr, customElement);
	const hasTemplateImport = descriptor.template && !isUseInlineTemplate(descriptor, options);
	const isTemplateInlined = !!descriptor.template && (!descriptor.template.lang || descriptor.template.lang === "html") && !descriptor.template.src;
	let templateCode = "";
	let templateMap = void 0;
	let templateMultiRoot;
	if (hasTemplateImport) ({code: templateCode, map: templateMap, multiRoot: templateMultiRoot} = await genTemplateCode(descriptor, options, pluginContext, ssr, customElement));
	if (hasTemplateImport) attachedProps.push(ssr ? ["ssrRender", "_sfc_ssrRender"] : ["render", "_sfc_render"]);
	else if (prevDescriptor && !isEqualBlock(descriptor.template, prevDescriptor.template)) attachedProps.push([ssr ? "ssrRender" : "render", "() => {}"]);
	const stylesCode = await genStyleCode(descriptor, pluginContext, customElement, attachedProps);
	const customBlocksCode = await genCustomBlockCode(descriptor, pluginContext);
	const output = [
		scriptCode,
		templateCode,
		isTemplateOnlyVapor ? `${scriptIdentifier}.__multiRoot = ${isTemplateInlined ? templateMultiRoot : "_sfc_multiRoot"}` : "",
		stylesCode,
		customBlocksCode
	];
	if (hasScoped) attachedProps.push([`__scopeId`, JSON.stringify(`data-v-${descriptor.id}`)]);
	if (devToolsEnabled || devServer && !isProduction) attachedProps.push([`__file`, JSON.stringify(isProduction ? path.basename(filename) : filename)]);
	if (devServer && devServer.config.server.hmr !== false && !ssr && !isProduction) {
		output.push(`_sfc_main.__hmrId = ${JSON.stringify(descriptor.id)}`);
		output.push("typeof __VUE_HMR_RUNTIME__ !== 'undefined' && __VUE_HMR_RUNTIME__.createRecord(_sfc_main.__hmrId, _sfc_main)");
		output.push(`import.meta.hot.on('file-changed', ({ file }) => {`, `  __VUE_HMR_RUNTIME__.CHANGED_FILE = file`, `})`);
		if (prevDescriptor && isOnlyTemplateChanged(prevDescriptor, descriptor)) output.push(`export const _rerender_only = __VUE_HMR_RUNTIME__.CHANGED_FILE === ${JSON.stringify(normalizePath(filename))}`);
		output.push(`import.meta.hot.accept(mod => {`, `  if (!mod) return`, `  const { default: updated, _rerender_only } = mod`, `  if (_rerender_only) {`, `    __VUE_HMR_RUNTIME__.rerender(updated.__hmrId, updated.render)`, `  } else {`, `    __VUE_HMR_RUNTIME__.reload(updated.__hmrId, updated)`, `  }`, `})`);
	}
	if (ssr) {
		const normalizedFilename = normalizePath(path.relative(options.root, filename));
		output.push(`import { useSSRContext as __vite_useSSRContext } from 'vue'`, `const _sfc_setup = _sfc_main.setup`, `_sfc_main.setup = (props, ctx) => {`, `  const ssrContext = __vite_useSSRContext()`, `  ;(ssrContext.modules || (ssrContext.modules = new Set())).add(${JSON.stringify(normalizedFilename)})`, `  return _sfc_setup ? _sfc_setup(props, ctx) : undefined`, `}`);
	}
	let resolvedMap = void 0;
	if (options.sourceMap) if (templateMap) {
		const gen = fromMap(scriptMap ?? {
			file: filename,
			sourceRoot: "",
			version: 3,
			sources: [],
			sourcesContent: [],
			names: [],
			mappings: ""
		});
		const tracer = new TraceMap(templateMap);
		const offset = (scriptCode.match(/\r?\n/g)?.length ?? 0) + 1;
		eachMapping(tracer, (m) => {
			if (m.source == null) return;
			addMapping(gen, {
				source: m.source,
				original: {
					line: m.originalLine,
					column: m.originalColumn
				},
				generated: {
					line: m.generatedLine + offset,
					column: m.generatedColumn
				}
			});
		});
		resolvedMap = toEncodedMap(gen);
		resolvedMap.sourcesContent = templateMap.sourcesContent;
	} else resolvedMap = scriptMap;
	if (!attachedProps.length) output.push(`export default _sfc_main`);
	else output.push(`import _export_sfc from '${EXPORT_HELPER_ID}'`, `export default /*#__PURE__*/_export_sfc(_sfc_main, [${attachedProps.map(([key, val]) => `['${key}',${val}]`).join(",")}])`);
	let resolvedCode = output.join("\n");
	const lang = descriptor.scriptSetup?.lang || descriptor.script?.lang;
	if (lang && /tsx?$/.test(lang) && !descriptor.script?.src) {
		const { transformWithOxc } = await import("vite");
		if (transformWithOxc) {
			const { code, map } = await transformWithOxc(resolvedCode, filename, {
				...options.devServer?.config.oxc,
				lang: "ts",
				sourcemap: options.sourceMap
			}, resolvedMap);
			resolvedCode = code;
			resolvedMap = resolvedMap ? map : resolvedMap;
		} else {
			const { code, map } = await transformWithEsbuild(resolvedCode, filename, {
				target: "esnext",
				charset: "utf8",
				...options.devServer?.config.esbuild,
				loader: "ts",
				sourcemap: options.sourceMap
			}, resolvedMap);
			resolvedCode = code;
			resolvedMap = resolvedMap ? map : resolvedMap;
		}
	}
	return {
		code: resolvedCode,
		map: resolvedMap || { mappings: "" },
		meta: { vite: { lang: descriptor.script?.lang || descriptor.scriptSetup?.lang || "js" } }
	};
}
async function genTemplateCode(descriptor, options, pluginContext, ssr, customElement) {
	const template = descriptor.template;
	const hasScoped = descriptor.styles.some((style) => style.scoped);
	const needsMultiRoot = !descriptor.script && !descriptor.scriptSetup && isVaporMode(descriptor, options);
	if ((!template.lang || template.lang === "html") && !template.src) {
		const result = transformTemplateInMain(template.content, descriptor, options, pluginContext, ssr, customElement);
		return {
			...result,
			multiRoot: needsMultiRoot ? result.multiRoot : void 0
		};
	} else {
		if (template.src) await linkSrcToDescriptor(template.src, descriptor, pluginContext, hasScoped);
		const src = template.src || descriptor.filename;
		const query = `?vue&type=template${template.src ? hasScoped ? `&src=${descriptor.id}` : "&src=true" : ""}${hasScoped ? `&scoped=${descriptor.id}` : ``}${attrsToQuery(template.attrs, "js", true)}`;
		const request = JSON.stringify(src + query);
		const renderFnName = ssr ? "ssrRender" : "render";
		return {
			code: `import { ${renderFnName} as _sfc_${renderFnName}${needsMultiRoot ? ", multiRoot as _sfc_multiRoot" : ""} } from ${request}`,
			map: void 0
		};
	}
}
async function genScriptCode(descriptor, options, pluginContext, ssr, customElement) {
	let scriptCode = `const ${scriptIdentifier} = { ${isVaporMode(descriptor, options) ? "__vapor: true" : ""} }`;
	let map;
	const script = resolveScript(descriptor, options, ssr, customElement);
	if (script) if (canInlineMain(descriptor, options)) {
		if (!options.compiler.version) {
			const userPlugins = options.script?.babelParserPlugins || [];
			const defaultPlugins = script.lang === "ts" ? userPlugins.includes("decorators") ? ["typescript"] : ["typescript", "decorators-legacy"] : [];
			scriptCode = options.compiler.rewriteDefault(script.content, scriptIdentifier, [...defaultPlugins, ...userPlugins]);
		} else scriptCode = script.content;
		map = script.map;
	} else {
		if (script.src) await linkSrcToDescriptor(script.src, descriptor, pluginContext, false);
		const src = script.src || descriptor.filename;
		const langFallback = script.src && path.extname(src).slice(1) || "js";
		const attrsQuery = attrsToQuery(script.attrs, langFallback);
		const query = `?vue&type=script${script.src ? `&src=true` : ``}${attrsQuery}`;
		const request = JSON.stringify(src + query);
		scriptCode = `import _sfc_main from ${request}\nexport * from ${request}`;
	}
	return {
		code: scriptCode,
		map
	};
}
async function genStyleCode(descriptor, pluginContext, customElement, attachedProps) {
	let stylesCode = ``;
	let cssModulesMap;
	if (descriptor.styles.length) {
		for (let i = 0; i < descriptor.styles.length; i++) {
			const style = descriptor.styles[i];
			if (style.src) await linkSrcToDescriptor(style.src, descriptor, pluginContext, style.scoped);
			const src = style.src || descriptor.filename;
			const attrsQuery = attrsToQuery(style.attrs, "css");
			const srcQuery = style.src ? style.scoped ? `&src=${descriptor.id}` : "&src=true" : "";
			const directQuery = customElement ? `&inline` : ``;
			const scopedQuery = style.scoped ? `&scoped=${descriptor.id}` : ``;
			const styleRequest = src + `?vue&type=style&index=${i}${srcQuery}${directQuery}${scopedQuery}` + attrsQuery;
			if (style.module) {
				if (customElement) throw new Error(`<style module> is not supported in custom elements mode.`);
				const [importCode, nameMap] = genCSSModulesCode(i, styleRequest, style.module);
				stylesCode += importCode;
				Object.assign(cssModulesMap ||= {}, nameMap);
			} else if (customElement) stylesCode += `\nimport _style_${i} from ${JSON.stringify(styleRequest)}`;
			else stylesCode += `\nimport ${JSON.stringify(styleRequest)}`;
		}
		if (customElement) attachedProps.push([`styles`, `[${descriptor.styles.map((_, i) => `_style_${i}`).join(",")}]`]);
	}
	if (cssModulesMap) {
		const mappingCode = Object.entries(cssModulesMap).reduce((code, [key, value]) => code + `"${key}":${value},\n`, "{\n") + "}";
		stylesCode += `\nconst cssModules = ${mappingCode}`;
		attachedProps.push([`__cssModules`, `cssModules`]);
	}
	return stylesCode;
}
function genCSSModulesCode(index, request, moduleName) {
	const styleVar = `style${index}`;
	const exposedName = typeof moduleName === "string" ? moduleName : "$style";
	const moduleRequest = request.replace(/\.(\w+)$/, ".module.$1");
	return [`\nimport ${styleVar} from ${JSON.stringify(moduleRequest)}`, { [exposedName]: styleVar }];
}
async function genCustomBlockCode(descriptor, pluginContext) {
	let code = "";
	for (let index = 0; index < descriptor.customBlocks.length; index++) {
		const block = descriptor.customBlocks[index];
		if (block.src) await linkSrcToDescriptor(block.src, descriptor, pluginContext, false);
		const src = block.src || descriptor.filename;
		const attrsQuery = attrsToQuery(block.attrs, block.type);
		const srcQuery = block.src ? `&src=true` : ``;
		const query = `?vue&type=${block.type}&index=${index}${srcQuery}${attrsQuery}`;
		const request = JSON.stringify(src + query);
		code += `import block${index} from ${request}\n`;
		code += `if (typeof block${index} === 'function') block${index}(_sfc_main)\n`;
	}
	return code;
}
/**
* For blocks with src imports, it is important to link the imported file
* with its owner SFC descriptor so that we can get the information about
* the owner SFC when compiling that file in the transform phase.
*/
async function linkSrcToDescriptor(src, descriptor, pluginContext, scoped) {
	setSrcDescriptor(((await pluginContext.resolve(src, descriptor.filename))?.id || src).replace(/\?.*$/, ""), descriptor, scoped);
}
const ignoreList = [
	"id",
	"index",
	"src",
	"type",
	"lang",
	"module",
	"scoped",
	"generic"
];
function attrsToQuery(attrs, langFallback, forceLangFallback = false) {
	let query = ``;
	for (const name in attrs) {
		const value = attrs[name];
		if (!ignoreList.includes(name)) query += `&${encodeURIComponent(name)}${value ? `=${encodeURIComponent(value)}` : ``}`;
	}
	if (langFallback || attrs.lang) query += `lang` in attrs ? forceLangFallback ? `&lang.${langFallback}` : `&lang.${attrs.lang}` : `&lang.${langFallback}`;
	return query;
}
//#endregion
//#region src/style.ts
async function transformStyle(code, descriptor, index, options, pluginContext, filename) {
	const block = descriptor.styles[index];
	const result = await options.compiler.compileStyleAsync({
		...options.style,
		filename: descriptor.filename,
		id: `data-v-${descriptor.id}`,
		isProd: options.isProduction,
		source: code,
		scoped: block.scoped,
		...options.cssDevSourcemap ? { postcssOptions: { map: {
			from: filename,
			inline: false,
			annotation: false
		} } } : {}
	});
	if (result.errors.length) {
		result.errors.forEach((error) => {
			if (error.line && error.column) error.loc = {
				file: descriptor.filename,
				line: error.line + block.loc.start.line,
				column: error.column
			};
			pluginContext.error(error);
		});
		return null;
	}
	const map = result.map ? await formatPostcssSourceMap(result.map, filename) : { mappings: "" };
	return {
		code: result.code,
		map,
		meta: block.scoped && !descriptor.isTemp ? { vite: { cssScopeTo: [descriptor.filename, "default"] } } : void 0
	};
}
//#endregion
//#region src/index.ts
function vuePlugin(rawOptions = {}) {
	clearScriptCache();
	const options = shallowRef({
		isProduction: process.env.NODE_ENV === "production",
		compiler: null,
		customElement: /\.ce\.vue$/,
		...rawOptions,
		root: process.cwd(),
		sourceMap: true,
		cssDevSourcemap: false
	});
	const include = shallowRef(rawOptions.include ?? /\.vue$/);
	const exclude = shallowRef(rawOptions.exclude);
	let optionsHookIsCalled = false;
	const filter = computed(() => createFilter(include.value, exclude.value));
	const customElementFilter = computed(() => {
		const customElement = options.value.features?.customElement || options.value.customElement;
		return typeof customElement === "boolean" ? () => customElement : createFilter(customElement);
	});
	let transformCachedModule = false;
	const plugin = {
		name: "vite:vue",
		api: {
			get options() {
				return options.value;
			},
			set options(value) {
				options.value = value;
			},
			get include() {
				return include.value;
			},
			set include(value) {
				if (optionsHookIsCalled) throw new Error("include cannot be updated after `options` hook is called");
				include.value = value;
			},
			get exclude() {
				return exclude.value;
			},
			set exclude(value) {
				if (optionsHookIsCalled) throw new Error("exclude cannot be updated after `options` hook is called");
				exclude.value = value;
			},
			version
		},
		handleHotUpdate(ctx) {
			ctx.server.ws.send({
				type: "custom",
				event: "file-changed",
				data: { file: normalizePath(ctx.file) }
			});
			if (options.value.compiler.invalidateTypeCache) options.value.compiler.invalidateTypeCache(ctx.file);
			let typeDepModules;
			const matchesFilter = filter.value(ctx.file);
			if (typeDepToSFCMap.has(ctx.file)) {
				typeDepModules = handleTypeDepChange(typeDepToSFCMap.get(ctx.file), ctx);
				if (!matchesFilter) return typeDepModules;
			}
			if (matchesFilter) return handleHotUpdate(ctx, options.value, customElementFilter.value(ctx.file), typeDepModules);
		},
		config(config) {
			const parseDefine = (v) => {
				try {
					return typeof v === "string" ? JSON.parse(v) : v;
				} catch (err) {
					return v;
				}
			};
			return {
				resolve: { dedupe: config.build?.ssr ? [] : ["vue"] },
				define: {
					__VUE_OPTIONS_API__: options.value.features?.optionsAPI ?? parseDefine(config.define?.__VUE_OPTIONS_API__) ?? true,
					__VUE_PROD_DEVTOOLS__: (options.value.features?.prodDevtools || parseDefine(config.define?.__VUE_PROD_DEVTOOLS__)) ?? false,
					__VUE_PROD_HYDRATION_MISMATCH_DETAILS__: (options.value.features?.prodHydrationMismatchDetails || parseDefine(config.define?.__VUE_PROD_HYDRATION_MISMATCH_DETAILS__)) ?? false
				},
				ssr: { external: config.legacy?.buildSsrCjsExternalHeuristics ? ["vue", "@vue/server-renderer"] : [] }
			};
		},
		configResolved(config) {
			options.value = {
				...options.value,
				root: config.root,
				sourceMap: config.command === "build" ? !!config.build.sourcemap : true,
				cssDevSourcemap: config.css?.devSourcemap ?? false,
				isProduction: config.isProduction,
				devToolsEnabled: !!(options.value.features?.prodDevtools || config.define.__VUE_PROD_DEVTOOLS__ || !config.isProduction)
			};
			const _warn = config.logger.warn;
			config.logger.warn = (...args) => {
				if (args[0].match(/\[lightningcss\] '(deep|slotted|global)' is not recognized as a valid pseudo-/)) return;
				_warn(...args);
			};
			transformCachedModule = config.command === "build" && options.value.sourceMap && config.build.watch != null;
		},
		options() {
			optionsHookIsCalled = true;
			plugin.transform.filter = { id: {
				include: [...makeIdFiltersToMatchWithQuery(ensureArray(include.value)), /[?&]vue\b/],
				exclude: exclude.value
			} };
		},
		shouldTransformCachedModule({ id }) {
			if (transformCachedModule && parseVueRequest(id).query.vue) return true;
			return false;
		},
		configureServer(server) {
			options.value.devServer = server;
		},
		buildStart() {
			const compiler = options.value.compiler = options.value.compiler || resolveCompiler(options.value.root);
			if (compiler.invalidateTypeCache) options.value.devServer?.watcher.on("unlink", (file) => {
				compiler.invalidateTypeCache(file);
			});
		},
		resolveId: {
			filter: { id: [exactRegex(EXPORT_HELPER_ID), /[?&]vue\b/] },
			handler(id) {
				if (id === "\0plugin-vue:export-helper") return id;
				if (parseVueRequest(id).query.vue) return id;
			}
		},
		load: {
			filter: { id: [exactRegex(EXPORT_HELPER_ID), /[?&]vue\b/] },
			handler(id, opt) {
				if (id === "\0plugin-vue:export-helper") return helperCode;
				const ssr = opt?.ssr === true;
				const { filename, query } = parseVueRequest(id);
				if (query.vue) {
					if (query.src) return fs.readFileSync(filename, "utf-8");
					const descriptor = getDescriptor(filename, options.value);
					let block;
					if (query.type === "script") block = resolveScript(descriptor, options.value, ssr, customElementFilter.value(filename));
					else if (query.type === "template") block = descriptor.template;
					else if (query.type === "style") block = descriptor.styles[query.index];
					else if (query.index != null) block = descriptor.customBlocks[query.index];
					if (block) return {
						code: block.content,
						map: block.map
					};
				}
			}
		},
		transform: { handler(code, id, opt) {
			const ssr = opt?.ssr === true;
			const { filename, query } = parseVueRequest(id);
			if (query.raw || query.url) return;
			if (!filter.value(filename) && !query.vue) return;
			if (!query.vue) return transformMain(code, filename, options.value, this, ssr, customElementFilter.value(filename));
			else {
				const descriptor = query.src ? getSrcDescriptor(filename, query) || getTempSrcDescriptor(filename, query) : getDescriptor(filename, options.value);
				if (query.src) this.addWatchFile(filename);
				if (query.type === "template") return transformTemplateAsModule(code, filename, descriptor, options.value, this, ssr, customElementFilter.value(filename));
				else if (query.type === "style") return transformStyle(code, descriptor, Number(query.index || 0), options.value, this, filename);
			}
		} }
	};
	return plugin;
}
function ensureArray(value) {
	return Array.isArray(value) ? value : [value];
}
function vuePluginCjs(options) {
	return vuePlugin.call(this, options);
}
Object.assign(vuePluginCjs, {
	default: vuePluginCjs,
	parseVueRequest
});
//#endregion
export { vuePlugin as default, vuePluginCjs as "module.exports", parseVueRequest };
