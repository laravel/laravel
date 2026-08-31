# Tapable

The tapable package exposes many Hook classes, which can be used to create hooks for plugins.

```javascript
const {
	AsyncParallelBailHook,
	AsyncParallelHook,
	AsyncSeriesBailHook,
	AsyncSeriesHook,
	AsyncSeriesWaterfallHook,
	SyncBailHook,
	SyncHook,
	SyncLoopHook,
	SyncWaterfallHook
} = require("tapable");
```

## Installation

```shell
npm install --save tapable
```

## Usage

All Hook constructors take one optional argument, which is a list of argument names as strings.

```js
const hook = new SyncHook(["arg1", "arg2", "arg3"]);
```

The best practice is to expose all hooks of a class in a `hooks` property:

```js
class Car {
	constructor() {
		this.hooks = {
			accelerate: new SyncHook(["newSpeed"]),
			brake: new SyncHook(),
			calculateRoutes: new AsyncParallelHook(["source", "target", "routesList"])
		};
	}

	/* ... */
}
```

Other people can now use these hooks:

```js
const myCar = new Car();

// Use the tap method to add a consumer (plugin)
myCar.hooks.brake.tap("WarningLampPlugin", () => warningLamp.on());
```

It's required to pass a name to identify the plugin/reason.

You may receive arguments:

```js
myCar.hooks.accelerate.tap("LoggerPlugin", (newSpeed) =>
	console.log(`Accelerating to ${newSpeed}`)
);
```

For sync hooks, `tap` is the only valid method to add a plugin. Async hooks also support async plugins:

```js
myCar.hooks.calculateRoutes.tapPromise(
	"GoogleMapsPlugin",
	(source, target, routesList) =>
		// return a promise
		google.maps.findRoute(source, target).then((route) => {
			routesList.add(route);
		})
);
myCar.hooks.calculateRoutes.tapAsync(
	"BingMapsPlugin",
	(source, target, routesList, callback) => {
		bing.findRoute(source, target, (err, route) => {
			if (err) return callback(err);
			routesList.add(route);
			// call the callback
			callback();
		});
	}
);

// You can still use sync plugins
myCar.hooks.calculateRoutes.tap(
	"CachedRoutesPlugin",
	(source, target, routesList) => {
		const cachedRoute = cache.get(source, target);
		if (cachedRoute) routesList.add(cachedRoute);
	}
);
```

The class declaring these hooks needs to call them:

```js
class Car {
	/**
	 * You won't get returned value from SyncHook or AsyncParallelHook,
	 * to do that, use SyncWaterfallHook and AsyncSeriesWaterfallHook respectively
	 */

	setSpeed(newSpeed) {
		// following call returns undefined even when you returned values
		this.hooks.accelerate.call(newSpeed);
	}

	useNavigationSystemPromise(source, target) {
		const routesList = new List();
		return this.hooks.calculateRoutes
			.promise(source, target, routesList)
			.then((res) =>
				// res is undefined for AsyncParallelHook
				routesList.getRoutes()
			);
	}

	useNavigationSystemAsync(source, target, callback) {
		const routesList = new List();
		this.hooks.calculateRoutes.callAsync(source, target, routesList, (err) => {
			if (err) return callback(err);
			callback(null, routesList.getRoutes());
		});
	}
}
```

The Hook will compile a method with the most efficient way of running your plugins. It generates code depending on:

- The number of registered plugins (none, one, many)
- The kind of registered plugins (sync, async, promise)
- The used call method (sync, async, promise)
- The number of arguments
- Whether interception is used

This ensures fastest possible execution. See [Code generation](#code-generation) for more details on the runtime compilation.

## Plugin API

A plugin registers a callback on a hook using one of the `tap*` methods. The hook type determines which of these are valid (see [Hook classes](#hook-classes)):

- `hook.tap(nameOrOptions, fn)` — register a synchronous callback.
- `hook.tapAsync(nameOrOptions, fn)` — register a callback-based async callback. The last argument passed to `fn` is a node-style callback `(err, result)`.
- `hook.tapPromise(nameOrOptions, fn)` — register a promise-returning async callback. If `fn` returns something that is not thenable, the hook throws.

The first argument can be either a string (the plugin name) or an options object that also allows influencing the order in which taps run:

```js
hook.tap(
	{
		name: "MyPlugin",
		stage: -10, // lower stages run earlier, default is 0
		before: "OtherPlugin" // run before a named tap (string or string[])
	},
	(...args) => {
		/* ... */
	}
);
```

| Option   | Type                   | Description                                                                                                                                                                    |
| -------- | ---------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| `name`   | `string`               | Required. Identifies the tap for debugging, interceptors, and the `before` option.                                                                                             |
| `stage`  | `number`               | Defaults to `0`. Taps with a lower stage run before taps with a higher stage. Taps with the same stage run in registration order.                                              |
| `before` | `string` \| `string[]` | The tap is inserted before the named tap(s). Unknown names are ignored. Combined with `stage`, `before` wins for the taps it targets; other taps are still ordered by `stage`. |

The `name` is also used by some ecosystems (like webpack) for profiling and error messages. Within a single tap registration, later interceptors' `register` hooks may still replace the tap object (see [Interception](#interception)).

### `hook.withOptions(options)`

`withOptions` returns a facade around the hook whose `tap*` methods automatically merge `options` into every registration. It is useful for libraries that want to pre-configure a `stage` or `before` for all the taps they add:

```js
const lateHook = myCar.hooks.accelerate.withOptions({ stage: 10 });
lateHook.tap("LogAfterOthers", (speed) => console.log("final speed", speed));
// equivalent to: myCar.hooks.accelerate.tap({ name: "LogAfterOthers", stage: 10 }, ...)
```

The returned object does not expose the `call*` methods, so it is safe to hand out to plugins.

A runnable example showing how `withOptions` influences tap ordering:

```js
const { SyncHook } = require("tapable");

const hook = new SyncHook(["value"]);

hook.tap("Default", (v) => console.log("default", v));

// Pre-configure stage: 10 so all taps registered through `late` run last.
const late = hook.withOptions({ stage: 10 });
late.tap("RunLast", (v) => console.log("last", v));

// Pre-configure stage: -10 so these taps run first. Each facade can also
// be further narrowed via `withOptions`.
const early = hook.withOptions({ stage: -10 });
early.tap("RunFirst", (v) => console.log("first", v));

hook.call(1);
// first 1
// default 1
// last 1
```

Per-tap options override values from `withOptions`. For example, `late.tap({ name: "Override", stage: 0 }, fn)` ignores the facade's `stage: 10` and registers `fn` at stage `0`.

## Hook types

Each hook can be tapped with one or several functions. How they are executed depends on the hook type:

- Basic hook (without “Waterfall”, “Bail” or “Loop” in its name). This hook simply calls every function it tapped in a row.

- **Waterfall**. A waterfall hook also calls each tapped function in a row. Unlike the basic hook, it passes a return value from each function to the next function.

- **Bail**. A bail hook allows exiting early. When any of the tapped function returns anything, the bail hook will stop executing the remaining ones.

- **Loop**. When a plugin in a loop hook returns a non-undefined value the hook will restart from the first plugin. It will loop until all plugins return undefined.

Additionally, hooks can be synchronous or asynchronous. To reflect this, there’re “Sync”, “AsyncSeries”, and “AsyncParallel” hook classes:

- **Sync**. A sync hook can only be tapped with synchronous functions (using `myHook.tap()`).

- **AsyncSeries**. An async-series hook can be tapped with synchronous, callback-based and promise-based functions (using `myHook.tap()`, `myHook.tapAsync()` and `myHook.tapPromise()`). They call each async method in a row.

- **AsyncParallel**. An async-parallel hook can also be tapped with synchronous, callback-based and promise-based functions (using `myHook.tap()`, `myHook.tapAsync()` and `myHook.tapPromise()`). However, they run each async method in parallel.

The hook type is reflected in its class name. E.g., `AsyncSeriesWaterfallHook` allows asynchronous functions and runs them in series, passing each function’s return value into the next function.

## Hook classes

The table below summarizes the 9 built-in hook classes. For each class:

- **Tap methods** are the `tapX` variants that may be used to register a handler.
- **Call methods** are the ways the owner of the hook can trigger it.
- **Result** is the value returned from `call` (or passed to the `callAsync` callback / resolved from the `promise` call).
- **Returned value from tap** describes whether the value returned from a tapped function has an effect.

| Class                      | Tap methods                     | Call methods           | Result                                          | Returned value from tap                              |
| -------------------------- | ------------------------------- | ---------------------- | ----------------------------------------------- | ---------------------------------------------------- |
| `SyncHook`                 | `tap`                           | `call`                 | `undefined`                                     | ignored                                              |
| `SyncBailHook`             | `tap`                           | `call`                 | first non-`undefined` value, or `undefined`     | short-circuits the hook                              |
| `SyncWaterfallHook`        | `tap`                           | `call`                 | final value (first argument after the last tap) | passed as first argument to the next tap             |
| `SyncLoopHook`             | `tap`                           | `call`                 | `undefined`                                     | non-`undefined` restarts the loop from the first tap |
| `AsyncParallelHook`        | `tap`, `tapAsync`, `tapPromise` | `callAsync`, `promise` | `undefined`                                     | ignored                                              |
| `AsyncParallelBailHook`    | `tap`, `tapAsync`, `tapPromise` | `callAsync`, `promise` | first non-`undefined` value, or `undefined`     | short-circuits the hook                              |
| `AsyncSeriesHook`          | `tap`, `tapAsync`, `tapPromise` | `callAsync`, `promise` | `undefined`                                     | ignored                                              |
| `AsyncSeriesBailHook`      | `tap`, `tapAsync`, `tapPromise` | `callAsync`, `promise` | first non-`undefined` value, or `undefined`     | short-circuits the hook                              |
| `AsyncSeriesLoopHook`      | `tap`, `tapAsync`, `tapPromise` | `callAsync`, `promise` | `undefined`                                     | non-`undefined` restarts the loop from the first tap |
| `AsyncSeriesWaterfallHook` | `tap`, `tapAsync`, `tapPromise` | `callAsync`, `promise` | final value (first argument after the last tap) | passed as first argument to the next tap             |

Detailed behavior of each class:

### SyncHook

A basic synchronous hook. Every tapped function is called in registration order with the arguments passed to `call`. Return values from tapped functions are ignored and `call` returns `undefined`.

- Tap methods: `tap`
- Call methods: `call`
- `tapAsync` and `tapPromise` throw an error.

```js
const hook = new SyncHook(["name"]);
hook.tap("A", (name) => console.log(`hello ${name}`));
hook.tap("B", (name) => console.log(`hi ${name}`));
hook.call("world");
// hello world
// hi world
```

### SyncBailHook

A synchronous hook that allows exiting early. Every tapped function is called in order until one returns a non-`undefined` value; that value becomes the result of `call` and the remaining taps are skipped. If all taps return `undefined`, `call` returns `undefined`.

- Tap methods: `tap`
- Call methods: `call`

```js
const hook = new SyncBailHook(["value"]);
hook.tap("Negative", (v) => (v < 0 ? "negative" : undefined));
hook.tap("Zero", (v) => (v === 0 ? "zero" : undefined));
hook.tap("Positive", (v) => "positive");

hook.call(-1); // "negative" (later taps skipped)
hook.call(5); // "positive"
```

### SyncWaterfallHook

A synchronous hook that threads a value through its tapped functions. The first argument passed to `call` is forwarded to the first tap. If a tap returns a non-`undefined` value it replaces that argument for the next tap; otherwise the previous value is kept. `call` returns the value after the last tap has run. Additional arguments (if any) are passed through unchanged.

- Tap methods: `tap`
- Call methods: `call`

```js
const hook = new SyncWaterfallHook(["value"]);
hook.tap("Double", (v) => v * 2);
hook.tap("PlusOne", (v) => v + 1);

hook.call(3); // 7  -> (3 * 2) + 1
```

### SyncLoopHook

A synchronous hook that keeps re-running its taps until all of them return `undefined` for a full pass. Whenever a tap returns a non-`undefined` value the hook restarts from the first tap. `call` returns `undefined`.

- Tap methods: `tap`
- Call methods: `call`

```js
const hook = new SyncLoopHook(["state"]);
let retries = 3;
hook.tap("Retry", () => {
	if (retries-- > 0) return true; // non-undefined restarts the loop
});
hook.tap("Log", () => console.log("pass"));

hook.call({});
// pass (runs once all taps return undefined)
```

### AsyncParallelHook

An asynchronous hook that runs all of its tapped functions in parallel. It completes when every tap has signalled completion (sync return, callback, or promise resolution). Return values and resolution values are ignored; `callAsync`'s callback is invoked with no result and `promise()` resolves to `undefined`. If any tap errors, the error is forwarded and remaining taps still complete but their results are discarded.

- Tap methods: `tap`, `tapAsync`, `tapPromise`
- Call methods: `callAsync`, `promise`

```js
const hook = new AsyncParallelHook(["source"]);
hook.tapPromise("Fetch", (src) => fetch(src));
hook.tapAsync("Log", (src, cb) => {
	console.log("fetching", src);
	cb();
});

await hook.promise("https://example.com");
```

### AsyncParallelBailHook

Like `AsyncParallelHook`, but designed to bail out with a result. All tapped functions start in parallel; the first tap to produce a non-`undefined` value (synchronously, via its callback, or by resolving its promise) determines the hook’s result. The remaining taps continue to run but their results are ignored. Order is determined by tap registration order: an earlier tap’s value takes precedence over a later one’s, even if the later one finishes first.

- Tap methods: `tap`, `tapAsync`, `tapPromise`
- Call methods: `callAsync`, `promise`

```js
const hook = new AsyncParallelBailHook(["key"]);
hook.tapPromise("Cache", async (key) => cache.get(key));
hook.tapPromise("Db", async (key) => db.lookup(key));

const value = await hook.promise("user:42");
// First non-undefined result (by registration order) wins.
```

### AsyncSeriesHook

An asynchronous hook that runs tapped functions one after another, waiting for each to finish before starting the next. Results are ignored; `callAsync`'s callback is invoked with no result and `promise()` resolves to `undefined`. The first error aborts the series.

- Tap methods: `tap`, `tapAsync`, `tapPromise`
- Call methods: `callAsync`, `promise`

```js
const hook = new AsyncSeriesHook(["request"]);
hook.tapPromise("Authenticate", async (req) => authenticate(req));
hook.tapPromise("Log", async (req) => logger.info(req.url));

await hook.promise(request);
```

### AsyncSeriesBailHook

An asynchronous series hook that allows exiting early. Tapped functions run one after another; as soon as one produces a non-`undefined` value, that value becomes the hook’s result and the remaining taps are skipped.

- Tap methods: `tap`, `tapAsync`, `tapPromise`
- Call methods: `callAsync`, `promise`

```js
const hook = new AsyncSeriesBailHook(["id"]);
hook.tapPromise("Memory", async (id) => memory.get(id));
hook.tapPromise("Disk", async (id) => disk.read(id));

const value = await hook.promise("doc-1");
// Stops at the first tap that produces a value.
```

### AsyncSeriesLoopHook

An asynchronous series hook that loops. Tapped functions run one after another; whenever a tap produces a non-`undefined` value the hook restarts from the first tap. The hook completes once a full pass yields `undefined` from every tap. The result is always `undefined`.

- Tap methods: `tap`, `tapAsync`, `tapPromise`
- Call methods: `callAsync`, `promise`

```js
const hook = new AsyncSeriesLoopHook(["job"]);
hook.tapPromise("Process", async (job) => {
	const more = await job.step();
	if (more) return true; // restart the loop
});

await hook.promise(job);
```

### AsyncSeriesWaterfallHook

An asynchronous series hook that threads a value through its taps. The first argument passed to `callAsync` / `promise` is forwarded to the first tap. A tap's non-`undefined` return / callback / resolution value replaces it for the next tap; `undefined` keeps the previous value. The hook completes with the value after the last tap.

- Tap methods: `tap`, `tapAsync`, `tapPromise`
- Call methods: `callAsync`, `promise`

```js
const hook = new AsyncSeriesWaterfallHook(["source"]);
hook.tapPromise("Read", async (src) => fs.readFile(src, "utf8"));
hook.tapPromise("Trim", async (text) => text.trim());

const output = await hook.promise("./input.txt");
```

## Interception

All hooks expose an `intercept(interceptor)` method. An interceptor is a plain object whose methods are invoked at specific points during the lifetime of the hook. Interceptors are invoked in registration order before the taps, and are useful for logging, tracing, profiling, or re-mapping tap options.

```js
myCar.hooks.calculateRoutes.intercept({
	name: "LoggingInterceptor",
	call: (source, target, routesList) => {
		console.log("Starting to calculate routes");
	},
	tap: (tapInfo) => {
		// tapInfo = { type: "promise", name: "GoogleMapsPlugin", fn: ..., stage: 0 }
		console.log(`${tapInfo.name} is running`);
	},
	register: (tapInfo) => {
		// Called once per tap (and for each tap already registered when the
		// interceptor is added). Return a new tapInfo object to replace it.
		console.log(`${tapInfo.name} is registered`);

		return tapInfo;
	}
});
```

| Handler    | Signature                        | When it runs                                                                                                                               |
| ---------- | -------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------ |
| `call`     | `(...args) => void`              | Before the hook starts executing its taps. Receives the arguments passed to `call` / `callAsync` / `promise`.                              |
| `tap`      | `(tap: Tap) => void`             | Before each tap runs. The `tap` object is a snapshot — mutations are ignored.                                                              |
| `loop`     | `(...args) => void`              | At the start of each iteration of a `SyncLoopHook` / `AsyncSeriesLoopHook`.                                                                |
| `error`    | `(err: Error) => void`           | Whenever a tap throws, rejects, or calls its callback with an error.                                                                       |
| `result`   | `(result: any) => void`          | When a bail or waterfall hook produces a value, or when a tap produces one for a loop hook.                                                |
| `done`     | `() => void`                     | When the hook finishes successfully (no error, no early bail).                                                                             |
| `register` | `(tap: Tap) => Tap \| undefined` | Once per tap at registration time (including taps that existed before the interceptor was added). Return a new `Tap` object to replace it. |
| `name`     | `string`                         | Optional label used by ecosystems for debugging.                                                                                           |
| `context`  | `boolean`                        | Opt into the shared `context` object. See [Context](#context).                                                                             |

Adding an interceptor invalidates the hook's compiled call function — the next `call` / `callAsync` / `promise` recompiles it so that the new interceptor is woven in.

## Context

Plugins and interceptors can opt-in to access an optional `context` object, which can be used to pass arbitrary values to subsequent plugins and interceptors.

```js
myCar.hooks.accelerate.intercept({
	context: true,
	tap: (context, tapInfo) => {
		// tapInfo = { type: "sync", name: "NoisePlugin", fn: ... }
		console.log(`${tapInfo.name} is doing it's job`);

		// `context` starts as an empty object if at least one plugin uses `context: true`.
		// If no plugins use `context: true`, then `context` is undefined.
		if (context) {
			// Arbitrary properties can be added to `context`, which plugins can then access.
			context.hasMuffler = true;
		}
	}
});

myCar.hooks.accelerate.tap(
	{
		name: "NoisePlugin",
		context: true
	},
	(context, newSpeed) => {
		if (context && context.hasMuffler) {
			console.log("Silence...");
		} else {
			console.log("Vroom!");
		}
	}
);
```

## HookMap

A `HookMap` is a helper class that lazily creates hooks per key. The constructor takes a factory function; the first time a key is requested via `for(key)`, the factory is called and the resulting hook is cached.

```js
const keyedHook = new HookMap((key) => new SyncHook(["arg"]));
```

Plugins use `for(key)` to obtain the hook for a specific key (creating it on demand) and then `tap` on it as usual:

```js
keyedHook.for("some-key").tap("MyPlugin", (arg) => {
	/* ... */
});
keyedHook.for("some-key").tapAsync("MyPlugin", (arg, callback) => {
	/* ... */
});
keyedHook.for("some-key").tapPromise("MyPlugin", (arg) => {
	/* ... */
});
```

The owner of the `HookMap` uses `get(key)` to look up an existing hook without creating one. This is typically preferred on the calling side so that keys no plugin cares about are never materialized:

```js
const hook = keyedHook.get("some-key");
if (hook !== undefined) {
	hook.callAsync("arg", (err) => {
		/* ... */
	});
}
```

A `HookMap` can also be intercepted. `intercept({ factory })` wraps the factory so you can customize or replace the hook returned for each new key.

## Code generation

Tapable does not iterate over taps at call time. Instead, the first time `call`, `callAsync` or `promise` is invoked after the hook has been modified, the hook compiles a specialized function using `new Function(...)` and caches it on the instance. This is what the README means by "evals in code": the hook's dispatch logic is generated as a string and turned into a real JavaScript function the engine can inline and optimize.

The generated function is tailored to:

- **Call type** — whether the owner called `call` (sync), `callAsync` (callback), or `promise`. Each produces a different skeleton — e.g. `promise()` wraps the body in `new Promise((_resolve, _reject) => { ... })`.
- **Tap types** — for each tap, the generator emits the right invocation pattern: direct call for `tap`, node-style callback wrapping for `tapAsync`, and `.then(...)` chaining for `tapPromise`.
- **Hook class** — `SyncHook` emits a straight-line sequence of calls; `SyncBailHook` emits early-return checks; `SyncWaterfallHook` threads a value through calls; loop hooks wrap the body in a re-entry loop; `AsyncParallel*` fans the taps out and counts completions; `AsyncSeries*` chains them.
- **Interceptors** — if interceptors are attached, calls to their `call`/`tap`/`loop`/`error`/`result`/`done` handlers are spliced into the generated body; otherwise they cost nothing.
- **Context** — a `_context` object is only created when at least one tap or interceptor opts into it with `context: true`.
- **Arity** — the generated code hard-codes the number of arguments declared when the hook was constructed, so no `arguments`/rest handling happens at runtime.

The compiled function is invalidated (reset back to a one-shot "recompile then call" trampoline) whenever the hook's shape changes — i.e. on any new `tap*` or `intercept` call. Steady-state calls therefore run straight through the cached function with no per-tap branching.

### Why this matters

- You only pay for features you use. An interceptor-free, sync-only hook compiles down to a short sequence of direct function calls.
- Debugging a hook means reading the generated source. If you need to see it, `Hook.prototype.compile` returns the `new Function(...)` result — log `hook._createCall("sync").toString()` (or `"async"` / `"promise"`) to inspect the body.
- Because the dispatch is code-generated, a hook's behavior is fully determined at compile time. Mutating tap options after registration (for example, changing `stage` on an existing `Tap` object) will not reorder taps until you cause a recompile.

## Hook/HookMap interface

Public (callable by anyone holding a reference to the hook, i.e. the plugins):

```ts
interface Hook {
	tap: (name: string | Tap, fn: (context?, ...args) => Result) => void;
	tapAsync: (
		name: string | Tap,
		fn: (
			context?,
			...args,
			callback: (err: Error | null, result: Result) => void
		) => void
	) => void;
	tapPromise: (
		name: string | Tap,
		fn: (context?, ...args) => Promise<Result>
	) => void;
	intercept: (interceptor: HookInterceptor) => void;
	withOptions: (
		options: TapOptions
	) => Omit<Hook, "call" | "callAsync" | "promise">;
}

interface HookInterceptor {
	name?: string;
	call?: (context?, ...args) => void;
	loop?: (context?, ...args) => void;
	tap?: (context?, tap: Tap) => void;
	error?: (err: Error) => void;
	result?: (result: any) => void;
	done?: () => void;
	register?: (tap: Tap) => Tap | undefined;
	context?: boolean;
}

interface HookMap {
	for: (key: any) => Hook;
	intercept: (interceptor: HookMapInterceptor) => void;
}

interface HookMapInterceptor {
	factory: (key: any, hook: Hook) => Hook;
}

interface Tap {
	name: string;
	type: "sync" | "async" | "promise";
	fn: Function;
	stage: number;
	context: boolean;
	before?: string | Array<string>;
}
```

Protected (only for the class containing the hook — it owns the right to trigger it):

```ts
interface Hook {
	isUsed: () => boolean;
	call: (...args) => Result;
	promise: (...args) => Promise<Result>;
	callAsync: (
		...args,
		callback: (err: Error | null, result: Result) => void
	) => void;
}

interface HookMap {
	get: (key: any) => Hook | undefined;
	for: (key: any) => Hook;
}
```

`isUsed()` returns `true` when the hook has at least one tap or interceptor registered. Hook owners can use it to skip expensive argument preparation when no plugin is listening:

```js
class Car {
	// ...
	setSpeed(newSpeed) {
		if (this.hooks.accelerate.isUsed()) {
			this.hooks.accelerate.call(newSpeed);
		}
	}
	// ...
}
```

## MultiHook

A `MultiHook` is a Hook-like facade that forwards `tap`, `tapAsync`, `tapPromise`, `intercept`, and `withOptions` to several underlying hooks at once. It does not expose `call*` methods — only the owners of the wrapped hooks decide when each of them runs. It is the typical way a class exposes a "happens on any of these events" listening surface without having the plugin wire itself up to every hook individually.

### Fan out a tap to several hooks

```js
const { MultiHook, SyncHook } = require("tapable");

class Car {
	constructor() {
		const accelerate = new SyncHook(["newSpeed"]);
		const brake = new SyncHook();
		this.hooks = {
			accelerate,
			brake,
			// `anyMovement` is not a real hook — it simply re-registers taps
			// on both `accelerate` and `brake`.
			anyMovement: new MultiHook([accelerate, brake])
		};
	}
}

const car = new Car();
car.hooks.anyMovement.tap("Telemetry", () => console.log("car moved"));

car.hooks.accelerate.call(42); // "car moved"
car.hooks.brake.call(); // "car moved"
```

The `MultiHook` has no state of its own: the tap above ends up inside `accelerate.taps` and `brake.taps`.

### Forwarding async taps

`tapAsync` / `tapPromise` forward to every wrapped hook — it is the plugin's job to make sure they are all compatible. Registering a `tapPromise` on a `MultiHook` that wraps a `SyncHook` will throw at registration time for that hook.

```js
const build = new AsyncSeriesHook(["stats"]);
const rebuild = new AsyncSeriesHook(["stats"]);
const anyBuild = new MultiHook([build, rebuild]);

anyBuild.tapPromise("Report", async (stats) => report.send(stats));
```

### Shared interceptors and options

`intercept` and `withOptions` are also forwarded, so a `MultiHook` can be used to attach the same interceptor or pre-configured options to a group of hooks:

```js
const anyBuild = new MultiHook([build, rebuild]);

anyBuild.intercept({
	call: () => console.log("build started"),
	done: () => console.log("build done")
});

// Every tap added through `late` is staged late on both underlying hooks.
const late = anyBuild.withOptions({ stage: 10 });
late.tap("RunLast", () => {
	/* ... */
});
```

### `isUsed`

`isUsed()` returns `true` if any of the wrapped hooks has at least one tap or interceptor, which lets the owner cheaply skip work when no one is listening on any of them:

```js
if (this.hooks.anyMovement.isUsed()) {
	// expensive telemetry payload is only built when a plugin actually cares
	this.hooks.accelerate.call(computeSpeed());
}
```
