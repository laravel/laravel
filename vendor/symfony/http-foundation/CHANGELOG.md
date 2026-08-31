CHANGELOG
=========

7.4
---

 * Add `#[WithHttpStatus]` to define status codes: 404 for `SignedUriException` and 403 for `ExpiredSignedUriException`
 * Add support for the `QUERY` HTTP method
 * Add support for structured MIME suffix
 * Add `Request::set/getAllowedHttpMethodOverride()` to list which HTTP methods can be overridden
 * Deprecate using `Request::sendHeaders()` after headers have already been sent; use a `StreamedResponse` instead
 * Deprecate method `Request::get()`, use properties `->attributes`, `query` or `request` directly instead
 * Make `Request::createFromGlobals()` parse the body of PUT, DELETE, PATCH and QUERY requests
 * Deprecate HTTP method override for methods GET, HEAD, CONNECT and TRACE; it will be ignored in Symfony 8.0
 * Deprecate accepting null `$format` argument to `Request::setFormat()`

7.3
---

 * Add support for iterable of string in `StreamedResponse`
 * Add `EventStreamResponse` and `ServerEvent` classes to streamline server event streaming
 * Add support for `valkey:` / `valkeys:` schemes for sessions
 * `Request::getPreferredLanguage()` now favors a more preferred language above exactly matching a locale
 * Allow `UriSigner` to use a `ClockInterface`
 * Add `UriSigner::verify()`

7.2
---

 * Add optional `$requests` parameter to `RequestStack::__construct()`
 * Add optional `$v4Bytes` and `$v6Bytes` parameters to `IpUtils::anonymize()`
 * Add `PRIVATE_SUBNETS` as a shortcut for private IP address ranges to `Request::setTrustedProxies()`
 * Deprecate passing `referer_check`, `use_only_cookies`, `use_trans_sid`, `trans_sid_hosts`, `trans_sid_tags`, `sid_bits_per_character` and `sid_length` options to `NativeSessionStorage`

7.1
---

 * Add optional `$expirationParameter` argument to `UriSigner::__construct()`
 * Add optional `$expiration` argument to `UriSigner::sign()`
 * Rename `$parameter` argument of `UriSigner::__construct()` to `$hashParameter`
 * Add `UploadedFile::getClientOriginalPath()`
 * Add `QueryParameterRequestMatcher`
 * Add `HeaderRequestMatcher`
 * Add support for `\SplTempFileObject` in `BinaryFileResponse`
 * Add `verbose` argument to response test constraints

7.0
---

 * Calling `ParameterBag::filter()` throws an `UnexpectedValueException` on invalid value, unless flag `FILTER_NULL_ON_FAILURE` is set
 * Calling `ParameterBag::getInt()` and `ParameterBag::getBool()` throws an `UnexpectedValueException` on invalid value
 * Remove classes `RequestMatcher` and `ExpressionRequestMatcher`
 * Remove `Request::getContentType()`, use `Request::getContentTypeFormat()` instead
 * Throw an `InvalidArgumentException` when calling `Request::create()` with a malformed URI
 * Require explicit argument when calling `JsonResponse::setCallback()`, `Response::setExpires/setLastModified/setEtag()`, `MockArraySessionStorage/NativeSessionStorage::setMetadataBag()`, `NativeSessionStorage::setSaveHandler()`
 * Add argument `$statusCode` to `Response::sendHeaders()` and `StreamedResponse::sendHeaders()`

6.4
---

 * Make `HeaderBag::getDate()`, `Response::getDate()`, `getExpires()` and `getLastModified()` return a `DateTimeImmutable`
 * Support root-level `Generator` in `StreamedJsonResponse`
 * Add `UriSigner` from the HttpKernel component
 * Add `partitioned` flag to `Cookie` (CHIPS Cookie)
 * Add argument `bool $flush = true` to `Response::send()`
 * Make `MongoDbSessionHandler` instantiable with the mongodb extension directly

6.3
---

 * Calling `ParameterBag::getDigit()`, `getAlnum()`, `getAlpha()` on an `array` throws a `UnexpectedValueException` instead of a `TypeError`
 * Add `ParameterBag::getString()` to convert a parameter into string and throw an exception if the value is invalid
 * Add `ParameterBag::getEnum()`
 * Create migration for session table when pdo handler is used
 * Add support for Relay PHP extension for Redis
 * The `Response::sendHeaders()` method now takes an optional HTTP status code as parameter, allowing to send informational responses such as Early Hints responses (103 status code)
 * Add `IpUtils::isPrivateIp()`
 * Add `Request::getPayload(): InputBag`
 * Deprecate conversion of invalid values in `ParameterBag::getInt()` and `ParameterBag::getBoolean()`,
 * Deprecate ignoring invalid values when using `ParameterBag::filter()`, unless flag `FILTER_NULL_ON_FAILURE` is set

6.2
---

 * Add `StreamedJsonResponse` class for efficient JSON streaming
 * The HTTP cache store uses the `xxh128` algorithm
 * Deprecate calling `JsonResponse::setCallback()`, `Response::setExpires/setLastModified/setEtag()`, `MockArraySessionStorage/NativeSessionStorage::setMetadataBag()`, `NativeSessionStorage::setSaveHandler()` without arguments
 * Add request matchers under the `Symfony\Component\HttpFoundation\RequestMatcher` namespace
 * Deprecate `RequestMatcher` in favor of `ChainRequestMatcher`
 * Deprecate `Symfony\Component\HttpFoundation\ExpressionRequestMatcher` in favor of `Symfony\Component\HttpFoundation\RequestMatcher\ExpressionRequestMatcher`

6.1
---

 * Add stale while revalidate and stale if error cache header
 * Allow dynamic session "ttl" when using a remote storage
 * Deprecate `Request::getContentType()`, use `Request::getContentTypeFormat()` instead

6.0
---

 * Remove the `NamespacedAttributeBag` class
 * Removed `Response::create()`, `JsonResponse::create()`,
   `RedirectResponse::create()`, `StreamedResponse::create()` and
   `BinaryFileResponse::create()` methods (use `__construct()` instead)
 * Not passing a `Closure` together with `FILTER_CALLBACK` to `ParameterBag::filter()` throws an `\InvalidArgumentException`; wrap your filter in a closure instead
 * Not passing a `Closure` together with `FILTER_CALLBACK` to `InputBag::filter()` throws an `\InvalidArgumentException`; wrap your filter in a closure instead
 * Removed the `Request::HEADER_X_FORWARDED_ALL` constant, use either `Request::HEADER_X_FORWARDED_FOR | Request::HEADER_X_FORWARDED_HOST | Request::HEADER_X_FORWARDED_PORT | Request::HEADER_X_FORWARDED_PROTO` or `Request::HEADER_X_FORWARDED_AWS_ELB` or `Request::HEADER_X_FORWARDED_TRAEFIK`constants instead
 * Rename `RequestStack::getMasterRequest()` to `getMainRequest()`
 * Not passing `FILTER_REQUIRE_ARRAY` or `FILTER_FORCE_ARRAY` flags to `InputBag::filter()` when filtering an array will throw `BadRequestException`
 * Removed the `Request::HEADER_X_FORWARDED_ALL` constant
 * Retrieving non-scalar values using `InputBag::get()` will throw `BadRequestException` (use `InputBad::all()` instead to retrieve an array)
 * Passing non-scalar default value as the second argument `InputBag::get()` will throw `\InvalidArgumentException`
 * Passing non-scalar, non-array value as the second argument `InputBag::set()` will throw `\InvalidArgumentException`
 * Passing `null` as `$requestIp` to `IpUtils::__checkIp()`, `IpUtils::__checkIp4()` or `IpUtils::__checkIp6()` is not supported anymore.

5.4
---

 * Deprecate passing `null` as `$requestIp` to `IpUtils::__checkIp()`, `IpUtils::__checkIp4()` or `IpUtils::__checkIp6()`, pass an empty string instead.
 * Add the `litespeed_finish_request` method to work with Litespeed
 * Deprecate `upload_progress.*` and `url_rewriter.tags` session options
 * Allow setting session options via DSN

5.3
---

 * Add the `SessionFactory`, `NativeSessionStorageFactory`, `PhpBridgeSessionStorageFactory` and `MockFileSessionStorageFactory` classes
 * Calling `Request::getSession()` when there is no available session throws a `SessionNotFoundException`
 * Add the `RequestStack::getSession` method
 * Deprecate the `NamespacedAttributeBag` class
 * Add `ResponseFormatSame` PHPUnit constraint
 * Deprecate the `RequestStack::getMasterRequest()` method and add `getMainRequest()` as replacement

5.2.0
-----

 * added support for `X-Forwarded-Prefix` header
 * added `HeaderUtils::parseQuery()`: it does the same as `parse_str()` but preserves dots in variable names
 * added `File::getContent()`
 * added ability to use comma separated ip addresses for `RequestMatcher::matchIps()`
 * added `Request::toArray()` to parse a JSON request body to an array
 * added `RateLimiter\RequestRateLimiterInterface` and `RateLimiter\AbstractRequestRateLimiter`
 * deprecated not passing a `Closure` together with `FILTER_CALLBACK` to `ParameterBag::filter()`; wrap your filter in a closure instead.
 * Deprecated the `Request::HEADER_X_FORWARDED_ALL` constant, use either `HEADER_X_FORWARDED_FOR | HEADER_X_FORWARDED_HOST | HEADER_X_FORWARDED_PORT | HEADER_X_FORWARDED_PROTO` or `HEADER_X_FORWARDED_AWS_ELB` or `HEADER_X_FORWARDED_TRAEFIK` constants instead.
 * Deprecated `BinaryFileResponse::create()`, use `__construct()` instead

5.1.0
-----

 * added `Cookie::withValue`, `Cookie::withDomain`, `Cookie::withExpires`,
   `Cookie::withPath`, `Cookie::withSecure`, `Cookie::withHttpOnly`,
   `Cookie::withRaw`, `Cookie::withSameSite`
 * Deprecate `Response::create()`, `JsonResponse::create()`,
   `RedirectResponse::create()`, and `StreamedResponse::create()` methods (use
   `__construct()` instead)
 * added `Request::preferSafeContent()` and `Response::setContentSafe()` to handle "safe" HTTP preference
   according to [RFC 8674](https://tools.ietf.org/html/rfc8674)
 * made the Mime component an optional dependency
 * added `MarshallingSessionHandler`, `IdentityMarshaller`
 * made `Session` accept a callback to report when the session is being used
 * Add support for all core cache control directives
 * Added `Symfony\Component\HttpFoundation\InputBag`
 * Deprecated retrieving non-string values using `InputBag::get()`, use `InputBag::all()` if you need access to the collection of values

5.0.0
-----

 * made `Cookie` auto-secure and lax by default
 * removed classes in the `MimeType` namespace, use the Symfony Mime component instead
 * removed method `UploadedFile::getClientSize()` and the related constructor argument
 * made `Request::getSession()` throw if the session has not been set before
 * removed `Response::HTTP_RESERVED_FOR_WEBDAV_ADVANCED_COLLECTIONS_EXPIRED_PROPOSAL`
 * passing a null url when instantiating a `RedirectResponse` is not allowed

4.4.0
-----

 * passing arguments to `Request::isMethodSafe()` is deprecated.
 * `ApacheRequest` is deprecated, use the `Request` class instead.
 * passing a third argument to `HeaderBag::get()` is deprecated, use method `all()` instead
 * [BC BREAK] `PdoSessionHandler` with MySQL changed the type of the lifetime column,
   make sure to run `ALTER TABLE sessions MODIFY sess_lifetime INTEGER UNSIGNED NOT NULL` to
   update your database.
 * `PdoSessionHandler` now precalculates the expiry timestamp in the lifetime column,
    make sure to run `CREATE INDEX expiry ON sessions (sess_lifetime)` to update your database
    to speed up garbage collection of expired sessions.
 * added `SessionHandlerFactory` to create session handlers with a DSN
 * added `IpUtils::anonymize()` to help with GDPR compliance.

4.3.0
-----

 * added PHPUnit constraints: `RequestAttributeValueSame`, `ResponseCookieValueSame`, `ResponseHasCookie`,
   `ResponseHasHeader`, `ResponseHeaderSame`, `ResponseIsRedirected`, `ResponseIsSuccessful`, and `ResponseStatusCodeSame`
 * deprecated `MimeTypeGuesserInterface` and `ExtensionGuesserInterface` in favor of `Symfony\Component\Mime\MimeTypesInterface`.
 * deprecated `MimeType` and `MimeTypeExtensionGuesser` in favor of `Symfony\Component\Mime\MimeTypes`.
 * deprecated `FileBinaryMimeTypeGuesser` in favor of `Symfony\Component\Mime\FileBinaryMimeTypeGuesser`.
 * deprecated `FileinfoMimeTypeGuesser` in favor of `Symfony\Component\Mime\FileinfoMimeTypeGuesser`.
 * added `UrlHelper` that allows to get an absolute URL and a relative path for a given path

4.2.0
-----

 * the default value of the "$secure" and "$samesite" arguments of Cookie's constructor
   will respectively change from "false" to "null" and from "null" to "lax" in Symfony
   5.0, you should define their values explicitly or use "Cookie::create()" instead.
 * added `matchPort()` in RequestMatcher

4.1.3
-----

 * [BC BREAK] Support for the IIS-only `X_ORIGINAL_URL` and `X_REWRITE_URL`
   HTTP headers has been dropped for security reasons.

4.1.0
-----

 * Query string normalization uses `parse_str()` instead of custom parsing logic.
 * Passing the file size to the constructor of the `UploadedFile` class is deprecated.
 * The `getClientSize()` method of the `UploadedFile` class is deprecated. Use `getSize()` instead.
 * added `RedisSessionHandler` to use Redis as a session storage
 * The `get()` method of the `AcceptHeader` class now takes into account the
   `*` and `*/*` default values (if they are present in the Accept HTTP header)
   when looking for items.
 * deprecated `Request::getSession()` when no session has been set. Use `Request::hasSession()` instead.
 * added `CannotWriteFileException`, `ExtensionFileException`, `FormSizeFileException`,
   `IniSizeFileException`, `NoFileException`, `NoTmpDirFileException`, `PartialFileException` to
   handle failed `UploadedFile`.
 * added `MigratingSessionHandler` for migrating between two session handlers without losing sessions
 * added `HeaderUtils`.

4.0.0
-----

 * the `Request::setTrustedHeaderName()` and `Request::getTrustedHeaderName()`
   methods have been removed
 * the `Request::HEADER_CLIENT_IP` constant has been removed, use
   `Request::HEADER_X_FORWARDED_FOR` instead
 * the `Request::HEADER_CLIENT_HOST` constant has been removed, use
   `Request::HEADER_X_FORWARDED_HOST` instead
 * the `Request::HEADER_CLIENT_PROTO` constant has been removed, use
   `Request::HEADER_X_FORWARDED_PROTO` instead
 * the `Request::HEADER_CLIENT_PORT` constant has been removed, use
   `Request::HEADER_X_FORWARDED_PORT` instead
 * checking for cacheable HTTP methods using the `Request::isMethodSafe()`
   method (by not passing `false` as its argument) is not supported anymore and
   throws a `\BadMethodCallException`
 * the `WriteCheckSessionHandler`, `NativeSessionHandler` and `NativeProxy` classes have been removed
 * setting session save handlers that do not implement `\SessionHandlerInterface` in
   `NativeSessionStorage::setSaveHandler()` is not supported anymore and throws a
   `\TypeError`

3.4.0
-----

 * implemented PHP 7.0's `SessionUpdateTimestampHandlerInterface` with a new
   `AbstractSessionHandler` base class and a new `StrictSessionHandler` wrapper
 * deprecated the `WriteCheckSessionHandler`, `NativeSessionHandler` and `NativeProxy` classes
 * deprecated setting session save handlers that do not implement `\SessionHandlerInterface` in `NativeSessionStorage::setSaveHandler()`
 * deprecated using `MongoDbSessionHandler` with the legacy mongo extension; use it with the mongodb/mongodb package and ext-mongodb instead
 * deprecated `MemcacheSessionHandler`; use `MemcachedSessionHandler` instead

3.3.0
-----

 * the `Request::setTrustedProxies()` method takes a new `$trustedHeaderSet` argument,
   see https://symfony.com/doc/current/deployment/proxies.html for more info,
 * deprecated the `Request::setTrustedHeaderName()` and `Request::getTrustedHeaderName()` methods,
 * added `File\Stream`, to be passed to `BinaryFileResponse` when the size of the served file is unknown,
   disabling `Range` and `Content-Length` handling, switching to chunked encoding instead
 * added the `Cookie::fromString()` method that allows to create a cookie from a
   raw header string

3.1.0
-----

 * Added support for creating `JsonResponse` with a string of JSON data

3.0.0
-----

 * The precedence of parameters returned from `Request::get()` changed from "GET, PATH, BODY" to "PATH, GET, BODY"

2.8.0
-----

 * Finding deep items in `ParameterBag::get()` is deprecated since version 2.8 and
   will be removed in 3.0.

2.6.0
-----

 * PdoSessionHandler changes
   - implemented different session locking strategies to prevent loss of data by concurrent access to the same session
   - [BC BREAK] save session data in a binary column without base64_encode
   - [BC BREAK] added lifetime column to the session table which allows to have different lifetimes for each session
   - implemented lazy connections that are only opened when a session is used by either passing a dsn string
     explicitly or falling back to session.save_path ini setting
   - added a createTable method that initializes a correctly defined table depending on the database vendor

2.5.0
-----

 * added `JsonResponse::setEncodingOptions()` & `JsonResponse::getEncodingOptions()` for easier manipulation
   of the options used while encoding data to JSON format.

2.4.0
-----

 * added RequestStack
 * added Request::getEncodings()
 * added accessors methods to session handlers

2.3.0
-----

 * added support for ranges of IPs in trusted proxies
 * `UploadedFile::isValid` now returns false if the file was not uploaded via HTTP (in a non-test mode)
 * Improved error-handling of `\Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler`
   to ensure the supplied PDO handler throws Exceptions on error (as the class expects). Added related test cases
   to verify that Exceptions are properly thrown when the PDO queries fail.

2.2.0
-----

 * fixed the Request::create() precedence (URI information always take precedence now)
 * added Request::getTrustedProxies()
 * deprecated Request::isProxyTrusted()
 * [BC BREAK] JsonResponse does not turn a top level empty array to an object anymore, use an ArrayObject to enforce objects
 * added a IpUtils class to check if an IP belongs to a CIDR
 * added Request::getRealMethod() to get the "real" HTTP method (getMethod() returns the "intended" HTTP method)
 * disabled _method request parameter support by default (call Request::enableHttpMethodParameterOverride() to
   enable it, and Request::getHttpMethodParameterOverride() to check if it is supported)
 * Request::splitHttpAcceptHeader() method is deprecated and will be removed in 2.3
 * Deprecated Flashbag::count() and \Countable interface, will be removed in 2.3

2.1.0
-----

 * added Request::getSchemeAndHttpHost() and Request::getUserInfo()
 * added a fluent interface to the Response class
 * added Request::isProxyTrusted()
 * added JsonResponse
 * added a getTargetUrl method to RedirectResponse
 * added support for streamed responses
 * made Response::prepare() method the place to enforce HTTP specification
 * [BC BREAK] moved management of the locale from the Session class to the Request class
 * added a generic access to the PHP built-in filter mechanism: ParameterBag::filter()
 * made FileBinaryMimeTypeGuesser command configurable
 * added Request::getUser() and Request::getPassword()
 * added support for the PATCH method in Request
 * removed the ContentTypeMimeTypeGuesser class as it is deprecated and never used on PHP 5.3
 * added ResponseHeaderBag::makeDisposition() (implements RFC 6266)
 * made mimetype to extension conversion configurable
 * [BC BREAK] Moved all session related classes and interfaces into own namespace, as
   `Symfony\Component\HttpFoundation\Session` and renamed classes accordingly.
   Session handlers are located in the subnamespace `Symfony\Component\HttpFoundation\Session\Handler`.
 * SessionHandlers must implement `\SessionHandlerInterface` or extend from the
   `Symfony\Component\HttpFoundation\Storage\Handler\NativeSessionHandler` base class.
 * Added internal storage driver proxy mechanism for forward compatibility with
   PHP 5.4 `\SessionHandler` class.
 * Added session handlers for custom Memcache, Memcached and Null session save handlers.
 * [BC BREAK] Removed `NativeSessionStorage` and replaced with `NativeFileSessionHandler`.
 * [BC BREAK] `SessionStorageInterface` methods removed: `write()`, `read()` and
   `remove()`.  Added `getBag()`, `registerBag()`.  The `NativeSessionStorage` class
   is a mediator for the session storage internals including the session handlers
   which do the real work of participating in the internal PHP session workflow.
 * [BC BREAK] Introduced mock implementations of `SessionStorage` to enable unit
   and functional testing without starting real PHP sessions.  Removed
   `ArraySessionStorage`, and replaced with `MockArraySessionStorage` for unit
   tests; removed `FilesystemSessionStorage`, and replaced with`MockFileSessionStorage`
   for functional tests.  These do not interact with global session ini
   configuration values, session functions or `$_SESSION` superglobal. This means
   they can be configured directly allowing multiple instances to work without
   conflicting in the same PHP process.
 * [BC BREAK] Removed the `close()` method from the `Session` class, as this is
   now redundant.
 * Deprecated the following methods from the Session class: `setFlash()`, `setFlashes()`
   `getFlash()`, `hasFlash()`, and `removeFlash()`. Use `getFlashBag()` instead
   which returns a `FlashBagInterface`.
 * `Session->clear()` now only clears session attributes as before it cleared
   flash messages and attributes. `Session->getFlashBag()->all()` clears flashes now.
 * Session data is now managed by `SessionBagInterface` to better encapsulate
   session data.
 * Refactored session attribute and flash messages system to their own
  `SessionBagInterface` implementations.
 * Added `FlashBag`. Flashes expire when retrieved by `get()` or `all()`. This
   implementation is ESI compatible.
 * Added `AutoExpireFlashBag` (default) to replicate Symfony 2.0.x auto expire
   behavior of messages auto expiring after one page page load.  Messages must
   be retrieved by `get()` or `all()`.
 * Added `Symfony\Component\HttpFoundation\Attribute\AttributeBag` to replicate
   attributes storage behavior from 2.0.x (default).
 * Added `Symfony\Component\HttpFoundation\Attribute\NamespacedAttributeBag` for
   namespace session attributes.
 * Flash API can stores messages in an array so there may be multiple messages
   per flash type.  The old `Session` class API remains without BC break as it
   will allow single messages as before.
 * Added basic session meta-data to the session to record session create time,
   last updated time, and the lifetime of the session cookie that was provided
   to the client.
 * Request::getClientIp() method doesn't take a parameter anymore but bases
   itself on the trustProxy parameter.
 * Added isMethod() to Request object.
 * [BC BREAK] The methods `getPathInfo()`, `getBaseUrl()` and `getBasePath()` of
   a `Request` now all return a raw value (vs a urldecoded value before). Any call
   to one of these methods must be checked and wrapped in a `rawurldecode()` if
   needed.
