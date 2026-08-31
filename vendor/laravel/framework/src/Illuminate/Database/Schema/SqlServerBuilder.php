<?php

namespace Illuminate\Database\Schema;

use Illuminate\Support\Arr;

class SqlServerBuilder extends Builder
{
    /**
     * Drop all tables from the database.
     *
     * @return void
     */
    public function dropAllTables()
    {
        $this->connection->statement($this->grammar->compileDropAllForeignKeys());

        $this->connection->statement($this->grammar->compileDropAllTables());
    }

    /**
     * Drop all views from the database.
     *
     * @return void
     */
    public function dropAllViews()
    {
        $this->connection->statement($this->grammar->compileDropAllViews());
    }

    /**
     * Get the default schema name for the connection.
     *
     * @return string|null
     */
    public function getCurrentSchemaName()
    {
        return Arr::first($this->getSchemas(), fn ($schema) => $schema['default'])['name'];
    }
}
