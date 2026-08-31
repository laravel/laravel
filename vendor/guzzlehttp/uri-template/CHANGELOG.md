# Changelog

All notable changes to `uri-template` will be documented in this file.

Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## v1.0.10 - 2026-07-17

### Fixed

- Fixed prefix modifiers counting Unicode code points and pct-encoded characters instead of bytes

## v1.0.9 - 2026-07-08

### Changed

- Pass explicit trim characters ahead of the PHP 8.6 trim default change

## v1.0.8 - 2026-06-23

### Fixed

- Report PCRE errors when URI template value encoding fails

## v1.0.7 - 2026-06-12

### Fixed

- Fixed the operator's leading character being omitted when defined variables expand to empty strings
- Fixed non-finite float values emitting coercion warnings on PHP 8.5

## v1.0.6 - 2026-05-23

### Fixed

- Fixed empty nested arrays adding empty components to exploded query expansions
- Fixed nested query array keys being double-encoded during exploded query expansion
- Fixed reserved and fragment expansion preserving existing pct-encoded triplets in variable values

## v1.0.5 - 2025-08-22

### Changed

- Officially support PHP 8.5

## v1.0.4 - 2025-02-03

### Changed

- Officially support PHP 8.4

## v1.0.3 - 2023-12-03

### Changed

- Updated link to RFC 6570

## v1.0.2 - 2023-08-27

### Changed

- Officially support PHP 8.2 and 8.3

### Fixed

- Fixed using `0` as an expanded value

## v1.0.1 - 2021-10-07

### Changed

- Officially support PHP 8.1

## v1.0.0 - 2021-08-14

### Changed

- Dropped support for PHP 7.1

## v0.2.0 - 2020-07-21

### Added

- Support PHP 7.1 and 8.0

### Changed

- Renamed `GuzzleHttp\Utility\` to `GuzzleHttp\UriTemplate\`

### Fixed

- Delegate RFC 3986 query string encoding to PHP
- Fixed some bugs when parts ofs values are not strings

## v0.1.1 - 2020-06-30

### Fixed

- Fixed an error due to strict_types [d47d1b0a8e78a3fac1cd0f69d675fc9e06771ac8](https://github.com/guzzle/uri-template/commit/d47d1b0a8e78a3fac1cd0f69d675fc9e06771ac8)

## v0.1.0 - 2020-06-30

### Added
- Moved the `UriTemplate` class in this package
