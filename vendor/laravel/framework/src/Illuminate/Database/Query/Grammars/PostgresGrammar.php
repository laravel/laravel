<?php

namespace Illuminate\Database\Query\Grammars;

use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinLateralClause;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class PostgresGrammar extends Grammar
{
    /**
     * All of the available clause operators.
     *
     * @var string[]
     */
    protected $operators = [
        '=', '<', '>', '<=', '>=', '<>', '!=',
        'like', 'not like', 'between', 'ilike', 'not ilike',
        '~', '&', '|', '#', '<<', '>>', '<<=', '>>=',
        '&&', '@>', '<@', '?', '?|', '?&', '||', '-', '@?', '@@', '#-',
        'is distinct from', 'is not distinct from',
    ];

    /**
     * The Postgres grammar specific custom operators.
     *
     * @var array
     */
    protected static $customOperators = [];

    /**
     * The grammar specific bitwise operators.
     *
     * @var array
     */
    protected $bitwiseOperators = [
        '~', '&', '|', '#', '<<', '>>', '<<=', '>>=',
    ];

    /**
     * Indicates if the cascade option should be used when truncating.
     *
     * @var bool
     */
    protected static $cascadeTruncate = true;

    /**
     * Compile a basic where clause.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $where
     * @return string
     */
    protected function whereBasic(Builder $query, $where)
    {
        if (str_contains(strtolower($where['operator']), 'like')) {
            return sprintf(
                '%s::text %s %s',
                $this->wrap($where['column']),
                $where['operator'],
                $this->parameter($where['value'])
            );
        }

        return parent::whereBasic($query, $where);
    }

    /**
     * Compile a bitwise operator where clause.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $where
     * @return string
     */
    protected function whereBitwise(Builder $query, $where)
    {
        $value = $this->parameter($where['value']);

        $operator = str_replace('?', '??', $where['operator']);

        return '('.$this->wrap($where['column']).' '.$operator.' '.$value.')::bool';
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
        $where['operator'] = $where['not'] ? 'not ' : '';

        $where['operator'] .= $where['caseSensitive'] ? 'like' : 'ilike';

        return $this->whereBasic($query, $where);
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
        $column = $this->wrap($where['column']);
        $value = $this->parameter($where['value']);

        if ($this->isJsonSelector($column)) {
            $column = '('.$column.')';
        }

        return $column.'::date '.$where['operator'].' '.$value;
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
        $column = $this->wrap($where['column']);
        $value = $this->parameter($where['value']);

        if ($this->isJsonSelector($column)) {
            $column = '('.$column.')';
        }

        return $column.'::time '.$where['operator'].' '.$value;
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

        return 'extract('.$type.' from '.$this->wrap($where['column']).') '.$where['operator'].' '.$value;
    }

    /**
     * Compile a "where fulltext" clause.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $where
     * @return string
     */
    public function whereFullText(Builder $query, $where)
    {
        $language = $where['options']['language'] ?? 'english';

        if (! in_array($language, $this->validFullTextLanguages())) {
            $language = 'english';
        }

        $isVector = $where['options']['vector'] ?? false;

        $columns = (new Collection($where['columns']))
            ->map(fn ($column) => $isVector
                ? $this->wrap($column)
                : "to_tsvector('{$language}', {$this->wrap($column)})")
            ->implode(' || ');

        $mode = 'plainto_tsquery';

        if (($where['options']['mode'] ?? []) === 'phrase') {
            $mode = 'phraseto_tsquery';
        }

        if (($where['options']['mode'] ?? []) === 'websearch') {
            $mode = 'websearch_to_tsquery';
        }

        if (($where['options']['mode'] ?? []) === 'raw') {
            $mode = 'to_tsquery';
        }

        return "({$columns}) @@ {$mode}('{$language}', {$this->parameter($where['value'])})";
    }

    /**
     * Get an array of valid full text languages.
     *
     * @return array
     */
    protected function validFullTextLanguages()
    {
        return [
            'simple',
            'arabic',
            'danish',
            'dutch',
            'english',
            'finnish',
            'french',
            'german',
            'hungarian',
            'indonesian',
            'irish',
            'italian',
            'lithuanian',
            'nepali',
            'norwegian',
            'portuguese',
            'romanian',
            'russian',
            'spanish',
            'swedish',
            'tamil',
            'turkish',
        ];
    }

    /**
     * Compile the "select *" portion of the query.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $columns
     * @return string|null
     */
    protected function compileColumns(Builder $query, $columns)
    {
        // If the query is actually performing an aggregating select, we will let that
        // compiler handle the building of the select clauses, as it will need some
        // more syntax that is best handled by that function to keep things neat.
        if (! is_null($query->aggregate)) {
            return;
        }

        if (is_array($query->distinct)) {
            $select = 'select distinct on ('.$this->columnize($query->distinct).') ';
        } elseif ($query->distinct) {
            $select = 'select distinct ';
        } else {
            $select = 'select ';
        }

        return $select.$this->columnize($columns);
    }

    /**
     * Compile a "JSON contains" statement into SQL.
     *
     * @param  string  $column
     * @param  string  $value
     * @return string
     */
    protected function compileJsonContains($column, $value)
    {
        $column = str_replace('->>', '->', $this->wrap($column));

        return '('.$column.')::jsonb @> '.$value;
    }

    /**
     * Compile a "JSON contains key" statement into SQL.
     *
     * @param  string  $column
     * @return string
     */
    protected function compileJsonContainsKey($column)
    {
        $segments = explode('->', $column);

        $lastSegment = array_pop($segments);

        if (filter_var($lastSegment, FILTER_VALIDATE_INT) !== false) {
            $i = $lastSegment;
        } elseif (preg_match('/\[(-?[0-9]+)\]$/', $lastSegment, $matches)) {
            $segments[] = Str::beforeLast($lastSegment, $matches[0]);

            $i = $matches[1];
        }

        $column = str_replace('->>', '->', $this->wrap(implode('->', $segments)));

        if (isset($i)) {
            return vsprintf('case when %s then %s else false end', [
                'jsonb_typeof(('.$column.")::jsonb) = 'array'",
                'jsonb_array_length(('.$column.')::jsonb) >= '.($i < 0 ? abs($i) : $i + 1),
            ]);
        }

        $key = "'".str_replace("'", "''", $lastSegment)."'";

        return 'coalesce(('.$column.')::jsonb ?? '.$key.', false)';
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
        $column = str_replace('->>', '->', $this->wrap($column));

        return 'jsonb_array_length(('.$column.')::jsonb) '.$operator.' '.$value;
    }

    /**
     * Compile a single having clause.
     *
     * @param  array  $having
     * @return string
     */
    protected function compileHaving(array $having)
    {
        if ($having['type'] === 'Bitwise') {
            return $this->compileHavingBitwise($having);
        }

        return parent::compileHaving($having);
    }

    /**
     * Compile a having clause involving a bitwise operator.
     *
     * @param  array  $having
     * @return string
     */
    protected function compileHavingBitwise($having)
    {
        $column = $this->wrap($having['column']);

        $parameter = $this->parameter($having['value']);

        return '('.$column.' '.$having['operator'].' '.$parameter.')::bool';
    }

    /**
     * Compile the lock into SQL.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  bool|string  $value
     * @return string
     */
    protected function compileLock(Builder $query, $value)
    {
        if (! is_string($value)) {
            return $value ? 'for update' : 'for share';
        }

        return $value;
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
        return $this->compileInsert($query, $values).' on conflict do nothing';
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
        return $this->compileInsertUsing($query, $columns, $sql).' on conflict do nothing';
    }

    /**
     * Compile an insert and get ID statement into SQL.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $values
     * @param  string|null  $sequence
     * @return string
     */
    public function compileInsertGetId(Builder $query, $values, $sequence)
    {
        return $this->compileInsert($query, $values).' returning '.$this->wrap($sequence ?: 'id');
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
     * Compile the columns for an update statement.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $values
     * @return string
     */
    protected function compileUpdateColumns(Builder $query, array $values)
    {
        return (new Collection($values))->map(function ($value, $key) {
            $column = last(explode('.', $key));

            if ($this->isJsonSelector($key)) {
                return $this->compileJsonUpdateColumn($column, $value);
            }

            return $this->wrap($column).' = '.$this->parameter($value);
        })->implode(', ');
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
     * Compile a "lateral join" clause.
     *
     * @param  \Illuminate\Database\Query\JoinLateralClause  $join
     * @param  string  $expression
     * @return string
     */
    public function compileJoinLateral(JoinLateralClause $join, string $expression): string
    {
        return trim("{$join->type} join lateral {$expression} on true");
    }

    /**
     * Prepares a JSON column being updated using the JSONB_SET function.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return string
     */
    protected function compileJsonUpdateColumn($key, $value)
    {
        $segments = explode('->', $key);

        $field = $this->wrap(array_shift($segments));

        $path = "'{".implode(',', $this->wrapJsonPathAttributes($segments, '"'))."}'";

        return "{$field} = jsonb_set({$field}::jsonb, {$path}, {$this->parameter($value)})";
    }

    /**
     * Compile an update from statement into SQL.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $values
     * @return string
     */
    public function compileUpdateFrom(Builder $query, $values)
    {
        $table = $this->wrapTable($query->from);

        // Each one of the columns in the update statements needs to be wrapped in the
        // keyword identifiers, also a place-holder needs to be created for each of
        // the values in the list of bindings so we can make the sets statements.
        $columns = $this->compileUpdateColumns($query, $values);

        $from = '';

        if (isset($query->joins)) {
            // When using Postgres, updates with joins list the joined tables in the from
            // clause, which is different than other systems like MySQL. Here, we will
            // compile out the tables that are joined and add them to a from clause.
            $froms = (new Collection($query->joins))
                ->map(fn ($join) => $this->wrapTable($join->table))
                ->all();

            if (count($froms) > 0) {
                $from = ' from '.implode(', ', $froms);
            }
        }

        $where = $this->compileUpdateWheres($query);

        return trim("update {$table} set {$columns}{$from} {$where}");
    }

    /**
     * Compile the additional where clauses for updates with joins.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return string
     */
    protected function compileUpdateWheres(Builder $query)
    {
        $baseWheres = $this->compileWheres($query);

        if (! isset($query->joins)) {
            return $baseWheres;
        }

        // Once we compile the join constraints, we will either use them as the where
        // clause or append them to the existing base where clauses. If we need to
        // strip the leading boolean we will do so when using as the only where.
        $joinWheres = $this->compileUpdateJoinWheres($query);

        if (trim($baseWheres) == '') {
            return 'where '.$this->removeLeadingBoolean($joinWheres);
        }

        return $baseWheres.' '.$joinWheres;
    }

    /**
     * Compile the "join" clause where clauses for an update.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return string
     */
    protected function compileUpdateJoinWheres(Builder $query)
    {
        $joinWheres = [];

        // Here we will just loop through all of the join constraints and compile them
        // all out then implode them. This should give us "where" like syntax after
        // everything has been built and then we will join it to the real wheres.
        foreach ($query->joins as $join) {
            foreach ($join->wheres as $where) {
                $method = "where{$where['type']}";

                $joinWheres[] = $where['boolean'].' '.$this->$method($query, $where);
            }
        }

        return implode(' ', $joinWheres);
    }

    /**
     * Prepare the bindings for an update statement.
     *
     * @param  array  $bindings
     * @param  array  $values
     * @return array
     */
    public function prepareBindingsForUpdateFrom(array $bindings, array $values)
    {
        $values = (new Collection($values))
            ->map(function ($value, $column) {
                return is_array($value) || ($this->isJsonSelector($column) && ! $this->isExpression($value))
                    ? json_encode($value)
                    : $value;
            })
            ->all();

        $bindingsWithoutWhere = Arr::except($bindings, ['select', 'where']);

        return array_values(
            array_merge($values, $bindings['where'], Arr::flatten($bindingsWithoutWhere))
        );
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

        $selectSql = $this->compileSelect($query->select($alias.'.ctid'));

        return "update {$table} set {$columns} where {$this->wrap('ctid')} in ({$selectSql})";
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
        $values = (new Collection($values))->map(function ($value, $column) {
            return is_array($value) || ($this->isJsonSelector($column) && ! $this->isExpression($value))
                ? json_encode($value)
                : $value;
        })->all();

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

        $selectSql = $this->compileSelect($query->select($alias.'.ctid'));

        return "delete from {$table} where {$this->wrap('ctid')} in ({$selectSql})";
    }

    /**
     * Compile a truncate table statement into SQL.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return array
     */
    public function compileTruncate(Builder $query)
    {
        return ['truncate '.$this->wrapTable($query->from).' restart identity'.(static::$cascadeTruncate ? ' cascade' : '') => []];
    }

    /**
     * Compile a query to get the number of open connections for a database.
     *
     * @return string
     */
    public function compileThreadCount()
    {
        return 'select count(*) as "Value" from pg_stat_activity';
    }

    /**
     * Wrap the given JSON selector.
     *
     * @param  string  $value
     * @return string
     */
    protected function wrapJsonSelector($value)
    {
        $path = explode('->', $value);

        $field = $this->wrapSegments(explode('.', array_shift($path)));

        $wrappedPath = $this->wrapJsonPathAttributes($path);

        $attribute = array_pop($wrappedPath);

        if (! empty($wrappedPath)) {
            return $field.'->'.implode('->', $wrappedPath).'->>'.$attribute;
        }

        return $field.'->>'.$attribute;
    }

    /**
     * Wrap the given JSON selector for boolean values.
     *
     * @param  string  $value
     * @return string
     */
    protected function wrapJsonBooleanSelector($value)
    {
        $selector = str_replace(
            '->>', '->',
            $this->wrapJsonSelector($value)
        );

        return '('.$selector.')::jsonb';
    }

    /**
     * Wrap the given JSON boolean value.
     *
     * @param  string  $value
     * @return string
     */
    protected function wrapJsonBooleanValue($value)
    {
        return "'".$value."'::jsonb";
    }

    /**
     * Wrap the attributes of the given JSON path.
     *
     * @param  array  $path
     * @return array
     */
    protected function wrapJsonPathAttributes($path)
    {
        $quote = func_num_args() === 2 ? func_get_arg(1) : "'";

        return (new Collection($path))
            ->map(fn ($attribute) => $this->parseJsonPathArrayKeys($attribute))
            ->collapse()
            ->map(function ($attribute) use ($quote) {
                return filter_var($attribute, FILTER_VALIDATE_INT) !== false
                    ? $attribute
                    : $quote.$attribute.$quote;
            })
            ->all();
    }

    /**
     * Parse the given JSON path attribute for array keys.
     *
     * @param  string  $attribute
     * @return array
     */
    protected function parseJsonPathArrayKeys($attribute)
    {
        if (preg_match('/(\[[^\]]+\])+$/', $attribute, $parts)) {
            $key = Str::beforeLast($attribute, $parts[0]);

            preg_match_all('/\[([^\]]+)\]/', $parts[0], $keys);

            return (new Collection([$key]))
                ->merge($keys[1])
                ->diff('')
                ->values()
                ->all();
        }

        return [$attribute];
    }

    /**
     * Substitute the given bindings into the given raw SQL query.
     *
     * @param  string  $sql
     * @param  array  $bindings
     * @return string
     */
    public function substituteBindingsIntoRawSql($sql, $bindings)
    {
        $query = parent::substituteBindingsIntoRawSql($sql, $bindings);

        foreach ($this->operators as $operator) {
            if (! str_contains($operator, '?')) {
                continue;
            }

            $query = str_replace(str_replace('?', '??', $operator), $operator, $query);
        }

        return $query;
    }

    /**
     * Get the Postgres grammar specific operators.
     *
     * @return array
     */
    public function getOperators()
    {
        return array_values(array_unique(array_merge(parent::getOperators(), static::$customOperators)));
    }

    /**
     * Set any Postgres grammar specific custom operators.
     *
     * @param  array  $operators
     * @return void
     */
    public static function customOperators(array $operators)
    {
        static::$customOperators = array_values(
            array_merge(static::$customOperators, array_filter(array_filter($operators, 'is_string')))
        );
    }

    /**
     * Enable or disable the "cascade" option when compiling the truncate statement.
     *
     * @param  bool  $value
     * @return void
     */
    public static function cascadeOnTruncate(bool $value = true)
    {
        static::$cascadeTruncate = $value;
    }

    /**
     * @deprecated use cascadeOnTruncate
     */
    public static function cascadeOnTrucate(bool $value = true)
    {
        self::cascadeOnTruncate($value);
    }
}
