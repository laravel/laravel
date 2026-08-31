/*
	MIT License http://www.opensource.org/licenses/mit-license.php
*/

"use strict";

// Browser stub for the default `graceful-fs` file system. A browser has no
// ambient file system, so the convenience entry's default resolver has nothing
// to read — pass your own `fileSystem` to `create()` /
// `ResolverFactory.createResolver()` instead. Each method throws a clear error
// if the default is actually used, while keeping the package able to bundle for
// the browser (no Node `fs` dependency is pulled in).

const message =
	"enhanced-resolve: no default file system is available in the browser. " +
	"Pass a `fileSystem` to `create()` or `ResolverFactory.createResolver()`.";

/**
 * @returns {never} always throws
 */
const unavailable = () => {
	throw new Error(message);
};

// The same set of methods `CachedInputFileSystem` reads from the file system
// (graceful-fs has no `readJson`/`readJsonSync`, so those stay undefined).
module.exports = {
	lstat: unavailable,
	lstatSync: unavailable,
	readFile: unavailable,
	readFileSync: unavailable,
	readdir: unavailable,
	readdirSync: unavailable,
	readlink: unavailable,
	readlinkSync: unavailable,
	realpath: unavailable,
	realpathSync: unavailable,
	stat: unavailable,
	statSync: unavailable,
};
