# CHANGELOG

## [Unreleased](https://github.com/FakerPHP/Faker/compare/v1.24.0...1.24.1)

- Removed domain `gmail.com.au` from `Provider\en_AU\Internet` (#886)

## [2024-11-09, v1.24.0](https://github.com/FakerPHP/Faker/compare/v1.23.1..v1.24.0)

- Fix internal deprecations in Doctrine's populator by @gnutix in (#889)
- Fix mobile phone number pattern for France by @ker0x in (#859)
- PHP 8.4 Support by @Jubeki in (#904)

- Added support for PHP 8.4 (#904)

## [2023-09-29, v1.23.1](https://github.com/FakerPHP/Faker/compare/v1.23.0..v1.23.1)

- Fixed double `а` female lastName in `ru_RU/Person::name()` (#832)
- Fixed polish license plates (#685)
- Stopped using `static` in callables in `Provider\pt_BR\PhoneNumber` (#785)
- Fixed incorrect female name (#794)
- Stopped using the deprecated `MT_RAND_PHP` constant to seed the random generator on PHP 8.3 (#844)

## [2023-06-12, v1.23.0](https://github.com/FakerPHP/Faker/compare/v1.22.0..v1.23.0)

- Update `randomElements` to return random number of elements when no count is provided (#658)

## [2023-05-14, v1.22.0](https://github.com/FakerPHP/Faker/compare/v1.21.0..v1.22.0)

- Fixed `randomElements()` to accept empty iterator (#605)
- Added support for passing an `Enum` to `randomElement()` and `randomElements()` (#620)
- Started rejecting invalid arguments passed to `randomElement()` and `randomElements()` (#642)

## [2022-12-13, v1.21.0](https://github.com/FakerPHP/Faker/compare/v1.20.0..v1.21.0)

- Dropped support for PHP 7.1, 7.2, and 7.3 (#543)
- Added support for PHP 8.2 (#528)

## [2022-07-20, v1.20.0](https://github.com/FakerPHP/Faker/compare/v1.19.0..v1.20.0)

- Fixed typo in French phone number (#452)
- Fixed some Hungarian naming bugs (#451)
- Fixed bug where the NL-BE VAT generation was incorrect (#455)
- Improve Turkish phone numbers for E164 and added landline support (#460)
- Add Microsoft Edge User Agent (#464)
- Added option to set image formats on Faker\Provider\Image (#473)
- Added support for French color translations (#466)
- Support filtering timezones by country code (#480)
- Fixed typo in some greek names (#490)
- Marked the Faker\Provider\Image as deprecated

## [2022-02-02, v1.19.0](https://github.com/FakerPHP/Faker/compare/v1.18.0..v1.19.0)

- Added color extension to core (#442)
- Added conflict with `doctrine/persistence` below version `1.4`
- Fix for support on different Doctrine ORM versions (#414)
- Fix usage of `Doctrine\Persistence` dependency
- Fix CZ Person birthNumber docblock return type (#437)
- Fix is_IS Person docbock types (#439)
- Fix is_IS Address docbock type (#438)
- Fix regexify escape backslash in character class (#434)
- Removed UUID from Generator to be able to extend it (#441)

## [2022-01-23, v1.18.0](https://github.com/FakerPHP/Faker/compare/v1.17.0..v1.18.0)

- Deprecated UUID, use uuid3 to specify version (#427)
- Reset formatters when adding a new provider (#366)
- Helper methods to use our custom generators (#155)
- Set allow-plugins for Composer 2.2 (#405)
- Fix kk_KZ\Person::individualIdentificationNumber generation (#411)
- Allow for -> syntax to be used in parsing (#423)
- Person->name was missing string return type (#424)
- Generate a valid BE TAX number (#415)
- Added the UUID extension to Core (#427)

## [2021-12-05, v1.17.0](https://github.com/FakerPHP/Faker/compare/v1.16.0..v1.17.0)

- Partial PHP 8.1 compatibility (#373)
- Add payment provider for `ne_NP` locale (#375)
- Add Egyptian Arabic `ar_EG` locale (#377)
- Updated list of South African TLDs (#383)
- Fixed formatting of E.164 numbers (#380)
- Allow `symfony/deprecation-contracts` `^3.0` (#397)

## [2021-09-06, v1.16.0](https://github.com/FakerPHP/Faker/compare/v1.15.0..v1.16.0)

- Add Company extension
- Add Address extension
- Add Person extension
- Add PhoneNumber extension
- Add VersionExtension (#350)
- Stricter types in Extension\Container and Extension\GeneratorAwareExtension (#345)
- Fix deprecated property access in `nl_NL` (#348)
- Add support for `psr/container` >= 2.0 (#354)
- Add missing union types in Faker\Generator (#352)

## [2021-07-06, v1.15.0](https://github.com/FakerPHP/Faker/compare/v1.14.1..v1.15.0)

- Updated the generator phpdoc to help identify magic methods (#307)
- Prevent direct access and triggered deprecation warning for "word" (#302)
- Updated length on all global e164 numbers (#301)
- Updated last names from different source (#312)
- Don't generate birth number of '000' for Swedish personal identity (#306)
- Add job list for localization id_ID (#339)

## [2021-03-30, v1.14.1](https://github.com/FakerPHP/Faker/compare/v1.14.0..v1.14.1)

- Fix where randomNumber and randomFloat would return a 0 value (#291 / #292)

## [2021-03-29, v1.14.0](https://github.com/FakerPHP/Faker/compare/v1.13.0..v1.14.0)

- Fix for realText to ensure the text keeps closer to its boundaries (#152)
- Fix where regexify produces a random character instead of a literal dot (#135
- Deprecate zh_TW methods that only call base methods (#122)
- Add used extensions to composer.json as suggestion (#120)
- Moved TCNo and INN from calculator to localized providers (#108)
- Fix regex dot/backslash issue where a dot is replaced with a backslash as escape character (#206)
- Deprecate direct property access (#164)
- Added test to assert unique() behaviour (#233)
- Added RUC for the es_PE locale (#244)
- Test IBAN formats for Latin America (AR/PE/VE) (#260)
- Added VAT number for en_GB (#255)
- Added new districts for the ne_NP locale (#258)
- Fix for U.S. Area Code Generation (#261)
- Fix in numerify where a better random numeric value is guaranteed (#256)
- Fix e164PhoneNumber to only generate valid phone numbers with valid country codes (#264)
- Extract fixtures into separate classes (#234)
- Remove french domains that no longer exists (#277)
- Fix error that occurs when getting a polish title (#279)
- Use valid area codes for North America E164 phone numbers (#280)

- Adding support for extensions and PSR-11 (#154)
- Adding trait for GeneratorAwareExtension (#165)
- Added helper class for extension (#162)
- Added blood extension to core (#232)
- Added barcode extension to core (#252)
- Added number extension (#257)

- Various code style updates
- Added a note about our breaking change promise (#273)

## [2020-12-18, v1.13.0](https://github.com/FakerPHP/Faker/compare/v1.12.1..v1.13.0)

Several fixes and new additions in this release. A lot of cleanup has been done
on the codebase on both tests and consistency.

- Feature/pl pl license plate (#62)
- Fix greek phone numbers (#16)
- Move AT payment provider logic to de_AT (#72)
- Fix wiktionary links (#73)
- Fix AT person links (#74)
- Fix AT cities (#75)
- Deprecate at_AT providers (#78)
- Add Austrian `ssn()` to `Person` provider (#79)
- Fix typos in id_ID Address (#83)
- Austrian post codes (#86)
- Updated Polish data (#70)
- Improve Austrian social security number generation (#88)
- Move US phone numbers with extension to own method (#91)
- Add UK National Insurance number generator (#89)
- Fix en_SG phone number generator (#100)
- Remove usage of mt_rand (#87)
- Remove whitespace from beginning of el_GR phone numbers (#105)
- Building numbers can not be 0, 00, 000 (#107)
- Add 172.16/12 local IPv4 block (#121)
- Add JCB credit card type (#124)
- Remove json_decode from emoji generation (#123)
- Remove ro street address (#146)

## [2020-12-11, v1.12.1](https://github.com/FakerPHP/Faker/compare/v1.12.0..v1.12.1)

This is a security release that prevents a hacker to execute code on the server.

## [2020-11-23, v1.12.0](https://github.com/FakerPHP/Faker/compare/v1.11.0..v1.12.0)

- Fix ro_RO first and last day of year calculation offset (#65)
- Fix en_NG locale test namespaces that did not match PSR-4 (#57)
- Added Singapore NRIC/FIN provider (#56)
- Added provider for Lithuanian municipalities (#58)
- Added blood types provider (#61)

## [2020-11-15, v1.11.0](https://github.com/FakerPHP/Faker/compare/v1.10.1..v1.11.0)

- Added Provider for Swedish Municipalities
- Updates to person names in pt_BR
- Many code style changes

## [2020-10-28, v1.10.1](https://github.com/FakerPHP/Faker/compare/v1.10.0..v1.10.1)

- Updates the Danish addresses in dk_DK
- Removed offense company names in nl_NL
- Clarify changelog with original fork
- Standin replacement for LoremPixel to Placeholder.com (#11)

## [2020-10-27, v1.10.0](https://github.com/FakerPHP/Faker/compare/v1.9.1..v1.10.0)

- Support PHP 7.1-8.0
- Fix typo in de_DE Company Provider
- Fix dateTimeThisYear method
- Fix typo in de_DE jobTitleFormat
- Fix IBAN generation for CR
- Fix typos in greek first names
- Fix US job title typo
- Do not clear entity manager for doctrine orm populator
- Remove persian rude words
- Corrections to RU names

## 2020-10-27, v1.9.1

- Initial version. Same as `fzaninotto/Faker:v1.9.1`.
