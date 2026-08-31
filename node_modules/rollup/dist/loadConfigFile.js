/*
  @license
	Rollup.js v4.62.2
	Fri, 19 Jun 2026 14:55:11 GMT - commit 8faa18777374582bb813d54ce3623f4acf1f9e0b

	https://github.com/rollup/rollup

	Released under the MIT License.
*/
'use strict';

Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });

require('node:fs/promises');
require('node:path');
require('node:process');
require('node:url');
require('./shared/rollup.js');
require('./shared/parseAst.js');
const loadConfigFile_js = require('./shared/loadConfigFile.js');
require('path');
require('./native.js');
require('node:perf_hooks');
require('./getLogFilter.js');



exports.loadConfigFile = loadConfigFile_js.loadConfigFile;
//# sourceMappingURL=loadConfigFile.js.map
