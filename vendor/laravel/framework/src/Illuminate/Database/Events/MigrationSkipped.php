<?php

namespace Illuminate\Database\Events;

use Illuminate\Contracts\Database\Events\MigrationEvent;

class MigrationSkipped implements MigrationEvent
{
    /**
     * Create a new event instance.
     *
     * @param  string  $migrationName  The name of the migration that was skipped.
     */
    public function __construct(
        public $migrationName,
    ) {
    }
}
