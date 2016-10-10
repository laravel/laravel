# Handlers, Formatters and Processors

- [Handlers](#handlers)
  - [Log to files and syslog](#log-to-files-and-syslog)
  - [Send alerts and emails](#send-alerts-and-emails)
  - [Log specific servers and networked logging](#log-specific-servers-and-networked-logging)
  - [Logging in development](#logging-in-development)
  - [Log to databases](#log-to-databases)
  - [Wrappers / Special Handlers](#wrappers--special-handlers)
- [Formatters](#formatters)
- [Processors](#processors)
- [Third Party Packages](#third-party-packages)

## Handlers

### Log to files and syslog

- _StreamHandler_: Logs records into any PHP stream, use this for log files.
- _RotatingFileHandler_: Logs records to a file and creates one logfile per day.
  It will also delete files older than `$maxFiles`. You should use
  [logrotate](http://linuxcommand.org/man_pages/logrotate8.html) for high profile
  setups though, this is just meant as a quick and dirty solution.
- _SyslogHandler_: Logs records to the syslog.
- _ErrorLogHandler_: Logs records to PHP's
  [`error_log()`](http://docs.php.net/manual/en/function.error-log.php) function.

### Send alerts and emails

- _NativeMailerHandler_: Sends emails using PHP's
  [`mail()`](http://php.net/manual/en/function.mail.php) function.
- _SwiftMailerHandler_: Sends emails using a [`Swift_Mailer`](http://swiftmailer.org/) instance.
- _PushoverHandler_: Sends mobile notifications via the [Pushover](https://www.pushover.net/) API.
- _HipChatHandler_: Logs records to a [HipChat](http://hipchat.com) chat room using its API.
- _FlowdockHandler_: Logs records to a [Flowdock](https://www.flowdock.com/) account.
- _SlackHandler_: Logs records to a [Slack](https://www.slack.com/) account.
- _MandrillHandler_: Sends emails via the Mandrill API using a [`Swift_Message`](http://swiftmailer.org/) instance.
- _FleepHookHandler_: Logs records to a [Fleep](https://fleep.io/) conversation using Webhooks.
- _IFTTTHandler_: Notifies an [IFTTT](https://ifttt.com/maker) trigger with the log channel, level name and message.

### Log specific servers and networked logging

- _SocketHandler_: Logs records to [sockets](http://php.net/fsockopen), use this
  for UNIX and TCP sockets. See an [example](sockets.md).
- _AmqpHandler_: Logs records to an [amqp](http://www.amqp.org/) compatible
  server. Requires the [php-amqp](http://pecl.php.net/package/amqp) extension (1.0+).
- _GelfHandler_: Logs records to a [Graylog2](http://www.graylog2.org) server.
- _CubeHandler_: Logs records to a [Cube](http://square.github.com/cube/) server.
- _RavenHandler_: Logs records to a [Sentry](http://getsentry.com/) server using
  [raven](https://packagist.org/packages/raven/raven).
- _ZendMonitorHandler_: Logs records to the Zend Monitor present in Zend Server.
- _NewRelicHandler_: Logs records to a [NewRelic](http://newrelic.com/) application.
- _LogglyHandler_: Logs records to a [Loggly](http://www.loggly.com/) account.
- _RollbarHandler_: Logs records to a [Rollbar](https://rollbar.com/) account.
- _SyslogUdpHandler_: Logs records to a remote [Syslogd](http://www.rsyslog.com/) server.
- _LogEntriesHandler_: Logs records to a [LogEntries](http://logentries.com/) account.

### Logging in development

- _FirePHPHandler_: Handler for [FirePHP](http://www.firephp.org/), providing
  inline `console` messages within [FireBug](http://getfirebug.com/).
- _ChromePHPHandler_: Handler for [ChromePHP](http://www.chromephp.com/), providing
  inline `console` messages within Chrome.
- _BrowserConsoleHandler_: Handler to send logs to browser's Javascript `console` with
  no browser extension required. Most browsers supporting `console` API are supported.
- _PHPConsoleHandler_: Handler for [PHP Console](https://chrome.google.com/webstore/detail/php-console/nfhmhhlpfleoednkpnnnkolmclajemef), providing
  inline `console` and notification popup messages within Chrome.

### Log to databases

- _RedisHandler_: Logs records to a [redis](http://redis.io) server.
- _MongoDBHandler_: Handler to write records in MongoDB via a
  [Mongo](http://pecl.php.net/package/mongo) extension connection.
- _CouchDBHandler_: Logs records to a CouchDB server.
- _DoctrineCouchDBHandler_: Logs records to a CouchDB server via the Doctrine CouchDB ODM.
- _ElasticSearchHandler_: Logs records to an Elastic Search server.
- _DynamoDbHandler_: Logs records to a DynamoDB table with the [AWS SDK](https://github.com/aws/aws-sdk-php).

### Wrappers / Special Handlers

- _FingersCrossedHandler_: A very interesting wrapper. It takes a logger as
  parameter and will accumulate log records of all levels until a record
  exceeds the defined severity level. At which point it delivers all records,
  including those of lower severity, to the handler it wraps. This means that
  until an error actually happens you will not see anything in your logs, but
  when it happens you will have the full information, including debug and info
  records. This provides you with all the information you need, but only when
  you need it.
- _WhatFailureGroupHandler_: This handler extends the _GroupHandler_ ignoring
   exceptions raised by each child handler. This allows you to ignore issues
   where a remote tcp connection may have died but you do not want your entire
   application to crash and may wish to continue to log to other handlers.
- _BufferHandler_: This handler will buffer all the log records it receives
  until `close()` is called at which point it will call `handleBatch()` on the
  handler it wraps with all the log messages at once. This is very useful to
  send an email with all records at once for example instead of having one mail
  for every log record.
- _GroupHandler_: This handler groups other handlers. Every record received is
  sent to all the handlers it is configured with.
- _FilterHandler_: This handler only lets records of the given levels through
   to the wrapped handler.
- _SamplingHandler_: Wraps around another handler and lets you sample records
   if you only want to store some of them.
- _NullHandler_: Any record it can handle will be thrown away. This can be used
  to put on top of an existing handler stack to disable it temporarily.
- _PsrHandler_: Can be used to forward log records to an existing PSR-3 logger
- _TestHandler_: Used for testing, it records everything that is sent to it and
  has accessors to read out the information.
- _HandlerWrapper_: A simple handler wrapper you can inherit from to create
 your own wrappers easily.

## Formatters

- _LineFormatter_: Formats a log record into a one-line string.
- _HtmlFormatter_: Used to format log records into a human readable html table, mainly suitable for emails.
- _NormalizerFormatter_: Normalizes objects/resources down to strings so a record can easily be serialized/encoded.
- _ScalarFormatter_: Used to format log records into an associative array of scalar values.
- _JsonFormatter_: Encodes a log record into json.
- _WildfireFormatter_: Used to format log records into the Wildfire/FirePHP protocol, only useful for the FirePHPHandler.
- _ChromePHPFormatter_: Used to format log records into the ChromePHP format, only useful for the ChromePHPHandler.
- _GelfMessageFormatter_: Used to format log records into Gelf message instances, only useful for the GelfHandler.
- _LogstashFormatter_: Used to format log records into [logstash](http://logstash.net/) event json, useful for any handler listed under inputs [here](http://logstash.net/docs/latest).
- _ElasticaFormatter_: Used to format log records into an Elastica\Document object, only useful for the ElasticSearchHandler.
- _LogglyFormatter_: Used to format log records into Loggly messages, only useful for the LogglyHandler.
- _FlowdockFormatter_: Used to format log records into Flowdock messages, only useful for the FlowdockHandler.
- _MongoDBFormatter_: Converts \DateTime instances to \MongoDate and objects recursively to arrays, only useful with the MongoDBHandler.

## Processors

- _PsrLogMessageProcessor_: Processes a log record's message according to PSR-3 rules, replacing `{foo}` with the value from `$context['foo']`.
- _IntrospectionProcessor_: Adds the line/file/class/method from which the log call originated.
- _WebProcessor_: Adds the current request URI, request method and client IP to a log record.
- _MemoryUsageProcessor_: Adds the current memory usage to a log record.
- _MemoryPeakUsageProcessor_: Adds the peak memory usage to a log record.
- _ProcessIdProcessor_: Adds the process id to a log record.
- _UidProcessor_: Adds a unique identifier to a log record.
- _GitProcessor_: Adds the current git branch and commit to a log record.
- _TagProcessor_: Adds an array of predefined tags to a log record.

## Third Party Packages

Third party handlers, formatters and processors are
[listed in the wiki](https://github.com/Seldaek/monolog/wiki/Third-Party-Packages). You
can also add your own there if you publish one.

&larr; [Usage](01-usage.md) |  [Utility classes](03-utilities.md) &rarr;
