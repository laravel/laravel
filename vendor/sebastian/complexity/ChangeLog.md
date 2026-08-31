# ChangeLog

All notable changes are documented in this file using the [Keep a CHANGELOG](https://keepachangelog.com/) principles.

## [4.0.1] - 2024-07-03

### Changed

* This project now uses PHPStan instead of Psalm for static analysis

## [4.0.0] - 2024-02-02

### Removed

* This component now requires PHP-Parser 5
* This component is no longer supported on PHP 8.1

## [3.2.0] - 2023-12-21

### Added

* `ComplexityCollection::sortByDescendingCyclomaticComplexity()`
* Support for `match` arms

### Changed

* This component is now compatible with `nikic/php-parser` 5.0

## [3.1.0] - 2023-09-28

### Added

* `Complexity::isFunction()` and `Complexity::isMethod()`
* `ComplexityCollection::isFunction()` and `ComplexityCollection::isMethod()`
* `ComplexityCollection::mergeWith()`

### Fixed

* Anonymous classes are not processed correctly

## [3.0.1] - 2023-08-31

### Fixed

* [#7](https://github.com/sebastianbergmann/complexity/pull/7): `ComplexityCalculatingVisitor` tries to process interface methods

## [3.0.0] - 2023-02-03

### Removed

* This component is no longer supported on PHP 7.3, PHP 7.4 and PHP 8.0

## [2.0.2] - 2020-10-26

### Fixed

* `SebastianBergmann\Complexity\Exception` now correctly extends `\Throwable`

## [2.0.1] - 2020-09-28

### Changed

* Changed PHP version constraint in `composer.json` from `^7.3 || ^8.0` to `>=7.3`

## [2.0.0] - 2020-07-25

### Removed

* The `ParentConnectingVisitor` has been removed (it should have been marked as `@internal`)

## [1.0.0] - 2020-07-22

* Initial release

[4.0.1]: https://github.com/sebastianbergmann/complexity/compare/4.0.0...4.0.1
[4.0.0]: https://github.com/sebastianbergmann/complexity/compare/3.2...4.0.0
[3.2.0]: https://github.com/sebastianbergmann/complexity/compare/3.1.0...3.2.0
[3.1.0]: https://github.com/sebastianbergmann/complexity/compare/3.0.1...3.1.0
[3.0.1]: https://github.com/sebastianbergmann/complexity/compare/3.0.0...3.0.1
[3.0.0]: https://github.com/sebastianbergmann/complexity/compare/2.0.2...3.0.0
[2.0.2]: https://github.com/sebastianbergmann/complexity/compare/2.0.1...2.0.2
[2.0.1]: https://github.com/sebastianbergmann/complexity/compare/2.0.0...2.0.1
[2.0.0]: https://github.com/sebastianbergmann/complexity/compare/1.0.0...2.0.0
[1.0.0]: https://github.com/sebastianbergmann/complexity/compare/70ee0ad32d9e2be3f85beffa3e2eb474193f2487...1.0.0
