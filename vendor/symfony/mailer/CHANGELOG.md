CHANGELOG
=========

7.4
---

 * Add `logger` (constructor) property to `RoundRobinTransport`

7.3
---

 * Add DSN param `retry_period` to override default email transport retry period
 * Add `Dsn::getBooleanOption()`
 * Add DSN param `source_ip` to allow binding to a (specific) IPv4 or IPv6 address.
 * Add DSN param `require_tls` to enforce use of TLS/STARTTLS
 * Add `DkimSignedMessageListener`, `SmimeEncryptedMessageListener`, and `SmimeSignedMessageListener`

7.2
---

 * Deprecate `TransportFactoryTestCase`, extend `AbstractTransportFactoryTestCase` instead

   The `testIncompleteDsnException()` test is no longer provided by default. If you make use of it by implementing the `incompleteDsnProvider()` data providers,
   you now need to use the `IncompleteDsnTestTrait`.

 * Make `TransportFactoryTestCase` compatible with PHPUnit 10+
 * Support unicode email addresses such as "dømi@dømi.example"

7.1
---

 * Dispatch Postmark's "406 - Inactive recipient" API error code as a `PostmarkDeliveryEvent` instead of throwing an exception
 * Add DSN param `auto_tls` to disable automatic STARTTLS
 * Add support for allowing some users even if `recipients` is defined in `EnvelopeListener`

7.0
---

 * Remove the OhMySmtp bridge in favor of the MailPace bridge

6.4
---

 * Add DSN parameter `peer_fingerprint` to verify TLS certificate fingerprint
 * Change the default port for the `mailjet+smtp` transport from 465 to 587

6.3
---

 * Add `MessageEvent::reject()` to allow rejecting an email before sending it
 * Change the default port for the `mailgun+smtp` transport from 465 to 587
 * Add `$authenticators` parameter in `EsmtpTransport` constructor and `EsmtpTransport::setAuthenticators()`
  to allow overriding of default eSMTP authenticators

6.2.7
-----

 * [BC BREAK] The following data providers for `TransportFactoryTestCase` are now static:
  `supportsProvider()`, `createProvider()`, `unsupportedSchemeProvider()`and `incompleteDsnProvider()`

6.2
---

 * Add a `mailer:test` command
 * Add `SentMessageEvent` and `FailedMessageEvent` events

6.1
---

 * Make `start()` and `stop()` methods public on `SmtpTransport`
 * Improve extensibility of `EsmtpTransport`

6.0
---

 * The `HttpTransportException` class takes a string at first argument

5.4
---

 * Enable the mailer to operate on any PSR-14-compatible event dispatcher

5.3
---

 * added the `mailer` monolog channel and set it on all transport definitions

5.2.0
-----

 * added `NativeTransportFactory` to configure a transport based on php.ini settings
 * added `local_domain`, `restart_threshold`, `restart_threshold_sleep` and `ping_threshold` options for `smtp`
 * added `command` option for `sendmail`

4.4.0
-----

 * [BC BREAK] changed the `NullTransport` DSN from `smtp://null` to `null://null`
 * [BC BREAK] renamed `SmtpEnvelope` to `Envelope`, renamed `DelayedSmtpEnvelope` to
   `DelayedEnvelope`
 * [BC BREAK] changed the syntax for failover and roundrobin DSNs

   Before:

   dummy://a || dummy://b (for failover)
   dummy://a && dummy://b (for roundrobin)

   After:

   failover(dummy://a dummy://b)
   roundrobin(dummy://a dummy://b)

 * added support for multiple transports on a `Mailer` instance
 * [BC BREAK] removed the `auth_mode` DSN option (it is now always determined automatically)
 * STARTTLS cannot be enabled anymore (it is used automatically if TLS is disabled and the server supports STARTTLS)
 * [BC BREAK] Removed the `encryption` DSN option (use `smtps` instead)
 * Added support for the `smtps` protocol (does the same as using `smtp` and port `465`)
 * Added PHPUnit constraints
 * Added `MessageDataCollector`
 * Added `MessageEvents` and `MessageLoggerListener` to allow collecting sent emails
 * [BC BREAK] `TransportInterface` has a new `__toString()` method
 * [BC BREAK] Classes `AbstractApiTransport` and `AbstractHttpTransport` moved under `Transport` sub-namespace.
 * [BC BREAK] Transports depend on `Symfony\Contracts\EventDispatcher\EventDispatcherInterface`
   instead of `Symfony\Component\EventDispatcher\EventDispatcherInterface`.
 * Added possibility to register custom transport for dsn by implementing
   `Symfony\Component\Mailer\Transport\TransportFactoryInterface` and tagging with `mailer.transport_factory` tag in DI.
 * Added `Symfony\Component\Mailer\Test\TransportFactoryTestCase` to ease testing custom transport factories.
 * Added `SentMessage::getDebug()` and `TransportExceptionInterface::getDebug` to help debugging
 * Made `MessageEvent` final
 * add DSN parameter `verify_peer` to disable TLS peer verification for SMTP transport

4.3.0
-----

 * Added the component.
