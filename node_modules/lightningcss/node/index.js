let parts = [process.platform, process.arch];
if (process.platform === 'linux') {
  const { MUSL, familySync } = require('detect-libc');
  const family = familySync();
  if (family === MUSL) {
    parts.push('musl');
  } else if (process.arch === 'arm') {
    parts.push('gnueabihf');
  } else {
    parts.push('gnu');
  }
} else if (process.platform === 'win32') {
  parts.push('msvc');
}

let native;
try {
  native = require(`lightningcss-${parts.join('-')}`);
} catch (err) {
  native = require(`../lightningcss.${parts.join('-')}.node`);
}

module.exports.transform = wrap(native.transform);
module.exports.transformStyleAttribute = wrap(native.transformStyleAttribute);
module.exports.bundle = wrap(native.bundle);
module.exports.bundleAsync = wrap(native.bundleAsync);
module.exports.browserslistToTargets = require('./browserslistToTargets');
module.exports.composeVisitors = require('./composeVisitors');
module.exports.Features = require('./flags').Features;

function wrap(call) {
  return (options) => {
    if (typeof options.visitor === 'function') {
      let deps = [];
      options.visitor = options.visitor({
        addDependency(dep) {
          deps.push(dep);
        }
      });

      let result = call(options);
      if (result instanceof Promise) {
        result = result.then(res => {
          if (deps.length) {
            res.dependencies ??= [];
            res.dependencies.push(...deps);
          }
          return res;
        });
      } else if (deps.length) {
        result.dependencies ??= [];
        result.dependencies.push(...deps);
      }
      return result;
    } else {
      return call(options);
    }
  };
}
