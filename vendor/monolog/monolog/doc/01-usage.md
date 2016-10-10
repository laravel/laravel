# Using Monolog

- [Installation](#installation)
- [Core Concepts](#core-concepts)
- [Log Levels](#log-levels)
- [Configuring a logger](#configuring-a-logger)
- [Adding extra data in the records](#adding-extra-data-in-the-records)
- [Leveraging channels](#leveraging-channels)
- [Customizing the log format](#customizing-the-log-format)

## Installation

Monolog is available on Packagist ([monolog/monolog](http://packagist.org/packages/monolog/monolog))
and as such installable via [Composer](http://getcomposer.org/).

```bash
composer require monolog/monolog
```

If you do not use Composer, you can grab the code from GitHub, and use any
PSR-0 compatible autoloader (e.g. the [Symfony2 ClassLoader component](https://github.com/symfony/ClassLoader))
to load Monolog classes.

## Core Concepts

Every `Logger` instance has a channel (name) and a stack of handlers. Whenever
you add a record to the logger, it traverses the handler stack. Each handler
decides whether it fully handled the record, and if so, the propagation of the
record ends there.

This allows for flexible logging setups, for example having a `StreamHandler` at
the bottom of the stack that will log anything to disk, and on top of that add
a `MailHandler` that will send emails only when an error message is logged.
Handlers also have a `$bubble` property which defines whether they block the
record or not if they handled it. In this example, setting the `MailHandler`'s
`$bubble` argument to false means that records handled by the `MailHandler` will
not propagate to the `StreamHandler` anymore.

You can create many `Logger`s, each defining a channel (e.g.: db, request,
router, ..) and each of them combining various handlers, which can be shared
or not. The channel is reflected in the logs and allows you to easily see or
filter records.

Each Handler also has a Formatter, a default one with settings that make sense
will be created if you don't set one. The formatters normalize and format
incoming records so that they can be used by the handlers to output useful
information.

Custom severity levels are not available. Only the eight
[RFC 5424](http://tools.ietf.org/html/rfc5424) levels (debug, info, notice,
warning, error, critical, alert, emergency) are present for basic filtering
purposes, but for sorting and other use cases that would require
flexibility, you should add Processors to the Logger that can add extra
information (tags, user ip, ..) to the records before they are handled.

## Log Levels

Monolog supports the logging levels described by [RFC 5424](http://tools.ietf.org/html/rfc5424).

- **DEBUG** (100): Detailed debug information.

- **INFO** (200): Interesting events. Examples: User logs in, SQL logs.

- **NOTICE** (250): Normal but significant events.

- **WARNING** (300): Exceptional occurrences that are not errors. Examples:
  Use of deprecated APIs, poor use of an API, undesirable things that are not
  necessarily wrong.

- **ERROR** (400): Runtime errors that do not require immediate action but
  should typically be logged and monitored.

- **CRITICAL** (500): Critical conditions. Example: Application component
  unavailable, unexpected exception.

- **ALERT** (550): Action must be taken immediately. Example: Entire website
  down, database unavailable, etc. This should trigger the SMS alerts and wake
  you up.

- **EMERGENCY** (600): Emergency: system is unusable.

## Configuring a logger

Here is a basic setup to log to a file and to firephp on the DEBUG level:

```php
<?php

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;

// Create the logger
$logger = new Logger('my_logger');
// Now add some handlers
$logger->pushHandler(new StreamHandler(__DIR__.'/my_app.log', Logger::DEBUG));
$logger->pushHandler(new FirePHPHandler());

// You can now use your logger
$logger->addInfo('My logger is now ready');
```

Let's explain it. The first step is to create the logger instance which will
be used in your code. The argument is a channel name, which is useful when
you use several loggers (see below for more details about it).

The logger itself does not know how to handle a record. It delegates it to
some handlers. The code above registers two handlers in the stack to allow
handling records in two different ways.

Note that the FirePHPHandler is called first as it is added on top of the
stack. This allows you to temporarily add a logger with bubbling disabled if
you want to override other configured loggers.

> If you use Monolog standalone and are looking for an easy way to
> configure many handlers, the [theorchard/monolog-cascade](https://github.com/theorchard/monolog-cascade)
> can help you build complex logging configs via PHP arrays, yaml or json configs.

## Adding extra data in the records

Monolog provides two different ways to add extra informations along the simple
textual message.

### Using the logging context

The first way is the context, allowing to pass an array of data along the
record:

```php
<?php

$logger->addInfo('Adding a new user', array('username' => 'Seldaek'));
```

Simple handlers (like the StreamHandler for instance) will simply format
the array to a string but richer handlers can take advantage of the context
(FirePHP is able to display arrays in pretty way for instance).

### Using processors

The second way is to add extra data for all records by using a processor.
Processors can be any callable. They will get the record as parameter and
must return it after having eventually changed the `extra` part of it. Let's
write a processor adding some dummy data in the record:

```php
<?php

$logger->pushProcessor(function ($record) {
    $record['extra']['dummy'] = 'Hello world!';

    return $record;
});
```

Monolog provides some built-in processors that can be used in your project.
Look at the [dedicated chapter](https://github.com/Seldaek/monolog/blob/master/doc/02-handlers-formatters-processors.md#processors) for the list.

> Tip: processors can also be registered on a specific handler instead of
  the logger to apply only for this handler.

## Leveraging channels

Channels are a great way to identify to which part of the application a record
is related. This is useful in big applications (and is leveraged by
MonologBundle in Symfony2).

Picture two loggers sharing a handler that writes to a single log file.
Channels would allow you to identify the logger that issued every record.
You can easily grep through the log files filtering this or that channel.

```php
<?php

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;

// Create some handlers
$stream = new StreamHandler(__DIR__.'/my_app.log', Logger::DEBUG);
$firephp = new FirePHPHandler();

// Create the main logger of the app
$logger = new Logger('my_logger');
$logger->pushHandler($stream);
$logger->pushHandler($firephp);

// Create a logger for the security-related stuff with a different channel
$securityLogger = new Logger('security');
$securityLogger->pushHandler($stream);
$securityLogger->pushHandler($firephp);

// Or clone the first one to only change the channel
$securityLogger = $logger->withName('security');
```

## Customizing the log format

In Monolog it's easy to customize the format of the logs written into files,
sockets, mails, databases and other handlers. Most of the handlers use the

```php
$record['formatted']
```

value to be automatically put into the log device. This value depends on the
formatter settings. You can choose between predefined formatter classes or
write your own (e.g. a multiline text file for human-readable output).

To configure a predefined formatter class, just set it as the handler's field:

```php
// the default date format is "Y-m-d H:i:s"
$dateFormat = "Y n j, g:i a";
// the default output format is "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n"
$output = "%datetime% > %level_name% > %message% %context% %extra%\n";
// finally, create a formatter
$formatter = new LineFormatter($output, $dateFormat);

// Create a handler
$stream = new StreamHandler(__DIR__.'/my_app.log', Logger::DEBUG);
$stream->setFormatter($formatter);
// bind it to a logger object
$securityLogger = new Logger('security');
$securityLogger->pushHandler($stream);
```

You may also reuse the same formatter between multiple handlers and share those
handlers between multiple loggers.

[Handlers, Formatters and Processors](02-handlers-formatters-processors.md) &rarr;
