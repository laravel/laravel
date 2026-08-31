# Changes in sebastianbergmann/environment

All notable changes in `sebastianbergmann/environment` are documented in this file using the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## [7.2.1] - 2025-05-21

### Fixed

* Take Xdebug mode into account for `Runtime::canCollectCodeCoverage()`

## [7.2.0] - 2024-07-03

### Changed

* Synced `Console::hasColorSupport()` with Symfony's `StreamOutput::hasColorSupport()` implementation
* Removed code left over from a time before PHP 5.4 and when HHVM was still supported
* This project now uses PHPStan instead of Psalm for static analysis

### Deprecated

* The `Runtime::getBinary()` method is now deprecated, use `escapeshellarg(PHP_BINARY)` instead
* The `Runtime::getRawBinary()` method is now deprecated, use the `PHP_BINARY` constant instead

## [7.1.0] - 2024-03-23

### Added

* [#72](https://github.com/sebastianbergmann/environment/pull/72): `Runtime::getRawBinary()`

## [7.0.0] - 2024-02-02

### Removed

* This component is no longer supported on PHP 8.1

## [6.1.1] - 2024-MM-DD

### Changed

* Synced `Console::hasColorSupport()` with Symfony's `StreamOutput::hasColorSupport()` implementation

## [6.1.0] - 2024-03-23

### Added

* [#72](https://github.com/sebastianbergmann/environment/pull/72): `Runtime::getRawBinary()`

## [6.0.1] - 2023-04-11

### Fixed

* [#68](https://github.com/sebastianbergmann/environment/pull/68): The Just-in-Time compiler is disabled when `opcache.jit_buffer_size` is set to `0`
* [#70](https://github.com/sebastianbergmann/environment/pull/70): The first `0` of `opcache.jit` only disables CPU-specific optimizations, not the Just-in-Time compiler itself

## [6.0.0] - 2023-02-03

### Removed

* Removed `SebastianBergmann\Environment\OperatingSystem::getFamily()` because this component is no longer supported on PHP versions that do not have `PHP_OS_FAMILY`
* Removed `SebastianBergmann\Environment\Runtime::isHHVM()`
* This component is no longer supported on PHP 7.3, PHP 7.4, and PHP 8.0

## [5.1.5] - 2022-MM-DD

### Fixed

* [#59](https://github.com/sebastianbergmann/environment/issues/59): Wrong usage of `stream_isatty()`, `fstat()` used without checking whether the function is available

## [5.1.4] - 2022-04-03

### Fixed

* [#63](https://github.com/sebastianbergmann/environment/pull/63): `Runtime::getCurrentSettings()` does not correctly process INI settings

## [5.1.3] - 2020-09-28

### Changed

* Changed PHP version constraint in `composer.json` from `^7.3 || ^8.0` to `>=7.3`

## [5.1.2] - 2020-06-26

### Added

* This component is now supported on PHP 8

## [5.1.1] - 2020-06-15

### Changed

* Tests etc. are now ignored for archive exports

## [5.1.0] - 2020-04-14

### Added

* `Runtime::performsJustInTimeCompilation()` returns `true` if PHP 8's JIT is active, `false` otherwise

## [5.0.2] - 2020-03-31

### Fixed

* [#55](https://github.com/sebastianbergmann/environment/issues/55): `stty` command is executed even if no tty is available

## [5.0.1] - 2020-02-19

### Changed

* `Runtime::getNameWithVersionAndCodeCoverageDriver()` now prioritizes PCOV over Xdebug when both extensions are loaded (just like php-code-coverage does)

## [5.0.0] - 2020-02-07

### Removed

* This component is no longer supported on PHP 7.1 and PHP 7.2

## [4.2.3] - 2019-11-20

### Changed

* [#50](https://github.com/sebastianbergmann/environment/pull/50): Windows improvements to console capabilities

### Fixed

* [#49](https://github.com/sebastianbergmann/environment/issues/49): Detection how OpCache handles docblocks does not work correctly when PHPDBG is used

## [4.2.2] - 2019-05-05

### Fixed

* [#44](https://github.com/sebastianbergmann/environment/pull/44): `TypeError` in `Console::getNumberOfColumnsInteractive()`

## [4.2.1] - 2019-04-25

### Fixed

* Fixed an issue in `Runtime::getCurrentSettings()`

## [4.2.0] - 2019-04-25

### Added

* [#36](https://github.com/sebastianbergmann/environment/pull/36): `Runtime::getCurrentSettings()`

## [4.1.0] - 2019-02-01

### Added

* Implemented `Runtime::getNameWithVersionAndCodeCoverageDriver()` method
* [#34](https://github.com/sebastianbergmann/environment/pull/34): Support for PCOV extension

## [4.0.2] - 2019-01-28

### Fixed

* [#33](https://github.com/sebastianbergmann/environment/issues/33): `Runtime::discardsComments()` returns true too eagerly

### Removed

* Removed support for Zend Optimizer+ in `Runtime::discardsComments()`

## [4.0.1] - 2018-11-25

### Fixed

* [#31](https://github.com/sebastianbergmann/environment/issues/31): Regressions in `Console` class

## [4.0.0] - 2018-10-23 [YANKED]

### Fixed

* [#25](https://github.com/sebastianbergmann/environment/pull/25): `Console::hasColorSupport()` does not work on Windows

### Removed

* This component is no longer supported on PHP 7.0

## [3.1.0] - 2017-07-01

### Added

* [#21](https://github.com/sebastianbergmann/environment/issues/21): Equivalent of `PHP_OS_FAMILY` (for PHP < 7.2) 

## [3.0.4] - 2017-06-20

### Fixed

* [#20](https://github.com/sebastianbergmann/environment/pull/20): PHP 7 mode of HHVM not forced

## [3.0.3] - 2017-05-18

### Fixed

* [#18](https://github.com/sebastianbergmann/environment/issues/18): `Uncaught TypeError: preg_match() expects parameter 2 to be string, null given`

## [3.0.2] - 2017-04-21

### Fixed

* [#17](https://github.com/sebastianbergmann/environment/issues/17): `Uncaught TypeError: trim() expects parameter 1 to be string, boolean given`

## [3.0.1] - 2017-04-21

### Fixed

* Fixed inverted logic in `Runtime::discardsComments()`

## [3.0.0] - 2017-04-21

### Added

* Implemented `Runtime::discardsComments()` for querying whether the PHP runtime discards annotations

### Removed

* This component is no longer supported on PHP 5.6

[7.2.1]: https://github.com/sebastianbergmann/environment/compare/7.2.0...7.2.1
[7.2.0]: https://github.com/sebastianbergmann/environment/compare/7.1.0...7.2.0
[7.1.0]: https://github.com/sebastianbergmann/environment/compare/7.0.0...7.1.0
[7.0.0]: https://github.com/sebastianbergmann/environment/compare/6.1...7.0.0
[6.1.1]: https://github.com/sebastianbergmann/environment/compare/6.1.0...6.1
[6.1.0]: https://github.com/sebastianbergmann/environment/compare/6.0.1...6.1.0
[6.0.1]: https://github.com/sebastianbergmann/environment/compare/6.0.0...6.0.1
[6.0.0]: https://github.com/sebastianbergmann/environment/compare/5.1.5...6.0.0
[5.1.5]: https://github.com/sebastianbergmann/environment/compare/5.1.4...5.1.5
[5.1.4]: https://github.com/sebastianbergmann/environment/compare/5.1.3...5.1.4
[5.1.3]: https://github.com/sebastianbergmann/environment/compare/5.1.2...5.1.3
[5.1.2]: https://github.com/sebastianbergmann/environment/compare/5.1.1...5.1.2
[5.1.1]: https://github.com/sebastianbergmann/environment/compare/5.1.0...5.1.1
[5.1.0]: https://github.com/sebastianbergmann/environment/compare/5.0.2...5.1.0
[5.0.2]: https://github.com/sebastianbergmann/environment/compare/5.0.1...5.0.2
[5.0.1]: https://github.com/sebastianbergmann/environment/compare/5.0.0...5.0.1
[5.0.0]: https://github.com/sebastianbergmann/environment/compare/4.2.3...5.0.0
[4.2.3]: https://github.com/sebastianbergmann/environment/compare/4.2.2...4.2.3
[4.2.2]: https://github.com/sebastianbergmann/environment/compare/4.2.1...4.2.2
[4.2.1]: https://github.com/sebastianbergmann/environment/compare/4.2.0...4.2.1
[4.2.0]: https://github.com/sebastianbergmann/environment/compare/4.1.0...4.2.0
[4.1.0]: https://github.com/sebastianbergmann/environment/compare/4.0.2...4.1.0
[4.0.2]: https://github.com/sebastianbergmann/environment/compare/4.0.1...4.0.2
[4.0.1]: https://github.com/sebastianbergmann/environment/compare/66691f8e2dc4641909166b275a9a4f45c0e89092...4.0.1
[4.0.0]: https://github.com/sebastianbergmann/environment/compare/3.1.0...66691f8e2dc4641909166b275a9a4f45c0e89092
[3.1.0]: https://github.com/sebastianbergmann/environment/compare/3.0...3.1.0
[3.0.4]: https://github.com/sebastianbergmann/environment/compare/3.0.3...3.0.4
[3.0.3]: https://github.com/sebastianbergmann/environment/compare/3.0.2...3.0.3
[3.0.2]: https://github.com/sebastianbergmann/environment/compare/3.0.1...3.0.2
[3.0.1]: https://github.com/sebastianbergmann/environment/compare/3.0.0...3.0.1
[3.0.0]: https://github.com/sebastianbergmann/environment/compare/2.0...3.0.0

