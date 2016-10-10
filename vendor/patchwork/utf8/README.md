Patchwork UTF-8 for PHP
=======================

[![Latest Stable Version](https://poser.pugx.org/patchwork/utf8/v/stable.png)](https://packagist.org/packages/patchwork/utf8)
[![Total Downloads](https://poser.pugx.org/patchwork/utf8/downloads.png)](https://packagist.org/packages/patchwork/utf8)
[![Build Status](https://secure.travis-ci.org/tchwork/utf8.png?branch=master)](http://travis-ci.org/tchwork/utf8)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/666c8ae7-0997-4d27-883a-6089ce3cc76b/mini.png)](https://insight.sensiolabs.com/projects/666c8ae7-0997-4d27-883a-6089ce3cc76b)

Patchwork UTF-8 gives PHP developpers extensive, portable and performant
handling of UTF-8 and [grapheme clusters](http://unicode.org/reports/tr29/).

It provides both :

- a portability layer for `mbstring`, `iconv`, and intl `Normalizer` and
  `grapheme_*` functions,
- an UTF-8 grapheme clusters aware replica of native string functions.

It can also serve as a documentation source referencing the practical problems
that arise when handling UTF-8 in PHP: Unicode concepts, related algorithms,
bugs in PHP core, workarounds, etc.

Portability
-----------

Unicode handling in PHP is best performed using a combo of `mbstring`, `iconv`,
`intl` and `pcre` with the `u` flag enabled. But when an application is expected
to run on many servers, you should be aware that these 4 extensions are not
always enabled.

Patchwork UTF-8 provides pure PHP implementations for 3 of those 4 extensions.
`pcre` compiled with unicode support is required but is widely available.
The following set of portability-fallbacks allows an application to run on a
server even if one or more of those extensions are not enabled:

- *utf8_encode, utf8_decode*,
- `mbstring`: *mb_check_encoding, mb_convert_case, mb_convert_encoding,
  mb_decode_mimeheader, mb_detect_encoding, mb_detect_order,
  mb_encode_mimeheader, mb_encoding_aliases, mb_internal_encoding, mb_language,
  mb_list_encodings, mb_strlen, mb_strpos, mb_strrpos, mb_strtolower,
  mb_strtoupper, mb_stripos, mb_stristr, mb_strrchr, mb_strrichr, mb_strripos,
  mb_strstr, mb_substitute_character, mb_substr*,
- `iconv`: *iconv, iconv_mime_decode, iconv_mime_decode_headers,
  iconv_get_encoding, iconv_set_encoding, iconv_mime_encode, ob_iconv_handler,
  iconv_strlen, iconv_strpos, iconv_strrpos, iconv_substr*,
- `intl`: *Normalizer, grapheme_extract, grapheme_stripos, grapheme_stristr,
  grapheme_strlen, grapheme_strpos, grapheme_strripos, grapheme_strrpos,
  grapheme_strstr, grapheme_substr, normalizer_is_normalized,
  normalizer_normalize*.

Patchwork\Utf8
--------------

[Grapheme clusters](http://unicode.org/reports/tr29/) should always be
considered when working with generic Unicode strings. The `Patchwork\Utf8`
class implements the quasi-complete set of native string functions that need
UTF-8 grapheme clusters awareness. Function names, arguments and behavior
carefully replicates native PHP string functions.

Some more functions are also provided to help handling UTF-8 strings:

- *filter()*: normalizes to UTF-8 NFC, converting from [CP-1252](http://wikipedia.org/wiki/CP-1252) when needed,
- *isUtf8()*: checks if a string contains well formed UTF-8 data,
- *toAscii()*: generic UTF-8 to ASCII transliteration,
- *strtocasefold()*: unicode transformation for caseless matching,
- *strtonatfold()*: generic case sensitive transformation for collation matching

Mirrored string functions are:
*strlen, substr, strpos, stripos, strrpos, strripos, strstr, stristr, strrchr,
strrichr, strtolower, strtoupper, wordwrap, chr, count_chars, ltrim, ord, rtrim,
trim, str_ireplace, str_pad, str_shuffle, str_split, str_word_count, strcmp,
strnatcmp, strcasecmp, strnatcasecmp, strncasecmp, strncmp, strcspn, strpbrk,
strrev, strspn, strtr, substr_compare, substr_count, substr_replace, ucfirst,
lcfirst, ucwords, number_format, utf8_encode, utf8_decode, json_decode,
filter_input, filter_input_array*.

Notably missing (but hard to replicate) are *printf*-family functions.

The implementation favors performance over full edge cases handling.
It generally works on UTF-8 normalized strings and provides filters to get them.

As the turkish locale requires special cares, a `Patchwork\TurkishUtf8` class
is provided for working with this locale. It clones all the features of
`Patchwork\Utf8` but knows about the turkish specifics.

Usage
-----

The recommended way to install Patchwork UTF-8 is [through
composer](http://getcomposer.org). Just create a `composer.json` file and run
the `php composer.phar install` command to install it:

    {
        "require": {
            "patchwork/utf8": "~1.1"
        }
    }

Then, early in your bootstrap sequence, you have to configure your environment:

```php
\Patchwork\Utf8\Bootup::initAll(); // Enables the portablity layer and configures PHP for UTF-8
\Patchwork\Utf8\Bootup::filterRequestUri(); // Redirects to an UTF-8 encoded URL if it's not already the case
\Patchwork\Utf8\Bootup::filterRequestInputs(); // Normalizes HTTP inputs to UTF-8 NFC
```

Run `phpunit` to see the code in action.

Make sure that you are confident about using UTF-8 by reading
[Character Sets / Character Encoding Issues](http://www.phpwact.org/php/i18n/charsets)
and [Handling UTF-8 with PHP](http://www.phpwact.org/php/i18n/utf-8),
or [PHP et UTF-8](http://julp.lescigales.org/articles/3-php-et-utf-8.html) for french readers.

You should also get familiar with the concept of
[Unicode Normalization](http://en.wikipedia.org/wiki/Unicode_equivalence) and
[Grapheme Clusters](http://unicode.org/reports/tr29/).

Do not blindly replace all use of PHP's string functions. Most of the time you
will not need to, and you will be introducing a significant performance overhead
to your application.

Screen your input on the *outer perimeter* so that only well formed UTF-8 pass
through. When dealing with badly formed UTF-8, you should not try to fix it
(see [Unicode Security Considerations](http://www.unicode.org/reports/tr36/#Deletion_of_Noncharacters)).
Instead, consider it as [CP-1252](http://wikipedia.org/wiki/CP-1252) and use
`Patchwork\Utf8::utf8_encode()` to get an UTF-8 string. Don't forget also to
choose one unicode normalization form and stick to it. NFC is now the defacto
standard. `Patchwork\Utf8::filter()` implements this behavior.

This library is orthogonal to `mbstring.func_overload` and will not work if the
php.ini setting is enabled.

Licensing
---------

Patchwork\Utf8 is free software; you can redistribute it and/or modify it under
the terms of the (at your option):
- [Apache License v2.0](http://apache.org/licenses/LICENSE-2.0.txt), or
- [GNU General Public License v2.0](http://gnu.org/licenses/gpl-2.0.txt).

Unicode handling requires tedious work to be implemented and maintained on the
long run. As such, contributions such as unit tests, bug reports, comments or
patches licensed under both licenses are really welcomed.

I hope many projects could adopt this code and together help solve the unicode
subject for PHP.
