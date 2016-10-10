Sockets Handler
===============

This handler allows you to write your logs to sockets using [fsockopen](http://php.net/fsockopen)
or [pfsockopen](http://php.net/pfsockopen).

Persistent sockets are mainly useful in web environments where you gain some performance not closing/opening
the connections between requests.

You can use a `unix://` prefix to access unix sockets and `udp://` to open UDP sockets instead of the default TCP.

Basic Example
-------------

```php
<?php

use Monolog\Logger;
use Monolog\Handler\SocketHandler;

// Create the logger
$logger = new Logger('my_logger');

// Create the handler
$handler = new SocketHandler('unix:///var/log/httpd_app_log.socket');
$handler->setPersistent(true);

// Now add the handler
$logger->pushHandler($handler, Logger::DEBUG);

// You can now use your logger
$logger->addInfo('My logger is now ready');

```

In this example, using syslog-ng, you should see the log on the log server:

    cweb1 [2012-02-26 00:12:03] my_logger.INFO: My logger is now ready [] []

