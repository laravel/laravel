Version 0.9.5 (23.07.2014)
--------------------------

**This is the last release on the 0.9 branch.**

* Add `NodeTraverser::removeVisitor()` method, which removes a visitor from the node traverser. The method was not added
  to the corresponding `NodeTraverserInterface` to avoid BC breaks with custom traversers (it is added in version 1.0).

* Deprecated `PHPParser_Template` and `PHPParser_TemplateLoader`. This functionality does not belong in the main project
  and - as far as I know - nobody is using it.

* Fix alias resolution in `NameResolver`: Class names are now correctly handled as case-insensitive.

* The undefined variable error, which is used in the lexer to reset the error state, will no longer interfere with
  custom error handlers.

* Make lexer compatible with `xdebug.scream`.

Version 0.9.4 (25.08.2013)
--------------------------
* [PHP 5.5] Add support for `ClassName::class`. This is parsed as an `Expr_ClassConstFetch` with `'class'` being the
  constant name.

* Syntax errors now include information on expected tokens and mimic the format of PHP's own (pre 5.4) error messages.
  Example:

        Old: Unexpected token T_STATIC on line 1
        New: Syntax error, unexpected T_STATIC, expecting T_STRING or T_NS_SEPARATOR or '{'

* `PHPParser_PrettyPrinter_Zend` was renamed to `PHPParser_PrettyPrinter_Default` as the default pretty printer only
  very loosely applies the Zend Coding Standard. The class `PHPParser_PrettyPrinter_Zend` extends
  `PHPParser_PrettyPrinter_Default` to maintain backwards compatibility.

* The pretty printer now prints namespaces in semicolon-style if possible (i.e. if the file does not contain a global
  namespace declaration).

* Added `prettyPrintFile(array $stmts)` method which will pretty print a file of statements including the opening
  `<?php` tag if it is required. Use of this method will also eliminate the unnecessary `<?php ?>` at the start and end
  of files using inline HTML.

* There now is a builder for interfaces (`PHPParser_Builder_Interface`).

* An interface for the node traversation has been added: `PHPParser_NodeTraverserInterface`

* Fix pretty printing of `include` expressions (precedence information was missing).

* Fix "undefined index" notices when generating the expected tokens for a syntax error.

* Improve performance of `PrettyPrinter` construction by no longer using the `uniqid()` function.

Version 0.9.3 (22.11.2012)
--------------------------

* [BC] As `list()` in `foreach` is now supported the structure of list assignments changed:

   1. There is no longer a dedicated `AssignList` node; instead a normal `Assign` node is used with a `List` as  `var`.
   2. Nested lists are now `List` nodes too, instead of just arrays.

* [BC] As arbitrary expressions are allowed in `empty()` now its subnode was renamed from `var` to `expr`.

* [BC] The protected `pSafe()` method in `PrettyPrinterAbstract` was renamed to `pNoIndent()`.

* [PHP 5.5] Add support for arbitrary expressions in `empty()`.

* [PHP 5.5] Add support for constant array / string dereferencing.
  Examples: `"foo"[2]`, `[1, 2, 3][2]`

* [PHP 5.5] Add support for `yield` expressions. This adds a new `Yield` expression type, with subnodes `key` and
  `value`.

* [PHP 5.5] Add support for `finally`. This adds a new `finallyStmts` subnode to the `TryCatch` node. If there is no
  finally clause it will be `null`.

* [PHP 5.5] Add support for `list()` destructuring of `foreach` values.
  Example: `foreach ($coords as list($x, $y)) { ... }`

* Improve pretty printing of expressions by printing less unnecessary parentheses. In particular concatenations are now
  printed as `$a . $b . $c . $d . $e` rather than `$a . ($b . ($c . ($d . $e)))`. This is implemented by taking operator
  associativity into account. New protected methods added to the pretty printer are `pPrec()`, `pInfixOp()`,
  `pPrefixOp()` and `pPostfixOp()`. This also fixes an issue with extraneous parentheses in closure bodies.

* Fix formatting of fall-through `case` statements in the Zend pretty printer.

* Fix parsing of `$foo =& new Bar`. It is now properly parsed as `AssignRef` (instead of `Assign`).

* Fix assignment of `$endAttributes`. Sometimes the attributes of the token right after the node were assigned, rather
  than the attributes of the last token in the node.

* `rebuildParser.php` is now designed to be run from the command line rather than from the browser.

Version 0.9.2 (07.07.2012)
--------------------------

* Add `Class->getMethods()` function, which returns all methods contained in the `stmts` array of the class node. This
  does not take inherited methods into account.

* Add `isPublic()`, `isProtected()`, `isPrivate()`. `isAbstract()`, `isFinal()` and `isStatic()` accessors to the
  `ClassMethod`, `Property` and `Class` nodes. (`Property` and `Class` obviously only have the accessors relevant to
  them.)

* Fix parsing of new expressions in parentheses, e.g. `return(new Foo);`.

* [BC] Due to the below changes nodes now optionally accept an `$attributes` array as the
  last parameter, instead of the previously used `$line` and `$docComment` parameters.

* Add mechanism for adding attributes to nodes in the lexer.

  The following attributes are now added by default:

   * `startLine`: The line the node started in.
   * `endLine`: The line the node ended in.
   * `comments`: An array of comments. The comments are instances of `PHPParser_Comment`
     (or `PHPParser_Comment_Doc` for doc comments).

  The methods `getLine()` and `setLine()` still exist and function as before, but internally
  operator on the `startLine` attribute.

  `getDocComment()` also continues to exist. It returns the last comment in the `comments`
  attribute if it is a doc comment, otherwise `null`. As `getDocComment()` now returns a
  comment object (which can be modified using `->setText()`) the `setDocComment()` method was
  removed. Comment objects implement a `__toString()` method, so `getDocComment()` should
  continue to work properly with old code.

* [BC] Use inject-once approach for lexer:

  Now the lexer is injected only once when creating the parser. Instead of

        $parser = new PHPParser_Parser;
        $parser->parse(new PHPParser_Lexer($code));
        $parser->parse(new PHPParser_Lexer($code2));

  you write:

        $parser = new PHPParser_Parser(new PHPParser_Lexer);
        $parser->parse($code);
        $parser->parse($code2);

* Fix `NameResolver` visitor to also resolve class names in `catch` blocks.

Version 0.9.1 (24.04.2012)
--------------------------

* Add ability to add attributes to nodes:

  It is now possible to add attributes to a node using `$node->setAttribute('name', 'value')` and to retrieve them using
  `$node->getAttribute('name' [, 'default'])`. Additionally the existance of an attribute can be checked with
  `$node->hasAttribute('name')` and all attributes can be returned using `$node->getAttributes()`.

* Add code generation features: Builders and templates.

  For more infos, see the [code generation documentation][1].

* [BC] Don't traverse nodes merged by another visitor:

  If a NodeVisitor returns an array of nodes to merge, these will no longer be traversed by all other visitors. This
  behavior only caused problems.

* Fix line numbers for some list structures.
* Fix XML unserialization of empty nodes.
* Fix parsing of integers that overflow into floats.
* Fix emulation of NOWDOC and binary floats.

Version 0.9.0 (05.01.2012)
--------------------------

First version.

 [1]: https://github.com/nikic/PHP-Parser/blob/master/doc/3_Code_generation.markdown