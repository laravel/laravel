<p align="center">
  <img src="./docs/public/nostics.svg" alt="nostics" width="320">
</p>

# nostics

[![npm version](https://img.shields.io/npm/v/nostics?color=blue)](https://npmx.dev/nostics)
[![npm downloads](https://img.shields.io/npm/dw/nostics?color=blue)](https://npmx.dev/nostics)
[![CI](https://img.shields.io/github/actions/workflow/status/vercel-labs/nostics/ci.yml?branch=main&label=CI)](https://github.com/vercel-labs/nostics/actions/workflows/ci.yml)
[![bundle size](https://deno.bundlejs.com/badge?q=nostics&treeshake=[{+defineDiagnostics+}])](https://bundlejs.com/?q=nostics&treeshake=%5B%7B+defineDiagnostics+%7D%5D)

Errors worth reading.

`nostics` helps you replace ad hoc error strings with stable diagnostic codes, actionable fixes, source locations, and docs links.

```txt
[NUXT_B2011] Plugin `./runtime/analytics.server.ts` is server-only but was registered with mode `client`.
├▶ fix: Rename the file or register it with mode `server`.
├▶ sources: modules/analytics.ts:18:5
╰▶ see: https://nuxt.com/e/b2011
```

## Install

```bash
pnpm add nostics
```

## Quick start

```ts
import { createConsoleReporter, defineDiagnostics } from 'nostics'

export const diagnostics = defineDiagnostics({
  docsBase: code => `https://nuxt.com/e/${code.replace('NUXT_', '').toLowerCase()}`,
  reporters: [createConsoleReporter()],
  codes: {
    NUXT_B2011: {
      why: (p: { src: string, mode: 'client' | 'server' }) => {
        const expected = p.mode === 'client' ? 'server' : 'client'
        return `Plugin "${p.src}" is ${expected}-only but was registered with mode "${p.mode}".`
      },
      fix: (p: { mode: 'client' | 'server' }) => {
        const expected = p.mode === 'client' ? 'server' : 'client'
        return `Rename the file or register it with mode "${expected}".`
      },
    },
    NUXT_B5001: {
      why: (p: { value: string, configPath: string }) =>
        `Invalid compatibilityDate "${p.value}" in ${p.configPath}.`,
      fix: (p: { example: string }) => `Use an ISO date like "${p.example}", or "latest".`,
    },
  },
})
```

Use the generated handles where the problem happens:

```ts
const plugin = resolvePlugin()
const source = locatePluginCall(plugin)
const config = loadNuxtConfig()

diagnostics.NUXT_B2011({
  src: plugin.src,
  mode: plugin.mode,
  sources: [source],
})

throw diagnostics.NUXT_B5001({
  configPath: config.filepath,
  value: config.compatibilityDate,
  example: '2024-04-03',
})
```

Calling a handle reports the diagnostic and returns a `Diagnostic`. Throwing the return value raises it. The params are inferred from your `why` and `fix` functions.

## Claude Code plugin

Install the plugin to give Claude skills for your diagnostic catalog:

```bash
claude plugin add https://github.com/vercel-labs/nostics
```

Claude will automatically pick up the `nostics` API reference and an `add-diagnostic` skill that finds the right catalog, chooses the next free code, and wires the call site.

## Agent skills

Prefer just the skills, without the plugin? Install them with [`npx skills`](https://github.com/vercel-labs/skills) into any supported agent (Claude Code, Codex, Cursor, opencode, and more):

```bash
npx skills add vercel-labs/nostics
```

## Why use it

- Stable codes that users can search and docs can link to.
- Typed params at the call site.
- `Diagnostic` instances that extend `Error`.
- Built-in console, file, fetch, and Vite dev reporters.
- Plain, ANSI, and JSON formatters.
- A build plugin that strips report-only diagnostics from production bundles.

The structured shape also makes diagnostics easier for tools and coding agents to read, without making that the main workflow.

## Vite plugins

Build-time plugins live in the separate [`@nostics/unplugin`](./packages/unplugin) package:

```bash
pnpm add -D @nostics/unplugin
```

For library builds, use the strip plugin:

```ts
import { nosticsStrip } from '@nostics/unplugin/strip-transform'

export default defineConfig({
  plugins: [nosticsStrip.vite()],
})
```

For browser diagnostics during Vite dev, use `createDevReporter()` in the browser and `nosticsCollector` in the consuming app:

```ts
import { nosticsCollector } from '@nostics/unplugin/dev-server-collector'

export default defineConfig({
  plugins: [nosticsCollector.vite()],
})
```

## Docs

See the docs site for the guide, production build notes, dev collector setup, and API reference.

## License

[MIT](./LICENSE)
