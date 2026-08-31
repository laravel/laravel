# ChangeLog

All notable changes are documented in this file using the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## [4.0.1] - 2024-07-03

### Changed

* This project now uses PHPStan instead of Psalm for static analysis

## [4.0.0] - 2024-02-02

### Removed

* The `SebastianBergmann\Template\Template::setFile()` method has been removed
* This component is no longer supported on PHP 8.1

## [3.0.1] - 2023-08-31

### Changed

* Warnings from `file_put_contents()` are now suppressed

## [3.0.0] - 2023-02-03

### Removed

* This component is no longer supported on PHP 7.3, PHP 7.4 and PHP 8.0

## [2.0.4] - 2020-10-26

### Fixed

* `SebastianBergmann\Template\Exception` now correctly extends `\Throwable`

## [2.0.3] - 2020-09-28

### Changed

* Changed PHP version constraint in `composer.json` from `^7.3 || ^8.0` to `>=7.3`

## [2.0.2] - 2020-06-26

### Added

* This component is now supported on PHP 8

## [2.0.1] - 2020-06-15

### Changed

* Tests etc. are now ignored for archive exports

## [2.0.0] - 2020-02-07

### Changed

* The `Text_Template` class was renamed to `SebastianBergmann\Template\Template`

### Removed

* Removed support for PHP 5.3, PHP 5.4, PHP 5.5, PHP 5.6, PHP 7.0, PHP 7.1, and PHP 7.2 

[4.0.1]: https://github.com/sebastianbergmann/php-text-template/compare/4.0.0...4.0.1
[4.0.0]: https://github.com/sebastianbergmann/php-text-template/compare/3.0...4.0.0
[3.0.1]: https://github.com/sebastianbergmann/php-text-template/compare/3.0.0...3.0.1
[3.0.0]: https://github.com/sebastianbergmann/php-text-template/compare/2.0.4...3.0.0
[2.0.4]: https://github.com/sebastianbergmann/php-text-template/compare/2.0.3...2.0.4
[2.0.3]: https://github.com/sebastianbergmann/php-text-template/compare/2.0.2...2.0.3
[2.0.2]: https://github.com/sebastianbergmann/php-text-template/compare/2.0.1...2.0.2
[2.0.1]: https://github.com/sebastianbergmann/php-text-template/compare/2.0.0...2.0.1
[2.0.0]: https://github.com/sebastianbergmann/php-text-template/compare/1.2.1...2.0.0
