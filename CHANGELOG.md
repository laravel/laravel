# Release Notes

## [Unreleased](https://github.com/laravel/laravel/compare/v7.30.1...7.x)


## [v7.30.1 (2020-10-31)](https://github.com/laravel/laravel/compare/v7.30.0...v7.30.1)

### Fixed
- Lower Ignition constraint to allow PHP 7.2 installs ([ff35597](https://github.com/laravel/laravel/commit/ff355973475624d9dc9ed5fd157a0ba0bef84710))


## [v7.30.0 (2020-10-30)](https://github.com/laravel/laravel/compare/v7.29.0...v7.30.0)

### Changed
- Bumped some dependencies


## [v7.29.0 (2020-10-29)](https://github.com/laravel/laravel/compare/v7.28.0...v7.29.0)

### Added
- PHP 8 Support ([4c25cb9](https://github.com/laravel/laravel/commit/4c25cb953a0bbd4812bf92af71a13920998def1e))

### Changed
- Bump minimum PHP version ([#5452](https://github.com/laravel/laravel/pull/5452))
- Update Faker ([#5461](https://github.com/laravel/laravel/pull/5461))
- Update minimum Laravel version ([02ca853](https://github.com/laravel/laravel/commit/02ca853809a97f372a3c6dc535c566efff9f6571))

### Fixed
- Delete removed webpack flag ([#5460](https://github.com/laravel/laravel/pull/5460))


## [v7.28.0 (2020-09-08)](https://github.com/laravel/laravel/compare/v7.25.0...v7.28.0)

Nothing specific.


## [v7.25.0 (2020-08-11)](https://github.com/laravel/laravel/compare/v7.12.0...v7.25.0)

### Added
- Add password reset migration ([9e5ba57](https://github.com/laravel/laravel/commit/9e5ba571a60a57ca2c3938bc5bd81d222cb6e618))

### Changed
- Bump `fruitcake/laravel-cors` ([#5320](https://github.com/laravel/laravel/pull/5320))
- Set framework version `^7.24` ([#5370](https://github.com/laravel/laravel/pull/5370))


## [v7.12.0 (2020-05-18)](https://github.com/laravel/laravel/compare/v7.6.0...v7.12.0)

### Added
- Allow configuring the auth_mode for SMTP mail driver ([#5293](https://github.com/laravel/laravel/pull/5293))
- Add basic trust host middleware ([5639581](https://github.com/laravel/laravel/commit/5639581ea56ecd556cdf6e6edc37ce5795740fd7))


## [v7.6.0 (2020-04-15)](https://github.com/laravel/laravel/compare/v7.3.0...v7.6.0)

### Changed
- Disable Telescope in PHPUnit ([#5277](https://github.com/laravel/laravel/pull/5277))

### Fixed
- Add both endpoint and url env variables ([#5276](https://github.com/laravel/laravel/pull/5276))


## [v7.3.0 (2020-03-24)](https://github.com/laravel/laravel/compare/v7.0.0...v7.3.0)

### Added
- Add serialize option to array cache config ([#5244](https://github.com/laravel/laravel/pull/5244))
- Add Mailgun and Postmark mailer ([#5243](https://github.com/laravel/laravel/pull/5243))
- Add new SQS queue suffix option ([#5252](https://github.com/laravel/laravel/pull/5252))
- Allow configuring the timeout for the smtp driver ([#5262](https://github.com/laravel/laravel/pull/5262))

### Changed
- Cleanup session config ([#5261](https://github.com/laravel/laravel/pull/5261))

### Fixed
- Ensure that `app.debug` is a bool ([5ddbfb8](https://github.com/laravel/laravel/commit/5ddbfb845439fcd5a46c23530b8774421a931760))
- Fix S3 endpoint url reference ([#5267](https://github.com/laravel/laravel/pull/5267))
- Update default CORS config ([#5259](https://github.com/laravel/laravel/pull/5259))

### Removed
- Remove `view.expires` config entry ([641fcfb](https://github.com/laravel/laravel/commit/641fcfb60aa47266c5b4767830dc45bad00c561c))


## [v7.0.0 (2020-03-03)](https://github.com/laravel/laravel/compare/v6.20.0...v7.0.0)

### Added
- Add HandleCors middleware ([#5189](https://github.com/laravel/laravel/pull/5189), [0bec06c](https://github.com/laravel/laravel/commit/0bec06cd45a7f6eda0d52f78dd5ff767d94ed5cc))
- Add new `view.expires` option ([#5209](https://github.com/laravel/laravel/pull/5209), [91dd1f6](https://github.com/laravel/laravel/commit/91dd1f61cdd3c7949593a4435dff8b77322761f2))
- Add `links` option to filesystem config ([#5222](https://github.com/laravel/laravel/pull/5222))
- Add Guzzle dependency ([c434eae](https://github.com/laravel/laravel/commit/c434eae43d673a709bb840f5f2e03b58da30682b), [705076f](https://github.com/laravel/laravel/commit/705076ffc28a834a1eb76b3550be2b6269a8fefb))
- Add array mailer ([#5240](https://github.com/laravel/laravel/pull/5240))

### Changed
- Laravel 7 constraint ([054bb43](https://github.com/laravel/laravel/commit/054bb43038f4acb7f356dd668715225ffc2e55ba))
- Implement new primary key syntax ([#5147](https://github.com/laravel/laravel/pull/5147))
- Switch to Symfony 5 ([#5157](https://github.com/laravel/laravel/pull/5157))
- Bumps `nunomaduro/collision` dependency to 4.1 ([#5221](https://github.com/laravel/laravel/pull/5221))
- Utilize Authentication Middleware Contract ([#5181](https://github.com/laravel/laravel/pull/5181), [#5182](https://github.com/laravel/laravel/pull/5182))
- Remove auth scaffolding ([b5bb91f](https://github.com/laravel/laravel/commit/b5bb91fea79a3bd5504cbcadfd4766f41f7d01ce), [13e4389](https://github.com/laravel/laravel/commit/13e43893ba2457c3e49898f0066a5ce8d7ea74f4), [3ee0065](https://github.com/laravel/laravel/commit/3ee0065bcd879b82ee42023165f8a8f71e893011))
- Import facades ([4d565e6](https://github.com/laravel/laravel/commit/4d565e681cbf496e0cdfb58743d4ae8238cef15e))
- Ignition v2 ([#5211](https://github.com/laravel/laravel/pull/5211))
- Bumped defaults for Laravel 7 ([#5195](https://github.com/laravel/laravel/pull/5195))
- Update mail config ([76d8227](https://github.com/laravel/laravel/commit/76d822768dcab14fa1ee1fd1f4a24065234860db), [61ec16f](https://github.com/laravel/laravel/commit/61ec16fe392967766b68d865ed10d56275a78718), [e43d454](https://github.com/laravel/laravel/commit/e43d4546a9c0bde49dae51fd6f4e2766674f1152), [130b8c8](https://github.com/laravel/laravel/commit/130b8c8bcb8f167e7013e7846004b2df3e405b72))
- Remove hyphen on email ([ffc74ba](https://github.com/laravel/laravel/commit/ffc74ba143a7de4a89f2c3fd525a5621ca879e38))
- Use `MAIL_MAILER` in test environment ([#5239](https://github.com/laravel/laravel/pull/5239))
