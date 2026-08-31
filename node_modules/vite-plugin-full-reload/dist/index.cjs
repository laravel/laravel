"use strict";Object.defineProperty(exports, "__esModule", {value: true}); function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }// src/index.ts
var _path = require('path');
var _picocolors = require('picocolors'); var _picocolors2 = _interopRequireDefault(_picocolors);
var _picomatch = require('picomatch'); var _picomatch2 = _interopRequireDefault(_picomatch);
var _vite = require('vite');
function normalizePaths(root, path) {
  return (Array.isArray(path) ? path : [path]).map((path2) => _path.resolve.call(void 0, root, path2)).map(_vite.normalizePath);
}
var src_default = (paths, config = {}) => ({
  name: "vite-plugin-full-reload",
  apply: "serve",
  // NOTE: Enable globbing so that Vite keeps track of the template files.
  config: () => ({ server: { watch: { disableGlobbing: false } } }),
  configureServer({ watcher, ws, config: { logger } }) {
    const { root = process.cwd(), log = true, always = true, delay = 0 } = config;
    const files = normalizePaths(root, paths);
    const shouldReload = _picomatch2.default.call(void 0, files);
    const checkReload = (path) => {
      if (shouldReload(path)) {
        setTimeout(() => ws.send({ type: "full-reload", path: always ? "*" : path }), delay);
        if (log)
          logger.info(`${_picocolors2.default.green("full reload")} ${_picocolors2.default.dim(_path.relative.call(void 0, root, path))}`, { clear: true, timestamp: true });
      }
    };
    watcher.add(files);
    watcher.on("add", checkReload);
    watcher.on("change", checkReload);
  }
});



exports.default = src_default; exports.normalizePaths = normalizePaths;
