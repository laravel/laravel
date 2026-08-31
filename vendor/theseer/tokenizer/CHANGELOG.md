# Changelog

All notable changes to Tokenizer are documented in this file using the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## [1.2.3] - 2024-03-03

### Changed

* Do not use implicitly nullable parameters

## [1.2.2] - 2023-11-20

### Fixed

* [#18](https://github.com/theseer/tokenizer/issues/18): Tokenizer fails on protobuf metadata files


## [1.2.1] - 2021-07-28

### Fixed

* [#13](https://github.com/theseer/tokenizer/issues/13): Fatal error when tokenizing files that contain only a single empty line


## [1.2.0] - 2020-07-13

This release is now PHP 8.0 compliant.

### Fixed

* Whitespace handling in general (only noticable in the intermediate `TokenCollection`) is now consitent  

### Changed

* Updated `Tokenizer` to deal with changed whitespace handling in PHP 8.0
  The XMLSerializer was unaffected.


## [1.1.3] - 2019-06-14

### Changed

* Ensure XMLSerializer can deal with empty token collections

### Fixed

* [#2](https://github.com/theseer/tokenizer/issues/2): Fatal error in infection / phpunit


## [1.1.2] - 2019-04-04

### Changed

* Reverted PHPUnit 8 test update to stay PHP 7.0 compliant


## [1.1.1] - 2019-04-03

### Fixed

* [#1](https://github.com/theseer/tokenizer/issues/1): Empty file causes invalid array read 

### Changed

* Tests should now be PHPUnit 8 compliant


## [1.1.0] - 2017-04-07

### Added

* Allow use of custom namespace for XML serialization


## [1.0.0] - 2017-04-05

Initial Release

[1.2.3]: https://github.com/theseer/tokenizer/compare/1.2.2...1.2.3
[1.2.2]: https://github.com/theseer/tokenizer/compare/1.2.1...1.2.2
[1.2.1]: https://github.com/theseer/tokenizer/compare/1.2.0...1.2.1
[1.2.0]: https://github.com/theseer/tokenizer/compare/1.1.3...1.2.0
[1.1.3]: https://github.com/theseer/tokenizer/compare/1.1.2...1.1.3
[1.1.2]: https://github.com/theseer/tokenizer/compare/1.1.1...1.1.2
[1.1.1]: https://github.com/theseer/tokenizer/compare/1.1.0...1.1.1
[1.1.0]: https://github.com/theseer/tokenizer/compare/1.0.0...1.1.0
[1.0.0]: https://github.com/theseer/tokenizer/compare/b2493e57de80c1b7414219b28503fa5c6b4d0a98...1.0.0
