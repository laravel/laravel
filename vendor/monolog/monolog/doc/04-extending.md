# Extending Monolog

Monolog is fully extensible, allowing you to adapt your logger to your needs.

## Writing your own handler

Monolog provides many built-in handlers. But if the one you need does not
exist, you can write it and use it in your logger. The only requirement is
to implement `Monolog\Handler\HandlerInterface`.

Let's write a PDOHandler to log records to a database. We will extend the
abstract class provided by Monolog to keep things DRY.

```php
<?php

use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;

class PDOHandler extends AbstractProcessingHandler
{
    private $initialized = false;
    private $pdo;
    private $statement;

    public function __construct(PDO $pdo, $level = Logger::DEBUG, $bubble = true)
    {
        $this->pdo = $pdo;
        parent::__construct($level, $bubble);
    }

    protected function write(array $record)
    {
        if (!$this->initialized) {
            $this->initialize();
        }

        $this->statement->execute(array(
            'channel' => $record['channel'],
            'level' => $record['level'],
            'message' => $record['formatted'],
            'time' => $record['datetime']->format('U'),
        ));
    }

    private function initialize()
    {
        $this->pdo->exec(
            'CREATE TABLE IF NOT EXISTS monolog '
            .'(channel VARCHAR(255), level INTEGER, message LONGTEXT, time INTEGER UNSIGNED)'
        );
        $this->statement = $this->pdo->prepare(
            'INSERT INTO monolog (channel, level, message, time) VALUES (:channel, :level, :message, :time)'
        );

        $this->initialized = true;
    }
}
```

You can now use this handler in your logger:

```php
<?php

$logger->pushHandler(new PDOHandler(new PDO('sqlite:logs.sqlite')));

// You can now use your logger
$logger->addInfo('My logger is now ready');
```

The `Monolog\Handler\AbstractProcessingHandler` class provides most of the
logic needed for the handler, including the use of processors and the formatting
of the record (which is why we use ``$record['formatted']`` instead of ``$record['message']``).

&larr; [Utility classes](03-utilities.md)
