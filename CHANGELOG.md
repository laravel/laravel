# Release Notes

## [Unreleased](https://github.com/laravel/laravel/compare/v8.5.12...8.x)


## [v8.5.12 (2021-03-02)](https://github.com/laravel/laravel/compare/v8.5.11...v8.5.12)

### Fixed
- Added sans-serif as Fallback Font ([#5543](https://github.com/laravel/laravel/pull/5543))
- Don't trim `current_password` ([#5546](https://github.com/laravel/laravel/pull/5546))


## [v8.5.11 (2021-02-23)](https://github.com/laravel/laravel/compare/v8.5.10...v8.5.11)

### Fixed
- Don't flash 'current_password' input ([#5541](https://github.com/laravel/laravel/pull/5541))


## [v8.5.10 (2021-02-16)](https://github.com/laravel/laravel/compare/v8.5.9...v8.5.10)

### Changed
- Add "ably" in comment as a broadcast connection ([#5531](https://github.com/laravel/laravel/pull/5531))
- Add unverified state to UserFactory ([#5533](https://github.com/laravel/laravel/pull/5533))
- Update login wording ([9a56a60](https://github.com/laravel/laravel/commit/9a56a60cc9e3785683e256d511ee1fb533025a0a))

### Fixed
- Fix dead link in web.config ([#5528](https://github.com/laravel/laravel/pull/5528))


## [v8.5.9 (2021-01-19)](https://github.com/laravel/laravel/compare/v8.5.8...v8.5.9)

### Removed
- Delete `docker-compose.yml` ([#5522](https://github.com/laravel/laravel/pull/5522))


## [v8.5.8 (2021-01-12)](https://github.com/laravel/laravel/compare/v8.5.7...v8.5.8)

### Fixed
- Update `TrustProxies.php` ([#5514](https://github.com/laravel/laravel/pull/5514))


## [v8.5.7 (2021-01-05)](https://github.com/laravel/laravel/compare/v8.5.6...v8.5.7)

### Changed
- Update sail to the v1.0.1 ([#5507](https://github.com/laravel/laravel/pull/5507))
- Upgrade to Mix v6 ([#5505](https://github.com/laravel/laravel/pull/5505))
- Updated Axios ([4de728e](https://github.com/laravel/laravel/commit/4de728e78c91b496ce5de09983a56e229aa0ade1))


## [v8.5.6 (2020-12-22)](https://github.com/laravel/laravel/compare/v8.5.5...v8.5.6)

### Added
- Add `lock_connection` ([bc339f7](https://github.com/laravel/laravel/commit/bc339f712389cf536ad7e340453f35d1dd865777), [e8788a7](https://github.com/laravel/laravel/commit/e8788a768899ff2a2ef1fe78e24b46e6e10175dc))


## [v8.5.5 (2020-12-12)](https://github.com/laravel/laravel/compare/v8.5.4...v8.5.5)

### Changed
- Revert changes to env file ([3b2ed46](https://github.com/laravel/laravel/commit/3b2ed46e65c603ddc682753f1a9bb5472c4e12a8))


## [v8.5.4 (2020-12-10)](https://github.com/laravel/laravel/compare/v8.5.3...v8.5.4)

### Changed
- Gitignore `docker-compose.override.yml` ([#5487](https://github.com/laravel/laravel/pull/5487)
- Update ENV vars to docker file ([ddb26fb](https://github.com/laravel/laravel/commit/ddb26fbc504cd64fb1b89511773aa8d03c758c6d))


## [v8.5.3 (2020-12-10)](https://github.com/laravel/laravel/compare/v8.5.2...v8.5.3)

### Changed
- Disable `TrustHosts` middleware ([b7cde8b](https://github.com/laravel/laravel/commit/b7cde8b495e183f386da63ff7792e0dea9cfcf56))


## [v8.5.2 (2020-12-08)](https://github.com/laravel/laravel/compare/v8.5.1...v8.5.2)

### Added
- Add Sail ([17668be](https://github.com/laravel/laravel/commit/17668beabe4cb489ad07abb8af0a9da01860601e))


## [v8.5.1 (2020-12-08)](https://github.com/laravel/laravel/compare/v8.5.0...v8.5.1)

### Changed
- Revert change to `QUEUE_CONNECTION` ([34368a4](https://github.com/laravel/laravel/commit/34368a4fab61839c106efb1eea087cc270639619))


## [v8.5.0 (2020-12-08)](https://github.com/laravel/laravel/compare/v8.4.4...v8.5.0)

### Added
- Add Sail file ([bcd87e8](https://github.com/laravel/laravel/commit/bcd87e80ac7fa6a5daf0e549059ad7cb0b41ce75))

### Changed
- Update env file for Sail ([a895748](https://github.com/laravel/laravel/commit/a895748980b3e055ffcb68b6bc1c2e5fad6ecb08))


## [v8.4.4 (2020-12-01)](https://github.com/laravel/laravel/compare/v8.4.3...v8.4.4)

### Changed
- Comment out `Redis` facade by default ([612d166](https://github.com/laravel/laravel/commit/612d16600419265566d01a19c852ddb13b5e9f4b))
- Uncomment `TrustHosts` middleware to enable it by default ([#5477](https://github.com/laravel/laravel/pull/5477))

### Removed
- Remove cloud option ([82213fb](https://github.com/laravel/laravel/commit/82213fbf40fc4ec687781d0b93ff60a7de536913))


## [v8.4.3 (2020-11-24)](https://github.com/laravel/laravel/compare/v8.4.2...v8.4.3)

### Added
- Add ably entry ([5182e9c](https://github.com/laravel/laravel/commit/5182e9c6de805e025fb4cfad63c210c3197002ab))

### Fixed
- Add missing null cache driver in `config/cache.php` ([#5472](https://github.com/laravel/laravel/pull/5472))


## [v8.4.2 (2020-11-17)](https://github.com/laravel/laravel/compare/v8.4.1...v8.4.2)

### Changed
- Add sanctum cookie endpoint to default cors paths ([aa6d3660](https://github.com/laravel/laravel/commit/aa6d3660114c93e537a52e0ba3c03071a7f3e67f))
- Modify the `cache.php` docblocks ([#5468](https://github.com/laravel/laravel/pull/5468))
- Add stub handler ([4931af1](https://github.com/laravel/laravel/commit/4931af14006610bf8fd1f860cea1117c68133e94))

### Fixed
- Closed @auth correctly ([#5471](https://github.com/laravel/laravel/pull/5471))


## [v8.4.1 (2020-11-10)](https://github.com/laravel/laravel/compare/v8.4.0...v8.4.1)

### Changed
- Add auth line ([b54ef29](https://github.com/laravel/laravel/commit/b54ef297b3c723c8438596c6e6afef93a7458b98))


## [v8.4.0 (2020-10-30)](https://github.com/laravel/laravel/compare/v8.3.0...v8.4.0)

### Changed
- Bump several dependencies


## [v8.3.0 (2020-10-29)](https://github.com/laravel/laravel/compare/v8.2.0...v8.3.0)

### Added
- PHP 8 Support ([4c25cb9](https://github.com/laravel/laravel/commit/4c25cb953a0bbd4812bf92af71a13920998def1e))

### Changed
- Update Faker ([#5461](https://github.com/laravel/laravel/pull/5461))
- Update minimum Laravel version ([86d4ec0](https://github.com/laravel/laravel/commit/86d4ec095f1681df736d53206780d79f5857907c))
- Revert to per user API rate limit ([#5456](https://github.com/laravel/laravel/pull/5456), [bec982b](https://github.com/laravel/laravel/commit/bec982b0a3962c8a3e1f665e987360bb8c056298))

### Fixed
- Delete removed webpack flag ([#5460](https://github.com/laravel/laravel/pull/5460))


## [v8.2.0 (2020-10-20)](https://github.com/laravel/laravel/compare/v8.1.0...v8.2.0)

### Added
- Added 'LOG_LEVEL' env variable in `.env.example` ([#5445](https://github.com/laravel/laravel/pull/5445))
- Add 'multiple_of' translation ([#5449](https://github.com/laravel/laravel/pull/5449))


## [v8.1.0 (2020-10-06)](https://github.com/laravel/laravel/compare/v8.0.3...v8.1.0)

### Added
- Added `LOG_LEVEL` env variable ([#5442](https://github.com/laravel/laravel/pull/5442))

### Changed
- Type hint the middleware Request ([#5438](https://github.com/laravel/laravel/pull/5438))


## [v8.0.3 (2020-09-22)](https://github.com/laravel/laravel/compare/v8.0.2...v8.0.3)

### Changed
- Add comment ([a6ca577](https://github.com/laravel/laravel/commit/a6ca5778391b150102637459ac3b2a42d78d495b))


## [v8.0.2 (2020-09-22)](https://github.com/laravel/laravel/compare/v8.0.1...v8.0.2)

### Changed
- Fully qualified user model in seeder ([#5406](https://github.com/laravel/laravel/pull/5406))
- Update model path in `AuthServiceProvider`'s policies ([#5412](https://github.com/laravel/laravel/pull/5412))
- Add commented code ([69d0c50](https://github.com/laravel/laravel/commit/69d0c504e3ff01e0fd219e02ebac9b1c22151c2a))

### Fixed
- Swap route order ([292a5b2](https://github.com/laravel/laravel/commit/292a5b26a9293d82ab5a7d0bb81bba02ea71758e))
- Fix route when uncomment $namespace ([#5424](https://github.com/laravel/laravel/pull/5424))

### Removed
- Removed `$namespace` property ([b33852e](https://github.com/laravel/laravel/commit/b33852ecace72791f4bc28b8dd84c108166512bf))


## [v8.0.1 (2020-09-09)](https://github.com/laravel/laravel/compare/v8.0.0...v8.0.1)

### Changed
- Re-add property to route service provider ([9cbc381](https://github.com/laravel/laravel/commit/9cbc3819f7b1c268447996d347a1733aa68e16d7))


## [v8.0.0 (2020-09-08)](https://github.com/laravel/laravel/compare/v7.30.1...v8.0.0)

Laravel 8 comes with a lot of changes to the base skeleton. Please consult the diff to see what's changed.
