/*
 * This file was automatically generated.
 * DO NOT MODIFY BY HAND.
 * Run `yarn fix:special` to update
 */

import { Buffer } from "buffer";
import { AsyncSeriesBailHook, AsyncSeriesHook, SyncHook } from "tapable";
import { URL as URL_Import } from "url";

declare interface Abortable {
	/**
	 * When provided the corresponding `AbortController` can be used to cancel an asynchronous action.
	 */
	signal?: AbortSignal;
}
type Alias = string | false | string[];
declare interface AliasOption {
	alias: Alias;
	name: string;
	onlyModule?: boolean;
}
type BaseFileSystem = FileSystem & SyncFileSystem;
declare interface BaseResolveRequest {
	/**
	 * path
	 */
	path: string | false;

	/**
	 * content
	 */
	context?: Context;

	/**
	 * description file path
	 */
	descriptionFilePath?: string;

	/**
	 * description file root
	 */
	descriptionFileRoot?: string;

	/**
	 * description file data
	 */
	descriptionFileData?: JsonObject;

	/**
	 * tsconfig paths map
	 */
	tsconfigPathsMap?: null | TsconfigPathsMap;

	/**
	 * relative path
	 */
	relativePath?: string;

	/**
	 * true when need to ignore symlinks, otherwise false
	 */
	ignoreSymlinks?: boolean;

	/**
	 * true when full specified, otherwise false
	 */
	fullySpecified?: boolean;

	/**
	 * inner request for internal usage
	 */
	__innerRequest?: string;

	/**
	 * inner request for internal usage
	 */
	__innerRequest_request?: string;

	/**
	 * inner relative path for internal usage
	 */
	__innerRequest_relativePath?: string;

	/**
	 * internal: shared marker `RestrictionsPlugin` flips when it filters out an existing target, letting `ExportsFieldPlugin` fall back instead of erroring
	 */
	__restrictionsMarker?: { blocked: boolean };
}
declare interface BasenameCacheEntry {
	/**
	 * cached dirname function
	 */
	fn: (maybePath: string, suffix?: string) => string;

	/**
	 * the underlying cache map
	 */
	cache: Map<string, Map<undefined | string, undefined | string>>;
}
type BufferEncoding =
	| "ascii"
	| "utf8"
	| "utf-8"
	| "utf16le"
	| "utf-16le"
	| "ucs2"
	| "ucs-2"
	| "base64"
	| "base64url"
	| "latin1"
	| "binary"
	| "hex";
type BufferEncodingOption = "buffer" | { encoding: "buffer" };
declare interface Cache {
	[index: string]: undefined | ResolveRequest | ResolveRequest[];
}
declare class CachedInputFileSystem {
	constructor(fileSystem: BaseFileSystem, duration: number);
	fileSystem: BaseFileSystem;
	lstat?: LStat;
	lstatSync?: LStatSync;
	stat: Stat;
	statSync: StatSync;
	readdir: Readdir;
	readdirSync: ReaddirSync;
	readFile: ReadFile;
	readFileSync: ReadFileSync;
	readJson?: (
		pathOrFileDescription: PathOrFileDescriptor,
		callback: (
			err: null | Error | NodeJS.ErrnoException,
			result?: JsonObject,
		) => void,
	) => void;
	readJsonSync?: (pathOrFileDescription: PathOrFileDescriptor) => JsonObject;
	readlink: Readlink;
	readlinkSync: ReadlinkSync;
	realpath?: RealPath;
	realpathSync?: RealPathSync;
	purge(
		what?:
			| string
			| number
			| URL_url
			| Buffer
			| (string | number | URL_url | Buffer)[]
			| Set<string | number | URL_url | Buffer>,
		options?: { exact?: boolean },
	): void;
}
declare class CloneBasenamePlugin {
	constructor(
		source:
			| string
			| AsyncSeriesBailHook<
					[ResolveRequest, ResolveContext],
					null | ResolveRequest
			  >,
		target:
			| string
			| AsyncSeriesBailHook<
					[ResolveRequest, ResolveContext],
					null | ResolveRequest
			  >,
	);
	source:
		| string
		| AsyncSeriesBailHook<
				[ResolveRequest, ResolveContext],
				null | ResolveRequest
		  >;
	target:
		| string
		| AsyncSeriesBailHook<
				[ResolveRequest, ResolveContext],
				null | ResolveRequest
		  >;
	apply(resolver: Resolver): void;
}
declare interface CompiledAliasOption {
	/**
	 * original alias name
	 */
	name: string;

	/**
	 * name + "/" — precomputed to avoid per-resolve concat
	 */
	nameWithSlash: string;

	/**
	 * alias target(s)
	 */
	alias: Alias;

	/**
	 * normalized onlyModule flag
	 */
	onlyModule: boolean;

	/**
	 * absolute form of `name` (with slash ending), null when not absolute
	 */
	absolutePath: null | string;

	/**
	 * substring before the single "*" in `name`, null when no wildcard
	 */
	wildcardPrefix: null | string;

	/**
	 * substring after the single "*" in `name`, null when no wildcard
	 */
	wildcardSuffix: null | string;

	/**
	 * first character code of `name` — used as a cheap screen on the hot path. `-1` indicates "matches any first char" (empty wildcard prefix).
	 */
	firstCharCode: number;

	/**
	 * true when `alias` is an array — precomputed so the hot path skips `Array.isArray`
	 */
	arrayAlias: boolean;
}
declare interface CompiledAliasOptions {
	/**
	 * declaration-ordered list
	 */
	all: CompiledAliasOption[];

	/**
	 * bucketed by first char code
	 */
	byFirstChar: Map<number, CompiledAliasOption[]>;

	/**
	 * true when an empty-prefix wildcard is present
	 */
	hasAnyFirstChar: boolean;

	/**
	 * true when the bucket fast-path should be used at resolve time
	 */
	useBuckets: boolean;
}
type Context = KnownContext & Record<any, any>;
declare interface Dirent<T extends string | Buffer = string> {
	/**
	 * true when is file, otherwise false
	 */
	isFile: () => boolean;

	/**
	 * true when is directory, otherwise false
	 */
	isDirectory: () => boolean;

	/**
	 * true when is block device, otherwise false
	 */
	isBlockDevice: () => boolean;

	/**
	 * true when is character device, otherwise false
	 */
	isCharacterDevice: () => boolean;

	/**
	 * true when is symbolic link, otherwise false
	 */
	isSymbolicLink: () => boolean;

	/**
	 * true when is FIFO, otherwise false
	 */
	isFIFO: () => boolean;

	/**
	 * true when is socket, otherwise false
	 */
	isSocket: () => boolean;

	/**
	 * name
	 */
	name: T;

	/**
	 * path
	 */
	parentPath: string;

	/**
	 * path
	 */
	path?: string;
}
declare interface DirnameCacheEntry {
	/**
	 * cached dirname function
	 */
	fn: (maybePath: string) => string;

	/**
	 * the underlying cache map
	 */
	cache: Map<string, string>;
}
type EncodingOption =
	| undefined
	| null
	| "ascii"
	| "utf8"
	| "utf-8"
	| "utf16le"
	| "utf-16le"
	| "ucs2"
	| "ucs-2"
	| "base64"
	| "base64url"
	| "latin1"
	| "binary"
	| "hex"
	| ObjectEncodingOptions;
type ErrorWithDetail = Error & { details?: string };
declare interface ExtensionAliasOption {
	alias: string | string[];
	extension: string;
}
declare interface ExtensionAliasOptions {
	[index: string]: string | string[];
}
declare interface FileSystem {
	/**
	 * read file method
	 */
	readFile: ReadFile;

	/**
	 * readdir method
	 */
	readdir: Readdir;

	/**
	 * read json method
	 */
	readJson?: (
		pathOrFileDescription: PathOrFileDescriptor,
		callback: (
			err: null | Error | NodeJS.ErrnoException,
			result?: JsonObject,
		) => void,
	) => void;

	/**
	 * read link method
	 */
	readlink: Readlink;

	/**
	 * lstat method
	 */
	lstat?: LStat;

	/**
	 * stat method
	 */
	stat: Stat;

	/**
	 * realpath method
	 */
	realpath?: RealPath;
}
type IBigIntStats = IStatsBase<bigint> & {
	atimeNs: bigint;
	mtimeNs: bigint;
	ctimeNs: bigint;
	birthtimeNs: bigint;
};
declare interface IStats {
	/**
	 * is file
	 */
	isFile: () => boolean;

	/**
	 * is directory
	 */
	isDirectory: () => boolean;

	/**
	 * is block device
	 */
	isBlockDevice: () => boolean;

	/**
	 * is character device
	 */
	isCharacterDevice: () => boolean;

	/**
	 * is symbolic link
	 */
	isSymbolicLink: () => boolean;

	/**
	 * is FIFO
	 */
	isFIFO: () => boolean;

	/**
	 * is socket
	 */
	isSocket: () => boolean;

	/**
	 * dev
	 */
	dev: number;

	/**
	 * ino
	 */
	ino: number;

	/**
	 * mode
	 */
	mode: number;

	/**
	 * nlink
	 */
	nlink: number;

	/**
	 * uid
	 */
	uid: number;

	/**
	 * gid
	 */
	gid: number;

	/**
	 * rdev
	 */
	rdev: number;

	/**
	 * size
	 */
	size: number;

	/**
	 * blksize
	 */
	blksize: number;

	/**
	 * blocks
	 */
	blocks: number;

	/**
	 * atime ms
	 */
	atimeMs: number;

	/**
	 * mtime ms
	 */
	mtimeMs: number;

	/**
	 * ctime ms
	 */
	ctimeMs: number;

	/**
	 * birthtime ms
	 */
	birthtimeMs: number;

	/**
	 * atime
	 */
	atime: Date;

	/**
	 * mtime
	 */
	mtime: Date;

	/**
	 * ctime
	 */
	ctime: Date;

	/**
	 * birthtime
	 */
	birthtime: Date;
}
declare interface IStatsBase<T> {
	/**
	 * is file
	 */
	isFile: () => boolean;

	/**
	 * is directory
	 */
	isDirectory: () => boolean;

	/**
	 * is block device
	 */
	isBlockDevice: () => boolean;

	/**
	 * is character device
	 */
	isCharacterDevice: () => boolean;

	/**
	 * is symbolic link
	 */
	isSymbolicLink: () => boolean;

	/**
	 * is FIFO
	 */
	isFIFO: () => boolean;

	/**
	 * is socket
	 */
	isSocket: () => boolean;

	/**
	 * dev
	 */
	dev: T;

	/**
	 * ino
	 */
	ino: T;

	/**
	 * mode
	 */
	mode: T;

	/**
	 * nlink
	 */
	nlink: T;

	/**
	 * uid
	 */
	uid: T;

	/**
	 * gid
	 */
	gid: T;

	/**
	 * rdev
	 */
	rdev: T;

	/**
	 * size
	 */
	size: T;

	/**
	 * blksize
	 */
	blksize: T;

	/**
	 * blocks
	 */
	blocks: T;

	/**
	 * atime ms
	 */
	atimeMs: T;

	/**
	 * mtime ms
	 */
	mtimeMs: T;

	/**
	 * ctime ms
	 */
	ctimeMs: T;

	/**
	 * birthtime ms
	 */
	birthtimeMs: T;

	/**
	 * atime
	 */
	atime: Date;

	/**
	 * mtime
	 */
	mtime: Date;

	/**
	 * ctime
	 */
	ctime: Date;

	/**
	 * birthtime
	 */
	birthtime: Date;
}
declare interface Iterator<T, Z> {
	(
		item: T,
		callback: (err?: null | Error, result?: null | Z) => void,
		i: number,
	): void;
}
declare interface JoinCacheEntry {
	/**
	 * cached join function
	 */
	fn: (rootPath: string, request: string) => string;

	/**
	 * the underlying cache map
	 */
	cache: Map<string, Map<string, undefined | string>>;
}
declare interface JsonObject {
	[index: string]:
		| undefined
		| null
		| string
		| number
		| boolean
		| JsonObject
		| JsonValue[];
}
type JsonValue = null | string | number | boolean | JsonObject | JsonValue[];
declare interface KnownContext {
	/**
	 * environments
	 */
	environments?: string[];
}
declare interface KnownHooks {
	/**
	 * resolve step hook
	 */
	resolveStep: SyncHook<
		[
			AsyncSeriesBailHook<
				[ResolveRequest, ResolveContext],
				null | ResolveRequest
			>,
			ResolveRequest,
		]
	>;

	/**
	 * no resolve hook
	 */
	noResolve: SyncHook<[ResolveRequest, Error]>;

	/**
	 * resolve hook
	 */
	resolve: AsyncSeriesBailHook<
		[ResolveRequest, ResolveContext],
		null | ResolveRequest
	>;

	/**
	 * result hook
	 */
	result: AsyncSeriesHook<[ResolveRequest, ResolveContext]>;
}
declare interface LStat {
	(
		path: PathLike,
		callback: (err: null | NodeJS.ErrnoException, result?: IStats) => void,
	): void;
	(
		path: PathLike,
		options: undefined | (StatOptions & { bigint?: false }),
		callback: (err: null | NodeJS.ErrnoException, result?: IStats) => void,
	): void;
	(
		path: PathLike,
		options: StatOptions & { bigint: true },
		callback: (
			err: null | NodeJS.ErrnoException,
			result?: IBigIntStats,
		) => void,
	): void;
	(
		path: PathLike,
		options: undefined | StatOptions,
		callback: (
			err: null | NodeJS.ErrnoException,
			result?: IStats | IBigIntStats,
		) => void,
	): void;
}
declare interface LStatSync {
	(path: PathLike, options?: undefined): IStats;
	(
		path: PathLike,
		options?: StatSyncOptions & { bigint?: false; throwIfNoEntry: false },
	): undefined | IStats;
	(
		path: PathLike,
		options: StatSyncOptions & { bigint: true; throwIfNoEntry: false },
	): undefined | IBigIntStats;
	(path: PathLike, options?: StatSyncOptions & { bigint?: false }): IStats;
	(path: PathLike, options: StatSyncOptions & { bigint: true }): IBigIntStats;
	(
		path: PathLike,
		options: StatSyncOptions & { bigint: boolean; throwIfNoEntry?: false },
	): IStats | IBigIntStats;
	(
		path: PathLike,
		options?: StatSyncOptions,
	): undefined | IStats | IBigIntStats;
}
declare class LogInfoPlugin {
	constructor(
		source:
			| string
			| AsyncSeriesBailHook<
					[ResolveRequest, ResolveContext],
					null | ResolveRequest
			  >,
	);
	source:
		| string
		| AsyncSeriesBailHook<
				[ResolveRequest, ResolveContext],
				null | ResolveRequest
		  >;
	apply(resolver: Resolver): void;
}
declare interface ObjectEncodingOptions {
	/**
	 * encoding
	 */
	encoding?:
		| null
		| "ascii"
		| "utf8"
		| "utf-8"
		| "utf16le"
		| "utf-16le"
		| "ucs2"
		| "ucs-2"
		| "base64"
		| "base64url"
		| "latin1"
		| "binary"
		| "hex";
}
declare interface ParsedIdentifier {
	/**
	 * request
	 */
	request: string;

	/**
	 * query
	 */
	query: string;

	/**
	 * fragment
	 */
	fragment: string;

	/**
	 * is directory
	 */
	directory: boolean;

	/**
	 * is module
	 */
	module: boolean;

	/**
	 * is file
	 */
	file: boolean;

	/**
	 * is internal
	 */
	internal: boolean;
}
declare interface PathCacheFunctions {
	/**
	 * cached join
	 */
	join: JoinCacheEntry;

	/**
	 * cached dirname
	 */
	dirname: DirnameCacheEntry;

	/**
	 * cached basename
	 */
	basename: BasenameCacheEntry;
}
type PathLike = string | URL_url | Buffer;
type PathOrFileDescriptor = string | number | URL_url | Buffer;
type Plugin =
	| undefined
	| null
	| false
	| ""
	| 0
	| { apply: (this: Resolver, resolver: Resolver) => void }
	| ((this: Resolver, resolver: Resolver) => void);
declare interface PnpApi {
	/**
	 * resolve to unqualified
	 */
	resolveToUnqualified: (
		packageName: string,
		issuer: string,
		options: { considerBuiltins: boolean },
	) => null | string;
}
declare interface ReadFile {
	(
		path: PathOrFileDescriptor,
		options:
			| undefined
			| null
			| ({ encoding?: null; flag?: string } & Abortable),
		callback: (err: null | NodeJS.ErrnoException, result?: Buffer) => void,
	): void;
	(
		path: PathOrFileDescriptor,
		options:
			| ({ encoding: BufferEncoding; flag?: string } & Abortable)
			| "ascii"
			| "utf8"
			| "utf-8"
			| "utf16le"
			| "utf-16le"
			| "ucs2"
			| "ucs-2"
			| "base64"
			| "base64url"
			| "latin1"
			| "binary"
			| "hex",
		callback: (err: null | NodeJS.ErrnoException, result?: string) => void,
	): void;
	(
		path: PathOrFileDescriptor,
		options:
			| undefined
			| null
			| "ascii"
			| "utf8"
			| "utf-8"
			| "utf16le"
			| "utf-16le"
			| "ucs2"
			| "ucs-2"
			| "base64"
			| "base64url"
			| "latin1"
			| "binary"
			| "hex"
			| (ObjectEncodingOptions & { flag?: string } & Abortable),
		callback: (
			err: null | NodeJS.ErrnoException,
			result?: string | Buffer,
		) => void,
	): void;
	(
		path: PathOrFileDescriptor,
		callback: (err: null | NodeJS.ErrnoException, result?: Buffer) => void,
	): void;
}
declare interface ReadFileSync {
	(
		path: PathOrFileDescriptor,
		options?: null | { encoding?: null; flag?: string },
	): Buffer;
	(
		path: PathOrFileDescriptor,
		options:
			| "ascii"
			| "utf8"
			| "utf-8"
			| "utf16le"
			| "utf-16le"
			| "ucs2"
			| "ucs-2"
			| "base64"
			| "base64url"
			| "latin1"
			| "binary"
			| "hex"
			| { encoding: BufferEncoding; flag?: string },
	): string;
	(
		path: PathOrFileDescriptor,
		options?:
			| null
			| "ascii"
			| "utf8"
			| "utf-8"
			| "utf16le"
			| "utf-16le"
			| "ucs2"
			| "ucs-2"
			| "base64"
			| "base64url"
			| "latin1"
			| "binary"
			| "hex"
			| (ObjectEncodingOptions & { flag?: string }),
	): string | Buffer;
}
declare interface Readdir {
	(
		path: PathLike,
		options:
			| undefined
			| null
			| "ascii"
			| "utf8"
			| "utf-8"
			| "utf16le"
			| "utf-16le"
			| "ucs2"
			| "ucs-2"
			| "base64"
			| "base64url"
			| "latin1"
			| "binary"
			| "hex"
			| {
					encoding:
						| null
						| "ascii"
						| "utf8"
						| "utf-8"
						| "utf16le"
						| "utf-16le"
						| "ucs2"
						| "ucs-2"
						| "base64"
						| "base64url"
						| "latin1"
						| "binary"
						| "hex";
					withFileTypes?: false;
					recursive?: boolean;
			  },
		callback: (err: null | NodeJS.ErrnoException, files?: string[]) => void,
	): void;
	(
		path: PathLike,
		options:
			| { encoding: "buffer"; withFileTypes?: false; recursive?: boolean }
			| "buffer",
		callback: (err: null | NodeJS.ErrnoException, files?: Buffer[]) => void,
	): void;
	(
		path: PathLike,
		options:
			| undefined
			| null
			| "ascii"
			| "utf8"
			| "utf-8"
			| "utf16le"
			| "utf-16le"
			| "ucs2"
			| "ucs-2"
			| "base64"
			| "base64url"
			| "latin1"
			| "binary"
			| "hex"
			| (ObjectEncodingOptions & {
					withFileTypes?: false;
					recursive?: boolean;
			  }),
		callback: (
			err: null | NodeJS.ErrnoException,
			files?: string[] | Buffer[],
		) => void,
	): void;
	(
		path: PathLike,
		callback: (err: null | NodeJS.ErrnoException, files?: string[]) => void,
	): void;
	(
		path: PathLike,
		options: ObjectEncodingOptions & {
			withFileTypes: true;
			recursive?: boolean;
		},
		callback: (
			err: null | NodeJS.ErrnoException,
			files?: Dirent<string>[],
		) => void,
	): void;
	(
		path: PathLike,
		options: { encoding: "buffer"; withFileTypes: true; recursive?: boolean },
		callback: (
			err: null | NodeJS.ErrnoException,
			files: Dirent<Buffer>[],
		) => void,
	): void;
}
declare interface ReaddirSync {
	(
		path: PathLike,
		options?:
			| null
			| "ascii"
			| "utf8"
			| "utf-8"
			| "utf16le"
			| "utf-16le"
			| "ucs2"
			| "ucs-2"
			| "base64"
			| "base64url"
			| "latin1"
			| "binary"
			| "hex"
			| {
					encoding:
						| null
						| "ascii"
						| "utf8"
						| "utf-8"
						| "utf16le"
						| "utf-16le"
						| "ucs2"
						| "ucs-2"
						| "base64"
						| "base64url"
						| "latin1"
						| "binary"
						| "hex";
					withFileTypes?: false;
					recursive?: boolean;
			  },
	): string[];
	(
		path: PathLike,
		options:
			| "buffer"
			| { encoding: "buffer"; withFileTypes?: false; recursive?: boolean },
	): Buffer[];
	(
		path: PathLike,
		options?:
			| null
			| "ascii"
			| "utf8"
			| "utf-8"
			| "utf16le"
			| "utf-16le"
			| "ucs2"
			| "ucs-2"
			| "base64"
			| "base64url"
			| "latin1"
			| "binary"
			| "hex"
			| (ObjectEncodingOptions & {
					withFileTypes?: false;
					recursive?: boolean;
			  }),
	): string[] | Buffer[];
	(
		path: PathLike,
		options: ObjectEncodingOptions & {
			withFileTypes: true;
			recursive?: boolean;
		},
	): Dirent<string>[];
	(
		path: PathLike,
		options: { encoding: "buffer"; withFileTypes: true; recursive?: boolean },
	): Dirent<Buffer>[];
}
declare interface Readlink {
	(
		path: PathLike,
		options: EncodingOption,
		callback: (err: null | NodeJS.ErrnoException, result?: string) => void,
	): void;
	(
		path: PathLike,
		options: BufferEncodingOption,
		callback: (err: null | NodeJS.ErrnoException, result?: Buffer) => void,
	): void;
	(
		path: PathLike,
		options: EncodingOption,
		callback: (
			err: null | NodeJS.ErrnoException,
			result?: string | Buffer,
		) => void,
	): void;
	(
		path: PathLike,
		callback: (err: null | NodeJS.ErrnoException, result?: string) => void,
	): void;
}
declare interface ReadlinkSync {
	(path: PathLike, options?: EncodingOption): string;
	(path: PathLike, options: BufferEncodingOption): Buffer;
	(path: PathLike, options?: EncodingOption): string | Buffer;
}
declare interface RealPath {
	(
		path: PathLike,
		options: EncodingOption,
		callback: (err: null | NodeJS.ErrnoException, result?: string) => void,
	): void;
	(
		path: PathLike,
		options: BufferEncodingOption,
		callback: (err: null | NodeJS.ErrnoException, result?: Buffer) => void,
	): void;
	(
		path: PathLike,
		options: EncodingOption,
		callback: (
			err: null | NodeJS.ErrnoException,
			result?: string | Buffer,
		) => void,
	): void;
	(
		path: PathLike,
		callback: (err: null | NodeJS.ErrnoException, result?: string) => void,
	): void;
}
declare interface RealPathSync {
	(path: PathLike, options?: EncodingOption): string;
	(path: PathLike, options: BufferEncodingOption): Buffer;
	(path: PathLike, options?: EncodingOption): string | Buffer;
}
declare interface ResolveContext {
	/**
	 * directories that was found on file system
	 */
	contextDependencies?: WriteOnlySet<string>;

	/**
	 * files that was found on file system
	 */
	fileDependencies?: WriteOnlySet<string>;

	/**
	 * dependencies that was not found on file system
	 */
	missingDependencies?: WriteOnlySet<string>;

	/**
	 * tip of the resolver call stack (a singly-linked list with Set-like API). For instance, `resolve → parsedResolve → describedResolve`. Accepts a legacy `Set<string>` for back-compat with older callers; it is normalized internally without a hot-path branch.
	 */
	stack?: StackEntry | Set<string>;

	/**
	 * log function
	 */
	log?: (str: string) => void;

	/**
	 * yield result, if provided plugins can return several results
	 */
	yield?: (request: ResolveRequest) => void;
}
declare interface ResolveFunction {
	(
		context: Context,
		parent: string | URL_url,
		specifier: string | URL_url,
		resolveContext?: ResolveContext,
	): string | false;
	(
		parent: string | URL_url,
		specifier: string | URL_url,
		resolveContext?: ResolveContext,
	): string | false;
}
declare interface ResolveFunctionAsync {
	(
		context: Context,
		parent: string | URL_url,
		specifier: string | URL_url,
		resolveContext: ResolveContext,
		callback: (
			err: null | ErrorWithDetail,
			res?: string | false,
			req?: ResolveRequest,
		) => void,
	): void;
	(
		context: Context,
		parent: string | URL_url,
		specifier: string | URL_url,
		callback: (
			err: null | ErrorWithDetail,
			res?: string | false,
			req?: ResolveRequest,
		) => void,
	): void;
	(
		parent: string | URL_url,
		specifier: string | URL_url,
		resolveContext: ResolveContext,
		callback: (
			err: null | ErrorWithDetail,
			res?: string | false,
			req?: ResolveRequest,
		) => void,
	): void;
	(
		parent: string | URL_url,
		specifier: string | URL_url,
		callback: (
			err: null | ErrorWithDetail,
			res?: string | false,
			req?: ResolveRequest,
		) => void,
	): void;
}
declare interface ResolveFunctionPromise {
	(
		context: Context,
		parent: string | URL_url,
		specifier: string | URL_url,
		resolveContext?: ResolveContext,
	): Promise<string | false>;
	(
		parent: string | URL_url,
		specifier: string | URL_url,
		resolveContext?: ResolveContext,
	): Promise<string | false>;
}
type ResolveOptionsOptionalFS = Omit<
	ResolveOptionsResolverFactoryObject_2,
	"fileSystem"
> &
	Partial<Pick<ResolveOptionsResolverFactoryObject_2, "fileSystem">>;
declare interface ResolveOptionsResolverFactoryObject_1 {
	/**
	 * alias
	 */
	alias: AliasOption[];

	/**
	 * fallback
	 */
	fallback: AliasOption[];

	/**
	 * alias fields
	 */
	aliasFields: Set<string | string[]>;

	/**
	 * extension alias
	 */
	extensionAlias: ExtensionAliasOption[];

	/**
	 * apply extension alias to exports field targets
	 */
	extensionAliasForExports: boolean;

	/**
	 * cache predicate
	 */
	cachePredicate: (predicate: ResolveRequest) => boolean;

	/**
	 * cache with context
	 */
	cacheWithContext: boolean;

	/**
	 * A list of exports field condition names.
	 */
	conditionNames: Set<string>;

	/**
	 * description files
	 */
	descriptionFiles: string[];

	/**
	 * enforce extension
	 */
	enforceExtension: boolean;

	/**
	 * exports fields
	 */
	exportsFields: Set<string | string[]>;

	/**
	 * imports fields
	 */
	importsFields: Set<string | string[]>;

	/**
	 * extensions
	 */
	extensions: Set<string>;

	/**
	 * fileSystem
	 */
	fileSystem: FileSystem;

	/**
	 * unsafe cache
	 */
	unsafeCache: false | Cache;

	/**
	 * symlinks
	 */
	symlinks: boolean;

	/**
	 * resolver
	 */
	resolver?: Resolver;

	/**
	 * modules
	 */
	modules: (string | string[])[];

	/**
	 * main fields
	 */
	mainFields: { name: string[]; forceRelative: boolean }[];

	/**
	 * main files
	 */
	mainFiles: Set<string>;

	/**
	 * plugins
	 */
	plugins: Plugin[];

	/**
	 * pnp API
	 */
	pnpApi: null | PnpApi;

	/**
	 * roots
	 */
	roots: Set<string>;

	/**
	 * fully specified
	 */
	fullySpecified: boolean;

	/**
	 * resolve to context
	 */
	resolveToContext: boolean;

	/**
	 * restrictions
	 */
	restrictions: Set<string | RegExp>;

	/**
	 * prefer relative
	 */
	preferRelative: boolean;

	/**
	 * prefer absolute
	 */
	preferAbsolute: boolean;

	/**
	 * tsconfig file path or config object
	 */
	tsconfig: string | boolean | TsconfigOptions;
}
declare interface ResolveOptionsResolverFactoryObject_2 {
	/**
	 * A list of module alias configurations or an object which maps key to value
	 */
	alias?: UserAliasOptions | UserAliasOptionEntry[];

	/**
	 * A list of module alias configurations or an object which maps key to value, applied only after modules option
	 */
	fallback?: UserAliasOptions | UserAliasOptionEntry[];

	/**
	 * An object which maps extension to extension aliases
	 */
	extensionAlias?: ExtensionAliasOptions;

	/**
	 * Also apply `extensionAlias` to paths resolved through the package.json `exports` field. Off by default (Node.js-aligned); when enabled, matches TypeScript's behavior for packages that ship TS sources alongside compiled JS.
	 */
	extensionAliasForExports?: boolean;

	/**
	 * A list of alias fields in description files
	 */
	aliasFields?: (string | string[])[];

	/**
	 * A function which decides whether a request should be cached or not. An object is passed with at least `path` and `request` properties.
	 */
	cachePredicate?: (predicate: ResolveRequest) => boolean;

	/**
	 * Whether or not the unsafeCache should include request context as part of the cache key.
	 */
	cacheWithContext?: boolean;

	/**
	 * A list of description files to read from
	 */
	descriptionFiles?: string[];

	/**
	 * A list of exports field condition names.
	 */
	conditionNames?: string[];

	/**
	 * Enforce that a extension from extensions must be used
	 */
	enforceExtension?: boolean;

	/**
	 * A list of exports fields in description files
	 */
	exportsFields?: (string | string[])[];

	/**
	 * A list of imports fields in description files
	 */
	importsFields?: (string | string[])[];

	/**
	 * A list of extensions which should be tried for files
	 */
	extensions?: string[];

	/**
	 * The file system which should be used
	 */
	fileSystem: FileSystem;

	/**
	 * Use this cache object to unsafely cache the successful requests
	 */
	unsafeCache?: boolean | Cache;

	/**
	 * Resolve symlinks to their symlinked location
	 */
	symlinks?: boolean;

	/**
	 * A prepared Resolver to which the plugins are attached
	 */
	resolver?: Resolver;

	/**
	 * A list of directories to resolve modules from, can be absolute path, folder name, or a `file:` `URL` instance
	 */
	modules?: string | URL_url | (string | URL_url)[];

	/**
	 * A list of main fields in description files
	 */
	mainFields?: (
		| string
		| string[]
		| { name: string | string[]; forceRelative: boolean }
	)[];

	/**
	 * A list of main files in directories
	 */
	mainFiles?: string[];

	/**
	 * A list of additional resolve plugins which should be applied
	 */
	plugins?: Plugin[];

	/**
	 * A PnP API that should be used - null is "never", undefined is "auto"
	 */
	pnpApi?: null | PnpApi;

	/**
	 * A list of root paths, each an absolute path or a `file:` `URL` instance
	 */
	roots?: (string | URL_url)[];

	/**
	 * The request is already fully specified and no extensions or directories are resolved for it
	 */
	fullySpecified?: boolean;

	/**
	 * Resolve to a context instead of a file
	 */
	resolveToContext?: boolean;

	/**
	 * A list of resolve restrictions, each an absolute path, a `file:` `URL` instance, or a RegExp
	 */
	restrictions?: (string | RegExp | URL_url)[];

	/**
	 * Use only the sync constraints of the file system calls
	 */
	useSyncFileSystemCalls?: boolean;

	/**
	 * Prefer to resolve module requests as relative requests before falling back to modules
	 */
	preferRelative?: boolean;

	/**
	 * Prefer to resolve server-relative urls as absolute paths before falling back to resolve in roots
	 */
	preferAbsolute?: boolean;

	/**
	 * TypeScript config file path (or `file:` `URL` instance) or config object with configFile and references
	 */
	tsconfig?: string | boolean | URL_url | UserTsconfigOptions;
}
type ResolveRequest = BaseResolveRequest & Partial<ParsedIdentifier>;
declare abstract class Resolver {
	fileSystem: FileSystem;
	options: ResolveOptionsResolverFactoryObject_1;
	pathCache: PathCacheFunctions;
	hooks: KnownHooks;
	ensureHook(
		name:
			| string
			| AsyncSeriesBailHook<
					[ResolveRequest, ResolveContext],
					null | ResolveRequest
			  >,
	): AsyncSeriesBailHook<
		[ResolveRequest, ResolveContext],
		null | ResolveRequest
	>;
	getHook(
		name:
			| string
			| AsyncSeriesBailHook<
					[ResolveRequest, ResolveContext],
					null | ResolveRequest
			  >,
	): AsyncSeriesBailHook<
		[ResolveRequest, ResolveContext],
		null | ResolveRequest
	>;
	resolveSync(
		parent: string | URL_url,
		specifier: string | URL_url,
		resolveContext?: ResolveContext,
	): string | false;
	resolveSync(
		context: Context,
		parent: string | URL_url,
		specifier: string | URL_url,
		resolveContext?: ResolveContext,
	): string | false;
	resolvePromise(
		parent: string | URL_url,
		specifier: string | URL_url,
		resolveContext?: ResolveContext,
	): Promise<string | false>;
	resolvePromise(
		context: Context,
		parent: string | URL_url,
		specifier: string | URL_url,
		resolveContext?: ResolveContext,
	): Promise<string | false>;
	resolve(
		parent: string | URL_url,
		specifier: string | URL_url,
		callback: (
			err: null | ErrorWithDetail,
			res?: string | false,
			req?: ResolveRequest,
		) => void,
	): void;
	resolve(
		parent: string | URL_url,
		specifier: string | URL_url,
		resolveContext: ResolveContext,
		callback: (
			err: null | ErrorWithDetail,
			res?: string | false,
			req?: ResolveRequest,
		) => void,
	): void;
	resolve(
		context: Context,
		parent: string | URL_url,
		specifier: string | URL_url,
		callback: (
			err: null | ErrorWithDetail,
			res?: string | false,
			req?: ResolveRequest,
		) => void,
	): void;
	resolve(
		context: Context,
		parent: string | URL_url,
		specifier: string | URL_url,
		resolveContext: ResolveContext,
		callback: (
			err: null | ErrorWithDetail,
			res?: string | false,
			req?: ResolveRequest,
		) => void,
	): void;
	doResolve(
		hook: AsyncSeriesBailHook<
			[ResolveRequest, ResolveContext],
			null | ResolveRequest
		>,
		request: ResolveRequest,
		message: null | string,
		resolveContext: ResolveContext,
		callback: (err?: null | Error, result?: ResolveRequest) => void,
	): void;
	parse(identifier: string): ParsedIdentifier;
	isModule(path: string): boolean;
	isPrivate(path: string): boolean;
	isDirectory(path: string): boolean;
	normalize(path: string): string;
	join(path: string, request: string): string;
	dirname(path: string): string;
	basename(path: string, suffix?: string): string;
}

/**
 * Singly-linked stack entry that also exposes a Set-like API
 * (`has`, `size`, iteration). Each `doResolve` call prepends a new
 * `StackEntry` that points at the previous tip via `.parent`, so pushing
 * is O(1) in time and memory. Recursion detection walks the linked list
 * (O(n)) but the stack is typically shallow, so this is cheaper overall
 * than cloning a `Set` per call.
 */
declare abstract class StackEntry {
	name?: string;
	path: string | false;
	request: string;
	query: string;
	fragment: string;
	directory: boolean;
	module: boolean;
	parent?: StackEntry;

	/**
	 * Strings seeded by callers that still pass `stack: new Set([...])`.
	 * Propagated through the chain so deeper `doResolve` calls still see
	 * them during recursion checks. `undefined` in the common case so
	 * there is no extra work on the hot path.
	 */
	preSeeded?: Set<string>;

	/**
	 * Walk the linked list looking for an entry with the same request shape.
	 * Set-compatible: callers that used `stack.has(entry)` keep working.
	 * NOTE: kept monomorphic on purpose. An earlier draft accepted a string
	 * query too (so pre-5.21 plugins keeping their own `Set<string>` of
	 * seen entries could probe the live stack with the formatted form),
	 * but adding the second shape regressed `doResolve`'s heap profile by
	 * ~1 MiB / 200 resolves on stack-churn — V8 keeps a polymorphic
	 * call-site state for `parent.has(stackEntry)` once `has` has two
	 * argument shapes. Plugins that need string membership can reach for
	 * `[...stack].find(e => e.includes(formattedString))` via the
	 * `String`-method proxies on `StackEntry` instead.
	 */
	has(query: StackEntry): boolean;

	/**
	 * Number of entries on the stack (oldest-to-newest length).
	 */
	get size(): number;

	/**
	 * Human-readable form used in recursion error messages, logs, and the
	 * iterator above. Not memoized: caching would require an extra slot on
	 * every `StackEntry`, which costs heap even on resolves that never look
	 * at the formatted form.
	 */
	toString(): string;

	/**
	 * Iterate entries from oldest (root) to newest (tip), matching how a
	 * `Set` that was populated in insertion order would iterate. Pre-seeded
	 * legacy `Set<string>` entries come first so error-message output stays
	 * ordered oldest-to-newest.
	 * Yields each entry as its formatted `toString()` form. Plugins written
	 * against the pre-5.21 `Set<string>` shape — e.g.
	 * `[...resolveContext.stack].find(a => a.includes("module:"))` — keep
	 * working unchanged because each yielded value is a plain string with
	 * all of `String.prototype` available natively. Resolves that never
	 * iterate the stack pay nothing; iteration costs one `toString()`
	 * allocation per stack frame.
	 */
	[Symbol.iterator](): IterableIterator<string>;
}
declare interface Stat {
	(
		path: PathLike,
		callback: (err: null | NodeJS.ErrnoException, result?: IStats) => void,
	): void;
	(
		path: PathLike,
		options: undefined | (StatOptions & { bigint?: false }),
		callback: (err: null | NodeJS.ErrnoException, result?: IStats) => void,
	): void;
	(
		path: PathLike,
		options: StatOptions & { bigint: true },
		callback: (
			err: null | NodeJS.ErrnoException,
			result?: IBigIntStats,
		) => void,
	): void;
	(
		path: PathLike,
		options: undefined | StatOptions,
		callback: (
			err: null | NodeJS.ErrnoException,
			result?: IStats | IBigIntStats,
		) => void,
	): void;
}
declare interface StatOptions {
	/**
	 * need bigint values
	 */
	bigint?: boolean;
}
declare interface StatSync {
	(path: PathLike, options?: undefined): IStats;
	(
		path: PathLike,
		options?: StatSyncOptions & { bigint?: false; throwIfNoEntry: false },
	): undefined | IStats;
	(
		path: PathLike,
		options: StatSyncOptions & { bigint: true; throwIfNoEntry: false },
	): undefined | IBigIntStats;
	(path: PathLike, options?: StatSyncOptions & { bigint?: false }): IStats;
	(path: PathLike, options: StatSyncOptions & { bigint: true }): IBigIntStats;
	(
		path: PathLike,
		options: StatSyncOptions & { bigint: boolean; throwIfNoEntry?: false },
	): IStats | IBigIntStats;
	(
		path: PathLike,
		options?: StatSyncOptions,
	): undefined | IStats | IBigIntStats;
}
declare interface StatSyncOptions {
	/**
	 * need bigint values
	 */
	bigint?: boolean;

	/**
	 * throw if no entry
	 */
	throwIfNoEntry?: boolean;
}
declare interface SyncFileSystem {
	/**
	 * read file sync method
	 */
	readFileSync: ReadFileSync;

	/**
	 * read dir sync method
	 */
	readdirSync: ReaddirSync;

	/**
	 * read json sync method
	 */
	readJsonSync?: (pathOrFileDescription: PathOrFileDescriptor) => JsonObject;

	/**
	 * read link sync method
	 */
	readlinkSync: ReadlinkSync;

	/**
	 * lstat sync method
	 */
	lstatSync?: LStatSync;

	/**
	 * stat sync method
	 */
	statSync: StatSync;

	/**
	 * real path sync method
	 */
	realpathSync?: RealPathSync;
}
declare interface TsconfigOptions {
	/**
	 * A relative path to the tsconfig file based on cwd, or an absolute path of tsconfig file
	 */
	configFile?: string;

	/**
	 * References to other tsconfig files. 'auto' inherits from TypeScript config, or an array of relative/absolute paths
	 */
	references?: string[] | "auto";

	/**
	 * Override baseUrl from tsconfig.json. If provided, this value will be used instead of the baseUrl in the tsconfig file
	 */
	baseUrl?: string;
}
declare interface TsconfigPathsData {
	/**
	 * tsconfig file data
	 */
	alias: CompiledAliasOptions;

	/**
	 * tsconfig file data
	 */
	modules: string[];
}
declare interface TsconfigPathsMap {
	/**
	 * main tsconfig paths data
	 */
	main: TsconfigPathsData;

	/**
	 * main tsconfig base URL (absolute path)
	 */
	mainContext: string;

	/**
	 * referenced tsconfig paths data mapped by baseUrl
	 */
	refs: { [index: string]: TsconfigPathsData };

	/**
	 * all contexts (main + refs) for quick lookup
	 */
	allContexts: { [index: string]: TsconfigPathsData };

	/**
	 * precomputed `Object.keys(allContexts)` — read-only; used on the `_selectPathsDataForContext` hot path
	 */
	contextList: string[];

	/**
	 * file dependencies
	 */
	fileDependencies: Set<string>;
}
declare class TsconfigPathsPlugin {
	constructor(configFileOrOptions: string | true | TsconfigOptions);
	isAutoConfigFile: boolean;
	configFile: string;
	references: "auto" | TsconfigReference[];
	baseUrl?: string;
	apply(resolver: Resolver): void;
}
declare interface TsconfigReference {
	/**
	 * Path to the referenced project
	 */
	path: string;
}
declare interface URL_url extends URL_Import {}
declare interface UserAliasOptionEntry {
	alias: UserAliasOptionNewRequest;
	name: string;
	onlyModule?: boolean;
}
type UserAliasOptionNewRequest =
	| string
	| false
	| URL_url
	| (string | URL_url)[];
declare interface UserAliasOptions {
	[index: string]: UserAliasOptionNewRequest;
}
declare interface UserTsconfigOptions {
	/**
	 * A path, or `file:` `URL` instance, pointing at the tsconfig file
	 */
	configFile?: string | URL_url;

	/**
	 * References to other tsconfig files. 'auto' inherits from TypeScript config, or an array of relative/absolute paths or `file:` `URL` instances
	 */
	references?: (string | URL_url)[] | "auto";

	/**
	 * Override baseUrl from tsconfig.json with a path or `file:` `URL` instance
	 */
	baseUrl?: string | URL_url;
}
declare interface WriteOnlySet<T> {
	add: (item: T) => void;
}
declare function exports(
	context: Context,
	parent: string | URL_url,
	specifier: string | URL_url,
	resolveContext: ResolveContext,
	callback: (
		err: null | ErrorWithDetail,
		res?: string | false,
		req?: ResolveRequest,
	) => void,
): void;
declare function exports(
	context: Context,
	parent: string | URL_url,
	specifier: string | URL_url,
	callback: (
		err: null | ErrorWithDetail,
		res?: string | false,
		req?: ResolveRequest,
	) => void,
): void;
declare function exports(
	parent: string | URL_url,
	specifier: string | URL_url,
	resolveContext: ResolveContext,
	callback: (
		err: null | ErrorWithDetail,
		res?: string | false,
		req?: ResolveRequest,
	) => void,
): void;
declare function exports(
	parent: string | URL_url,
	specifier: string | URL_url,
	callback: (
		err: null | ErrorWithDetail,
		res?: string | false,
		req?: ResolveRequest,
	) => void,
): void;
declare namespace exports {
	export const sync: ResolveFunction;
	export const promise: ResolveFunctionPromise;
	export function create(
		options: ResolveOptionsOptionalFS,
	): ResolveFunctionAsync;
	export namespace create {
		export const sync: (options: ResolveOptionsOptionalFS) => ResolveFunction;
		export const promise: (
			options: ResolveOptionsOptionalFS,
		) => ResolveFunctionPromise;
	}
	export namespace ResolverFactory {
		export let createResolver: (
			options: ResolveOptionsResolverFactoryObject_2,
		) => Resolver;
	}
	export const forEachBail: <T, Z>(
		array: T[],
		iterator: Iterator<T, Z>,
		callback: (err?: null | Error, result?: null | Z, i?: number) => void,
	) => void;
	export type ResolveCallback = (
		err: null | ErrorWithDetail,
		res?: string | false,
		req?: ResolveRequest,
	) => void;
	export {
		CachedInputFileSystem,
		CloneBasenamePlugin,
		LogInfoPlugin,
		TsconfigPathsPlugin,
		ResolveOptionsOptionalFS,
		BaseFileSystem,
		PnpApi,
		Resolver,
		Context,
		FileSystem,
		ResolveContext,
		ResolveRequest,
		SyncFileSystem,
		Plugin,
		ResolveOptionsResolverFactoryObject_2 as ResolveOptions,
		ResolveFunctionAsync,
		ResolveFunction,
		ResolveFunctionPromise,
	};
}

export = exports;
