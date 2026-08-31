<?php

namespace Illuminate\Queue\Events;

class QueueResumed
{
    /**
     * Create a new event instance.
     *
     * @param  string  $connection  The connection name.
     * @param  string  $queue  The queue name.
     */
    public function __construct(
        public $connection,
        public $queue,
    ) {
    }
}
