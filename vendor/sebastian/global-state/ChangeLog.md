# Changes in sebastian/global-state

All notable changes in `sebastian/global-state` are documented in this file using the [Keep a CHANGELOG](https://keepachangelog.com/) principles.

## [7.0.2] - 2024-07-03

### Changed

* This project now uses PHPStan instead of Psalm for static analysis

## [7.0.1] - 2024-03-02

### Changed

* Do not use implicitly nullable parameters

## [7.0.0] - 2024-02-02

### Removed

* This component is no longer supported on PHP 8.1

## [6.0.2] - 2024-03-02

### Changed

* Do not use implicitly nullable parameters

## [6.0.1] - 2023-07-19

### Changed

* Changed usage of `ReflectionProperty::setValue()` to be compatible with PHP 8.3

## [6.0.0] - 2023-02-03

### Changed

* Renamed `SebastianBergmann\GlobalState\ExcludeList::addStaticAttribute()` to `SebastianBergmann\GlobalState\ExcludeList::addStaticProperty()`
* Renamed `SebastianBergmann\GlobalState\ExcludeList::isStaticAttributeExcluded()` to `SebastianBergmann\GlobalState\ExcludeList::isStaticPropertyExcluded()`
* Renamed `SebastianBergmann\GlobalState\Restorer::restoreStaticAttributes()` to `SebastianBergmann\GlobalState\Restorer::restoreStaticProperties()`
* Renamed `SebastianBergmann\GlobalState\Snapshot::staticAttributes()` to `SebastianBergmann\GlobalState\Snapshot::staticProperties()`

### Removed

* Removed `SebastianBergmann\GlobalState\Restorer::restoreFunctions()`
* This component is no longer supported on PHP 7.3, PHP 7.4 and PHP 8.0

## [5.0.5] - 2022-02-14

### Fixed

* [#34](https://github.com/sebastianbergmann/global-state/pull/34): Uninitialised typed static properties are not handled correctly

## [5.0.4] - 2022-02-10

### Fixed

* The `$includeTraits` parameter of `SebastianBergmann\GlobalState\Snapshot::__construct()` is not respected

## [5.0.3] - 2021-06-11

### Changed

* `SebastianBergmann\GlobalState\CodeExporter::globalVariables()` now generates code that is compatible with PHP 8.1

## [5.0.2] - 2020-10-26

### Fixed

* `SebastianBergmann\GlobalState\Exception` now correctly extends `\Throwable`

## [5.0.1] - 2020-09-28

### Changed

* Changed PHP version constraint in `composer.json` from `^7.3 || ^8.0` to `>=7.3`

## [5.0.0] - 2020-08-07

### Changed

* The `SebastianBergmann\GlobalState\Blacklist` class has been renamed to `SebastianBergmann\GlobalState\ExcludeList`

## [4.0.0] - 2020-02-07

### Removed

* This component is no longer supported on PHP 7.2

## [3.0.2] - 2022-02-10

### Fixed

* The `$includeTraits` parameter of `SebastianBergmann\GlobalState\Snapshot::__construct()` is not respected

## [3.0.1] - 2020-11-30

### Changed

* Changed PHP version constraint in `composer.json` from `^7.2` to `>=7.2`

## [3.0.0] - 2019-02-01

### Changed

* `Snapshot::canBeSerialized()` now recursively checks arrays and object graphs for variables that cannot be serialized

### Removed

* This component is no longer supported on PHP 7.0 and PHP 7.1

[7.0.2]: https://github.com/sebastianbergmann/global-state/compare/7.0.1...7.0.2
[7.0.1]: https://github.com/sebastianbergmann/global-state/compare/7.0.0...7.0.1
[7.0.0]: https://github.com/sebastianbergmann/global-state/compare/6.0...7.0.0
[6.0.2]: https://github.com/sebastianbergmann/global-state/compare/6.0.1...6.0.2
[6.0.1]: https://github.com/sebastianbergmann/global-state/compare/6.0.0...6.0.1
[6.0.0]: https://github.com/sebastianbergmann/global-state/compare/5.0.5...6.0.0
[5.0.5]: https://github.com/sebastianbergmann/global-state/compare/5.0.4...5.0.5
[5.0.4]: https://github.com/sebastianbergmann/global-state/compare/5.0.3...5.0.4
[5.0.3]: https://github.com/sebastianbergmann/global-state/compare/5.0.2...5.0.3
[5.0.2]: https://github.com/sebastianbergmann/global-state/compare/5.0.1...5.0.2
[5.0.1]: https://github.com/sebastianbergmann/global-state/compare/5.0.0...5.0.1
[5.0.0]: https://github.com/sebastianbergmann/global-state/compare/4.0.0...5.0.0
[4.0.0]: https://github.com/sebastianbergmann/global-state/compare/3.0.2...4.0.0
[3.0.2]: https://github.com/sebastianbergmann/phpunit/compare/3.0.1...3.0.2
[3.0.1]: https://github.com/sebastianbergmann/phpunit/compare/3.0.0...3.0.1
[3.0.0]: https://github.com/sebastianbergmann/phpunit/compare/2.0.0...3.0.0

