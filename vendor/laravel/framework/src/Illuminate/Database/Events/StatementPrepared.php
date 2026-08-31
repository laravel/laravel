<?php

namespace Illuminate\Database\Events;

class StatementPrepared
{
    /**
     * Create a new event instance.
     *
     * @param  \Illuminate\Database\Connection  $connection  The database connection instance.
     * @param  \PDOStatement  $statement  The PDO statement.
     */
    public function __construct(
        public $connection,
        public $statement,
    ) {
    }
}
