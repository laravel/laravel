# Changelog

All notable changes to phar-io/manifest are documented in this file using the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## [2.0.4] - 03-03-2024

### Changed

- Make `EMail` an optional attribute for author
- Stick with PHP 7.2 compatibilty 
- Do not use implict nullable type (thanks @sebastianbergmann), this should make things work on PHP 8.4

## [2.0.3] - 20.07.2021

- Fixed PHP 7.2 / PHP 7.3 incompatibility introduced in previous release

## [2.0.2] - 20.07.2021

- Fixed PHP 8.1 deprecation notice

## [2.0.1] - 27.06.2020

This release now supports the use of PHP 7.2+ and ^8.0

## [2.0.0] - 10.05.2020

This release now requires PHP 7.2+

### Changed

- Upgraded to phar-io/version 3.0
    - Version strings `v1.2.3` will now be converted to valid semantic version strings `1.2.3`
    - Abreviated strings like `1.0` will get expaneded to `1.0.0`  

### Unreleased

[Unreleased]: https://github.com/phar-io/manifest/compare/2.1.0...HEAD
[2.1.0]: https://github.com/phar-io/manifest/compare/2.0.3...2.1.0
[2.0.3]: https://github.com/phar-io/manifest/compare/2.0.2...2.0.3
[2.0.2]: https://github.com/phar-io/manifest/compare/2.0.1...2.0.2
[2.0.1]: https://github.com/phar-io/manifest/compare/2.0.0...2.0.1
[2.0.0]: https://github.com/phar-io/manifest/compare/1.0.1...2.0.0
[1.0.3]: https://github.com/phar-io/manifest/compare/1.0.2...1.0.3
[1.0.2]: https://github.com/phar-io/manifest/compare/1.0.1...1.0.2
[1.0.1]: https://github.com/phar-io/manifest/compare/1.0.0...1.0.1
