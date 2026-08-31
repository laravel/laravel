---
name: nostics
description: "Structured diagnostic code library for JavaScript/TypeScript. Turns errors and other conditions into typed, machine-readable `Diagnostic` instances with stable codes, docs URLs, and actionable fields. Use this skill whenever the project imports `nostics`, or works with `defineDiagnostics`/`defineProdDiagnostics`, the `Diagnostic` class, diagnostic code registries, or structured error handling. Also covers reporters (`createConsoleReporter`, `createFetchReporter` from nostics/reporters/fetch, `createFileReporter` from nostics/reporters/node, `createDevReporter` from nostics/reporters/dev), formatters (`formatDiagnostic`, `ansiFormatter`, `jsonFormatter`), and Vite plugins (`nosticsStrip` from @nostics/unplugin/strip-transform, `nosticsCollector` from @nostics/unplugin/dev-server-collector). Also use when migrating a library's existing `console.warn`/`console.error`/`warn()` helpers or thrown `Error`s to diagnostics: follow `references/migration.md`."
license: MIT
---

# nostics

Every error condition becomes a typed `Diagnostic` (extends `Error`) with a stable code, docs URL, and actionable `fix`. Serializable via `toJSON()`.

`Diagnostic`: `name` (the code), `message`/`why` (interpolated text), `fix?`, `docs?`, `sources?` (`'file:line:column'`), `cause?`, `toJSON()`. Throw it, catch it with `instanceof Diagnostic`, send `toJSON()` across process boundaries.

## defineDiagnostics

Returns one callable handle per code. Calling a handle builds a fresh `Diagnostic`, fires every reporter in order, and returns it. `throw` the return value to raise (reporters still run, so a thrown diagnostic also reports).

```ts
import { createConsoleReporter, defineDiagnostics } from 'nostics'

const diagnostics = /*#__PURE__*/ defineDiagnostics({
  docsBase: (code) => `https://nuxt.com/e/${code.replace('NUXT_', '').toLowerCase()}`,
  reporters: [/*#__PURE__*/ createConsoleReporter()],
  codes: {
    NUXT_B1001: {
      why: 'Could not compile template.',
      fix: 'Check the template for syntax errors.',
    },
    NUXT_B2011: {
      why: (p: { src: string }) => `Invalid plugin "${p.src}". src option is required.`,
      fix: 'Pass a string path or an object with a `src` to `addPlugin()`.',
    },
    NUXT_W9001: { why: 'message', docs: false }, // per-code: string overrides docsBase, false opts out
  },
})
```

- **`docsBase`** `string | (code) => string | undefined`: string appends `/${code.toLowerCase()}`; function returns the full URL (or `undefined` to omit).
- **`codes`**: each definition needs `why` (`string | (params) => string`, the only required field, becomes `Error.message`); optional `fix` (`string | (params) => string`) and `docs` (`string | false`).
- **`reporters`**: fired on every call; optional. Their `options` types are intersected; required reporter options become required at the call site. Omit it (or pass `[]`) for a catalog whose codes are only ever `throw`n: the thrown `Diagnostic` already carries the message, so a console reporter would print it once and surface it again from the uncaught error, a visible duplicate. Keep report-only warnings and fatal throws in separate catalogs when one needs a reporter and the other does not.
- **Param inference**: params from `why` and `fix` are intersected and required at the call site. If `why` needs `{ src }` and `fix` needs `{ date }`, the call requires `{ src, date }`.

## Call sites

```ts
diagnostics.NUXT_B1001() // no params: report only
diagnostics.NUXT_B2011({ src: '/plugins/bad.ts' }) // params first
diagnostics.NUXT_B2011({
  src,
  cause: originalError,
  sources: ['nuxt.config.ts:42:3'],
}) // runtime fields merge in
diagnostics.NUXT_B2011({ src }, { method: 'error' }) // reporter options second
throw diagnostics.NUXT_B2011({ src }) // raise
```

`cause`/`sources` go in the params object; `sources` matters most for build/config diagnostics where the JS stack points inside the library. Catch with `if (err instanceof Diagnostic)` then read `.name`, `.message`, `.fix`, `.docs`.

## Reporters

`(diagnostic: Diagnostic, options?: Opts) => void`. Declaring a required `options` type makes the second call-site argument required and typed.

| Reporter                          | Import                    | Description                                                                                                                                                                            |
| --------------------------------- | ------------------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `createConsoleReporter(options?)` | `nostics`                 | `console[method](formatter(d))`. `method` defaults `'warn'` (`'log'\|'warn'\|'error'`), `formatter` defaults `formatDiagnostic`; both via options, `method` also overridable per call. |
| `createFetchReporter(url)`        | `nostics/reporters/fetch` | POSTs diagnostic JSON to the URL; failures swallowed.                                                                                                                                  |
| `createFileReporter(options?)`    | `nostics/reporters/node`  | Appends NDJSON to a local file (default `.nostics.log`).                                                                                                                               |
| `createDevReporter()`             | `nostics/reporters/dev`   | Sends `toJSON()` to the Vite dev server via `import.meta.hot.send()`.                                                                                                                  |

```ts
import type { DiagnosticReporter } from 'nostics'
const sentryReporter: DiagnosticReporter = (d) =>
  sentry.captureMessage(d.message, { tags: { code: d.name } })
const audited: DiagnosticReporter<{ priority: number }> = (d, o) =>
  audit.log({ name: d.name, priority: o.priority })
// → audited makes diagnostics.X({...}, { priority: 1 }) required and type-checked.
```

## Formatters

| Formatter               | Import                    | Description                                                                                                |
| ----------------------- | ------------------------- | ---------------------------------------------------------------------------------------------------------- |
| `formatDiagnostic`      | `nostics`                 | Plain unicode-decorated string (built-in reporters use it).                                                |
| `ansiFormatter(colors)` | `nostics/formatters/ansi` | Colorized; accepts a `Colors` interface (`red`/`yellow`/`cyan`/`gray`/`bold`/`dim`, each `(s) => string`). |
| `jsonFormatter`         | `nostics/formatters/json` | `JSON.stringify(diagnostic)` via `toJSON()`.                                                               |

`formatDiagnostic` output, detail order fixed `fix` → `sources` → `see`, missing fields omitted:

```
[NUXT_B2011] Invalid plugin `/plugins/bad.ts`. src option is required.
├▶ fix: Pass a string path or an object with a `src` to `addPlugin()`.
├▶ sources: nuxt.config.ts:42:3
╰▶ see: https://nuxt.com/e/b2011
```

## Vite plugins (`@nostics/unplugin`, dev dependency)

`@nostics/unplugin/strip-transform` (library authors, build optimization) and `@nostics/unplugin/dev-server-collector` (app developers, dev-time collection). Both unplugin-based: `.vite()`, `.webpack()`, `.rollup()`, etc.

- **`nosticsStrip`** marks `defineDiagnostics()` `/*#__PURE__*/` and wraps bare diagnostic expression statements with a `NODE_ENV` guard so they tree-shake out of production. Option `packageName?` (default `'nostics'`). Throws/returns/assignments stay (they are behavior). For tracking: relative imports, export the catalog directly, no factory wrappers or deep barrels.
  - The plugin is optional. The same production output happens with no build transform if the catalog is annotated by hand: put `/*#__PURE__*/` before `defineDiagnostics(` and before each reporter factory call inside it (as in every example here), and dev-guard each report-only call site (`process.env.NODE_ENV !== 'production' && diagnostics.CODE(p)`). Always write the annotations in source; reach for the plugin when report-only call sites are unguarded and you want stripping without touching them.
- **`nosticsCollector`** listens for `createDevReporter()` diagnostics over the Vite WebSocket and writes them as NDJSON via `createFileReporter`. Vite-only. Options `logFile?` (default `.nostics.log`), `debug?` (default `!!process.env.DEBUG`).

```ts
// vite.config.ts
import { nosticsStrip } from '@nostics/unplugin/strip-transform'
import { nosticsCollector } from '@nostics/unplugin/dev-server-collector'
export default defineConfig({
  plugins: [nosticsStrip.vite(), nosticsCollector.vite()],
})

// src/diagnostics.ts — pair the collector with createDevReporter()
import { createConsoleReporter, defineDiagnostics } from 'nostics'
import { createDevReporter } from 'nostics/reporters/dev'
export const diagnostics = /*#__PURE__*/ defineDiagnostics({
  reporters: [/*#__PURE__*/ createConsoleReporter(), /*#__PURE__*/ createDevReporter()],
  codes: {
    /* ... */
  },
})
```

## Production builds

- **Report-only** diagnostics (bare `diagnostics.X()`) should disappear: `nosticsStrip` or hand annotations drop them, then the unused catalog tree-shakes.
- **Surviving** diagnostics (`throw`/`return`/assigned/argument) stay, and each keeps the _whole_ catalog reachable, so every `why`/`fix` ships. Not every library throws in production: if yours only reports, stripping is enough, stop here.

When a library _does_ `throw` in production, pick `defineProdDiagnostics` at definition time with a `NODE_ENV` ternary, so a consumer bundler drops the dev branch (all catalog text):

```ts
import { defineDiagnostics, defineProdDiagnostics } from 'nostics'
export const diagnostics =
  process.env.NODE_ENV === 'production'
    ? /*#__PURE__*/ defineProdDiagnostics({ docsBase })
    : /*#__PURE__*/ defineDiagnostics({
        docsBase,
        reporters: [
          /* ... */
        ],
        codes: {
          /* text */
        },
      })
```

The accessed code becomes the instance `name`, `docs` still derives from `docsBase`, `why` points to the docs URL when one exists (empty otherwise), no `why`/`fix` text ships. No `reporters` by default (so a surviving `throw` doesn't also log and then resurface as the uncaught error); pass `reporters` to keep prod telemetry. `nosticsStrip` tracks this ternary like a direct catalog export.

## Conventions

- **Codes** are stable, fully-qualified `PREFIX_XNNNN` (`B` build, `R` runtime, `C` config, `D` deprecation). Never reuse or reassign a published code.
- Always provide `why`; provide `fix` whenever the solution is known (the most actionable field for humans and agents). Use parameterized templates for runtime values, not string concatenation outside the factory.
- **`why` is the diagnosis, `fix` is the remedy — split them, don't overlap them.** `why` states only what is wrong; `fix` states only what to do. The reporter prints both, so any wording that appears in both is dead weight. When a single source sentence carries both (`"A hash must start with '#'. Prefix it with '#'."`), cut it in two — diagnosis to `why`, remedy to `fix` — rather than pasting the whole thing into `why` and echoing it in `fix`. `fix` accepts a param function too (`(p) => ...`), so move value-bearing remedies (`use "#${p.hash}"`) into it instead of leaving them in `why`.
- Pass `cause` when re-raising; pass `sources` when the JS stack doesn't reflect the user's source.
- Split large catalogs by domain (`diagnostics/build.ts`, `runtime.ts`, `config.ts`, re-exported from `index.ts`), each `defineDiagnostics()` sharing `docsBase` with its own code range.

## References

- **Migrating an existing library** to nostics (replacing `console.warn`/`console.error`/`warn()`/thrown `Error`s with diagnostic codes, without changing runtime behavior): follow `references/migration.md` start to finish.
- Building the error-code documentation site (page template, deployment, agent optimization): `references/documentation-site.md`.
