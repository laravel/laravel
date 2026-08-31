<?php

namespace Illuminate\Cache\Events;

use Throwable;

class CacheFailedOver
{
    /**
     * Create a new event instance.
     *
     * @param  string|null  $storeName  The name of the cache store that failed.
     * @param  \Throwable  $exception  The exception that was thrown.
     */
    public function __construct(
        public ?string $storeName,
        public Throwable $exception,
    ) {
    }
}
