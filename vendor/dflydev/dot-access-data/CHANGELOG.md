# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [3.0.3] - 2024-07-08

### Fixed

 - Fixed PHP 8.4 deprecation notices (#47)

## [3.0.2] - 2022-10-27

### Fixed

 - Added missing return types to docblocks (#44, #45)

## [3.0.1] - 2021-08-13

### Added

 - Adds ReturnTypeWillChange to suppress PHP 8.1 warnings (#40)

## [3.0.0] - 2021-01-01

### Added
 - Added support for both `.` and `/`-delimited key paths (#24)
 - Added parameter and return types to everything; enabled strict type checks (#18)
 - Added new exception classes to better identify certain types of errors (#20)
 - `Data` now implements `ArrayAccess` (#17)
 - Added ability to merge non-associative array values (#31, #32)

### Changed
 - All thrown exceptions are now instances or subclasses of `DataException` (#20)
 - Calling `get()` on a missing key path without providing a default will throw a `MissingPathException` instead of returning `null` (#29)
 - Bumped supported PHP versions to 7.1 - 8.x (#18)

### Fixed
 - Fixed incorrect merging of array values into string values (#32)
 - Fixed `get()` method behaving as if keys with `null` values didn't exist

## [2.0.0] - 2017-12-21

### Changed
 - Bumped supported PHP versions to 7.0 - 7.4 (#12)
 - Switched to PSR-4 autoloading

## [1.1.0] - 2017-01-20

### Added
 - Added new `has()` method to check for the existence of the given key (#4, #7)

## [1.0.1] - 2015-08-12

### Added
 - Added new optional `$default` parameter to the `get()` method (#2)

## [1.0.0] - 2012-07-17

**Initial release!**

[Unreleased]: https://github.com/dflydev/dflydev-dot-access-data/compare/v3.0.3...main
[3.0.3]: https://github.com/dflydev/dflydev-dot-access-data/compare/v3.0.2...v3.0.3
[3.0.2]: https://github.com/dflydev/dflydev-dot-access-data/compare/v3.0.1...v3.0.2
[3.0.1]: https://github.com/dflydev/dflydev-dot-access-data/compare/v3.0.0...v3.0.1
[3.0.0]: https://github.com/dflydev/dflydev-dot-access-data/compare/v2.0.0...v3.0.0
[2.0.0]: https://github.com/dflydev/dflydev-dot-access-data/compare/v1.1.0...v2.0.0
[1.1.0]: https://github.com/dflydev/dflydev-dot-access-data/compare/v1.0.1...v1.1.0
[1.0.1]: https://github.com/dflydev/dflydev-dot-access-data/compare/v1.0.0...v1.0.1
[1.0.0]: https://github.com/dflydev/dflydev-dot-access-data/releases/tag/v1.0.0
