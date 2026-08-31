# Change Log
All notable changes to this project will be documented in this file.
Updates should follow the [Keep a CHANGELOG](https://keepachangelog.com/) principles.

## [Unreleased][unreleased]

## [1.2.0] - 2022-12-11

### Changed

- Values can now be set prior to the corresponding schema being registered.
- `exists()` and `get()` now only trigger validation for the relevant schema, not the entire config at once.

## [1.1.1] - 2021-08-14

### Changed

 - Bumped the minimum version of dflydev/dot-access-data for PHP 8.1 support

## [1.1.0] - 2021-06-19

### Changed

- Bumped the minimum PHP version to 7.4+
- Bumped the minimum version of nette/schema to 1.2.0

## [1.0.1] - 2021-05-31

### Fixed

- Fixed the `ConfigurationExceptionInterface` marker interface not extending `Throwable` (#2)

## [1.0.0] - 2021-05-31

Initial release! 🎉

[unreleased]: https://github.com/thephpleague/config/compare/v1.2.0...main
[1.2.0]: https://github.com/thephpleague/config/compare/v1.1.1...v.1.2.0
[1.1.1]: https://github.com/thephpleague/config/compare/v1.1.0...v1.1.1
[1.1.0]: https://github.com/thephpleague/config/compare/v1.0.1...v1.1.0
[1.0.1]: https://github.com/thephpleague/config/compare/v1.0.0...v1.0.1
[1.0.0]: https://github.com/thephpleague/config/releases/tag/v1.0.0
