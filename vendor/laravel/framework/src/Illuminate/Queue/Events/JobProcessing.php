<?php

namespace Illuminate\Queue\Events;

class JobProcessing
{
    /**
     * Create a new event instance.
     *
     * @param  string  $connectionName  The connection name.
     * @param  \Illuminate\Contracts\Queue\Job  $job  The job instance.
     */
    public function __construct(
        public $connectionName,
        public $job,
    ) {
    }
}
