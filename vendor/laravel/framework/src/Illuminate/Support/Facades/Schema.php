<?php

namespace Illuminate\Support\Facades;

/**
 * @method static void defaultStringLength(int $length)
 * @method static void defaultTimePrecision(int|null $precision)
 * @method static void defaultMorphKeyType(string $type)
 * @method static void morphUsingUuids()
 * @method static void morphUsingUlids()
 * @method static bool createDatabase(string $name)
 * @method static bool dropDatabaseIfExists(string $name)
 * @method static array getSchemas()
 * @method static bool hasTable(string $table)
 * @method static bool hasView(string $view)
 * @method static array getTables(string|string[]|null $schema = null)
 * @method static array getTableListing(string|string[]|null $schema = null, bool $schemaQualified = true)
 * @method static array getViews(string|string[]|null $schema = null)
 * @method static array getTypes(string|string[]|null $schema = null)
 * @method static bool hasColumn(string $table, string $column)
 * @method static bool hasColumns(string $table, array $columns)
 * @method static void whenTableHasColumn(string $table, string $column, \Closure $callback)
 * @method static void whenTableDoesntHaveColumn(string $table, string $column, \Closure $callback)
 * @method static void whenTableHasIndex(string $table, string|array $index, \Closure $callback, string|null $type = null)
 * @method static void whenTableDoesntHaveIndex(string $table, string|array $index, \Closure $callback, string|null $type = null)
 * @method static string getColumnType(string $table, string $column, bool $fullDefinition = false)
 * @method static array getColumnListing(string $table)
 * @method static array getColumns(string $table)
 * @method static array getIndexes(string $table)
 * @method static array getIndexListing(string $table)
 * @method static bool hasIndex(string $table, string|array $index, string|null $type = null)
 * @method static array getForeignKeys(string $table)
 * @method static void table(string $table, \Closure $callback)
 * @method static void create(string $table, \Closure $callback)
 * @method static void drop(string $table)
 * @method static void dropIfExists(string $table)
 * @method static void dropColumns(string $table, string|array $columns)
 * @method static void dropAllTables()
 * @method static void dropAllViews()
 * @method static void dropAllTypes()
 * @method static void rename(string $from, string $to)
 * @method static bool enableForeignKeyConstraints()
 * @method static bool disableForeignKeyConstraints()
 * @method static mixed withoutForeignKeyConstraints(\Closure $callback)
 * @method static void ensureVectorExtensionExists(string|null $schema = null)
 * @method static void ensureExtensionExists(string $name, string|null $schema = null)
 * @method static string[]|null getCurrentSchemaListing()
 * @method static string|null getCurrentSchemaName()
 * @method static array parseSchemaAndTable(string $reference, string|bool|null $withDefaultSchema = null)
 * @method static \Illuminate\Database\Connection getConnection()
 * @method static void blueprintResolver(\Closure $resolver)
 * @method static void macro(string $name, object|callable $macro)
 * @method static void mixin(object $mixin, bool $replace = true)
 * @method static bool hasMacro(string $name)
 * @method static void flushMacros()
 *
 * @see \Illuminate\Database\Schema\Builder
 */
class Schema extends Facade
{
    /**
     * Indicates if the resolved facade should be cached.
     *
     * @var bool
     */
    protected static $cached = false;

    /**
     * Get a schema builder instance for a connection.
     *
     * @param  string|null  $name
     * @return \Illuminate\Database\Schema\Builder
     */
    public static function connection($name)
    {
        return static::$app['db']->connection($name)->getSchemaBuilder();
    }

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'db.schema';
    }
}
