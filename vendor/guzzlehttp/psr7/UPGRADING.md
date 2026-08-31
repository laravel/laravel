Guzzle PSR-7 Upgrade Guide
==========================

1.x to 2.0
----------

Guzzle PSR-7 2.0 is a major release that removes deprecated APIs, raises the
minimum PHP version, and adds PHP 7 parameter and return types. Applications that
only depend on PSR-7 interfaces should usually need small changes. Applications
that call helper functions, extend package classes, or pass invalid argument
types need closer review.

#### PHP Version and Dependencies

Guzzle PSR-7 2.0 requires PHP `^7.2.5 || ^8.0`. Guzzle PSR-7 1.x supported PHP
`>=5.4.0`.

Composer dependency changes that can affect upgrades:

- `ralouphie/getallheaders` v2 support was dropped; 2.0 requires `^3.0`.
- `psr/http-factory:^1.0` is required because 2.0 ships PSR-17 factories through `GuzzleHttp\Psr7\HttpFactory`.

#### PHP 7 Type Hints and Return Types

Type hints and return types were added wherever possible. Please make sure:

- You pass values of the documented type when calling methods and functions.
- Classes that extend Guzzle PSR-7 classes update any overridden method signatures to remain compatible.
- Code that expected package-specific `InvalidArgumentException` exceptions for invalid argument types may now receive PHP `TypeError` exceptions instead.

Common examples include passing a real integer status code to `Response::__construct()` and passing a string method to `Request::__construct()`.

#### Removed Function API

The static API was introduced in 1.7.0 to mitigate problems with functions
conflicting between global and local copies of the package. The function API was
removed in 2.0.0, along with the Composer `files` autoload entry that loaded
`src/functions_include.php`.

Replace namespaced function calls with the corresponding static methods in the
`GuzzleHttp\Psr7` namespace:

```php
// Before:
use function GuzzleHttp\Psr7\stream_for;

$stream = stream_for('body');

// After:
use GuzzleHttp\Psr7\Utils;

$stream = Utils::streamFor('body');
```

| Original Function | Replacement Method |
|-------------------|--------------------|
| `str` | `Message::toString` |
| `uri_for` | `Utils::uriFor` |
| `stream_for` | `Utils::streamFor` |
| `parse_header` | `Header::parse` |
| `normalize_header` | `Header::normalize` |
| `modify_request` | `Utils::modifyRequest` |
| `rewind_body` | `Message::rewindBody` |
| `try_fopen` | `Utils::tryFopen` |
| `copy_to_string` | `Utils::copyToString` |
| `copy_to_stream` | `Utils::copyToStream` |
| `hash` | `Utils::hash` |
| `readline` | `Utils::readLine` |
| `parse_request` | `Message::parseRequest` |
| `parse_response` | `Message::parseResponse` |
| `parse_query` | `Query::parse` |
| `build_query` | `Query::build` |
| `mimetype_from_filename` | `MimeType::fromFilename` |
| `mimetype_from_extension` | `MimeType::fromExtension` |
| `_parse_message` | `Message::parseMessage` |
| `_parse_request_uri` | `Message::parseRequestUri` |
| `get_message_body_summary` | `Message::bodySummary` |
| `_caseless_remove` | `Utils::caselessRemove` |

`Header::normalize()` remains the direct 2.0 replacement for
`normalize_header()`. In newer 2.x versions, prefer `Header::splitList()` for
new code.

#### Deprecated URI Methods Removed

The deprecated `Uri::resolve()` and `Uri::removeDotSegments()` methods were
removed. Use `UriResolver` instead.

```php
// Before:
$resolved = Uri::resolve($base, '../path');
$path = Uri::removeDotSegments('/a/../b');

// After:
use GuzzleHttp\Psr7\UriResolver;
use GuzzleHttp\Psr7\Utils;

$resolved = UriResolver::resolve($base, Utils::uriFor('../path'));
$path = UriResolver::removeDotSegments('/a/../b');
```

#### Stricter URI Validation

Guzzle PSR-7 1.x automatically fixed a URI that combined an authority with a
relative path by prepending `/` to the path. That deprecated behavior was removed
in 2.0. Such URIs now throw `InvalidArgumentException`.

```php
// Before: automatically converted to //example.com/foo.
$uri = (new Uri())->withHost('example.com')->withPath('foo');

// After: make the absolute path explicit.
$uri = (new Uri())->withHost('example.com')->withPath('/foo');
```

#### Header Validation

Header names are validated more strictly according to RFC 7230 token syntax.
Names containing whitespace, `/`, `(`, `)`, `\\`, or other invalid characters are
rejected.

If you construct messages from untrusted or non-standard input, normalize or
reject invalid header names before constructing `Request`, `Response`, or
`ServerRequest` instances.

#### Query String Boolean Serialization

`Query::build()` now serializes booleans as `1` and `0`, matching
`http_build_query()` behavior.

```php
Query::build(['enabled' => true, 'disabled' => false]);
// enabled=1&disabled=0
```

In current 2.x versions, pass `false` as the third argument if you need textual
boolean values:

```php
Query::build(['enabled' => true, 'disabled' => false], PHP_QUERY_RFC3986, false);
// enabled=true&disabled=false
```

#### Final Stream and Decorator Classes

Several classes that were annotated with `@final` in 1.x are declared `final` in
2.0:

- `AppendStream`
- `BufferStream`
- `CachingStream`
- `DroppingStream`
- `FnStream`
- `InflateStream`
- `LazyOpenStream`
- `LimitStream`
- `MultipartStream`
- `NoSeekStream`
- `PumpStream`
- `StreamWrapper`

If your code extends one of these classes, replace inheritance with composition.
For custom streams, implement `Psr\Http\Message\StreamInterface` directly or use
`GuzzleHttp\Psr7\StreamDecoratorTrait` in your own class.

`Request`, `Response`, `ServerRequest`, `Stream`, `UploadedFile`, and `Uri` remain
extendable in 2.0, but overridden methods must have compatible signatures.

#### Public Constants and Internal Details

Some constants that were public in 1.x are implementation details in 2.0:

- `Stream::READABLE_MODES`
- `Stream::WRITABLE_MODES`
- `Uri::HTTP_DEFAULT_HOST`

If your code used these constants, define application-specific constants instead
of depending on package internals.

#### Stream Behavior Changes

`BufferStream::write()` returns `0` instead of `false` when the buffer exceeds
its high-water mark. This keeps the method compatible with the `int` return type
from `StreamInterface::write()`.

Several stream `__toString()` implementations now catch `Throwable`. On PHP 7.4
and newer, exceptions thrown during stringification are rethrown. Avoid relying
on `(string) $stream` to hide read failures; call `getContents()` or `read()` and
handle exceptions when failures are possible.

#### PSR-17 Factories

Guzzle PSR-7 2.0 adds `GuzzleHttp\Psr7\HttpFactory`, an implementation of the
PSR-17 factory interfaces from `psr/http-factory`. This is additive, but it is
the reason for the new required dependency.

For the full 2.0 diff, see
https://github.com/guzzle/psr7/compare/1.8.1...2.0.0.
