<?php

namespace Illuminate\Testing\Concerns;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\ParallelTesting;
use Illuminate\Support\Facades\Schema;

trait TestDatabases
{
    /**
     * Indicates if the test database schema is up to date.
     *
     * @var bool
     */
    protected static $schemaIsUpToDate = false;

    /**
     * The root database name prior to concatenating the token.
     *
     * @var null|string
     */
    protected static $originalDatabaseName = null;

    /**
     * Boot a test database.
     *
     * @return void
     */
    protected function bootTestDatabase()
    {
        ParallelTesting::setUpProcess(function () {
            $this->whenNotUsingInMemoryDatabase(function ($database) {
                if (ParallelTesting::option('recreate_databases')) {
                    Schema::dropDatabaseIfExists(
                        $this->testDatabase($database)
                    );
                }
            });
        });

        ParallelTesting::setUpTestCase(function ($testCase) {
            $uses = array_flip(class_uses_recursive(get_class($testCase)));

            $databaseTraits = [
                Testing\DatabaseMigrations::class,
                Testing\DatabaseTransactions::class,
                Testing\DatabaseTruncation::class,
                Testing\RefreshDatabase::class,
            ];

            if (Arr::hasAny($uses, $databaseTraits) && ! ParallelTesting::option('without_databases')) {
                $this->whenNotUsingInMemoryDatabase(function ($database) use ($uses) {
                    [$testDatabase, $created] = $this->ensureTestDatabaseExists($database);

                    $this->switchToDatabase($testDatabase);

                    if ($created) {
                        ParallelTesting::callSetUpTestDatabaseBeforeMigratingCallbacks($testDatabase);
                    }

                    if (isset($uses[Testing\DatabaseTransactions::class])) {
                        $this->ensureSchemaIsUpToDate();
                    }

                    if ($created) {
                        ParallelTesting::callSetUpTestDatabaseCallbacks($testDatabase);
                    }
                });
            }
        });

        ParallelTesting::tearDownProcess(function () {
            $this->whenNotUsingInMemoryDatabase(function ($database) {
                if (ParallelTesting::option('drop_databases')) {
                    Schema::dropDatabaseIfExists(
                        $this->testDatabase($database)
                    );
                }
            });
        });
    }

    /**
     * Ensure a test database exists and returns its name.
     *
     * @param  string  $database
     * @return array
     */
    protected function ensureTestDatabaseExists($database)
    {
        $testDatabase = $this->testDatabase($database);

        try {
            $this->usingDatabase($testDatabase, function () {
                Schema::hasTable('dummy');
            });
        } catch (QueryException) {
            $this->usingDatabase($database, function () use ($testDatabase) {
                Schema::dropDatabaseIfExists($testDatabase);
                Schema::createDatabase($testDatabase);
            });

            return [$testDatabase, true];
        }

        return [$testDatabase, false];
    }

    /**
     * Ensure the current database test schema is up to date.
     *
     * @return void
     */
    protected function ensureSchemaIsUpToDate()
    {
        if (! static::$schemaIsUpToDate) {
            Artisan::call('migrate');

            static::$schemaIsUpToDate = true;
        }
    }

    /**
     * Runs the given callable using the given database.
     *
     * @param  string  $database
     * @param  callable  $callable
     * @return void
     */
    protected function usingDatabase($database, $callable)
    {
        $original = DB::getConfig('database');

        try {
            $this->switchToDatabase($database);
            $callable();
        } finally {
            $this->switchToDatabase($original);
        }
    }

    /**
     * Apply the given callback when tests are not using in memory database.
     *
     * @param  callable  $callback
     * @return void
     */
    protected function whenNotUsingInMemoryDatabase($callback)
    {
        if (ParallelTesting::option('without_databases')) {
            return;
        }

        $database = DB::getConfig('database');

        if ($database !== ':memory:') {
            $callback($database);
        }
    }

    /**
     * Switch to the given database.
     *
     * @param  string  $database
     * @return void
     */
    protected function switchToDatabase($database)
    {
        DB::purge();

        $default = config('database.default');

        $url = config("database.connections.{$default}.url");

        if ($url) {
            config()->set(
                "database.connections.{$default}.url",
                preg_replace('/^(.*)(\/[\w-]*)(\??.*)$/', "$1/{$database}$3", $url),
            );
        } else {
            config()->set(
                "database.connections.{$default}.database",
                $database,
            );
        }
    }

    /**
     * Returns the test database name.
     *
     * @return string
     */
    protected function testDatabase($database)
    {
        if (! isset(self::$originalDatabaseName)) {
            self::$originalDatabaseName = $database;
        } else {
            $database = self::$originalDatabaseName;
        }

        $token = ParallelTesting::token();

        return "{$database}_test_{$token}";
    }
}
