CHANGELOG
=========

7.3
---

 * Add `error:dump` command

7.1
---

 * Increase log level to "error" at least for all PHP errors

6.4
---

 * `FlattenExceptionNormalizer` no longer implements `ContextAwareNormalizerInterface`

6.3
---

 * Display exception properties in the HTML error page

6.1
---

 * Report overridden `@final` constants and properties
 * Read environment variable `SYMFONY_IDE` to configure file link format

5.4
---

 * Make `DebugClassLoader` trigger deprecation notices on missing return types
 * Add `SYMFONY_PATCH_TYPE_DECLARATIONS='force=2'` mode to `DebugClassLoader` to turn annotations into native return types

5.2.0
-----

 * added the ability to set `HtmlErrorRenderer::$template` to a custom template to render when not in debug mode.

5.1.0
-----

 * The `HtmlErrorRenderer` and `SerializerErrorRenderer` add `X-Debug-Exception` and `X-Debug-Exception-File` headers in debug mode.

4.4.0
-----

 * added the component
 * added `ErrorHandler::call()` method utility to turn any PHP error into `\ErrorException`
