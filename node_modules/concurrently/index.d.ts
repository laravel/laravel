/*
 * While in local development, make sure you've run `pnpm run build` first.
 */
import { concurrently } from './dist/src/index.js';

export * from './dist/src/index.js';
// @ts-expect-error ignore the usage of `export =` along with `export default`.
// This is seemingly fine, but the file needs to be included in the TS project, which can't be done
// due to importing from `dist`. See https://stackoverflow.com/q/42609768/2083599
export = concurrently;
export default concurrently;
