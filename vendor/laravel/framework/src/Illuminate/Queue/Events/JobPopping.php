<?php

namespace Illuminate\Queue\Events;

class JobPopping
{
    /**
     * Create a new event instance.
     *
     * @param  string  $connectionName  The connection name.
     * @param  string|null  $queue  The queue name.
     */
    public function __construct(
        public $connectionName,
        public $queue = null
    ) {
    }
}
