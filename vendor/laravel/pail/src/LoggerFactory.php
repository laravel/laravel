<?php

namespace Laravel\Pail;

use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

class LoggerFactory
{
    /**
     * Creates a new instance of the logger factory.
     */
    public function __construct(
        protected File $file,
    ) {
        //
    }

    /**
     * Creates a new instance of the logger.
     */
    public function create(): LoggerInterface
    {
        $handler = new StreamHandler($this->file->__toString(), Level::Debug, true, null, true);
        $handler->setFormatter(new JsonFormatter);

        return new Logger('pail', [$handler]);
    }
}
