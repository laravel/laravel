'use strict';

var test = require('tape');
var quote = require('../').quote;

test('quote', function (t) {
	t.equal(quote(['a', 'b', 'c d']), 'a b \'c d\'');
	t.equal(
		quote(['a', 'b', "it's a \"neat thing\""]),
		'a b "it\'s a \\"neat thing\\""'
	);
	t.equal(
		quote(['$', '`', '\'']),
		'\\$ \\` "\'"'
	);
	t.equal(quote([]), '');
	t.equal(quote(['a\nb']), "'a\nb'");
	t.equal(quote([' #(){}*|][!']), "' #(){}*|][!'");
	t.equal(quote(["'#(){}*|][!"]), '"\'#(){}*|][\\!"');
	t.equal(quote(['X#(){}*|][!']), 'X\\#\\(\\)\\{\\}\\*\\|\\]\\[\\!');
	t.equal(quote(['a\n#\nb']), "'a\n#\nb'");
	t.equal(quote(['><;{}']), '\\>\\<\\;\\{\\}');
	t.equal(quote(['a', 1, true, false]), 'a 1 true false');
	t.equal(quote(['a', 1, null, undefined]), 'a 1 null undefined');
	t.equal(quote(['a\\x']), "'a\\x'");
	t.equal(quote(['a"b']), '\'a"b\'');
	t.equal(quote(['"a"b"']), '\'"a"b"\'');
	t.equal(quote(['a\\"b']), '\'a\\"b\'');
	t.equal(quote(['a\\b']), '\'a\\b\'');
	t.end();
});

test('quote tilde (escapes leading ~ to prevent shell tilde-expansion)', function (t) {
	t.equal(quote(['~']), '\\~');
	t.equal(quote(['~/foo']), '\\~/foo');
	t.equal(quote(['~root']), '\\~root');
	t.equal(quote(['~root/x']), '\\~root/x');
	t.equal(quote(['~+']), '\\~+');
	t.equal(quote(['~-']), '\\~-');
	t.equal(quote(['a~b']), 'a\\~b');
	t.equal(quote(['x~']), 'x\\~');
	t.end();
});

test('quote ops', function (t) {
	t.equal(quote(['a', { op: '|' }, 'b']), 'a \\| b');
	t.equal(
		quote(['a', { op: '&&' }, 'b', { op: ';' }, 'c']),
		'a \\&\\& b \\; c'
	);
	t.end();
});

test('quote windows paths', { skip: 'breaking change, disabled until 2.x' }, function (t) {
	var path = 'C:\\projects\\node-shell-quote\\index.js';

	t.equal(quote([path, 'b', 'c d']), 'C:\\projects\\node-shell-quote\\index.js b \'c d\'');

	t.end();
});

test("chars for windows paths don't break out", function (t) {
	var x = '`:\\a\\b';
	t.equal(quote([x]), "'`:\\a\\b'");
	t.end();
});

test('empty strings', function (t) {
	t.equal(quote(['-x', '', 'y']), '-x \'\' y');

	t.end();
});

test('quote ops: allowlist', function (t) {
	var ops = ['||', '&&', ';;', '|&', '<(', '<<<', '>>', '>&', '<&', '&', ';', '(', ')', '|', '<', '>'];
	for (var i = 0; i < ops.length; i++) {
		var op = ops[i];
		var expected = '';
		for (var j = 0; j < op.length; j++) { expected += '\\' + op.charAt(j); }
		t.equal(quote([{ op: op }]), expected, 'op ' + op);
	}
	t.end();
});

test('quote ops: rejects line terminators (GHSA-w7jw-789q-3m8p)', function (t) {
	t['throws'](function () { quote([{ op: ';\nid' }]); }, TypeError, 'newline in op');
	t['throws'](function () { quote([{ op: ';\rid' }]); }, TypeError, 'carriage return in op');
	t['throws'](function () { quote([{ op: ';\u2028id' }]); }, TypeError, 'U+2028 in op');
	t['throws'](function () { quote([{ op: ';\u2029id' }]); }, TypeError, 'U+2029 in op');
	t.end();
});

test('quote ops: rejects non-allowlisted values', function (t) {
	t['throws'](function () { quote([{ op: '' }]); }, TypeError, 'empty op');
	t['throws'](function () { quote([{ op: 'foo' }]); }, TypeError, 'arbitrary string');
	t['throws'](function () { quote([{ op: '|||' }]); }, TypeError, 'near-miss');
	t['throws'](function () { quote([{ op: 42 }]); }, TypeError, 'non-string op');
	t.end();
});

test('quote glob pattern', function (t) {
	t.equal(quote([{ op: 'glob', pattern: 'test/*.test.js' }]), 'test/*.test.js');
	t.equal(quote([{ op: 'glob', pattern: '?ab' }]), '?ab');
	t.equal(quote([{ op: 'glob', pattern: '[ab]c' }]), '[ab]c');
	t.equal(quote([{ op: 'glob', pattern: '{a,b}' }]), '{a,b}');
	t.equal(quote([{ op: 'glob', pattern: 'my dir/*.txt' }]), 'my\\ dir/*.txt');
	t.equal(quote([{ op: 'glob', pattern: 'a$b' }]), 'a\\$b');
	t['throws'](function () { quote([{ op: 'glob' }]); }, TypeError, 'missing pattern');
	t['throws'](function () { quote([{ op: 'glob', pattern: 'a\nb' }]); }, TypeError, 'newline in pattern');
	t['throws'](function () { quote([{ op: 'glob', pattern: 'a\u2028b' }]); }, TypeError, 'U+2028 in pattern');
	t.end();
});

test('quote comment', function (t) {
	t.equal(quote(['echo', 'hi', { comment: ' a comment' }]), 'echo hi # a comment');
	t.equal(quote([{ comment: '' }]), '#');
	t['throws'](function () { quote([{ comment: 'a\nb' }]); }, TypeError, 'newline in comment');
	t['throws'](function () { quote([{ comment: 'a\rb' }]); }, TypeError, 'CR in comment');
	t['throws'](function () { quote([{ comment: 'a\u2028b' }]); }, TypeError, 'U+2028 in comment');
	t.end();
});

test('quote rejects unrecognized object shapes', function (t) {
	t['throws'](function () { quote([{}]); }, TypeError, 'empty object');
	t['throws'](function () { quote([{ foo: 'bar' }]); }, TypeError, 'unknown key');
	t['throws'](function () { quote([{ op: null }]); }, TypeError, 'null op');
	t.end();
});
