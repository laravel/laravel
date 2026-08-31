# ChangeLog

All notable changes are documented in this file using the [Keep a CHANGELOG](https://keepachangelog.com/) principles.

## [6.0.3] - 2025-08-13

### Changed

* Do not use `SplObjectStorage` methods that will be deprecated in PHP 8.5

## [6.0.2] - 2024-07-03

### Changed

* This project now uses PHPStan instead of Psalm for static analysis

## [6.0.1] - 2024-06-17

### Changed

* [#30](https://github.com/sebastianbergmann/recursion-context/pull/30): Use more efficient `spl_object_id()` over `spl_object_hash()`

## [6.0.0] - 2024-02-02

### Removed

* This component is no longer supported on PHP 8.1

## [5.0.1] - 2025-08-10

### Changed

* Do not use `SplObjectStorage` methods that will be deprecated in PHP 8.5

## [5.0.0] - 2023-02-03

### Removed

* This component is no longer supported on PHP 7.3, PHP 7.4 and PHP 8.0

## [4.0.5] - 2023-02-03

### Fixed

* [#26](https://github.com/sebastianbergmann/recursion-context/pull/26): Don't clobber `null` values if `array_key_exists(PHP_INT_MAX, $array)`

## [4.0.4] - 2020-10-26

### Fixed

* `SebastianBergmann\RecursionContext\Exception` now correctly extends `\Throwable`

## [4.0.3] - 2020-09-28

### Changed

* [#21](https://github.com/sebastianbergmann/recursion-context/pull/21): Add type annotations for in/out parameters
* Changed PHP version constraint in `composer.json` from `^7.3 || ^8.0` to `>=7.3`

## [4.0.2] - 2020-06-26

### Added

* This component is now supported on PHP 8

## [4.0.1] - 2020-06-15

### Changed

* Tests etc. are now ignored for archive exports

[6.0.3]: https://github.com/sebastianbergmann/recursion-context/compare/6.0.2...6.0.3
[6.0.2]: https://github.com/sebastianbergmann/recursion-context/compare/6.0.1...6.0.2
[6.0.1]: https://github.com/sebastianbergmann/recursion-context/compare/6.0.0...6.0.1
[6.0.0]: https://github.com/sebastianbergmann/recursion-context/compare/5.0...6.0.0
[5.0.1]: https://github.com/sebastianbergmann/recursion-context/compare/5.0.0...5.0.1
[5.0.0]: https://github.com/sebastianbergmann/recursion-context/compare/4.0.5...5.0.0
[4.0.5]: https://github.com/sebastianbergmann/recursion-context/compare/4.0.4...4.0.5
[4.0.4]: https://github.com/sebastianbergmann/recursion-context/compare/4.0.3...4.0.4
[4.0.3]: https://github.com/sebastianbergmann/recursion-context/compare/4.0.2...4.0.3
[4.0.2]: https://github.com/sebastianbergmann/recursion-context/compare/4.0.1...4.0.2
[4.0.1]: https://github.com/sebastianbergmann/recursion-context/compare/4.0.0...4.0.1
