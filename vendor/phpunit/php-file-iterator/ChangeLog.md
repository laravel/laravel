# Change Log

All notable changes to this project will be documented in this file. This project adheres to [Semantic Versioning](http://semver.org/).

## [5.1.1] - 2026-02-02

### Fixed

* [#84](https://github.com/sebastianbergmann/php-file-iterator/issues/84): `Factory` drops directories during wildcard resolution

## [5.1.0] - 2024-08-27

### Added

* [#83](https://github.com/sebastianbergmann/php-file-iterator/pull/83): Support for "Globstar" pattern

## [5.0.1] - 2024-07-03

### Changed

* This project now uses PHPStan instead of Psalm for static analysis

## [5.0.0] - 2024-02-02

### Removed

* This component is no longer supported on PHP 8.1

## [4.1.0] - 2023-08-31

### Added

* [#81](https://github.com/sebastianbergmann/php-file-iterator/issues/81): Accept `array|string $paths` in `Facade::getFilesAsArray()`

## [4.0.2] - 2023-05-07

### Fixed

* [#80](https://github.com/sebastianbergmann/php-file-iterator/pull/80): Ignore unresolvable symbolic link

## [4.0.1] - 2023-02-10

### Fixed

* [#67](https://github.com/sebastianbergmann/php-file-iterator/issues/61): Excluded directories are traversed unnecessarily

## [4.0.0] - 2023-02-03

### Removed

* The optional `$commonPath` parameter of `SebastianBergmann\FileIterator\Facade` as well as the functionality it controlled has been removed
* The `SebastianBergmann\FileIterator\Factory` and `SebastianBergmann\FileIterator\Iterator` classes are now marked `@internal`
* This component is no longer supported on PHP 7.3, PHP 7.4 and PHP 8.0

## [3.0.6] - 2021-12-02

### Changed

* [#73](https://github.com/sebastianbergmann/php-file-iterator/pull/73): Micro performance improvements on parsing paths

## [3.0.5] - 2020-09-28

### Changed

* Changed PHP version constraint in `composer.json` from `^7.3 || ^8.0` to `>=7.3`

## [3.0.4] - 2020-07-11

### Fixed

* [#67](https://github.com/sebastianbergmann/php-file-iterator/issues/67): `TypeError` in `SebastianBergmann\FileIterator\Iterator::accept()`

## [3.0.3] - 2020-06-26

### Added

* This component is now supported on PHP 8

## [3.0.2] - 2020-06-15

### Changed

* Tests etc. are now ignored for archive exports

## [3.0.1] - 2020-04-18

### Fixed

* [#64](https://github.com/sebastianbergmann/php-file-iterator/issues/64): Release tarball contains Composer PHAR

## [3.0.0] - 2020-02-07

### Removed

* This component is no longer supported on PHP 7.1 and PHP 7.2

## [2.0.5] - 2021-12-02

### Changed

* [#73](https://github.com/sebastianbergmann/php-file-iterator/pull/73): Micro performance improvements on parsing paths

### Fixed

* [#74](https://github.com/sebastianbergmann/php-file-iterator/pull/74): Document return type of `SebastianBergmann\FileIterator\Iterator::accept()` so that Symfony's `DebugClassLoader` does not trigger a deprecation warning

## [2.0.4] - 2021-07-19

### Changed

* Added `ReturnTypeWillChange` attribute to `SebastianBergmann\FileIterator\Iterator::accept()` because the return type of `\FilterIterator::accept()` will change in PHP 8.1

## [2.0.3] - 2020-11-30

### Changed

* Changed PHP version constraint in `composer.json` from `^7.1` to `>=7.1`

## [2.0.2] - 2018-09-13

### Fixed

* [#48](https://github.com/sebastianbergmann/php-file-iterator/issues/48): Excluding an array that contains false ends up excluding the current working directory

## [2.0.1] - 2018-06-11

### Fixed

* [#46](https://github.com/sebastianbergmann/php-file-iterator/issues/46): Regression with hidden parent directory

## [2.0.0] - 2018-05-28

### Fixed

* [#30](https://github.com/sebastianbergmann/php-file-iterator/issues/30): Exclude is not considered if it is a parent of the base path

### Changed

* This component now uses namespaces

### Removed

* This component is no longer supported on PHP 5.3, PHP 5.4, PHP 5.5, PHP 5.6, and PHP 7.0

## [1.4.5] - 2017-11-27

### Fixed

* [#37](https://github.com/sebastianbergmann/php-file-iterator/issues/37): Regression caused by fix for [#30](https://github.com/sebastianbergmann/php-file-iterator/issues/30)

## [1.4.4] - 2017-11-27

### Fixed

* [#30](https://github.com/sebastianbergmann/php-file-iterator/issues/30): Exclude is not considered if it is a parent of the base path

## [1.4.3] - 2017-11-25

### Fixed

* [#34](https://github.com/sebastianbergmann/php-file-iterator/issues/34): Factory should use canonical directory names

## [1.4.2] - 2016-11-26

No changes

## [1.4.1] - 2015-07-26

No changes

## 1.4.0 - 2015-04-02

### Added

* [#23](https://github.com/sebastianbergmann/php-file-iterator/pull/23): Added support for wildcards (glob) in exclude

[5.1.1]: https://github.com/sebastianbergmann/php-file-iterator/compare/5.1.0...5.1.1
[5.1.0]: https://github.com/sebastianbergmann/php-file-iterator/compare/5.0.1...5.1.0
[5.0.1]: https://github.com/sebastianbergmann/php-file-iterator/compare/5.0.0...5.0.1
[5.0.0]: https://github.com/sebastianbergmann/php-file-iterator/compare/4.1...5.0.0
[4.1.0]: https://github.com/sebastianbergmann/php-file-iterator/compare/4.0.2...4.1.0
[4.0.2]: https://github.com/sebastianbergmann/php-file-iterator/compare/4.0.1...4.0.2
[4.0.1]: https://github.com/sebastianbergmann/php-file-iterator/compare/4.0.0...4.0.1
[4.0.0]: https://github.com/sebastianbergmann/php-file-iterator/compare/3.0.6...4.0.0
[3.0.6]: https://github.com/sebastianbergmann/php-file-iterator/compare/3.0.5...3.0.6
[3.0.5]: https://github.com/sebastianbergmann/php-file-iterator/compare/3.0.4...3.0.5
[3.0.4]: https://github.com/sebastianbergmann/php-file-iterator/compare/3.0.3...3.0.4
[3.0.3]: https://github.com/sebastianbergmann/php-file-iterator/compare/3.0.2...3.0.3
[3.0.2]: https://github.com/sebastianbergmann/php-file-iterator/compare/3.0.1...3.0.2
[3.0.1]: https://github.com/sebastianbergmann/php-file-iterator/compare/3.0.0...3.0.1
[3.0.0]: https://github.com/sebastianbergmann/php-file-iterator/compare/2.0.5...3.0.0
[2.0.5]: https://github.com/sebastianbergmann/php-file-iterator/compare/2.0.4...2.0.5
[2.0.4]: https://github.com/sebastianbergmann/php-file-iterator/compare/2.0.3...2.0.4
[2.0.3]: https://github.com/sebastianbergmann/php-file-iterator/compare/2.0.2...2.0.3
[2.0.2]: https://github.com/sebastianbergmann/php-file-iterator/compare/2.0.1...2.0.2
[2.0.1]: https://github.com/sebastianbergmann/php-file-iterator/compare/2.0.0...2.0.1
[2.0.0]: https://github.com/sebastianbergmann/php-file-iterator/compare/1.4.5...2.0.0
[1.4.5]: https://github.com/sebastianbergmann/php-file-iterator/compare/1.4.4...1.4.5
[1.4.4]: https://github.com/sebastianbergmann/php-file-iterator/compare/1.4.3...1.4.4
[1.4.3]: https://github.com/sebastianbergmann/php-file-iterator/compare/1.4.2...1.4.3
[1.4.2]: https://github.com/sebastianbergmann/php-file-iterator/compare/1.4.1...1.4.2
[1.4.1]: https://github.com/sebastianbergmann/php-file-iterator/compare/1.4.0...1.4.1
