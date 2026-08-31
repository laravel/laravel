"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.assertDeprecated = assertDeprecated;
const deprecations = new Set();
/**
 * Asserts that some condition is true, and if not, prints a warning about it being deprecated.
 * The message is printed only once.
 */
function assertDeprecated(check, name, message) {
    if (!check && !deprecations.has(name)) {
        // eslint-disable-next-line no-console
        console.warn(`[concurrently] ${name} is deprecated. ${message}`);
        deprecations.add(name);
    }
}
