/**
 * SyntaxHighlighter
 * http://alexgorbatchev.com/SyntaxHighlighter
 *
 * SyntaxHighlighter is donationware. If you are using it, please donate.
 * http://alexgorbatchev.com/SyntaxHighlighter/donate.html
 *
 * @version
 * 3.0.83 (July 02 2010)
 * 
 * @copyright
 * Copyright (C) 2004-2010 Alex Gorbatchev.
 *
 * @license
 * Dual licensed under the MIT and GPL licenses.
 */
;(function()
{
	// CommonJS
	typeof(require) != 'undefined' ? SyntaxHighlighter = require('shCore').SyntaxHighlighter : null;

	function Brush()
	{
		var keywords =	'abs addr and ansichar ansistring array as asm begin boolean byte cardinal ' +
						'case char class comp const constructor currency destructor div do double ' +
						'downto else end except exports extended false file finalization finally ' +
						'for function goto if implementation in inherited int64 initialization ' +
						'integer interface is label library longint longword mod nil not object ' +
						'of on or packed pansichar pansistring pchar pcurrency pdatetime pextended ' +
						'pint64 pointer private procedure program property pshortstring pstring ' +
						'pvariant pwidechar pwidestring protected public published raise real real48 ' +
						'record repeat set shl shortint shortstring shr single smallint string then ' +
						'threadvar to true try type unit until uses val var varirnt while widechar ' +
						'widestring with word write writeln xor';

		this.regexList = [
			{ regex: /\(\*[\s\S]*?\*\)/gm,								css: 'comments' },  	// multiline comments (* *)
			{ regex: /{(?!\$)[\s\S]*?}/gm,								css: 'comments' },  	// multiline comments { }
			{ regex: SyntaxHighlighter.regexLib.singleLineCComments,	css: 'comments' },  	// one line
			{ regex: SyntaxHighlighter.regexLib.singleQuotedString,		css: 'string' },		// strings
			{ regex: /\{\$[a-zA-Z]+ .+\}/g,								css: 'color1' },		// compiler Directives and Region tags
			{ regex: /\b[\d\.]+\b/g,									css: 'value' },			// numbers 12345
			{ regex: /\$[a-zA-Z0-9]+\b/g,								css: 'value' },			// numbers $F5D3
			{ regex: new RegExp(this.getKeywords(keywords), 'gmi'),		css: 'keyword' }		// keyword
			];
	};

	Brush.prototype	= new SyntaxHighlighter.Highlighter();
	Brush.aliases	= ['delphi', 'pascal', 'pas'];

	SyntaxHighlighter.brushes.Delphi = Brush;

	// CommonJS
	typeof(exports) != 'undefined' ? exports.Brush = Brush : null;
})();
