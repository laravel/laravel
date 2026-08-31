# ChangeLog

All notable changes are documented in this file using the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## [6.0.2] - 2024-07-03

### Changed

* This project now uses PHPStan instead of Psalm for static analysis

## [6.0.1] - 2024-03-02

### Changed

* Do not use implicitly nullable parameters

## [6.0.0] - 2024-02-02

### Removed

* `SebastianBergmann\Diff\Chunk::getStart()`, `SebastianBergmann\Diff\Chunk::getStartRange()`, `SebastianBergmann\Diff\Chunk::getEnd()`, `SebastianBergmann\Diff\Chunk::getEndRange()`, and `SebastianBergmann\Diff\Chunk::getLines()`
* `SebastianBergmann\Diff\Diff::getFrom()`, `SebastianBergmann\Diff\Diff::getTo()`, and `SebastianBergmann\Diff\Diff::getChunks()`
* `SebastianBergmann\Diff\Line::getContent()` and `SebastianBergmann\Diff\Diff::getType()`
* Removed support for PHP 8.1

## [5.1.1] - 2024-03-02

### Changed

* Do not use implicitly nullable parameters

## [5.1.0] - 2023-12-22

### Added

* `SebastianBergmann\Diff\Chunk::start()`, `SebastianBergmann\Diff\Chunk::startRange()`, `SebastianBergmann\Diff\Chunk::end()`, `SebastianBergmann\Diff\Chunk::endRange()`, and `SebastianBergmann\Diff\Chunk::lines()`
* `SebastianBergmann\Diff\Diff::from()`, `SebastianBergmann\Diff\Diff::to()`, and `SebastianBergmann\Diff\Diff::chunks()`
* `SebastianBergmann\Diff\Line::content()` and `SebastianBergmann\Diff\Diff::type()`
* `SebastianBergmann\Diff\Line::isAdded()`,`SebastianBergmann\Diff\Line::isRemoved()`, and `SebastianBergmann\Diff\Line::isUnchanged()`

### Changed

* `SebastianBergmann\Diff\Diff` now implements `IteratorAggregate`, iterating over it yields the aggregated `SebastianBergmann\Diff\Chunk` objects
* `SebastianBergmann\Diff\Chunk` now implements `IteratorAggregate`, iterating over it yields the aggregated `SebastianBergmann\Diff\Line` objects

### Deprecated

* `SebastianBergmann\Diff\Chunk::getStart()`, `SebastianBergmann\Diff\Chunk::getStartRange()`, `SebastianBergmann\Diff\Chunk::getEnd()`, `SebastianBergmann\Diff\Chunk::getEndRange()`, and `SebastianBergmann\Diff\Chunk::getLines()`
* `SebastianBergmann\Diff\Diff::getFrom()`, `SebastianBergmann\Diff\Diff::getTo()`, and `SebastianBergmann\Diff\Diff::getChunks()`
* `SebastianBergmann\Diff\Line::getContent()` and `SebastianBergmann\Diff\Diff::getType()`

## [5.0.3] - 2023-05-01

### Changed

* [#119](https://github.com/sebastianbergmann/diff/pull/119): Improve performance of `TimeEfficientLongestCommonSubsequenceCalculator`

## [5.0.2] - 2023-05-01

### Changed

* [#118](https://github.com/sebastianbergmann/diff/pull/118): Improve performance of `MemoryEfficientLongestCommonSubsequenceCalculator`

## [5.0.1] - 2023-03-23

### Fixed

* [#115](https://github.com/sebastianbergmann/diff/pull/115): `Parser::parseFileDiff()` does not handle diffs correctly that only add lines or only remove lines

## [5.0.0] - 2023-02-03

### Changed

* Passing a `DiffOutputBuilderInterface` instance to `Differ::__construct()` is no longer optional

### Removed

* Removed support for PHP 7.3, PHP 7.4, and PHP 8.0

## [4.0.4] - 2020-10-26

### Fixed

* `SebastianBergmann\Diff\Exception` now correctly extends `\Throwable`

## [4.0.3] - 2020-09-28

### Changed

* Changed PHP version constraint in `composer.json` from `^7.3 || ^8.0` to `>=7.3`

## [4.0.2] - 2020-06-30

### Added

* This component is now supported on PHP 8

## [4.0.1] - 2020-05-08

### Fixed

* [#99](https://github.com/sebastianbergmann/diff/pull/99): Regression in unified diff output of identical strings

## [4.0.0] - 2020-02-07

### Removed

* Removed support for PHP 7.1 and PHP 7.2

## [3.0.2] - 2019-02-04

### Changed

* `Chunk::setLines()` now ensures that the `$lines` array only contains `Line` objects

## [3.0.1] - 2018-06-10

### Fixed

* Removed `"minimum-stability": "dev",` from `composer.json`

## [3.0.0] - 2018-02-01

* The `StrictUnifiedDiffOutputBuilder` implementation of the `DiffOutputBuilderInterface` was added

### Changed

* The default `DiffOutputBuilderInterface` implementation now generates context lines (unchanged lines)

### Removed

* Removed support for PHP 7.0

### Fixed

* [#70](https://github.com/sebastianbergmann/diff/issues/70): Diffing of arrays no longer works

## [2.0.1] - 2017-08-03

### Fixed

* [#66](https://github.com/sebastianbergmann/diff/pull/66): Restored backwards compatibility for PHPUnit 6.1.4, 6.2.0, 6.2.1, 6.2.2, and 6.2.3

## [2.0.0] - 2017-07-11 [YANKED]

### Added

* [#64](https://github.com/sebastianbergmann/diff/pull/64): Show line numbers for chunks of a diff

### Removed

* This component is no longer supported on PHP 5.6

[6.0.2]: https://github.com/sebastianbergmann/diff/compare/6.0.1...6.0.2
[6.0.1]: https://github.com/sebastianbergmann/diff/compare/6.0.0...6.0.1
[6.0.0]: https://github.com/sebastianbergmann/diff/compare/5.1...6.0.0
[5.1.1]: https://github.com/sebastianbergmann/diff/compare/5.1.0...5.1.1
[5.1.0]: https://github.com/sebastianbergmann/diff/compare/5.0.3...5.1.0
[5.0.3]: https://github.com/sebastianbergmann/diff/compare/5.0.2...5.0.3
[5.0.2]: https://github.com/sebastianbergmann/diff/compare/5.0.1...5.0.2
[5.0.1]: https://github.com/sebastianbergmann/diff/compare/5.0.0...5.0.1
[5.0.0]: https://github.com/sebastianbergmann/diff/compare/4.0.4...5.0.0
[4.0.4]: https://github.com/sebastianbergmann/diff/compare/4.0.3...4.0.4
[4.0.3]: https://github.com/sebastianbergmann/diff/compare/4.0.2...4.0.3
[4.0.2]: https://github.com/sebastianbergmann/diff/compare/4.0.1...4.0.2
[4.0.1]: https://github.com/sebastianbergmann/diff/compare/4.0.0...4.0.1
[4.0.0]: https://github.com/sebastianbergmann/diff/compare/3.0.2...4.0.0
[3.0.2]: https://github.com/sebastianbergmann/diff/compare/3.0.1...3.0.2
[3.0.1]: https://github.com/sebastianbergmann/diff/compare/3.0.0...3.0.1
[3.0.0]: https://github.com/sebastianbergmann/diff/compare/2.0...3.0.0
[2.0.1]: https://github.com/sebastianbergmann/diff/compare/c341c98ce083db77f896a0aa64f5ee7652915970...2.0.1
[2.0.0]: https://github.com/sebastianbergmann/diff/compare/1.4...c341c98ce083db77f896a0aa64f5ee7652915970
