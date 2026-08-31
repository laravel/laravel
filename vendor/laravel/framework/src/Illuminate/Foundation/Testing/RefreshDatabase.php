<?php

namespace Illuminate\Foundation\Testing;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\Traits\CanConfigureMigrationCommands;

trait RefreshDatabase
{
    use CanConfigureMigrationCommands;

    /**
     * Define hooks to migrate the database before and after each test.
     *
     * @return void
     */
    public function refreshDatabase()
    {
        $this->beforeRefreshingDatabase();

        if ($this->usingInMemoryDatabases()) {
            $this->restoreInMemoryDatabase();
        }

        $this->refreshTestDatabase();

        $this->afterRefreshingDatabase();
    }

    /**
     * Determine if any of the connections transacting is using in-memory databases.
     *
     * @return bool
     */
    protected function usingInMemoryDatabases()
    {
        foreach ($this->connectionsToTransact() as $name) {
            if ($this->usingInMemoryDatabase($name)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if a given database connection is an in-memory database.
     *
     * @return bool
     */
    protected function usingInMemoryDatabase(?string $name = null)
    {
        if (is_null($name)) {
            $name = config('database.default');
        }

        return config("database.connections.{$name}.database") === ':memory:';
    }

    /**
     * Restore the in-memory database between tests.
     *
     * @return void
     */
    protected function restoreInMemoryDatabase()
    {
        $database = $this->app->make('db');

        foreach ($this->connectionsToTransact() as $name) {
            if (isset(RefreshDatabaseState::$inMemoryConnections[$name])) {
                $database->connection($name)->setPdo(RefreshDatabaseState::$inMemoryConnections[$name]);
            }
        }
    }

    /**
     * Refresh a conventional test database.
     *
     * @return void
     */
    protected function refreshTestDatabase()
    {
        if (! RefreshDatabaseState::$migrated) {
            $this->migrateDatabases();

            $this->app[Kernel::class]->setArtisan(null);

            $this->updateLocalCacheOfInMemoryDatabases();

            RefreshDatabaseState::$migrated = true;
        }

        $this->beginDatabaseTransaction();
    }

    /**
     * Update locally cached in-memory PDO connections after migration.
     *
     * @return void
     */
    protected function updateLocalCacheOfInMemoryDatabases()
    {
        $database = $this->app->make('db');

        foreach ($this->connectionsToTransact() as $name) {
            if ($this->usingInMemoryDatabase($name)) {
                RefreshDatabaseState::$inMemoryConnections[$name] = $database->connection($name)->getPdo();
            }
        }
    }

    /**
     * Migrate the database.
     *
     * @return void
     */
    protected function migrateDatabases()
    {
        $this->artisan('migrate:fresh', $this->migrateFreshUsing());
    }

    /**
     * Begin a database transaction on the testing database.
     *
     * @return void
     */
    public function beginDatabaseTransaction()
    {
        $database = $this->app->make('db');

        $connections = $this->connectionsToTransact();

        $this->app->instance('db.transactions', $transactionsManager = new DatabaseTransactionsManager($connections));

        foreach ($connections as $name) {
            $connection = $database->connection($name);

            $connection->setTransactionManager($transactionsManager);

            if ($this->usingInMemoryDatabase($name)) {
                RefreshDatabaseState::$inMemoryConnections[$name] ??= $connection->getPdo();
            }

            $dispatcher = $connection->getEventDispatcher();

            $connection->unsetEventDispatcher();
            $connection->beginTransaction();
            $connection->setEventDispatcher($dispatcher);
        }

        $this->beforeApplicationDestroyed(function () use ($database) {
            foreach ($this->connectionsToTransact() as $name) {
                $connection = $database->connection($name);
                $dispatcher = $connection->getEventDispatcher();

                $connection->unsetEventDispatcher();

                if ($connection->getPdo() && ! $connection->getPdo()->inTransaction()) {
                    RefreshDatabaseState::$migrated = false;
                }

                $connection->rollBack();
                $connection->setEventDispatcher($dispatcher);
                $connection->disconnect();
            }
        });
    }

    /**
     * The database connections that should have transactions.
     *
     * @return array
     */
    protected function connectionsToTransact()
    {
        return property_exists($this, 'connectionsToTransact')
            ? $this->connectionsToTransact
            : [config('database.default')];
    }

    /**
     * Perform any work that should take place before the database has started refreshing.
     *
     * @return void
     */
    protected function beforeRefreshingDatabase()
    {
        // ...
    }

    /**
     * Perform any work that should take place once the database has finished refreshing.
     *
     * @return void
     */
    protected function afterRefreshingDatabase()
    {
        // ...
    }
}
