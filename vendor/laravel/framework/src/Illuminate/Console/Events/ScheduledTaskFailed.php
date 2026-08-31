<?php

namespace Illuminate\Console\Events;

use Illuminate\Console\Scheduling\Event;
use Throwable;

class ScheduledTaskFailed
{
    /**
     * Create a new event instance.
     *
     * @param  \Illuminate\Console\Scheduling\Event  $task  The scheduled event that failed.
     * @param  \Throwable  $exception  The exception that was thrown.
     */
    public function __construct(
        public Event $task,
        public Throwable $exception,
    ) {
    }
}
