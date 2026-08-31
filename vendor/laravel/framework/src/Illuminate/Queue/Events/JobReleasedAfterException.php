<?php

namespace Illuminate\Queue\Events;

class JobReleasedAfterException
{
    /**
     * Create a new event instance.
     *
     * @param  string  $connectionName  The connection name.
     * @param  \Illuminate\Contracts\Queue\Job  $job  The job instance.
     * @param  int|null  $backoff  The backoff delay.
     */
    public function __construct(
        public $connectionName,
        public $job,
        public $backoff = null
    ) {
    }
}
