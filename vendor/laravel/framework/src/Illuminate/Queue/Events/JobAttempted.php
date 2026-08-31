<?php

namespace Illuminate\Queue\Events;

class JobAttempted
{
    /**
     * Create a new event instance.
     *
     * @param  string  $connectionName  The connection name.
     * @param  \Illuminate\Contracts\Queue\Job  $job  The job instance.
     * @param  bool  $exceptionOccurred  Indicates if an exception occurred while processing the job.
     */
    public function __construct(
        public $connectionName,
        public $job,
        public $exceptionOccurred = false,
    ) {
    }

    /**
     * Determine if the job completed with failing or an unhandled exception occurring.
     *
     * @return bool
     */
    public function successful(): bool
    {
        return ! $this->job->hasFailed() && ! $this->exceptionOccurred;
    }
}
