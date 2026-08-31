<?php

namespace Illuminate\Database\Query\Processors;

use Illuminate\Database\Query\Builder;

class Processor
{
    /**
     * Process the results of a "select" query.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $results
     * @return array
     */
    public function processSelect(Builder $query, $results)
    {
        return $results;
    }

    /**
     * Process an  "insert get ID" query.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  string  $sql
     * @param  array  $values
     * @param  string|null  $sequence
     * @return int
     */
    public function processInsertGetId(Builder $query, $sql, $values, $sequence = null)
    {
        $query->getConnection()->insert($sql, $values);

        $id = $query->getConnection()->getPdo()->lastInsertId($sequence);

        return is_numeric($id) ? (int) $id : $id;
    }

    /**
     * Process the results of a schemas query.
     *
     * @param  list<array<string, mixed>>  $results
     * @return list<array{name: string, path: string|null, default: bool}>
     */
    public function processSchemas($results)
    {
        return array_map(function ($result) {
            $result = (object) $result;

            return [
                'name' => $result->name,
                'path' => $result->path ?? null, // SQLite Only...
                'default' => (bool) $result->default,
            ];
        }, $results);
    }

    /**
     * Process the results of a tables query.
     *
     * @param  list<array<string, mixed>>  $results
     * @return list<array{name: string, schema: string|null, schema_qualified_name: string, size: int|null, comment: string|null, collation: string|null, engine: string|null}>
     */
    public function processTables($results)
    {
        return array_map(function ($result) {
            $result = (object) $result;

            return [
                'name' => $result->name,
                'schema' => $result->schema ?? null,
                'schema_qualified_name' => isset($result->schema) ? $result->schema.'.'.$result->name : $result->name,
                'size' => isset($result->size) ? (int) $result->size : null,
                'comment' => $result->comment ?? null, // MySQL and PostgreSQL
                'collation' => $result->collation ?? null, // MySQL only
                'engine' => $result->engine ?? null, // MySQL only
            ];
        }, $results);
    }

    /**
     * Process the results of a views query.
     *
     * @param  list<array<string, mixed>>  $results
     * @return list<array{name: string, schema: string, schema_qualified_name: string, definition: string}>
     */
    public function processViews($results)
    {
        return array_map(function ($result) {
            $result = (object) $result;

            return [
                'name' => $result->name,
                'schema' => $result->schema ?? null,
                'schema_qualified_name' => isset($result->schema) ? $result->schema.'.'.$result->name : $result->name,
                'definition' => $result->definition,
            ];
        }, $results);
    }

    /**
     * Process the results of a types query.
     *
     * @param  list<array<string, mixed>>  $results
     * @return list<array{name: string, schema: string, type: string, type: string, category: string, implicit: bool}>
     */
    public function processTypes($results)
    {
        return $results;
    }

    /**
     * Process the results of a columns query.
     *
     * @param  list<array<string, mixed>>  $results
     * @return list<array{name: string, type: string, type_name: string, nullable: bool, default: mixed, auto_increment: bool, comment: string|null, generation: array{type: string, expression: string|null}|null}>
     */
    public function processColumns($results)
    {
        return $results;
    }

    /**
     * Process the results of an indexes query.
     *
     * @param  list<array<string, mixed>>  $results
     * @return list<array{name: string, columns: list<string>, type: string, unique: bool, primary: bool}>
     */
    public function processIndexes($results)
    {
        return $results;
    }

    /**
     * Process the results of a foreign keys query.
     *
     * @param  list<array<string, mixed>>  $results
     * @return list<array{name: string, columns: list<string>, foreign_schema: string, foreign_table: string, foreign_columns: list<string>, on_update: string, on_delete: string}>
     */
    public function processForeignKeys($results)
    {
        return $results;
    }
}
