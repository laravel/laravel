# CHANGELOG

## v2.18.0

* Line numbers are now clickable.

## v2.17.0

* Support cursor IDE.

## v2.16.0

* Support PHP `8.4`.
* Drop support for PHP older than `7.1`.

## v2.15.4

* Improve link color in comments.

## v2.15.3

* Improve performance of the syntax highlighting (#758).

## v2.15.2

* Fixed missing code highlight, which additionally led to issue with switching tabs, between application and all frames ([#747](https://github.com/filp/whoops/issues/747)).

## v2.15.1

* Fixed bug with PrettyPageHandler "*Calling `getFrameFilters` method on null*" ([#751](https://github.com/filp/whoops/pull/751)).

## v2.15.0

* Add addFrameFilter ([#749](https://github.com/filp/whoops/pull/749))

## v2.14.6

* Upgraded prismJS to version `1.29.0` due to security issue ([#741][i741]).

[i741]: https://github.com/filp/whoops/pull/741

## v2.14.5

* Allow `ArrayAccess` on super globals.

## v2.14.4

* Fix PHP `5.5` support.
* Allow to use psr/log `2` or `3`.

## v2.14.3

* Support PHP `8.1`.

## v2.14.1

* Fix syntax highlighting scrolling too far.
* Improve the way we detect xdebug linkformat.

## v2.14.0

* Switched syntax highlighting to Prism.js.

Avoids licensing issues with prettify, and uses a maintained, modern project.

## v2.13.0

* Add Netbeans editor.

## v2.12.1

* Avoid redirecting away from an error.

## v2.12.0

* Hide non-string values in super globals when requested.

## v2.11.0

* Customize exit code.

## v2.10.0

* Better chaining on handler classes.

## v2.9.2

* Fix copy button styles.

## v2.9.1

* Fix xdebug function crash on PHP `8`.

## v2.9.0

* `JsonResponseHandler` includes the exception code.

## v2.8.0

* Support PHP 8.

## v2.7.3

* `PrettyPageHandler` functionality to hide superglobal keys has a clearer name 
(`hideSuperglobalKey`).

## v2.7.2

* `PrettyPageHandler` now accepts custom js files.
* `PrettyPageHandler` and `templateHelper` is now accessible through inheritance.

## v2.7.1

* Fix a PHP warning in some cases with anonymous classes.

## v2.7.0

* Added `removeFirstHandler` and `removeLastHandler`.

## v2.6.0

* Fix 2.4.0 `pushHandler` changing the order of handlers.

## v2.5.1

* Fix error messaging in a rare case.

## v2.5.0

* Automatically configure xdebug if available.

## v2.4.1

* Try harder to close all output buffers.

## v2.4.0

* Allow to prepend and append handlers.

## v2.3.2

* Various fixes from the community.

## v2.3.1

* Prevent exception in Whoops when caught exception frame is not related to real file.

## v2.3.0

* Show previous exception messages.

## v2.2.0

* Support PHP `7.2`.

## v2.1.0

* Add a `SystemFacade` to allow clients to override Whoops behavior.
* Show frame arguments in `PrettyPageHandler`.
* Highlight the line with the error.
* Add icons to search on Google and Stack Overflow.

## v2.0.0

Backwards compatibility breaking changes:

* `Run` class is now `final`. If you inherited from `Run`, please now instead use a custom `SystemFacade` injected into the `Run` constructor, or contribute your changes to our core.
* PHP < 5.5 support dropped.
