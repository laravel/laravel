<?php

namespace Illuminate\Database\Events;

use Illuminate\Contracts\Database\Events\MigrationEvent;

class NoPendingMigrations implements MigrationEvent
{
    /**
     * Create a new event instance.
     *
     * @param  string  $method  The migration method that was called.
     */
    public function __construct(
        public $method,
    ) {
    }
}
