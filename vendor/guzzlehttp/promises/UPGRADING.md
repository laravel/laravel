Guzzle Promises Upgrade Guide
=============================

1.x to 2.0
----------

Guzzle Promises 2.0 is a major release that removes deprecated APIs, raises the
minimum PHP version, and adds PHP 7 parameter and return types. Applications that
only use the object-oriented API should usually need small changes. Applications
that call helper functions, implement package interfaces, extend package classes,
or pass invalid argument types need closer review.

#### PHP Version and Dependencies

Guzzle Promises 2.0 requires PHP `^7.2.5 || ^8.0`. Guzzle Promises 1.x
supported PHP `>=5.5`.

#### PHP 7 Type Hints and Return Types

Type hints and return types were added wherever possible. Please make sure:

- You pass values of the documented type when calling methods and functions.
- Classes that implement `PromiseInterface`, `PromisorInterface`, or
  `TaskQueueInterface` update method signatures to remain compatible.
- Classes that extend Guzzle Promises classes update any overridden method
  signatures to remain compatible.
- Code that expected package-specific exceptions for invalid argument types may
  now receive PHP `TypeError` exceptions instead.

#### Soft-Final Classes

All previously non-final non-exception classes are now final or annotated with
`@final`. If your code extends one of these classes, replace inheritance with
composition or implement the relevant interface directly.

#### Removed Function API

The static API was introduced in 1.4.0 to mitigate problems with functions
conflicting between global and local copies of the package. The function API was
removed in 2.0.0, along with the Composer `files` autoload entry that loaded
`src/functions_include.php`.

Replace namespaced function calls with the corresponding static methods in the
`GuzzleHttp\Promise` namespace:

```php
// Before:
use function GuzzleHttp\Promise\promise_for;

$promise = promise_for('value');

// After:
use GuzzleHttp\Promise\Create;

$promise = Create::promiseFor('value');
```

| Original Function | Replacement Method |
|-------------------|--------------------|
| `queue` | `Utils::queue` |
| `task` | `Utils::task` |
| `promise_for` | `Create::promiseFor` |
| `rejection_for` | `Create::rejectionFor` |
| `exception_for` | `Create::exceptionFor` |
| `iter_for` | `Create::iterFor` |
| `inspect` | `Utils::inspect` |
| `inspect_all` | `Utils::inspectAll` |
| `unwrap` | `Utils::unwrap` |
| `all` | `Utils::all` |
| `some` | `Utils::some` |
| `any` | `Utils::any` |
| `settle` | `Utils::settle` |
| `each` | `Each::of` |
| `each_limit` | `Each::ofLimit` |
| `each_limit_all` | `Each::ofLimitAll` |
| `!is_fulfilled` | `Is::pending` |
| `is_fulfilled` | `Is::fulfilled` |
| `is_rejected` | `Is::rejected` |
| `is_settled` | `Is::settled` |
| `coroutine` | `Coroutine::of` |

For the full 2.0 diff, see
https://github.com/guzzle/promises/compare/1.5.3...2.0.0.
