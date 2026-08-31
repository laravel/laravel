/*
	MIT License http://www.opensource.org/licenses/mit-license.php
	Author Natsu @xiaoxiaojx
*/

"use strict";

const { aliasResolveHandler, compileAliasOptions } = require("./AliasUtils");
const { modulesResolveHandler } = require("./ModulesUtils");
const { readJson } = require("./util/fs");
const { PathType: _PathType, isSubPath, normalize } = require("./util/path");

/** @typedef {import("./Resolver")} Resolver */
/** @typedef {import("./Resolver").ResolveStepHook} ResolveStepHook */
/** @typedef {import("./AliasUtils").AliasOption} AliasOption */
/** @typedef {import("./Resolver").ResolveRequest} ResolveRequest */
/** @typedef {import("./Resolver").ResolveContext} ResolveContext */
/** @typedef {import("./Resolver").FileSystem} FileSystem */
/** @typedef {import("./Resolver").TsconfigPathsData} TsconfigPathsData */
/** @typedef {import("./Resolver").TsconfigPathsMap} TsconfigPathsMap */
/** @typedef {import("./ResolverFactory").TsconfigOptions} TsconfigOptions */

// Sentinel stored in `_contextSelectionCache` for `requestPath`s whose
// scan returned `null` ("no context matched"). Using a non-null marker
// lets the cache-hit path be a single `Map.get()` — we distinguish
// "cached null" from "not cached yet" without a second `has` lookup.
const NULL_CONTEXT = Symbol("NULL_CONTEXT");

/**
 * Per-`TsconfigPathsMap` memoization of `_selectPathsDataForContext`.
 *
 * Real-world builds resolve hundreds of requests per source file (every
 * import in the file), and webpack-style resolvers walk the same
 * `requestPath` (= source-file directory) for each one. Without this
 * cache every resolve re-scans the full `contextList` even though the
 * answer is invariant for a given `(map, requestPath)` pair.
 *
 * The outer key is the `TsconfigPathsMap` itself — rebuilt on every
 * tsconfig change — so a `WeakMap` lets the inner map be collected
 * automatically once the map goes away. The inner Map is keyed by
 * `requestPath` (string); a `Symbol` sentinel stands in for "no
 * matching context" so `Map.get` alone distinguishes the three states
 * (cached data / cached null / not cached).
 * @type {WeakMap<TsconfigPathsMap, Map<string, TsconfigPathsData | typeof NULL_CONTEXT>>}
 */
const _contextSelectionCache = new WeakMap();

/**
 * @typedef {object} TsconfigCompilerOptions
 * @property {string=} baseUrl Base URL for resolving paths
 * @property {{ [key: string]: string[] }=} paths TypeScript paths mapping
 */

/**
 * @typedef {object} TsconfigReference
 * @property {string} path Path to the referenced project
 */

/**
 * @typedef {object} Tsconfig
 * @property {TsconfigCompilerOptions=} compilerOptions Compiler options
 * @property {string | string[]=} extends Extended configuration paths
 * @property {TsconfigReference[]=} references Project references
 */

const DEFAULT_CONFIG_FILE = "tsconfig.json";

const READ_JSON_OPTIONS = { stripComments: true };

// Trailing `/*` or `\*` segment of a tsconfig `paths` mapping (e.g.
// `./src/*` → `./src`). Hoisted so we don't allocate a fresh regex per
// path entry on every tsconfig load — and so the same regex object can be
// reused for the matching `test` + `replace` pair below.
const WILDCARD_TAIL_RE = /[/\\]\*$/;

/**
 * @param {string} pattern Path pattern
 * @returns {number} Length of the prefix
 */
function getPrefixLength(pattern) {
	const prefixLength = pattern.indexOf("*");
	if (prefixLength === -1) {
		return pattern.length;
	}
	return prefixLength;
}

/**
 * Sort path patterns.
 * If a module name can be matched with multiple patterns then pattern with the longest prefix will be picked.
 * @param {string[]} arr Array of path patterns
 * @returns {string[]} Array of path patterns sorted by longest prefix
 */
function sortByLongestPrefix(arr) {
	return [...arr].sort((a, b) => getPrefixLength(b) - getPrefixLength(a));
}

/**
 * Merge two tsconfig objects
 * @param {Tsconfig | null} base base config
 * @param {Tsconfig | null} config config to merge
 * @returns {Tsconfig} merged config
 */
function mergeTsconfigs(base, config) {
	base = base || {};
	config = config || {};

	return {
		...base,
		...config,
		compilerOptions: {
			.../** @type {TsconfigCompilerOptions} */ (base.compilerOptions),
			.../** @type {TsconfigCompilerOptions} */ (config.compilerOptions),
		},
	};
}

/**
 * Substitute ${configDir} template variable in path
 * @param {string} pathValue the path value
 * @param {string} configDir the config directory
 * @returns {string} the path with substituted template
 */
function substituteConfigDir(pathValue, configDir) {
	// eslint-disable-next-line no-template-curly-in-string
	if (!pathValue.includes("${configDir}")) return pathValue;
	return pathValue.replace(/\$\{configDir\}/g, configDir);
}

/**
 * Convert tsconfig paths to resolver options
 * @param {string} configDir Config file directory
 * @param {{ [key: string]: string[] }} paths TypeScript paths mapping
 * @param {Resolver} resolver resolver instance
 * @param {string=} baseUrl Base URL for resolving paths (relative to configDir)
 * @returns {TsconfigPathsData} the resolver options
 */
function tsconfigPathsToResolveOptions(configDir, paths, resolver, baseUrl) {
	// Calculate absolute base URL
	const absoluteBaseUrl = !baseUrl
		? configDir
		: resolver.join(configDir, baseUrl);

	/** @type {string[]} */
	const sortedKeys = sortByLongestPrefix(Object.keys(paths));
	/** @type {AliasOption[]} */
	const alias = [];
	/** @type {string[]} */
	const modules = [];

	for (const pattern of sortedKeys) {
		const mappings = paths[pattern];
		// Substitute ${configDir} in path mappings
		const absolutePaths = mappings.map((mapping) => {
			const substituted = substituteConfigDir(mapping, configDir);
			return resolver.join(absoluteBaseUrl, substituted);
		});

		if (absolutePaths.length > 0) {
			if (pattern === "*") {
				// Pull `dir/*` entries directly into `modules` with their
				// trailing wildcard stripped, skipping anything else. The
				// previous `.map(...).filter(Boolean)` form allocated two
				// throwaway arrays plus a spread iterator per `*` mapping.
				for (let j = 0; j < absolutePaths.length; j++) {
					const dir = absolutePaths[j];
					if (WILDCARD_TAIL_RE.test(dir)) {
						modules.push(dir.replace(WILDCARD_TAIL_RE, ""));
					}
				}
			} else {
				alias.push({ name: pattern, alias: absolutePaths });
			}
		}
	}

	if (baseUrl && absoluteBaseUrl && !modules.includes(absoluteBaseUrl)) {
		modules.push(absoluteBaseUrl);
	}

	return {
		alias: compileAliasOptions(resolver, alias),
		modules,
	};
}

/**
 * Get the base context for the current project
 * @param {string} context the context
 * @param {Resolver} resolver resolver instance
 * @param {string=} baseUrl base URL for resolving paths
 * @returns {string} the base context
 */
function getAbsoluteBaseUrl(context, resolver, baseUrl) {
	return !baseUrl ? context : resolver.join(context, baseUrl);
}

/**
 * @param {TsconfigPathsData} main main paths data
 * @param {string} mainContext main context
 * @param {{ [baseUrl: string]: TsconfigPathsData }} refs references map
 * @param {Set<string>} fileDependencies file dependencies
 * @returns {TsconfigPathsMap} the tsconfig paths map
 */
function buildTsconfigPathsMap(main, mainContext, refs, fileDependencies) {
	const allContexts = /** @type {{ [context: string]: TsconfigPathsData }} */ ({
		[mainContext]: main,
		...refs,
	});
	// Precompute the key list once per tsconfig load. `_selectPathsDataForContext`
	// runs per resolve and otherwise would call `Object.entries(allContexts)`
	// each time, allocating a fresh [key, value][] array.
	const contextList = Object.keys(allContexts);
	return {
		main,
		mainContext,
		refs,
		allContexts,
		contextList,
		fileDependencies,
	};
}

module.exports = class TsconfigPathsPlugin {
	/**
	 * @param {true | string | TsconfigOptions} configFileOrOptions tsconfig file path or options object
	 */
	constructor(configFileOrOptions) {
		if (
			typeof configFileOrOptions === "object" &&
			configFileOrOptions !== null
		) {
			// Options object format
			const { configFile } = configFileOrOptions;
			/** @type {boolean} */
			this.isAutoConfigFile = typeof configFile !== "string";
			/** @type {string} */
			this.configFile = this.isAutoConfigFile
				? DEFAULT_CONFIG_FILE
				: /** @type {string} */ (configFile);
			/** @type {string[] | "auto"} */
			if (Array.isArray(configFileOrOptions.references)) {
				/** @type {TsconfigReference[] | "auto"} */
				this.references = configFileOrOptions.references.map((ref) => ({
					path: ref,
				}));
			} else if (configFileOrOptions.references === "auto") {
				this.references = "auto";
			} else {
				this.references = [];
			}
			/** @type {string | undefined} */
			this.baseUrl = configFileOrOptions.baseUrl;
		} else {
			/** @type {boolean} */
			this.isAutoConfigFile = configFileOrOptions === true;
			/** @type {string} */
			this.configFile = this.isAutoConfigFile
				? DEFAULT_CONFIG_FILE
				: /** @type {string} */ (configFileOrOptions);
			/** @type {TsconfigReference[] | "auto"} */
			this.references = [];
			/** @type {string | undefined} */
			this.baseUrl = undefined;
		}
	}

	/**
	 * @param {Resolver} resolver the resolver
	 * @returns {void}
	 */
	apply(resolver) {
		const aliasTarget = resolver.ensureHook("internal-resolve");
		const moduleTarget = resolver.ensureHook("module");

		resolver
			.getHook("raw-resolve")
			.tapAsync("TsconfigPathsPlugin", (request, resolveContext, callback) => {
				this._getTsconfigPathsMap(
					resolver,
					request,
					resolveContext,
					(err, tsconfigPathsMap) => {
						if (err) return callback(err);
						if (!tsconfigPathsMap) return callback();

						const selectedData = this._selectPathsDataForContext(
							request.path,
							tsconfigPathsMap,
						);

						if (!selectedData) return callback();

						aliasResolveHandler(
							resolver,
							selectedData.alias,
							aliasTarget,
							request,
							resolveContext,
							(err, result) => {
								if (err) return callback(err);
								if (result) return callback(null, result);
								// https://github.com/webpack/webpack/issues/20944
								// Unlike resolve.alias, tsconfig paths should fall through
								// when a pattern matches but the mapped path does not exist
								// (matching TypeScript's native resolution behavior).
								return callback();
							},
						);
					},
				);
			});

		resolver
			.getHook("raw-module")
			.tapAsync("TsconfigPathsPlugin", (request, resolveContext, callback) => {
				this._getTsconfigPathsMap(
					resolver,
					request,
					resolveContext,
					(err, tsconfigPathsMap) => {
						if (err) return callback(err);
						if (!tsconfigPathsMap) return callback();

						const selectedData = this._selectPathsDataForContext(
							request.path,
							tsconfigPathsMap,
						);

						if (!selectedData) return callback();

						modulesResolveHandler(
							resolver,
							selectedData.modules,
							moduleTarget,
							request,
							resolveContext,
							callback,
						);
					},
				);
			});
	}

	/**
	 * Get TsconfigPathsMap for the request (with caching)
	 * @param {Resolver} resolver the resolver
	 * @param {ResolveRequest} request the request
	 * @param {ResolveContext} resolveContext the resolve context
	 * @param {(err: Error | null, result?: TsconfigPathsMap | null) => void} callback the callback
	 * @returns {void}
	 */
	_getTsconfigPathsMap(resolver, request, resolveContext, callback) {
		if (typeof request.tsconfigPathsMap !== "undefined") {
			const cached = request.tsconfigPathsMap;
			if (!cached) return callback(null, null);
			if (resolveContext.fileDependencies) {
				for (const fileDependency of cached.fileDependencies) {
					resolveContext.fileDependencies.add(fileDependency);
				}
			}
			return callback(null, cached);
		}

		if (this.isAutoConfigFile) {
			this._findTsconfigUpward(
				resolver,
				request.path || process.cwd(),
				(err, result) => {
					if (err) {
						request.tsconfigPathsMap = null;
						return callback(err);
					}
					if (!result) {
						request.tsconfigPathsMap = null;
						return callback(null, null);
					}
					const map = /** @type {TsconfigPathsMap} */ (result);
					request.tsconfigPathsMap = map;
					if (resolveContext.fileDependencies) {
						for (const fileDependency of map.fileDependencies) {
							resolveContext.fileDependencies.add(fileDependency);
						}
					}
					callback(null, map);
				},
			);
			return;
		}

		const absTsconfigPath = resolver.join(
			request.path || process.cwd(),
			this.configFile,
		);
		this._loadTsconfigPathsMap(resolver, absTsconfigPath, (err, result) => {
			if (err) {
				request.tsconfigPathsMap = null;
				return callback(err);
			}

			const map = /** @type {TsconfigPathsMap} */ (result);
			request.tsconfigPathsMap = map;
			if (resolveContext.fileDependencies) {
				for (const fileDependency of map.fileDependencies) {
					resolveContext.fileDependencies.add(fileDependency);
				}
			}
			callback(null, map);
		});
	}

	/**
	 * Walk up from startDir to the filesystem root looking for tsconfig.json.
	 * Like TypeScript's own `findConfigFile` / `forEachAncestorDirectory`.
	 * @param {Resolver} resolver the resolver
	 * @param {string} startDir the directory to start searching from
	 * @param {(err: Error | null, result?: TsconfigPathsMap | null) => void} callback the callback
	 * @returns {void}
	 */
	_findTsconfigUpward(resolver, startDir, callback) {
		const { fileSystem } = resolver;
		const configFileName = this.configFile;

		/**
		 * @param {string} dir current directory
		 */
		const check = (dir) => {
			const candidate = resolver.join(dir, configFileName);
			fileSystem.stat(candidate, (statErr) => {
				if (!statErr) {
					// Found — load it
					this._loadTsconfigPathsMap(resolver, candidate, (loadErr, result) => {
						if (loadErr) return callback(loadErr);
						callback(null, result);
					});
					return;
				}
				// Not found — move to parent
				const parentDir = resolver.dirname(dir);
				if (parentDir === dir) {
					// Reached filesystem root, no tsconfig.json found
					return callback(null, null);
				}
				check(parentDir);
			});
		};

		check(startDir);
	}

	/**
	 * Load tsconfig.json and build complete TsconfigPathsMap
	 * Includes main project paths and all referenced projects
	 * @param {Resolver} resolver the resolver
	 * @param {string} absTsconfigPath absolute path to tsconfig.json
	 * @param {(err: Error | null, result?: TsconfigPathsMap) => void} callback the callback
	 * @returns {void}
	 */
	_loadTsconfigPathsMap(resolver, absTsconfigPath, callback) {
		/** @type {Set<string>} */
		const fileDependencies = new Set();

		this._loadTsconfig(
			resolver,
			absTsconfigPath,
			fileDependencies,
			undefined,
			(err, config) => {
				if (err) return callback(err);

				const cfg = /** @type {Tsconfig} */ (config);
				const compilerOptions = cfg.compilerOptions || {};
				const mainContext = resolver.dirname(absTsconfigPath);

				const baseUrl =
					this.baseUrl !== undefined ? this.baseUrl : compilerOptions.baseUrl;

				const main = tsconfigPathsToResolveOptions(
					mainContext,
					compilerOptions.paths || {},
					resolver,
					baseUrl,
				);
				/** @type {{ [baseUrl: string]: TsconfigPathsData }} */
				const refs = {};

				let referencesToUse = null;
				if (this.references === "auto") {
					referencesToUse = cfg.references;
				} else if (Array.isArray(this.references)) {
					referencesToUse = this.references;
				}

				if (!Array.isArray(referencesToUse)) {
					return callback(
						null,
						buildTsconfigPathsMap(main, mainContext, refs, fileDependencies),
					);
				}

				this._loadTsconfigReferences(
					resolver,
					mainContext,
					referencesToUse,
					fileDependencies,
					refs,
					(refErr) => {
						if (refErr) return callback(refErr);
						callback(
							null,
							buildTsconfigPathsMap(main, mainContext, refs, fileDependencies),
						);
					},
				);
			},
		);
	}

	/**
	 * Select the correct TsconfigPathsData based on request.path (context-aware)
	 * Matches the behavior of tsconfig-paths-webpack-plugin
	 * @param {string | false} requestPath the request path
	 * @param {TsconfigPathsMap} tsconfigPathsMap the tsconfig paths map
	 * @returns {TsconfigPathsData | null} the selected paths data
	 */
	_selectPathsDataForContext(requestPath, tsconfigPathsMap) {
		const { main, allContexts, contextList } = tsconfigPathsMap;
		if (!requestPath) {
			return main;
		}
		// Single-context tsconfigs (no project references) hit the loop
		// below at most once; in that case the cache lookup costs more
		// than the loop itself. Only memoize when there are 2+ contexts
		// — that's the monorepo / project-references shape where the
		// scan actually walks multiple entries per resolve and the
		// `(map, requestPath)` answer can be reused.
		/** @type {Map<string, TsconfigPathsData | typeof NULL_CONTEXT> | undefined} */
		let perMap;
		if (contextList.length >= 2) {
			perMap = _contextSelectionCache.get(tsconfigPathsMap);
			if (perMap !== undefined) {
				const cached = perMap.get(requestPath);
				if (cached !== undefined) {
					return cached === NULL_CONTEXT
						? null
						: /** @type {TsconfigPathsData} */ (cached);
				}
			} else {
				perMap = new Map();
				_contextSelectionCache.set(tsconfigPathsMap, perMap);
			}
		}
		let longestMatchContext = null;
		let longestMatchLength = 0;
		// Iterate the pre-computed key list (the previous
		// `Object.entries(allContexts)` form allocated a fresh
		// `[key, value][]` per resolve). Defer the `allContexts[context]`
		// lookup to after we know the context actually matches — non-matches
		// are the common case and don't need the property access.
		for (let i = 0; i < contextList.length; i++) {
			const context = contextList[i];
			if (context === requestPath) {
				const exact = allContexts[context];
				if (perMap !== undefined) perMap.set(requestPath, exact);
				return exact;
			}
			// Cheap integer-compare gate first: a context can only beat the
			// current longest match if its own length is strictly greater.
			// Skipping `isSubPath` (a `startsWith` + char-code probe) when the
			// length already disqualifies the candidate avoids the per-resolve
			// scan over every shorter context.
			if (
				context.length > longestMatchLength &&
				isSubPath(context, requestPath)
			) {
				longestMatchContext = context;
				longestMatchLength = context.length;
			}
		}
		const result =
			longestMatchContext === null ? null : allContexts[longestMatchContext];
		if (perMap !== undefined) {
			perMap.set(requestPath, result === null ? NULL_CONTEXT : result);
		}
		return result;
	}

	/**
	 * Load tsconfig from extends path
	 * @param {Resolver} resolver the resolver
	 * @param {string} configFilePath current config file path
	 * @param {string} extendedConfigValue extends value
	 * @param {Set<string>} fileDependencies the file dependencies
	 * @param {Set<string>} visitedConfigPaths config paths being loaded (for circular extends detection)
	 * @param {(err: Error | null, result?: Tsconfig) => void} callback callback
	 * @returns {void}
	 */
	_loadTsconfigFromExtends(
		resolver,
		configFilePath,
		extendedConfigValue,
		fileDependencies,
		visitedConfigPaths,
		callback,
	) {
		const { fileSystem } = resolver;
		const currentDir = resolver.dirname(configFilePath);

		// Substitute ${configDir} in extends path
		extendedConfigValue = substituteConfigDir(extendedConfigValue, currentDir);

		// Remember the original value before potentially appending .json
		const originalExtendedConfigValue = extendedConfigValue;

		if (
			typeof extendedConfigValue === "string" &&
			!extendedConfigValue.includes(".json")
		) {
			extendedConfigValue += ".json";
		}

		const initialExtendedConfigPath = resolver.join(
			currentDir,
			extendedConfigValue,
		);

		/**
		 * @param {string} extendedConfigPath resolved config path to load
		 */
		const loadExtended = (extendedConfigPath) => {
			this._loadTsconfig(
				resolver,
				extendedConfigPath,
				fileDependencies,
				visitedConfigPaths,
				(err, config) => {
					if (err) return callback(err);

					const cfg = /** @type {Tsconfig} */ (config);
					const compilerOptions = cfg.compilerOptions || {
						baseUrl: undefined,
					};

					if (compilerOptions.baseUrl) {
						const extendedConfigDir = resolver.dirname(extendedConfigPath);
						compilerOptions.baseUrl = getAbsoluteBaseUrl(
							extendedConfigDir,
							resolver,
							compilerOptions.baseUrl,
						);
					}

					delete cfg.references;

					callback(null, cfg);
				},
			);
		};

		fileSystem.stat(initialExtendedConfigPath, (existsErr) => {
			if (!existsErr) return loadExtended(initialExtendedConfigPath);

			// The relative form does not exist — treat the value as a package
			// specifier and compute its `node_modules/<...>` sub-path.
			let nodeModulesSubPath = null;
			if (
				typeof originalExtendedConfigValue === "string" &&
				originalExtendedConfigValue.startsWith("@") &&
				originalExtendedConfigValue.split("/").length === 2
			) {
				// Scoped package, no sub-path ("@scope/name") →
				// node_modules/@scope/name/tsconfig.json (not @scope/name.json).
				// See: test/fixtures/tsconfig-paths/extends-pkg-entry/
				nodeModulesSubPath = `${originalExtendedConfigValue}/${DEFAULT_CONFIG_FILE}`;
			} else if (extendedConfigValue.includes("/")) {
				// Package sub-path ("react/tsconfig", "@scope/name/tsconfig") →
				// node_modules/react/tsconfig.json.
				// See: test/fixtures/tsconfig-paths/extends-npm/
				nodeModulesSubPath = extendedConfigValue;
			} else if (
				!originalExtendedConfigValue.startsWith(".") &&
				!originalExtendedConfigValue.startsWith("/")
			) {
				// Unscoped package, no sub-path ("my-base-config") →
				// node_modules/my-base-config/tsconfig.json.
				nodeModulesSubPath = `${originalExtendedConfigValue}/${DEFAULT_CONFIG_FILE}`;
			}

			// Not a package specifier (relative/absolute) — load the missing
			// path anyway so the read surfaces its own ENOENT.
			if (nodeModulesSubPath === null) {
				return loadExtended(initialExtendedConfigPath);
			}

			// Walk ancestor node_modules like Node.js/TypeScript so a package
			// hoisted to a parent workspace directory is found. #21457
			const subPath = normalize(`node_modules/${nodeModulesSubPath}`);
			this._findExtendsInNodeModules(resolver, currentDir, subPath, (found) => {
				loadExtended(found || resolver.join(currentDir, subPath));
			});
		});
	}

	/**
	 * Walk up from startDir looking for `<dir>/<subPath>` (a
	 * `node_modules/...` sub-path), matching Node.js module resolution so a
	 * package hoisted to a parent workspace's node_modules is found.
	 * @param {Resolver} resolver the resolver
	 * @param {string} startDir directory to start searching from
	 * @param {string} subPath node_modules-relative sub-path to look for
	 * @param {(found: string | null) => void} callback receives the found path or null
	 * @returns {void}
	 */
	_findExtendsInNodeModules(resolver, startDir, subPath, callback) {
		const { fileSystem } = resolver;

		/**
		 * @param {string} dir current directory
		 */
		const check = (dir) => {
			const candidate = resolver.join(dir, subPath);
			fileSystem.stat(candidate, (statErr) => {
				if (!statErr) return callback(candidate);
				const parentDir = resolver.dirname(dir);
				if (parentDir === dir) return callback(null);
				check(parentDir);
			});
		};

		check(startDir);
	}

	/**
	 * Load referenced tsconfig projects and store in referenceMatchMap
	 * Simple implementation matching tsconfig-paths-webpack-plugin:
	 * Just load each reference and store independently
	 * @param {Resolver} resolver the resolver
	 * @param {string} context the context
	 * @param {TsconfigReference[]} references array of references
	 * @param {Set<string>} fileDependencies the file dependencies
	 * @param {{ [baseUrl: string]: TsconfigPathsData }} referenceMatchMap the map to populate
	 * @param {(err: Error | null) => void} callback callback
	 * @param {Set<string>=} visitedRefPaths visited reference config paths (for circular reference detection)
	 * @returns {void}
	 */
	_loadTsconfigReferences(
		resolver,
		context,
		references,
		fileDependencies,
		referenceMatchMap,
		callback,
		visitedRefPaths,
	) {
		if (references.length === 0) return callback(null);

		const visited = visitedRefPaths || new Set();
		let pending = references.length;
		const finishOne = () => {
			if (--pending === 0) callback(null);
		};

		for (const ref of references) {
			const refPath = substituteConfigDir(ref.path, context);
			const refConfigPath = resolver.join(
				resolver.join(context, refPath),
				DEFAULT_CONFIG_FILE,
			);

			if (visited.has(refConfigPath)) {
				finishOne();
				continue;
			}
			visited.add(refConfigPath);

			this._loadTsconfig(
				resolver,
				refConfigPath,
				fileDependencies,
				undefined,
				(err, refConfig) => {
					// Failures are swallowed to match tsconfig-paths-webpack-plugin:
					// a broken reference must not abort the main project's resolution.
					if (err) return finishOne();

					const cfg = /** @type {Tsconfig} */ (refConfig);
					if (cfg.compilerOptions && cfg.compilerOptions.paths) {
						const refContext = resolver.dirname(refConfigPath);

						referenceMatchMap[refContext] = tsconfigPathsToResolveOptions(
							refContext,
							cfg.compilerOptions.paths || {},
							resolver,
							cfg.compilerOptions.baseUrl,
						);
					}

					if (this.references === "auto" && Array.isArray(cfg.references)) {
						this._loadTsconfigReferences(
							resolver,
							resolver.dirname(refConfigPath),
							cfg.references,
							fileDependencies,
							referenceMatchMap,
							finishOne,
							visited,
						);
					} else {
						finishOne();
					}
				},
			);
		}
	}

	/**
	 * Load tsconfig.json with extends support
	 * @param {Resolver} resolver the resolver
	 * @param {string} configFilePath absolute path to tsconfig.json
	 * @param {Set<string>} fileDependencies the file dependencies
	 * @param {Set<string> | undefined} visitedConfigPaths config paths being loaded (for circular extends detection)
	 * @param {(err: Error | null, result?: Tsconfig) => void} callback callback
	 * @returns {void}
	 */
	_loadTsconfig(
		resolver,
		configFilePath,
		fileDependencies,
		visitedConfigPaths,
		callback,
	) {
		const visited = visitedConfigPaths || new Set();

		if (visited.has(configFilePath)) {
			return callback(null, /** @type {Tsconfig} */ ({}));
		}
		visited.add(configFilePath);

		readJson(
			resolver.fileSystem,
			configFilePath,
			READ_JSON_OPTIONS,
			(err, parsed) => {
				if (err) return callback(/** @type {Error} */ (err));

				const config = /** @type {Tsconfig} */ (parsed);
				fileDependencies.add(configFilePath);

				const extendedConfig = config.extends;
				if (!extendedConfig) return callback(null, config);

				if (!Array.isArray(extendedConfig)) {
					this._loadTsconfigFromExtends(
						resolver,
						configFilePath,
						extendedConfig,
						fileDependencies,
						visited,
						(extErr, extendedTsconfig) => {
							if (extErr) return callback(extErr);
							callback(
								null,
								mergeTsconfigs(
									/** @type {Tsconfig} */ (extendedTsconfig),
									config,
								),
							);
						},
					);
					return;
				}

				/** @type {Tsconfig} */
				let base = {};
				let i = 0;
				const next = () => {
					if (i >= extendedConfig.length) {
						return callback(null, mergeTsconfigs(base, config));
					}
					this._loadTsconfigFromExtends(
						resolver,
						configFilePath,
						extendedConfig[i++],
						fileDependencies,
						visited,
						(extErr, extendedTsconfig) => {
							if (extErr) return callback(extErr);
							base = mergeTsconfigs(
								base,
								/** @type {Tsconfig} */ (extendedTsconfig),
							);
							next();
						},
					);
				};
				next();
			},
		);
	}
};
