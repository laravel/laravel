# ChangeLog

All notable changes are documented in this file using the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## [11.0.12] - 2025-12-24

### Fixed

* [#1092](https://github.com/sebastianbergmann/php-code-coverage/issues/1092): Error in `DOMDocument::saveXML()` is not handled
* [#1131](https://github.com/sebastianbergmann/php-code-coverage/issues/1131): Invalid XML generated when both PCOV and Xdebug are loaded

## [11.0.11] - 2025-08-27

### Changed

* [#1085](https://github.com/sebastianbergmann/php-code-coverage/pull/1085): Improve performance by skipping empty lines after filter has been applied

## [11.0.10] - 2025-06-18

### Changed

* Changed CSS for HTML report to not use common ligatures as this sometimes lead to hard-to-read code
* Updated Bootstrap to version 5.3.6 for HTML report

## [11.0.9] - 2025-02-25

### Changed

* Changed version identifier for static analysis cache from "MD5 over source code" to `Version::id()`
* The `SebastianBergmann\CodeCoverage\Filter::includeUncoveredFiles()` and `SebastianBergmann\CodeCoverage\Filter::excludeUncoveredFiles()` methods are no longer deprecated

### Fixed

* [#1063](https://github.com/sebastianbergmann/php-code-coverage/issues/1063): HTML report highlights argument named `fn` differently than other named arguments

## [11.0.8] - 2024-12-11

### Changed

* [#1054](https://github.com/sebastianbergmann/php-code-coverage/pull/1054): Use click event for toggling "tests covering this line" popover in HTML report

## [11.0.7] - 2024-10-09

### Changed

* [#1037](https://github.com/sebastianbergmann/php-code-coverage/pull/1037): Upgrade Bootstrap to version 5.3.3 for HTML report
* [#1046](https://github.com/sebastianbergmann/php-code-coverage/pull/1046): CSS fixes for HTML report

### Deprecated

* The `SebastianBergmann\CodeCoverage\Filter::includeUncoveredFiles()`, `SebastianBergmann\CodeCoverage\Filter::excludeUncoveredFiles()`, and `SebastianBergmann\CodeCoverage\Filter::excludeFile()` methods have been deprecated

## [11.0.6] - 2024-08-22

### Changed

* Updated dependencies (so that users that install using Composer's `--prefer-lowest` CLI option also get recent versions)

## [11.0.5] - 2024-07-03

### Changed

* This project now uses PHPStan instead of Psalm for static analysis

## [11.0.4] - 2024-06-29

### Fixed

* [#967](https://github.com/sebastianbergmann/php-code-coverage/issues/967): Identification of executable lines for `match` expressions does not work correctly

## [11.0.3] - 2024-03-12

### Fixed

* [#1033](https://github.com/sebastianbergmann/php-code-coverage/issues/1033): `@codeCoverageIgnore` annotation does not work on `enum`

## [11.0.2] - 2024-03-09

### Changed

* [#1032](https://github.com/sebastianbergmann/php-code-coverage/pull/1032): Pad lines in code coverage report only when colors are shown

## [11.0.1] - 2024-03-02

### Changed

* Do not use implicitly nullable parameters

## [11.0.0] - 2024-02-02

### Removed

* The `SebastianBergmann\CodeCoverage\Filter::includeDirectory()`, `SebastianBergmann\CodeCoverage\Filter::excludeDirectory()`, and `SebastianBergmann\CodeCoverage\Filter::excludeFile()` methods have been removed
* This component now requires PHP-Parser 5
* This component is no longer supported on PHP 8.1

[11.0.12]: https://github.com/sebastianbergmann/php-code-coverage/compare/11.0.11...11.0.12
[11.0.11]: https://github.com/sebastianbergmann/php-code-coverage/compare/11.0.10...11.0.11
[11.0.10]: https://github.com/sebastianbergmann/php-code-coverage/compare/11.0.9...11.0.10
[11.0.9]: https://github.com/sebastianbergmann/php-code-coverage/compare/11.0.8...11.0.9
[11.0.8]: https://github.com/sebastianbergmann/php-code-coverage/compare/11.0.7...11.0.8
[11.0.7]: https://github.com/sebastianbergmann/php-code-coverage/compare/11.0.6...11.0.7
[11.0.6]: https://github.com/sebastianbergmann/php-code-coverage/compare/11.0.5...11.0.6
[11.0.5]: https://github.com/sebastianbergmann/php-code-coverage/compare/11.0.4...11.0.5
[11.0.4]: https://github.com/sebastianbergmann/php-code-coverage/compare/11.0.3...11.0.4
[11.0.3]: https://github.com/sebastianbergmann/php-code-coverage/compare/11.0.2...11.0.3
[11.0.2]: https://github.com/sebastianbergmann/php-code-coverage/compare/11.0.1...11.0.2
[11.0.1]: https://github.com/sebastianbergmann/php-code-coverage/compare/11.0.0...11.0.1
[11.0.0]: https://github.com/sebastianbergmann/php-code-coverage/compare/10.1...11.0.0
