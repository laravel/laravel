<?php

namespace Illuminate\Database\Schema;

use Illuminate\Support\Fluent;

/**
 * @method $this after(string $column) Place the column "after" another column (MySQL)
 * @method $this always(bool $value = true) Used as a modifier for generatedAs() (PostgreSQL)
 * @method $this autoIncrement() Set INTEGER columns as auto-increment (primary key)
 * @method $this change() Change the column
 * @method $this charset(string $charset) Specify a character set for the column (MySQL)
 * @method $this collation(string $collation) Specify a collation for the column
 * @method $this comment(string $comment) Add a comment to the column (MySQL/PostgreSQL)
 * @method $this default(mixed $value) Specify a "default" value for the column
 * @method $this first() Place the column "first" in the table (MySQL)
 * @method $this from(int $startingValue) Set the starting value of an auto-incrementing field (MySQL/PostgreSQL)
 * @method $this fulltext(bool|string $indexName = null) Add a fulltext index
 * @method $this generatedAs(string|\Illuminate\Contracts\Database\Query\Expression $expression = null) Create a SQL compliant identity column (PostgreSQL)
 * @method $this instant() Specify that algorithm=instant should be used for the column operation (MySQL)
 * @method $this index(bool|string $indexName = null) Add an index
 * @method $this invisible() Specify that the column should be invisible to "SELECT *" (MySQL)
 * @method $this lock(('none'|'shared'|'default'|'exclusive') $value) Specify the DDL lock mode for the column operation (MySQL)
 * @method $this nullable(bool $value = true) Allow NULL values to be inserted into the column
 * @method $this persisted() Mark the computed generated column as persistent (SQL Server)
 * @method $this primary(bool $value = true) Add a primary index
 * @method $this spatialIndex(bool|string $indexName = null) Add a spatial index
 * @method $this vectorIndex(bool|string $indexName = null) Add a vector index
 * @method $this startingValue(int $startingValue) Set the starting value of an auto-incrementing field (MySQL/PostgreSQL)
 * @method $this storedAs(string|\Illuminate\Contracts\Database\Query\Expression $expression) Create a stored generated column (MySQL/PostgreSQL/SQLite)
 * @method $this type(string $type) Specify a type for the column
 * @method $this unique(bool|string $indexName = null) Add a unique index
 * @method $this unsigned() Set the INTEGER column as UNSIGNED (MySQL)
 * @method $this useCurrent() Set the TIMESTAMP column to use CURRENT_TIMESTAMP as default value
 * @method $this useCurrentOnUpdate() Set the TIMESTAMP column to use CURRENT_TIMESTAMP when updating (MySQL)
 * @method $this virtualAs(string|\Illuminate\Contracts\Database\Query\Expression $expression) Create a virtual generated column (MySQL/PostgreSQL/SQLite)
 */
class ColumnDefinition extends Fluent
{
    //
}
