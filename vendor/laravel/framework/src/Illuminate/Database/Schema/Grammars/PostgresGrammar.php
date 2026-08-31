<?php

namespace Illuminate\Database\Schema\Grammars;

use Illuminate\Database\Query\Expression;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;
use LogicException;

class PostgresGrammar extends Grammar
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
    protected $modifiers = ['Collate', 'Nullable', 'Default', 'VirtualAs', 'StoredAs', 'GeneratedAs', 'Increment'];

    /**
     * The columns available as serials.
     *
     * @var string[]
     */
    protected $serials = ['bigInteger', 'integer', 'mediumInteger', 'smallInteger', 'tinyInteger'];

    /**
     * The commands to be executed outside of create or alter command.
     *
     * @var string[]
     */
    protected $fluentCommands = ['AutoIncrementStartingValues', 'Comment'];

    /**
     * Compile a create database command.
     *
     * @param  string  $name
     * @return string
     */
    public function compileCreateDatabase($name)
    {
        $sql = parent::compileCreateDatabase($name);

        if ($charset = $this->connection->getConfig('charset')) {
            $sql .= sprintf(' encoding %s', $this->wrapValue($charset));
        }

        return $sql;
    }

    /**
     * Compile the query to determine the schemas.
     *
     * @return string
     */
    public function compileSchemas()
    {
        return 'select nspname as name, nspname = current_schema() as "default" from pg_namespace where '
            .$this->compileSchemaWhereClause(null, 'nspname')
            .' order by nspname';
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
            'select exists (select 1 from pg_class c, pg_namespace n where '
            ."n.nspname = %s and c.relname = %s and c.relkind in ('r', 'p') and n.oid = c.relnamespace)",
            $schema ? $this->quoteString($schema) : 'current_schema()',
            $this->quoteString($table)
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
        return 'select c.relname as name, n.nspname as schema, pg_total_relation_size(c.oid) as size, '
            ."obj_description(c.oid, 'pg_class') as comment from pg_class c, pg_namespace n "
            ."where c.relkind in ('r', 'p') and n.oid = c.relnamespace and "
            .$this->compileSchemaWhereClause($schema, 'n.nspname')
            .' order by n.nspname, c.relname';
    }

    /**
     * Compile the query to determine the views.
     *
     * @param  string|string[]|null  $schema
     * @return string
     */
    public function compileViews($schema)
    {
        return 'select viewname as name, schemaname as schema, definition from pg_views where '
            .$this->compileSchemaWhereClause($schema, 'schemaname')
            .' order by schemaname, viewname';
    }

    /**
     * Compile the query to determine the user-defined types.
     *
     * @param  string|string[]|null  $schema
     * @return string
     */
    public function compileTypes($schema)
    {
        return 'select t.typname as name, n.nspname as schema, t.typtype as type, t.typcategory as category, '
            ."((t.typinput = 'array_in'::regproc and t.typoutput = 'array_out'::regproc) or t.typtype = 'm') as implicit "
            .'from pg_type t join pg_namespace n on n.oid = t.typnamespace '
            .'left join pg_class c on c.oid = t.typrelid '
            .'left join pg_type el on el.oid = t.typelem '
            .'left join pg_class ce on ce.oid = el.typrelid '
            ."where ((t.typrelid = 0 and (ce.relkind = 'c' or ce.relkind is null)) or c.relkind = 'c') "
            ."and not exists (select 1 from pg_depend d where d.objid in (t.oid, t.typelem) and d.deptype = 'e') and "
            .$this->compileSchemaWhereClause($schema, 'n.nspname');
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
        return $column.(match (true) {
            ! empty($schema) && is_array($schema) => ' in ('.$this->quoteString($schema).')',
            ! empty($schema) => ' = '.$this->quoteString($schema),
            default => " <> 'information_schema' and $column not like 'pg\_%'",
        });
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
        $serverVersion = $this->connection->getServerVersion();

        return sprintf(
            'select a.attname as name, t.typname as type_name, format_type(a.atttypid, a.atttypmod) as type, '
            .(version_compare($serverVersion, '9.1', '<')
                ? 'null as collation, '
                : '(select tc.collcollate from pg_catalog.pg_collation tc where tc.oid = a.attcollation) as collation, ')
            .'not a.attnotnull as nullable, '
            .'(select pg_get_expr(adbin, adrelid) from pg_attrdef where c.oid = pg_attrdef.adrelid and pg_attrdef.adnum = a.attnum) as default, '
            .(version_compare($serverVersion, '12.0', '<') ? "'' as generated, " : 'a.attgenerated as generated, ')
            .'col_description(c.oid, a.attnum) as comment '
            .'from pg_attribute a, pg_class c, pg_type t, pg_namespace n '
            .'where c.relname = %s and n.nspname = %s and a.attnum > 0 and a.attrelid = c.oid and a.atttypid = t.oid and n.oid = c.relnamespace '
            .'order by a.attnum',
            $this->quoteString($table),
            $schema ? $this->quoteString($schema) : 'current_schema()'
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
            "select ic.relname as name, string_agg(a.attname, ',' order by indseq.ord) as columns, "
            .'am.amname as "type", i.indisunique as "unique", i.indisprimary as "primary" '
            .'from pg_index i '
            .'join pg_class tc on tc.oid = i.indrelid '
            .'join pg_namespace tn on tn.oid = tc.relnamespace '
            .'join pg_class ic on ic.oid = i.indexrelid '
            .'join pg_am am on am.oid = ic.relam '
            .'join lateral unnest(i.indkey) with ordinality as indseq(num, ord) on true '
            .'left join pg_attribute a on a.attrelid = i.indrelid and a.attnum = indseq.num '
            .'where tc.relname = %s and tn.nspname = %s '
            .'group by ic.relname, am.amname, i.indisunique, i.indisprimary',
            $this->quoteString($table),
            $schema ? $this->quoteString($schema) : 'current_schema()'
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
            'select c.conname as name, '
            ."string_agg(la.attname, ',' order by conseq.ord) as columns, "
            .'fn.nspname as foreign_schema, fc.relname as foreign_table, '
            ."string_agg(fa.attname, ',' order by conseq.ord) as foreign_columns, "
            .'c.confupdtype as on_update, c.confdeltype as on_delete '
            .'from pg_constraint c '
            .'join pg_class tc on c.conrelid = tc.oid '
            .'join pg_namespace tn on tn.oid = tc.relnamespace '
            .'join pg_class fc on c.confrelid = fc.oid '
            .'join pg_namespace fn on fn.oid = fc.relnamespace '
            .'join lateral unnest(c.conkey) with ordinality as conseq(num, ord) on true '
            .'join pg_attribute la on la.attrelid = c.conrelid and la.attnum = conseq.num '
            .'join pg_attribute fa on fa.attrelid = c.confrelid and fa.attnum = c.confkey[conseq.ord] '
            ."where c.contype = 'f' and tc.relname = %s and tn.nspname = %s "
            .'group by c.conname, fn.nspname, fc.relname, c.confupdtype, c.confdeltype',
            $this->quoteString($table),
            $schema ? $this->quoteString($schema) : 'current_schema()'
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
        return sprintf('%s table %s (%s)',
            $blueprint->temporary ? 'create temporary' : 'create',
            $this->wrapTable($blueprint),
            implode(', ', $this->getColumns($blueprint))
        );
    }

    /**
     * Compile a column addition command.
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
     * Compile the auto-incrementing column starting values.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $command
     * @return string
     */
    public function compileAutoIncrementStartingValues(Blueprint $blueprint, Fluent $command)
    {
        if ($command->column->autoIncrement
            && $value = $command->column->get('startingValue', $command->column->get('from'))) {
            return sprintf(
                'select setval(pg_get_serial_sequence(%s, %s), %s, false)',
                $this->quoteString($this->wrapTable($blueprint)),
                $this->quoteString($command->column->name),
                $value
            );
        }
    }

    /** @inheritDoc */
    public function compileChange(Blueprint $blueprint, Fluent $command)
    {
        $column = $command->column;

        $changes = ['type '.$this->getType($column).$this->modifyCollate($blueprint, $column)];

        foreach ($this->modifiers as $modifier) {
            if ($modifier === 'Collate') {
                continue;
            }

            if (method_exists($this, $method = "modify{$modifier}")) {
                $constraints = (array) $this->{$method}($blueprint, $column);

                foreach ($constraints as $constraint) {
                    $changes[] = $constraint;
                }
            }
        }

        return sprintf('alter table %s %s',
            $this->wrapTable($blueprint),
            implode(', ', $this->prefixArray('alter column '.$this->wrap($column), $changes))
        );
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
        $columns = $this->columnize($command->columns);

        return 'alter table '.$this->wrapTable($blueprint)." add primary key ({$columns})";
    }

    /**
     * Compile a unique key command.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $command
     * @return string[]
     */
    public function compileUnique(Blueprint $blueprint, Fluent $command)
    {
        $uniqueStatement = 'unique';

        if (! is_null($command->nullsNotDistinct)) {
            $uniqueStatement .= ' nulls '.($command->nullsNotDistinct ? 'not distinct' : 'distinct');
        }

        if ($command->online || $command->algorithm) {
            $createIndexSql = sprintf('create unique index %s%s on %s%s (%s)',
                $command->online ? 'concurrently ' : '',
                $this->wrap($command->index),
                $this->wrapTable($blueprint),
                $command->algorithm ? ' using '.$command->algorithm : '',
                $this->columnize($command->columns)
            );

            $sql = sprintf('alter table %s add constraint %s unique using index %s',
                $this->wrapTable($blueprint),
                $this->wrap($command->index),
                $this->wrap($command->index)
            );
        } else {
            $sql = sprintf(
                'alter table %s add constraint %s %s (%s)',
                $this->wrapTable($blueprint),
                $this->wrap($command->index),
                $uniqueStatement,
                $this->columnize($command->columns)
            );
        }

        if (! is_null($command->deferrable)) {
            $sql .= $command->deferrable ? ' deferrable' : ' not deferrable';
        }

        if ($command->deferrable && ! is_null($command->initiallyImmediate)) {
            $sql .= $command->initiallyImmediate ? ' initially immediate' : ' initially deferred';
        }

        return isset($createIndexSql) ? [$createIndexSql, $sql] : [$sql];
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
        return sprintf('create index %s%s on %s%s (%s)',
            $command->online ? 'concurrently ' : '',
            $this->wrap($command->index),
            $this->wrapTable($blueprint),
            $command->algorithm ? ' using '.$command->algorithm : '',
            $this->columnize($command->columns)
        );
    }

    /**
     * Compile a fulltext index key command.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $command
     * @return string
     *
     * @throws \RuntimeException
     */
    public function compileFulltext(Blueprint $blueprint, Fluent $command)
    {
        $language = $command->language ?: 'english';

        $columns = array_map(function ($column) use ($language) {
            return "to_tsvector({$this->quoteString($language)}, {$this->wrap($column)})";
        }, $command->columns);

        return sprintf('create index %s%s on %s using gin ((%s))',
            $command->online ? 'concurrently ' : '',
            $this->wrap($command->index),
            $this->wrapTable($blueprint),
            implode(' || ', $columns)
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
        $command->algorithm = 'gist';

        if (! is_null($command->operatorClass)) {
            return $this->compileIndexWithOperatorClass($blueprint, $command);
        }

        return $this->compileIndex($blueprint, $command);
    }

    /**
     * Compile a vector index key command.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $command
     * @return string
     */
    public function compileVectorIndex(Blueprint $blueprint, Fluent $command)
    {
        return $this->compileIndexWithOperatorClass($blueprint, $command);
    }

    /**
     * Compile a spatial index with operator class key command.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $command
     * @return string
     */
    protected function compileIndexWithOperatorClass(Blueprint $blueprint, Fluent $command)
    {
        $columns = $this->columnizeWithOperatorClass($command->columns, $command->operatorClass);

        return sprintf('create index %s%s on %s%s (%s)',
            $command->online ? 'concurrently ' : '',
            $this->wrap($command->index),
            $this->wrapTable($blueprint),
            $command->algorithm ? ' using '.$command->algorithm : '',
            $columns
        );
    }

    /**
     * Convert an array of column names to a delimited string with operator class.
     *
     * @param  array  $columns
     * @param  string  $operatorClass
     * @return string
     */
    protected function columnizeWithOperatorClass(array $columns, $operatorClass)
    {
        return implode(', ', array_map(function ($column) use ($operatorClass) {
            return $this->wrap($column).' '.$operatorClass;
        }, $columns));
    }

    /**
     * Compile a foreign key command.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $command
     * @return string
     */
    public function compileForeign(Blueprint $blueprint, Fluent $command)
    {
        $sql = parent::compileForeign($blueprint, $command);

        if (! is_null($command->deferrable)) {
            $sql .= $command->deferrable ? ' deferrable' : ' not deferrable';
        }

        if ($command->deferrable && ! is_null($command->initiallyImmediate)) {
            $sql .= $command->initiallyImmediate ? ' initially immediate' : ' initially deferred';
        }

        if (! is_null($command->notValid)) {
            $sql .= ' not valid';
        }

        return $sql;
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
     * @param  array<string>  $tables
     * @return string
     */
    public function compileDropAllTables($tables)
    {
        return 'drop table '.implode(', ', $this->escapeNames($tables)).' cascade';
    }

    /**
     * Compile the SQL needed to drop all views.
     *
     * @param  array<string>  $views
     * @return string
     */
    public function compileDropAllViews($views)
    {
        return 'drop view '.implode(', ', $this->escapeNames($views)).' cascade';
    }

    /**
     * Compile the SQL needed to drop all types.
     *
     * @param  array<string>  $types
     * @return string
     */
    public function compileDropAllTypes($types)
    {
        return 'drop type '.implode(', ', $this->escapeNames($types)).' cascade';
    }

    /**
     * Compile the SQL needed to drop all domains.
     *
     * @param  array<string>  $domains
     * @return string
     */
    public function compileDropAllDomains($domains)
    {
        return 'drop domain '.implode(', ', $this->escapeNames($domains)).' cascade';
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
        $columns = $this->prefixArray('drop column', $this->wrapArray($command->columns));

        return 'alter table '.$this->wrapTable($blueprint).' '.implode(', ', $columns);
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
        [, $table] = $this->connection->getSchemaBuilder()->parseSchemaAndTable($blueprint->getTable());
        $index = $this->wrap("{$this->connection->getTablePrefix()}{$table}_pkey");

        return 'alter table '.$this->wrapTable($blueprint)." drop constraint {$index}";
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

        return "alter table {$this->wrapTable($blueprint)} drop constraint {$index}";
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
        return "drop index {$this->wrap($command->index)}";
    }

    /**
     * Compile a drop fulltext index command.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $command
     * @return string
     */
    public function compileDropFullText(Blueprint $blueprint, Fluent $command)
    {
        return $this->compileDropIndex($blueprint, $command);
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
        $from = $this->wrapTable($blueprint);

        return "alter table {$from} rename to ".$this->wrapTable($command->to);
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
        return sprintf('alter index %s rename to %s',
            $this->wrap($command->from),
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
        return 'SET CONSTRAINTS ALL IMMEDIATE;';
    }

    /**
     * Compile the command to disable foreign key constraints.
     *
     * @return string
     */
    public function compileDisableForeignKeyConstraints()
    {
        return 'SET CONSTRAINTS ALL DEFERRED;';
    }

    /**
     * Compile a comment command.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $command
     * @return string
     */
    public function compileComment(Blueprint $blueprint, Fluent $command)
    {
        if (! is_null($comment = $command->column->comment) || $command->column->change) {
            return sprintf('comment on column %s.%s is %s',
                $this->wrapTable($blueprint),
                $this->wrap($command->column->name),
                is_null($comment) ? 'NULL' : "'".str_replace("'", "''", $comment)."'"
            );
        }
    }

    /**
     * Compile a table comment command.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $command
     * @return string
     */
    public function compileTableComment(Blueprint $blueprint, Fluent $command)
    {
        return sprintf('comment on table %s is %s',
            $this->wrapTable($blueprint),
            "'".str_replace("'", "''", $command->comment)."'"
        );
    }

    /**
     * Quote-escape the given tables, views, or types.
     *
     * @param  array<string>  $names
     * @return array<string>
     */
    public function escapeNames($names)
    {
        return array_map(
            fn ($name) => (new Collection(explode('.', $name)))->map($this->wrapValue(...))->implode('.'),
            $names
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
        if ($column->length) {
            return "char({$column->length})";
        }

        return 'char';
    }

    /**
     * Create the column definition for a string type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeString(Fluent $column)
    {
        if ($column->length) {
            return "varchar({$column->length})";
        }

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
        return 'varchar(255)';
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
        return $column->autoIncrement && is_null($column->generatedAs) && ! $column->change ? 'serial' : 'integer';
    }

    /**
     * Create the column definition for a big integer type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeBigInteger(Fluent $column)
    {
        return $column->autoIncrement && is_null($column->generatedAs) && ! $column->change ? 'bigserial' : 'bigint';
    }

    /**
     * Create the column definition for a medium integer type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeMediumInteger(Fluent $column)
    {
        return $this->typeInteger($column);
    }

    /**
     * Create the column definition for a tiny integer type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeTinyInteger(Fluent $column)
    {
        return $this->typeSmallInteger($column);
    }

    /**
     * Create the column definition for a small integer type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeSmallInteger(Fluent $column)
    {
        return $column->autoIncrement && is_null($column->generatedAs) && ! $column->change ? 'smallserial' : 'smallint';
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
     * Create the column definition for a real type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeReal(Fluent $column)
    {
        return 'real';
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
        return 'boolean';
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
            'varchar(255) check ("%s" in (%s))',
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
        return 'json';
    }

    /**
     * Create the column definition for a jsonb type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeJsonb(Fluent $column)
    {
        return 'jsonb';
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
        return 'time'.(is_null($column->precision) ? '' : "($column->precision)").' without time zone';
    }

    /**
     * Create the column definition for a time (with time zone) type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeTimeTz(Fluent $column)
    {
        return 'time'.(is_null($column->precision) ? '' : "($column->precision)").' with time zone';
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

        return 'timestamp'.(is_null($column->precision) ? '' : "($column->precision)").' without time zone';
    }

    /**
     * Create the column definition for a timestamp (with time zone) type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeTimestampTz(Fluent $column)
    {
        if ($column->useCurrent) {
            $column->default(new Expression('CURRENT_TIMESTAMP'));
        }

        return 'timestamp'.(is_null($column->precision) ? '' : "($column->precision)").' with time zone';
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
            $column->default(new Expression('EXTRACT(YEAR FROM CURRENT_DATE)'));
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
        return 'bytea';
    }

    /**
     * Create the column definition for a uuid type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeUuid(Fluent $column)
    {
        return 'uuid';
    }

    /**
     * Create the column definition for an IP address type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeIpAddress(Fluent $column)
    {
        return 'inet';
    }

    /**
     * Create the column definition for a MAC address type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeMacAddress(Fluent $column)
    {
        return 'macaddr';
    }

    /**
     * Create the column definition for a spatial Geometry type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeGeometry(Fluent $column)
    {
        if ($column->subtype) {
            return sprintf('geometry(%s%s)',
                strtolower($column->subtype),
                $column->srid ? ','.$column->srid : ''
            );
        }

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
        if ($column->subtype) {
            return sprintf('geography(%s%s)',
                strtolower($column->subtype),
                $column->srid ? ','.$column->srid : ''
            );
        }

        return 'geography';
    }

    /**
     * Create the column definition for a vector type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeVector(Fluent $column)
    {
        return isset($column->dimensions) && $column->dimensions !== ''
            ? "vector({$column->dimensions})"
            : 'vector';
    }

    /**
     * Create the column definition for a tsvector type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeTsvector(Fluent $column)
    {
        return 'tsvector';
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
            return ' collate '.$this->wrapValue($column->collation);
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
        if ($column->change) {
            return $column->nullable ? 'drop not null' : 'set not null';
        }

        return $column->nullable ? ' null' : ' not null';
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
        if ($column->change) {
            if (! $column->autoIncrement || ! is_null($column->generatedAs)) {
                return is_null($column->default) ? 'drop default' : 'set default '.$this->getDefaultValue($column->default);
            }

            return null;
        }

        if (! is_null($column->default)) {
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
        if (! $column->change
            && ! $this->hasCommand($blueprint, 'primary')
            && (in_array($column->type, $this->serials) || ($column->generatedAs !== null))
            && $column->autoIncrement) {
            return ' primary key';
        }
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
        if ($column->change) {
            if (array_key_exists('virtualAs', $column->getAttributes())) {
                return is_null($column->virtualAs)
                    ? 'drop expression if exists'
                    : throw new LogicException('This database driver does not support modifying generated columns.');
            }

            return null;
        }

        if (! is_null($column->virtualAs)) {
            return " generated always as ({$this->getValue($column->virtualAs)}) virtual";
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
        if ($column->change) {
            if (array_key_exists('storedAs', $column->getAttributes())) {
                return is_null($column->storedAs)
                    ? 'drop expression if exists'
                    : throw new LogicException('This database driver does not support modifying generated columns.');
            }

            return null;
        }

        if (! is_null($column->storedAs)) {
            return " generated always as ({$this->getValue($column->storedAs)}) stored";
        }
    }

    /**
     * Get the SQL for an identity column modifier.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $column
     * @return string|list<string>|null
     */
    protected function modifyGeneratedAs(Blueprint $blueprint, Fluent $column)
    {
        $sql = null;

        if (! is_null($column->generatedAs)) {
            $sql = sprintf(
                ' generated %s as identity%s',
                $column->always ? 'always' : 'by default',
                ! is_bool($column->generatedAs) && ! empty($column->generatedAs) ? " ({$column->generatedAs})" : ''
            );
        }

        if ($column->change) {
            $changes = $column->autoIncrement && is_null($sql) ? [] : ['drop identity if exists'];

            if (! is_null($sql)) {
                $changes[] = 'add '.$sql;
            }

            return $changes;
        }

        return $sql;
    }
}
