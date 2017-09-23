# Release Notes

## v5.5.0 (2017-08-30)

### Added
- Added `same_site` to `session.php` config ([#4168](https://github.com/laravel/laravel/pull/4168))
- Added `TrustProxies` middleware ([e23a1d2](https://github.com/laravel/laravel/commit/e23a1d284f134bfce258cf736ea8667a407ba50c), [#4302](https://github.com/laravel/laravel/pull/4302))
- Autoload commands ([5d54c21](https://github.com/laravel/laravel/commit/5d54c21ea869a7a5b503f0899307e4728feed11b))
- Added Whoops ([#4364](https://github.com/laravel/laravel/pull/4364))

### Changed
- Refactored exception handler (_too many commits_)
- Renamed `ModelFactory.php` to `UserFactory.php` to encourage separate files ([67a8a11](https://github.com/laravel/laravel/commit/67a8a1157004c4373663ec4a9398780feb6d6fa4))
- Use `RefreshDatabase` trait ([a536402](https://github.com/laravel/laravel/commit/a536402228108da9423a0db1e0cf492f3f51c8b8), [#4373](https://github.com/laravel/laravel/pull/4373))
- Use Composer's `@php` directive ([#4278](https://github.com/laravel/laravel/pull/4278))
- Use `post-autoload-dump` ([2f4d726](https://github.com/laravel/laravel/commit/2f4d72699cdc9b7db953055287697a60b6d8b294))
- Try to build session cookie name from app name ([#4305](https://github.com/laravel/laravel/pull/4305))
 
### Fixed
- Fixed Apache trailing slash redirect for subdirectory installs ([#4344](https://github.com/laravel/laravel/pull/4344))

### Removed
- Dropped `bootstrap/autoload.php` ([#4226](https://github.com/laravel/laravel/pull/4226), [#4227](https://github.com/laravel/laravel/pull/4227), [100f71e](https://github.com/laravel/laravel/commit/100f71e71a24fd8f339a7687557b77dd872b054b))
- Emptied `$dontReport` array on exception handler ([758392c](https://github.com/laravel/laravel/commit/758392c30fa0b2651ca9409aebb040a64816dde4))
- Removed `TinkerServiceProvider` ([6db0f35](https://github.com/laravel/laravel/commit/6db0f350fbaa21b2acf788d10961aba983a19be2)) 
- Removed migrations from autoload classmap ([#4340](https://github.com/laravel/laravel/pull/4340))


## v5.4.30 (2017-07-20)

### Changed
- Simplified mix require ([#4283](https://github.com/laravel/laravel/pull/4283))
- Upgraded Laravel Mix to `^1.0` ([#4294](https://github.com/laravel/laravel/pull/4294))
- Upgraded `axios` and `cross-env` package ([#4299](https://github.com/laravel/laravel/pull/4299))
- Ignore Yarn error log ([#4322](https://github.com/laravel/laravel/pull/4322))

### Fixed
- Use `app()->getLocale()` ([#4282](https://github.com/laravel/laravel/pull/4282))
- Use quotes in `app.scss` ([#4287](https://github.com/laravel/laravel/pull/4287))


## v5.4.23 (2017-05-11)

### Added
- Added SQL Server connection ([#4253](https://github.com/laravel/laravel/pull/4253), [#4254](https://github.com/laravel/laravel/pull/4254))

### Changed
- Switch to using meta
- Use CSRF token from `meta` tag, instead of `window.Laravel` object ([#4260](https://github.com/laravel/laravel/pull/4260))
- Log console error if CSRF token cannot be found ([1155245](https://github.com/laravel/laravel/commit/1155245a596113dc2cd0e9083603fa11df2eacd9))

### Fixed
- Added missing `ipv4` and `ipv6` validation messages ([#4261](https://github.com/laravel/laravel/pull/4261))


## v5.4.21 (2017-04-28)

### Added
- Added `FILESYSTEM_DRIVER` and `FILESYSTEM_CLOUD` environment variables ([#4236](https://github.com/laravel/laravel/pull/4236))

### Changed
- Use lowercase doctype ([#4241](https://github.com/laravel/laravel/pull/4241))


## v5.4.19 (2017-04-20)

### Added
- Added `optimize-autoloader` to `config` in `composer.json` ([#4189](https://github.com/laravel/laravel/pull/4189))
- Added `.vagrant` directory to `.gitignore` ([#4191](https://github.com/laravel/laravel/pull/4191))
- Added `npm run development` and `npm run prod` commands ([#4190](https://github.com/laravel/laravel/pull/4190), [#4193](https://github.com/laravel/laravel/pull/4193))
- Added `APP_NAME` environment variable ([#4204](https://github.com/laravel/laravel/pull/4204))

### Changed
- Changed Laravel Mix version to `0.*` ([#4188](https://github.com/laravel/laravel/pull/4188))
- Add to axios defaults instead of overwriting them ([#4208](https://github.com/laravel/laravel/pull/4208))
- Added `string` validation rule to `RegisterController` ([#4212](https://github.com/laravel/laravel/pull/4212))
- Moved Vue inclusion from `bootstrap.js` to `app.js` ([17ec5c5](https://github.com/laravel/laravel/commit/17ec5c51d60bb05985f287f09041c56fcd41d9ce))
- Only load libraries if present ([d905b2e](https://github.com/laravel/laravel/commit/d905b2e7bede2967d37ed7b260cd9d526bb9cabd))
- Ignore the NPM debug log ([#4232](https://github.com/laravel/laravel/pull/4232))
- Use fluent middleware definition in `LoginController` ([#4229](https://github.com/laravel/laravel/pull/4229))


## v5.4.16 (2017-03-17)

### Added
- Added `unix_socket` to `mysql` in `config/database.php` ()[#4179](https://github.com/laravel/laravel/pull/4179))
- Added Pusher example code to `bootstrap.js` ([31c2623](https://github.com/laravel/laravel/commit/31c262301899b6cd1a4ce2631ad0e313b444b131))

### Changed
- Use `smtp.mailtrap.io` as default `MAIL_HOST` ([#4182](https://github.com/laravel/laravel/pull/4182))
- Use `resource_path()` in `config/view.php` ([#4165](https://github.com/laravel/laravel/pull/4165))
- Use `cross-env` binary ([#4167](https://github.com/laravel/laravel/pull/4167))

### Removed
- Remove index from password reset `token` column ([#4180](https://github.com/laravel/laravel/pull/4180))
