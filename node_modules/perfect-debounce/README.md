# perfect-debounce

<!-- automd:badges color=yellow codecov bundlephobia packagephobia  -->

[![npm version](https://img.shields.io/npm/v/perfect-debounce?color=yellow)](https://npmjs.com/package/perfect-debounce)
[![npm downloads](https://img.shields.io/npm/dm/perfect-debounce?color=yellow)](https://npm.chart.dev/perfect-debounce)
[![bundle size](https://img.shields.io/bundlephobia/minzip/perfect-debounce?color=yellow)](https://bundlephobia.com/package/perfect-debounce)
[![install size](https://badgen.net/packagephobia/install/perfect-debounce?color=yellow)](https://packagephobia.com/result?p=perfect-debounce)
[![codecov](https://img.shields.io/codecov/c/gh/unjs/perfect-debounce?color=yellow)](https://codecov.io/gh/unjs/perfect-debounce)

<!-- /automd -->

Improved debounce function with Promise support.

## Features

- Well tested debounce implementation
- Native Promise support
- Avoid duplicate calls while promise is being resolved
- Configurable `trailing` and `leading` behavior
- Control methods

## Usage

Install package:

```sh
npx nypm i perfect-debounce
```

Import:

```js
import { debounce } from "perfect-debounce";
```

Debounce function:

```js
const debounced = debounce(async () => {
  // Some heavy stuff
}, 25);
```

When calling `debounced`, it will wait at least for `25ms` as configured before actually calling your function. This helps to avoid multiple calls.

### Control Methods

The returned debounced function provides additional control methods:

- `debounced.cancel()`: Cancel any pending invocation that has not yet occurred.
- `await debounced.flush()`: Immediately invoke the pending function call (if any) and return its result.
- `debounced.isPending()`: Returns `true` if there is a pending invocation waiting to be called, otherwise `false`.

```js
debounced.cancel(); // Cancel any pending call
await debounced.flush(); // Immediately invoke pending call (if any)
debounced.isPending(); // Returns true if a call is pending
```

### Example

```js
const debounced = debounce(async (value) => {
  // Some async work
  return value * 2;
}, 100);

debounced(1);
debounced(2);
debounced(3);

// Check if a call is pending
console.log(debounced.isPending()); // true

// Immediately invoke the pending call
const result = await debounced.flush();
console.log(result); // 6

// Cancel any further pending calls
debounced.cancel();
```

To avoid initial wait, we can set `leading: true` option. It will cause function to be immediately called if there is no other call:

```js
const debounced = debounce(
  async () => {
    // Some heavy stuff
  },
  25,
  { leading: true },
);
```

If executing async function takes longer than debounce value, duplicate calls will be still prevented a last call will happen. To disable this behavior, we can set `trailing: false` option:

```js
const debounced = debounce(
  async () => {
    // Some heavy stuff
  },
  25,
  { trailing: false },
);
```

## 💻 Development

- Clone this repository
- Enable [Corepack](https://github.com/nodejs/corepack) using `corepack enable` (use `npm i -g corepack` for Node.js < 16.10)
- Install dependencies using `pnpm install`
- Run interactive tests using `pnpm dev`

## License

Based on [sindresorhus/p-debounce](https://github.com/sindresorhus/p-debounce).

Made with 💛 Published under [MIT License](./LICENSE).
