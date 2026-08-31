/*
 * While in local development, make sure you've run `pnpm run build` first.
 */
import { concurrently } from './dist/src/index.js';

export * from './dist/src/index.js';
export default concurrently;
