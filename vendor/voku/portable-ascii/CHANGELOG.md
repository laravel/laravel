# Changelog

### 2.1.1 (2026-04-24)

- fix long-string `ASCII::to_ascii()` regression coverage to match current degree-sign handling and lock in cache-order behavior across cleanup, transliteration, and retention modes

### 2.1.0 (2026-04-16)

- run all checks and tests up to PHP 8.5
- raise the minimum PHP version from 7.0 to 7.1
- update PHPUnit dev-dependency to support ~8.5 || ~9.6 || ~10.5 || ~11.5
- modernize CI: add a required PHPStan job, update the GitHub Actions matrix, and refresh the AppVeyor config
- optimize `ASCII::to_ascii()` / `ASCII::to_transliterate()` hot paths, add benchmark coverage, and document current benchmark results
- harden invalid UTF-8 handling in `clean()`, `to_ascii()`, and `to_transliterate()` for malformed, overlong, surrogate, and out-of-range sequences
- expand regression coverage for malformed UTF-8, transliteration boundaries, slug loops, and other edge cases

### 2.0.3 (2024-11-21)

- use modern phpdocs e.g. list<int> or conditional-return annotations 

### 2.0.2 (2024-11-21)

- small fix for PHP 8.4 (thanks to @gilbertoalbino)

### 2.0.1 (2022-03-08)

- "To people of Russia": There is a war in Ukraine right now. The forces of the Russian Federation are attacking civilians.
- optimize some phpdocs

### 2.0.0 (2022-01-24)

- prefer "Russian - Passport (2013), ICAO" instead of "Russian - GOST 7.79-2000(B)"
- fix "Ukrainian" char-mapping (thanks to @Andr1yk0)
- fix "Persian" char-mapping (thanks to @frost-cyber)

### 1.6.1 (2022-01-24)

- revert: prefer "Russian - Passport (2013), ICAO" instead of "Russian - GOST 7.79-2000(B)"
- revert: fix "Ukrainian" char-mapping (thanks to @Andr1yk0)
- revert: fix "Persian" char-mapping (thanks to @frost-cyber)

### 1.6.0 (2022-01-24)

- prefer "Russian - Passport (2013), ICAO" instead of "Russian - GOST 7.79-2000(B)"
- fix "Ukrainian" char-mapping (thanks to @Andr1yk0) 
- fix "Persian" char-mapping (thanks to @frost-cyber) 
- fix "ASCII::normalize_whitespace()" -> "CARRIAGE RETURN" is more like "<br>" and no "\n"
- add "ASCII::to_ascii_remap()" -> this method will return broken characters and is only for special cases

### 1.5.6 (2020-11-12)
    
- "ASCII::normalize_whitespace()" -> can now also remove "control characters" if needed v2

### 1.5.5 (2020-11-12)

- fix "Greeklish" char-mapping (thanks @sebdesign)
- "ASCII::normalize_whitespace()" -> can now also remove "control characters" if needed

### 1.5.4 (2020-11-08)

- add some missing replacements in U+23xx page (thanks @marcoffee)
- fix "Russian" char-mapping (thanks @ilyahoilik)
- running test with PHP 8.0 rc3

### 1.5.3 (2020-07-23)

- fix "Georgian" char-mapping (thanks @waska14)

### 1.5.2 (2020-06-16)

- add "Bengali" (bn) language support (thanks @eliyas5044)
- fix "Portuguese" char-mapping
- reduce the file size (removed extra comments from "avian2/unidecode")

### 1.5.1 (2020-05-26)

- fix merge ASCII transliterations from "avian2/unidecode" (python)
  -> https://github.com/avian2/unidecode/
  
### 1.5.0 (2020-05-24)

- merge ASCII transliterations from "avian2/unidecode" (python)
  -> https://github.com/avian2/unidecode/

### 1.4.11 (2020-05-23)

- "composer.json" -> remove "autoload-dev" stuff from "autoload"
- "voku/php-readme-helper" -> auto-generate the API documentation in the README

### 1.4.10 (2020-03-13)

- ASCII::to_ascii() -> fix extra symbol handling in the regex
- ASCII::to_ascii() -> fix for languages with multi-length-special-char (e.g. Greek -> 'ει' => 'i')

### 1.4.9 (2020-03-06)

- ASCII::to_slugify() -> fix php warning from empty "separator"

### 1.4.8 (2020-02-06)

- small optimization for "ASCII::to_ascii()" performance

### 1.4.7 (2020-01-27)

- fix possible wrong type from "getDataIfExists()" -> e.g. a bug reported where "/data/" was modified
- inline variables
- do not use "=== true" for "bool"-types

### 1.4.6 (2019-12-23)

- optimize "ASCII::to_ascii()" performance
- add "armenian" chars
- add "ASCII:getAllLanguages()"

### 1.4.5 (2019-12-19)

- use "@psalm-pure" v2

### 1.4.4 (2019-12-19)

- use "@psalm-pure"

### 1.4.3 (2019-12-19)

- use "@psalm-immutable"

### 1.4.2 (2019-12-13)

- optimize the performance v2
- more fixes for non-ascii regex

### 1.4.1 (2019-12-13)

- fix regex for non-ascii

### 1.4.0 (2019-12-13)

- optimize the performance, via single char replacements

### 1.3.6 (2019-12-13)

- "ascii_extras" -> convert the static content into ascii 
   -> e.g.: instead of replacing "+" with "più" we use "piu" (Italian), because we want to use ascii anyway

### 1.3.5 (2019-11-11)

- fix "ASCII::remove_invisible_characters()" -> do not remove invisible encoded url strings by default

### 1.3.4 (2019-10-14)

- fix static cache for "ASCII::charsArrayWithOneLanguage"

### 1.3.3 (2019-10-14)

- fix "Turkish" mapping -> 'ä' -> 'a'

### 1.3.2 (2019-10-14)

- fix language parameter usage with e.g. "de_DE"
- re-add missing "extra"-mapping chars

### 1.3.1 (2019-10-13)

- fix "ASCII::to_slugify" -> remove unicode chars
- add more test for ascii chars in the mapping
- fix non ascii chars in the mapping

### 1.3.0 (2019-10-12)

- add transliteration "fr" (was supported before, but with chars from other languages)
- add transliteration "ru" - Passport (2013), ICAO
- add transliteration "ru" - GOST 7.79-2000(B)
- add transliteration "el" - greeklish
- add transliteration "zh"
- add transliteration "nl"
- add transliteration "it"
- add transliteration "mk"
- add transliteration "pt"
- add constants -> ASCII::*LANGUAGE_CODES
- add more special latin chars / (currency) symbols
- add simple tests for all supported languages
- optimize "Russian" to ASCII (via "translit.ru")
- optimize performance of string replacement
- optimize performance of array merging
- optimize phpdoc comments
- "ASCII::to_transliterate" -> use "transliterator_create" + static cache
- "ASCII::to_ascii" -> fix "remove unsupported chars"
- "ASCII::to_ascii" -> add some more special chars
- run/fix static analyse via "pslam" + "phpstan" 
- auto fix code style via "php-cs-fixer"
- fix transliteration for "german"
- fix transliteration for "persian" (thanks @mardep)
- fix transliteration for "polish" (thanks @dariusz.drobisz)
- fix transliteration for "bulgarian" (thanks @mkosturkov)
- fix transliteration for "croatian" (thanks @ludifonovac)
- fix transliteration for "serbian" (thanks @ludifonovac)
- fix transliteration for "swedish" (thanks @nicholasruunu)
- fix transliteration for "france" (thanks @sharptsa)
- fix transliteration for "serbian" (thanks @nikolaposa)
- fix transliteration for "czech" (thanks @slepic)

### 1.2.3 (2019-09-10)

- fix language depending ASCII chars (the order matters)

### 1.2.2 (2019-09-10)

- fix bulgarian ASCII chars | thanks @bgphp

### 1.2.1 (2019-09-07)

- "charsArray()" -> add access to "ASCII::$ASCII_MAPS*""

### 1.2.0 (2019-09-07)

- "to_slugify()" -> use the extra ascii array

### 1.1.0 (2019-09-07)

- add + split extra ascii replacements

### 1.0.0 (2019-09-05)

- initial commit
