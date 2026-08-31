<?php

namespace Illuminate\Database\Migrations;

abstract class Migration
{
    /**
     * The name of the database connection to use.
     *
     * @var string|null
     */
    protected $connection;

    /**
     * Enables, if supported, wrapping the migration within a transaction.
     *
     * @var bool
     */
    public $withinTransaction = true;

    /**
     * Get the migration connection name.
     *
     * @return string|null
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Determine if this migration should run.
     *
     * @return bool
     */
    public function shouldRun(): bool
    {
        return true;
    }
}
