<?php

namespace Illuminate\Database\Events;

class SchemaDumped
{
    /**
     * The database connection instance.
     *
     * @var \Illuminate\Database\Connection
     */
    public $connection;

    /**
     * The database connection name.
     *
     * @var string
     */
    public $connectionName;

    /**
     * The path to the schema dump.
     *
     * @var string
     */
    public $path;

    /**
     * Create a new event instance.
     *
     * @param  \Illuminate\Database\Connection  $connection
     * @param  string  $path
     */
    public function __construct($connection, $path)
    {
        $this->connection = $connection;
        $this->connectionName = $connection->getName();
        $this->path = $path;
    }
}
