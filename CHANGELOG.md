# Release Notes

## [Unreleased](https://github.com/laravel/laravel/compare/v8.4.0...master)


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
