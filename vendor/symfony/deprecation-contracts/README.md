Symfony Deprecation Contracts
=============================

A generic function and convention to trigger deprecation notices.

This package provides a single global function named `trigger_deprecation()` that triggers silenced deprecation notices.

By using a custom PHP error handler such as the one provided by the Symfony ErrorHandler component,
the triggered deprecations can be caught and logged for later discovery, both on dev and prod environments.

The function requires at least 3 arguments:
 - the name of the Composer package that is triggering the deprecation
 - the version of the package that introduced the deprecation
 - the message of the deprecation
 - more arguments can be provided: they will be inserted in the message using `printf()` formatting

Example:
```php
trigger_deprecation('symfony/blockchain', '8.9', 'Using "%s" is deprecated, use "%s" instead.', 'bitcoin', 'fabcoin');
```

This will generate the following message:
`Since symfony/blockchain 8.9: Using "bitcoin" is deprecated, use "fabcoin" instead.`

While not recommended, the deprecation notices can be completely ignored by declaring an empty
`function trigger_deprecation() {}` in your application.
