/*
	MIT License http://www.opensource.org/licenses/mit-license.php
	Author Tobias Koppers @sokra
*/

"use strict";

const { AsyncSeriesBailHook, AsyncSeriesHook, SyncHook } = require("tapable");
const createInnerContext = require("./createInnerContext");
const { parseIdentifier } = require("./util/identifier");
const {
	PathType,
	createCachedBasename,
	createCachedDirname,
	createCachedJoin,
	getType,
	normalize,
	toPath,
} = require("./util/path");

/* eslint-disable jsdoc/check-alignment */
// TODO in the next major release use only `Promise.withResolvers()`
const _withResolvers =
	// eslint-disable-next-line n/no-unsupported-features/es-syntax
	Promise.withResolvers
		? /**
			 * @param {Resolver} self resolver
			 * @param {Context} context context information object
			 * @param {string | URL} path context path or a `file:` URL instance
			 * @param {string | URL} request request string or a `file:` URL instance
			 * @param {ResolveContext} resolveContext resolve context
			 * @returns {Promise<string | false>} result
			 */
			(self, context, path, request, resolveContext) => {
				// eslint-disable-next-line n/no-unsupported-features/es-syntax
				const { promise, resolve, reject } = Promise.withResolvers();
				self.resolve(context, path, request, resolveContext, (err, res) => {
					if (err) reject(err);
					else resolve(/** @type {string | false} */ (res));
				});
				return promise;
			}
		: /**
			 * @param {Resolver} self resolver
			 * @param {Context} context context information object
			 * @param {string | URL} path context path or a `file:` URL instance
			 * @param {string | URL} request request string or a `file:` URL instance
			 * @param {ResolveContext} resolveContext resolve context
			 * @returns {Promise<string | false>} result
			 */
			(self, context, path, request, resolveContext) =>
				new Promise((resolve, reject) => {
					self.resolve(context, path, request, resolveContext, (err, res) => {
						if (err) reject(err);
						else resolve(/** @type {string | false} */ (res));
					});
				});
/* eslint-enable jsdoc/check-alignment */

/** @typedef {import("./AliasUtils").AliasOption} AliasOption */
/** @typedef {import("./util/path").CachedJoin} CachedJoin */
/** @typedef {import("./util/path").CachedDirname} CachedDirname */
/** @typedef {import("./util/path").CachedBasename} CachedBasename */

/**
 * @typedef {object} JoinCacheEntry
 * @property {CachedJoin["fn"]} fn cached join function
 * @property {CachedJoin["cache"]} cache the underlying cache map
 */

/**
 * @typedef {object} DirnameCacheEntry
 * @property {CachedDirname["fn"]} fn cached dirname function
 * @property {CachedDirname["cache"]} cache the underlying cache map
 */

/**
 * @typedef {object} BasenameCacheEntry
 * @property {CachedBasename["fn"]} fn cached dirname function
 * @property {CachedBasename["cache"]} cache the underlying cache map
 */

/**
 * @typedef {object} PathCacheFunctions
 * @property {JoinCacheEntry} join cached join
 * @property {DirnameCacheEntry} dirname cached dirname
 * @property {BasenameCacheEntry} basename cached basename
 */

/** @type {WeakMap<FileSystem, PathCacheFunctions>} */
const _pathCacheByFs = new WeakMap();

const HASH_ESCAPE_RE = /#/g;

/** @typedef {import("./ResolverFactory").ResolveOptions} ResolveOptions */

/**
 * @typedef {object} KnownContext
 * @property {string[]=} environments environments
 */

// eslint-disable-next-line jsdoc/reject-any-type
/** @typedef {KnownContext & Record<any, any>} Context */

/** @typedef {Error & { details?: string }} ErrorWithDetail */

/** @typedef {(err: ErrorWithDetail | null, res?: string | false, req?: ResolveRequest) => void} ResolveCallback */

/**
 * @typedef {object} PossibleFileSystemError
 * @property {string=} code code
 * @property {number=} errno number
 * @property {string=} path path
 * @property {string=} syscall syscall
 */

/**
 * @template T
 * @callback FileSystemCallback
 * @param {PossibleFileSystemError & Error | null} err
 * @param {T=} result
 */

/**
 * @typedef {string | Buffer | URL} PathLike
 */

/**
 * @typedef {PathLike | number} PathOrFileDescriptor
 */

/**
 * @typedef {object} ObjectEncodingOptions
 * @property {BufferEncoding | null | undefined=} encoding encoding
 */

/**
 * @typedef {ObjectEncodingOptions | BufferEncoding | undefined | null} EncodingOption
 */

/** @typedef {(err: NodeJS.ErrnoException | null, result?: string) => void} StringCallback */
/** @typedef {(err: NodeJS.ErrnoException | null, result?: Buffer) => void} BufferCallback */
/** @typedef {(err: NodeJS.ErrnoException | null, result?: (string | Buffer)) => void} StringOrBufferCallback */
/** @typedef {(err: NodeJS.ErrnoException | null, result?: IStats) => void} StatsCallback */
/** @typedef {(err: NodeJS.ErrnoException | null, result?: IBigIntStats) => void} BigIntStatsCallback */
/** @typedef {(err: NodeJS.ErrnoException | null, result?: (IStats | IBigIntStats)) => void} StatsOrBigIntStatsCallback */
/** @typedef {(err: NodeJS.ErrnoException | Error | null, result?: JsonObject) => void} ReadJsonCallback */

/**
 * @template T
 * @typedef {object} IStatsBase
 * @property {() => boolean} isFile is file
 * @property {() => boolean} isDirectory is directory
 * @property {() => boolean} isBlockDevice is block device
 * @property {() => boolean} isCharacterDevice is character device
 * @property {() => boolean} isSymbolicLink is symbolic link
 * @property {() => boolean} isFIFO is FIFO
 * @property {() => boolean} isSocket is socket
 * @property {T} dev dev
 * @property {T} ino ino
 * @property {T} mode mode
 * @property {T} nlink nlink
 * @property {T} uid uid
 * @property {T} gid gid
 * @property {T} rdev rdev
 * @property {T} size size
 * @property {T} blksize blksize
 * @property {T} blocks blocks
 * @property {T} atimeMs atime ms
 * @property {T} mtimeMs mtime ms
 * @property {T} ctimeMs ctime ms
 * @property {T} birthtimeMs birthtime ms
 * @property {Date} atime atime
 * @property {Date} mtime mtime
 * @property {Date} ctime ctime
 * @property {Date} birthtime birthtime
 */

/**
 * @typedef {IStatsBase<number>} IStats
 */

/**
 * @typedef {IStatsBase<bigint> & { atimeNs: bigint, mtimeNs: bigint, ctimeNs: bigint, birthtimeNs: bigint }} IBigIntStats
 */

/**
 * @template {string | Buffer} [T=string]
 * @typedef {object} Dirent
 * @property {() => boolean} isFile true when is file, otherwise false
 * @property {() => boolean} isDirectory true when is directory, otherwise false
 * @property {() => boolean} isBlockDevice true when is block device, otherwise false
 * @property {() => boolean} isCharacterDevice true when is character device, otherwise false
 * @property {() => boolean} isSymbolicLink true when is symbolic link, otherwise false
 * @property {() => boolean} isFIFO true when is FIFO, otherwise false
 * @property {() => boolean} isSocket true when is socket, otherwise false
 * @property {T} name name
 * @property {string} parentPath path
 * @property {string=} path path
 */

/**
 * @typedef {object} StatOptions
 * @property {(boolean | undefined)=} bigint need bigint values
 */

/**
 * @typedef {object} StatSyncOptions
 * @property {(boolean | undefined)=} bigint need bigint values
 * @property {(boolean | undefined)=} throwIfNoEntry throw if no entry
 */

/**
 * @typedef {{
 * (path: PathOrFileDescriptor, options: ({ encoding?: null | undefined, flag?: string | undefined } & import("events").Abortable) | undefined | null, callback: BufferCallback): void,
 * (path: PathOrFileDescriptor, options: ({ encoding: BufferEncoding, flag?: string | undefined } & import("events").Abortable) | BufferEncoding, callback: StringCallback): void,
 * (path: PathOrFileDescriptor, options: (ObjectEncodingOptions & { flag?: string | undefined } & import("events").Abortable) | BufferEncoding | undefined | null, callback: StringOrBufferCallback): void,
 * (path: PathOrFileDescriptor, callback: BufferCallback): void,
 * }} ReadFile
 */

/**
 * @typedef {"buffer" | { encoding: "buffer" }} BufferEncodingOption
 */

/**
 * @typedef {{
 * (path: PathOrFileDescriptor, options?: { encoding?: null | undefined, flag?: string | undefined } | null): Buffer,
 * (path: PathOrFileDescriptor, options: { encoding: BufferEncoding, flag?: string | undefined } | BufferEncoding): string,
 * (path: PathOrFileDescriptor, options?: (ObjectEncodingOptions & { flag?: string | undefined }) | BufferEncoding | null): string | Buffer,
 * }} ReadFileSync
 */

/**
 * @typedef {{
 * (path: PathLike, options: { encoding: BufferEncoding | null, withFileTypes?: false | undefined, recursive?: boolean | undefined } | BufferEncoding | undefined | null, callback: (err: NodeJS.ErrnoException | null, files?: string[]) => void): void,
 * (path: PathLike, options: { encoding: "buffer", withFileTypes?: false | undefined, recursive?: boolean | undefined } | "buffer", callback: (err: NodeJS.ErrnoException | null, files?: Buffer[]) => void): void,
 * (path: PathLike, options: (ObjectEncodingOptions & { withFileTypes?: false | undefined, recursive?: boolean | undefined }) | BufferEncoding | undefined | null, callback: (err: NodeJS.ErrnoException | null, files?: string[] | Buffer[]) => void): void,
 * (path: PathLike, callback: (err: NodeJS.ErrnoException | null, files?: string[]) => void): void,
 * (path: PathLike, options: ObjectEncodingOptions & { withFileTypes: true, recursive?: boolean | undefined }, callback: (err: NodeJS.ErrnoException | null, files?: Dirent<string>[]) => void): void,
 * (path: PathLike, options: { encoding: "buffer", withFileTypes: true, recursive?: boolean | undefined }, callback: (err: NodeJS.ErrnoException | null, files: Dirent<Buffer>[]) => void): void,
 * }} Readdir
 */

/**
 * @typedef {{
 * (path: PathLike, options?: { encoding: BufferEncoding | null, withFileTypes?: false | undefined, recursive?: boolean | undefined } | BufferEncoding | null): string[],
 * (path: PathLike, options: { encoding: "buffer", withFileTypes?: false | undefined, recursive?: boolean | undefined } | "buffer"): Buffer[],
 * (path: PathLike, options?: (ObjectEncodingOptions & { withFileTypes?: false | undefined, recursive?: boolean | undefined }) | BufferEncoding | null): string[] | Buffer[],
 * (path: PathLike, options: ObjectEncodingOptions & { withFileTypes: true, recursive?: boolean | undefined }): Dirent[],
 * (path: PathLike, options: { encoding: "buffer", withFileTypes: true, recursive?: boolean | undefined }): Dirent<Buffer>[],
 * }} ReaddirSync
 */

/**
 * @typedef {(pathOrFileDescription: PathOrFileDescriptor, callback: ReadJsonCallback) => void} ReadJson
 */

/**
 * @typedef {(pathOrFileDescription: PathOrFileDescriptor) => JsonObject} ReadJsonSync
 */

/**
 * @typedef {{
 * (path: PathLike, options: EncodingOption, callback: StringCallback): void,
 * (path: PathLike, options: BufferEncodingOption, callback: BufferCallback): void,
 * (path: PathLike, options: EncodingOption, callback: StringOrBufferCallback): void,
 * (path: PathLike, callback: StringCallback): void,
 * }} Readlink
 */

/**
 * @typedef {{
 * (path: PathLike, options?: EncodingOption): string,
 * (path: PathLike, options: BufferEncodingOption): Buffer,
 * (path: PathLike, options?: EncodingOption): string | Buffer,
 * }} ReadlinkSync
 */

/**
 * @typedef {{
 * (path: PathLike, callback: StatsCallback): void,
 * (path: PathLike, options: (StatOptions & { bigint?: false | undefined }) | undefined, callback: StatsCallback): void,
 * (path: PathLike, options: StatOptions & { bigint: true }, callback: BigIntStatsCallback): void,
 * (path: PathLike, options: StatOptions | undefined, callback: StatsOrBigIntStatsCallback): void,
 * }} LStat
 */

/**
 * @typedef {{
 * (path: PathLike, options?: undefined): IStats,
 * (path: PathLike, options?: StatSyncOptions & { bigint?: false | undefined, throwIfNoEntry: false }): IStats | undefined,
 * (path: PathLike, options: StatSyncOptions & { bigint: true, throwIfNoEntry: false }): IBigIntStats | undefined,
 * (path: PathLike, options?: StatSyncOptions & { bigint?: false | undefined }): IStats,
 * (path: PathLike, options: StatSyncOptions & { bigint: true }): IBigIntStats,
 * (path: PathLike, options: StatSyncOptions & { bigint: boolean, throwIfNoEntry?: false | undefined }): IStats | IBigIntStats,
 * (path: PathLike, options?: StatSyncOptions): IStats | IBigIntStats | undefined,
 * }} LStatSync
 */

/**
 * @typedef {{
 * (path: PathLike, callback: StatsCallback): void,
 * (path: PathLike, options: (StatOptions & { bigint?: false | undefined }) | undefined, callback: StatsCallback): void,
 * (path: PathLike, options: StatOptions & { bigint: true }, callback: BigIntStatsCallback): void,
 * (path: PathLike, options: StatOptions | undefined, callback: StatsOrBigIntStatsCallback): void,
 * }} Stat
 */

/**
 * @typedef {{
 * (path: PathLike, options?: undefined): IStats,
 * (path: PathLike, options?: StatSyncOptions & { bigint?: false | undefined, throwIfNoEntry: false }): IStats | undefined,
 * (path: PathLike, options: StatSyncOptions & { bigint: true, throwIfNoEntry: false }): IBigIntStats | undefined,
 * (path: PathLike, options?: StatSyncOptions & { bigint?: false | undefined }): IStats,
 * (path: PathLike, options: StatSyncOptions & { bigint: true }): IBigIntStats,
 * (path: PathLike, options: StatSyncOptions & { bigint: boolean, throwIfNoEntry?: false | undefined }): IStats | IBigIntStats,
 * (path: PathLike, options?: StatSyncOptions): IStats | IBigIntStats | undefined,
 * }} StatSync
 */

/**
 * @typedef {{
 * (path: PathLike, options: EncodingOption, callback: StringCallback): void,
 * (path: PathLike, options: BufferEncodingOption, callback: BufferCallback): void,
 * (path: PathLike, options: EncodingOption, callback: StringOrBufferCallback): void,
 * (path: PathLike, callback: StringCallback): void,
 * }} RealPath
 */

/**
 * @typedef {{
 * (path: PathLike, options?: EncodingOption): string,
 * (path: PathLike, options: BufferEncodingOption): Buffer,
 * (path: PathLike, options?: EncodingOption): string | Buffer,
 * }} RealPathSync
 */

/**
 * @typedef {object} FileSystem
 * @property {ReadFile} readFile read file method
 * @property {Readdir} readdir readdir method
 * @property {ReadJson=} readJson read json method
 * @property {Readlink} readlink read link method
 * @property {LStat=} lstat lstat method
 * @property {Stat} stat stat method
 * @property {RealPath=} realpath realpath method
 */

/**
 * @typedef {object} SyncFileSystem
 * @property {ReadFileSync} readFileSync read file sync method
 * @property {ReaddirSync} readdirSync read dir sync method
 * @property {ReadJsonSync=} readJsonSync read json sync method
 * @property {ReadlinkSync} readlinkSync read link sync method
 * @property {LStatSync=} lstatSync lstat sync method
 * @property {StatSync} statSync stat sync method
 * @property {RealPathSync=} realpathSync real path sync method
 */

/**
 * @typedef {object} ParsedIdentifier
 * @property {string} request request
 * @property {string} query query
 * @property {string} fragment fragment
 * @property {boolean} directory is directory
 * @property {boolean} module is module
 * @property {boolean} file is file
 * @property {boolean} internal is internal
 */

/** @typedef {string | number | boolean | null} JsonPrimitive */
/** @typedef {JsonValue[]} JsonArray */
/** @typedef {JsonPrimitive | JsonObject | JsonArray} JsonValue */
/** @typedef {{ [Key in string]?: JsonValue | undefined }} JsonObject */

/**
 * @typedef {object} TsconfigPathsMap
 * @property {TsconfigPathsData} main main tsconfig paths data
 * @property {string} mainContext main tsconfig base URL (absolute path)
 * @property {{ [baseUrl: string]: TsconfigPathsData }} refs referenced tsconfig paths data mapped by baseUrl
 * @property {{ [context: string]: TsconfigPathsData }} allContexts all contexts (main + refs) for quick lookup
 * @property {string[]} contextList precomputed `Object.keys(allContexts)` — read-only; used on the `_selectPathsDataForContext` hot path
 * @property {Set<string>} fileDependencies file dependencies
 */

/**
 * @typedef {object} TsconfigPathsData
 * @property {import("./AliasUtils").CompiledAliasOptions} alias tsconfig file data
 * @property {string[]} modules tsconfig file data
 */

/**
 * @typedef {object} BaseResolveRequest
 * @property {string | false} path path
 * @property {Context=} context content
 * @property {string=} descriptionFilePath description file path
 * @property {string=} descriptionFileRoot description file root
 * @property {JsonObject=} descriptionFileData description file data
 * @property {TsconfigPathsMap | null | undefined=} tsconfigPathsMap tsconfig paths map
 * @property {string=} relativePath relative path
 * @property {boolean=} ignoreSymlinks true when need to ignore symlinks, otherwise false
 * @property {boolean=} fullySpecified true when full specified, otherwise false
 * @property {string=} __innerRequest inner request for internal usage
 * @property {string=} __innerRequest_request inner request for internal usage
 * @property {string=} __innerRequest_relativePath inner relative path for internal usage
 * @property {{ blocked: boolean }=} __restrictionsMarker internal: shared marker `RestrictionsPlugin` flips when it filters out an existing target, letting `ExportsFieldPlugin` fall back instead of erroring
 */

/** @typedef {BaseResolveRequest & Partial<ParsedIdentifier>} ResolveRequest */

/**
 * @template T
 * @typedef {{ add: (item: T) => void }} WriteOnlySet
 */

/** @typedef {(request: ResolveRequest) => void} ResolveContextYield */

/**
 * Singly-linked stack entry that also exposes a Set-like API
 * (`has`, `size`, iteration). Each `doResolve` call prepends a new
 * `StackEntry` that points at the previous tip via `.parent`, so pushing
 * is O(1) in time and memory. Recursion detection walks the linked list
 * (O(n)) but the stack is typically shallow, so this is cheaper overall
 * than cloning a `Set` per call.
 */
class StackEntry {
	/**
	 * @param {ResolveStepHook} hook hook
	 * @param {ResolveRequest} request request
	 * @param {StackEntry=} parent previous tip
	 * @param {Set<string>=} preSeeded entries pre-seeded via the legacy `Set<string>` API
	 */
	constructor(hook, request, parent, preSeeded) {
		this.name = hook.name;
		this.path = request.path;
		this.request = request.request || "";
		this.query = request.query || "";
		this.fragment = request.fragment || "";
		this.directory = Boolean(request.directory);
		this.module = Boolean(request.module);
		/** @type {StackEntry | undefined} */
		this.parent = parent;
		/**
		 * Strings seeded by callers that still pass `stack: new Set([...])`.
		 * Propagated through the chain so deeper `doResolve` calls still see
		 * them during recursion checks. `undefined` in the common case so
		 * there is no extra work on the hot path.
		 * @type {Set<string> | undefined}
		 */
		this.preSeeded = preSeeded;
	}

	/**
	 * Walk the linked list looking for an entry with the same request shape.
	 * Set-compatible: callers that used `stack.has(entry)` keep working.
	 *
	 * NOTE: kept monomorphic on purpose. An earlier draft accepted a string
	 * query too (so pre-5.21 plugins keeping their own `Set<string>` of
	 * seen entries could probe the live stack with the formatted form),
	 * but adding the second shape regressed `doResolve`'s heap profile by
	 * ~1 MiB / 200 resolves on stack-churn — V8 keeps a polymorphic
	 * call-site state for `parent.has(stackEntry)` once `has` has two
	 * argument shapes. Plugins that need string membership can reach for
	 * `[...stack].find(e => e.includes(formattedString))` via the
	 * `String`-method proxies on `StackEntry` instead.
	 * @param {StackEntry} query entry to look for
	 * @returns {boolean} whether the stack already contains an equivalent entry
	 */
	has(query) {
		/** @type {StackEntry | undefined} */
		let node = this;
		while (node) {
			if (
				node.name === query.name &&
				node.path === query.path &&
				node.request === query.request &&
				node.query === query.query &&
				node.fragment === query.fragment &&
				node.directory === query.directory &&
				node.module === query.module
			) {
				return true;
			}
			node = node.parent;
		}
		return this.preSeeded !== undefined && this.preSeeded.has(query.toString());
	}

	/**
	 * Number of entries on the stack (oldest-to-newest length).
	 * @returns {number} size
	 */
	get size() {
		let count = this.preSeeded ? this.preSeeded.size : 0;
		/** @type {StackEntry | undefined} */
		let node = this;
		while (node) {
			count++;
			node = node.parent;
		}
		return count;
	}

	/**
	 * Iterate entries from oldest (root) to newest (tip), matching how a
	 * `Set` that was populated in insertion order would iterate. Pre-seeded
	 * legacy `Set<string>` entries come first so error-message output stays
	 * ordered oldest-to-newest.
	 *
	 * Yields each entry as its formatted `toString()` form. Plugins written
	 * against the pre-5.21 `Set<string>` shape — e.g.
	 * `[...resolveContext.stack].find(a => a.includes("module:"))` — keep
	 * working unchanged because each yielded value is a plain string with
	 * all of `String.prototype` available natively. Resolves that never
	 * iterate the stack pay nothing; iteration costs one `toString()`
	 * allocation per stack frame.
	 * @returns {IterableIterator<string>} iterator
	 */
	*[Symbol.iterator]() {
		if (this.preSeeded !== undefined) {
			for (const entry of this.preSeeded) yield entry;
		}
		/** @type {StackEntry[]} */
		const entries = [];
		/** @type {StackEntry | undefined} */
		let node = this;
		while (node) {
			entries.push(node);
			node = node.parent;
		}
		for (let i = entries.length - 1; i >= 0; i--) yield entries[i].toString();
	}

	/**
	 * Human-readable form used in recursion error messages, logs, and the
	 * iterator above. Not memoized: caching would require an extra slot on
	 * every `StackEntry`, which costs heap even on resolves that never look
	 * at the formatted form.
	 * @returns {string} formatted entry
	 */
	toString() {
		return `${this.name}: (${this.path}) ${this.request}${this.query}${
			this.fragment
		}${this.directory ? " directory" : ""}${this.module ? " module" : ""}`;
	}
}

/**
 * Resolve context
 * @typedef {object} ResolveContext
 * @property {WriteOnlySet<string>=} contextDependencies directories that was found on file system
 * @property {WriteOnlySet<string>=} fileDependencies files that was found on file system
 * @property {WriteOnlySet<string>=} missingDependencies dependencies that was not found on file system
 * @property {StackEntry | Set<string>=} stack tip of the resolver call stack (a singly-linked list with Set-like API). For instance, `resolve → parsedResolve → describedResolve`. Accepts a legacy `Set<string>` for back-compat with older callers; it is normalized internally without a hot-path branch.
 * @property {((str: string) => void)=} log log function
 * @property {ResolveContextYield=} yield yield result, if provided plugins can return several results
 */

/** @typedef {AsyncSeriesBailHook<[ResolveRequest, ResolveContext], ResolveRequest | null>} ResolveStepHook */

/**
 * @typedef {object} KnownHooks
 * @property {SyncHook<[ResolveStepHook, ResolveRequest], void>} resolveStep resolve step hook
 * @property {SyncHook<[ResolveRequest, Error]>} noResolve no resolve hook
 * @property {ResolveStepHook} resolve resolve hook
 * @property {AsyncSeriesHook<[ResolveRequest, ResolveContext]>} result result hook
 */

/**
 * @typedef {{ [key: string]: ResolveStepHook }} EnsuredHooks
 */

/**
 * @param {string} str input string
 * @returns {string} in camel case
 */
function toCamelCase(str) {
	return str.replace(/-([a-z])/g, (str) => str.slice(1).toUpperCase());
}

class Resolver {
	/**
	 * @param {ResolveStepHook} hook hook
	 * @param {ResolveRequest} request request
	 * @param {StackEntry=} parent previous tip of the stack
	 * @param {Set<string>=} preSeeded entries pre-seeded via the legacy `Set<string>` API
	 * @returns {StackEntry} stack entry
	 */
	static createStackEntry(hook, request, parent, preSeeded) {
		return new StackEntry(hook, request, parent, preSeeded);
	}

	/**
	 * @param {FileSystem} fileSystem a filesystem
	 * @param {ResolveOptions} options options
	 */
	constructor(fileSystem, options) {
		/** @type {FileSystem} */
		this.fileSystem = fileSystem;
		/** @type {ResolveOptions} */
		this.options = options;
		let pathCache = _pathCacheByFs.get(fileSystem);
		if (!pathCache) {
			pathCache = {
				join: createCachedJoin(),
				dirname: createCachedDirname(),
				basename: createCachedBasename(),
			};
			_pathCacheByFs.set(fileSystem, pathCache);
		}
		/** @type {PathCacheFunctions} */
		this.pathCache = pathCache;
		/** @type {KnownHooks} */
		this.hooks = {
			resolveStep: new SyncHook(["hook", "request"], "resolveStep"),
			noResolve: new SyncHook(["request", "error"], "noResolve"),
			resolve: new AsyncSeriesBailHook(
				["request", "resolveContext"],
				"resolve",
			),
			result: new AsyncSeriesHook(["result", "resolveContext"], "result"),
		};
	}

	/**
	 * @param {string | ResolveStepHook} name hook name or hook itself
	 * @returns {ResolveStepHook} the hook
	 */
	ensureHook(name) {
		if (typeof name !== "string") {
			return name;
		}
		name = toCamelCase(name);
		if (name.startsWith("before")) {
			return /** @type {ResolveStepHook} */ (
				this.ensureHook(name[6].toLowerCase() + name.slice(7)).withOptions({
					stage: -10,
				})
			);
		}
		if (name.startsWith("after")) {
			return /** @type {ResolveStepHook} */ (
				this.ensureHook(name[5].toLowerCase() + name.slice(6)).withOptions({
					stage: 10,
				})
			);
		}
		/** @type {ResolveStepHook} */
		const hook = /** @type {KnownHooks & EnsuredHooks} */ (this.hooks)[name];
		if (!hook) {
			/** @type {KnownHooks & EnsuredHooks} */
			(this.hooks)[name] = new AsyncSeriesBailHook(
				["request", "resolveContext"],
				name,
			);

			return /** @type {KnownHooks & EnsuredHooks} */ (this.hooks)[name];
		}
		return hook;
	}

	/**
	 * @param {string | ResolveStepHook} name hook name or hook itself
	 * @returns {ResolveStepHook} the hook
	 */
	getHook(name) {
		if (typeof name !== "string") {
			return name;
		}
		name = toCamelCase(name);
		if (name.startsWith("before")) {
			return /** @type {ResolveStepHook} */ (
				this.getHook(name[6].toLowerCase() + name.slice(7)).withOptions({
					stage: -10,
				})
			);
		}
		if (name.startsWith("after")) {
			return /** @type {ResolveStepHook} */ (
				this.getHook(name[5].toLowerCase() + name.slice(6)).withOptions({
					stage: 10,
				})
			);
		}
		/** @type {ResolveStepHook} */
		const hook = /** @type {KnownHooks & EnsuredHooks} */ (this.hooks)[name];
		if (!hook) {
			throw new Error(`Hook ${name} doesn't exist`);
		}
		return hook;
	}

	/**
	 * @overload
	 * @param {string | URL} parent context path or a `file:` URL instance
	 * @param {string | URL} specifier request string or a `file:` URL instance
	 * @param {ResolveContext=} resolveContext resolve context
	 * @returns {string | false} result
	 */
	/**
	 * @overload
	 * @param {Context} context context information object
	 * @param {string | URL} parent context path or a `file:` URL instance
	 * @param {string | URL} specifier request string or a `file:` URL instance
	 * @param {ResolveContext=} resolveContext resolve context
	 * @returns {string | false} result
	 */
	/**
	 * @param {Context | string | URL} context context information object, or the context path (string or `file:` URL instance) when no context is provided
	 * @param {string | URL | ResolveContext=} parent context path (string or `file:` URL instance) or resolve context when no context is provided
	 * @param {string | URL | ResolveContext=} specifier request string (or `file:` URL instance) or resolve context when no context is provided
	 * @param {ResolveContext=} resolveContext resolve context
	 * @returns {string | false} result
	 */
	resolveSync(context, parent, specifier, resolveContext) {
		/** @type {Error | null | undefined} */
		let err;
		/** @type {string | false | undefined} */
		let result;
		let sync = false;
		// `|| {}` so the underlying `resolve()` hits its 5-arg fast path
		// (skips the overload-shifting prologue) regardless of whether the
		// caller supplied a resolveContext.
		this.resolve(
			/** @type {Context} */ (context),
			/** @type {string} */ (parent),
			/** @type {string} */ (specifier),
			/** @type {ResolveContext} */ (resolveContext) || {},
			(_err, r) => {
				err = _err;
				result = r;
				sync = true;
			},
		);
		if (!sync) {
			throw new Error(
				"Cannot 'resolveSync' because the fileSystem is not sync. Use 'resolve'!",
			);
		}
		if (err) throw err;
		if (result === undefined) throw new Error("No result");
		return result;
	}

	/**
	 * @overload
	 * @param {string | URL} parent context path or a `file:` URL instance
	 * @param {string | URL} specifier request string or a `file:` URL instance
	 * @param {ResolveContext=} resolveContext resolve context
	 * @returns {Promise<string | false>} result
	 */
	/**
	 * @overload
	 * @param {Context} context context information object
	 * @param {string | URL} parent context path or a `file:` URL instance
	 * @param {string | URL} specifier request string or a `file:` URL instance
	 * @param {ResolveContext=} resolveContext resolve context
	 * @returns {Promise<string | false>} result
	 */
	/**
	 * @param {Context | string | URL} context context information object, or the context path (string or `file:` URL instance) when no context is provided
	 * @param {string | URL | ResolveContext=} parent context path (string or `file:` URL instance) or resolve context when no context is provided
	 * @param {string | URL | ResolveContext=} specifier request string (or `file:` URL instance) or resolve context when no context is provided
	 * @param {ResolveContext=} resolveContext resolve context
	 * @returns {Promise<string | false>} result
	 */
	resolvePromise(context, parent, specifier, resolveContext) {
		// `|| {}` ensures the 5-arg fast path inside `resolve()` is reached
		// even when the caller doesn't pass a resolveContext.
		return _withResolvers(
			this,
			/** @type {Context} */ (context),
			/** @type {string} */ (parent),
			/** @type {string} */ (specifier),
			/** @type {ResolveContext} */ (resolveContext) || {},
		);
	}

	/**
	 * @overload
	 * @param {string | URL} parent context path or a `file:` URL instance
	 * @param {string | URL} specifier request string or a `file:` URL instance
	 * @param {ResolveCallback} callback callback function
	 * @returns {void}
	 */
	/**
	 * @overload
	 * @param {string | URL} parent context path or a `file:` URL instance
	 * @param {string | URL} specifier request string or a `file:` URL instance
	 * @param {ResolveContext} resolveContext resolve context
	 * @param {ResolveCallback} callback callback function
	 * @returns {void}
	 */
	/**
	 * @overload
	 * @param {Context} context context information object
	 * @param {string | URL} parent context path or a `file:` URL instance
	 * @param {string | URL} specifier request string or a `file:` URL instance
	 * @param {ResolveCallback} callback callback function
	 * @returns {void}
	 */
	/**
	 * @overload
	 * @param {Context} context context information object
	 * @param {string | URL} parent context path or a `file:` URL instance
	 * @param {string | URL} specifier request string or a `file:` URL instance
	 * @param {ResolveContext} resolveContext resolve context
	 * @param {ResolveCallback} callback callback function
	 * @returns {void}
	 */
	/**
	 * @param {Context | string | URL} context context information object, or the context path (string or `file:` URL instance) when no context is provided
	 * @param {string | URL | ResolveContext | ResolveCallback=} parent context path (string or `file:` URL instance) or (when no context) resolve context or callback
	 * @param {string | URL | ResolveContext | ResolveCallback=} specifier request string (or `file:` URL instance) or (when no context) resolve context or callback
	 * @param {ResolveContext | ResolveCallback=} resolveContext resolve context or callback when no resolve context is provided
	 * @param {ResolveCallback=} callback callback function
	 * @returns {void}
	 */
	resolve(context, parent, specifier, resolveContext, callback) {
		// Fast path for the common 5-arg call (`resolver.resolve(ctx, from,
		// req, resolveCtx, cb)`) — every call from `resolveSync` /
		// `resolvePromise` plus the vast majority of direct API callers.
		// PR #536 added runtime overload-shifting to support optional
		// `context` / `resolveContext`; that adds several `typeof` checks
		// per resolve which show up as a measurable instruction-count
		// regression on every benchmark that calls into this method. Skip
		// the shifting entirely when all 5 args are already well-typed.
		if (
			typeof callback === "function" &&
			typeof context === "object" &&
			context !== null &&
			typeof resolveContext === "object" &&
			resolveContext !== null
		) {
			// proceed straight to per-arg validation below
		} else {
			// Slow path: shift positional args based on what was supplied.
			// Shift when context is omitted (first positional arg is the parent,
			// either a string or a `file:` URL instance).
			if (typeof context === "string" || context instanceof URL) {
				// Keep an already-supplied callback (resolveSync / resolvePromise
				// always pass one in the 5th position).
				if (typeof callback !== "function") {
					callback = /** @type {ResolveCallback | undefined} */ (
						resolveContext
					);
				}
				resolveContext =
					/** @type {ResolveContext | ResolveCallback | undefined} */ (
						specifier
					);
				specifier = /** @type {string} */ (parent);
				parent = context;
				context = {};
			}
			// 4-arg form: the resolveContext slot holds the callback.
			if (typeof resolveContext === "function") {
				callback = resolveContext;
				resolveContext = {};
			} else if (!resolveContext || typeof resolveContext !== "object") {
				resolveContext = {};
			}
			if (typeof callback !== "function") {
				throw new TypeError("callback argument is not a function");
			}
			if (!context || typeof context !== "object") {
				context = {};
			}
		}
		// Accept `file:` URL instances for parent/specifier, converting to a
		// filesystem path (mirrors the URL support in resolve options). The
		// `instanceof` check sits on the error branch, so the common string
		// case keeps its single `typeof` check on this hot path.
		if (typeof parent !== "string") {
			if (parent instanceof URL) parent = toPath(parent);
			else return callback(new Error("path argument is not a string"));
		}
		if (typeof specifier !== "string") {
			if (specifier instanceof URL) specifier = toPath(specifier);
			else return callback(new Error("request argument is not a string"));
		}

		/** @type {ResolveRequest} */
		const obj = {
			context,
			path: parent,
			request: specifier,
		};

		/** @type {ResolveContextYield | undefined} */
		let yield_;
		let yieldCalled = false;
		/** @type {ResolveContextYield | undefined} */
		let finishYield;
		if (typeof resolveContext.yield === "function") {
			const old = resolveContext.yield;
			/**
			 * @param {ResolveRequest} obj object
			 */
			yield_ = (obj) => {
				old(obj);
				yieldCalled = true;
			};
			/**
			 * @param {ResolveRequest} result result
			 * @returns {void}
			 */
			finishYield = (result) => {
				if (result) {
					/** @type {ResolveContextYield} */ (yield_)(result);
				}
				callback(null);
			};
		}

		/**
		 * @param {ResolveRequest} result result
		 * @returns {void}
		 */
		const finishResolved = (result) => {
			const resultPath = result.path;
			if (resultPath === false) return callback(null, false, result);
			const escapedPath = resultPath.includes("#")
				? resultPath.replace(HASH_ESCAPE_RE, "\0#")
				: resultPath;
			const resultQuery = result.query;
			let escapedQuery;
			if (resultQuery) {
				escapedQuery = resultQuery.includes("#")
					? resultQuery.replace(HASH_ESCAPE_RE, "\0#")
					: resultQuery;
			} else {
				escapedQuery = "";
			}
			return callback(
				null,
				`${escapedPath}${escapedQuery}${result.fragment || ""}`,
				result,
			);
		};

		/**
		 * @param {string} message resolve message
		 * @param {string[]} log logs
		 * @returns {void}
		 */
		const finishWithoutResolve = (message, log) => {
			/**
			 * @type {ErrorWithDetail}
			 */
			const error = new Error(`Can't ${message}`);
			error.details = log.join("\n");
			this.hooks.noResolve.call(obj, error);
			return callback(error);
		};

		if (resolveContext.log) {
			const message = `resolve '${specifier}' in '${parent}'`;
			// We need log anyway to capture it in case of an error
			const parentLog = resolveContext.log;
			/** @type {string[]} */
			const log = [];
			return this.doResolve(
				this.hooks.resolve,
				obj,
				message,
				{
					log: (msg) => {
						parentLog(msg);
						log.push(msg);
					},
					yield: yield_,
					fileDependencies: resolveContext.fileDependencies,
					contextDependencies: resolveContext.contextDependencies,
					missingDependencies: resolveContext.missingDependencies,
					stack: resolveContext.stack,
				},
				(err, result) => {
					if (err) return callback(err);

					if (yieldCalled || (result && yield_)) {
						return /** @type {ResolveContextYield} */ (finishYield)(
							/** @type {ResolveRequest} */ (result),
						);
					}

					if (result) return finishResolved(result);

					return finishWithoutResolve(message, log);
				},
			);
		}
		// Try to resolve assuming there is no error
		// We don't log stuff in this case
		// When there is no yield wrapper, the caller's resolveContext can
		// be passed directly — its `log` is already falsy (we are in the
		// !log branch) and `yield` is undefined, so a fresh wrapper would
		// be an identical copy.
		const rc = yield_
			? {
					log: undefined,
					yield: yield_,
					fileDependencies: resolveContext.fileDependencies,
					contextDependencies: resolveContext.contextDependencies,
					missingDependencies: resolveContext.missingDependencies,
					stack: resolveContext.stack,
				}
			: resolveContext;
		return this.doResolve(this.hooks.resolve, obj, null, rc, (err, result) => {
			if (err) return callback(err);

			if (yieldCalled || (result && yield_)) {
				return /** @type {ResolveContextYield} */ (finishYield)(
					/** @type {ResolveRequest} */ (result),
				);
			}

			if (result) return finishResolved(result);

			// log is missing for the error details
			// so we redo the resolving for the log info
			// this is more expensive to the success case
			// is assumed by default
			const message = `resolve '${specifier}' in '${parent}'`;
			/** @type {string[]} */
			const log = [];

			return this.doResolve(
				this.hooks.resolve,
				obj,
				message,
				{
					log: (msg) => log.push(msg),
					yield: yield_,
					stack: resolveContext.stack,
				},
				(err, result) => {
					if (err) return callback(err);

					// In a case that there is a race condition and yield will be called
					if (yieldCalled || (result && yield_)) {
						return /** @type {ResolveContextYield} */ (finishYield)(
							/** @type {ResolveRequest} */ (result),
						);
					}

					return finishWithoutResolve(message, log);
				},
			);
		});
	}

	/**
	 * @param {ResolveStepHook} hook hook
	 * @param {ResolveRequest} request request
	 * @param {null | string} message string
	 * @param {ResolveContext} resolveContext resolver context
	 * @param {(err?: null | Error, result?: ResolveRequest) => void} callback callback
	 * @returns {void}
	 */
	doResolve(hook, request, message, resolveContext, callback) {
		const rawStack = resolveContext.stack;
		/** @type {StackEntry | undefined} */
		let parent;
		/** @type {Set<string> | undefined} */
		let preSeeded;
		if (rawStack instanceof StackEntry) {
			parent = rawStack;
			preSeeded = rawStack.preSeeded;
		} else if (rawStack) {
			// TODO in the next major remove `Set<string>` support in favor of `StackEntry`
			// Legacy `stack: new Set<string>()` API: don't link the Set into
			// the parent chain (it would pollute iteration and field-compare
			// walks). Carry the strings on the StackEntry itself instead so
			// deeper `doResolve` calls keep seeing pre-seeded entries.
			preSeeded = /** @type {Set<string>} */ (rawStack);
		}
		// Prepend a new linked-list node. O(1) allocation, no Set clone.
		const stackEntry = Resolver.createStackEntry(
			hook,
			request,
			parent,
			preSeeded,
		);

		// When `parent` exists, its `has()` already consults `preSeeded`
		// (inherited from the same chain), so we only need the direct Set
		// lookup on the very first `doResolve` call (no parent yet).
		if (
			parent !== undefined
				? parent.has(stackEntry)
				: preSeeded !== undefined && preSeeded.has(stackEntry.toString())
		) {
			/**
			 * Prevent recursion
			 * @type {Error & { recursion?: boolean }}
			 */
			const recursionError = new Error(
				`Recursion in resolving\nStack:\n  ${[...stackEntry].join("\n  ")}`,
			);
			recursionError.recursion = true;
			if (resolveContext.log) {
				resolveContext.log("abort resolving because of recursion");
			}
			return callback(recursionError);
		}
		this.hooks.resolveStep.call(hook, request);

		if (hook.isUsed()) {
			// No-log fast path: when the context was created internally
			// (stack is already a StackEntry from a prior doResolve), we
			// can mutate stack in-place and restore it in the callback,
			// avoiding the createInnerContext allocation entirely.
			if (!resolveContext.log && rawStack instanceof StackEntry) {
				resolveContext.stack = stackEntry;
				return hook.callAsync(request, resolveContext, (err, result) => {
					resolveContext.stack = rawStack;
					if (err) return callback(err);
					if (result) return callback(null, result);
					callback();
				});
			}
			const innerContext = createInnerContext(
				resolveContext,
				stackEntry,
				message,
			);
			return hook.callAsync(request, innerContext, (err, result) => {
				if (err) return callback(err);
				if (result) return callback(null, result);
				callback();
			});
		}
		callback();
	}

	/**
	 * @param {string} identifier identifier
	 * @returns {ParsedIdentifier} parsed identifier
	 */
	parse(identifier) {
		/** @type {ParsedIdentifier} */
		const part = {
			request: "",
			query: "",
			fragment: "",
			module: false,
			directory: false,
			file: false,
			internal: false,
		};

		const parsedIdentifier = parseIdentifier(identifier);

		if (!parsedIdentifier) return part;

		[part.request, part.query, part.fragment] = parsedIdentifier;

		if (part.request.length > 0) {
			// `getType` looks at the prefix of its input and the prefix is
			// identical between `identifier` and `part.request` in every
			// non-`\0`-escape case (slicing off `?query` / `#fragment` doesn't
			// touch the head). `parseIdentifier`'s common fast path returns
			// the same `identifier` reference as `parsedIdentifier[0]`, so a
			// pointer-equality check detects the case where we can compute
			// `getType` once and use it for both `module` and `internal`. The
			// `\0#…` escape path produces a fresh `part.request` and falls
			// through to the second `getType(identifier)` call to preserve
			// the original `internal` flag.
			const requestType = getType(part.request);
			part.module = requestType === PathType.Normal;
			part.internal =
				identifier === part.request
					? requestType === PathType.Internal
					: getType(identifier) === PathType.Internal;
			// `isDirectory` is just `endsWith("/")` — inline so `parse()`
			// doesn't pay for the extra method dispatch on every resolve.
			part.directory = part.request.endsWith("/");
			if (part.directory) {
				part.request = part.request.slice(0, -1);
			}
		}

		return part;
	}

	/**
	 * @param {string} path path
	 * @returns {boolean} true, if the path is a module
	 */
	isModule(path) {
		return getType(path) === PathType.Normal;
	}

	/**
	 * @param {string} path path
	 * @returns {boolean} true, if the path is private
	 */
	isPrivate(path) {
		return getType(path) === PathType.Internal;
	}

	/**
	 * @param {string} path a path
	 * @returns {boolean} true, if the path is a directory path
	 */
	isDirectory(path) {
		return path.endsWith("/");
	}

	/**
	 * @param {string} path path
	 * @returns {string} normalized path
	 */
	normalize(path) {
		return normalize(path);
	}

	/**
	 * @param {string} path path
	 * @param {string} request request
	 * @returns {string} joined path
	 */
	join(path, request) {
		return this.pathCache.join.fn(path, request);
	}

	/**
	 * @param {string} path path
	 * @returns {string} parent directory
	 */
	dirname(path) {
		return this.pathCache.dirname.fn(path);
	}

	/**
	 * @param {string} path the path to evaluate
	 * @param {string=} suffix an extension to remove from the result
	 * @returns {string} the last portion of a path
	 */
	basename(path, suffix) {
		return this.pathCache.basename.fn(path, suffix);
	}
}

module.exports = Resolver;
