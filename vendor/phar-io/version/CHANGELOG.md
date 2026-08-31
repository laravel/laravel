# Changelog

All notable changes to phar-io/version are documented in this file using the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## [3.2.1] - 2022-02-21

### Fixed

- Have ExactVersionConstraint honor build metadata (added in 3.2.0)


## [3.2.0] - 2022-02-21

### Added

- Build metadata is now supported and considered for equality checks only


## [3.1.1] - 2022-02-07

### Fixed

- [#28](https://github.com/phar-io/version/issues/28): `VersionConstraintParser` does not support logical OR represented by single pipe (|) (Thanks @llaville)


## [3.1.0] - 2021-02-23

### Changed

- Internal Refactoring
- More scalar types

### Added

- [#24](https://github.com/phar-io/version/issues/24): `Version::getOriginalString()` added (Thanks @addshore)
- Version constraints using the caret operator (`^`) now honor pre-1.0 releases, e.g. `^0.3` translates to `0.3.*`)
- Various integration tests for version constraint processing

### Fixed

- [#23](https://github.com/phar-io/version/pull/23): Tilde operator without patch level



## [3.0.4] - 14.12.2020

### Fixed 

- [#22](https://github.com/phar-io/version/pull/22): make dev suffix rank works for uppercase too

## [3.0.3] - 30.11.2020

### Added

- Comparator method `Version::equals()` added


## [3.0.2] - 27.06.2020

This release now supports PHP 7.2+ and PHP ^8.0. No other changes included.


## [3.0.1] - 09.05.2020

__Potential BC Break Notice:__
`Version::getVersionString()` no longer returns `v` prefixes in case the "input"
string contained one. These are not part of the semver specs
(see https://semver.org/#is-v123-a-semantic-version) and get stripped out.
As of Version 3.1.0 `Version::getOriginalString()` can be used to still
retrieve it as given.

### Changed

- Internal Refactoring
- More scalar types

### Fixed

- Fixed Constraint processing Regression for ^1.2  and ~1.2


## [3.0.0] - 05.05.2020

### Changed

- Require PHP 7.2+
- All code now uses strict mode
- Scalar types have been added as needed

### Added

- The technically invalid format using 'v' prefix ("v1.2.3") is now properly supported 


## [2.0.1] - 08.07.2018

### Fixed

- Versions without a pre-release suffix are now always considered greater 
than versions with a pre-release suffix. Example: `3.0.0 > 3.0.0-alpha.1`  


## [2.0.0] - 23.06.2018

Changes to public API:

- `PreReleaseSuffix::construct()`: optional parameter `$number` removed
- `PreReleaseSuffix::isGreaterThan()`: introduced
- `Version::hasPreReleaseSuffix()`: introduced

### Added

- [#11](https://github.com/phar-io/version/issues/11): Added support for pre-release version suffixes. Supported values are:
  - `dev`
  - `beta` (also abbreviated form `b`)
  - `rc`
  - `alpha` (also abbreviated form `a`)
  - `patch` (also abbreviated form `p`)

  All values can be followed by a number, e.g. `beta3`. 

  When comparing versions, the pre-release suffix is taken into account. Example:
`1.5.0 > 1.5.0-beta1 > 1.5.0-alpha3 > 1.5.0-alpha2 > 1.5.0-dev11`

### Changed

- reorganized the source directories

### Fixed

- [#10](https://github.com/phar-io/version/issues/10): Version numbers containing 
a numeric suffix as seen in Debian packages are now supported.  


[3.1.0]: https://github.com/phar-io/version/compare/3.0.4...3.1.0
[3.0.4]: https://github.com/phar-io/version/compare/3.0.3...3.0.4
[3.0.3]: https://github.com/phar-io/version/compare/3.0.2...3.0.3
[3.0.2]: https://github.com/phar-io/version/compare/3.0.1...3.0.2
[3.0.1]: https://github.com/phar-io/version/compare/3.0.0...3.0.1
[3.0.0]: https://github.com/phar-io/version/compare/2.0.1...3.0.0
[2.0.1]: https://github.com/phar-io/version/compare/2.0.0...2.0.1
[2.0.0]: https://github.com/phar-io/version/compare/1.0.1...2.0.0
