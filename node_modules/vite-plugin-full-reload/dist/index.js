// src/index.ts
import { relative, resolve } from "path";
import colors from "picocolors";
import picomatch from "picomatch";
import { normalizePath } from "vite";
function normalizePaths(root, path) {
  return (Array.isArray(path) ? path : [path]).map((path2) => resolve(root, path2)).map(normalizePath);
}
var src_default = (paths, config = {}) => ({
  name: "vite-plugin-full-reload",
  apply: "serve",
  // NOTE: Enable globbing so that Vite keeps track of the template files.
  config: () => ({ server: { watch: { disableGlobbing: false } } }),
  configureServer({ watcher, ws, config: { logger } }) {
    const { root = process.cwd(), log = true, always = true, delay = 0 } = config;
    const files = normalizePaths(root, paths);
    const shouldReload = picomatch(files);
    const checkReload = (path) => {
      if (shouldReload(path)) {
        setTimeout(() => ws.send({ type: "full-reload", path: always ? "*" : path }), delay);
        if (log)
          logger.info(`${colors.green("full reload")} ${colors.dim(relative(root, path))}`, { clear: true, timestamp: true });
      }
    };
    watcher.add(files);
    watcher.on("add", checkReload);
    watcher.on("change", checkReload);
  }
});
export {
  src_default as default,
  normalizePaths
};
