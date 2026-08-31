# ast-walker-scope

[![npm version][npm-version-src]][npm-version-href]
[![npm downloads][npm-downloads-src]][npm-downloads-href]
[![Unit Test][unit-test-src]][unit-test-href]

Traverse Babel AST with scope information.

Inherited from [estree-walker](https://github.com/Rich-Harris/estree-walker).

## Install

```bash
npm i ast-walker-scope
```

## Usage

### Basic Example

For a real example, you can refer to [example.ts](./example.ts)

```ts
import { walk } from 'ast-walker-scope'

const code = `
const a = 'root level'

{
  const a = 'second level'
  let secondLevel = true
  console.log(a, secondLevel)
}

var err = undefined
try {
} catch (err) {
  console.log(err)
}

console.log(a)
`.trim()

walk(code, {
  leave(this, node) {
    if (node.type === 'CallExpression') {
      console.log(`\nLevel: ${this.level}`)
      for (const [name, node] of Object.entries(this.scope)) {
        console.log(
          `variable ${name} is located at line ${node.loc?.start.line}, column ${node.loc?.start.column}`,
        )
      }
    }
  },
})
```

Output:

```
Level: 2
variable a is located at line 4, column 8
variable secondLevel is located at line 5, column 6

Level: 2
variable a is located at line 1, column 6
variable err is located at line 12, column 9

Level: 1
variable a is located at line 1, column 6
variable err is located at line 9, column 4
```

## Typings

```ts
export type Scope = Record<string, Node>
export interface HookContext extends WalkerContext {
  // inherited from estree-walker
  skip: () => void
  remove: () => void
  replace: (node: Node) => void

  // arguments of estree-walker hook
  parent: Node
  key: string
  index: number

  // scope info
  scope: Scope
  scopes: Scope[]
  level: number
}
```

## Sponsors

<p align="center">
  <a href="https://cdn.jsdelivr.net/gh/sxzz/sponsors/sponsors.svg">
    <img src='https://cdn.jsdelivr.net/gh/sxzz/sponsors/sponsors.svg'/>
  </a>
</p>

## Credits

- [@vue/reactivity-transform](https://github.com/vuejs/core/blob/v3.2.37/packages/reactivity-transform/src/reactivityTransform.ts) - almost copy-like referenced

## License

[MIT](./LICENSE) License © 2022-PRESENT [三咲智子](https://github.com/sxzz)

<!-- Badges -->

[npm-version-src]: https://img.shields.io/npm/v/ast-walker-scope.svg
[npm-version-href]: https://npmjs.com/package/ast-walker-scope
[npm-downloads-src]: https://img.shields.io/npm/dm/ast-walker-scope
[npm-downloads-href]: https://www.npmcharts.com/compare/ast-walker-scope?interval=30
[unit-test-src]: https://github.com/sxzz/ast-walker-scope/actions/workflows/unit-test.yml/badge.svg
[unit-test-href]: https://github.com/sxzz/ast-walker-scope/actions/workflows/unit-test.yml
