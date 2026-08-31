# ChangeLog

All notable changes are documented in this file using the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## [5.1.3] - 2025-08-09

### Fixed

* [#34](https://github.com/sebastianbergmann/type/pull/34): `infection.json` is missing from `.gitattributes`

## [5.1.2] - 2025-03-18

### Fixed

* [#33](https://github.com/sebastianbergmann/type/issues/33): `ReflectionMapper` does not handle unions that contain `iterable` correctly

## [5.1.1] - 2025-03-18

### Fixed

* [#33](https://github.com/sebastianbergmann/type/issues/33): `ReflectionMapper` does not handle unions that contain `iterable` correctly

## [5.1.0] - 2024-09-17

### Added

* Added `ReflectionMapper::fromPropertyType()` for mapping `\ReflectionProperty` to a `Type` object

## [5.0.1] - 2024-07-03

### Changed

* This project now uses PHPStan instead of Psalm for static analysis

## [5.0.0] - 2024-02-02

### Removed

* This component is no longer supported on PHP 8.1

## [4.0.0] - 2023-02-03

### Removed

* This component is no longer supported on PHP 7.3, PHP 7.4 and PHP 8.0

## [3.2.1] - 2023-02-03

### Fixed

* [#28](https://github.com/sebastianbergmann/type/pull/28): Potential undefined offset warning/notice

## [3.2.0] - 2022-09-12

### Added

* [#25](https://github.com/sebastianbergmann/type/issues/25): Support Disjunctive Normal Form types
* Added `ReflectionMapper::fromParameterTypes()`
* Added `IntersectionType::types()` and `UnionType::types()`
* Added `UnionType::containsIntersectionTypes()`

## [3.1.0] - 2022-08-29

### Added

* [#21](https://github.com/sebastianbergmann/type/issues/21): Support `true` as stand-alone type

## [3.0.0] - 2022-03-15

### Added

* Support for intersection types introduced in PHP 8.1
* Support for the `never` return type introduced in PHP 8.1
* Added `Type::isCallable()`, `Type::isGenericObject()`, `Type::isIterable()`, `Type::isMixed()`, `Type::isNever()`, `Type::isNull()`, `Type::isObject()`, `Type::isSimple()`, `Type::isStatic()`, `Type::isUnion()`, `Type::isUnknown()`, and `Type::isVoid()`

### Changed

* Renamed `ReflectionMapper::fromMethodReturnType(ReflectionMethod $method)` to `ReflectionMapper::fromReturnType(ReflectionFunctionAbstract $functionOrMethod)`

### Removed

* Removed `Type::getReturnTypeDeclaration()` (use `Type::asString()` instead and prefix its result with `': '`)
* Removed `TypeName::getNamespaceName()` (use `TypeName::namespaceName()` instead)
* Removed `TypeName::getSimpleName()` (use `TypeName::simpleName()` instead)
* Removed `TypeName::getQualifiedName()` (use `TypeName::qualifiedName()` instead)

## [2.3.4] - 2021-06-15

### Fixed

* Fixed regression introduced in 2.3.3

## [2.3.3] - 2021-06-15 [YANKED]

### Fixed

* [#15](https://github.com/sebastianbergmann/type/issues/15): "false" pseudo type is not handled properly

## [2.3.2] - 2021-06-04

### Fixed

* Fixed handling of tentatively declared return types

## [2.3.1] - 2020-10-26

### Fixed

* `SebastianBergmann\Type\Exception` now correctly extends `\Throwable`

## [2.3.0] - 2020-10-06

### Added

* [#14](https://github.com/sebastianbergmann/type/issues/14): Support for `static` return type that is introduced in PHP 8

## [2.2.2] - 2020-09-28

### Changed

* Changed PHP version constraint in `composer.json` from `^7.3 || ^8.0` to `>=7.3`

## [2.2.1] - 2020-07-05

### Fixed

* Fixed handling of `mixed` type in `ReflectionMapper::fromMethodReturnType()`

## [2.2.0] - 2020-07-05

### Added

* Added `MixedType` object for representing PHP 8's `mixed` type

## [2.1.1] - 2020-06-26

### Added

* This component is now supported on PHP 8

## [2.1.0] - 2020-06-01

### Added

* Added `UnionType` object for representing PHP 8's Union Types
* Added `ReflectionMapper::fromMethodReturnType()` for mapping `\ReflectionMethod::getReturnType()` to a `Type` object
* Added `Type::name()` for retrieving the name of a type
* Added `Type::asString()` for retrieving a textual representation of a type

### Changed

* Deprecated `Type::getReturnTypeDeclaration()` (use `Type::asString()` instead and prefix its result with `': '`)
* Deprecated `TypeName::getNamespaceName()` (use `TypeName::namespaceName()` instead)
* Deprecated `TypeName::getSimpleName()` (use `TypeName::simpleName()` instead)
* Deprecated `TypeName::getQualifiedName()` (use `TypeName::qualifiedName()` instead)

## [2.0.0] - 2020-02-07

### Removed

* This component is no longer supported on PHP 7.2

## [1.1.3] - 2019-07-02

### Fixed

* Fixed class name comparison in `ObjectType` to be case-insensitive

## [1.1.2] - 2019-06-19

### Fixed

* Fixed handling of `object` type

## [1.1.1] - 2019-06-08

### Fixed

* Fixed autoloading of `callback_function.php` fixture file

## [1.1.0] - 2019-06-07

### Added

* Added support for `callable` type
* Added support for `iterable` type

## [1.0.0] - 2019-06-06

* Initial release based on [code contributed by Michel Hartmann to PHPUnit](https://github.com/sebastianbergmann/phpunit/pull/3673)

[5.1.3]: https://github.com/sebastianbergmann/type/compare/5.1.2...5.1.3
[5.1.2]: https://github.com/sebastianbergmann/type/compare/5.1.1...5.1.2
[5.1.1]: https://github.com/sebastianbergmann/type/compare/5.1.0...5.1.1
[5.1.0]: https://github.com/sebastianbergmann/type/compare/5.0.1...5.1.0
[5.0.1]: https://github.com/sebastianbergmann/type/compare/5.0.0...5.0.1
[5.0.0]: https://github.com/sebastianbergmann/type/compare/4.0...5.0.0
[4.0.0]: https://github.com/sebastianbergmann/type/compare/3.2.1...4.0.0
[3.2.1]: https://github.com/sebastianbergmann/type/compare/3.2.0...3.2.1
[3.2.0]: https://github.com/sebastianbergmann/type/compare/3.1.0...3.2.0
[3.1.0]: https://github.com/sebastianbergmann/type/compare/3.0.0...3.1.0
[3.0.0]: https://github.com/sebastianbergmann/type/compare/2.3.4...3.0.0
[2.3.4]: https://github.com/sebastianbergmann/type/compare/ca39369c41313ed12c071ed38ecda8fcdb248859...2.3.4
[2.3.3]: https://github.com/sebastianbergmann/type/compare/2.3.2...ca39369c41313ed12c071ed38ecda8fcdb248859
[2.3.2]: https://github.com/sebastianbergmann/type/compare/2.3.1...2.3.2
[2.3.1]: https://github.com/sebastianbergmann/type/compare/2.3.0...2.3.1
[2.3.0]: https://github.com/sebastianbergmann/type/compare/2.2.2...2.3.0
[2.2.2]: https://github.com/sebastianbergmann/type/compare/2.2.1...2.2.2
[2.2.1]: https://github.com/sebastianbergmann/type/compare/2.2.0...2.2.1
[2.2.0]: https://github.com/sebastianbergmann/type/compare/2.1.1...2.2.0
[2.1.1]: https://github.com/sebastianbergmann/type/compare/2.1.0...2.1.1
[2.1.0]: https://github.com/sebastianbergmann/type/compare/2.0.0...2.1.0
[2.0.0]: https://github.com/sebastianbergmann/type/compare/1.1.3...2.0.0
[1.1.3]: https://github.com/sebastianbergmann/type/compare/1.1.2...1.1.3
[1.1.2]: https://github.com/sebastianbergmann/type/compare/1.1.1...1.1.2
[1.1.1]: https://github.com/sebastianbergmann/type/compare/1.1.0...1.1.1
[1.1.0]: https://github.com/sebastianbergmann/type/compare/1.0.0...1.1.0
[1.0.0]: https://github.com/sebastianbergmann/type/compare/ff74aa41746bd8d10e931843ebf37d42da513ede...1.0.0
