# unplugin-utils

[![npm version][npm-version-src]][npm-version-href]
[![npm downloads][npm-downloads-src]][npm-downloads-href]
[![Unit Test][unit-test-src]][unit-test-href]
[![codecov][codecov-src]][codecov-href]

A set of utility functions commonly used by unplugins.

Thanks to [@rollup/pluginutils](https://github.com/rollup/plugins/tree/master/packages/pluginutils). This projects is heavily copied from it.

## Why Fork?

- 🌍 Platform agnostic, supports running in the browser, Node.js...
- ✂️ Subset, smaller bundle size.
- **💯 Coverage**: 100% test coverage.

## Install

```bash
npm i unplugin-utils
```

## Usage

### createFilter

```ts
export default function myPlugin(options = {}) {
  const filter = createFilter(options.include, options.exclude)

  return {
    transform(code, id) {
      if (!filter(id)) return

      // proceed with the transformation...
    },
  }
}
```

### normalizePath

```ts
import { normalizePath } from 'unplugin-utils'

normalizePath(String.raw`foo\bar`) // 'foo/bar'
normalizePath('foo/bar') // 'foo/bar'
```

## Sponsors

<p align="center">
  <a href="https://cdn.jsdelivr.net/gh/sxzz/sponsors/sponsors.svg">
    <img src='https://cdn.jsdelivr.net/gh/sxzz/sponsors/sponsors.svg'/>
  </a>
</p>

## License

[MIT](./LICENSE) License © 2025-PRESENT [Kevin Deng](https://github.com/sxzz)

[MIT](./LICENSE) Copyright (c) 2019 RollupJS Plugin Contributors (https://github.com/rollup/plugins/graphs/contributors)

<!-- Badges -->

[npm-version-src]: https://img.shields.io/npm/v/unplugin-utils.svg
[npm-version-href]: https://npmjs.com/package/unplugin-utils
[npm-downloads-src]: https://img.shields.io/npm/dm/unplugin-utils
[npm-downloads-href]: https://www.npmcharts.com/compare/unplugin-utils?interval=30
[unit-test-src]: https://github.com/sxzz/unplugin-utils/actions/workflows/unit-test.yml/badge.svg
[unit-test-href]: https://github.com/sxzz/unplugin-utils/actions/workflows/unit-test.yml
[codecov-src]: https://codecov.io/gh/sxzz/unplugin-utils/graph/badge.svg?token=VDWXCPSL1O
[codecov-href]: https://codecov.io/gh/sxzz/unplugin-utils
