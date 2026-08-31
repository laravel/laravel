<?php

namespace Illuminate\Database\Schema;

class MySqlBuilder extends Builder
{
    /**
     * Drop all tables from the database.
     *
     * @return void
     */
    public function dropAllTables()
    {
        $tables = $this->getTableListing($this->getCurrentSchemaListing());

        if (empty($tables)) {
            return;
        }

        $this->disableForeignKeyConstraints();

        try {
            $this->connection->statement(
                $this->grammar->compileDropAllTables($tables)
            );
        } finally {
            $this->enableForeignKeyConstraints();
        }
    }

    /**
     * Drop all views from the database.
     *
     * @return void
     */
    public function dropAllViews()
    {
        $views = array_column($this->getViews($this->getCurrentSchemaListing()), 'schema_qualified_name');

        if (empty($views)) {
            return;
        }

        $this->connection->statement(
            $this->grammar->compileDropAllViews($views)
        );
    }

    /**
     * Get the names of current schemas for the connection.
     *
     * @return string[]|null
     */
    public function getCurrentSchemaListing()
    {
        return [$this->connection->getDatabaseName()];
    }
}
