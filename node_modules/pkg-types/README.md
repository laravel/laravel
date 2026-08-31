# pkg-types

<!-- automd:badges color=yellow codecov -->

[![npm version](https://img.shields.io/npm/v/pkg-types?color=yellow)](https://npmjs.com/package/pkg-types)
[![npm downloads](https://img.shields.io/npm/dm/pkg-types?color=yellow)](https://npm.chart.dev/pkg-types)
[![codecov](https://img.shields.io/codecov/c/gh/unjs/pkg-types?color=yellow)](https://codecov.io/gh/unjs/pkg-types)

<!-- /automd -->

Node.js utilities and TypeScript definitions for `package.json`, `tsconfig.json`, and other configuration files.

## Install

<!-- automd:pm-i -->

```sh
# ✨ Auto-detect
npx nypm install pkg-types

# npm
npm install pkg-types

# yarn
yarn add pkg-types

# pnpm
pnpm add pkg-types

# bun
bun install pkg-types

# deno
deno install npm:pkg-types
```

<!-- /automd -->

## Usage

### Package Configuration

#### `readPackage`

Reads any package file format (package.json, package.json5, or package.yaml) with automatic format detection.

```js
import { readPackage } from "pkg-types";
const localPackage = await readPackage();
// or
const pkg = await readPackage("/fully/resolved/path/to/folder");
```

#### `writePackage`

Writes package data with format detection based on file extension.

```js
import { writePackage } from "pkg-types";

await writePackage("path/to/package.json", pkg);
await writePackage("path/to/package.json5", pkg);
await writePackage("path/to/package.yaml", pkg);
```

#### `findPackage`

Finds the nearest package file (package.json, package.json5, or package.yaml).

```js
import { findPackage } from "pkg-types";
const filename = await findPackage();
// or
const filename = await findPackage("/fully/resolved/path/to/folder");
```

#### `readPackageJSON`

```js
import { readPackageJSON } from "pkg-types";
const localPackageJson = await readPackageJSON();
// or
const packageJson = await readPackageJSON("/fully/resolved/path/to/folder");
```

#### `writePackageJSON`

```js
import { writePackageJSON } from "pkg-types";

await writePackageJSON("path/to/package.json", pkg);
```

#### `resolvePackageJSON`

```js
import { resolvePackageJSON } from "pkg-types";
const filename = await resolvePackageJSON();
// or
const packageJson = await resolvePackageJSON("/fully/resolved/path/to/folder");
```

#### `updatePackage`

Reads a package file and passes a proxied PackageJson to a callback (the callback may mutate it in-place or return a new object). The updated package is then written back using the same file format (`.json`/`.json5`/`.yaml`). The proxy auto-creates common map fields (e.g. `scripts`, `dependencies`) when accessed.

```js
import { updatePackage } from "pkg-types";

await updatePackage("path/to/package", (pkg) => {
  pkg.version = "1.0.1";
  pkg.dependencies.lodash = "^4.17.21";
});
```

#### `sortPackage`

Returns a new PackageJson that reorders known top-level fields according to the convention and alphabetically sorts certain nested maps (like `dependencies`, `devDependencies`, `optionalDependencies`, `peerDependencies` and `scripts`). Unknown top-level keys retain their original relative order. The input object is not mutated.

```js
import { sortPackage } from "pkg-types";

const sorted = sortPackage(pkg);
```

#### `normalizePackage`

Normalizes a `PackageJson` for stable output: sorts top-level fields and dependency maps, and removes dependency fields (`dependencies`, `devDependencies`, `optionalDependencies`, `peerDependencies`) if they are not plain objects. Returns a new normalized object.

```js
import { normalizePackage } from "pkg-types";

const normalized = normalizePackage(pkg);
```

### TypeScript Configuration

#### `readTSConfig`

```js
import { readTSConfig } from "pkg-types";
const tsconfig = await readTSConfig();
// or
const tsconfig2 = await readTSConfig("/fully/resolved/path/to/folder");
```

#### `writeTSConfig`

```js
import { writeTSConfig } from "pkg-types";

await writeTSConfig("path/to/tsconfig.json", tsconfig);
```

#### `resolveTSConfig`

```js
import { resolveTSConfig } from "pkg-types";
const filename = await resolveTSConfig();
// or
const tsconfig = await resolveTSConfig("/fully/resolved/path/to/folder");
```

### File Resolution

#### `findFile`

```js
import { findFile } from "pkg-types";
const filename = await findFile("README.md", {
  startingFrom: id,
  rootPattern: /^node_modules$/,
  test: (filename) => filename.endsWith(".md"),
});
```

#### `findNearestFile`

```js
import { findNearestFile } from "pkg-types";
const filename = await findNearestFile("package.json");
```

#### `findFarthestFile`

```js
import { findFarthestFile } from "pkg-types";
const filename = await findFarthestFile("package.json");
```

#### `resolveLockfile`

Find path to the lock file (`yarn.lock`, `package-lock.json`, `pnpm-lock.yaml`, `npm-shrinkwrap.json`, `bun.lockb`, `bun.lock`, `deno.lock`) or throws an error.

```js
import { resolveLockfile } from "pkg-types";
const lockfile = await resolveLockfile(".");
```

#### `findWorkspaceDir`

Try to detect workspace dir by in order:

1. Farthest workspace file (`pnpm-workspace.yaml`, `lerna.json`, `turbo.json`, `rush.json`, `deno.json`, `deno.jsonc`)
2. Closest `.git/config` file
3. Farthest lockfile
4. Farthest `package.json` file

If fails, throws an error.

```js
import { findWorkspaceDir } from "pkg-types";
const workspaceDir = await findWorkspaceDir(".");
```

### Git Configuration

#### `resolveGitConfig`

Finds closest `.git/config` file.

```js
import { resolveGitConfig } from "pkg-types";

const gitConfig = await resolveGitConfig(".");
```

#### `readGitConfig`

Finds and reads closest `.git/config` file into a JS object.

```js
import { readGitConfig } from "pkg-types";

const gitConfigObj = await readGitConfig(".");
```

#### `writeGitConfig`

Stringifies git config object into INI text format and writes it to a file.

```js
import { writeGitConfig } from "pkg-types";

await writeGitConfig(".git/config", gitConfigObj);
```

#### `parseGitConfig`

Parses a git config file in INI text format into a JavaScript object.

```js
import { parseGitConfig } from "pkg-types";

const gitConfigObj = parseGitConfig(gitConfigINI);
```

#### `stringifyGitConfig`

Stringifies a git config object into a git config file INI text format.

```js
import { stringifyGitConfig } from "pkg-types";

const gitConfigINI = stringifyGitConfig(gitConfigObj);
```

## Types

- **Note:** In order to make types work, you need to install `typescript` as a devDependency.

You can directly use typed interfaces:

```ts
import type { TSConfig, PackageJson, GitConfig } from "pkg-types";
```

### Define Utilities

You can use define utilities for type support and auto-completion when working in plain `.js` files. These functions simply return the input object but provide TypeScript type hints.

#### `definePackageJSON`

Provides type safety and auto-completion for package.json objects.

```js
import { definePackageJSON } from "pkg-types";

const pkg = definePackageJSON({
  name: "my-package",
  version: "1.0.0",
  // TypeScript will provide auto-completion here
});
```

#### `defineTSConfig`

Provides type safety and auto-completion for tsconfig.json objects.

```js
import { defineTSConfig } from "pkg-types";

const tsconfig = defineTSConfig({
  compilerOptions: {
    target: "ES2020",
    // TypeScript will provide auto-completion here
  },
});
```

#### `defineGitConfig`

Provides type safety and auto-completion for git config objects.

```js
import { defineGitConfig } from "pkg-types";

const gitConfig = defineGitConfig({
  user: {
    name: "John Doe",
    email: "john@example.com",
  },
  // TypeScript will provide auto-completion here
});
```

## Alternatives

- [dominikg/tsconfck](https://github.com/dominikg/tsconfck)

## License

<!-- automd:contributors license=MIT author="pi0,danielroe" -->

Published under the [MIT](https://github.com/unjs/pkg-types/blob/main/LICENSE) license.
Made by [@pi0](https://github.com/pi0), [@danielroe](https://github.com/danielroe) and [community](https://github.com/unjs/pkg-types/graphs/contributors) 💛
<br><br>
<a href="https://github.com/unjs/pkg-types/graphs/contributors">
<img src="https://contrib.rocks/image?repo=unjs/pkg-types" />
</a>

<!-- /automd -->
