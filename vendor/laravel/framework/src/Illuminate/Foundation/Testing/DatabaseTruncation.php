<?php

namespace Illuminate\Foundation\Testing;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Foundation\Testing\Traits\CanConfigureMigrationCommands;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

trait DatabaseTruncation
{
    use CanConfigureMigrationCommands;

    /**
     * The cached names of the database tables for each connection.
     *
     * @var array
     */
    protected static array $allTables;

    /**
     * Truncate the database tables for all configured connections.
     *
     * @return void
     */
    protected function truncateDatabaseTables(): void
    {
        $this->beforeTruncatingDatabase();

        // Migrate and seed the database on first run...
        if (! RefreshDatabaseState::$migrated) {
            $this->artisan('migrate:fresh', $this->migrateFreshUsing());

            $this->app[Kernel::class]->setArtisan(null);

            RefreshDatabaseState::$migrated = true;

            return;
        }

        // Always clear any test data on subsequent runs...
        $this->truncateTablesForAllConnections();

        if ($seeder = $this->seeder()) {
            // Use a specific seeder class...
            $this->artisan('db:seed', ['--class' => $seeder]);
        } elseif ($this->shouldSeed()) {
            // Use the default seeder class...
            $this->artisan('db:seed');
        }

        $this->afterTruncatingDatabase();
    }

    /**
     * Truncate the database tables for all configured connections.
     *
     * @return void
     */
    protected function truncateTablesForAllConnections(): void
    {
        $database = $this->app->make('db');

        (new Collection($this->connectionsToTruncate()))
            ->each(function ($name) use ($database) {
                $connection = $database->connection($name);

                $connection->getSchemaBuilder()->withoutForeignKeyConstraints(
                    fn () => $this->truncateTablesForConnection($connection, $name)
                );
            });
    }

    /**
     * Truncate the database tables for the given database connection.
     *
     * @param  \Illuminate\Database\ConnectionInterface  $connection
     * @param  string|null  $name
     * @return void
     */
    protected function truncateTablesForConnection(ConnectionInterface $connection, ?string $name): void
    {
        $dispatcher = $connection->getEventDispatcher();

        $connection->unsetEventDispatcher();

        (new Collection($this->getAllTablesForConnection($connection, $name)))
            ->when(
                $this->tablesToTruncate($connection, $name),
                function (Collection $tables, array $tablesToTruncate) {
                    return $tables->filter(fn (array $table) => $this->tableExistsIn($table, $tablesToTruncate));
                },
                function (Collection $tables) use ($connection, $name) {
                    $exceptTables = $this->exceptTables($connection, $name);

                    return $tables->reject(fn (array $table) => $this->tableExistsIn($table, $exceptTables));
                }
            )
            ->each(function (array $table) use ($connection) {
                $connection->withoutTablePrefix(function ($connection) use ($table) {
                    $table = $connection->table($table['schema_qualified_name']);

                    if ($table->exists()) {
                        $table->truncate();
                    }
                });
            });

        $connection->setEventDispatcher($dispatcher);
    }

    /**
     * Get all the tables that belong to the connection.
     */
    protected function getAllTablesForConnection(ConnectionInterface $connection, ?string $name): array
    {
        if (isset(static::$allTables[$name])) {
            return static::$allTables[$name];
        }

        $schema = $connection->getSchemaBuilder();

        return static::$allTables[$name] = Arr::from($schema->getTables($schema->getCurrentSchemaListing()));
    }

    /**
     * Determine if a table exists in the given list, with or without its schema.
     */
    protected function tableExistsIn(array $table, array $tables): bool
    {
        return $table['schema']
            ? ! empty(array_intersect([$table['name'], $table['schema_qualified_name']], $tables))
            : in_array($table['name'], $tables);
    }

    /**
     * The database connections that should have their tables truncated.
     *
     * @return array
     */
    protected function connectionsToTruncate(): array
    {
        return property_exists($this, 'connectionsToTruncate')
            ? $this->connectionsToTruncate
            : [null];
    }

    /**
     * Get the tables that should be truncated.
     */
    protected function tablesToTruncate(ConnectionInterface $connection, ?string $connectionName): ?array
    {
        return property_exists($this, 'tablesToTruncate') && is_array($this->tablesToTruncate)
            ? $this->tablesToTruncate[$connectionName] ?? $this->tablesToTruncate
            : null;
    }

    /**
     * Get the tables that should not be truncated.
     */
    protected function exceptTables(ConnectionInterface $connection, ?string $connectionName): array
    {
        $migrations = $this->app['config']->get('database.migrations');

        $migrationsTable = is_array($migrations) ? ($migrations['table'] ?? 'migrations') : $migrations;
        $migrationsTable = $connection->getTablePrefix().$migrationsTable;

        return property_exists($this, 'exceptTables') && is_array($this->exceptTables)
            ? array_merge(
                $this->exceptTables[$connectionName] ?? $this->exceptTables,
                [$migrationsTable],
            )
            : [$migrationsTable];
    }

    /**
     * Perform any work that should take place before the database has started truncating.
     *
     * @return void
     */
    protected function beforeTruncatingDatabase(): void
    {
        //
    }

    /**
     * Perform any work that should take place once the database has finished truncating.
     *
     * @return void
     */
    protected function afterTruncatingDatabase(): void
    {
        //
    }
}
