# ChangeLog

All notable changes are documented in this file using the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## [6.3.3] - 2026-01-24

### Changed

* [#134](https://github.com/sebastianbergmann/comparator/issues/134): Suppress warning introduced in PHP 8.5

## [6.3.2] - 2025-08-10

### Changed

* Do not use `SplObjectStorage` methods that will be deprecated in PHP 8.5

## [6.3.1] - 2025-03-07

### Fixed

* [#122](https://github.com/sebastianbergmann/comparator/issues/122): `INF` is considered equal to `-INF`

## [6.3.0] - 2025-01-06

### Added

* [#121](https://github.com/sebastianbergmann/comparator/pull/121): Support for `BcMath\Number` objects

## [6.2.1] - 2024-10-31

### Fixed

* [#119](https://github.com/sebastianbergmann/comparator/pull/119): `Uninitialized string offset -1` warning

## [6.2.0] - 2024-10-30

### Changed

* [#117](https://github.com/sebastianbergmann/comparator/pull/117): Remove common prefixes and suffixes from actual and expected single-line strings

## [6.1.1] - 2024-10-18

### Fixed

* Reverted [#113](https://github.com/sebastianbergmann/comparator/pull/113) as it broke backward compatibility

## [6.1.0] - 2024-09-11

### Added

* Specialized comparator for enumerations

## [6.0.2] - 2024-08-12

### Fixed

* [#112](https://github.com/sebastianbergmann/comparator/issues/112): Arrays with different keys and the same values are considered equal in canonicalize mode

## [6.0.1] - 2024-07-03

### Changed

* This project now uses PHPStan instead of Psalm for static analysis

## [6.0.0] - 2024-02-02

### Removed

* Removed support for PHP 8.1

## [5.0.4] - 2025-09-07

### Changed

* Do not use `SplObjectStorage` methods that will be deprecated in PHP 8.5

## [5.0.3] - 2024-10-18

### Fixed

* Reverted [#113](https://github.com/sebastianbergmann/comparator/pull/113) as it broke backward compatibility

## [5.0.2] - 2024-08-12

### Fixed

* [#112](https://github.com/sebastianbergmann/comparator/issues/112): Arrays with different keys and the same values are considered equal in canonicalize mode

## [5.0.1] - 2023-08-14

### Fixed

* `MockObjectComparator` only works on instances of `PHPUnit\Framework\MockObject\MockObject`, but not on instances of `PHPUnit\Framework\MockObject\Stub`
* `MockObjectComparator` only ignores the `$__phpunit_invocationMocker` property, but not other properties with names prefixed with `__phpunit_`

## [5.0.0] - 2023-02-03

### Changed

* Methods now have parameter and return type declarations
* `Comparator::$factory` is now private, use `Comparator::factory()` instead
* `ComparisonFailure`, `DOMNodeComparator`, `DateTimeComparator`, `ExceptionComparator`, `MockObjectComparator`, `NumericComparator`, `ResourceComparator`, `SplObjectStorageComparator`, and `TypeComparator` are now `final`
* `ScalarComparator` and `DOMNodeComparator` now use `mb_strtolower($string, 'UTF-8')` instead of `strtolower($string)`

### Removed

* Removed `$identical` parameter from `ComparisonFailure::__construct()`
* Removed `Comparator::$exporter`
* Removed support for PHP 7.3, PHP 7.4, and PHP 8.0

## [4.0.8] - 2022-09-14

### Fixed

* [#102](https://github.com/sebastianbergmann/comparator/pull/102): Fix `float` comparison precision

## [4.0.7] - 2022-09-14

### Fixed

* [#99](https://github.com/sebastianbergmann/comparator/pull/99): Fix weak comparison between `'0'` and `false`

## [4.0.6] - 2020-10-26

### Fixed

* `SebastianBergmann\Comparator\Exception` now correctly extends `\Throwable`

## [4.0.5] - 2020-09-30

### Fixed

* [#89](https://github.com/sebastianbergmann/comparator/pull/89): Handle PHP 8 `ValueError`

## [4.0.4] - 2020-09-28

### Changed

* Changed PHP version constraint in `composer.json` from `^7.3 || ^8.0` to `>=7.3`

## [4.0.3] - 2020-06-26

### Added

* This component is now supported on PHP 8

## [4.0.2] - 2020-06-15

### Fixed

* [#85](https://github.com/sebastianbergmann/comparator/issues/85): Version 4.0.1 breaks backward compatibility

## [4.0.1] - 2020-06-15

### Changed

* Tests etc. are now ignored for archive exports

## [4.0.0] - 2020-02-07

### Removed

* Removed support for PHP 7.1 and PHP 7.2

## [3.0.5] - 2022-09-14

### Fixed

* [#102](https://github.com/sebastianbergmann/comparator/pull/102): Fix `float` comparison precision

## [3.0.4] - 2022-09-14

### Fixed

* [#99](https://github.com/sebastianbergmann/comparator/pull/99): Fix weak comparison between `'0'` and `false`

## [3.0.3] - 2020-11-30

### Changed

* Changed PHP version constraint in `composer.json` from `^7.1` to `>=7.1`

## [3.0.2] - 2018-07-12

### Changed

* By default, `MockObjectComparator` is now tried before all other (default) comparators

## [3.0.1] - 2018-06-14

### Fixed

* [#53](https://github.com/sebastianbergmann/comparator/pull/53): `DOMNodeComparator` ignores `$ignoreCase` parameter
* [#58](https://github.com/sebastianbergmann/comparator/pull/58): `ScalarComparator` does not handle extremely ugly string comparison edge cases

## [3.0.0] - 2018-04-18

### Fixed

* [#48](https://github.com/sebastianbergmann/comparator/issues/48): `DateTimeComparator` does not support fractional second deltas

### Removed

* Removed support for PHP 7.0

## [2.1.3] - 2018-02-01

### Changed

* This component is now compatible with version 3 of `sebastian/diff`

## [2.1.2] - 2018-01-12

### Fixed

* Fix comparison of `DateTimeImmutable` objects

## [2.1.1] - 2017-12-22

### Fixed

* [phpunit/#2923](https://github.com/sebastianbergmann/phpunit/issues/2923): Unexpected failed date matching

## [2.1.0] - 2017-11-03

### Added

* Added `SebastianBergmann\Comparator\Factory::reset()` to unregister all non-default comparators
* Added support for `phpunit/phpunit-mock-objects` version `^5.0`

[6.3.3]: https://github.com/sebastianbergmann/comparator/compare/6.3.2...6.3.3
[6.3.2]: https://github.com/sebastianbergmann/comparator/compare/6.3.1...6.3.2
[6.3.1]: https://github.com/sebastianbergmann/comparator/compare/6.3.0...6.3.1
[6.3.0]: https://github.com/sebastianbergmann/comparator/compare/6.2.1...6.3.0
[6.2.1]: https://github.com/sebastianbergmann/comparator/compare/6.2.0...6.2.1
[6.2.0]: https://github.com/sebastianbergmann/comparator/compare/6.1.1...6.2.0
[6.1.1]: https://github.com/sebastianbergmann/comparator/compare/6.1.0...6.1.1
[6.1.0]: https://github.com/sebastianbergmann/comparator/compare/6.0.2...6.1.0
[6.0.2]: https://github.com/sebastianbergmann/comparator/compare/6.0.1...6.0.2
[6.0.1]: https://github.com/sebastianbergmann/comparator/compare/6.0.0...6.0.1
[6.0.0]: https://github.com/sebastianbergmann/comparator/compare/5.0...6.0.0
[5.0.5]: https://github.com/sebastianbergmann/comparator/compare/5.0.4...5.0.5
[5.0.4]: https://github.com/sebastianbergmann/comparator/compare/5.0.3...5.0.4
[5.0.3]: https://github.com/sebastianbergmann/comparator/compare/5.0.2...5.0.3
[5.0.2]: https://github.com/sebastianbergmann/comparator/compare/5.0.1...5.0.2
[5.0.1]: https://github.com/sebastianbergmann/comparator/compare/5.0.0...5.0.1
[5.0.0]: https://github.com/sebastianbergmann/comparator/compare/4.0...5.0.0
[4.0.10]: https://github.com/sebastianbergmann/comparator/compare/4.0.9...4.0.10
[4.0.9]: https://github.com/sebastianbergmann/comparator/compare/4.0.8...4.0.9
[4.0.8]: https://github.com/sebastianbergmann/comparator/compare/4.0.7...4.0.8
[4.0.7]: https://github.com/sebastianbergmann/comparator/compare/4.0.6...4.0.7
[4.0.6]: https://github.com/sebastianbergmann/comparator/compare/4.0.5...4.0.6
[4.0.5]: https://github.com/sebastianbergmann/comparator/compare/4.0.4...4.0.5
[4.0.4]: https://github.com/sebastianbergmann/comparator/compare/4.0.3...4.0.4
[4.0.3]: https://github.com/sebastianbergmann/comparator/compare/4.0.2...4.0.3
[4.0.2]: https://github.com/sebastianbergmann/comparator/compare/4.0.1...4.0.2
[4.0.1]: https://github.com/sebastianbergmann/comparator/compare/4.0.0...4.0.1
[4.0.0]: https://github.com/sebastianbergmann/comparator/compare/3.0...4.0.0
[3.0.7]: https://github.com/sebastianbergmann/comparator/compare/3.0.6...3.0.7
[3.0.6]: https://github.com/sebastianbergmann/comparator/compare/3.0.5...3.0.6
[3.0.5]: https://github.com/sebastianbergmann/comparator/compare/3.0.4...3.0.5
[3.0.4]: https://github.com/sebastianbergmann/comparator/compare/3.0.3...3.0.4
[3.0.3]: https://github.com/sebastianbergmann/comparator/compare/3.0.2...3.0.3
[3.0.2]: https://github.com/sebastianbergmann/comparator/compare/3.0.1...3.0.2
[3.0.1]: https://github.com/sebastianbergmann/comparator/compare/3.0.0...3.0.1
[3.0.0]: https://github.com/sebastianbergmann/comparator/compare/2.1.3...3.0.0
[2.1.3]: https://github.com/sebastianbergmann/comparator/compare/2.1.2...2.1.3
[2.1.2]: https://github.com/sebastianbergmann/comparator/compare/2.1.1...2.1.2
[2.1.1]: https://github.com/sebastianbergmann/comparator/compare/2.1.0...2.1.1
[2.1.0]: https://github.com/sebastianbergmann/comparator/compare/2.0.2...2.1.0
