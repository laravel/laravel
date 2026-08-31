<?php

namespace Illuminate\Database\Migrations;

interface MigrationRepositoryInterface
{
    /**
     * Get the completed migrations.
     *
     * @return string[]
     */
    public function getRan();

    /**
     * Get the list of migrations.
     *
     * @param  int  $steps
     * @return array{id: int, migration: string, batch: int}[]
     */
    public function getMigrations($steps);

    /**
     * Get the list of the migrations by batch.
     *
     * @param  int  $batch
     * @return array{id: int, migration: string, batch: int}[]
     */
    public function getMigrationsByBatch($batch);

    /**
     * Get the last migration batch.
     *
     * @return array{id: int, migration: string, batch: int}[]
     */
    public function getLast();

    /**
     * Get the completed migrations with their batch numbers.
     *
     * @return array<int, string>[]
     */
    public function getMigrationBatches();

    /**
     * Log that a migration was run.
     *
     * @param  string  $file
     * @param  int  $batch
     * @return void
     */
    public function log($file, $batch);

    /**
     * Remove a migration from the log.
     *
     * @param  objectt{id?: int, migration: string, batch?: int}  $migration
     * @return void
     */
    public function delete($migration);

    /**
     * Get the next migration batch number.
     *
     * @return int
     */
    public function getNextBatchNumber();

    /**
     * Create the migration repository data store.
     *
     * @return void
     */
    public function createRepository();

    /**
     * Determine if the migration repository exists.
     *
     * @return bool
     */
    public function repositoryExists();

    /**
     * Delete the migration repository data store.
     *
     * @return void
     */
    public function deleteRepository();

    /**
     * Set the information source to gather data.
     *
     * @param  string  $name
     * @return void
     */
    public function setSource($name);
}
