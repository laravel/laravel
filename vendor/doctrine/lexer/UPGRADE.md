Note about upgrading: Doctrine uses static and runtime mechanisms to raise
awareness about deprecated code.

- Use of `@deprecated` docblock that is detected by IDEs (like PHPStorm) or
  Static Analysis tools (like Psalm, phpstan)
- Use of our low-overhead runtime deprecation API, details:
  https://github.com/doctrine/deprecations/

# Upgrade to 3.0.0

`Doctrine\Common\Lexer\Token` no longer implements `ArrayAccess`.
Parameter type declarations have been added to
`Doctrine\Common\Lexer\AbstractLexer` and `Doctrine\Common\Lexer\Token`.
You should add both parameter type declarations and return type declarations to
your lexers, based on the `@return` phpdoc.

# Upgrade to 2.0.0

`AbstractLexer::glimpse()` and `AbstractLexer::peek()` now return
instances of `Doctrine\Common\Lexer\Token`, which is an array-like class
Using it as an array is deprecated in favor of using properties of that class.
Using `count()` on it is deprecated with no replacement.
