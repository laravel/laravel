# ChangeLog

All notable changes are documented in this file using the [Keep a CHANGELOG](https://keepachangelog.com/) principles.

## [5.0.1] - 2024-07-03

### Changed

* This project now uses PHPStan instead of Psalm for static analysis

## [5.0.0] - 2024-02-02

### Removed

* This component is no longer supported on PHP 8.1

## [4.0.0] - 2023-02-03

### Removed

* This component is no longer supported on PHP 7.3, PHP 7.4 and PHP 8.0

## [3.1.1] - 2020-09-28

### Changed

* Changed PHP version constraint in `composer.json` from `^7.3 || ^8.0` to `>=7.3`

## [3.1.0] - 2020-08-06

### Changed

* [#14](https://github.com/sebastianbergmann/php-invoker/pull/14): Clear alarm in `finally` block

## [3.0.2] - 2020-06-26

### Added

* This component is now supported on PHP 8

## [3.0.1] - 2020-06-15

### Changed

* Tests etc. are now ignored for archive exports

## [3.0.0] - 2020-02-07

### Added

* Added `canInvokeWithTimeout()` method to check requirements for the functionality provided by this component to work

### Changed

* Moved `"ext-pcntl": "*"` requirement from `require` to `suggest` so that this component can be installed even if `ext/pcntl` is not available
* `invoke()` now raises an exception when the requirements for the functionality provided by this component to work are not met

### Removed

* This component is no longer supported on PHP 7.1 and PHP 7.2

[5.0.1]: https://github.com/sebastianbergmann/php-invoker/compare/5.0.1...5.0.1
[5.0.0]: https://github.com/sebastianbergmann/php-invoker/compare/4.0...5.0.0
[4.0.0]: https://github.com/sebastianbergmann/php-invoker/compare/3.1.1...4.0.0
[3.1.1]: https://github.com/sebastianbergmann/php-invoker/compare/3.1.0...3.1.1
[3.1.0]: https://github.com/sebastianbergmann/php-invoker/compare/3.0.2...3.1.0
[3.0.2]: https://github.com/sebastianbergmann/php-invoker/compare/3.0.1...3.0.2
[3.0.1]: https://github.com/sebastianbergmann/php-invoker/compare/3.0.0...3.0.1
[3.0.0]: https://github.com/sebastianbergmann/php-invoker/compare/2.0.0...3.0.0
