CHANGELOG
=========

7.4
---

 * Add support for adding more default casters to `AbstractCloner::addDefaultCasters()`
 * Select HtmlDumper only if `Accept` header contains "html"

7.3
---

 * Add casters for `Dba\Connection`, `SQLite3Result`, `OpenSSLAsymmetricKey` and `OpenSSLCertificateSigningRequest`
 * Deprecate `ResourceCaster::castCurl()`, `ResourceCaster::castGd()` and `ResourceCaster::castOpensslX509()`
 * Mark all casters as `@internal`

7.2
---

 * Add support for `FORCE_COLOR` environment variable
 * Add support for virtual properties

7.1
---

 * Add support for new DOM extension classes in `DOMCaster`

7.0
---

 * Add argument `$label` to `VarDumper::dump()`
 * Require explicit argument when calling `VarDumper::setHandler()`
 * Remove display of backtrace in `Twig_Template`, only `Twig\Template` is supported

6.4
---

 * Dump uninitialized properties

6.3
---

 * Add caster for `WeakMap`
 * Add support of named arguments to `dd()` and `dump()` to display the argument name
 * Add support for `Relay\Relay`
 * Add display of invisible characters

6.2
---

 * Add support for `FFI\CData` and `FFI\CType`
 * Deprecate calling `VarDumper::setHandler()` without arguments

5.4
---

 * Add ability to style integer and double values independently
 * Add casters for Symfony's UUIDs and ULIDs
 * Add support for `Fiber`

5.2.0
-----

 * added support for PHPUnit `--colors` option
 * added `VAR_DUMPER_FORMAT=server` env var value support
 * prevent replacing the handler when the `VAR_DUMPER_FORMAT` env var is set

5.1.0
-----

 * added `RdKafka` support

4.4.0
-----

 * added `VarDumperTestTrait::setUpVarDumper()` and `VarDumperTestTrait::tearDownVarDumper()`
   to configure casters & flags to use in tests
 * added `ImagineCaster` and infrastructure to dump images
 * added the stamps of a message after it is dispatched in `TraceableMessageBus` and `MessengerDataCollector` collected data
 * added `UuidCaster`
 * made all casters final
 * added support for the `NO_COLOR` env var (https://no-color.org/)

4.3.0
-----

 * added `DsCaster` to support dumping the contents of data structures from the Ds extension

4.2.0
-----

 * support selecting the format to use by setting the environment variable `VAR_DUMPER_FORMAT` to `html` or `cli`

4.1.0
-----

 * added a `ServerDumper` to send serialized Data clones to a server
 * added a `ServerDumpCommand` and `DumpServer` to run a server collecting
   and displaying dumps on a single place with multiple formats support
 * added `CliDescriptor` and `HtmlDescriptor` descriptors for `server:dump` CLI and HTML formats support

4.0.0
-----

 * support for passing `\ReflectionClass` instances to the `Caster::castObject()`
   method has been dropped, pass class names as strings instead
 * the `Data::getRawData()` method has been removed
 * the `VarDumperTestTrait::assertDumpEquals()` method expects a 3rd `$filter = 0`
   argument and moves `$message = ''` argument at 4th position.
 * the `VarDumperTestTrait::assertDumpMatchesFormat()` method expects a 3rd `$filter = 0`
   argument and moves `$message = ''` argument at 4th position.

3.4.0
-----

 * added `AbstractCloner::setMinDepth()` function to ensure minimum tree depth
 * deprecated `MongoCaster`

2.7.0
-----

 * deprecated `Cloner\Data::getLimitedClone()`. Use `withMaxDepth`, `withMaxItemsPerDepth` or `withRefHandles` instead.
