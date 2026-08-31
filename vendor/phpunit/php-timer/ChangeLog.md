# ChangeLog

All notable changes are documented in this file using the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## [7.0.1] - 2024-07-03

### Changed

* This project now uses PHPStan instead of Psalm for static analysis

## [7.0.0] - 2024-02-02

### Removed

* This component is no longer supported on PHP 8.1

## [6.0.0] - 2023-02-03

### Removed

* This component is no longer supported on PHP 7.3, PHP 7.4 and PHP 8.0

## [5.0.3] - 2020-10-26

### Fixed

* `SebastianBergmann\Timer\Exception` now correctly extends `\Throwable`

## [5.0.2] - 2020-09-28

### Changed

* Changed PHP version constraint in `composer.json` from `^7.3 || ^8.0` to `>=7.3`

## [5.0.1] - 2020-06-26

### Added

* This component is now supported on PHP 8

## [5.0.0] - 2020-06-07

### Changed

* Parameter type for `SebastianBergmann\Timer\Duration::fromMicroseconds()` was changed from `int` to `float` 
* Parameter type for `SebastianBergmann\Timer\Duration::fromNanoseconds()` was changed from `int` to `float`
* Return type for `SebastianBergmann\Timer\Duration::asNanoseconds()` was changed from `int` to `float`

### Fixed

* [#31](https://github.com/sebastianbergmann/php-timer/issues/31): Type Error on 32-bit systems (where `hrtime()` returns `float` instead of `int`)

## [4.0.0] - 2020-06-01

### Added

* Introduced `Duration` value object for encapsulating a duration with nanosecond granularity
* Introduced `ResourceUsageFormatter` object for formatting resource usage with option to explicitly pass a duration (instead of looking at the unreliable `$_SERVER['REQUEST_TIME_FLOAT']` variable)

### Changed

* The methods of `Timer` are no longer static
* `Timer::stop()` now returns a `Duration` value object

### Removed

* Functionality that is now implemented in `Duration` and `ResourceUsageFormatter` has been removed from `Timer`

## [3.1.4] - 2020-04-20

### Changed

* `Timer::timeSinceStartOfRequest()` no longer tries `$_SERVER['REQUEST_TIME']` when `$_SERVER['REQUEST_TIME_FLOAT']` is not available (`$_SERVER['REQUEST_TIME_FLOAT']` was added in PHP 5.4 and this library requires PHP 7.3)
* Improved exception messages when `$_SERVER['REQUEST_TIME_FLOAT']` is not set or is not of type `float`

### Changed

## [3.1.3] - 2020-04-20

### Changed

* `Timer::timeSinceStartOfRequest()` now raises an exception if `$_SERVER['REQUEST_TIME_FLOAT']` does not contain a `float` (or `$_SERVER['REQUEST_TIME']` does not contain an `int`)

## [3.1.2] - 2020-04-17

### Changed

* Improved the fix for [#30](https://github.com/sebastianbergmann/php-timer/issues/30) and restored usage of `hrtime()`

## [3.1.1] - 2020-04-17

### Fixed

* [#30](https://github.com/sebastianbergmann/php-timer/issues/30): Resolution of time returned by `Timer::stop()` is different than before (this reverts using `hrtime()` instead of `microtime()`)

## [3.1.0] - 2020-04-17

### Added

* `Timer::secondsToShortTimeString()` as alternative to `Timer::secondsToTimeString()`

### Changed

* `Timer::start()` and `Timer::stop()` now use `hrtime()` (high resolution monotonic timer) instead of `microtime()`
* `Timer::timeSinceStartOfRequest()` now uses `Timer::secondsToShortTimeString()` for time formatting
* Improved formatting of `Timer::secondsToTimeString()` result

## [3.0.0] - 2020-02-07

### Removed

* This component is no longer supported on PHP 7.1 and PHP 7.2

## [2.1.2] - 2019-06-07

### Fixed

* [#21](https://github.com/sebastianbergmann/php-timer/pull/21): Formatting of memory consumption does not work on 32bit systems

## [2.1.1] - 2019-02-20

### Changed

* Improved formatting of memory consumption for `resourceUsage()`

## [2.1.0] - 2019-02-20

### Changed

* Improved formatting of memory consumption for `resourceUsage()`

## [2.0.0] - 2018-02-01

### Changed

* This component now uses namespaces

### Removed

* This component is no longer supported on PHP 5.3, PHP 5.4, PHP 5.5, PHP 5.6, and PHP 7.0

[7.0.1]: https://github.com/sebastianbergmann/php-timer/compare/7.0.0...7.0.1
[7.0.0]: https://github.com/sebastianbergmann/php-timer/compare/6.0...7.0.0
[6.0.0]: https://github.com/sebastianbergmann/php-timer/compare/5.0.3...6.0.0
[5.0.3]: https://github.com/sebastianbergmann/php-timer/compare/5.0.2...5.0.3
[5.0.2]: https://github.com/sebastianbergmann/php-timer/compare/5.0.1...5.0.2
[5.0.1]: https://github.com/sebastianbergmann/php-timer/compare/5.0.0...5.0.1
[5.0.0]: https://github.com/sebastianbergmann/php-timer/compare/4.0.0...5.0.0
[4.0.0]: https://github.com/sebastianbergmann/php-timer/compare/3.1.4...4.0.0
[3.1.4]: https://github.com/sebastianbergmann/php-timer/compare/3.1.3...3.1.4
[3.1.3]: https://github.com/sebastianbergmann/php-timer/compare/3.1.2...3.1.3
[3.1.2]: https://github.com/sebastianbergmann/php-timer/compare/3.1.1...3.1.2
[3.1.1]: https://github.com/sebastianbergmann/php-timer/compare/3.1.0...3.1.1
[3.1.0]: https://github.com/sebastianbergmann/php-timer/compare/3.0.0...3.1.0
[3.0.0]: https://github.com/sebastianbergmann/php-timer/compare/2.1.2...3.0.0
[2.1.2]: https://github.com/sebastianbergmann/php-timer/compare/2.1.1...2.1.2
[2.1.1]: https://github.com/sebastianbergmann/php-timer/compare/2.1.0...2.1.1
[2.1.0]: https://github.com/sebastianbergmann/php-timer/compare/2.0.0...2.1.0
[2.0.0]: https://github.com/sebastianbergmann/php-timer/compare/1.0.9...2.0.0
