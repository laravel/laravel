/*
	MIT License http://www.opensource.org/licenses/mit-license.php
*/

"use strict";

// Browser shim for the Node `url` builtin, providing the `fileURLToPath` the
// resolver uses. Node, Deno and Bun use the native `url` (so they get the fully
// correct implementation); only browser bundles use this, wired in through the
// package `browser` field. The implementation is a URL-based port — see
// ./fileURLToPath.
module.exports = {
	fileURLToPath: require("./fileURLToPath"),
};
