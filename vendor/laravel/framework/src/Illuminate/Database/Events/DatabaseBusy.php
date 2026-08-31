<?php

namespace Illuminate\Database\Events;

class DatabaseBusy
{
    /**
     * Create a new event instance.
     *
     * @param  string  $connectionName  The database connection name.
     * @param  int  $connections  The number of open connections.
     */
    public function __construct(
        public $connectionName,
        public $connections,
    ) {
    }
}
