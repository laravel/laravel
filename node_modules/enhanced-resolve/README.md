# enhanced-resolve

[![npm][npm]][npm-url]
[![Build Status][build-status]][build-status-url]
[![codecov][codecov-badge]][codecov-url]
[![Install Size][size]][size-url]
[![GitHub Discussions][discussion]][discussion-url]

Offers an async require.resolve function. It's highly configurable.

## Features

- plugin system
- provide a custom filesystem
- sync and async node.js filesystems included

## Getting Started

### Install

```sh
# npm
npm install enhanced-resolve
# or Yarn
yarn add enhanced-resolve
# or pnpm
pnpm add enhanced-resolve
```

### Resolve

There is a Node.js API which allows to resolve requests according to the Node.js resolving rules.
Sync, async (callback) and promise APIs are offered. A `create` method allows to create a custom resolve function.

```js
const resolve = require("enhanced-resolve");

resolve("/some/path/to/folder", "module/dir", (err, result) => {
	result; // === "/some/path/node_modules/module/dir/index.js"
});

resolve.sync("/some/path/to/folder", "../../dir");
// === "/some/path/dir/index.js"

const result = await resolve.promise("/some/path/to/folder", "../../dir");
// === "/some/path/dir/index.js"

const myResolve = resolve.create({
	// or resolve.create.sync / resolve.create.promise
	extensions: [".ts", ".js"],
	// see more options below
});

myResolve("/some/path/to/folder", "ts-module", (err, result) => {
	result; // === "/some/node_modules/ts-module/index.ts"
});
```

### Public API

All of the following are exposed from `require("enhanced-resolve")`.

#### `resolve(context?, parent, specifier, resolveContext?, callback)`

Async Node-style resolver using the built-in defaults (`conditionNames: ["node"]`, `extensions: [".js", ".json", ".node"]`). Both `context` and `resolveContext` are optional, so the function accepts four shapes:

```js
resolve(context, parent, specifier, resolveContext, callback);
resolve(context, parent, specifier, callback);
resolve(parent, specifier, resolveContext, callback);
resolve(parent, specifier, callback); // most common
```

The `parent` / `specifier` naming mirrors the ECMAScript resolution terms (as in `import.meta.resolve(specifier, parent)`): `parent` is the location you resolve **from**, and `specifier` is the string being resolved.

##### Arguments — what each one is and when to pass it

| Argument         | Required | What it is                                                                                                                                                                                                                                           | When to pass it                                                                                                               |
| ---------------- | -------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ----------------------------------------------------------------------------------------------------------------------------- |
| `context`        | no       | Extra info about _who_ is requesting, e.g. `{ environments: ["node+es3+es5+process+native"] }`. Plugins can read arbitrary keys off it. When omitted, a built-in Node context is used.                                                               | Pass it only when a plugin you use reads from it. Most callers omit it.                                                       |
| `parent`         | yes      | The absolute directory to resolve **from** — the location the `specifier` is relative to (the ECMAScript "parent", historically called the "lookup start path").                                                                                     | Always.                                                                                                                       |
| `specifier`      | yes      | The string to resolve — a relative specifier (`./utils`), a bare module (`lodash/merge`), an alias, etc. (the ECMAScript "specifier", historically called the "request").                                                                            | Always.                                                                                                                       |
| `resolveContext` | no       | A collector object the resolver writes into / reads from: `fileDependencies`, `contextDependencies`, `missingDependencies` (`Set`s it fills in), `log` (a function it calls for trace output).                                                       | Pass it when you need to know which files/dirs were touched (e.g. to set up a watcher) or want a log of the resolution steps. |
| `callback`       | yes      | `(err, result, resolveRequest) => void`. `result` is the resolved absolute path (or `false` if the request resolves to an ignored module). `resolveRequest` is the full request object with `path`, `query`, `fragment`, `descriptionFilePath`, etc. | Always.                                                                                                                       |

##### Minimal form — just `parent`, `specifier`, `callback`

```js
const resolve = require("enhanced-resolve");

resolve(__dirname, "./utils", (err, result) => {
	if (err) throw err;
	result; // === "/abs/path/to/utils.js"
});
```

##### Inspecting the full result via the third callback argument

```js
resolve(__dirname, "./utils?query#frag", (err, result, resolveRequest) => {
	result; // === "/abs/path/to/utils.js?query#frag" — path with query/fragment appended
	resolveRequest.path; // === "/abs/path/to/utils.js" (the bare path, no query/fragment)
	resolveRequest.query; // === "?query"  (empty string if the request has none)
	resolveRequest.fragment; // === "#frag"   (empty string if the request has none)
	resolveRequest.descriptionFilePath; // path to the nearest package.json, if any
});
```

##### With `resolveContext` — collect dependencies and a log

Pass a `resolveContext` when you need the resolver to report back _what it looked at_. The `Set`s you provide are filled in during resolution, and `log` is called with each step:

```js
const fileDependencies = new Set();
const missingDependencies = new Set();
const contextDependencies = new Set();
const logs = [];

resolve(
	__dirname,
	"./utils",
	{
		fileDependencies, // files that were read/checked
		missingDependencies, // paths that were probed but didn't exist
		contextDependencies, // directories that were read
		log: (msg) => logs.push(msg), // trace of each resolution step
	},
	(err, result) => {
		result; // === "/abs/path/to/utils.js"
		fileDependencies; // Set { ".../utils.js", ".../package.json", ... }
		missingDependencies; // Set { ".../utils", ".../utils.json", ... } (candidates tried)
		// `fileDependencies` / `missingDependencies` are exactly what you'd feed
		// a file watcher so a rebuild re-runs when any of them changes.
	},
);
```

##### With a custom `context`

Pass a `context` when a plugin reads from it. The default resolver only uses `environments`, but custom plugins may key off anything you put here:

```js
resolve(
	{ environments: ["node+es3+es5+process+native"] }, // context
	__dirname,
	"./utils",
	(err, result) => {
		result; // === "/abs/path/to/utils.js"
	},
);
```

#### `resolve.sync(context?, parent, specifier, resolveContext?) => string | false`

Synchronous variant. Throws on failure, returns `false` when the resolve yields no result.

```js
const file = resolve.sync(__dirname, "./utils");
```

#### `resolve.promise(context?, parent, specifier, resolveContext?) => Promise<string | false>`

Promise variant of `resolve`.

```js
const file = await resolve.promise(__dirname, "./utils");
```

#### `resolve.create(options) => ResolveFunctionAsync`

Builds a customized async resolve function. Options are the same as for [`ResolverFactory.createResolver`](#resolver-options); `fileSystem` defaults to the built-in Node.js filesystem.

```js
const resolveTs = resolve.create({ extensions: [".ts", ".tsx", ".js"] });

resolveTs(__dirname, "./component", (err, result) => {
	// result === "/abs/path/to/component.tsx"
});
```

#### `resolve.create.sync(options) => ResolveFunction`

Sync variant of `resolve.create`.

```js
const resolveTsSync = resolve.create.sync({ extensions: [".ts", ".js"] });
const file = resolveTsSync(__dirname, "./component");
```

#### `resolve.create.promise(options) => ResolveFunctionPromise`

Promise variant of `resolve.create`.

```js
const resolveTsPromise = resolve.create.promise({ extensions: [".ts", ".js"] });
const file = await resolveTsPromise(__dirname, "./component");
```

#### `ResolverFactory.createResolver(options) => Resolver`

Lower-level factory. Returns a `Resolver` whose `resolve`, `resolveSync`, and `resolvePromise` methods accept `(context, parent, specifier, resolveContext, [callback])`. Use this when you need a reusable resolver instance or access to its `hooks` (see the [Plugins](#plugins) section). `fileSystem` is required here — the high-level `resolve.create` defaults it for you.

```js
const fs = require("fs");
const { CachedInputFileSystem, ResolverFactory } = require("enhanced-resolve");

const resolver = ResolverFactory.createResolver({
	fileSystem: new CachedInputFileSystem(fs, 4000),
	extensions: [".js", ".json"],
});

// callback
resolver.resolve({}, __dirname, "./utils", {}, (err, file) => {
	// ...
});

// sync (requires a sync fileSystem)
const fileSync = resolver.resolveSync({}, __dirname, "./utils");

// promise
const filePromise = await resolver.resolvePromise({}, __dirname, "./utils", {});
```

#### `CachedInputFileSystem(fileSystem, duration)`

Wraps any Node-compatible `fs` to add an in-memory cache for `stat`, `readdir`, `readFile`, `readJson`, and `readlink`. `duration` is the cache TTL in milliseconds (typically `4000`). Call `.purge()` to invalidate, or `.purge(path)` / `.purge([path, ...])` to invalidate specific entries — do this whenever you know files changed (e.g. from a watcher).

```js
const fs = require("fs");
const { CachedInputFileSystem } = require("enhanced-resolve");

const cachedFs = new CachedInputFileSystem(fs, 4000);
// later, when files change:
cachedFs.purge("/abs/path/to/changed-file.js");
```

#### Exported plugins & helpers

For use with the `plugins` option or as standalone utilities:

- `ResolverFactory` — see above.
- `CachedInputFileSystem` — see above.
- `CloneBasenamePlugin(source, target)` — joins the directory's basename onto the path. See [Built-in Plugins](#built-in-plugins).
- `LogInfoPlugin(source)` — logs pipeline state at a hook; enable by passing a `log` function on the `resolveContext`.
- `TsconfigPathsPlugin(options)` — applies `tsconfig.json` `paths` / `baseUrl` mappings; typically configured via the `tsconfig` resolver option instead.
- `forEachBail(array, iterator, callback)` — bail-style async iterator used internally; useful when authoring plugins that try several candidates in order.

```js
const { LogInfoPlugin } = require("enhanced-resolve");

const resolver = ResolverFactory.createResolver({
	fileSystem: cachedFs,
	extensions: [".js"],
	plugins: [new LogInfoPlugin("described-resolve")],
});

resolver.resolve(
	{},
	__dirname,
	"./utils",
	{ log: (msg) => console.log(msg) },
	() => {},
);
```

### Creating a Resolver

The easiest way to create a resolver is to use the `createResolver` function on `ResolveFactory`, along with one of the supplied File System implementations.

```js
const fs = require("fs");
const { CachedInputFileSystem, ResolverFactory } = require("enhanced-resolve");

// create a resolver
const myResolver = ResolverFactory.createResolver({
	// Typical usage will consume the `fs` + `CachedInputFileSystem`, which wraps Node.js `fs` to add caching.
	fileSystem: new CachedInputFileSystem(fs, 4000),
	extensions: [".js", ".json"],
	/* any other resolver options here. Options/defaults can be seen below */
});

// resolve a file with the new resolver
const context = {};
const parent = "/Users/webpack/some/root/dir"; // directory to resolve from
const specifier = "./path/to-look-up.js"; // string to resolve
const resolveContext = {};

// callback
myResolver.resolve(
	context,
	parent,
	specifier,
	resolveContext,
	(err /* Error */, filepath /* string */) => {
		// Do something with the path
	},
);

// promise
try {
	const filepath = await myResolver.resolvePromise(
		context,
		parent,
		specifier,
		resolveContext,
	);
	// Do something with the path
} catch (err) {
	// handle resolve failure
}

// sync (requires a sync fileSystem, e.g. the default Node.js one)
const filepath = myResolver.resolveSync(context, parent, specifier);
```

#### Resolver Options

| Field                    | Default                     | Description                                                                                                                                                                                                                                                                                 |
| ------------------------ | --------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| alias                    | []                          | A list of module alias configurations or an object which maps key to value                                                                                                                                                                                                                  |
| aliasFields              | []                          | A list of alias fields in description files                                                                                                                                                                                                                                                 |
| extensionAlias           | {}                          | An object which maps extension to extension aliases                                                                                                                                                                                                                                         |
| extensionAliasForExports | false                       | Also apply `extensionAlias` to paths resolved through the package.json `exports` field. Off by default (Node.js-aligned)                                                                                                                                                                    |
| cachePredicate           | function() { return true }; | A function which decides whether a request should be cached or not. An object is passed to the function with `path` and `request` properties.                                                                                                                                               |
| cacheWithContext         | true                        | If unsafe cache is enabled, includes `request.context` in the cache key                                                                                                                                                                                                                     |
| conditionNames           | []                          | A list of exports field condition names                                                                                                                                                                                                                                                     |
| descriptionFiles         | ["package.json"]            | A list of description files to read from                                                                                                                                                                                                                                                    |
| enforceExtension         | false                       | Enforce that a extension from extensions must be used                                                                                                                                                                                                                                       |
| exportsFields            | ["exports"]                 | A list of exports fields in description files                                                                                                                                                                                                                                               |
| extensions               | [".js", ".json", ".node"]   | A list of extensions which should be tried for files                                                                                                                                                                                                                                        |
| fallback                 | []                          | Same as `alias`, but only used if default resolving fails                                                                                                                                                                                                                                   |
| fileSystem               |                             | The file system which should be used                                                                                                                                                                                                                                                        |
| fullySpecified           | false                       | Request passed to resolve is already fully specified and extensions or main files are not resolved for it (they are still resolved for internal requests)                                                                                                                                   |
| mainFields               | ["main"]                    | A list of main fields in description files                                                                                                                                                                                                                                                  |
| mainFiles                | ["index"]                   | A list of main files in directories                                                                                                                                                                                                                                                         |
| modules                  | ["node_modules"]            | A list of directories to resolve modules from, can be absolute path or folder name                                                                                                                                                                                                          |
| plugins                  | []                          | A list of additional resolve plugins which should be applied                                                                                                                                                                                                                                |
| resolver                 | undefined                   | A prepared Resolver to which the plugins are attached                                                                                                                                                                                                                                       |
| resolveToContext         | false                       | Resolve to a context instead of a file                                                                                                                                                                                                                                                      |
| preferRelative           | false                       | Prefer to resolve module requests as relative request and fallback to resolving as module                                                                                                                                                                                                   |
| preferAbsolute           | false                       | Prefer to resolve server-relative urls as absolute paths before falling back to resolve in roots                                                                                                                                                                                            |
| restrictions             | []                          | A list of resolve restrictions                                                                                                                                                                                                                                                              |
| roots                    | []                          | A list of root paths                                                                                                                                                                                                                                                                        |
| symlinks                 | true                        | Whether to resolve symlinks to their symlinked location                                                                                                                                                                                                                                     |
| tsconfig                 | false                       | TypeScript config for paths mapping. Can be `false` (disabled), `true` (use default `tsconfig.json`), a string path to `tsconfig.json`, or an object with `configFile`, `references`, and `baseUrl` options. Supports JSONC format (comments and trailing commas) like TypeScript compiler. |
| tsconfig.configFile      | tsconfig.json               | Path to the tsconfig.json file                                                                                                                                                                                                                                                              |
| tsconfig.references      | []                          | Project references. `'auto'` to load from tsconfig, or an array of paths to referenced projects                                                                                                                                                                                             |
| tsconfig.baseUrl         | undefined                   | Override baseUrl from tsconfig.json. If provided, this value will be used instead of the baseUrl in the tsconfig file                                                                                                                                                                       |
| unsafeCache              | false                       | Use this cache object to unsafely cache the successful requests                                                                                                                                                                                                                             |

#### Option Examples

Small snippets for the non-obvious options. All options are passed to `resolve.create({ ... })` or `ResolverFactory.createResolver({ ... })`.

**`alias`** — rewrite matching requests to a target path, module, or to `false` to ignore them. Accepts an object or an array of entries (array form lets you specify ordering / `onlyModule`).

```js
const options = {
	alias: {
		"@": path.resolve(__dirname, "src"), // @/utils → src/utils
		lodash$: "lodash-es", // exact "lodash", not "lodash/foo"
		"ignored-module": false, // short-circuit to an empty module
	},
};
```

**`aliasFields`** — read alias maps from fields in `package.json`. The `browser` field is the common case:

```js
const options = { aliasFields: ["browser"] };
```

**`extensionAlias`** — maps one request extension to a list of candidate extensions. Useful for TypeScript ESM where imports are written with `.js` but the source is `.ts`. Applies both to direct requests (e.g. `./foo.js`) and to paths produced by the package.json `imports` field (e.g. `#foo` → `./foo.js` → `./foo.ts`). By default it does **not** apply to paths produced by the `exports` field (to stay aligned with Node.js, which does not substitute extensions on package-exported paths) — see `extensionAliasForExports` below to opt in:

```js
const options = {
	extensionAlias: {
		".js": [".ts", ".js"],
		".mjs": [".mts", ".mjs"],
	},
};
```

**`extensionAliasForExports`** — when `true`, also apply `extensionAlias` to paths resolved through the package.json `exports` field. Off by default to match Node.js. Turn it on if you want full alignment with TypeScript's resolver for packages that ship `.ts` sources alongside the compiled `.js` files they list in `exports` (e.g. monorepo source packages, or the `eslint-import-resolver-typescript` use case):

```js
const options = {
	extensionAlias: { ".js": [".ts", ".js"] },
	extensionAliasForExports: true,
};
```

**`conditionNames` + `exportsFields`** — pick which conditions to match in the `exports` field of `package.json`:

```js
const options = {
	conditionNames: ["import", "node", "default"],
	exportsFields: ["exports"],
};
```

**`extensions`** — extensions to try for extensionless requests, in order:

```js
const options = { extensions: [".ts", ".tsx", ".js", ".json"] };
```

**`fallback`** — same shape as `alias`, but only consulted when the primary resolve fails. Handy for polyfills:

```js
const options = {
	fallback: {
		crypto: require.resolve("crypto-browserify"),
		stream: false,
	},
};
```

**`modules`** — where to look for bare-module requests. Entries can be folder names (searched hierarchically up the tree) or absolute paths (searched directly):

```js
const options = { modules: [path.resolve(__dirname, "src"), "node_modules"] };
```

**`mainFields` / `mainFiles`** — fields in `package.json` to try for a package entry point, and filenames to try inside a directory:

```js
const options = {
	mainFields: ["browser", "module", "main"],
	mainFiles: ["index"],
};
```

**`roots` + `preferAbsolute`** — resolve server-relative URLs (starting with `/`) against one or more root directories. With `preferAbsolute: true`, absolute-path resolution is tried before the roots are consulted.

```js
const options = {
	roots: [path.resolve(__dirname, "public")],
	preferAbsolute: false,
};
```

**`restrictions`** — reject results that don't satisfy at least one restriction. Accepts strings (path prefixes) or `RegExp`s:

```js
const options = {
	restrictions: [path.resolve(__dirname, "src"), /\.(js|ts)$/],
};
```

**`tsconfig`** — apply TypeScript `paths` / `baseUrl` mappings. Either pass `true` to load `./tsconfig.json`, a path string, or a configuration object:

```js
const options = {
	tsconfig: {
		configFile: path.resolve(__dirname, "tsconfig.json"),
		references: "auto", // honor project references declared in tsconfig
	},
};
```

**`symlinks`** — resolve to the real path by following symlinks. Set to `false` to keep the symlinked path (common for monorepo / pnpm layouts where you want module identity tied to the workspace location):

```js
const options = { symlinks: false };
```

**`fullySpecified`** — require fully-specified requests (no extension inference, no `index` lookup) for non-internal requests. Matches Node.js ESM semantics:

```js
const options = { fullySpecified: true };
```

**`unsafeCache`** — pass an object to use as an in-memory cache of successful resolves. Set to `true` to let the resolver allocate its own:

```js
const options = {
	unsafeCache: {}, // or true
	cacheWithContext: false, // skip context in the cache key — faster, but only safe if context doesn't change the result
};
```

To observe whether a request was served from the cache, wrap the cache object in a `Proxy`. `UnsafeCachePlugin` reads entries with `cache[id]` (cache lookup) and writes them with `cache[id] = result` (cache store), so trapping `get` and `set` is enough to distinguish hits from misses:

```js
const cache = {};
const observedCache = new Proxy(cache, {
	get(target, name, receiver) {
		const hit = name in target;
		console.log(hit ? `[cache hit]  ${name}` : `[cache miss] ${name}`);
		return Reflect.get(target, name, receiver);
	},
	set(target, name, value, receiver) {
		console.log(`[cache set]  ${name}`);
		return Reflect.set(target, name, value, receiver);
	},
});

const resolver = ResolverFactory.createResolver({
	fileSystem: new CachedInputFileSystem(fs, 4000),
	extensions: [".js", ".json"],
	unsafeCache: observedCache,
});
```

The `name` argument is the cache id — a `JSON.stringify`'d object containing `type`, `context`, `path`, `query`, `fragment`, and `request` — so you can parse it to report on specific resolves. Only successful resolves go through the cache; failures never touch it.

**`fileSystem`** — any `fs`-compatible implementation. Usually `new CachedInputFileSystem(fs, 4000)`; can be a virtual filesystem (e.g. `memfs`) for testing:

```js
const options = { fileSystem: new CachedInputFileSystem(require("fs"), 4000) };
```

**`plugins`** — additional plugin instances appended to the pipeline. See [Plugins](#plugins):

```js
const options = {
	plugins: [new TsconfigPathsPlugin({ configFile: "./tsconfig.json" })],
};
```

## Plugins

Similar to `webpack`, the core of `enhanced-resolve` functionality is implemented as individual plugins that are executed using [`tapable`](https://github.com/webpack/tapable).
These plugins can extend the functionality of the library, adding other ways for files/contexts to be resolved.

A plugin should be a `class` (or its ES5 equivalent) with an `apply` method. The `apply` method will receive a `resolver` instance, that can be used to hook in to the event system.

Plugins are executed in a pipeline, and register which event they should be executed before/after. `source` is the name of the event that starts the pipeline, and `target` is what event this plugin should fire, which is what continues the execution of the pipeline. For a full view of how these plugin events form a chain, see `lib/ResolverFactory.js`, in the `//// pipeline ////` section.

### Built-in Plugins

`enhanced-resolve` ships with the following plugins. Most of them are wired up automatically by `ResolverFactory` based on the [resolver options](#resolver-options); the ones exported from the package entry (`TsconfigPathsPlugin`, `CloneBasenamePlugin`, `LogInfoPlugin`) are the ones you're most likely to use explicitly.

| Plugin                                   | Purpose                                                                                                                              |
| ---------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------ |
| `AliasPlugin`                            | Replaces a matching request with one or more alternative targets. Powers the `alias` and `fallback` options.                         |
| `AliasFieldPlugin`                       | Applies aliasing based on a field in the description file (e.g. the `browser` field). Powers `aliasFields`.                          |
| `AppendPlugin`                           | Appends a string (typically an extension) to the current path. Used for `extensions`.                                                |
| `CloneBasenamePlugin`                    | Joins the current directory basename onto the path (e.g. `/foo/bar` → `/foo/bar/bar`). Useful for directory-named main-file schemes. |
| `ConditionalPlugin`                      | Forwards the request only when it matches a given partial request shape.                                                             |
| `DescriptionFilePlugin`                  | Finds and loads the nearest description file (e.g. `package.json`) so other plugins can read its fields. Powers `descriptionFiles`.  |
| `DirectoryExistsPlugin`                  | Only continues the pipeline if the current path is an existing directory.                                                            |
| `ExportsFieldPlugin`                     | Resolves requests through the `exports` field of a package's description file. Powers `exportsFields` and `conditionNames`.          |
| `ExtensionAliasPlugin`                   | Maps one extension to a list of alternative extensions (e.g. `.js` → `.ts`, `.js`). Powers `extensionAlias`.                         |
| `FileExistsPlugin`                       | Only continues the pipeline if the current path is an existing file, and records the file as a dependency.                           |
| `ImportsFieldPlugin`                     | Resolves `#name` requests through the `imports` field of the enclosing package.                                                      |
| `JoinRequestPlugin`                      | Joins the current path with the current request into a new path.                                                                     |
| `JoinRequestPartPlugin`                  | Splits a module request into module name + inner request, joining the inner request onto the path.                                   |
| `LogInfoPlugin`                          | Emits verbose log output at a given pipeline step. Handy for debugging resolves via `resolveContext.log`.                            |
| `MainFieldPlugin`                        | Uses a field in the description file (e.g. `main`) to point to the entry file of a package. Powers `mainFields`.                     |
| `ModulesInHierarchicalDirectoriesPlugin` | Searches for a module by walking up parent directories (the standard `node_modules` lookup). Powers `modules`.                       |
| `ModulesInRootPlugin`                    | Searches for a module in a single absolute directory. Powers absolute-path entries in `modules`.                                     |
| `NextPlugin`                             | Forwards the request from one hook to another without modification — glue between pipeline steps.                                    |
| `ParsePlugin`                            | Parses a raw request string into its components (path, query, fragment, module flag, etc.).                                          |
| `PnpPlugin`                              | Resolves module requests through a Yarn PnP API when one is available.                                                               |
| `RestrictionsPlugin`                     | Rejects results that don't match a list of path restrictions (strings or regular expressions). Powers `restrictions`.                |
| `ResultPlugin`                           | Terminal plugin that fires the `result` hook — signals a successful resolve.                                                         |
| `RootsPlugin`                            | Resolves server-relative URL requests (starting with `/`) against one or more root directories. Powers `roots`.                      |
| `SelfReferencePlugin`                    | Resolves a package self-reference (e.g. `my-pkg/foo` from within `my-pkg`).                                                          |
| `SymlinkPlugin`                          | Real paths the resolved file by following symlinks. Can be disabled via the `symlinks` option.                                       |
| `TryNextPlugin`                          | Forwards the request to the next hook with a log message. Useful for trying alternative resolutions.                                 |
| `TsconfigPathsPlugin`                    | Rewrites requests using the `paths` and `baseUrl` from a `tsconfig.json`. Powers the `tsconfig` option.                              |
| `UnsafeCachePlugin`                      | Caches successful resolves in an in-memory map to speed up repeated requests. Powers `unsafeCache`.                                  |
| `UseFilePlugin`                          | Joins a fixed filename onto the current path (e.g. `index`). Powers `mainFiles`.                                                     |

#### Plugin wiring and goals

One-line goal and default wiring (`source → target`) for each plugin. `*` means the plugin is tapped on several hooks — the common ones are listed. Plugins without a fixed wiring are user-tapped.

- **`AliasPlugin`** — Goal: redirect requests matching a configured key to an alternative target. `raw-resolve` → `internal-resolve` for `alias`; `file` → `internal-resolve` as a last-chance remap; `described-resolve` → `internal-resolve` for `fallback`.
- **`AliasFieldPlugin`** — Goal: apply aliases declared in a description-file field like `browser`, so environment-specific substitutions happen without user config. `raw-resolve` / `file` → `internal-resolve`.
- **`AppendPlugin`** — Goal: try appending a fixed string (usually an extension) to the current path. `raw-file` → `file`, one instance per entry in `extensions`.
- **`CloneBasenamePlugin`** — Goal: join the directory's basename onto the path (e.g. `/foo/bar` → `/foo/bar/bar`) for directory-named-main layouts. User-wired via `plugins`.
- **`ConditionalPlugin`** — Goal: gate a forward on a partial match of the request shape (e.g. `{ module: true }`), used to fan-out at branching hooks. Tapped on `after-normal-resolve`, `resolve-as-module`, `described-relative`, and `raw-file`.
- **`DescriptionFilePlugin`** — Goal: locate and attach the nearest description file (usually `package.json`) so downstream plugins can read its fields. `parsed-resolve` → `described-resolve`, `relative` → `described-relative`, `undescribed-resolve-in-package` → `resolve-in-package`, `undescribed-existing-directory` → `existing-directory`, `undescribed-raw-file` → `raw-file`.
- **`DirectoryExistsPlugin`** — Goal: only continue the pipeline if the current path exists as a directory. `resolve-as-module` → `undescribed-resolve-in-package`, `directory` → `undescribed-existing-directory`.
- **`ExportsFieldPlugin`** — Goal: map a request through the `exports` field of a package's description file (with `conditionNames`). `resolve-in-package` → `relative`.
- **`ExtensionAliasPlugin`** — Goal: rewrite a request's extension to a list of candidate extensions (e.g. `.js` → `.ts`, `.js`) for TypeScript ESM and similar. `raw-resolve` → `normal-resolve` for direct requests; also `imports-field-relative` → `relative` so extension substitution applies to `imports`-field targets.
- **`FileExistsPlugin`** — Goal: confirm a candidate path exists as a file and record it as a file dependency. `final-file` → `existing-file`.
- **`ImportsFieldPlugin`** — Goal: resolve `#name` requests through the `imports` field of the enclosing package. `internal` → `imports-field-relative` (relative target) or `imports-resolve` (bare target).
- **`JoinRequestPlugin`** — Goal: join the current path with the current request into a single concrete path. `after-normal-resolve` → `relative` (three stage-offset copies for `preferRelative`, `preferAbsolute`, and default), `resolve-in-existing-directory` → `relative`.
- **`JoinRequestPartPlugin`** — Goal: split a module request into module name + inner request, joining the inner part onto the path. `module` → `resolve-as-module`.
- **`LogInfoPlugin`** — Goal: emit verbose log output at a chosen hook; enable by passing a `log` function on `resolveContext`. User-wired via `plugins`.
- **`MainFieldPlugin`** — Goal: follow a description-file field (e.g. `main`, `module`, `browser`) to the entry file of a package. `existing-directory` → `resolve-in-existing-directory`, one instance per entry in `mainFields`.
- **`ModulesInHierarchicalDirectoriesPlugin`** — Goal: search for a module by walking up parent directories (the standard `node_modules` lookup). `raw-module` → `module`; when PnP is enabled, `alternate-raw-module` → `module` too.
- **`ModulesInRootPlugin`** — Goal: search for a module in a single absolute directory (powers absolute-path entries in `modules`). `raw-module` → `module`.
- **`NextPlugin`** — Goal: glue — forward the current request unchanged from one hook to another. Used across the pipeline wherever two hooks should run sequentially.
- **`ParsePlugin`** — Goal: split the raw request string into path / query / fragment / `module` / `directory` / `internal` flags. `resolve` → `parsed-resolve`; also wired on `internal-resolve` and `imports-resolve`.
- **`PnpPlugin`** — Goal: resolve bare-module requests through Yarn's PnP API when available. `raw-module` → `undescribed-resolve-in-package` on hit, `alternate-raw-module` on miss.
- **`RestrictionsPlugin`** — Goal: reject resolved paths that don't satisfy at least one string prefix or RegExp. Tapped on `resolved`.
- **`ResultPlugin`** — Goal: terminal plugin — fires the `result` lifecycle hook and signals a successful resolve. Tapped on `resolved`.
- **`RootsPlugin`** — Goal: resolve server-relative URL requests (starting with `/`) against one or more root directories. `after-normal-resolve` → `relative`.
- **`SelfReferencePlugin`** — Goal: resolve a package self-reference (`my-pkg/foo` from inside `my-pkg`) via its own `exports`. `raw-module` → `resolve-as-module`.
- **`SymlinkPlugin`** — Goal: real-path the resolved file by following symlinks; can be disabled via `symlinks: false`. `existing-file` → `existing-file` (runs via a stage offset on the same hook).
- **`TryNextPlugin`** — Goal: forward the request to another hook with a log message, useful for trying an alternative candidate. `raw-file` → `file` (as the "no extension" attempt) and user-wired.
- **`TsconfigPathsPlugin`** — Goal: rewrite requests using the `paths` and `baseUrl` from a `tsconfig.json` (including project references). Taps `described-resolve` internally and forwards to `internal-resolve`; exported for direct use as well.
- **`UnsafeCachePlugin`** — Goal: cache successful resolves in an in-memory map for repeated requests. `described-resolve` → `raw-resolve` (only when `unsafeCache` is enabled).
- **`UseFilePlugin`** — Goal: join a fixed filename (e.g. `index`) onto the current path to try as an entry file. `existing-directory` / `undescribed-existing-directory` → `undescribed-raw-file`, one instance per entry in `mainFiles`.

### Hooks

A resolver exposes two kinds of [`tapable`](https://github.com/webpack/tapable) hooks:

- **Lifecycle hooks** on `resolver.hooks` — fired by the resolver itself around each `resolve` call. Use these to observe, not to transform the request.
- **Pipeline hooks** — the named steps that plugins tap as `source` and forward to as `target`. Every pipeline hook is an `AsyncSeriesBailHook<[request, resolveContext], request | null>`: return `callback()` to pass on, `callback(err)` to fail, or `callback(null, request)` to short-circuit with a result. Obtain them with `resolver.ensureHook(name)` (creates if missing) or `resolver.getHook(name)` (throws if missing); names are kebab-case or camelCase and are interchangeable.

#### Lifecycle hooks

| Hook          | Type                  | Fires when                                                                                                                           |
| ------------- | --------------------- | ------------------------------------------------------------------------------------------------------------------------------------ |
| `resolveStep` | `SyncHook`            | Every time the resolver hands a request to a pipeline hook. Arguments: `(hook, request)`. Ideal for tracing.                         |
| `noResolve`   | `SyncHook`            | When a top-level `resolve()` call can't produce a result. Arguments: `(request, error)`.                                             |
| `resolve`     | `AsyncSeriesBailHook` | Entry point of the pipeline (also listed below). Tap this to intercept requests before parsing.                                      |
| `result`      | `AsyncSeriesHook`     | After a successful resolve, with the final request. Fired by `ResultPlugin`. Tap to observe/record results without short-circuiting. |

#### Pipeline hooks

Listed roughly in the order the default pipeline visits them. Full wiring lives in `lib/ResolverFactory.js` under `//// pipeline ////`.

| Hook                             | Role                                                                                                                                                                                      |
| -------------------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `resolve`                        | Entry point. `ParsePlugin` parses the raw request (path, query, fragment, module flag) and forwards to `parsed-resolve`.                                                                  |
| `internal-resolve`               | Re-entry point used by internal rewrites (e.g. after an `alias` fires). Same role as `resolve`, but `fullySpecified` is forced off.                                                       |
| `imports-resolve`                | Re-entry point for the target of an `imports` field match; prevents recursive `#` resolution per the Node.js ESM spec.                                                                    |
| `parsed-resolve`                 | Request has been parsed. `DescriptionFilePlugin` attaches the nearest `package.json`, then forwards to `described-resolve`.                                                               |
| `described-resolve`              | Description file is attached. Where `unsafeCache`, `fallback`, and most user plugins (including `MyLibSrcPlugin` below) hook in.                                                          |
| `raw-resolve`                    | After description. Where `alias`, `aliasFields`, `tsconfig` paths, and `extensionAlias` rewrites fire before default resolution.                                                          |
| `normal-resolve`                 | Default resolution starts. Branches into `relative` (for `./`, `../`, absolute), `raw-module` (bare modules), or `internal` (`#imports`).                                                 |
| `internal`                       | `#name` imports-field entry. `ImportsFieldPlugin` maps the specifier and forwards to `imports-field-relative` or `imports-resolve`.                                                       |
| `imports-field-relative`         | Concrete path from an `imports`-field match, before the normal `relative` flow. `ExtensionAliasPlugin` taps here so `.js` → `.ts` also fires for `#name` targets. Forwards to `relative`. |
| `raw-module`                     | Bare-module lookup. `SelfReferencePlugin`, `ModulesInHierarchicalDirectoriesPlugin`, `ModulesInRootPlugin`, and `PnpPlugin` all tap here.                                                 |
| `alternate-raw-module`           | Fallback module lookup used by `PnpPlugin` when PnP can't resolve and `node_modules` should be tried.                                                                                     |
| `module`                         | A candidate module directory was built. `JoinRequestPartPlugin` splits off the inner request and forwards to `resolve-as-module`.                                                         |
| `resolve-as-module`              | Treat candidate as a package. `DirectoryExistsPlugin` gates on existence; short single-file modules may re-enter via `undescribed-raw-file`.                                              |
| `undescribed-resolve-in-package` | Inside a located package directory, before its `package.json` has been read. Loads the description, forwards to `resolve-in-package`.                                                     |
| `resolve-in-package`             | Inside a package with its description loaded. `ExportsFieldPlugin` matches `exports`, otherwise forwards to `resolve-in-existing-directory`.                                              |
| `resolve-in-existing-directory`  | Package directory confirmed; join the remaining request onto it and continue at `relative`.                                                                                               |
| `relative`                       | A concrete path on disk. `DescriptionFilePlugin` loads the nearest `package.json` and forwards to `described-relative`.                                                                   |
| `described-relative`             | Branches to `raw-file` (treat as file) and `directory` (treat as directory). `resolveToContext` skips the file branch.                                                                    |
| `directory`                      | Candidate directory. `DirectoryExistsPlugin` gates on existence and forwards to `undescribed-existing-directory`.                                                                         |
| `undescribed-existing-directory` | Existing directory, before its `package.json` has been read. `UseFilePlugin` tries `mainFiles` via `undescribed-raw-file`.                                                                |
| `existing-directory`             | Existing directory with description loaded. `MainFieldPlugin` tries `mainFields`; `UseFilePlugin` falls back to `mainFiles`.                                                              |
| `undescribed-raw-file`           | Candidate file path, before description is read. Loads description, then forwards to `raw-file`.                                                                                          |
| `raw-file`                       | Apply extension handling: `ConditionalPlugin` short-circuits when `fullySpecified`, `TryNextPlugin` + `AppendPlugin` try each extension.                                                  |
| `file`                           | A specific file path. Last place `alias` and `aliasFields` can redirect; forwards to `final-file`.                                                                                        |
| `final-file`                     | `FileExistsPlugin` checks the file is real and records it as a file dependency, then forwards to `existing-file`.                                                                         |
| `existing-file`                  | Real file on disk. `SymlinkPlugin` real-paths symlinks (unless `symlinks: false`), then forwards to `resolved`.                                                                           |
| `resolved`                       | Terminal hook. `RestrictionsPlugin` enforces `restrictions`; `ResultPlugin` fires the `result` lifecycle hook.                                                                            |

#### `before-` and `after-` prefixes

`ensureHook("before-foo")` and `getHook("before-foo")` return the `foo` hook with `stage: -10`; `after-foo` returns it with `stage: 10`. Use this to tap earlier or later than the default stage without creating a separate hook. You'll see `after-parsed-resolve`, `after-normal-resolve`, `after-relative`, and `after-undescribed-resolve-in-package` used this way inside `ResolverFactory`.

#### Request flow by request type

The same 26 pipeline hooks serve every request, but different request shapes take different paths through them. Each `➝` below is one `doResolve` / `NextPlugin` / plugin forward; `resolveStep` fires on every arrow, so tapping it (see [Hook examples](#hook-examples)) prints these chains live.

Relative path (`./utils` from `/src/index.js`) — the default "resolve on disk" path:

```text
resolve                                    (ParsePlugin)
  ➝ parsed-resolve                         (DescriptionFilePlugin attaches nearest package.json)
  ➝ described-resolve                      (NextPlugin; or UnsafeCachePlugin short-circuit)
  ➝ raw-resolve                            (NextPlugin; alias/tsconfig would branch here)
  ➝ normal-resolve                         (JoinRequestPlugin: path=/src/utils, request="")
  ➝ relative                               (DescriptionFilePlugin loads /src/package.json)
  ➝ described-relative                     (branches to file and directory candidates)
        ├─ ➝ raw-file                      (ConditionalPlugin / TryNextPlugin)
        │     ➝ file                       (AppendPlugin tried each extension, e.g. .js)
        │     ➝ final-file                 (FileExistsPlugin confirms the file)
        │     ➝ existing-file              (SymlinkPlugin real-paths it)
        │     ➝ resolved                   (RestrictionsPlugin → ResultPlugin)
        └─ ➝ directory                     (DirectoryExistsPlugin; used when path is a dir)
              ➝ undescribed-existing-directory
              ➝ existing-directory         (MainFieldPlugin tries "main", UseFilePlugin tries "index")
              ➝ undescribed-raw-file ➝ raw-file ➝ …
```

Bare module (`lodash/merge`) — walks up `node_modules`, then treats the hit as a package:

```text
resolve ➝ parsed-resolve ➝ described-resolve ➝ raw-resolve ➝ normal-resolve
  ➝ raw-module                             (ConditionalPlugin {module:true})
  ➝ module                                 (ModulesInHierarchicalDirectoriesPlugin walks
                                            /src/node_modules, /node_modules, …)
  ➝ resolve-as-module                      (JoinRequestPartPlugin splits "lodash" / "./merge")
  ➝ undescribed-resolve-in-package         (DirectoryExistsPlugin gates on lodash/ existing)
  ➝ resolve-in-package                     (DescriptionFilePlugin loads lodash/package.json)
        ├─ ➝ relative                      (ExportsFieldPlugin, if "exports" matches)
        └─ ➝ resolve-in-existing-directory (otherwise; JoinRequestPlugin joins "./merge")
              ➝ relative ➝ … (same tail as the relative flow above)
```

Internal import (`#util` from inside a package) — re-enters the pipeline after mapping:

```text
resolve ➝ parsed-resolve ➝ described-resolve ➝ raw-resolve ➝ normal-resolve
  ➝ internal                               (ConditionalPlugin {internal:true})
  ➝ imports-resolve                        (ImportsFieldPlugin mapped "#util" to a bare target)
  ➝ parsed-resolve ➝ …                     (fresh pipeline run, internal:false so # isn't remapped)
```

When the `imports` field maps to a relative target, the branch instead goes:

```text
  ➝ internal
  ➝ imports-field-relative                 (ImportsFieldPlugin mapped "#util" to "./util.js";
                                            ExtensionAliasPlugin can swap .js → .ts here)
  ➝ relative ➝ … (same tail as the relative flow above)
```

Alias hit (`@/button` with `alias: { "@": "/src" }`) — rewrites then restarts:

```text
resolve ➝ parsed-resolve ➝ described-resolve
  ➝ raw-resolve
  ➝ internal-resolve                       (AliasPlugin rewrote request → "/src/button")
  ➝ parsed-resolve ➝ … (fullySpecified forced off; AliasPlugin won't re-fire for the rewritten form)
```

`exports`-field hit inside a package (`pkg/feature` matching `"./feature"` in `exports`):

```text
… ➝ raw-module ➝ module ➝ resolve-as-module ➝ undescribed-resolve-in-package
  ➝ resolve-in-package
  ➝ relative                               (ExportsFieldPlugin jumped to the exports target;
                                            main-field / main-file logic is skipped)
  ➝ described-relative ➝ raw-file ➝ file ➝ final-file ➝ existing-file ➝ resolved
```

Failure — every candidate opts out (`callback()`) and no handler ever short-circuits with a result. `noResolve` fires once, for the top-level request:

```text
… ➝ final-file
       ✗ FileExistsPlugin: ENOENT  (opts out; no extension candidates left)
  ⇠ bail hooks unwind, each tapped handler has already tried its alternatives
  ⇒ top-level resolve() returns no result
  ⇒ resolver.hooks.noResolve(request, error)    (ResultPlugin never fires)
```

#### Hook examples

Trace every pipeline step and observe failures via the lifecycle hooks:

```js
resolver.hooks.resolveStep.tap("Trace", (hook, request) => {
	console.log(`[step] ${hook.name}: ${request.request} @ ${request.path}`);
});
resolver.hooks.noResolve.tap("Trace", (request, error) => {
	console.log(`[fail] ${request.request}: ${error.message}`);
});
resolver.hooks.result.tapAsync("Trace", (request, _ctx, callback) => {
	console.log(`[done] ${request.path}`);
	callback();
});
```

Short-circuit at `file` to redirect any `.css` request to a stub without continuing the pipeline:

```js
class StubCssPlugin {
	apply(resolver) {
		resolver
			.getHook("file")
			.tapAsync("StubCssPlugin", (request, _ctx, callback) => {
				if (!request.path || !request.path.endsWith(".css")) return callback();
				callback(null, { ...request, path: require.resolve("./empty.css") });
			});
	}
}
```

Forward to a different hook with `doResolve` to restart resolution with a rewritten request — see `MyLibSrcPlugin` in [Writing a Custom Plugin](#writing-a-custom-plugin) for the canonical pattern (`getHook("described-resolve")` → `doResolve(ensureHook("resolve"), …)`).

### Writing a Custom Plugin

The example below adds a plugin that rewrites any request starting with `my-lib/` to `my-lib/src/`. It taps the `described-resolve` hook (after the description file has been located) and forwards the rewritten request to `resolve`, so the pipeline restarts with the new request.

```js
const fs = require("fs");
const { CachedInputFileSystem, ResolverFactory } = require("enhanced-resolve");

class MyLibSrcPlugin {
	apply(resolver) {
		const target = resolver.ensureHook("resolve");
		resolver
			.getHook("described-resolve")
			.tapAsync("MyLibSrcPlugin", (request, resolveContext, callback) => {
				if (!request.request || !request.request.startsWith("my-lib/")) {
					return callback();
				}
				const newRequest = {
					...request,
					request: request.request.replace(/^my-lib\//, "my-lib/src/"),
				};
				resolver.doResolve(
					target,
					newRequest,
					"rewrote my-lib → my-lib/src",
					resolveContext,
					callback,
				);
			});
	}
}

const myResolver = ResolverFactory.createResolver({
	fileSystem: new CachedInputFileSystem(fs, 4000),
	extensions: [".js", ".json"],
	plugins: [new MyLibSrcPlugin()],
});
```

Tips for writing your own plugin:

- Call `callback()` with no arguments to pass the request on to the next tapped handler at the same `source` hook. This is how you "opt out" when a request doesn't apply.
- Call `resolver.doResolve(target, newRequest, message, resolveContext, callback)` to continue the pipeline at a different hook with a (possibly modified) request.
- Return early with `callback(null, result)` to short-circuit with a specific result, or `callback(err)` to fail the resolve.
- See [Hooks](#hooks) for the full list of pipeline hooks, their order, and the `before-` / `after-` stage modifiers. `lib/ResolverFactory.js` has the exact wiring under `//// pipeline ////`.

## Escaping

It's allowed to escape `#` as `\0#` to avoid parsing it as fragment.

enhanced-resolve will try to resolve requests containing `#` as path and as fragment, so it will automatically figure out if `./some#thing` means `.../some.js#thing` or `.../some#thing.js`. When a `#` is resolved as path it will be escaped in the result. Here: `.../some\0#thing.js`.

## Tests

```sh
npm run test
```

## Passing options from webpack

If you are using `webpack`, and you want to pass custom options to `enhanced-resolve`, the options are passed from the `resolve` key of your webpack configuration e.g.:

```
resolve: {
  extensions: ['.js', '.jsx'],
  modules: [path.resolve(__dirname, 'src'), 'node_modules'],
  plugins: [new DirectoryNamedWebpackPlugin()]
  ...
},
```

## License

Copyright (c) 2012-2019 JS Foundation and other contributors

MIT (http://www.opensource.org/licenses/mit-license.php)

[npm]: https://img.shields.io/npm/v/enhanced-resolve.svg
[npm-url]: https://www.npmjs.com/package/enhanced-resolve
[build-status]: https://github.com/webpack/enhanced-resolve/actions/workflows/test.yml/badge.svg
[build-status-url]: https://github.com/webpack/enhanced-resolve/actions
[codecov-badge]: https://codecov.io/gh/webpack/enhanced-resolve/branch/main/graph/badge.svg?token=6B6NxtsZc3
[codecov-url]: https://codecov.io/gh/webpack/enhanced-resolve
[size]: https://packagephobia.com/badge?p=enhanced-resolve
[size-url]: https://packagephobia.com/result?p=enhanced-resolve
[discussion]: https://img.shields.io/github/discussions/webpack/webpack
[discussion-url]: https://github.com/webpack/webpack/discussions
