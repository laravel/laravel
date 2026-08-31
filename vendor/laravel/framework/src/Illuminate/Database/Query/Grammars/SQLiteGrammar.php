<?php

namespace Illuminate\Database\Query\Grammars;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use InvalidArgumentException;

class SQLiteGrammar extends Grammar
{
    /**
     * All of the available clause operators.
     *
     * @var string[]
     */
    protected $operators = [
        '=', '<', '>', '<=', '>=', '<>', '!=',
        'like', 'not like', 'ilike',
        '&', '|', '<<', '>>',
    ];

    /**
     * Compile the lock into SQL.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  bool|string  $value
     * @return string
     */
    protected function compileLock(Builder $query, $value)
    {
        return '';
    }

    /**
     * Wrap a union subquery in parentheses.
     *
     * @param  string  $sql
     * @return string
     */
    protected function wrapUnion($sql)
    {
        return 'select * from ('.$sql.')';
    }

    /**
     * Compile a "where like" clause.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $where
     * @return string
     */
    protected function whereLike(Builder $query, $where)
    {
        if ($where['caseSensitive'] == false) {
            return parent::whereLike($query, $where);
        }
        $where['operator'] = $where['not'] ? 'not glob' : 'glob';

        return $this->whereBasic($query, $where);
    }

    /**
     * Convert a LIKE pattern to a GLOB pattern using simple string replacement.
     *
     * @param  string  $value
     * @param  bool  $caseSensitive
     * @return string
     */
    public function prepareWhereLikeBinding($value, $caseSensitive)
    {
        return $caseSensitive === false ? $value : str_replace(
            ['*', '?', '%', '_'],
            ['[*]', '[?]', '*', '?'],
            $value
        );
    }

    /**
     * Compile a "where null safe equals" clause.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $where
     * @return string
     */
    protected function whereNullSafeEquals(Builder $query, $where)
    {
        return $this->wrap($where['column']).' is '.$this->parameter($where['value']);
    }

    /**
     * Compile a "where date" clause.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $where
     * @return string
     */
    protected function whereDate(Builder $query, $where)
    {
        return $this->dateBasedWhere('%Y-%m-%d', $query, $where);
    }

    /**
     * Compile a "where day" clause.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $where
     * @return string
     */
    protected function whereDay(Builder $query, $where)
    {
        return $this->dateBasedWhere('%d', $query, $where);
    }

    /**
     * Compile a "where month" clause.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $where
     * @return string
     */
    protected function whereMonth(Builder $query, $where)
    {
        return $this->dateBasedWhere('%m', $query, $where);
    }

    /**
     * Compile a "where year" clause.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $where
     * @return string
     */
    protected function whereYear(Builder $query, $where)
    {
        return $this->dateBasedWhere('%Y', $query, $where);
    }

    /**
     * Compile a "where time" clause.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $where
     * @return string
     */
    protected function whereTime(Builder $query, $where)
    {
        return $this->dateBasedWhere('%H:%M:%S', $query, $where);
    }

    /**
     * Compile a date based where clause.
     *
     * @param  string  $type
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $where
     * @return string
     */
    protected function dateBasedWhere($type, Builder $query, $where)
    {
        $value = $this->parameter($where['value']);

        return "strftime('{$type}', {$this->wrap($where['column'])}) {$where['operator']} cast({$value} as text)";
    }

    /**
     * Compile the index hints for the query.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  \Illuminate\Database\Query\IndexHint  $indexHint
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    protected function compileIndexHint(Builder $query, $indexHint)
    {
        if ($indexHint->type !== 'force') {
            return '';
        }

        $index = $indexHint->index;

        if (! preg_match('/^[a-zA-Z0-9_$]+$/', $index)) {
            throw new InvalidArgumentException('Index name contains invalid characters.');
        }

        return "indexed by {$index}";
    }

    /**
     * Compile a "JSON length" statement into SQL.
     *
     * @param  string  $column
     * @param  string  $operator
     * @param  string  $value
     * @return string
     */
    protected function compileJsonLength($column, $operator, $value)
    {
        [$field, $path] = $this->wrapJsonFieldAndPath($column);

        return 'json_array_length('.$field.$path.') '.$operator.' '.$value;
    }

    /**
     * Compile a "JSON contains" statement into SQL.
     *
     * @param  string  $column
     * @param  mixed  $value
     * @return string
     */
    protected function compileJsonContains($column, $value)
    {
        [$field, $path] = $this->wrapJsonFieldAndPath($column);

        return 'exists (select 1 from json_each('.$field.$path.') where '.$this->wrap('json_each.value').' is '.$value.')';
    }

    /**
     * Prepare the binding for a "JSON contains" statement.
     *
     * @param  mixed  $binding
     * @return mixed
     */
    public function prepareBindingForJsonContains($binding)
    {
        return $binding;
    }

    /**
     * Compile a "JSON contains key" statement into SQL.
     *
     * @param  string  $column
     * @return string
     */
    protected function compileJsonContainsKey($column)
    {
        [$field, $path] = $this->wrapJsonFieldAndPath($column);

        return 'json_type('.$field.$path.') is not null';
    }

    /**
     * Compile a group limit clause.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return string
     */
    protected function compileGroupLimit(Builder $query)
    {
        $version = $query->getConnection()->getServerVersion();

        if (version_compare($version, '3.25.0', '>=')) {
            return parent::compileGroupLimit($query);
        }

        $query->groupLimit = null;

        return $this->compileSelect($query);
    }

    /**
     * Compile an update statement into SQL.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $values
     * @return string
     */
    public function compileUpdate(Builder $query, array $values)
    {
        if (isset($query->joins) || isset($query->limit)) {
            return $this->compileUpdateWithJoinsOrLimit($query, $values);
        }

        return parent::compileUpdate($query, $values);
    }

    /**
     * Compile an insert ignore statement into SQL.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $values
     * @return string
     */
    public function compileInsertOrIgnore(Builder $query, array $values)
    {
        return Str::replaceFirst('insert', 'insert or ignore', $this->compileInsert($query, $values));
    }

    /**
     * Compile an insert ignore statement using a subquery into SQL.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $columns
     * @param  string  $sql
     * @return string
     */
    public function compileInsertOrIgnoreUsing(Builder $query, array $columns, string $sql)
    {
        return Str::replaceFirst('insert', 'insert or ignore', $this->compileInsertUsing($query, $columns, $sql));
    }

    /**
     * Compile the columns for an update statement.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $values
     * @return string
     */
    protected function compileUpdateColumns(Builder $query, array $values)
    {
        $jsonGroups = $this->groupJsonColumnsForUpdate($values);

        return (new Collection($values))
            ->reject(fn ($value, $key) => $this->isJsonSelector($key))
            ->merge($jsonGroups)
            ->map(function ($value, $key) use ($jsonGroups) {
                $column = last(explode('.', $key));

                $value = isset($jsonGroups[$key]) ? $this->compileJsonPatch($column, $value) : $this->parameter($value);

                return $this->wrap($column).' = '.$value;
            })
            ->implode(', ');
    }

    /**
     * Compile an "upsert" statement into SQL.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $values
     * @param  array  $uniqueBy
     * @param  array  $update
     * @return string
     */
    public function compileUpsert(Builder $query, array $values, array $uniqueBy, array $update)
    {
        $sql = $this->compileInsert($query, $values);

        $sql .= ' on conflict ('.$this->columnize($uniqueBy).') do update set ';

        $columns = (new Collection($update))->map(function ($value, $key) {
            return is_numeric($key)
                ? $this->wrap($value).' = '.$this->wrapValue('excluded').'.'.$this->wrap($value)
                : $this->wrap($key).' = '.$this->parameter($value);
        })->implode(', ');

        return $sql.$columns;
    }

    /**
     * Group the nested JSON columns.
     *
     * @param  array  $values
     * @return array
     */
    protected function groupJsonColumnsForUpdate(array $values)
    {
        $groups = [];

        foreach ($values as $key => $value) {
            if ($this->isJsonSelector($key)) {
                Arr::set($groups, str_replace('->', '.', Str::after($key, '.')), $value);
            }
        }

        return $groups;
    }

    /**
     * Compile a "JSON" patch statement into SQL.
     *
     * @param  string  $column
     * @param  mixed  $value
     * @return string
     */
    protected function compileJsonPatch($column, $value)
    {
        return "json_patch(ifnull({$this->wrap($column)}, json('{}')), json({$this->parameter($value)}))";
    }

    /**
     * Compile an update statement with joins or limit into SQL.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $values
     * @return string
     */
    protected function compileUpdateWithJoinsOrLimit(Builder $query, array $values)
    {
        $table = $this->wrapTable($query->from);

        $columns = $this->compileUpdateColumns($query, $values);

        $alias = last(preg_split('/\s+as\s+/i', $query->from));

        $selectSql = $this->compileSelect($query->select($alias.'.rowid'));

        return "update {$table} set {$columns} where {$this->wrap('rowid')} in ({$selectSql})";
    }

    /**
     * Prepare the bindings for an update statement.
     *
     * @param  array  $bindings
     * @param  array  $values
     * @return array
     */
    #[\Override]
    public function prepareBindingsForUpdate(array $bindings, array $values)
    {
        $groups = $this->groupJsonColumnsForUpdate($values);

        $values = (new Collection($values))
            ->reject(fn ($value, $key) => $this->isJsonSelector($key))
            ->merge($groups)
            ->map(fn ($value) => is_array($value) ? json_encode($value) : $value)
            ->all();

        $cleanBindings = Arr::except($bindings, 'select');

        $values = Arr::flatten(array_map(fn ($value) => value($value), $values));

        return array_values(
            array_merge($values, Arr::flatten($cleanBindings))
        );
    }

    /**
     * Compile a delete statement into SQL.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return string
     */
    public function compileDelete(Builder $query)
    {
        if (isset($query->joins) || isset($query->limit)) {
            return $this->compileDeleteWithJoinsOrLimit($query);
        }

        return parent::compileDelete($query);
    }

    /**
     * Compile a delete statement with joins or limit into SQL.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return string
     */
    protected function compileDeleteWithJoinsOrLimit(Builder $query)
    {
        $table = $this->wrapTable($query->from);

        $alias = last(preg_split('/\s+as\s+/i', $query->from));

        $selectSql = $this->compileSelect($query->select($alias.'.rowid'));

        return "delete from {$table} where {$this->wrap('rowid')} in ({$selectSql})";
    }

    /**
     * Compile a truncate table statement into SQL.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return array
     */
    public function compileTruncate(Builder $query)
    {
        [$schema, $table] = $query->getConnection()->getSchemaBuilder()->parseSchemaAndTable($query->from);

        $schema = $schema ? $this->wrapValue($schema).'.' : '';

        return [
            'delete from '.$schema.'sqlite_sequence where name = ?' => [$query->getConnection()->getTablePrefix().$table],
            'delete from '.$this->wrapTable($query->from) => [],
        ];
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
