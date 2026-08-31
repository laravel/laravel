<?php

namespace Illuminate\Foundation\Testing;

class RefreshDatabaseState
{
    /**
     * The current SQLite in-memory database connections.
     *
     * @var array<string, \PDO>
     */
    public static $inMemoryConnections = [];

    /**
     * Indicates if the test database has been migrated.
     *
     * @var bool
     */
    public static $migrated = false;

    /**
     * Indicates if a lazy refresh hook has been invoked.
     *
     * @var bool
     */
    public static $lazilyRefreshed = false;
}
