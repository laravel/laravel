# Tokenizer

A small library for converting tokenized PHP source code into XML.

[![Test](https://github.com/theseer/tokenizer/actions/workflows/ci.yml/badge.svg)](https://github.com/theseer/tokenizer/actions/workflows/ci.yml)

## Installation

You can add this library as a local, per-project dependency to your project using [Composer](https://getcomposer.org/):

    composer require theseer/tokenizer

If you only need this library during development, for instance to run your project's test suite, then you should add it as a development-time dependency:

    composer require --dev theseer/tokenizer

## Usage examples

```php
$tokenizer = new TheSeer\Tokenizer\Tokenizer();
$tokens = $tokenizer->parse(file_get_contents(__DIR__ . '/src/XMLSerializer.php'));

$serializer = new TheSeer\Tokenizer\XMLSerializer();
$xml = $serializer->toXML($tokens);

echo $xml;
```

The generated XML structure looks something like this:

```xml
<?xml version="1.0"?>
<source xmlns="https://github.com/theseer/tokenizer">
 <line no="1">
  <token name="T_OPEN_TAG">&lt;?php </token>
  <token name="T_DECLARE">declare</token>
  <token name="T_OPEN_BRACKET">(</token>
  <token name="T_STRING">strict_types</token>
  <token name="T_WHITESPACE"> </token>
  <token name="T_EQUAL">=</token>
  <token name="T_WHITESPACE"> </token>
  <token name="T_LNUMBER">1</token>
  <token name="T_CLOSE_BRACKET">)</token>
  <token name="T_SEMICOLON">;</token>
 </line>
</source>
```
