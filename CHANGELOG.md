# Release Notes

## [Unreleased](https://github.com/laravel/laravel/compare/v9.1.9...9.x)

## [v9.1.9](https://github.com/laravel/laravel/compare/v9.1.8...v9.1.9) - 2022-05-28

### Changed

- Switch to ESM imports by @jessarcher in https://github.com/laravel/laravel/pull/5895

## [v9.1.8](https://github.com/laravel/laravel/compare/v9.1.7...v9.1.8) - 2022-05-05

### Changed

- Add local_domain option to smtp configuration by @bintzandt in https://github.com/laravel/laravel/pull/5877
- Add specific test user in seeder by @driesvints in https://github.com/laravel/laravel/pull/5879

## [v9.1.7](https://github.com/laravel/laravel/compare/v9.1.6...v9.1.7) - 2022-05-03

### Changed

- Deprecation log stack trace option by @driesvints in https://github.com/laravel/laravel/pull/5874

## [v9.1.6](https://github.com/laravel/laravel/compare/v9.1.5...v9.1.6) - 2022-04-20

### Changed

- Move password lines into main translation file by @taylorotwell in https://github.com/laravel/laravel/commit/db0d052ece1c17c506633f4c9f5604b65e1cc3a4
- Add missing maintenance to config by @ibrunotome in https://github.com/laravel/laravel/pull/5868

## [v9.1.5](https://github.com/laravel/laravel/compare/v9.1.4...v9.1.5) - 2022-04-12

### Changed

- Rearrange route methods by @osbre in https://github.com/laravel/laravel/pull/5862
- Add levels to handler by @taylorotwell in https://github.com/laravel/laravel/commit/a507e1424339633ce423729ec0ac49b99f0e57d7

## [v9.1.4](https://github.com/laravel/laravel/compare/v9.1.3...v9.1.4) - 2022-03-29

### Changed

- Add encryption configuration by @taylorotwell in https://github.com/laravel/laravel/commit/f7b982ebdf7bd31eda9f05f901bd92ed32446156

## [v9.1.3](https://github.com/laravel/laravel/compare/v9.1.2...v9.1.3) - 2022-03-29

### Changed

- Add an example to the class aliases by @nshiro in https://github.com/laravel/laravel/pull/5846
- Add username in config to use with phpredis + ACL by @neoteknic in https://github.com/laravel/laravel/pull/5851
- Remove "password" from validation lang by @mnastalski in https://github.com/laravel/laravel/pull/5856
- Make authenticate session a route middleware by @taylorotwell in https://github.com/laravel/laravel/pull/5842

## [v9.1.2](https://github.com/laravel/laravel/compare/v9.1.1...v9.1.2) - 2022-03-15

### Changed

- The docker-compose.override.yml should not be ignored by default by @dakira in https://github.com/laravel/laravel/pull/5838

## [v9.1.1](https://github.com/laravel/laravel/compare/v9.1.0...v9.1.1) - 2022-03-08

### Changed

- Add option to configure Mailgun transporter scheme by @jnoordsij in https://github.com/laravel/laravel/pull/5831
- Add `throw` to filesystems config by @ankurk91 in https://github.com/laravel/laravel/pull/5835

### Fixed

- Small typo fix in filesystems.php by @tooshay in https://github.com/laravel/laravel/pull/5827
- Update sendmail default params by @driesvints in https://github.com/laravel/laravel/pull/5836

## [v9.1.0](https://github.com/laravel/laravel/compare/v9.0.1...v9.1.0) - 2022-02-22

### Changed

- Remove namespace from Routes by @emargareten in https://github.com/laravel/laravel/pull/5818
- Update sanctum config file by @suyar in https://github.com/laravel/laravel/pull/5820
- Replace Laravel CORS package by @driesvints in https://github.com/laravel/laravel/pull/5825

## [v9.0.1](https://github.com/laravel/laravel/compare/v9.0.0...v9.0.1) - 2022-02-15

### Changed

- Improve typing on user factory by @axlon in https://github.com/laravel/laravel/pull/5806
- Align min PHP version with docs by @u01jmg3 in https://github.com/laravel/laravel/pull/5807
- Remove redundant `null`s by @felixdorn in https://github.com/laravel/laravel/pull/5811
- Remove default commented namespace by @driesvints in https://github.com/laravel/laravel/pull/5816
- Add underscore to prefix in database cache key by @m4tlch in https://github.com/laravel/laravel/pull/5817

### Fixed

- Fix lang alphabetical order by @shuvroroy in https://github.com/laravel/laravel/pull/5812

## [v9.0.0 (2022-02-08)](https://github.com/laravel/laravel/compare/v8.6.11...v9.0.0)

Laravel 9 includes a variety of changes to the application skeleton. Please consult the diff to see what's new.
