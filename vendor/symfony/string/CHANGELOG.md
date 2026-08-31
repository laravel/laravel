CHANGELOG
=========

7.4
---

 * Deprecate implementing `__sleep/wakeup()` on string implementations

7.3
---

 * Add the `AbstractString::pascal()` method

7.2
---

 * Add `TruncateMode` enum to handle more truncate methods
 * Add the `AbstractString::kebab()` method

7.1
---

 * Add `localeLower()`, `localeUpper()`, `localeTitle()` methods to `AbstractUnicodeString`

6.2
---

  * Add support for emoji in `AsciiSlugger`

5.4
---

 * Add `trimSuffix()` and `trimPrefix()` methods

5.3
---

 * Made `AsciiSlugger` fallback to parent locale's symbolsMap

5.2.0
-----

 * added a `FrenchInflector` class

5.1.0
-----

 * added the `AbstractString::reverse()` method
 * made `AbstractString::width()` follow POSIX.1-2001
 * added `LazyString` which provides memoizing stringable objects
 * The component is not marked as `@experimental` anymore
 * added the `s()` helper method to get either an `UnicodeString` or `ByteString` instance,
   depending of the input string UTF-8 compliance
 * added `$cut` parameter to `Symfony\Component\String\AbstractString::truncate()`
 * added `AbstractString::containsAny()`
 * allow passing a string of custom characters to `ByteString::fromRandom()`

5.0.0
-----

 * added the component as experimental
