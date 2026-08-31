<?php

namespace Illuminate\Queue\Events;

use Throwable;

class QueueFailedOver
{
    /**
     * Create a new event instance.
     *
     * @param  string|null  $connectionName  The queue connection that failed.
     * @param  \Closure|string|object  $command  The job instance.
     * @param  \Throwable  $exception  The exception that was thrown.
     */
    public function __construct(
        public ?string $connectionName,
        public mixed $command,
        public Throwable $exception,
    ) {
    }
}
