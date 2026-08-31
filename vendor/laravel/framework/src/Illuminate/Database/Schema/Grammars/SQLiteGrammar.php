<?php

namespace Illuminate\Database\Schema\Grammars;

use Illuminate\Database\Query\Expression;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\IndexDefinition;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;
use RuntimeException;

class SQLiteGrammar extends Grammar
{
    /**
     * The possible column modifiers.
     *
     * @var string[]
     */
    protected $modifiers = ['Increment', 'Nullable', 'Default', 'Collate', 'VirtualAs', 'StoredAs'];

    /**
     * The columns available as serials.
     *
     * @var string[]
     */
    protected $serials = ['bigInteger', 'integer', 'mediumInteger', 'smallInteger', 'tinyInteger'];

    /**
     * Get the commands to be compiled on the alter command.
     *
     * @return array
     */
    public function getAlterCommands()
    {
        $alterCommands = ['change', 'primary', 'dropPrimary', 'foreign', 'dropForeign'];

        if (version_compare($this->connection->getServerVersion(), '3.35', '<')) {
            $alterCommands[] = 'dropColumn';
        }

        return $alterCommands;
    }

    /**
     * Compile the query to determine the SQL text that describes the given object.
     *
     * @param  string|null  $schema
     * @param  string  $name
     * @param  string  $type
     * @return string
     */
    public function compileSqlCreateStatement($schema, $name, $type = 'table')
    {
        return sprintf('select "sql" from %s.sqlite_master where type = %s and name = %s',
            $this->wrapValue($schema ?? 'main'),
            $this->quoteString($type),
            $this->quoteString($name)
        );
    }

    /**
     * Compile the query to determine if the dbstat table is available.
     *
     * @return string
     */
    public function compileDbstatExists()
    {
        return "select exists (select 1 from pragma_compile_options where compile_options = 'ENABLE_DBSTAT_VTAB') as enabled";
    }

    /**
     * Compile the query to determine the schemas.
     *
     * @return string
     */
    public function compileSchemas()
    {
        return 'select name, file as path, name = \'main\' as "default" from pragma_database_list order by name';
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
            'select exists (select 1 from %s.sqlite_master where name = %s and type = \'table\') as "exists"',
            $this->wrapValue($schema ?? 'main'),
            $this->quoteString($table)
        );
    }

    /**
     * Compile the query to determine the tables.
     *
     * @param  string|string[]|null  $schema
     * @param  bool  $withSize
     * @return string
     */
    public function compileTables($schema, $withSize = false)
    {
        return 'select tl.name as name, tl.schema as schema'
            .($withSize ? ', (select sum(s.pgsize) '
                .'from (select tl.name as name union select il.name as name from pragma_index_list(tl.name, tl.schema) as il) as es '
                .'join dbstat(tl.schema) as s on s.name = es.name) as size' : '')
            .' from pragma_table_list as tl where'
            .(match (true) {
                ! empty($schema) && is_array($schema) => ' tl.schema in ('.$this->quoteString($schema).') and',
                ! empty($schema) => ' tl.schema = '.$this->quoteString($schema).' and',
                default => '',
            })
            ." tl.type in ('table', 'virtual') and tl.name not like 'sqlite\_%' escape '\' "
            .'order by tl.schema, tl.name';
    }

    /**
     * Compile the query for legacy versions of SQLite to determine the tables.
     *
     * @param  string  $schema
     * @param  bool  $withSize
     * @return string
     */
    public function compileLegacyTables($schema, $withSize = false)
    {
        return $withSize
            ? sprintf(
                'select m.tbl_name as name, %s as schema, sum(s.pgsize) as size from %s.sqlite_master as m '
                .'join dbstat(%s) as s on s.name = m.name '
                ."where m.type in ('table', 'index') and m.tbl_name not like 'sqlite\_%%' escape '\' "
                .'group by m.tbl_name '
                .'order by m.tbl_name',
                $this->quoteString($schema),
                $this->wrapValue($schema),
                $this->quoteString($schema)
            )
            : sprintf(
                'select name, %s as schema from %s.sqlite_master '
                ."where type = 'table' and name not like 'sqlite\_%%' escape '\' order by name",
                $this->quoteString($schema),
                $this->wrapValue($schema)
            );
    }

    /**
     * Compile the query to determine the views.
     *
     * @param  string  $schema
     * @return string
     */
    public function compileViews($schema)
    {
        return sprintf(
            "select name, %s as schema, sql as definition from %s.sqlite_master where type = 'view' order by name",
            $this->quoteString($schema),
            $this->wrapValue($schema)
        );
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
            'select name, type, not "notnull" as "nullable", dflt_value as "default", pk as "primary", hidden as "extra" '
            .'from pragma_table_xinfo(%s, %s) order by cid asc',
            $this->quoteString($table),
            $this->quoteString($schema ?? 'main')
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
            'select \'primary\' as name, group_concat(col) as columns, 1 as "unique", 1 as "primary" '
            .'from (select name as col from pragma_table_xinfo(%s, %s) where pk > 0 order by pk, cid) group by name '
            .'union select name, group_concat(col) as columns, "unique", origin = \'pk\' as "primary" '
            .'from (select il.*, ii.name as col from pragma_index_list(%s, %s) il, pragma_index_info(il.name, %s) ii order by il.seq, ii.seqno) '
            .'group by name, "unique", "primary"',
            $table = $this->quoteString($table),
            $schema = $this->quoteString($schema ?? 'main'),
            $table,
            $schema,
            $schema
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
            'select group_concat("from") as columns, %s as foreign_schema, "table" as foreign_table, '
            .'group_concat("to") as foreign_columns, on_update, on_delete '
            .'from (select * from pragma_foreign_key_list(%s, %s) order by id desc, seq) '
            .'group by id, "table", on_update, on_delete',
            $schema = $this->quoteString($schema ?? 'main'),
            $this->quoteString($table),
            $schema
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
        return sprintf('%s table %s (%s%s%s)',
            $blueprint->temporary ? 'create temporary' : 'create',
            $this->wrapTable($blueprint),
            implode(', ', $this->getColumns($blueprint)),
            $this->addForeignKeys($this->getCommandsByName($blueprint, 'foreign')),
            $this->addPrimaryKeys($this->getCommandByName($blueprint, 'primary'))
        );
    }

    /**
     * Get the foreign key syntax for a table creation statement.
     *
     * @param  \Illuminate\Database\Schema\ForeignKeyDefinition[]  $foreignKeys
     * @return string|null
     */
    protected function addForeignKeys($foreignKeys)
    {
        return (new Collection($foreignKeys))->reduce(function ($sql, $foreign) {
            // Once we have all the foreign key commands for the table creation statement
            // we'll loop through each of them and add them to the create table SQL we
            // are building, since SQLite needs foreign keys on the tables creation.
            return $sql.$this->getForeignKey($foreign);
        }, '');
    }

    /**
     * Get the SQL for the foreign key.
     *
     * @param  \Illuminate\Support\Fluent  $foreign
     * @return string
     */
    protected function getForeignKey($foreign)
    {
        // We need to columnize the columns that the foreign key is being defined for
        // so that it is a properly formatted list. Once we have done this, we can
        // return the foreign key SQL declaration to the calling method for use.
        $sql = sprintf(', foreign key(%s) references %s(%s)',
            $this->columnize($foreign->columns),
            $this->wrapTable($foreign->on),
            $this->columnize((array) $foreign->references)
        );

        if (! is_null($foreign->onDelete)) {
            $sql .= " on delete {$foreign->onDelete}";
        }

        // If this foreign key specifies the action to be taken on update we will add
        // that to the statement here. We'll append it to this SQL and then return
        // this SQL so we can keep adding any other foreign constraints to this.
        if (! is_null($foreign->onUpdate)) {
            $sql .= " on update {$foreign->onUpdate}";
        }

        return $sql;
    }

    /**
     * Get the primary key syntax for a table creation statement.
     *
     * @param  \Illuminate\Support\Fluent|null  $primary
     * @return string|null
     */
    protected function addPrimaryKeys($primary)
    {
        if (! is_null($primary)) {
            return ", primary key ({$this->columnize($primary->columns)})";
        }
    }

    /**
     * Compile alter table commands for adding columns.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $command
     * @return string
     */
    public function compileAdd(Blueprint $blueprint, Fluent $command)
    {
        return sprintf('alter table %s add column %s',
            $this->wrapTable($blueprint),
            $this->getColumn($blueprint, $command->column)
        );
    }

    /**
     * Compile alter table command into a series of SQL statements.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $command
     * @return list<string>|string
     */
    public function compileAlter(Blueprint $blueprint, Fluent $command)
    {
        $columnNames = [];
        $autoIncrementColumn = null;

        $columns = (new Collection($blueprint->getState()->getColumns()))
            ->map(function ($column) use ($blueprint, &$columnNames, &$autoIncrementColumn) {
                $name = $this->wrap($column);

                $autoIncrementColumn = $column->autoIncrement ? $column->name : $autoIncrementColumn;

                if (is_null($column->virtualAs) && is_null($column->virtualAsJson) &&
                    is_null($column->storedAs) && is_null($column->storedAsJson)) {
                    $columnNames[] = $name;
                }

                return $this->addModifiers(
                    $this->wrap($column).' '.($column->full_type_definition ?? $this->getType($column)),
                    $blueprint,
                    $column
                );
            })->all();

        $indexes = (new Collection($blueprint->getState()->getIndexes()))
            ->reject(fn ($index) => str_starts_with('sqlite_', $index->index))
            ->map(fn ($index) => $this->{'compile'.ucfirst($index->name)}($blueprint, $index))
            ->all();

        [, $tableName] = $this->connection->getSchemaBuilder()->parseSchemaAndTable($blueprint->getTable());
        $tempTable = $this->wrapTable($blueprint, '__temp__'.$this->connection->getTablePrefix());
        $table = $this->wrapTable($blueprint);
        $columnNames = implode(', ', $columnNames);

        $foreignKeyConstraintsEnabled = $this->connection->scalar($this->pragma('foreign_keys'));

        return array_filter(array_merge([
            $foreignKeyConstraintsEnabled ? $this->compileDisableForeignKeyConstraints() : null,
            sprintf('create table %s (%s%s%s)',
                $tempTable,
                implode(', ', $columns),
                $this->addForeignKeys($blueprint->getState()->getForeignKeys()),
                $autoIncrementColumn ? '' : $this->addPrimaryKeys($blueprint->getState()->getPrimaryKey())
            ),
            sprintf('insert into %s (%s) select %s from %s', $tempTable, $columnNames, $columnNames, $table),
            sprintf('drop table %s', $table),
            sprintf('alter table %s rename to %s', $tempTable, $this->wrapTable($tableName)),
        ], $indexes, [$foreignKeyConstraintsEnabled ? $this->compileEnableForeignKeyConstraints() : null]));
    }

    /** @inheritDoc */
    public function compileChange(Blueprint $blueprint, Fluent $command)
    {
        // Handled on table alteration...
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
        // Handled on table creation or alteration...
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
        [$schema, $table] = $this->connection->getSchemaBuilder()->parseSchemaAndTable($blueprint->getTable());

        return sprintf('create unique index %s%s on %s (%s)',
            $schema ? $this->wrapValue($schema).'.' : '',
            $this->wrap($command->index),
            $this->wrapTable($table),
            $this->columnize($command->columns)
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
        [$schema, $table] = $this->connection->getSchemaBuilder()->parseSchemaAndTable($blueprint->getTable());

        return sprintf('create index %s%s on %s (%s)',
            $schema ? $this->wrapValue($schema).'.' : '',
            $this->wrap($command->index),
            $this->wrapTable($table),
            $this->columnize($command->columns)
        );
    }

    /**
     * Compile a spatial index key command.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $command
     * @return void
     *
     * @throws \RuntimeException
     */
    public function compileSpatialIndex(Blueprint $blueprint, Fluent $command)
    {
        throw new RuntimeException('The database driver in use does not support spatial indexes.');
    }

    /**
     * Compile a foreign key command.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $command
     * @return string|null
     */
    public function compileForeign(Blueprint $blueprint, Fluent $command)
    {
        // Handled on table creation or alteration...
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
        return 'drop table if exists '.$this->wrapTable($blueprint);
    }

    /**
     * Compile the SQL needed to drop all tables.
     *
     * @param  string|null  $schema
     * @return string
     */
    public function compileDropAllTables($schema = null)
    {
        return sprintf("delete from %s.sqlite_master where type in ('table', 'index', 'trigger')",
            $this->wrapValue($schema ?? 'main')
        );
    }

    /**
     * Compile the SQL needed to drop all views.
     *
     * @param  string|null  $schema
     * @return string
     */
    public function compileDropAllViews($schema = null)
    {
        return sprintf("delete from %s.sqlite_master where type in ('view')",
            $this->wrapValue($schema ?? 'main')
        );
    }

    /**
     * Compile the SQL needed to rebuild the database.
     *
     * @param  string|null  $schema
     * @return string
     */
    public function compileRebuild($schema = null)
    {
        return sprintf('vacuum %s',
            $this->wrapValue($schema ?? 'main')
        );
    }

    /**
     * Compile a drop column command.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $command
     * @return list<string>|null
     */
    public function compileDropColumn(Blueprint $blueprint, Fluent $command)
    {
        if (version_compare($this->connection->getServerVersion(), '3.35', '<')) {
            // Handled on table alteration...

            return null;
        }

        $table = $this->wrapTable($blueprint);

        $columns = $this->prefixArray('drop column', $this->wrapArray($command->columns));

        return (new Collection($columns))->map(fn ($column) => 'alter table '.$table.' '.$column)->all();
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
        // Handled on table alteration...
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
        return $this->compileDropIndex($blueprint, $command);
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
        [$schema] = $this->connection->getSchemaBuilder()->parseSchemaAndTable($blueprint->getTable());

        return sprintf('drop index %s%s',
            $schema ? $this->wrapValue($schema).'.' : '',
            $this->wrap($command->index)
        );
    }

    /**
     * Compile a drop spatial index command.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $command
     * @return void
     *
     * @throws \RuntimeException
     */
    public function compileDropSpatialIndex(Blueprint $blueprint, Fluent $command)
    {
        throw new RuntimeException('The database driver in use does not support spatial indexes.');
    }

    /**
     * Compile a drop foreign key command.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $command
     * @return array
     */
    public function compileDropForeign(Blueprint $blueprint, Fluent $command)
    {
        if (empty($command->columns)) {
            throw new RuntimeException('This database driver does not support dropping foreign keys by name.');
        }

        // Handled on table alteration...
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
        $from = $this->wrapTable($blueprint);

        return "alter table {$from} rename to ".$this->wrapTable($command->to);
    }

    /**
     * Compile a rename index command.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $command
     * @return array
     *
     * @throws \RuntimeException
     */
    public function compileRenameIndex(Blueprint $blueprint, Fluent $command)
    {
        $indexes = $this->connection->getSchemaBuilder()->getIndexes($blueprint->getTable());

        $index = Arr::first($indexes, fn ($index) => $index['name'] === $command->from);

        if (! $index) {
            throw new RuntimeException("Index [{$command->from}] does not exist.");
        }

        if ($index['primary']) {
            throw new RuntimeException('SQLite does not support altering primary keys.');
        }

        if ($index['unique']) {
            return [
                $this->compileDropUnique($blueprint, new IndexDefinition(['index' => $index['name']])),
                $this->compileUnique($blueprint,
                    new IndexDefinition(['index' => $command->to, 'columns' => $index['columns']])
                ),
            ];
        }

        return [
            $this->compileDropIndex($blueprint, new IndexDefinition(['index' => $index['name']])),
            $this->compileIndex($blueprint,
                new IndexDefinition(['index' => $command->to, 'columns' => $index['columns']])
            ),
        ];
    }

    /**
     * Compile the command to enable foreign key constraints.
     *
     * @return string
     */
    public function compileEnableForeignKeyConstraints()
    {
        return $this->pragma('foreign_keys', 1);
    }

    /**
     * Compile the command to disable foreign key constraints.
     *
     * @return string
     */
    public function compileDisableForeignKeyConstraints()
    {
        return $this->pragma('foreign_keys', 0);
    }

    /**
     * Get the SQL to get or set a PRAGMA value.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return string
     */
    public function pragma(string $key, mixed $value = null): string
    {
        return sprintf('pragma %s%s',
            $key,
            is_null($value) ? '' : ' = '.$value
        );
    }

    /**
     * Create the column definition for a char type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeChar(Fluent $column)
    {
        return 'varchar';
    }

    /**
     * Create the column definition for a string type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeString(Fluent $column)
    {
        return 'varchar';
    }

    /**
     * Create the column definition for a tiny text type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeTinyText(Fluent $column)
    {
        return 'text';
    }

    /**
     * Create the column definition for a text type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeText(Fluent $column)
    {
        return 'text';
    }

    /**
     * Create the column definition for a medium text type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeMediumText(Fluent $column)
    {
        return 'text';
    }

    /**
     * Create the column definition for a long text type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeLongText(Fluent $column)
    {
        return 'text';
    }

    /**
     * Create the column definition for an integer type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeInteger(Fluent $column)
    {
        return 'integer';
    }

    /**
     * Create the column definition for a big integer type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeBigInteger(Fluent $column)
    {
        return 'integer';
    }

    /**
     * Create the column definition for a medium integer type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeMediumInteger(Fluent $column)
    {
        return 'integer';
    }

    /**
     * Create the column definition for a tiny integer type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeTinyInteger(Fluent $column)
    {
        return 'integer';
    }

    /**
     * Create the column definition for a small integer type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeSmallInteger(Fluent $column)
    {
        return 'integer';
    }

    /**
     * Create the column definition for a float type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeFloat(Fluent $column)
    {
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
        return 'double';
    }

    /**
     * Create the column definition for a decimal type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeDecimal(Fluent $column)
    {
        return 'numeric';
    }

    /**
     * Create the column definition for a boolean type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeBoolean(Fluent $column)
    {
        return 'tinyint(1)';
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
            'varchar check ("%s" in (%s))',
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
        return $this->connection->getConfig('use_native_json') ? 'json' : 'text';
    }

    /**
     * Create the column definition for a jsonb type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeJsonb(Fluent $column)
    {
        return $this->connection->getConfig('use_native_jsonb') ? 'jsonb' : 'text';
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
            $column->default(new Expression('CURRENT_DATE'));
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
     * Note: "SQLite does not have a storage class set aside for storing dates and/or times."
     *
     * @link https://www.sqlite.org/datatype3.html
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeDateTimeTz(Fluent $column)
    {
        return $this->typeDateTime($column);
    }

    /**
     * Create the column definition for a time type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeTime(Fluent $column)
    {
        return 'time';
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

        return 'datetime';
    }

    /**
     * Create the column definition for a timestamp (with time zone) type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeTimestampTz(Fluent $column)
    {
        return $this->typeTimestamp($column);
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
            $column->default(new Expression("(CAST(strftime('%Y', 'now') AS INTEGER))"));
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
        return 'blob';
    }

    /**
     * Create the column definition for a uuid type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeUuid(Fluent $column)
    {
        return 'varchar';
    }

    /**
     * Create the column definition for an IP address type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeIpAddress(Fluent $column)
    {
        return 'varchar';
    }

    /**
     * Create the column definition for a MAC address type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeMacAddress(Fluent $column)
    {
        return 'varchar';
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
        return $this->typeGeometry($column);
    }

    /**
     * Create the column definition for a generated, computed column type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return void
     *
     * @throws \RuntimeException
     */
    protected function typeComputed(Fluent $column)
    {
        throw new RuntimeException('This database driver requires a type, see the virtualAs / storedAs modifiers.');
    }

    /**
     * Get the SQL for a generated virtual column modifier.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $column
     * @return string|null
     */
    protected function modifyVirtualAs(Blueprint $blueprint, Fluent $column)
    {
        if (! is_null($virtualAs = $column->virtualAsJson)) {
            if ($this->isJsonSelector($virtualAs)) {
                $virtualAs = $this->wrapJsonSelector($virtualAs);
            }

            return " as ({$virtualAs})";
        }

        if (! is_null($virtualAs = $column->virtualAs)) {
            return " as ({$this->getValue($virtualAs)})";
        }
    }

    /**
     * Get the SQL for a generated stored column modifier.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $column
     * @return string|null
     */
    protected function modifyStoredAs(Blueprint $blueprint, Fluent $column)
    {
        if (! is_null($storedAs = $column->storedAsJson)) {
            if ($this->isJsonSelector($storedAs)) {
                $storedAs = $this->wrapJsonSelector($storedAs);
            }

            return " as ({$storedAs}) stored";
        }

        if (! is_null($storedAs = $column->storedAs)) {
            return " as ({$this->getValue($column->storedAs)}) stored";
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
        if (is_null($column->virtualAs) &&
            is_null($column->virtualAsJson) &&
            is_null($column->storedAs) &&
            is_null($column->storedAsJson)) {
            return $column->nullable ? '' : ' not null';
        }

        if ($column->nullable === false) {
            return ' not null';
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
        if (! is_null($column->default) && is_null($column->virtualAs) && is_null($column->virtualAsJson) && is_null($column->storedAs)) {
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
        if (in_array($column->type, $this->serials) && $column->autoIncrement) {
            return ' primary key autoincrement';
        }
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
            return " collate '{$column->collation}'";
        }
    }

    /**
     * Wrap the given JSON selector.
     *
     * @param  string  $value
     * @return string
     */
    protected function wrapJsonSelector($value)
    {
        [$field, $path] = $this->wrapJsonFieldAndPath($value);

        return 'json_extract('.$field.$path.')';
    }
}
