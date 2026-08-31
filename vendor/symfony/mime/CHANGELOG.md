CHANGELOG
=========

7.4
---

 * Deprecate implementing `__sleep/wakeup()` on `AbstractPart` implementations; use `__(un)serialize()` instead

7.0
---

 * Remove `Email::attachPart()`, use `Email::addPart()` instead
 * Argument `$body` is now required (at least null) in `Message::setBody()`
 * Require explicit argument when calling `Message::setBody()`

6.3
---

 * Support detection of related parts if `Content-Id` is used instead of the name
 * Add `TextPart::getDisposition()`

6.2
---

 * Add `File`
 * Deprecate `Email::attachPart()`, use `addPart()` instead
 * Deprecate calling `Message::setBody()` without arguments

6.1
---

 * Add `DataPart::getFilename()` and `DataPart::getContentType()`

6.0
---

 * Remove `Address::fromString()`, use `Address::create()` instead
 * Remove `Serializable` interface from `RawMessage`

5.2.0
-----

 * Add support for DKIM
 * Deprecated `Address::fromString()`, use `Address::create()` instead

4.4.0
-----

 * [BC BREAK] Removed `NamedAddress` (`Address` now supports a name)
 * Added PHPUnit constraints
 * Added `AbstractPart::asDebugString()`
 * Added `Address::fromString()`

4.3.3
-----

 * [BC BREAK] Renamed method `Headers::getAll()` to `Headers::all()`.

4.3.0
-----

 * Introduced the component as experimental
