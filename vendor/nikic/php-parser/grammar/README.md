What do all those files mean?
=============================

 * `zend_language_parser.phpy`: PHP grammer written in a pseudo language
 * `analyze.php`:               Analyzes the `.phpy`-grammer and outputs some info about it
 * `rebuildParser.php`:         Preprocesses the `.phpy`-grammar and builds the parser using `kmyacc`
 * `kmyacc.php.parser`:         A `kmyacc` parser prototype file for PHP

.phpy pseudo language
=====================

The `.phpy` file is a normal grammer in `kmyacc` (`yacc`) style, with some transformations
applied to it:

 * Nodes are created using the syntax `Name[..., ...]`. This is transformed into
   `new PHPParser_Node_Name(..., ..., $attributes)`
 * `Name::abc` is transformed to `PHPParser_Node_Name::abc`
 * Some function-like constructs are resolved (see `rebuildParser.php` for a list)
 * Associative arrays are written as `[key: value, ...]`, which is transformed to
   `array('key' => value, ...)`

Building the parser
===================

In order to rebuild the parser, you need [moriyoshi's fork of kmyacc](https://github.com/moriyoshi/kmyacc-forked).
After you compiled/installed it, run the `rebuildParser.php` script.

By default only the `Parser.php` is built. If you want to additionally build `Parser/Debug.php` and `y.output` run the
script with `--debug`. If you want to retain the preprocessed grammar pass `--keep-tmp-grammar`.