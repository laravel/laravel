# @rolldown/pluginutils [![npm](https://img.shields.io/npm/v/@rolldown/pluginutils.svg)](https://npmx.dev/package/@rolldown/pluginutils)

Plugin utilities for [Rolldown](https://rolldown.rs).

Includes regex helpers for plugin hook filters, composable filter expressions, and a helper for filtering out Vite-serve-only plugins.

## Install

```bash
pnpm add -D @rolldown/pluginutils
```

## Usage

```ts
import { exactRegex, prefixRegex, makeIdFiltersToMatchWithQuery } from '@rolldown/pluginutils'
```

All filter helpers are also exposed via the `/filter` subpath:

```ts
import { and, or, id, include } from '@rolldown/pluginutils/filter'
```

## Regex helpers

### `exactRegex`

- **Type:** `(str: string, flags?: string) => RegExp`

Constructs a `RegExp` that matches the exact string specified. Useful as a plugin hook filter.

```ts
import { exactRegex } from '@rolldown/pluginutils'

const plugin = {
  name: 'plugin',
  resolveId: {
    filter: { id: exactRegex('foo') },
    handler(id) {}, // only called for `foo`
  },
}
```

### `prefixRegex`

- **Type:** `(str: string, flags?: string) => RegExp`

Constructs a `RegExp` that matches values starting with the specified prefix.

```ts
import { prefixRegex } from '@rolldown/pluginutils'

const plugin = {
  name: 'plugin',
  resolveId: {
    filter: { id: prefixRegex('foo') },
    handler(id) {}, // called for IDs starting with `foo`
  },
}
```

### `makeIdFiltersToMatchWithQuery`

- **Type:** `(input: string | RegExp | (string | RegExp)[]) => string | RegExp | (string | RegExp)[]`

Converts an id filter so that it also matches ids that include a query string.

```ts
import { makeIdFiltersToMatchWithQuery } from '@rolldown/pluginutils'

const plugin = {
  name: 'plugin',
  transform: {
    filter: { id: makeIdFiltersToMatchWithQuery(['**/*.js', /\.ts$/]) },
    // Matches:
    //   foo.js, foo.js?foo, foo.txt?foo.js,
    //   foo.ts, foo.ts?foo, foo.txt?foo.ts
    handler(code, id) {},
  },
}
```

## Composable filters

[Composable filter expressions](https://rolldown.rs/apis/plugin-api/hook-filters#composable-filters) for use cases where a simple `id`/`include`/`exclude` is not enough. For example, when a plugin needs to combine `id`, `moduleType`, `code`, and `query` conditions.

```ts
import { and, code, id, include, interpreter, moduleType, or } from '@rolldown/pluginutils'

const expr = include(and(or(id(/\.tsx?$/), id(/\.jsx?$/)), moduleType('tsx'), code(/import React/)))

interpreter(expr, sourceCode, sourceId, 'tsx') // boolean
```

### Builders

| Builder                        | Description                                                                                                                                              |
| ------------------------------ | -------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `and(...exprs)`                | All operands must match.                                                                                                                                 |
| `or(...exprs)`                 | At least one operand must match.                                                                                                                         |
| `not(expr)`                    | Negates the operand.                                                                                                                                     |
| `id(pattern, params?)`         | Match the module id. `pattern` is `string` or `RegExp`. `params.cleanUrl` strips the query/hash before matching.                                         |
| `importerId(pattern, params?)` | Match the importer's id. Same shape as `id`.                                                                                                             |
| `moduleType(type)`             | Match Rolldown's module type (`'js'`, `'jsx'`, `'ts'`, `'tsx'`, `'json'`, `'text'`, `'base64'`, `'dataurl'`, `'binary'`, `'empty'`, or a custom string). |
| `code(pattern)`                | Match the module source. `string` matches with `includes`; `RegExp` with `test`.                                                                         |
| `query(key, pattern)`          | Match a single query parameter. `pattern` is `boolean` (key presence/truthiness), `string` (exact value), or `RegExp` (value pattern).                   |
| `queries(obj)`                 | Shorthand for `and(...)` over multiple `query` entries.                                                                                                  |
| `include(expr)`                | Top-level wrapper marking `expr` as an inclusion rule.                                                                                                   |
| `exclude(expr)`                | Top-level wrapper marking `expr` as an exclusion rule.                                                                                                   |

### `interpreter`

- **Type:** `(exprs, code?, id?, moduleType?, importerId?) => boolean`

Evaluates one or more top-level expressions against the given inputs. Returns `true` when at least one `include` matches and no `exclude` matches; when no `include` is present, defaults to `true` unless an `exclude` matches.

The argument required by each expression must be provided. For example, evaluating an `id(...)` expression without passing `id` will throw.

## `filterVitePlugins`

- **Type:** `<T>(plugins: T | T[] | null | undefined | false) => T[]`

Removes Vite plugins that target the dev server (`apply: 'serve'`) from a (possibly nested) plugin array. Plugins whose `apply` is a function are invoked with a `command: 'build'` context to decide. Useful when reusing a Vite plugin array inside a Rolldown config.

```ts
import { defineConfig } from 'rolldown'
import { filterVitePlugins } from '@rolldown/pluginutils'
import viteReact from '@vitejs/plugin-react'

export default defineConfig({
  plugins: filterVitePlugins([
    viteReact(),
    {
      name: 'dev-only',
      apply: 'serve', // filtered out
      // ...
    },
  ]),
})
```

## License

MIT
