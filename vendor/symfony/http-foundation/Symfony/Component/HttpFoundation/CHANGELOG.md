CHANGELOG
=========

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
   behaviour of messages auto expiring after one page page load.  Messages must
   be retrieved by `get()` or `all()`.
 * Added `Symfony\Component\HttpFoundation\Attribute\AttributeBag` to replicate
   attributes storage behaviour from 2.0.x (default).
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
