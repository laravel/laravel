<?php

namespace Illuminate\Database\Events;

abstract class ConnectionEvent
{
    /**
     * The name of the connection.
     *
     * @var string
     */
    public $connectionName;

    /**
     * The database connection instance.
     *
     * @var \Illuminate\Database\Connection
     */
    public $connection;

    /**
     * Create a new event instance.
     *
     * @param  \Illuminate\Database\Connection  $connection
     */
    public function __construct($connection)
    {
        $this->connection = $connection;
        $this->connectionName = $connection->getName();
    }
}
