import { isAbsolute, join, resolve } from "pathe";
import pm from "picomatch";
//#region src/path.ts
/**
* Converts path separators to forward slash.
*/
function normalizePath(filename) {
	return filename.replaceAll("\\", "/");
}
//#endregion
//#region src/utils.ts
const isArray = Array.isArray;
function toArray(thing) {
	if (isArray(thing)) return thing;
	if (thing == null) return [];
	return [thing];
}
//#endregion
//#region src/filter.ts
const escapeMark = "[_#EsCaPe#_]";
function getMatcherString(id, resolutionBase) {
	if (resolutionBase === false || isAbsolute(id) || id.startsWith("**")) return normalizePath(id);
	return join(normalizePath(resolve(resolutionBase || "")).replaceAll(/[-^$*+?.()|[\]{}]/g, `${escapeMark}$&`), normalizePath(id)).replaceAll(escapeMark, "\\");
}
/**
* Constructs a filter function which can be used to determine whether or not
* certain modules should be operated upon.
* @param include If `include` is omitted or has zero length, filter will return `true` by default.
* @param exclude ID must not match any of the `exclude` patterns.
* @param options Additional options.
* @param options.resolve Optionally resolves the patterns against a directory other than `process.cwd()`.
* If a `string` is specified, then the value will be used as the base directory.
* Relative paths will be resolved against `process.cwd()` first.
* If `false`, then the patterns will not be resolved against any directory.
* This can be useful if you want to create a filter for virtual module names.
*/
function createFilter(include, exclude, options) {
	const resolutionBase = options && options.resolve;
	const getMatcher = (id) => id instanceof RegExp ? id : { test: (what) => {
		return pm(getMatcherString(id, resolutionBase), { dot: true })(what);
	} };
	const includeMatchers = toArray(include).map(getMatcher);
	const excludeMatchers = toArray(exclude).map(getMatcher);
	if (!includeMatchers.length && !excludeMatchers.length) return (id) => typeof id === "string" && !id.includes("\0");
	return function result(id) {
		if (typeof id !== "string") return false;
		if (id.includes("\0")) return false;
		const pathId = normalizePath(id);
		for (const matcher of excludeMatchers) {
			if (matcher instanceof RegExp) matcher.lastIndex = 0;
			if (matcher.test(pathId)) return false;
		}
		for (const matcher of includeMatchers) {
			if (matcher instanceof RegExp) matcher.lastIndex = 0;
			if (matcher.test(pathId)) return true;
		}
		return !includeMatchers.length;
	};
}
//#endregion
export { createFilter, normalizePath, toArray };
