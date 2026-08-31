'use strict';

var test = require('tape');
var parse = require('../').parse;

test('parse shell commands', function (t) {
	t.same(parse(''), [], 'parses an empty string');

	t['throws'](
		function () { parse('${}'); },
		Error,
		'empty substitution throws'
	);
	t['throws'](
		function () { parse('${'); },
		Error,
		'incomplete substitution throws'
	);

	t.same(parse('a \'b\' "c"'), ['a', 'b', 'c']);
	t.same(
		parse('beep "boop" \'foo bar baz\' "it\'s \\"so\\" groovy"'),
		['beep', 'boop', 'foo bar baz', 'it\'s "so" groovy']
	);
	t.same(parse('a b\\ c d'), ['a', 'b c', 'd']);
	t.same(parse('\\$beep bo\\`op'), ['$beep', 'bo`op']);
	t.same(parse('echo "foo = \\"foo\\""'), ['echo', 'foo = "foo"']);
	t.same(parse(''), []);
	t.same(parse(' '), []);
	t.same(parse('\t'), []);
	t.same(parse('a"b c d"e'), ['ab c de']);
	t.same(parse('a\\ b"c d"\\ e f'), ['a bc d e', 'f']);
	t.same(parse('a\\ b"c d"\\ e\'f g\' h'), ['a bc d ef g', 'h']);
	t.same(parse("x \"bl'a\"'h'"), ['x', "bl'ah"]);
	t.same(parse("x bl^'a^'h'", {}, { escape: '^' }), ['x', "bl'a'h"]);
	t.same(parse('abcH def', {}, { escape: 'H' }), ['abc def']);

	t.deepEqual(parse('# abc  def  ghi'), [{ comment: ' abc  def  ghi' }], 'start-of-line comment content is unparsed');
	t.deepEqual(parse('xyz # abc  def  ghi'), ['xyz', { comment: ' abc  def  ghi' }], 'comment content is unparsed');

	t.deepEqual(parse('-x "" -y'), ['-x', '', '-y'], 'empty string is preserved');

	t.same(
		parse('2;b', {}, { escape: 'd' }),
		[{ op: '2;b' }],
		'control char in unquoted context mid-token with regex-special escape returns op'
	);

	t.end();
});

test('parse stays linear in token count (GHSA-395f-4hp3-45gv)', function (t) {
	// the old concat-in-reduce finalizer was O(n^2): this many tokens took
	// ~minutes, so under the unfixed code this test hangs rather than passes
	var n = 2e5;
	var input = new Array(n + 1).join('x '); // avoid String#repeat for old engines

	var words = parse(input);
	t.equal(words.length, n, 'every token is returned');
	t.equal(words[0], 'x', 'first token is correct');
	t.equal(words[n - 1], 'x', 'last token is correct');

	var withEnv = parse(input, function () { return 'v'; });
	t.equal(withEnv.length, n, 'env-function path returns every token');

	t.end();
});
