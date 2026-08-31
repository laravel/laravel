import { createRequire } from "node:module";
import _createJiti from "../dist/jiti.cjs";
// Static import so Bun bundles babel.cjs into compiled binaries
import _babelTransform from "../dist/babel.cjs";

function onError(err) {
  throw err; /* ↓ Check stack trace ↓ */
}

const nativeImport = (id) => import(id);

export function createJiti(id, opts = {}) {
  if (!opts.transform) {
    opts = { ...opts, transform: _babelTransform };
  }
  return _createJiti(id, opts, {
    onError,
    nativeImport,
    createRequire,
  });
}

export default createJiti;
