# Change Log

## [3.3.3] - 2024-08-10

### Added
- N/A

### Changed
- N/A

### Fixed
- Added fixes for making sure `?` is not passed for both DOM and DOW (#148, thank you https://github.com/LeoVie)
- Fixed bug in Next Execution Time by sorting minutes properly (#160, thank you https://github.com/imyip)

## [3.3.2] - 2022-09-19

### Added
- N/A

### Changed
- Skip some daylight savings time tests for PHP 8.1 daylight savings time weirdness (#146)

### Fixed
- Changed string interpolations to work better with PHP 8.2 (#142)

## [3.3.1] - 2022-01-18

### Added
- N/A

### Changed
- N/A

### Fixed
- Fixed issue when timezones had no transition, which can occur over very short timespans (#134)

## [3.3.0] - 2022-01-13

### Added
- Added ability to register your own expression aliases (#132)

### Changed
- Changed how Day of Week and Day of Month resolve when one or the other is `*` or `?`

### Fixed
- PHPStan should no longer error out

## [3.2.4] - 2022-01-12

### Added
- N/A

### Changed
- Changed how Day of Week increment/decrement to help with DST changes (#131)

### Fixed
- N/A

## [3.2.3] - 2022-01-05

### Added
- N/A

### Changed
- Changed how minutes and hours increment/decrement to help with DST changes (#131)

### Fixed
- N/A

## [3.2.2] - 2022-01-05

### Added
- N/A

### Changed
- Marked some methods `@internal` (#124)

### Fixed
- Fixed issue with small ranges and large steps that caused an error with `range()` (#88)
- Fixed issue where wraparound logic incorrectly considered high bound on range (#89)

## [3.2.1] - 2022-01-04

### Added
- N/A

### Changed
- Added PHP 8.1 to testing (#125)

### Fixed
- Allow better mixture of ranges, steps, and lists (#122)
- Fixed return order when multiple dates are requested and inverted (#121)
- Better handling over DST (#115)
- Fixed PHPStan tests (#130)

## [3.2.0] - 2022-01-04

### Added
- Added alias for `@midnight` (#117)

### Changed
- Improved testing for instance of field in tests (#105)
- Optimization for determining multiple run dates (#75)
- `CronExpression` properties changed from private to protected (#106)

### Fixed
- N/A

## [3.1.0] - 2020-11-24

### Added
- Added `CronExpression::getParts()` method to get parts of the expression as an array (#83)

### Changed
- Changed to Interfaces for some type hints (#97, #86)
- Dropped minimum PHP version to 7.2
- Few syntax changes for phpstan compatibility (#93)

### Fixed
- N/A

### Deprecated
- Deprecated `CronExpression::factory` in favor of the constructor (#56)
- Deprecated `CronExpression::YEAR` as a formality, the functionality is already removed (#87)

## [3.0.1] - 2020-10-12
### Added
- Added support for PHP 8 (#92)
### Changed
- N/A
### Fixed
- N/A

## [3.0.0] - 2020-03-25

**MAJOR CHANGE** - In previous versions of this library, setting both a "Day of Month" and a "Day of Week" would be interpreted as an `AND` statement, not an `OR` statement. For example:

`30 0 1 * 1`

would evaluate to "Run 30 minutes after the 0 hour when the Day Of Month is 1 AND a Monday" instead of "Run 30 minutes after the 0 hour on Day Of Month 1 OR a Monday", where the latter is more inline with most cron systems. This means that if your cron expression has both of these fields set, you may see your expression fire more often starting with v3.0.0. 

### Added
- Additional docblocks for IDE and documentation
- Added phpstan as a development dependency
- Added a `Cron\FieldFactoryInterface` to make migrations easier (#38)
### Changed
- Changed some DI testing during TravisCI runs
- `\Cron\CronExpression::determineTimezone()` now checks for `\DateTimeInterface` instead of just `\DateTime`
- Errors with fields now report a more human-understandable error and are 1-based instead of 0-based
- Better support for `\DateTimeImmutable` across the library by typehinting for `\DateTimeInterface` now
- Literals should now be less case-sensative across the board
- Changed logic for when both a Day of Week and a Day of Month are supplied to now be an OR statement, not an AND
### Fixed
- Fixed infinite loop when determining last day of week from literals
- Fixed bug where single number ranges were allowed (ex: `1/10`)
- Fixed nullable FieldFactory in CronExpression where no factory could be supplied
- Fixed issue where logic for dropping seconds to 0 could lead to a timezone change

## [2.3.1] - 2020-10-12
### Added
- Added support for PHP 8 (#92)
### Changed
- N/A
### Fixed
- N/A

## [2.3.0] - 2019-03-30
### Added
- Added support for DateTimeImmutable via DateTimeInterface
- Added support for PHP 7.3
- Started listing projects that use the library
### Changed
- Errors should now report a human readable position in the cron expression, instead of starting at 0
### Fixed
- N/A

## [2.2.0] - 2018-06-05
### Added
- Added support for steps larger than field ranges (#6)
## Changed
- N/A
### Fixed
- Fixed validation for numbers with leading 0s (#12)

## [2.1.0] - 2018-04-06
### Added
- N/A
### Changed
- Upgraded to PHPUnit 6 (#2)
### Fixed
- Refactored timezones to deal with some inconsistent behavior (#3)
- Allow ranges and lists in same expression (#5)
- Fixed regression where literals were not converted to their numerical counterpart (#)

## [2.0.0] - 2017-10-12
### Added
- N/A

### Changed
- Dropped support for PHP 5.x
- Dropped support for the YEAR field, as it was not part of the cron standard

### Fixed
- Reworked validation for all the field types
- Stepping should now work for 1-indexed fields like Month (#153)

## [1.2.0] - 2017-01-22
### Added
- Added IDE, CodeSniffer, and StyleCI.IO support

### Changed
- Switched to PSR-4 Autoloading

### Fixed
- 0 step expressions are handled better
- Fixed `DayOfMonth` validation to be more strict
- Typos

## [1.1.0] - 2016-01-26
### Added
- Support for non-hourly offset timezones 
- Checks for valid expressions

### Changed
- Max Iterations no longer hardcoded for `getRunDate()`
- Supports DateTimeImmutable for newer PHP verions

### Fixed
- Fixed looping bug for PHP 7 when determining the last specified weekday of a month

## [1.0.3] - 2013-11-23
### Added
- Now supports expressions with any number of extra spaces, tabs, or newlines

### Changed
- Using static instead of self in `CronExpression::factory`

### Fixed
- Fixes issue [#28](https://github.com/mtdowling/cron-expression/issues/28) where PHP increments of ranges were failing due to PHP casting hyphens to 0
- Only set default timezone if the given $currentTime is not a DateTime instance ([#34](https://github.com/mtdowling/cron-expression/issues/34))
