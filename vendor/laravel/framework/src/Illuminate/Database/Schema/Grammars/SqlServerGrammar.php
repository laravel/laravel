<?php

namespace Illuminate\Database\Schema\Grammars;

use Illuminate\Database\Query\Expression;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Fluent;

class SqlServerGrammar extends Grammar
{
    /**
     * If this Grammar supports schema changes wrapped in a transaction.
     *
     * @var bool
     */
    protected $transactions = true;

    /**
     * The possible column modifiers.
     *
     * @var string[]
     */
    protected $modifiers = ['Collate', 'Nullable', 'Default', 'Persisted', 'Increment'];

    /**
     * The columns available as serials.
     *
     * @var string[]
     */
    protected $serials = ['tinyInteger', 'smallInteger', 'mediumInteger', 'integer', 'bigInteger'];

    /**
     * The commands to be executed outside of create or alter command.
     *
     * @var string[]
     */
    protected $fluentCommands = ['Default'];

    /**
     * Compile the query to determine the schemas.
     *
     * @return string
     */
    public function compileSchemas()
    {
        return 'select name, iif(schema_id = schema_id(), 1, 0) as [default] from sys.schemas '
            ."where name not in ('information_schema', 'sys') and name not like 'db[_]%' order by name";
    }

    /**
     * Compile the query to determine if the given table exists.
     *
     * @param  string|null  $schema
     * @param  string  $table
     * @return string
     */
    public function compileTableExists($schema, $table)
    {
        return sprintf(
            'select (case when object_id(%s, \'U\') is null then 0 else 1 end) as [exists]',
            $this->quoteString($schema ? $schema.'.'.$table : $table)
        );
    }

    /**
     * Compile the query to determine the tables.
     *
     * @param  string|string[]|null  $schema
     * @return string
     */
    public function compileTables($schema)
    {
        return 'select t.name as name, schema_name(t.schema_id) as [schema], sum(u.total_pages) * 8 * 1024 as size '
            .'from sys.tables as t '
            .'join sys.partitions as p on p.object_id = t.object_id '
            .'join sys.allocation_units as u on u.container_id = p.hobt_id '
            ."where t.is_ms_shipped = 0 and t.name <> 'sysdiagrams'"
            .$this->compileSchemaWhereClause($schema, 'schema_name(t.schema_id)')
            .' group by t.name, t.schema_id '
            .'order by [schema], t.name';
    }

    /**
     * Compile the query to determine the views.
     *
     * @param  string|string[]|null  $schema
     * @return string
     */
    public function compileViews($schema)
    {
        return 'select name, schema_name(v.schema_id) as [schema], definition from sys.views as v '
            .'inner join sys.sql_modules as m on v.object_id = m.object_id '
            .'where v.is_ms_shipped = 0'
            .$this->compileSchemaWhereClause($schema, 'schema_name(v.schema_id)')
            .' order by [schema], name';
    }

    /**
     * Compile the query to compare the schema.
     *
     * @param  string|string[]|null  $schema
     * @param  string  $column
     * @return string
     */
    protected function compileSchemaWhereClause($schema, $column)
    {
        return match (true) {
            ! empty($schema) && is_array($schema) => " and $column in (".$this->quoteString($schema).')',
            ! empty($schema) => " and $column = ".$this->quoteString($schema),
            default => '',
        };
    }

    /**
     * Compile the query to determine the columns.
     *
     * @param  string|null  $schema
     * @param  string  $table
     * @return string
     */
    public function compileColumns($schema, $table)
    {
        return sprintf(
            'select col.name, type.name as type_name, '
            .'col.max_length as length, col.precision as precision, col.scale as places, '
            .'col.is_nullable as nullable, def.definition as [default], '
            .'col.is_identity as autoincrement, col.collation_name as collation, '
            .'com.definition as [expression], is_persisted as [persisted], '
            .'cast(prop.value as nvarchar(max)) as comment '
            .'from sys.columns as col '
            .'join sys.types as type on col.user_type_id = type.user_type_id '
            .'join sys.objects as obj on col.object_id = obj.object_id '
            .'join sys.schemas as scm on obj.schema_id = scm.schema_id '
            .'left join sys.default_constraints def on col.default_object_id = def.object_id and col.object_id = def.parent_object_id '
            ."left join sys.extended_properties as prop on obj.object_id = prop.major_id and col.column_id = prop.minor_id and prop.name = 'MS_Description' "
            .'left join sys.computed_columns as com on col.column_id = com.column_id and col.object_id = com.object_id '
            ."where obj.type in ('U', 'V') and obj.name = %s and scm.name = %s "
            .'order by col.column_id',
            $this->quoteString($table),
            $schema ? $this->quoteString($schema) : 'schema_name()',
        );
    }

    /**
     * Compile the query to determine the indexes.
     *
     * @param  string|null  $schema
     * @param  string  $table
     * @return string
     */
    public function compileIndexes($schema, $table)
    {
        return sprintf(
            "select idx.name as name, string_agg(col.name, ',') within group (order by idxcol.key_ordinal) as columns, "
            .'idx.type_desc as [type], idx.is_unique as [unique], idx.is_primary_key as [primary] '
            .'from sys.indexes as idx '
            .'join sys.tables as tbl on idx.object_id = tbl.object_id '
            .'join sys.schemas as scm on tbl.schema_id = scm.schema_id '
            .'join sys.index_columns as idxcol on idx.object_id = idxcol.object_id and idx.index_id = idxcol.index_id '
            .'join sys.columns as col on idxcol.object_id = col.object_id and idxcol.column_id = col.column_id '
            .'where tbl.name = %s and scm.name = %s '
            .'group by idx.name, idx.type_desc, idx.is_unique, idx.is_primary_key',
            $this->quoteString($table),
            $schema ? $this->quoteString($schema) : 'schema_name()',
        );
    }

    /**
     * Compile the query to determine the foreign keys.
     *
     * @param  string|null  $schema
     * @param  string  $table
     * @return string
     */
    public function compileForeignKeys($schema, $table)
    {
        return sprintf(
            'select fk.name as name, '
            ."string_agg(lc.name, ',') within group (order by fkc.constraint_column_id) as columns, "
            .'fs.name as foreign_schema, ft.name as foreign_table, '
            ."string_agg(fc.name, ',') within group (order by fkc.constraint_column_id) as foreign_columns, "
            .'fk.update_referential_action_desc as on_update, '
            .'fk.delete_referential_action_desc as on_delete '
            .'from sys.foreign_keys as fk '
            .'join sys.foreign_key_columns as fkc on fkc.constraint_object_id = fk.object_id '
            .'join sys.tables as lt on lt.object_id = fk.parent_object_id '
            .'join sys.schemas as ls on lt.schema_id = ls.schema_id '
            .'join sys.columns as lc on fkc.parent_object_id = lc.object_id and fkc.parent_column_id = lc.column_id '
            .'join sys.tables as ft on ft.object_id = fk.referenced_object_id '
            .'join sys.schemas as fs on ft.schema_id = fs.schema_id '
            .'join sys.columns as fc on fkc.referenced_object_id = fc.object_id and fkc.referenced_column_id = fc.column_id '
            .'where lt.name = %s and ls.name = %s '
            .'group by fk.name, fs.name, ft.name, fk.update_referential_action_desc, fk.delete_referential_action_desc',
            $this->quoteString($table),
            $schema ? $this->quoteString($schema) : 'schema_name()',
        );
    }

    /**
     * Compile a create table command.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $command
     * @return string
     */
    public function compileCreate(Blueprint $blueprint, Fluent $command)
    {
        return sprintf('create table %s (%s)',
            $this->wrapTable($blueprint, $blueprint->temporary ? '#'.$this->connection->getTablePrefix() : null),
            implode(', ', $this->getColumns($blueprint))
        );
    }

    /**
     * Compile a column addition table command.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $command
     * @return string
     */
    public function compileAdd(Blueprint $blueprint, Fluent $command)
    {
        return sprintf('alter table %s add %s',
            $this->wrapTable($blueprint),
            $this->getColumn($blueprint, $command->column)
        );
    }

    /** @inheritDoc */
    public function compileRenameColumn(Blueprint $blueprint, Fluent $command)
    {
        return sprintf("sp_rename %s, %s, N'COLUMN'",
            $this->quoteString($this->wrapTable($blueprint).'.'.$this->wrap($command->from)),
            $this->wrap($command->to)
        );
    }

    /** @inheritDoc */
    public function compileChange(Blueprint $blueprint, Fluent $command)
    {
        return [
            $this->compileDropDefaultConstraint($blueprint, $command),
            sprintf('alter table %s alter column %s',
                $this->wrapTable($blueprint),
                $this->getColumn($blueprint, $command->column),
            ),
        ];
    }

    /**
     * Compile a primary key command.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $command
     * @return string
     */
    public function compilePrimary(Blueprint $blueprint, Fluent $command)
    {
        return sprintf('alter table %s add constraint %s primary key (%s)',
            $this->wrapTable($blueprint),
            $this->wrap($command->index),
            $this->columnize($command->columns)
        );
    }

    /**
     * Compile a unique key command.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $command
     * @return string
     */
    public function compileUnique(Blueprint $blueprint, Fluent $command)
    {
        return sprintf('create unique index %s on %s (%s)%s',
            $this->wrap($command->index),
            $this->wrapTable($blueprint),
            $this->columnize($command->columns),
            $command->online ? ' with (online = on)' : ''
        );
    }

    /**
     * Compile a plain index key command.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $command
     * @return string
     */
    public function compileIndex(Blueprint $blueprint, Fluent $command)
    {
        return sprintf('create index %s on %s (%s)%s',
            $this->wrap($command->index),
            $this->wrapTable($blueprint),
            $this->columnize($command->columns),
            $command->online ? ' with (online = on)' : ''
        );
    }

    /**
     * Compile a spatial index key command.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $command
     * @return string
     */
    public function compileSpatialIndex(Blueprint $blueprint, Fluent $command)
    {
        return sprintf('create spatial index %s on %s (%s)',
            $this->wrap($command->index),
            $this->wrapTable($blueprint),
            $this->columnize($command->columns)
        );
    }

    /**
     * Compile a default command.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $command
     * @return string|null
     */
    public function compileDefault(Blueprint $blueprint, Fluent $command)
    {
        if ($command->column->change && ! is_null($command->column->default)) {
            return sprintf('alter table %s add default %s for %s',
                $this->wrapTable($blueprint),
                $this->getDefaultValue($command->column->default),
                $this->wrap($command->column)
            );
        }
    }

    /**
     * Compile a drop table command.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $command
     * @return string
     */
    public function compileDrop(Blueprint $blueprint, Fluent $command)
    {
        return 'drop table '.$this->wrapTable($blueprint);
    }

    /**
     * Compile a drop table (if exists) command.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $command
     * @return string
     */
    public function compileDropIfExists(Blueprint $blueprint, Fluent $command)
    {
        return sprintf('if object_id(%s, \'U\') is not null drop table %s',
            $this->quoteString($this->wrapTable($blueprint)),
            $this->wrapTable($blueprint)
        );
    }

    /**
     * Compile the SQL needed to drop all tables.
     *
     * @return string
     */
    public function compileDropAllTables()
    {
        return "EXEC sp_msforeachtable 'DROP TABLE ?'";
    }

    /**
     * Compile a drop column command.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $command
     * @return string
     */
    public function compileDropColumn(Blueprint $blueprint, Fluent $command)
    {
        $columns = $this->wrapArray($command->columns);

        $dropExistingConstraintsSql = $this->compileDropDefaultConstraint($blueprint, $command).';';

        return $dropExistingConstraintsSql.'alter table '.$this->wrapTable($blueprint).' drop column '.implode(', ', $columns);
    }

    /**
     * Compile a drop default constraint command.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $command
     * @return string
     */
    public function compileDropDefaultConstraint(Blueprint $blueprint, Fluent $command)
    {
        $columns = $command->name === 'change'
            ? "'".$command->column->name."'"
            : "'".implode("', '", $command->columns)."'";

        $table = $this->wrapTable($blueprint);
        $tableName = $this->quoteString($this->wrapTable($blueprint));

        $sql = "DECLARE @sql NVARCHAR(MAX) = '';";
        $sql .= "SELECT @sql += 'ALTER TABLE $table DROP CONSTRAINT ' + OBJECT_NAME([default_object_id]) + ';' ";
        $sql .= 'FROM sys.columns ';
        $sql .= "WHERE [object_id] = OBJECT_ID($tableName) AND [name] in ($columns) AND [default_object_id] <> 0;";
        $sql .= 'EXEC(@sql)';

        return $sql;
    }

    /**
     * Compile a drop primary key command.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $command
     * @return string
     */
    public function compileDropPrimary(Blueprint $blueprint, Fluent $command)
    {
        $index = $this->wrap($command->index);

        return "alter table {$this->wrapTable($blueprint)} drop constraint {$index}";
    }

    /**
     * Compile a drop unique key command.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $command
     * @return string
     */
    public function compileDropUnique(Blueprint $blueprint, Fluent $command)
    {
        $index = $this->wrap($command->index);

        return "drop index {$index} on {$this->wrapTable($blueprint)}";
    }

    /**
     * Compile a drop index command.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $command
     * @return string
     */
    public function compileDropIndex(Blueprint $blueprint, Fluent $command)
    {
        $index = $this->wrap($command->index);

        return "drop index {$index} on {$this->wrapTable($blueprint)}";
    }

    /**
     * Compile a drop spatial index command.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $command
     * @return string
     */
    public function compileDropSpatialIndex(Blueprint $blueprint, Fluent $command)
    {
        return $this->compileDropIndex($blueprint, $command);
    }

    /**
     * Compile a drop foreign key command.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $command
     * @return string
     */
    public function compileDropForeign(Blueprint $blueprint, Fluent $command)
    {
        $index = $this->wrap($command->index);

        return "alter table {$this->wrapTable($blueprint)} drop constraint {$index}";
    }

    /**
     * Compile a rename table command.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $command
     * @return string
     */
    public function compileRename(Blueprint $blueprint, Fluent $command)
    {
        return sprintf('sp_rename %s, %s',
            $this->quoteString($this->wrapTable($blueprint)),
            $this->wrapTable($command->to)
        );
    }

    /**
     * Compile a rename index command.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $command
     * @return string
     */
    public function compileRenameIndex(Blueprint $blueprint, Fluent $command)
    {
        return sprintf("sp_rename %s, %s, N'INDEX'",
            $this->quoteString($this->wrapTable($blueprint).'.'.$this->wrap($command->from)),
            $this->wrap($command->to)
        );
    }

    /**
     * Compile the command to enable foreign key constraints.
     *
     * @return string
     */
    public function compileEnableForeignKeyConstraints()
    {
        return 'EXEC sp_msforeachtable @command1="print \'?\'", @command2="ALTER TABLE ? WITH CHECK CHECK CONSTRAINT all";';
    }

    /**
     * Compile the command to disable foreign key constraints.
     *
     * @return string
     */
    public function compileDisableForeignKeyConstraints()
    {
        return 'EXEC sp_msforeachtable "ALTER TABLE ? NOCHECK CONSTRAINT all";';
    }

    /**
     * Compile the command to drop all foreign keys.
     *
     * @return string
     */
    public function compileDropAllForeignKeys()
    {
        return "DECLARE @sql NVARCHAR(MAX) = N'';
            SELECT @sql += 'ALTER TABLE '
                + QUOTENAME(OBJECT_SCHEMA_NAME(parent_object_id)) + '.' + + QUOTENAME(OBJECT_NAME(parent_object_id))
                + ' DROP CONSTRAINT ' + QUOTENAME(name) + ';'
            FROM sys.foreign_keys;

            EXEC sp_executesql @sql;";
    }

    /**
     * Compile the command to drop all views.
     *
     * @return string
     */
    public function compileDropAllViews()
    {
        return "DECLARE @sql NVARCHAR(MAX) = N'';
            SELECT @sql += 'DROP VIEW ' + QUOTENAME(OBJECT_SCHEMA_NAME(object_id)) + '.' + QUOTENAME(name) + ';'
            FROM sys.views;

            EXEC sp_executesql @sql;";
    }

    /**
     * Create the column definition for a char type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeChar(Fluent $column)
    {
        return "nchar({$column->length})";
    }

    /**
     * Create the column definition for a string type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeString(Fluent $column)
    {
        return "nvarchar({$column->length})";
    }

    /**
     * Create the column definition for a tiny text type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeTinyText(Fluent $column)
    {
        return 'nvarchar(255)';
    }

    /**
     * Create the column definition for a text type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeText(Fluent $column)
    {
        return 'nvarchar(max)';
    }

    /**
     * Create the column definition for a medium text type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeMediumText(Fluent $column)
    {
        return 'nvarchar(max)';
    }

    /**
     * Create the column definition for a long text type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeLongText(Fluent $column)
    {
        return 'nvarchar(max)';
    }

    /**
     * Create the column definition for an integer type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeInteger(Fluent $column)
    {
        return 'int';
    }

    /**
     * Create the column definition for a big integer type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeBigInteger(Fluent $column)
    {
        return 'bigint';
    }

    /**
     * Create the column definition for a medium integer type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeMediumInteger(Fluent $column)
    {
        return 'int';
    }

    /**
     * Create the column definition for a tiny integer type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeTinyInteger(Fluent $column)
    {
        return 'tinyint';
    }

    /**
     * Create the column definition for a small integer type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeSmallInteger(Fluent $column)
    {
        return 'smallint';
    }

    /**
     * Create the column definition for a float type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeFloat(Fluent $column)
    {
        if ($column->precision) {
            return "float({$column->precision})";
        }

        return 'float';
    }

    /**
     * Create the column definition for a double type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeDouble(Fluent $column)
    {
        return 'double precision';
    }

    /**
     * Create the column definition for a decimal type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeDecimal(Fluent $column)
    {
        return "decimal({$column->total}, {$column->places})";
    }

    /**
     * Create the column definition for a boolean type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeBoolean(Fluent $column)
    {
        return 'bit';
    }

    /**
     * Create the column definition for an enumeration type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeEnum(Fluent $column)
    {
        return sprintf(
            'nvarchar(255) check ("%s" in (%s))',
            $column->name,
            $this->quoteString($column->allowed)
        );
    }

    /**
     * Create the column definition for a json type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeJson(Fluent $column)
    {
        return 'nvarchar(max)';
    }

    /**
     * Create the column definition for a jsonb type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeJsonb(Fluent $column)
    {
        return 'nvarchar(max)';
    }

    /**
     * Create the column definition for a date type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeDate(Fluent $column)
    {
        if ($column->useCurrent) {
            $column->default(new Expression('CAST(GETDATE() AS DATE)'));
        }

        return 'date';
    }

    /**
     * Create the column definition for a date-time type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeDateTime(Fluent $column)
    {
        return $this->typeTimestamp($column);
    }

    /**
     * Create the column definition for a date-time (with time zone) type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeDateTimeTz(Fluent $column)
    {
        return $this->typeTimestampTz($column);
    }

    /**
     * Create the column definition for a time type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeTime(Fluent $column)
    {
        return $column->precision ? "time($column->precision)" : 'time';
    }

    /**
     * Create the column definition for a time (with time zone) type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeTimeTz(Fluent $column)
    {
        return $this->typeTime($column);
    }

    /**
     * Create the column definition for a timestamp type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeTimestamp(Fluent $column)
    {
        if ($column->useCurrent) {
            $column->default(new Expression('CURRENT_TIMESTAMP'));
        }

        return $column->precision ? "datetime2($column->precision)" : 'datetime';
    }

    /**
     * Create the column definition for a timestamp (with time zone) type.
     *
     * @link https://docs.microsoft.com/en-us/sql/t-sql/data-types/datetimeoffset-transact-sql?view=sql-server-ver15
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeTimestampTz(Fluent $column)
    {
        if ($column->useCurrent) {
            $column->default(new Expression('CURRENT_TIMESTAMP'));
        }

        return $column->precision ? "datetimeoffset($column->precision)" : 'datetimeoffset';
    }

    /**
     * Create the column definition for a year type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeYear(Fluent $column)
    {
        if ($column->useCurrent) {
            $column->default(new Expression('CAST(YEAR(GETDATE()) AS INTEGER)'));
        }

        return $this->typeInteger($column);
    }

    /**
     * Create the column definition for a binary type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeBinary(Fluent $column)
    {
        if ($column->length) {
            return $column->fixed ? "binary({$column->length})" : "varbinary({$column->length})";
        }

        return 'varbinary(max)';
    }

    /**
     * Create the column definition for a uuid type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeUuid(Fluent $column)
    {
        return 'uniqueidentifier';
    }

    /**
     * Create the column definition for an IP address type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeIpAddress(Fluent $column)
    {
        return 'nvarchar(45)';
    }

    /**
     * Create the column definition for a MAC address type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeMacAddress(Fluent $column)
    {
        return 'nvarchar(17)';
    }

    /**
     * Create the column definition for a spatial Geometry type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeGeometry(Fluent $column)
    {
        return 'geometry';
    }

    /**
     * Create the column definition for a spatial Geography type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeGeography(Fluent $column)
    {
        return 'geography';
    }

    /**
     * Create the column definition for a generated, computed column type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string|null
     */
    protected function typeComputed(Fluent $column)
    {
        return "as ({$this->getValue($column->expression)})";
    }

    /**
     * Get the SQL for a collation column modifier.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $column
     * @return string|null
     */
    protected function modifyCollate(Blueprint $blueprint, Fluent $column)
    {
        if (! is_null($column->collation)) {
            return ' collate '.$column->collation;
        }
    }

    /**
     * Get the SQL for a nullable column modifier.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $column
     * @return string|null
     */
    protected function modifyNullable(Blueprint $blueprint, Fluent $column)
    {
        if ($column->type !== 'computed') {
            return $column->nullable ? ' null' : ' not null';
        }
    }

    /**
     * Get the SQL for a default column modifier.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $column
     * @return string|null
     */
    protected function modifyDefault(Blueprint $blueprint, Fluent $column)
    {
        if (! $column->change && ! is_null($column->default)) {
            return ' default '.$this->getDefaultValue($column->default);
        }
    }

    /**
     * Get the SQL for an auto-increment column modifier.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $column
     * @return string|null
     */
    protected function modifyIncrement(Blueprint $blueprint, Fluent $column)
    {
        if (! $column->change && in_array($column->type, $this->serials) && $column->autoIncrement) {
            return $this->hasCommand($blueprint, 'primary') ? ' identity' : ' identity primary key';
        }
    }

    /**
     * Get the SQL for a generated stored column modifier.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $column
     * @return string|null
     */
    protected function modifyPersisted(Blueprint $blueprint, Fluent $column)
    {
        if ($column->change) {
            if ($column->type === 'computed') {
                return $column->persisted ? ' add persisted' : ' drop persisted';
            }

            return null;
        }

        if ($column->persisted) {
            return ' persisted';
        }
    }

    /**
     * Quote the given string literal.
     *
     * @param  string|array<string>  $value
     * @return string
     */
    public function quoteString($value)
    {
        if (is_array($value)) {
            return implode(', ', array_map([$this, __FUNCTION__], $value));
        }

        return "N'$value'";
    }
}
