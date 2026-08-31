<?php

namespace Illuminate\Queue\Events;

class JobQueueing
{
    /**
     * Create a new event instance.
     *
     * @param  string  $connectionName  The connection name.
     * @param  string|null  $queue  The queue name.
     * @param  \Closure|string|object  $job  The job instance.
     * @param  string  $payload  The job payload.
     * @param  int|null  $delay  The number of seconds the job was delayed.
     */
    public function __construct(
        public $connectionName,
        public $queue,
        public $job,
        public $payload,
        public $delay,
    ) {
    }

    /**
     * Get the decoded job payload.
     *
     * @return array
     */
    public function payload()
    {
        return json_decode($this->payload, true, flags: JSON_THROW_ON_ERROR);
    }
}
