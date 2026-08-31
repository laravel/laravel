<?php

namespace Illuminate\Queue\Events;

class JobFailed
{
    /**
     * Create a new event instance.
     *
     * @param  string  $connectionName  The connection name.
     * @param  \Illuminate\Contracts\Queue\Job  $job  The job instance.
     * @param  \Throwable  $exception  The exception that caused the job to fail.
     */
    public function __construct(
        public $connectionName,
        public $job,
        public $exception,
    ) {
    }
}
