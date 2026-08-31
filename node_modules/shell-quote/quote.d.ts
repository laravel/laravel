import parse = require('./parse');

/**
 * Quotes an array of tokens into a shell-safe string.
 *
 * Accepts strings and the object shapes that {@link parse} emits. Throws a
 * `TypeError` for unrecognized object shapes, `op` values outside the
 * allowlist, or `pattern`/`comment` values containing line terminators.
 *
 * @param args - Array of tokens to quote.
 * @returns A shell-safe quoted string.
 */
declare function quote(args: readonly parse.ParseEntry[]): string;

export = quote;
