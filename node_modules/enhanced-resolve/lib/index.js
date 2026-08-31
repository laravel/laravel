/*
	MIT License http://www.opensource.org/licenses/mit-license.php
	Author Tobias Koppers @sokra
*/

"use strict";

const memoize = require("./util/memoize");

/** @typedef {import("./CachedInputFileSystem").BaseFileSystem} BaseFileSystem */
/** @typedef {import("./PnpPlugin").PnpApiImpl} PnpApi */
/** @typedef {import("./Resolver")} Resolver */
/** @typedef {import("./Resolver").Context} Context */
/** @typedef {import("./Resolver").FileSystem} FileSystem */
/** @typedef {import("./Resolver").ResolveCallback} ResolveCallback */
/** @typedef {import("./Resolver").ResolveContext} ResolveContext */
/** @typedef {import("./Resolver").ResolveRequest} ResolveRequest */
/** @typedef {import("./Resolver").SyncFileSystem} SyncFileSystem */
/** @typedef {import("./ResolverFactory").Plugin} Plugin */
/** @typedef {import("./ResolverFactory").UserResolveOptions} ResolveOptions */

/**
 * @typedef {{
 * (context: Context, parent: string | URL, specifier: string | URL, resolveContext: ResolveContext, callback: ResolveCallback): void,
 * (context: Context, parent: string | URL, specifier: string | URL, callback: ResolveCallback): void,
 * (parent: string | URL, specifier: string | URL, resolveContext: ResolveContext, callback: ResolveCallback): void,
 * (parent: string | URL, specifier: string | URL, callback: ResolveCallback): void,
 * }} ResolveFunctionAsync
 */

/**
 * @typedef {{
 * (context: Context, parent: string | URL, specifier: string | URL, resolveContext?: ResolveContext): string | false,
 * (parent: string | URL, specifier: string | URL, resolveContext?: ResolveContext): string | false,
 * }} ResolveFunction
 */

/**
 * @typedef {{
 * (context: Context, parent: string | URL, specifier: string | URL, resolveContext?: ResolveContext): Promise<string | false>,
 * (parent: string | URL, specifier: string | URL, resolveContext?: ResolveContext): Promise<string | false>,
 * }} ResolveFunctionPromise
 */

const getCachedFileSystem = memoize(() => require("./CachedInputFileSystem"));

const getNodeFileSystem = memoize(() => {
	const fs = require("graceful-fs");

	const CachedInputFileSystem = getCachedFileSystem();

	return new CachedInputFileSystem(fs, 4000);
});
const getNodeContext = memoize(() => ({
	environments: ["node+es3+es5+process+native"],
}));

const getResolverFactory = memoize(() => require("./ResolverFactory"));

const getAsyncResolver = memoize(() =>
	getResolverFactory().createResolver({
		conditionNames: ["node"],
		extensions: [".js", ".json", ".node"],
		fileSystem: getNodeFileSystem(),
	}),
);

/**
 * @type {ResolveFunctionAsync}
 */
const resolve =
	/**
	 * @param {object | string | URL} context context
	 * @param {string | URL} parent parent path
	 * @param {string | URL | ResolveContext | ResolveCallback} specifier specifier to resolve
	 * @param {ResolveContext | ResolveCallback=} resolveContext resolve context
	 * @param {ResolveCallback=} callback callback
	 */
	(context, parent, specifier, resolveContext, callback) => {
		if (typeof context === "string" || context instanceof URL) {
			callback = /** @type {ResolveCallback} */ (resolveContext);
			resolveContext = /** @type {ResolveContext} */ (specifier);
			specifier = parent;
			parent = context;
			context = getNodeContext();
		}
		if (typeof callback !== "function") {
			callback = /** @type {ResolveCallback} */ (resolveContext);
		}
		getAsyncResolver().resolve(
			context,
			parent,
			/** @type {string} */ (specifier),
			/** @type {ResolveContext} */ (resolveContext),
			/** @type {ResolveCallback} */ (callback),
		);
	};

const getSyncResolver = memoize(() =>
	getResolverFactory().createResolver({
		conditionNames: ["node"],
		extensions: [".js", ".json", ".node"],
		useSyncFileSystemCalls: true,
		fileSystem: getNodeFileSystem(),
	}),
);

/**
 * @type {ResolveFunction}
 */
const resolveSync =
	/**
	 * @param {object | string | URL} context context
	 * @param {string | URL} parent parent path
	 * @param {string | URL | ResolveContext | undefined} specifier specifier to resolve
	 * @param {ResolveContext=} resolveContext resolve context
	 * @returns {string | false} resolved path
	 */
	(context, parent, specifier, resolveContext) => {
		if (typeof context === "string" || context instanceof URL) {
			resolveContext = /** @type {ResolveContext} */ (specifier);
			specifier = parent;
			parent = context;
			context = getNodeContext();
		}
		return getSyncResolver().resolveSync(
			context,
			parent,
			/** @type {string} */ (specifier),
			/** @type {ResolveContext} */ (resolveContext),
		);
	};

/**
 * @type {ResolveFunctionPromise}
 */
const resolvePromise =
	/**
	 * @param {object | string | URL} context context
	 * @param {string | URL} parent parent path
	 * @param {string | URL | ResolveContext | undefined} specifier specifier to resolve
	 * @param {ResolveContext=} resolveContext resolve context
	 * @returns {Promise<string | false>} resolved path
	 */
	(context, parent, specifier, resolveContext) => {
		if (typeof context === "string" || context instanceof URL) {
			resolveContext = /** @type {ResolveContext} */ (specifier);
			specifier = parent;
			parent = context;
			context = getNodeContext();
		}
		return getAsyncResolver().resolvePromise(
			context,
			parent,
			/** @type {string} */ (specifier),
			/** @type {ResolveContext} */ (resolveContext),
		);
	};

/** @typedef {Omit<ResolveOptions, "fileSystem"> & Partial<Pick<ResolveOptions, "fileSystem">>} ResolveOptionsOptionalFS */

/**
 * @param {ResolveOptionsOptionalFS} options Resolver options
 * @returns {ResolveFunctionAsync} Resolver function
 */
function create(options) {
	const resolver = getResolverFactory().createResolver({
		...options,
		fileSystem: options.fileSystem || getNodeFileSystem(),
	});
	/**
	 * @param {object | string | URL} context Custom context
	 * @param {string | URL} parent Base/parent path
	 * @param {string | URL | ResolveContext | ResolveCallback} specifier Specifier to resolve
	 * @param {ResolveContext | ResolveCallback=} resolveContext Resolve context
	 * @param {ResolveCallback=} callback Result callback
	 */
	return function create(context, parent, specifier, resolveContext, callback) {
		if (typeof context === "string" || context instanceof URL) {
			callback = /** @type {ResolveCallback} */ (resolveContext);
			resolveContext = /** @type {ResolveContext} */ (specifier);
			specifier = parent;
			parent = context;
			context = getNodeContext();
		}
		if (typeof callback !== "function") {
			callback = /** @type {ResolveCallback} */ (resolveContext);
		}
		resolver.resolve(
			context,
			parent,
			/** @type {string} */ (specifier),
			/** @type {ResolveContext} */ (resolveContext),
			callback,
		);
	};
}

/**
 * @param {ResolveOptionsOptionalFS} options Resolver options
 * @returns {ResolveFunction} Resolver function
 */
function createSync(options) {
	const resolver = getResolverFactory().createResolver({
		useSyncFileSystemCalls: true,
		...options,
		fileSystem: options.fileSystem || getNodeFileSystem(),
	});
	/**
	 * @param {object | string | URL} context custom context
	 * @param {string | URL} parent base/parent path
	 * @param {string | URL | ResolveContext | undefined} specifier specifier to resolve
	 * @param {ResolveContext=} resolveContext Resolve context
	 * @returns {string | false} Resolved path or false
	 */
	return function createSync(context, parent, specifier, resolveContext) {
		if (typeof context === "string" || context instanceof URL) {
			resolveContext = /** @type {ResolveContext} */ (specifier);
			specifier = parent;
			parent = context;
			context = getNodeContext();
		}
		return resolver.resolveSync(
			context,
			parent,
			/** @type {string} */ (specifier),
			/** @type {ResolveContext} */ (resolveContext),
		);
	};
}

/**
 * @param {ResolveOptionsOptionalFS} options Resolver options
 * @returns {ResolveFunctionPromise} Resolver function
 */
function createPromise(options) {
	const resolver = getResolverFactory().createResolver({
		...options,
		fileSystem: options.fileSystem || getNodeFileSystem(),
	});
	/**
	 * @param {object | string | URL} context Custom context
	 * @param {string | URL} parent Base/parent path
	 * @param {string | URL | ResolveContext | undefined} specifier Specifier to resolve
	 * @param {ResolveContext=} resolveContext Resolve context
	 * @returns {Promise<string | false>} resolved path
	 */
	return function createPromise(context, parent, specifier, resolveContext) {
		if (typeof context === "string" || context instanceof URL) {
			resolveContext = /** @type {ResolveContext} */ (specifier);
			specifier = parent;
			parent = context;
			context = getNodeContext();
		}
		return resolver.resolvePromise(
			context,
			parent,
			/** @type {string} */ (specifier),
			/** @type {ResolveContext} */ (resolveContext),
		);
	};
}

/**
 * @template A
 * @template B
 * @param {A} obj input a
 * @param {B} exports input b
 * @returns {A & B} merged
 */
const mergeExports = (obj, exports) => {
	const descriptors = Object.getOwnPropertyDescriptors(exports);
	Object.defineProperties(obj, descriptors);
	return /** @type {A & B} */ (Object.freeze(obj));
};

module.exports = mergeExports(resolve, {
	get sync() {
		return resolveSync;
	},
	get promise() {
		return resolvePromise;
	},
	create: mergeExports(create, {
		get sync() {
			return createSync;
		},
		get promise() {
			return createPromise;
		},
	}),
	get ResolverFactory() {
		return getResolverFactory();
	},
	get CachedInputFileSystem() {
		return getCachedFileSystem();
	},
	get CloneBasenamePlugin() {
		return require("./CloneBasenamePlugin");
	},
	get LogInfoPlugin() {
		return require("./LogInfoPlugin");
	},
	get TsconfigPathsPlugin() {
		return require("./TsconfigPathsPlugin");
	},
	get forEachBail() {
		return require("./forEachBail");
	},
});
