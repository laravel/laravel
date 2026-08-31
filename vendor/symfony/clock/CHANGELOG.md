CHANGELOG
=========

7.1
---

 * Add `DatePoint::getMicrosecond()` and `DatePoint::setMicrosecond()`

6.4
---

 * Add `DatePoint`: an immutable DateTime implementation with stricter error handling and return types
 * Throw `DateMalformedStringException`/`DateInvalidTimeZoneException` when appropriate
 * Add `$modifier` argument to the `now()` helper

6.3
---

 * Add `ClockAwareTrait` to help write time-sensitive classes
 * Add `Clock` class and `now()` function

6.2
---

 * Add the component
