import fs, { lstatSync, realpathSync, statSync } from "node:fs";
import { URL as URL$1, fileURLToPath, pathToFileURL } from "node:url";
import path, { isAbsolute } from "node:path";
import assert from "node:assert";
import process$1 from "node:process";
import { format, inspect } from "node:util";
const nodeBuiltins = [
	"_http_agent",
	"_http_client",
	"_http_common",
	"_http_incoming",
	"_http_outgoing",
	"_http_server",
	"_stream_duplex",
	"_stream_passthrough",
	"_stream_readable",
	"_stream_transform",
	"_stream_wrap",
	"_stream_writable",
	"_tls_common",
	"_tls_wrap",
	"assert",
	"assert/strict",
	"async_hooks",
	"buffer",
	"child_process",
	"cluster",
	"console",
	"constants",
	"crypto",
	"dgram",
	"diagnostics_channel",
	"dns",
	"dns/promises",
	"domain",
	"events",
	"fs",
	"fs/promises",
	"http",
	"http2",
	"https",
	"inspector",
	"inspector/promises",
	"module",
	"net",
	"os",
	"path",
	"path/posix",
	"path/win32",
	"perf_hooks",
	"process",
	"punycode",
	"querystring",
	"readline",
	"readline/promises",
	"repl",
	"stream",
	"stream/consumers",
	"stream/promises",
	"stream/web",
	"string_decoder",
	"sys",
	"timers",
	"timers/promises",
	"tls",
	"trace_events",
	"tty",
	"url",
	"util",
	"util/types",
	"v8",
	"vm",
	"wasi",
	"worker_threads",
	"zlib"
];
const classRegExp = /^([A-Z][a-z\d]*)+$/;
const kTypes = /* @__PURE__ */ new Set([
	"string",
	"function",
	"number",
	"object",
	"Function",
	"Object",
	"boolean",
	"bigint",
	"symbol"
]);
const messages = /* @__PURE__ */ new Map();
function formatList(array, type = "and") {
	switch (array.length) {
		case 0: return "";
		case 1: return `${array[0]}`;
		case 2: return `${array[0]} ${type} ${array[1]}`;
		case 3: return `${array[0]}, ${array[1]}, ${type} ${array[2]}`;
		default: return `${array.slice(0, -1).join(", ")}, ${type} ${array.at(-1)}`;
	}
}
function getExpectedArgumentLength(message) {
	let expectedLength = 0;
	const regex = /%[dfijoOs]/g;
	while (regex.exec(message) !== null) expectedLength++;
	return expectedLength;
}
function createError(sym, value, constructor) {
	messages.set(sym, value);
	return makeNodeErrorWithCode(constructor, sym);
}
const kIsNodeError = Symbol("kIsNodeError");
function makeNodeErrorWithCode(Base, key) {
	const message = messages.get(key);
	const expectedLength = typeof message === "string" ? getExpectedArgumentLength(message) : -1;
	switch (expectedLength) {
		case 0: {
			class NodeError extends Base {
				code = key;
				constructor(...args) {
					assert.ok(args.length === 0, `Code: ${key}; The provided arguments length (${args.length}) does not match the required ones (${expectedLength}).`);
					super(message);
				}
				get ["constructor"]() {
					return Base;
				}
				get [kIsNodeError]() {
					return true;
				}
				toString() {
					return `${this.name} [${key}]: ${this.message}`;
				}
			}
			return NodeError;
		}
		case -1: {
			class NodeError extends Base {
				code = key;
				constructor(...args) {
					super();
					Object.defineProperty(this, "message", {
						value: getMessage(key, args, this),
						enumerable: false,
						writable: true,
						configurable: true
					});
				}
				get ["constructor"]() {
					return Base;
				}
				get [kIsNodeError]() {
					return true;
				}
				toString() {
					return `${this.name} [${key}]: ${this.message}`;
				}
			}
			return NodeError;
		}
		default: {
			class NodeError extends Base {
				code = key;
				constructor(...args) {
					assert.ok(args.length === expectedLength, `Code: ${key}; The provided arguments length (${args.length}) does not match the required ones (${expectedLength}).`);
					args.unshift(message);
					super(Reflect.apply(format, null, args));
				}
				get ["constructor"]() {
					return Base;
				}
				get [kIsNodeError]() {
					return true;
				}
				toString() {
					return `${this.name} [${key}]: ${this.message}`;
				}
			}
			return NodeError;
		}
	}
}
function getMessage(key, parameters, self) {
	const message = messages.get(key);
	assert.ok(message !== void 0, "expected `message` to be found");
	if (typeof message === "function") {
		assert.ok(message.length <= parameters.length, `Code: ${key}; The provided arguments length (${parameters.length}) does not match the required ones (${message.length}).`);
		return Reflect.apply(message, self, parameters);
	}
	const expectedLength = getExpectedArgumentLength(message);
	assert.ok(expectedLength === parameters.length, `Code: ${key}; The provided arguments length (${parameters.length}) does not match the required ones (${expectedLength}).`);
	if (parameters.length === 0) return message;
	parameters.unshift(message);
	return Reflect.apply(format, null, parameters);
}
function determineSpecificType(value) {
	if (value === null) return "null";
	else if (value === void 0) return "undefined";
	const type = typeof value;
	switch (type) {
		case "bigint": return `type bigint (${value}n)`;
		case "number":
			if (value === 0) return 1 / value === Number.NEGATIVE_INFINITY ? "type number (-0)" : "type number (0)";
			else if (Number.isNaN(value)) return "type number (NaN)";
			else if (value === Number.POSITIVE_INFINITY) return "type number (Infinity)";
			else if (value === Number.NEGATIVE_INFINITY) return "type number (-Infinity)";
			return `type number (${value})`;
		case "boolean": return value ? "type boolean (true)" : "type boolean (false)";
		case "symbol": return `type symbol (${String(value)})`;
		case "function": return `function ${value.name}`;
		case "object":
			if (value.constructor && value.constructor.name) return `an instance of ${value.constructor.name}`;
			return `${inspect(value, { depth: -1 })}`;
		case "string": {
			let string = value;
			if (string.length > 28) string = `${string.slice(0, 25)}...`;
			if (!string.includes("'")) return `type string ('${string}')`;
			return `type string (${JSON.stringify(string)})`;
		}
		default: {
			let inspected = inspect(value, { colors: false });
			if (inspected.length > 28) inspected = `${inspected.slice(0, 25)}...`;
			return `type ${type} (${inspected})`;
		}
	}
}
createError("ERR_INVALID_ARG_TYPE", (name, expected, actual) => {
	assert.ok(typeof name === "string", "'name' must be a string");
	if (!Array.isArray(expected)) expected = [expected];
	let message = "The ";
	if (name.endsWith(" argument")) message += `${name} `;
	else {
		const type = name.includes(".") ? "property" : "argument";
		message += `"${name}" ${type} `;
	}
	message += "must be ";
	const types = [];
	const instances = [];
	const other = [];
	for (const value of expected) {
		assert.ok(typeof value === "string", "All expected entries have to be of type string");
		if (kTypes.has(value)) types.push(value.toLowerCase());
		else if (classRegExp.exec(value) === null) {
			assert.ok(value !== "object", "The value \"object\" should be written as \"Object\"");
			other.push(value);
		} else instances.push(value);
	}
	if (instances.length > 0) {
		const pos = types.indexOf("object");
		if (pos !== -1) {
			types.splice(pos, 1);
			instances.push("Object");
		}
	}
	if (types.length > 0) {
		message += `${types.length > 1 ? "one of type" : "of type"} ${formatList(types, "or")}`;
		if (instances.length > 0 || other.length > 0) message += " or ";
	}
	if (instances.length > 0) {
		message += `an instance of ${formatList(instances, "or")}`;
		if (other.length > 0) message += " or ";
	}
	if (other.length > 0) if (other.length > 1) message += `one of ${formatList(other, "or")}`;
	else {
		if (other[0]?.toLowerCase() !== other[0]) message += "an ";
		message += `${other[0]}`;
	}
	message += `. Received ${determineSpecificType(actual)}`;
	return message;
}, TypeError);
const ERR_INVALID_MODULE_SPECIFIER = createError("ERR_INVALID_MODULE_SPECIFIER", (request, reason, base) => {
	return `Invalid module "${request}" ${reason}${base ? ` imported from ${base}` : ""}`;
}, TypeError);
const ERR_INVALID_PACKAGE_CONFIG = createError("ERR_INVALID_PACKAGE_CONFIG", (path, base, message) => {
	return `Invalid package config ${path}${base ? ` while importing ${base}` : ""}${message ? `. ${message}` : ""}`;
}, Error);
const ERR_INVALID_PACKAGE_TARGET = createError("ERR_INVALID_PACKAGE_TARGET", (packagePath, key, target, isImport = false, base) => {
	const relatedError = typeof target === "string" && !isImport && target.length > 0 && !target.startsWith("./");
	if (key === ".") {
		assert.ok(isImport === false);
		return `Invalid "exports" main target ${JSON.stringify(target)} defined in the package config ${packagePath}package.json${base ? ` imported from ${base}` : ""}${relatedError ? "; targets must start with \"./\"" : ""}`;
	}
	return `Invalid "${isImport ? "imports" : "exports"}" target ${JSON.stringify(target)} defined for '${key}' in the package config ${packagePath}package.json${base ? ` imported from ${base}` : ""}${relatedError ? "; targets must start with \"./\"" : ""}`;
}, Error);
const ERR_MODULE_NOT_FOUND = createError("ERR_MODULE_NOT_FOUND", function(path, base, exactUrl = false) {
	if (exactUrl && typeof exactUrl === "string") this.url = `${exactUrl}`;
	return `Cannot find ${exactUrl ? "module" : "package"} '${path}' imported from ${base}`;
}, Error);
const ERR_PACKAGE_IMPORT_NOT_DEFINED = createError("ERR_PACKAGE_IMPORT_NOT_DEFINED", (specifier, packagePath, base) => {
	return `Package import specifier "${specifier}" is not defined${packagePath ? ` in package ${packagePath || ""}package.json` : ""} imported from ${base}`;
}, TypeError);
const ERR_PACKAGE_PATH_NOT_EXPORTED = createError("ERR_PACKAGE_PATH_NOT_EXPORTED", (packagePath, subpath, base) => {
	if (subpath === ".") return `No "exports" main defined in ${packagePath}package.json${base ? ` imported from ${base}` : ""}`;
	return `Package subpath '${subpath}' is not defined by "exports" in ${packagePath}package.json${base ? ` imported from ${base}` : ""}`;
}, Error);
const ERR_UNSUPPORTED_DIR_IMPORT = createError("ERR_UNSUPPORTED_DIR_IMPORT", function(path, base, exactUrl = void 0) {
	this.url = exactUrl;
	return `Directory import '${path}' is not supported resolving ES modules imported from ${base}`;
}, Error);
const ERR_UNSUPPORTED_RESOLVE_REQUEST = createError("ERR_UNSUPPORTED_RESOLVE_REQUEST", "Failed to resolve module specifier \"%s\" from \"%s\": Invalid relative URL or base scheme is not hierarchical.", TypeError);
const ERR_UNKNOWN_FILE_EXTENSION = createError("ERR_UNKNOWN_FILE_EXTENSION", (extension, path) => {
	return `Unknown file extension "${extension}" for ${path}`;
}, TypeError);
createError("ERR_INVALID_ARG_VALUE", (name, value, reason = "is invalid") => {
	let inspected = inspect(value);
	if (inspected.length > 128) inspected = `${inspected.slice(0, 128)}...`;
	return `The ${name.includes(".") ? "property" : "argument"} '${name}' ${reason}. Received ${inspected}`;
}, TypeError);
const hasOwnProperty$1 = {}.hasOwnProperty;
const cache = /* @__PURE__ */ new Map();
function read(jsonPath, { base, specifier }) {
	const existing = cache.get(jsonPath);
	if (existing) return existing;
	let string;
	try {
		string = fs.readFileSync(path.toNamespacedPath(jsonPath), "utf8");
	} catch (error) {
		const exception = error;
		if (exception.code !== "ENOENT") throw exception;
	}
	const result = {
		exists: false,
		pjsonPath: jsonPath,
		main: void 0,
		name: void 0,
		type: "none",
		exports: void 0,
		imports: void 0
	};
	if (string !== void 0) {
		let parsed;
		try {
			parsed = JSON.parse(string);
		} catch (error_) {
			const error = new ERR_INVALID_PACKAGE_CONFIG(jsonPath, (base ? `"${specifier}" from ` : "") + fileURLToPath(base || specifier), error_.message);
			error.cause = error_;
			throw error;
		}
		result.exists = true;
		if (hasOwnProperty$1.call(parsed, "name") && typeof parsed.name === "string") result.name = parsed.name;
		if (hasOwnProperty$1.call(parsed, "main") && typeof parsed.main === "string") result.main = parsed.main;
		if (hasOwnProperty$1.call(parsed, "exports")) result.exports = parsed.exports;
		if (hasOwnProperty$1.call(parsed, "imports")) result.imports = parsed.imports;
		if (hasOwnProperty$1.call(parsed, "type") && (parsed.type === "commonjs" || parsed.type === "module")) result.type = parsed.type;
	}
	cache.set(jsonPath, result);
	return result;
}
function getPackageScopeConfig(resolved) {
	let packageJSONUrl = new URL("package.json", resolved);
	while (true) {
		if (packageJSONUrl.pathname.endsWith("node_modules/package.json")) break;
		const packageConfig = read(fileURLToPath(packageJSONUrl), { specifier: resolved });
		if (packageConfig.exists) return packageConfig;
		const lastPackageJSONUrl = packageJSONUrl;
		packageJSONUrl = new URL("../package.json", packageJSONUrl);
		if (packageJSONUrl.pathname === lastPackageJSONUrl.pathname) break;
	}
	return {
		pjsonPath: fileURLToPath(packageJSONUrl),
		exists: false,
		type: "none"
	};
}
const hasOwnProperty = {}.hasOwnProperty;
const extensionFormatMap = {
	__proto__: null,
	".json": "json",
	".cjs": "commonjs",
	".cts": "commonjs",
	".js": "module",
	".ts": "module",
	".mts": "module",
	".mjs": "module"
};
const protocolHandlers = {
	__proto__: null,
	"data:": getDataProtocolModuleFormat,
	"file:": getFileProtocolModuleFormat,
	"node:": () => "builtin"
};
function mimeToFormat(mime) {
	if (mime && /^\s*(text|application)\/javascript\s*(;\s*charset=utf-?8\s*)?$/i.test(mime)) return "module";
	if (mime === "application/json") return "json";
	return null;
}
function getDataProtocolModuleFormat(parsed) {
	const { 1: mime } = /^([^/]+\/[^;,]+)(?:[^,]*?)(;base64)?,/.exec(parsed.pathname) || [
		null,
		null,
		null
	];
	return mimeToFormat(mime);
}
const DOT_CODE = 46;
const SLASH_CODE = 47;
function extname(url) {
	const pathname = url.pathname;
	for (let i = pathname.length - 1; i > 0; i--) switch (pathname.charCodeAt(i)) {
		case SLASH_CODE: return "";
		case DOT_CODE: return pathname.charCodeAt(i - 1) === SLASH_CODE ? "" : pathname.slice(i);
	}
	return "";
}
function getFileProtocolModuleFormat(url, _context, ignoreErrors) {
	const ext = extname(url);
	if (ext === ".js") {
		const { type: packageType } = getPackageScopeConfig(url);
		if (packageType !== "none") return packageType;
		return "commonjs";
	}
	if (ext === "") {
		const { type: packageType } = getPackageScopeConfig(url);
		if (packageType === "module") return "module";
		if (packageType !== "none") return packageType;
		return "commonjs";
	}
	const format = extensionFormatMap[ext];
	if (format) return format;
	if (ignoreErrors) return;
	throw new ERR_UNKNOWN_FILE_EXTENSION(ext, fileURLToPath(url));
}
function defaultGetFormatWithoutErrors(url, context) {
	const protocol = url.protocol;
	if (!hasOwnProperty.call(protocolHandlers, protocol)) return null;
	return protocolHandlers[protocol](url, context, true) || null;
}
const RegExpPrototypeSymbolReplace = RegExp.prototype[Symbol.replace];
const own = {}.hasOwnProperty;
const invalidSegmentRegEx = /(^|\\|\/)((\.|%2e)(\.|%2e)?|(n|%6e|%4e)(o|%6f|%4f)(d|%64|%44)(e|%65|%45)(_|%5f)(m|%6d|%4d)(o|%6f|%4f)(d|%64|%44)(u|%75|%55)(l|%6c|%4c)(e|%65|%45)(s|%73|%53))?(\\|\/|$)/i;
const deprecatedInvalidSegmentRegEx = /(^|\\|\/)((\.|%2e)(\.|%2e)?|(n|%6e|%4e)(o|%6f|%4f)(d|%64|%44)(e|%65|%45)(_|%5f)(m|%6d|%4d)(o|%6f|%4f)(d|%64|%44)(u|%75|%55)(l|%6c|%4c)(e|%65|%45)(s|%73|%53))(\\|\/|$)/i;
const invalidPackageNameRegEx = /^\.|%|\\/;
const patternRegEx = /\*/g;
const encodedSeparatorRegEx = /%2f|%5c/i;
const emittedPackageWarnings = /* @__PURE__ */ new Set();
const doubleSlashRegEx = /[/\\]{2}/;
function emitInvalidSegmentDeprecation(target, request, match, packageJsonUrl, internal, base, isTarget) {
	if (process$1.noDeprecation) return;
	const pjsonPath = fileURLToPath(packageJsonUrl);
	const double = doubleSlashRegEx.exec(isTarget ? target : request) !== null;
	process$1.emitWarning(`Use of deprecated ${double ? "double slash" : "leading or trailing slash matching"} resolving "${target}" for module request "${request}" ${request === match ? "" : `matched to "${match}" `}in the "${internal ? "imports" : "exports"}" field module resolution of the package at ${pjsonPath}${base ? ` imported from ${fileURLToPath(base)}` : ""}.`, "DeprecationWarning", "DEP0166");
}
function emitLegacyIndexDeprecation(url, packageJsonUrl, base, main) {
	if (process$1.noDeprecation) return;
	if (defaultGetFormatWithoutErrors(url, { parentURL: base.href }) !== "module") return;
	const urlPath = fileURLToPath(url.href);
	const packagePath = fileURLToPath(new URL$1(".", packageJsonUrl));
	const basePath = fileURLToPath(base);
	if (!main) process$1.emitWarning(`No "main" or "exports" field defined in the package.json for ${packagePath} resolving the main entry point "${urlPath.slice(packagePath.length)}", imported from ${basePath}.\nDefault "index" lookups for the main are deprecated for ES modules.`, "DeprecationWarning", "DEP0151");
	else if (path.resolve(packagePath, main) !== urlPath) process$1.emitWarning(`Package ${packagePath} has a "main" field set to "${main}", excluding the full filename and extension to the resolved file at "${urlPath.slice(packagePath.length)}", imported from ${basePath}.\n Automatic extension resolution of the "main" field is deprecated for ES modules.`, "DeprecationWarning", "DEP0151");
}
function tryStatSync(path) {
	try {
		return statSync(path);
	} catch {}
}
function fileExists(url) {
	const stats = statSync(url, { throwIfNoEntry: false });
	const isFile = stats ? stats.isFile() : void 0;
	return isFile === null || isFile === void 0 ? false : isFile;
}
function legacyMainResolve(packageJsonUrl, packageConfig, base) {
	let guess;
	if (packageConfig.main !== void 0) {
		guess = new URL$1(packageConfig.main, packageJsonUrl);
		if (fileExists(guess)) return guess;
		const tries = [
			`./${packageConfig.main}.js`,
			`./${packageConfig.main}.json`,
			`./${packageConfig.main}.node`,
			`./${packageConfig.main}/index.js`,
			`./${packageConfig.main}/index.json`,
			`./${packageConfig.main}/index.node`
		];
		let i = -1;
		while (++i < tries.length) {
			guess = new URL$1(tries[i], packageJsonUrl);
			if (fileExists(guess)) break;
			guess = void 0;
		}
		if (guess) {
			emitLegacyIndexDeprecation(guess, packageJsonUrl, base, packageConfig.main);
			return guess;
		}
	}
	const tries = [
		"./index.js",
		"./index.json",
		"./index.node"
	];
	let i = -1;
	while (++i < tries.length) {
		guess = new URL$1(tries[i], packageJsonUrl);
		if (fileExists(guess)) break;
		guess = void 0;
	}
	if (guess) {
		emitLegacyIndexDeprecation(guess, packageJsonUrl, base, packageConfig.main);
		return guess;
	}
	throw new ERR_MODULE_NOT_FOUND(fileURLToPath(new URL$1(".", packageJsonUrl)), fileURLToPath(base));
}
function finalizeResolution(resolved, base, preserveSymlinks) {
	if (encodedSeparatorRegEx.exec(resolved.pathname) !== null) throw new ERR_INVALID_MODULE_SPECIFIER(resolved.pathname, String.raw`must not include encoded "/" or "\" characters`, fileURLToPath(base));
	let filePath;
	try {
		filePath = fileURLToPath(resolved);
	} catch (error) {
		Object.defineProperty(error, "input", { value: String(resolved) });
		Object.defineProperty(error, "module", { value: String(base) });
		throw error;
	}
	const stats = tryStatSync(filePath.endsWith("/") ? filePath.slice(-1) : filePath);
	if (stats && stats.isDirectory()) {
		const error = new ERR_UNSUPPORTED_DIR_IMPORT(filePath, fileURLToPath(base));
		error.url = String(resolved);
		throw error;
	}
	if (!stats || !stats.isFile()) {
		const error = new ERR_MODULE_NOT_FOUND(filePath || resolved.pathname, base && fileURLToPath(base), true);
		error.url = String(resolved);
		throw error;
	}
	if (!preserveSymlinks) {
		const real = realpathSync(filePath);
		const { search, hash } = resolved;
		resolved = pathToFileURL(real + (filePath.endsWith(path.sep) ? "/" : ""));
		resolved.search = search;
		resolved.hash = hash;
	}
	return resolved;
}
function importNotDefined(specifier, packageJsonUrl, base) {
	return new ERR_PACKAGE_IMPORT_NOT_DEFINED(specifier, packageJsonUrl && fileURLToPath(new URL$1(".", packageJsonUrl)), fileURLToPath(base));
}
function exportsNotFound(subpath, packageJsonUrl, base) {
	return new ERR_PACKAGE_PATH_NOT_EXPORTED(fileURLToPath(new URL$1(".", packageJsonUrl)), subpath, base && fileURLToPath(base));
}
function throwInvalidSubpath(request, match, packageJsonUrl, internal, base) {
	throw new ERR_INVALID_MODULE_SPECIFIER(request, `request is not a valid match in pattern "${match}" for the "${internal ? "imports" : "exports"}" resolution of ${fileURLToPath(packageJsonUrl)}`, base && fileURLToPath(base));
}
function invalidPackageTarget(subpath, target, packageJsonUrl, internal, base) {
	target = typeof target === "object" && target !== null ? JSON.stringify(target, null, "") : `${target}`;
	return new ERR_INVALID_PACKAGE_TARGET(fileURLToPath(new URL$1(".", packageJsonUrl)), subpath, target, internal, base && fileURLToPath(base));
}
function resolvePackageTargetString(target, subpath, match, packageJsonUrl, base, pattern, internal, isPathMap, conditions) {
	if (subpath !== "" && !pattern && target.at(-1) !== "/") throw invalidPackageTarget(match, target, packageJsonUrl, internal, base);
	if (!target.startsWith("./")) {
		if (internal && !target.startsWith("../") && !target.startsWith("/")) {
			let isURL = false;
			try {
				new URL$1(target);
				isURL = true;
			} catch {}
			if (!isURL) return packageResolve(pattern ? RegExpPrototypeSymbolReplace.call(patternRegEx, target, () => subpath) : target + subpath, packageJsonUrl, conditions);
		}
		throw invalidPackageTarget(match, target, packageJsonUrl, internal, base);
	}
	if (invalidSegmentRegEx.exec(target.slice(2)) !== null) if (deprecatedInvalidSegmentRegEx.exec(target.slice(2)) === null) {
		if (!isPathMap) {
			const request = pattern ? match.replace("*", () => subpath) : match + subpath;
			emitInvalidSegmentDeprecation(pattern ? RegExpPrototypeSymbolReplace.call(patternRegEx, target, () => subpath) : target, request, match, packageJsonUrl, internal, base, true);
		}
	} else throw invalidPackageTarget(match, target, packageJsonUrl, internal, base);
	const resolved = new URL$1(target, packageJsonUrl);
	const resolvedPath = resolved.pathname;
	const packagePath = new URL$1(".", packageJsonUrl).pathname;
	if (!resolvedPath.startsWith(packagePath)) throw invalidPackageTarget(match, target, packageJsonUrl, internal, base);
	if (subpath === "") return resolved;
	if (invalidSegmentRegEx.exec(subpath) !== null) {
		const request = pattern ? match.replace("*", () => subpath) : match + subpath;
		if (deprecatedInvalidSegmentRegEx.exec(subpath) === null) {
			if (!isPathMap) emitInvalidSegmentDeprecation(pattern ? RegExpPrototypeSymbolReplace.call(patternRegEx, target, () => subpath) : target, request, match, packageJsonUrl, internal, base, false);
		} else throwInvalidSubpath(request, match, packageJsonUrl, internal, base);
	}
	if (pattern) return new URL$1(RegExpPrototypeSymbolReplace.call(patternRegEx, resolved.href, () => subpath));
	return new URL$1(subpath, resolved);
}
function isArrayIndex(key) {
	const keyNumber = Number(key);
	if (`${keyNumber}` !== key) return false;
	return keyNumber >= 0 && keyNumber < 4294967295;
}
function resolvePackageTarget(packageJsonUrl, target, subpath, packageSubpath, base, pattern, internal, isPathMap, conditions) {
	if (typeof target === "string") return resolvePackageTargetString(target, subpath, packageSubpath, packageJsonUrl, base, pattern, internal, isPathMap, conditions);
	if (Array.isArray(target)) {
		const targetList = target;
		if (targetList.length === 0) return null;
		let lastException;
		let i = -1;
		while (++i < targetList.length) {
			const targetItem = targetList[i];
			let resolveResult;
			try {
				resolveResult = resolvePackageTarget(packageJsonUrl, targetItem, subpath, packageSubpath, base, pattern, internal, isPathMap, conditions);
			} catch (error) {
				const exception = error;
				lastException = exception;
				if (exception.code === "ERR_INVALID_PACKAGE_TARGET") continue;
				throw error;
			}
			if (resolveResult === void 0) continue;
			if (resolveResult === null) {
				lastException = null;
				continue;
			}
			return resolveResult;
		}
		if (lastException === void 0 || lastException === null) return lastException;
		throw lastException;
	}
	if (typeof target === "object" && target !== null) {
		const keys = Object.getOwnPropertyNames(target);
		let i = -1;
		while (++i < keys.length) {
			const key = keys[i];
			if (isArrayIndex(key)) throw new ERR_INVALID_PACKAGE_CONFIG(fileURLToPath(packageJsonUrl), fileURLToPath(base), "\"exports\" cannot contain numeric property keys.");
		}
		i = -1;
		while (++i < keys.length) {
			const key = keys[i];
			if (key === "default" || conditions && conditions.has(key)) {
				const conditionalTarget = target[key];
				const resolveResult = resolvePackageTarget(packageJsonUrl, conditionalTarget, subpath, packageSubpath, base, pattern, internal, isPathMap, conditions);
				if (resolveResult === void 0) continue;
				return resolveResult;
			}
		}
		return;
	}
	if (target === null) return null;
	throw invalidPackageTarget(packageSubpath, target, packageJsonUrl, internal, base);
}
function isConditionalExportsMainSugar(exports, packageJsonUrl, base) {
	if (typeof exports === "string" || Array.isArray(exports)) return true;
	if (typeof exports !== "object" || exports === null) return false;
	const keys = Object.getOwnPropertyNames(exports);
	let isConditionalSugar = false;
	let i = 0;
	let keyIndex = -1;
	while (++keyIndex < keys.length) {
		const key = keys[keyIndex];
		const currentIsConditionalSugar = key === "" || key[0] !== ".";
		if (i++ === 0) isConditionalSugar = currentIsConditionalSugar;
		else if (isConditionalSugar !== currentIsConditionalSugar) throw new ERR_INVALID_PACKAGE_CONFIG(fileURLToPath(packageJsonUrl), fileURLToPath(base), "\"exports\" cannot contain some keys starting with '.' and some not. The exports object must either be an object of package subpath keys or an object of main entry condition name keys only.");
	}
	return isConditionalSugar;
}
function emitTrailingSlashPatternDeprecation(match, pjsonUrl, base) {
	if (process$1.noDeprecation) return;
	const pjsonPath = fileURLToPath(pjsonUrl);
	if (emittedPackageWarnings.has(pjsonPath + "|" + match)) return;
	emittedPackageWarnings.add(pjsonPath + "|" + match);
	process$1.emitWarning(`Use of deprecated trailing slash pattern mapping "${match}" in the "exports" field module resolution of the package at ${pjsonPath}${base ? ` imported from ${fileURLToPath(base)}` : ""}. Mapping specifiers ending in "/" is no longer supported.`, "DeprecationWarning", "DEP0155");
}
function packageExportsResolve(packageJsonUrl, packageSubpath, packageConfig, base, conditions) {
	let exports = packageConfig.exports;
	if (isConditionalExportsMainSugar(exports, packageJsonUrl, base)) exports = { ".": exports };
	if (own.call(exports, packageSubpath) && !packageSubpath.includes("*") && !packageSubpath.endsWith("/")) {
		const target = exports[packageSubpath];
		const resolveResult = resolvePackageTarget(packageJsonUrl, target, "", packageSubpath, base, false, false, false, conditions);
		if (resolveResult === null || resolveResult === void 0) throw exportsNotFound(packageSubpath, packageJsonUrl, base);
		return resolveResult;
	}
	let bestMatch = "";
	let bestMatchSubpath = "";
	const keys = Object.getOwnPropertyNames(exports);
	let i = -1;
	while (++i < keys.length) {
		const key = keys[i];
		const patternIndex = key.indexOf("*");
		if (patternIndex !== -1 && packageSubpath.startsWith(key.slice(0, patternIndex))) {
			if (packageSubpath.endsWith("/")) emitTrailingSlashPatternDeprecation(packageSubpath, packageJsonUrl, base);
			const patternTrailer = key.slice(patternIndex + 1);
			if (packageSubpath.length >= key.length && packageSubpath.endsWith(patternTrailer) && patternKeyCompare(bestMatch, key) === 1 && key.lastIndexOf("*") === patternIndex) {
				bestMatch = key;
				bestMatchSubpath = packageSubpath.slice(patternIndex, packageSubpath.length - patternTrailer.length);
			}
		}
	}
	if (bestMatch) {
		const target = exports[bestMatch];
		const resolveResult = resolvePackageTarget(packageJsonUrl, target, bestMatchSubpath, bestMatch, base, true, false, packageSubpath.endsWith("/"), conditions);
		if (resolveResult === null || resolveResult === void 0) throw exportsNotFound(packageSubpath, packageJsonUrl, base);
		return resolveResult;
	}
	throw exportsNotFound(packageSubpath, packageJsonUrl, base);
}
function patternKeyCompare(a, b) {
	const aPatternIndex = a.indexOf("*");
	const bPatternIndex = b.indexOf("*");
	const baseLengthA = aPatternIndex === -1 ? a.length : aPatternIndex + 1;
	const baseLengthB = bPatternIndex === -1 ? b.length : bPatternIndex + 1;
	if (baseLengthA > baseLengthB) return -1;
	if (baseLengthB > baseLengthA) return 1;
	if (aPatternIndex === -1) return 1;
	if (bPatternIndex === -1) return -1;
	if (a.length > b.length) return -1;
	if (b.length > a.length) return 1;
	return 0;
}
function packageImportsResolve(name, base, conditions) {
	if (name === "#" || name.endsWith("/")) throw new ERR_INVALID_MODULE_SPECIFIER(name, "is not a valid internal imports specifier name", fileURLToPath(base));
	let packageJsonUrl;
	const packageConfig = getPackageScopeConfig(base);
	if (packageConfig.exists) {
		packageJsonUrl = pathToFileURL(packageConfig.pjsonPath);
		const imports = packageConfig.imports;
		if (imports) if (own.call(imports, name) && !name.includes("*")) {
			const resolveResult = resolvePackageTarget(packageJsonUrl, imports[name], "", name, base, false, true, false, conditions);
			if (resolveResult !== null && resolveResult !== void 0) return resolveResult;
		} else {
			let bestMatch = "";
			let bestMatchSubpath = "";
			const keys = Object.getOwnPropertyNames(imports);
			let i = -1;
			while (++i < keys.length) {
				const key = keys[i];
				const patternIndex = key.indexOf("*");
				if (patternIndex !== -1 && name.startsWith(key.slice(0, -1))) {
					const patternTrailer = key.slice(patternIndex + 1);
					if (name.length >= key.length && name.endsWith(patternTrailer) && patternKeyCompare(bestMatch, key) === 1 && key.lastIndexOf("*") === patternIndex) {
						bestMatch = key;
						bestMatchSubpath = name.slice(patternIndex, name.length - patternTrailer.length);
					}
				}
			}
			if (bestMatch) {
				const target = imports[bestMatch];
				const resolveResult = resolvePackageTarget(packageJsonUrl, target, bestMatchSubpath, bestMatch, base, true, true, false, conditions);
				if (resolveResult !== null && resolveResult !== void 0) return resolveResult;
			}
		}
	}
	throw importNotDefined(name, packageJsonUrl, base);
}
function parsePackageName(specifier, base) {
	let separatorIndex = specifier.indexOf("/");
	let validPackageName = true;
	let isScoped = false;
	if (specifier[0] === "@") {
		isScoped = true;
		if (separatorIndex === -1 || specifier.length === 0) validPackageName = false;
		else separatorIndex = specifier.indexOf("/", separatorIndex + 1);
	}
	const packageName = separatorIndex === -1 ? specifier : specifier.slice(0, separatorIndex);
	if (invalidPackageNameRegEx.exec(packageName) !== null) validPackageName = false;
	if (!validPackageName) throw new ERR_INVALID_MODULE_SPECIFIER(specifier, "is not a valid package name", fileURLToPath(base));
	return {
		packageName,
		packageSubpath: "." + (separatorIndex === -1 ? "" : specifier.slice(separatorIndex)),
		isScoped
	};
}
function packageResolve(specifier, base, conditions) {
	if (nodeBuiltins.includes(specifier)) return new URL$1("node:" + specifier);
	const { packageName, packageSubpath, isScoped } = parsePackageName(specifier, base);
	const packageConfig = getPackageScopeConfig(base);
	if (packageConfig.exists && packageConfig.name === packageName && packageConfig.exports !== void 0 && packageConfig.exports !== null) return packageExportsResolve(pathToFileURL(packageConfig.pjsonPath), packageSubpath, packageConfig, base, conditions);
	let packageJsonUrl = new URL$1("./node_modules/" + packageName + "/package.json", base);
	let packageJsonPath = fileURLToPath(packageJsonUrl);
	let lastPath;
	do {
		const stat = tryStatSync(packageJsonPath.slice(0, -13));
		if (!stat || !stat.isDirectory()) {
			lastPath = packageJsonPath;
			packageJsonUrl = new URL$1((isScoped ? "../../../../node_modules/" : "../../../node_modules/") + packageName + "/package.json", packageJsonUrl);
			packageJsonPath = fileURLToPath(packageJsonUrl);
			continue;
		}
		const packageConfig = read(packageJsonPath, {
			base,
			specifier
		});
		if (packageConfig.exports !== void 0 && packageConfig.exports !== null) return packageExportsResolve(packageJsonUrl, packageSubpath, packageConfig, base, conditions);
		if (packageSubpath === ".") return legacyMainResolve(packageJsonUrl, packageConfig, base);
		return new URL$1(packageSubpath, packageJsonUrl);
	} while (packageJsonPath.length !== lastPath.length);
	throw new ERR_MODULE_NOT_FOUND(packageName, fileURLToPath(base), false);
}
function isRelativeSpecifier(specifier) {
	if (specifier[0] === ".") {
		if (specifier.length === 1 || specifier[1] === "/") return true;
		if (specifier[1] === "." && (specifier.length === 2 || specifier[2] === "/")) return true;
	}
	return false;
}
function shouldBeTreatedAsRelativeOrAbsolutePath(specifier) {
	if (specifier === "") return false;
	if (specifier[0] === "/") return true;
	return isRelativeSpecifier(specifier);
}
function moduleResolve(specifier, base, conditions, preserveSymlinks) {
	const protocol = base.protocol;
	const isData = protocol === "data:";
	let resolved;
	if (shouldBeTreatedAsRelativeOrAbsolutePath(specifier)) try {
		resolved = new URL$1(specifier, base);
	} catch (error_) {
		const error = new ERR_UNSUPPORTED_RESOLVE_REQUEST(specifier, base);
		error.cause = error_;
		throw error;
	}
	else if (protocol === "file:" && specifier[0] === "#") resolved = packageImportsResolve(specifier, base, conditions);
	else try {
		resolved = new URL$1(specifier);
	} catch (error_) {
		if (isData && !nodeBuiltins.includes(specifier)) {
			const error = new ERR_UNSUPPORTED_RESOLVE_REQUEST(specifier, base);
			error.cause = error_;
			throw error;
		}
		resolved = packageResolve(specifier, base, conditions);
	}
	assert.ok(resolved !== void 0, "expected to be defined");
	if (resolved.protocol !== "file:") return resolved;
	return finalizeResolution(resolved, base, preserveSymlinks);
}
const DEFAULT_CONDITIONS_SET = /* #__PURE__ */ new Set(["node", "import"]);
const DEFAULT_CONDITIONS_KEY = "2:6:import4:node";
const isWindows = /* #__PURE__ */ (() => process.platform === "win32")();
const globalCache = /* #__PURE__ */ (() => globalThis["__EXSOLVE_CACHE__"] ||= /* @__PURE__ */ new Map())();
function resolveModuleURL(input, options) {
	const parsedInput = _parseInput(input);
	if ("external" in parsedInput) return parsedInput.external;
	const specifier = parsedInput.specifier;
	let url = parsedInput.url;
	let absolutePath = parsedInput.absolutePath;
	let cacheKey;
	let cacheObj;
	if (options?.cache !== false) {
		cacheKey = _cacheKey(absolutePath || specifier, options);
		cacheObj = options?.cache && typeof options?.cache === "object" ? options.cache : globalCache;
	}
	if (cacheObj) {
		const cached = cacheObj.get(cacheKey);
		if (typeof cached === "string") return cached;
		if (cached instanceof Error) {
			if (options?.try) return;
			throw cached;
		}
	}
	if (absolutePath) try {
		const stat = lstatSync(absolutePath);
		if (stat.isSymbolicLink()) {
			absolutePath = realpathSync(absolutePath);
			url = pathToFileURL(absolutePath);
		}
		if (stat.isFile()) {
			if (cacheObj) cacheObj.set(cacheKey, url.href);
			return url.href;
		}
	} catch (error) {
		if (error?.code !== "ENOENT") {
			if (cacheObj) cacheObj.set(cacheKey, error);
			throw error;
		}
	}
	const conditionsSet = options?.conditions ? new Set(options.conditions) : DEFAULT_CONDITIONS_SET;
	const target = specifier || url.href;
	const bases = _normalizeBases(options?.from);
	const suffixes = options?.suffixes || [""];
	const extensions = options?.extensions ? ["", ...options.extensions] : [""];
	let resolved;
	for (const base of bases) {
		for (const suffix of suffixes) {
			let name = _join(target, suffix);
			if (name === ".") name += "/.";
			for (const extension of extensions) {
				resolved = _tryModuleResolve(name + extension, base, conditionsSet);
				if (resolved) break;
			}
			if (resolved) break;
		}
		if (resolved) break;
	}
	if (!resolved) {
		const error = /* @__PURE__ */ new Error(`Cannot resolve module "${input}" (from: ${bases.map((u) => _fmtPath(u)).join(", ")})`);
		error.code = "ERR_MODULE_NOT_FOUND";
		if (cacheObj) cacheObj.set(cacheKey, error);
		if (options?.try) return;
		throw error;
	}
	if (cacheObj) cacheObj.set(cacheKey, resolved.href);
	return resolved.href;
}
function resolveModulePath(id, options) {
	const resolved = resolveModuleURL(id, options);
	if (!resolved) return;
	if (!resolved.startsWith("file://") && options?.try) return;
	const absolutePath = fileURLToPath(resolved);
	return isWindows ? _normalizeWinPath(absolutePath) : absolutePath;
}
function createResolver(defaults) {
	if (defaults?.from) defaults = {
		...defaults,
		from: _normalizeBases(defaults?.from)
	};
	return {
		resolveModuleURL: (id, opts) => resolveModuleURL(id, {
			...defaults,
			...opts
		}),
		resolveModulePath: (id, opts) => resolveModulePath(id, {
			...defaults,
			...opts
		}),
		clearResolveCache: () => {
			if (defaults?.cache !== false) if (defaults?.cache && typeof defaults?.cache === "object") defaults.cache.clear();
			else globalCache.clear();
		}
	};
}
function clearResolveCache() {
	globalCache.clear();
}
function _tryModuleResolve(specifier, base, conditions) {
	try {
		return moduleResolve(specifier, base, conditions);
	} catch {}
}
function _normalizeBases(inputs) {
	const urls = (Array.isArray(inputs) ? inputs : [inputs]).flatMap((input) => _normalizeBase(input));
	if (urls.length === 0) return [pathToFileURL("./")];
	return urls;
}
function _normalizeBase(input) {
	if (!input) return [];
	if (_isURL(input)) return [input];
	if (typeof input !== "string") return [];
	if (/^(?:node|data|http|https|file):/.test(input)) return new URL(input);
	try {
		if (input.endsWith("/") || statSync(input).isDirectory()) return pathToFileURL(input + "/");
		return pathToFileURL(input);
	} catch {
		return [pathToFileURL(input + "/"), pathToFileURL(input)];
	}
}
function _fmtPath(input) {
	try {
		return fileURLToPath(input);
	} catch {
		return input;
	}
}
function _cacheKey(id, options) {
	let from;
	if (Array.isArray(options?.from)) from = options.from;
	else if (options?.from) from = [options.from];
	return _cacheKeyValues([id]) + _conditionsKey(options?.conditions) + _cacheKeyValues(options?.extensions) + _cacheKeyValues(from) + _cacheKeyValues(options?.suffixes);
}
function _conditionsKey(conditions) {
	if (!conditions) return DEFAULT_CONDITIONS_KEY;
	return _cacheKeyValues([...new Set(conditions)].sort());
}
function _cacheKeyValues(values) {
	if (!values) return "-";
	let key = `${values.length}:`;
	for (const value of values) {
		const stringValue = String(value);
		key += `${stringValue.length}:${stringValue}`;
	}
	return key;
}
function _join(a, b) {
	if (!a || !b || b === "/") return a;
	return (a.endsWith("/") ? a : a + "/") + (b.startsWith("/") ? b.slice(1) : b);
}
function _normalizeWinPath(path) {
	return path.replace(/\\/g, "/").replace(/^[a-z]:\//, (r) => r.toUpperCase());
}
function _isURL(input) {
	return input instanceof URL || input?.constructor?.name === "URL";
}
function _parseInput(input) {
	if (typeof input === "string") {
		if (input.startsWith("file:")) {
			const url = new URL(input);
			return {
				url,
				absolutePath: fileURLToPath(url)
			};
		}
		if (isAbsolute(input)) return {
			url: pathToFileURL(input),
			absolutePath: input
		};
		if (/^(?:node|data|http|https):/.test(input)) return { external: input };
		if (nodeBuiltins.includes(input) && !input.includes(":")) return { external: `node:${input}` };
		return { specifier: input };
	}
	if (_isURL(input)) {
		if (input.protocol === "file:") return {
			url: input,
			absolutePath: fileURLToPath(input)
		};
		return { external: input.href };
	}
	throw new TypeError("id must be a `string` or `URL`");
}
export { clearResolveCache, createResolver, resolveModulePath, resolveModuleURL };
