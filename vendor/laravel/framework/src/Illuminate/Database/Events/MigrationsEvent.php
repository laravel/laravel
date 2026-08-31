<?php

namespace Illuminate\Database\Events;

use Illuminate\Contracts\Database\Events\MigrationEvent as MigrationEventContract;

abstract class MigrationsEvent implements MigrationEventContract
{
    /**
     * Create a new event instance.
     *
     * @param  string  $method  The migration method that was invoked.
     * @param  array<string, mixed>  $options  The options provided when the migration method was invoked.
     */
    public function __construct(
        public $method,
        public array $options = [],
    ) {
    }
}
