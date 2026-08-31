# 🧵 Scule

[![npm version][npm-version-src]][npm-version-href]
[![npm downloads][npm-downloads-src]][npm-downloads-href]
[![bundle][bundle-src]][bundle-href]
[![Codecov][codecov-src]][codecov-href]

<!-- ![](.github/banner.svg) -->

## Install

Install using npm or yarn:

```bash
npm i scule
```

Import:

```js
// CommonJS
const { pascalCase } = require("scule");

// ESM
import { pascalCase } from "scule";
```

**Notice:** You may need to transpile package for legacy environments.

## Utils

### `pascalCase(str, opts?: { normalize })`

Splits string and joins by PascalCase convention:

```ts
pascalCase("foo-bar_baz");
// FooBarBaz
```

**Notice:** If an uppercase letter is followed by other uppercase letters (like `FooBAR`), they are preserved. You can use `{ normalize: true }` for strictly following pascalCase convention.

### `camelCase(str, opts?: { normalize })`

Splits string and joins by camelCase convention:

```ts
camelCase("foo-bar_baz");
// fooBarBaz
```

### `kebabCase(str)`

Splits string and joins by kebab-case convention:

```ts
kebabCase("fooBar_Baz");
// foo-bar-baz
```

**Notice:** It does **not** preserve case.

### `snakeCase`

Splits string and joins by snake_case convention:

```ts
snakeCase("foo-barBaz");
// foo_bar_baz
```

### `flatCase`

Splits string and joins by flatcase convention:

```ts
flatCase("foo-barBaz");
// foobarbaz
```

### `trainCase(str, opts?: { normalize })`

Split string and joins by Train-Case (a.k.a. HTTP-Header-Case) convention:

```ts
trainCase("FooBARb");
// Foo-Ba-Rb
```

**Notice:** If an uppercase letter is followed by other uppercase letters (like `WWWAuthenticate`), they are preserved (=> `WWW-Authenticate`). You can use `{ normalize: true }` for strictly only having the first letter uppercased.

### `titleCase(str, opts?: { normalize })`

With Title Case all words are capitalized, except for minor words.
A compact regex of common minor words (such as `a`, `for`, `to`) is used to automatically keep them lower case.

```ts
titleCase("this-IS-aTitle");
// This is a Title
```

### `upperFirst(str)`

Converts first character to upper case:

```ts
upperFirst("hello world!");
// Hello world!
```

### `lowerFirst(str)`

Converts first character to lower case:

```ts
lowerFirst("Hello world!");
// hello world!
```

### `splitByCase(str, splitters?)`

- Splits string by the splitters provided (default: `['-', '_', '/', '.']`)
- Splits when case changes from lower to upper or upper to lower
- Ignores numbers for case changes
- Case is preserved in returned value
- Is an irreversible function since splitters are omitted

## Development

- Clone this repository
- Install latest LTS version of [Node.js](https://nodejs.org/en/)
- Enable [Corepack](https://github.com/nodejs/corepack) using corepack enable
- Install dependencies using pnpm install
- Run interactive tests using pnpm dev

## License

[MIT](./LICENSE)

<!-- Badges -->

[npm-version-src]: https://img.shields.io/npm/v/scule?style=flat&colorA=18181B&colorB=F0DB4F
[npm-version-href]: https://npmjs.com/package/scule
[npm-downloads-src]: https://img.shields.io/npm/dm/scule?style=flat&colorA=18181B&colorB=F0DB4F
[npm-downloads-href]: https://npmjs.com/package/scule
[codecov-src]: https://img.shields.io/codecov/c/gh/unjs/scule/main?style=flat&colorA=18181B&colorB=F0DB4F
[codecov-href]: https://codecov.io/gh/unjs/scule
[bundle-src]: https://img.shields.io/bundlephobia/minzip/scule?style=flat&colorA=18181B&colorB=F0DB4F
[bundle-href]: https://bundlephobia.com/result?p=scule
