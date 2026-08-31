'use strict';

/**
 * @import {
 * 	ControlOperator,
 * 	Env,
 * 	GlobPattern,
 * 	ParseEntry,
 * } from './parse' */

// '<(' is process substitution operator and
// can be parsed the same as control operator
var CONTROL = /** @type {const} */ ('(?:') + /** @type {const} */ ([
	'\\|\\|',
	'\\&\\&',
	';;',
	'\\|\\&',
	'\\<\\(',
	'\\<\\<\\<',
	'>>',
	'>\\&',
	'<\\&',
	'[&;()|<>]'
]).join(/** @type {const} */ ('|')) + /** @type {const} */ (')');
var controlRE = new RegExp('^' + CONTROL + '$');
var META = /** @type {const} */ ('|&;()<> \\t');
var SINGLE_QUOTE = /** @type {const} */ ('"((\\\\"|[^"])*?)"');
var DOUBLE_QUOTE = /** @type {const} */ ('\'((\\\\\'|[^\'])*?)\'');
var hash = /^#$/;

var SQ = /** @type {const} */ ("'");
var DQ = /** @type {const} */ ('"');
var DS = /** @type {const} */ ('$');

var TOKEN = '';
var mult = /** @type {const} */ (0x100000000); // Math.pow(16, 8);
for (var i = 0; i < 4; i++) {
	TOKEN += (mult * Math.random()).toString(16);
}
var startsWithToken = new RegExp('^' + TOKEN);

/**
 * @param {string} s
 * @param {RegExp} r
 */
function matchAll(s, r) {
	var origIndex = r.lastIndex;

	var matches = [];
	var matchObj;

	while ((matchObj = r.exec(s))) {
		matches[matches.length] = matchObj;
		if (r.lastIndex === matchObj.index) {
			r.lastIndex += 1;
		}
	}

	r.lastIndex = origIndex;

	return matches;
}

/**
 * @param {Env} env
 * @param {string} pre
 * @param {string} key
 */
function getVar(env, pre, key) {
	var r = typeof env === 'function' ? env(key) : env[key];
	if (typeof r === 'undefined' && key != '') {
		r = '';
	} else if (typeof r === 'undefined') {
		r = '$';
	}

	if (typeof r === 'object') {
		return pre + TOKEN + JSON.stringify(r) + TOKEN;
	}
	return pre + r;
}

/**
 * @param {string} string
 * @param {Env} [env]
 * @param {{ escape?: string }} [opts]
 * @returns {ParseEntry[]}
 */
function parseInternal(string, env, opts) {
	if (!opts) {
		opts = {};
	}
	var BS = opts.escape || '\\';
	var BAREWORD = '(\\' + BS + '[\'"' + META + ']|[^\\s\'"' + META + '])+';

	var chunker = new RegExp([
		'(' + CONTROL + ')', // control chars
		'(' + BAREWORD + '|' + SINGLE_QUOTE + '|' + DOUBLE_QUOTE + ')+'
	].join('|'), 'g');

	var matches = matchAll(string, chunker);

	if (matches.length === 0) {
		return [];
	}
	if (!env) {
		env = {};
	}

	var commented = false;

	return matches.map(function (match) {
		var s = match[0];
		if (!s || commented) {
			return void undefined;
		}
		if (controlRE.test(s)) {
			return /** @type {ControlOperator} */ ({ op: s });
		}

		// Hand-written scanner/parser for Bash quoting rules:
		//
		// 1. inside single quotes, all characters are printed literally.
		// 2. inside double quotes, all characters are printed literally
		//    except variables prefixed by '$' and backslashes followed by
		//    either a double quote or another backslash.
		// 3. outside of any quotes, backslashes are treated as escape
		//    characters and not printed (unless they are themselves escaped)
		// 4. quote context can switch mid-token if there is no whitespace
		//     between the two quote contexts (e.g. all'one'"token" parses as
		//     "allonetoken")
		/** @type {string | boolean} */
		var quote = false;
		var esc = false;
		var out = '';
		var isGlob = false;
		/** @type {number} */
		var i;

		function parseEnvVar() {
			i += 1;
			/** @type {number | RegExpMatchArray | null} */
			var varend;
			/** @type {string} */
			var varname;
			var char = s.charAt(i);

			if (char === '{') {
				i += 1;
				if (s.charAt(i) === '}') {
					throw new Error('Bad substitution: ' + s.slice(i - 2, i + 1));
				}
				varend = s.indexOf('}', i);
				if (varend < 0) {
					throw new Error('Bad substitution: ' + s.slice(i));
				}
				varname = s.slice(i, varend);
				i = varend;
			} else if ((/[*@#?$!_-]/).test(char)) {
				varname = char;
				i += 1;
			} else {
				var slicedFromI = s.slice(i);
				varend = slicedFromI.match(/[^\w\d_]/);
				if (!varend) {
					varname = slicedFromI;
					i = s.length;
				} else {
					varname = slicedFromI.slice(0, varend.index);
					i += /** @type {number} */ (varend.index) - 1;
				}
			}
			return getVar(/** @type {NonNullable<typeof env>} */ (env), '', varname);
		}

		for (i = 0; i < s.length; i++) {
			var c = s.charAt(i);
			isGlob = isGlob || (!quote && (c === '*' || c === '?'));
			if (esc) {
				out += c;
				esc = false;
			} else if (quote) {
				if (c === quote) {
					quote = false;
				} else if (quote == SQ) {
					out += c;
				} else { // Double quote
					if (c === BS) {
						i += 1;
						c = s.charAt(i);
						if (c === DQ || c === BS || c === DS) {
							out += c;
						} else {
							out += BS + c;
						}
					} else if (c === DS) {
						out += parseEnvVar();
					} else {
						out += c;
					}
				}
			} else if (c === DQ || c === SQ) {
				quote = c;
			} else if (controlRE.test(c)) {
				return /** @type {ControlOperator} */ ({ op: s });
			} else if (hash.test(c)) {
				commented = true;
				var commentObj = { comment: string.slice(match.index + i + 1) };
				if (out.length) {
					return /** @type {const} */ ([out, commentObj]);
				}
				return /** @type {const} */ ([commentObj]);
			} else if (c === BS) {
				esc = true;
			} else if (c === DS) {
				out += parseEnvVar();
			} else {
				out += c;
			}
		}

		if (isGlob) {
			return /** @type {GlobPattern} */ ({ op: 'glob', pattern: out });
		}

		return out;
	}).reduce(function (prev, arg) { // finalize parsed arguments
		if (typeof arg === 'undefined') {
			return prev;
		}
		/** @type {ParseEntry[]} */ ([]).concat(arg).forEach(function (entry) {
			prev[prev.length] = entry;
		});
		return prev;
	}, /** @type {ParseEntry[]} */ ([]));
}

/** @type {import('./parse')} */
module.exports = function parse(s, env, opts) {
	var mapped = parseInternal(s, env, opts);
	if (typeof env !== 'function') {
		return mapped;
	}
	return mapped.reduce(function (acc, s) {
		if (typeof s === 'object') {
			acc[acc.length] = s;
			return acc;
		}
		var xs = s.split(RegExp('(' + TOKEN + '.*?' + TOKEN + ')', 'g'));
		if (xs.length === 1) {
			acc[acc.length] = xs[0];
			return acc;
		}
		xs.filter(Boolean).forEach(function (x) {
			acc[acc.length] = startsWithToken.test(x)
				? JSON.parse(x.split(TOKEN)[1])
				: x;
		});
		return acc;
	}, /** @type {ParseEntry[]} */ ([]));
};
