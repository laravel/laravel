Symfony Polyfill / Intl: Grapheme
=================================

This component provides a partial, native PHP implementation of the
[Grapheme functions](https://php.net/intl.grapheme) from the
[Intl](https://php.net/intl) extension.

- [`grapheme_extract`](https://php.net/grapheme_extract): Extract a sequence of grapheme
  clusters from a text buffer, which must be encoded in UTF-8
- [`grapheme_stripos`](https://php.net/grapheme_stripos): Find position (in grapheme units)
  of first occurrence of a case-insensitive string
- [`grapheme_stristr`](https://php.net/grapheme_stristr): Returns part of haystack string
  from the first occurrence of case-insensitive needle to the end of haystack
- [`grapheme_strlen`](https://php.net/grapheme_strlen): Get string length in grapheme units
- [`grapheme_strpos`](https://php.net/grapheme_strpos): Find position (in grapheme units)
  of first occurrence of a string
- [`grapheme_strripos`](https://php.net/grapheme_strripos): Find position (in grapheme units)
  of last occurrence of a case-insensitive string
- [`grapheme_strrpos`](https://php.net/grapheme_strrpos): Find position (in grapheme units)
  of last occurrence of a string
- [`grapheme_strstr`](https://php.net/grapheme_strstr): Returns part of haystack string from
  the first occurrence of needle to the end of haystack
- [`grapheme_substr`](https://php.net/grapheme_substr): Return part of a string
- [`grapheme_str_split`](https://php.net/grapheme_str_split): Splits a string into an array of individual or chunks of graphemes

More information can be found in the
[main Polyfill README](https://github.com/symfony/polyfill/blob/main/README.md).

License
=======

This library is released under the [MIT license](LICENSE).
