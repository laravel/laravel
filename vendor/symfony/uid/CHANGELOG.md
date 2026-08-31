CHANGELOG
=========

7.4
---

 * Add microsecond precision to UUIDv7
 * Default to `UuidV7` when using `UuidFactory`
 * Add `MockUuidFactory` to allow deterministic and mockable UUID generation for testing purposes

7.3
---

 * Add component-specific exception hierarchy

7.2
---

 * Make `AbstractUid` implement `Ds\Hashable` if available
 * Add support for binary, base-32 and base-58 representations in `Uuid::isValid()`
 * Add the `Uuid::FORMAT_RFC_9562` constant to validate UUIDs in the RFC 9562 format

7.1
---

 * Add `UuidV1::toV6()`, `UuidV1::toV7()` and `UuidV6::toV7()`
 * Add `AbstractUid::toString()`

6.2
---

 * Add `UuidV7` and `UuidV8`
 * Add `TimeBasedUidInterface` to describe UIDs that embed a timestamp
 * Add `MaxUuid` and `MaxUlid`

5.4
---

 * Add `NilUlid`

5.3
---

 * The component is not marked as `@experimental` anymore
 * Add `AbstractUid::fromBinary()`, `AbstractUid::fromBase58()`, `AbstractUid::fromBase32()` and `AbstractUid::fromRfc4122()`
 * [BC BREAK] Replace `UuidV1::getTime()`, `UuidV6::getTime()` and `Ulid::getTime()` by `UuidV1::getDateTime()`, `UuidV6::getDateTime()` and `Ulid::getDateTime()`
 * Add `Uuid::NAMESPACE_*` constants from RFC4122
 * Add `UlidFactory`, `UuidFactory`, `RandomBasedUuidFactory`, `TimeBasedUuidFactory` and `NameBasedUuidFactory`
 * Add commands to generate and inspect UUIDs and ULIDs

5.2.0
-----

 * made UUIDv6 always return truly random node fields to prevent leaking the MAC of the host

5.1.0
-----

 * added support for UUID
 * added support for ULID
 * added the component
