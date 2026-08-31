<?php

namespace Illuminate\Console\Events;

use Illuminate\Console\Scheduling\Event;

class ScheduledTaskStarting
{
    /**
     * Create a new event instance.
     *
     * @param  \Illuminate\Console\Scheduling\Event  $task  The scheduled event being run.
     */
    public function __construct(
        public Event $task,
    ) {
    }
}
