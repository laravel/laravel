Lexer component documentation
=============================

The lexer is responsible for providing tokens to the parser. The project comes with two lexers: `PHPParser_Lexer` and
`PHPParser_Lexer_Emulative`. The latter is an extension of the former, which adds the ability to emulate tokens of
newer PHP versions and thus allows parsing of new code on older versions.

A lexer has to define the following public interface:

    startLexing($code);
    getNextToken(&$value = null, &$startAttributes = null, &$endAttributes = null);
    handleHaltCompiler();

startLexing
-----------

The `startLexing` method is invoked when the `parse()` method of the parser is called. It's argument will be whatever
was passed to the `parse()` method.

Even though `startLexing` is meant to accept a source code string, you could for example overwrite it to accept a file:

```php
<?php

class FileLexer extends PHPParser_Lexer {
    public function startLexing($fileName) {
        if (!file_exists($fileName)) {
            throw new InvalidArgumentException(sprintf('File "%s" does not exist', $fileName));
        }

        parent::startLexing(file_get_contents($fileName));
    }
}

$parser = new PHPParser_Parser(new FileLexer);

var_dump($parser->parse('someFile.php'));
var_dump($parser->parse('someOtherFile.php'));
```

getNextToken
------------

`getNextToken` returns the ID of the next token and sets some additional information in the three variables which it
accepts by-ref. If no more tokens are available it has to return `0`, which is the ID of the `EOF` token.

The first by-ref variable `$value` should contain the textual content of the token. It is what will be available as `$1`
etc in the parser.

The other two by-ref variables `$startAttributes` and `$endAttributes` define which attributes will eventually be
assigned to the generated nodes: The parser will take the `$startAttributes` from the first token which is part of the
node and the `$endAttributes` from the last token that is part of the node.

E.g. if the tokens `T_FUNCTION T_STRING ... '{' ... '}'` constitute a node, then the `$startAttributes` from the
`T_FUNCTION` token will be taken and the `$endAttributes` from the `'}'` token.

By default the lexer creates the attributes `startLine`, `comments` (both part of `$startAttributes`) and `endLine`
(part of `$endAttributes`).

If you don't want all these attributes to be added (to reduce memory usage of the AST) you can simply remove them by
overriding the method:

```php
<?php

class LessAttributesLexer extends PHPParser_Lexer {
    public function getNextToken(&$value = null, &$startAttributes = null, &$endAttributes = null) {
        $tokenId = parent::getNextToken($value, $startAttributes, $endAttributes);

        // only keep startLine attribute
        unset($startAttributes['comments']);
        unset($endAttributes['endLine']);

        return $tokenId;
    }
}
```

You can obviously also add additional attributes. E.g. in conjunction with the above `FileLexer` you might want to add
a `fileName` attribute to all nodes:

```php
<?php

class FileLexer extends PHPParser_Lexer {
    protected $fileName;

    public function startLexing($fileName) {
        if (!file_exists($fileName)) {
            throw new InvalidArgumentException(sprintf('File "%s" does not exist', $fileName));
        }

        $this->fileName = $fileName;
        parent::startLexing(file_get_contents($fileName));
    }

    public function getNextToken(&$value = null, &$startAttributes = null, &$endAttributes = null) {
        $tokenId = parent::getNextToken($value, $startAttributes, $endAttributes);

        // we could use either $startAttributes or $endAttributes here, because the fileName is always the same
        // (regardless of whether it is the start or end token). We choose $endAttributes, because it is slightly
        // more efficient (as the parser has to keep a stack for the $startAttributes).
        $endAttributes['fileName'] = $fileName;

        return $tokenId;
    }
}
```

handleHaltCompiler
------------------

The method is invoked whenever a `T_HALT_COMPILER` token is encountered. It has to return the remaining string after the
construct (not including `();`).