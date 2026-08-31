# magic-string-ast

[![npm version][npm-version-src]][npm-version-href]
[![npm downloads][npm-downloads-src]][npm-downloads-href]
[![JSR][jsr-badge-src]][jsr-badge-href]
[![Unit Test][unit-test-src]][unit-test-href]

[magic-string](https://github.com/rich-harris/magic-string) with AST shortcut.

## Install

```bash
# npm
npm i magic-string-ast

# jsr
npx jsr add -D @sxzz/magic-string-ast
```

## Usage

```ts
import { MagicStringAST } from 'magic-string-ast'

const offset = 0
const node = {
  // AST node with `start` and `end` properties
  start: 6,
  end: 7,
  // ...
}

const s = new MagicStringAST('const a = 1')
s.sliceNode(node, { offset }) // 'a'
s.removeNode(node)
s.moveNode(node, 0)
s.overwriteNode(node, 'foo')

// support source-map, inspired by muggle-string.
s.replaceRange(5, 5, '(', expression, ')') // appendLeft
s.replaceRange(5, 8, '(', expression, ')') // overwrite
s.replaceRange(5, 8) // remove
```

For more APIs, see [docs](https://jsr.io/@sxzz/magic-string-ast/doc) and [magic-string](https://github.com/rich-harris/magic-string#usage).

## Sponsors

<p align="center">
  <a href="https://cdn.jsdelivr.net/gh/sxzz/sponsors/sponsors.svg">
    <img src='https://cdn.jsdelivr.net/gh/sxzz/sponsors/sponsors.svg'/>
  </a>
</p>

## License

[MIT](./LICENSE) License © 2023-PRESENT [Kevin Deng](https://github.com/sxzz)

<!-- Badges -->

[npm-version-src]: https://img.shields.io/npm/v/magic-string-ast.svg
[npm-version-href]: https://npmjs.com/package/magic-string-ast
[npm-downloads-src]: https://img.shields.io/npm/dm/magic-string-ast
[npm-downloads-href]: https://www.npmcharts.com/compare/magic-string-ast?interval=30
[jsr-badge-src]: https://jsr.io/badges/@sxzz/magic-string-ast
[jsr-badge-href]: https://jsr.io/@sxzz/magic-string-ast
[unit-test-src]: https://github.com/sxzz/magic-string-ast/actions/workflows/unit-test.yml/badge.svg
[unit-test-href]: https://github.com/sxzz/magic-string-ast/actions/workflows/unit-test.yml
