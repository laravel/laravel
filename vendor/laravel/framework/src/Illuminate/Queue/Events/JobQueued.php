<?php

namespace Illuminate\Queue\Events;

class JobQueued
{
    /**
     * Create a new event instance.
     *
     * @param  string  $connectionName  The connection name.
     * @param  string|null  $queue  The queue name.
     * @param  string|int|null  $id  The job ID.
     * @param  \Closure|string|object  $job  The job instance.
     * @param  string  $payload  The job payload.
     * @param  int|null  $delay  The amount of time the job was delayed.
     */
    public function __construct(
        public $connectionName,
        public $queue,
        public $id,
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
