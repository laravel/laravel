<?php

namespace Illuminate\Testing\Constraints;

use Illuminate\Database\Connection;
use PHPUnit\Framework\Constraint\Constraint;
use ReflectionClass;

class CountInDatabase extends Constraint
{
    /**
     * The database connection.
     *
     * @var \Illuminate\Database\Connection
     */
    protected $database;

    /**
     * The expected table entries count that will be checked against the actual count.
     *
     * @var int
     */
    protected $expectedCount;

    /**
     * The actual table entries count that will be checked against the expected count.
     *
     * @var int
     */
    protected $actualCount;

    /**
     * Create a new constraint instance.
     *
     * @param  \Illuminate\Database\Connection  $database
     * @param  int  $expectedCount
     */
    public function __construct(Connection $database, int $expectedCount)
    {
        $this->expectedCount = $expectedCount;

        $this->database = $database;
    }

    /**
     * Check if the expected and actual count are equal.
     *
     * @param  string  $table
     * @return bool
     */
    public function matches($table): bool
    {
        $this->actualCount = $this->database->table($table)->count();

        return $this->actualCount === $this->expectedCount;
    }

    /**
     * Get the description of the failure.
     *
     * @param  string  $table
     * @return string
     */
    public function failureDescription($table): string
    {
        return sprintf(
            "table [%s] matches expected entries count of %s. Entries found: %s.\n",
            $table, $this->expectedCount, $this->actualCount
        );
    }

    /**
     * Get a string representation of the object.
     *
     * @param  int  $options
     * @return string
     */
    public function toString($options = 0): string
    {
        return (new ReflectionClass($this))->name;
    }
}
