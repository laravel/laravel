CHANGELOG
=========

2.4.0
-----

 * The switch user listener now preserves the query string when switching a user
 * The remember-me cookie hashes now use HMAC, which means that current cookies will be invalidated
 * added simpler customization options
 * structured component into three sub-components Acl, Core and Http
 * added Csrf sub-component
 * changed Http sub-component to depend on Csrf sub-component instead of the Form component

2.3.0
-----

 * [BC BREAK] the BCrypt encoder constructor signature has changed (the first argument was removed)
   To use the BCrypt encoder, you now need PHP 5.5 or "ircmaxell/password-compat" as a composer dependency
 * [BC BREAK] return 401 instead of 500 when using use_forward during for form authentication
 * added a `require_previous_session` option to `AbstractAuthenticationListener`

2.2.0
-----

 * `Symfony\Component\Security\Http\Firewall` and
   `Symfony\Component\Security\Http\RememberMe\ResponseListener` now
   implements EventSubscriberInterface
 * added secure random number generator
 * added PBKDF2 Password encoder
 * added BCrypt password encoder

2.1.0
-----

 * [BC BREAK] The signature of ExceptionListener has changed
 * changed the HttpUtils constructor signature to take a UrlGenerator and a UrlMatcher instead of a Router
 * EncoderFactoryInterface::getEncoder() can now also take a class name as an argument
 * allow switching to the user that is already impersonated
 * added support for the remember_me parameter in the query
 * added AccessMapInterface
 * [BC BREAK] moved user comparison logic out of UserInterface
 * made the logout path check configurable
 * after login, the user is now redirected to `default_target_path` if
   `use_referer` is true and the referrer is the `login_path`.
 * added a way to remove a token from a session
 * [BC BREAK] changed `MutableAclInterface::setParentAcl` to accept `null`,
   review your implementation to reflect this change.
 * `ObjectIdentity::fromDomainObject`, `UserSecurityIdentity::fromAccount` and
   `UserSecurityIdentity::fromToken` now return correct identities for proxies
   objects (e.g. Doctrine proxies)
 * [BC BREAK] moved the default authentication success and failure handling to
   separate classes. The order of arguments in the constructor of the
   `AbstractAuthenticationListener` has changed.
 * [BC BREAK] moved the default logout success handling to a separate class. The
   order of arguments in the constructor of `LogoutListener` has changed.
 * [BC BREAK] The constructor of `AuthenticationException` and all child
   classes now matches the constructor of `\Exception`. The extra information
   getters and setters are removed. There are now dedicated getters/setters for
   token (`AuthenticationException'), user (`AccountStatusException`) and
   username (`UsernameNotFoundException`).
