/*
 * While in local development, make sure you've run `pnpm run build` first.
 */

// eslint-disable-next-line @typescript-eslint/no-require-imports
const concurrently = require('./dist/src/index.js');

// For require()
module.exports = exports = concurrently.concurrently;

// For TS + import syntax; mimics `export default`
exports.default = exports;

Object.assign(exports, concurrently);
