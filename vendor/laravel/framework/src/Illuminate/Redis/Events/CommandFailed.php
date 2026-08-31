<?php

namespace Illuminate\Redis\Events;

use Throwable;

class CommandFailed
{
    /**
     * The Redis command that failed.
     *
     * @var string
     */
    public $command;

    /**
     * The array of command parameters.
     *
     * @var array
     */
    public $parameters;

    /**
     * The exception that was thrown.
     *
     * @var \Throwable
     */
    public $exception;

    /**
     * The Redis connection instance.
     *
     * @var \Illuminate\Redis\Connections\Connection
     */
    public $connection;

    /**
     * The Redis connection name.
     *
     * @var string
     */
    public $connectionName;

    /**
     * Create a new event instance.
     *
     * @param  string  $command
     * @param  array  $parameters
     * @param  \Throwable  $exception
     * @param  \Illuminate\Redis\Connections\Connection  $connection
     */
    public function __construct($command, $parameters, Throwable $exception, $connection)
    {
        $this->command = $command;
        $this->parameters = $parameters;
        $this->exception = $exception;
        $this->connection = $connection;
        $this->connectionName = $connection->getName();
    }
}
