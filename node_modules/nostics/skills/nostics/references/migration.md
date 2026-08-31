# Migrate errors and warnings to nostics

Follow this start to finish whenever the task is migrating a library's existing user-facing errors, warnings, and logs (`console.warn`/`console.error`, `warn()` helpers, thrown `Error`s) to nostics diagnostics. Turn ad hoc reporting into a catalog of stable diagnostic codes **without changing runtime behavior**. Full API: the `nostics` skill's `SKILL.md` (this reference lives beside it).

## What to migrate

Inventory `console.warn`, `console.error`, `warn(...)` helpers, `throw new Error(...)`, `Promise.reject(new Error(...))`. Skip tests, fixtures, snapshots, and generated output. Plain debug `console.log`s are usually not user-facing; leave them.

Migrate:

- dev warnings that report and keep going
- warnings followed by recovery or a fallback (replace only the report, keep the recovery)
- plain user-facing thrown or rejected `Error`s (the diagnostic becomes the thrown/rejected value)
- deprecation notices
- build/config errors caused by a user's file: always pass **both** the original error as `cause` and the file as `sources`, because the JS stack points inside the library and is useless to the user

Do **not** migrate:

- **structured errors other code inspects** (type fields, private symbols, `isXxxError()` guards, `instanceof` checks): they are control flow, not reporting. Leave them unchanged. Only if such an error is also deliberately user-facing, add a separate dev-only report with the error as `cause`; never replace the error object itself.
- **catch blocks that only log a native/platform error and fall back** when the library cannot name a likely cause or a concrete fix: the native error is the best available information, keep the plain log. This exception covers platform APIs failing (e.g. `history.pushState`, storage quota), **not** errors caused by the user's own files: a caught parse or config error on a user file should become a diagnostic carrying the original error as `cause` and the file as `sources`.
- anything where the diagnostic would only restate "an operation failed". A diagnostic earns its place by naming a likely user-code problem or a concrete fix.

## Preserve behavior exactly

A project's dev guard may be `process.env.NODE_ENV !== 'production'` or its own build-time constant (libraries often define a flag for this; recognize whatever the codebase uses). Written as `DEV` below; treat all forms the same:

- Keep existing dev guards exactly as they are. nostics stripping is additive and does not replace them. If a throw or reject only happened in dev, it must still only happen in dev.
- Never add a guard the original did not have. A throw or report that fired in production keeps firing in production builds that do not use stripping; note that migrating an unguarded report-only call makes it strippable, so once `nosticsStrip` runs in the build it disappears from production bundles. That is usually the goal of the migration, but if the library deliberately reports in production, surface that decision instead of changing it silently.
- Keep throw vs reject, timing, recovery code, and returned fallbacks.
- Keep structured error shapes (fields, symbols). Migrating a throw replaces the thrown message with the diagnostic's `why`: if tests assert the exact old text, update them deliberately as part of the migration, never weaken the message to dodge a test.

## Catalog shape

One catalog file per area of the library (a single `src/diagnostics.ts` is fine for small ones), exported directly. No factory wrappers and no deep barrel re-exports: the strip plugin tracks the export across one relative import. `nostics` is a runtime import: add it to `dependencies`, not `devDependencies` (library bundlers refuse or inline it otherwise).

```ts
import { createConsoleReporter, defineDiagnostics } from 'nostics'

export const diagnostics = /*#__PURE__*/ defineDiagnostics({
  docsBase: (code) => `https://example.com/e/${code.toLowerCase()}`,
  reporters: [/*#__PURE__*/ createConsoleReporter()],
  codes: {
    LIB_R0001: {
      why: (p: { hook: string }) => `${p.hook}() must be called at the top of a setup function.`,
      fix: 'Move the call into setup() or a composable called by setup().',
    },
  },
})
```

- Codes are `PREFIX_XNNNN`. Pick the category letter by **area**, not severity: `B` build, `R` runtime, `C` config, `D` deprecation. A runtime warning is `R`; reserve `D` for deprecations. Published codes are permanent: never rename or reuse one.
- `why` says what happened with runtime values interpolated through typed param functions (both `why` and `fix` accept them; their params are merged and required at the call site). `fix` is the concrete next action, never a restatement of the problem.
- **Split the original warning; never copy it whole into `why`.** Most existing warnings bundle the diagnosis and the remedy in one string (`"A hash must start with '#'. Prefix it with '#'."`). The reporter prints `why` **and** `fix`, so pasting the full sentence into `why` and then writing a `fix` duplicates the remedy on screen. Cut the sentence in two: diagnosis to `why`, remedy to `fix`. If the remedy needs the offending value, make `fix` a param function — its params merge with `why`'s.

  ```ts
  // before:  warn(`A \`hash\` should start with "#". Replace "${hash}" with "#${hash}".`)

  // ❌ remedy lives in why and is echoed by fix
  { why: (p) => `A \`hash\` should start with "#". Replace "${p.hash}" with "#${p.hash}".`,
    fix: 'Prefix the hash with "#".' }

  // ✅ diagnosis in why, remedy in fix (a function, because it needs the value)
  { why: (p) => `A \`hash\` should start with "#" but received "${p.hash}".`,
    fix: (p) => `Prepend "#": use "#${p.hash}".` }
  ```

- Extra `console.warn`/`console.error` arguments must not be lost: an error value becomes `cause`; data values are interpolated into `why` (e.g. `JSON.stringify(p.value)`).
- `cause` and `sources` (`'file:line:column'` strings pointing at user code) go **inside the params object** (the first argument), merged with the message params. The second argument is reporter options only, e.g. `{ method: 'error' }`.
- `docsBase` is optional. If the project has no documented error-page URL scheme, propose one and surface it to the maintainer rather than inventing pages that do not exist. When pointing per-code `docs` at existing documentation with `#hash` anchors, verify each anchor against the built or published HTML (grep the `id=`) instead of guessing it. A custom slugify can keep heading casing and leave a trailing hyphen, so the real anchor may look like `#Using-the-store-outside-of-setup-`.

## Call-site patterns

| Before                                      | After                                                                                                  |
| ------------------------------------------- | ------------------------------------------------------------------------------------------------------ |
| `DEV && warn(msg)`                          | `DEV && diagnostics.LIB_R0001(params)` (same guard)                                                    |
| warn, then recover/fallback                 | diagnostic, then the same recovery                                                                     |
| warn, then `throw new Error(...)`           | `throw diagnostics.LIB_R0002(params)`                                                                  |
| warn, then `Promise.reject(new Error(...))` | `return Promise.reject(diagnostics.LIB_R0003(params))`                                                 |
| `console.error(...)` level                  | `diagnostics.LIB_B0001(params, { method: 'error' })`                                                   |
| caught error tied to a user file            | `diagnostics.LIB_B0002({ ...params, cause: err, sources: ['src/file.ts:10:5'] }, { method: 'error' })` |
| structured/internal error                   | leave unchanged                                                                                        |

Calling a handle always runs the reporters, so `throw diagnostics.CODE(params)` reports **and** throws. For a warn-then-throw site that is the same double output it already had. For a bare `throw new Error(...)` it adds a console report before the throw, which duplicates the message the uncaught error already shows. When a catalog's codes are only ever thrown (config validators, fatal asserts), give that catalog **no reporters** (`reporters` is optional) so the throw is the only output, and keep warnings in a separate reporter-backed catalog. Dropping the reporter also keeps a strict test harness happy when its `afterEach` fails on any unasserted `console.warn`/`console.error`.

Report-only calls must stay bare expression statements (`DEV && diagnostics.LIB_R0001(p)` included) so `nosticsStrip` can remove them in production. `throw`/`return`/assigned diagnostics are behavior and stay.

Dropping diagnostics from production builds takes two pieces: `/*#__PURE__*/` annotations on the catalog (`defineDiagnostics(...)` and each reporter factory call inside it) so an unused catalog tree-shakes away, and a `DEV` guard on every report-only call site. Both can be written manually in source, or the `nosticsStrip` build plugin adds them at build time (`import { nosticsStrip } from '@nostics/unplugin/strip-transform'`, then the matching unplugin adapter: `nosticsStrip.rolldown()`, `.vite()`, `.rollup()`, ...). Decide from context: when every report-only site is already dev-guarded, manual annotations in the catalog file are enough and avoid a build transform; reach for the plugin when call sites are unguarded and stripping is wanted. Either way the behavior rule holds: if the library deliberately reports unguarded in production, do not silence it with a guard or the plugin; ask the maintainer.

## Verify

- Tests for warnings, throws, guards, and error shapes still pass; tests asserting exact message text are updated consciously, not accidentally.
- Watch substring assertions when splitting a warning. `toHaveBeenWarned('...')` / `toContain` pin a **fragment**, not the whole message, and a fragment may sit in the remedy half you just moved to `fix`. Before splitting, grep the tests for substrings of each warning: keep pinned **diagnosis** fragments in `why`; when a test pins a **remedy** fragment, update that assertion to the surviving `why` text. The same warning is often pinned by a shared constant duplicated across several spec files — fix every copy.
- Dev-only gates are still present everywhere the source had them, and no new gates were added.
- Report-only diagnostics remain strippable expression statements. Thrown/returned diagnostics keep their message text in production by design: they are behavior, not reports.
