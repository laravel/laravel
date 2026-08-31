# ChangeLog

All notable changes are documented in this file using the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## [3.0.3] - 2025-03-19

### Fixed

* [#9](https://github.com/sebastianbergmann/code-unit/issues/9): `Mapper::stringToCodeUnits('Foo::bar')` does not find method `Foo::bar` when a function named `\bar` is defined

## [3.0.2] - 2024-12-12

### Fixed

* [#8](https://github.com/sebastianbergmann/code-unit/issues/8): `Mapper::stringToCodeUnits()` does not consider "inheritance" between traits

## [3.0.1] - 2024-07-03

### Changed

* This project now uses PHPStan instead of Psalm for static analysis

## [3.0.0] - 2024-02-02

### Removed

* This component is no longer supported on PHP 8.1

## [2.0.0] - 2023-02-03

### Added

* Added `SebastianBergmann\CodeUnit\FileUnit` value object that represents a sourcecode file 

### Removed

* `SebastianBergmann\CodeUnit\CodeUnitCollection::fromArray()` has been removed
* `SebastianBergmann\CodeUnit\Mapper::stringToCodeUnits()` no longer supports `ClassName<*>`
* This component is no longer supported on PHP 7.3, PHP 7.4, and PHP 8.0

## [1.0.8] - 2020-10-26

### Fixed

* `SebastianBergmann\CodeUnit\Exception` now correctly extends `\Throwable`

## [1.0.7] - 2020-10-02

### Fixed

* `SebastianBergmann\CodeUnit\Mapper::stringToCodeUnits()` no longer attempts to create `CodeUnit` objects for code units that are not declared in userland

## [1.0.6] - 2020-09-28

### Changed

* Changed PHP version constraint in `composer.json` from `^7.3 || ^8.0` to `>=7.3`

## [1.0.5] - 2020-06-26

### Fixed

* [#3](https://github.com/sebastianbergmann/code-unit/issues/3): Regression in 1.0.4

## [1.0.4] - 2020-06-26

### Added

* This component is now supported on PHP 8

## [1.0.3] - 2020-06-15

### Changed

* Tests etc. are now ignored for archive exports

## [1.0.2] - 2020-04-30

### Fixed

* `Mapper::stringToCodeUnits()` raised the wrong exception for `Class::method` when a class named `Class` exists but does not have a method named `method`

## [1.0.1] - 2020-04-27

### Fixed

* [#2](https://github.com/sebastianbergmann/code-unit/issues/2): `Mapper::stringToCodeUnits()` breaks when `ClassName<extended>` is used for class that extends built-in class

## [1.0.0] - 2020-03-30

* Initial release

[3.0.3]: https://github.com/sebastianbergmann/code-unit/compare/3.0.2...3.0.3
[3.0.2]: https://github.com/sebastianbergmann/code-unit/compare/3.0.1...3.0.2
[3.0.1]: https://github.com/sebastianbergmann/code-unit/compare/3.0.0...3.0.1
[3.0.0]: https://github.com/sebastianbergmann/code-unit/compare/2.0...3.0.0
[2.0.0]: https://github.com/sebastianbergmann/code-unit/compare/1.0.8...2.0.0
[1.0.8]: https://github.com/sebastianbergmann/code-unit/compare/1.0.7...1.0.8
[1.0.7]: https://github.com/sebastianbergmann/code-unit/compare/1.0.6...1.0.7
[1.0.6]: https://github.com/sebastianbergmann/code-unit/compare/1.0.5...1.0.6
[1.0.5]: https://github.com/sebastianbergmann/code-unit/compare/1.0.4...1.0.5
[1.0.4]: https://github.com/sebastianbergmann/code-unit/compare/1.0.3...1.0.4
[1.0.3]: https://github.com/sebastianbergmann/code-unit/compare/1.0.2...1.0.3
[1.0.2]: https://github.com/sebastianbergmann/code-unit/compare/1.0.1...1.0.2
[1.0.1]: https://github.com/sebastianbergmann/code-unit/compare/1.0.0...1.0.1
[1.0.0]: https://github.com/sebastianbergmann/code-unit/compare/530c3900e5db9bcb8516da545bef0d62536cedaa...1.0.0
