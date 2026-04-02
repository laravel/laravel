<?php

namespace Illuminate\Queue\Events;

class JobDebounced
{
    /**
     * Create a new event instance.
     *
     * @param  string  $connectionName  The connection name.
     * @param  \Illuminate\Contracts\Queue\Job  $job  The queue job instance.
     * @param  mixed  $command  The deserialized command object.
     */
    public function __construct(
        public $connectionName,
        public $job,
        public $command,
    ) {
    }
}
