<?php

namespace Illuminate\Database\Query\Grammars;

use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Database\Concerns\CompilesJsonPaths;
use Illuminate\Database\Grammar as BaseGrammar;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Database\Query\JoinLateralClause;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use RuntimeException;

class Grammar extends BaseGrammar
{
    use CompilesJsonPaths;

    /**
     * The grammar specific operators.
     *
     * @var array
     */
    protected $operators = [];

    /**
     * The grammar specific bitwise operators.
     *
     * @var array
     */
    protected $bitwiseOperators = [];

    /**
     * The components that make up a select clause.
     *
     * @var string[]
     */
    protected $selectComponents = [
        'aggregate',
        'columns',
        'from',
        'indexHint',
        'joins',
        'wheres',
        'groups',
        'havings',
        'orders',
        'limit',
        'offset',
        'lock',
    ];

    /**
     * Compile a select query into SQL.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return string
     */
    public function compileSelect(Builder $query)
    {
        if (($query->unions || $query->havings) && $query->aggregate) {
            return $this->compileUnionAggregate($query);
        }

        // If a "group limit" is in place, we will need to compile the SQL to use a
        // different syntax. This primarily supports limits on eager loads using
        // Eloquent. We'll also set the columns if they have not been defined.
        if (isset($query->groupLimit)) {
            if (is_null($query->columns)) {
                $query->columns = ['*'];
            }

            return $this->compileGroupLimit($query);
        }

        // If the query does not have any columns set, we'll set the columns to the
        // * character to just get all of the columns from the database. Then we
        // can build the query and concatenate all the pieces together as one.
        $original = $query->columns;

        if (is_null($query->columns)) {
            $query->columns = ['*'];
        }

        // To compile the query, we'll spin through each component of the query and
        // see if that component exists. If it does we'll just call the compiler
        // function for the component which is responsible for making the SQL.
        $sql = trim($this->concatenate(
            $this->compileComponents($query))
        );

        if ($query->unions) {
            $sql = $this->wrapUnion($sql).' '.$this->compileUnions($query);
        }

        $query->columns = $original;

        return $sql;
    }

    /**
     * Compile the components necessary for a select clause.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return array
     */
    protected function compileComponents(Builder $query)
    {
        $sql = [];

        foreach ($this->selectComponents as $component) {
            if (isset($query->$component)) {
                $method = 'compile'.ucfirst($component);

                $sql[$component] = $this->$method($query, $query->$component);
            }
        }

        return $sql;
    }

    /**
     * Compile an aggregated select clause.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array{function: string, columns: array<\Illuminate\Contracts\Database\Query\Expression|string>}  $aggregate
     * @return string
     */
    protected function compileAggregate(Builder $query, $aggregate)
    {
        $column = $this->columnize($aggregate['columns']);

        // If the query has a "distinct" constraint and we're not asking for all columns
        // we need to prepend "distinct" onto the column name so that the query takes
        // it into account when it performs the aggregating operations on the data.
        if (is_array($query->distinct)) {
            $column = 'distinct '.$this->columnize($query->distinct);
        } elseif ($query->distinct && $column !== '*') {
            $column = 'distinct '.$column;
        }

        return 'select '.$aggregate['function'].'('.$column.') as aggregate';
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

        if ($query->distinct) {
            $select = 'select distinct ';
        } else {
            $select = 'select ';
        }

        return $select.$this->columnize($columns);
    }

    /**
     * Compile the "from" portion of the query.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  string  $table
     * @return string
     */
    protected function compileFrom(Builder $query, $table)
    {
        return 'from '.$this->wrapTable($table);
    }

    /**
     * Compile the "join" portions of the query.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $joins
     * @return string
     */
    protected function compileJoins(Builder $query, $joins)
    {
        return (new Collection($joins))->map(function ($join) use ($query) {
            $table = $this->wrapTable($join->table);

            $nestedJoins = is_null($join->joins) ? '' : ' '.$this->compileJoins($query, $join->joins);

            $tableAndNestedJoins = is_null($join->joins) ? $table : '('.$table.$nestedJoins.')';

            if ($join instanceof JoinLateralClause) {
                return $this->compileJoinLateral($join, $tableAndNestedJoins);
            }

            return trim("{$join->type} join {$tableAndNestedJoins} {$this->compileWheres($join)}");
        })->implode(' ');
    }

    /**
     * Compile a "lateral join" clause.
     *
     * @param  \Illuminate\Database\Query\JoinLateralClause  $join
     * @param  string  $expression
     * @return string
     *
     * @throws \RuntimeException
     */
    public function compileJoinLateral(JoinLateralClause $join, string $expression): string
    {
        throw new RuntimeException('This database engine does not support lateral joins.');
    }

    /**
     * Compile the "where" portions of the query.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return string
     */
    public function compileWheres(Builder $query)
    {
        // Each type of where clause has its own compiler function, which is responsible
        // for actually creating the where clauses SQL. This helps keep the code nice
        // and maintainable since each clause has a very small method that it uses.
        if (is_null($query->wheres)) {
            return '';
        }

        // If we actually have some where clauses, we will strip off the first boolean
        // operator, which is added by the query builders for convenience so we can
        // avoid checking for the first clauses in each of the compilers methods.
        if (count($sql = $this->compileWheresToArray($query)) > 0) {
            return $this->concatenateWhereClauses($query, $sql);
        }

        return '';
    }

    /**
     * Get an array of all the where clauses for the query.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return array
     */
    protected function compileWheresToArray($query)
    {
        return (new Collection($query->wheres))
            ->map(fn ($where) => $where['boolean'].' '.$this->{"where{$where['type']}"}($query, $where))
            ->all();
    }

    /**
     * Format the where clause statements into one string.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $sql
     * @return string
     */
    protected function concatenateWhereClauses($query, $sql)
    {
        $conjunction = $query instanceof JoinClause ? 'on' : 'where';

        return $conjunction.' '.$this->removeLeadingBoolean(implode(' ', $sql));
    }

    /**
     * Compile a raw where clause.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $where
     * @return string
     */
    protected function whereRaw(Builder $query, $where)
    {
        return $where['sql'] instanceof Expression ? $where['sql']->getValue($this) : $where['sql'];
    }

    /**
     * Compile a basic where clause.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $where
     * @return string
     */
    protected function whereBasic(Builder $query, $where)
    {
        $value = $this->parameter($where['value']);

        $operator = str_replace('?', '??', $where['operator']);

        return $this->wrap($where['column']).' '.$operator.' '.$value;
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
        return $this->whereBasic($query, $where);
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
        if ($where['caseSensitive']) {
            throw new RuntimeException('This database engine does not support case sensitive like operations.');
        }

        $where['operator'] = $where['not'] ? 'not like' : 'like';

        return $this->whereBasic($query, $where);
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
        return $this->wrap($where['column']).' is not distinct from '.$this->parameter($where['value']);
    }

    /**
     * Compile a "where in" clause.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $where
     * @return string
     */
    protected function whereIn(Builder $query, $where)
    {
        if (! empty($where['values'])) {
            return $this->wrap($where['column']).' in ('.$this->parameterize($where['values']).')';
        }

        return '0 = 1';
    }

    /**
     * Compile a "where not in" clause.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $where
     * @return string
     */
    protected function whereNotIn(Builder $query, $where)
    {
        if (! empty($where['values'])) {
            return $this->wrap($where['column']).' not in ('.$this->parameterize($where['values']).')';
        }

        return '1 = 1';
    }

    /**
     * Compile a "where not in raw" clause.
     *
     * For safety, whereIntegerInRaw ensures this method is only used with integer values.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $where
     * @return string
     */
    protected function whereNotInRaw(Builder $query, $where)
    {
        if (! empty($where['values'])) {
            return $this->wrap($where['column']).' not in ('.implode(', ', $where['values']).')';
        }

        return '1 = 1';
    }

    /**
     * Compile a "where in raw" clause.
     *
     * For safety, whereIntegerInRaw ensures this method is only used with integer values.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $where
     * @return string
     */
    protected function whereInRaw(Builder $query, $where)
    {
        if (! empty($where['values'])) {
            return $this->wrap($where['column']).' in ('.implode(', ', $where['values']).')';
        }

        return '0 = 1';
    }

    /**
     * Compile a "where null" clause.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $where
     * @return string
     */
    protected function whereNull(Builder $query, $where)
    {
        return $this->wrap($where['column']).' is null';
    }

    /**
     * Compile a "where not null" clause.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $where
     * @return string
     */
    protected function whereNotNull(Builder $query, $where)
    {
        return $this->wrap($where['column']).' is not null';
    }

    /**
     * Compile a "between" where clause.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $where
     * @return string
     */
    protected function whereBetween(Builder $query, $where)
    {
        $between = $where['not'] ? 'not between' : 'between';

        $min = $this->parameter(is_array($where['values']) ? array_first($where['values']) : $where['values'][0]);

        $max = $this->parameter(is_array($where['values']) ? array_last($where['values']) : $where['values'][1]);

        return $this->wrap($where['column']).' '.$between.' '.$min.' and '.$max;
    }

    /**
     * Compile a "between" where clause.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $where
     * @return string
     */
    protected function whereBetweenColumns(Builder $query, $where)
    {
        $between = $where['not'] ? 'not between' : 'between';

        $min = $this->wrap(is_array($where['values']) ? array_first($where['values']) : $where['values'][0]);

        $max = $this->wrap(is_array($where['values']) ? array_last($where['values']) : $where['values'][1]);

        return $this->wrap($where['column']).' '.$between.' '.$min.' and '.$max;
    }

    /**
     * Compile a "value between" where clause.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $where
     * @return string
     */
    protected function whereValueBetween(Builder $query, $where)
    {
        $between = $where['not'] ? 'not between' : 'between';

        $min = $this->wrap(is_array($where['columns']) ? array_first($where['columns']) : $where['columns'][0]);

        $max = $this->wrap(is_array($where['columns']) ? array_last($where['columns']) : $where['columns'][1]);

        return $this->parameter($where['value']).' '.$between.' '.$min.' and '.$max;
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
        return $this->dateBasedWhere('date', $query, $where);
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
        return $this->dateBasedWhere('time', $query, $where);
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
        return $this->dateBasedWhere('day', $query, $where);
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
        return $this->dateBasedWhere('month', $query, $where);
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
        return $this->dateBasedWhere('year', $query, $where);
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

        return $type.'('.$this->wrap($where['column']).') '.$where['operator'].' '.$value;
    }

    /**
     * Compile a where clause comparing two columns.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $where
     * @return string
     */
    protected function whereColumn(Builder $query, $where)
    {
        return $this->wrap($where['first']).' '.$where['operator'].' '.$this->wrap($where['second']);
    }

    /**
     * Compile a nested where clause.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $where
     * @return string
     */
    protected function whereNested(Builder $query, $where)
    {
        // Here we will calculate what portion of the string we need to remove. If this
        // is a join clause query, we need to remove the "on" portion of the SQL and
        // if it is a normal query we need to take the leading "where" of queries.
        $offset = $where['query'] instanceof JoinClause ? 3 : 6;

        return '('.substr($this->compileWheres($where['query']), $offset).')';
    }

    /**
     * Compile a where condition with a sub-select.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $where
     * @return string
     */
    protected function whereSub(Builder $query, $where)
    {
        $select = $this->compileSelect($where['query']);

        return $this->wrap($where['column']).' '.$where['operator']." ($select)";
    }

    /**
     * Compile a where exists clause.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $where
     * @return string
     */
    protected function whereExists(Builder $query, $where)
    {
        return 'exists ('.$this->compileSelect($where['query']).')';
    }

    /**
     * Compile a where exists clause.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $where
     * @return string
     */
    protected function whereNotExists(Builder $query, $where)
    {
        return 'not exists ('.$this->compileSelect($where['query']).')';
    }

    /**
     * Compile a where row values condition.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $where
     * @return string
     */
    protected function whereRowValues(Builder $query, $where)
    {
        $columns = $this->columnize($where['columns']);

        $values = $this->parameterize($where['values']);

        return '('.$columns.') '.$where['operator'].' ('.$values.')';
    }

    /**
     * Compile a "where JSON boolean" clause.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $where
     * @return string
     */
    protected function whereJsonBoolean(Builder $query, $where)
    {
        $column = $this->wrapJsonBooleanSelector($where['column']);

        $value = $this->wrapJsonBooleanValue(
            $this->parameter($where['value'])
        );

        return $column.' '.$where['operator'].' '.$value;
    }

    /**
     * Compile a "where JSON contains" clause.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $where
     * @return string
     */
    protected function whereJsonContains(Builder $query, $where)
    {
        $not = $where['not'] ? 'not ' : '';

        return $not.$this->compileJsonContains(
            $where['column'],
            $this->parameter($where['value'])
        );
    }

    /**
     * Compile a "JSON contains" statement into SQL.
     *
     * @param  string  $column
     * @param  string  $value
     * @return string
     *
     * @throws \RuntimeException
     */
    protected function compileJsonContains($column, $value)
    {
        throw new RuntimeException('This database engine does not support JSON contains operations.');
    }

    /**
     * Compile a "where JSON overlaps" clause.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $where
     * @return string
     */
    protected function whereJsonOverlaps(Builder $query, $where)
    {
        $not = $where['not'] ? 'not ' : '';

        return $not.$this->compileJsonOverlaps(
            $where['column'],
            $this->parameter($where['value'])
        );
    }

    /**
     * Compile a "JSON overlaps" statement into SQL.
     *
     * @param  string  $column
     * @param  string  $value
     * @return string
     *
     * @throws \RuntimeException
     */
    protected function compileJsonOverlaps($column, $value)
    {
        throw new RuntimeException('This database engine does not support JSON overlaps operations.');
    }

    /**
     * Prepare the binding for a "JSON contains" statement.
     *
     * @param  mixed  $binding
     * @return string
     */
    public function prepareBindingForJsonContains($binding)
    {
        return json_encode($binding, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Compile a "where JSON contains key" clause.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $where
     * @return string
     */
    protected function whereJsonContainsKey(Builder $query, $where)
    {
        $not = $where['not'] ? 'not ' : '';

        return $not.$this->compileJsonContainsKey(
            $where['column']
        );
    }

    /**
     * Compile a "JSON contains key" statement into SQL.
     *
     * @param  string  $column
     * @return string
     *
     * @throws \RuntimeException
     */
    protected function compileJsonContainsKey($column)
    {
        throw new RuntimeException('This database engine does not support JSON contains key operations.');
    }

    /**
     * Compile a "where JSON length" clause.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $where
     * @return string
     */
    protected function whereJsonLength(Builder $query, $where)
    {
        return $this->compileJsonLength(
            $where['column'],
            $where['operator'],
            $this->parameter($where['value'])
        );
    }

    /**
     * Compile a "JSON length" statement into SQL.
     *
     * @param  string  $column
     * @param  string  $operator
     * @param  string  $value
     * @return string
     *
     * @throws \RuntimeException
     */
    protected function compileJsonLength($column, $operator, $value)
    {
        throw new RuntimeException('This database engine does not support JSON length operations.');
    }

    /**
     * Compile a "JSON value cast" statement into SQL.
     *
     * @param  string  $value
     * @return string
     */
    public function compileJsonValueCast($value)
    {
        return $value;
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
        throw new RuntimeException('This database engine does not support fulltext search operations.');
    }

    /**
     * Compile a clause based on an expression.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $where
     * @return string
     */
    public function whereExpression(Builder $query, $where)
    {
        return $where['column']->getValue($this);
    }

    /**
     * Compile the "group by" portions of the query.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $groups
     * @return string
     */
    protected function compileGroups(Builder $query, $groups)
    {
        return 'group by '.$this->columnize($groups);
    }

    /**
     * Compile the "having" portions of the query.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return string
     */
    protected function compileHavings(Builder $query)
    {
        return 'having '.$this->removeLeadingBoolean((new Collection($query->havings))->map(function ($having) {
            return $having['boolean'].' '.$this->compileHaving($having);
        })->implode(' '));
    }

    /**
     * Compile a single having clause.
     *
     * @param  array  $having
     * @return string
     */
    protected function compileHaving(array $having)
    {
        // If the having clause is "raw", we can just return the clause straight away
        // without doing any more processing on it. Otherwise, we will compile the
        // clause into SQL based on the components that make it up from builder.
        return match ($having['type']) {
            'Raw' => $having['sql'],
            'between' => $this->compileHavingBetween($having),
            'Null' => $this->compileHavingNull($having),
            'NotNull' => $this->compileHavingNotNull($having),
            'bit' => $this->compileHavingBit($having),
            'Expression' => $this->compileHavingExpression($having),
            'Nested' => $this->compileNestedHavings($having),
            default => $this->compileBasicHaving($having),
        };
    }

    /**
     * Compile a basic having clause.
     *
     * @param  array  $having
     * @return string
     */
    protected function compileBasicHaving($having)
    {
        $column = $this->wrap($having['column']);

        $parameter = $this->parameter($having['value']);

        return $column.' '.$having['operator'].' '.$parameter;
    }

    /**
     * Compile a "between" having clause.
     *
     * @param  array  $having
     * @return string
     */
    protected function compileHavingBetween($having)
    {
        $between = $having['not'] ? 'not between' : 'between';

        $column = $this->wrap($having['column']);

        $min = $this->parameter(head($having['values']));

        $max = $this->parameter(last($having['values']));

        return $column.' '.$between.' '.$min.' and '.$max;
    }

    /**
     * Compile a having null clause.
     *
     * @param  array  $having
     * @return string
     */
    protected function compileHavingNull($having)
    {
        $column = $this->wrap($having['column']);

        return $column.' is null';
    }

    /**
     * Compile a having not null clause.
     *
     * @param  array  $having
     * @return string
     */
    protected function compileHavingNotNull($having)
    {
        $column = $this->wrap($having['column']);

        return $column.' is not null';
    }

    /**
     * Compile a having clause involving a bit operator.
     *
     * @param  array  $having
     * @return string
     */
    protected function compileHavingBit($having)
    {
        $column = $this->wrap($having['column']);

        $parameter = $this->parameter($having['value']);

        return '('.$column.' '.$having['operator'].' '.$parameter.') != 0';
    }

    /**
     * Compile a having clause involving an expression.
     *
     * @param  array  $having
     * @return string
     */
    protected function compileHavingExpression($having)
    {
        return $having['column']->getValue($this);
    }

    /**
     * Compile a nested having clause.
     *
     * @param  array  $having
     * @return string
     */
    protected function compileNestedHavings($having)
    {
        return '('.substr($this->compileHavings($having['query']), 7).')';
    }

    /**
     * Compile the "order by" portions of the query.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $orders
     * @return string
     */
    protected function compileOrders(Builder $query, $orders)
    {
        if (! empty($orders)) {
            return 'order by '.implode(', ', $this->compileOrdersToArray($query, $orders));
        }

        return '';
    }

    /**
     * Compile the query orders to an array.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $orders
     * @return array
     */
    protected function compileOrdersToArray(Builder $query, $orders)
    {
        return array_map(function ($order) use ($query) {
            if (isset($order['sql']) && $order['sql'] instanceof Expression) {
                return $order['sql']->getValue($query->getGrammar());
            }

            if (isset($order['type']) && $order['type'] === 'InOrderOf') {
                return $this->compileInOrderOf($order);
            }

            return $order['sql'] ?? $this->wrap($order['column']).' '.$order['direction'];
        }, $orders);
    }

    /**
     * Compile an "in order of" clause.
     *
     * @param  array  $order
     * @return string
     */
    protected function compileInOrderOf($order)
    {
        $column = $this->wrap($order['column']);

        $cases = [];

        foreach (array_values($order['values']) as $index => $value) {
            $cases[] = 'when '.$column.' = '.$this->parameter($value).' then '.$index;
        }

        return 'case '.implode(' ', $cases).' else '.count($order['values']).' end';
    }

    /**
     * Compile the random statement into SQL.
     *
     * @param  string|int  $seed
     * @return string
     */
    public function compileRandom($seed)
    {
        return 'RANDOM()';
    }

    /**
     * Compile the "limit" portions of the query.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  int  $limit
     * @return string
     */
    protected function compileLimit(Builder $query, $limit)
    {
        return 'limit '.(int) $limit;
    }

    /**
     * Compile a group limit clause.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return string
     */
    protected function compileGroupLimit(Builder $query)
    {
        $selectBindings = array_merge($query->getRawBindings()['select'], $query->getRawBindings()['order']);

        $query->setBindings($selectBindings, 'select');
        $query->setBindings([], 'order');

        $limit = (int) $query->groupLimit['value'];
        $offset = $query->offset;

        if (isset($offset)) {
            $offset = (int) $offset;
            $limit += $offset;

            $query->offset = null;
        }

        $components = $this->compileComponents($query);

        $components['columns'] .= $this->compileRowNumber(
            $query->groupLimit['column'],
            $components['orders'] ?? ''
        );

        unset($components['orders']);

        $table = $this->wrap('laravel_table');
        $row = $this->wrap('laravel_row');

        $sql = $this->concatenate($components);

        $sql = 'select * from ('.$sql.') as '.$table.' where '.$row.' <= '.$limit;

        if (isset($offset)) {
            $sql .= ' and '.$row.' > '.$offset;
        }

        return $sql.' order by '.$row;
    }

    /**
     * Compile a row number clause.
     *
     * @param  string  $partition
     * @param  string  $orders
     * @return string
     */
    protected function compileRowNumber($partition, $orders)
    {
        $over = trim('partition by '.$this->wrap($partition).' '.$orders);

        return ', row_number() over ('.$over.') as '.$this->wrap('laravel_row');
    }

    /**
     * Compile the "offset" portions of the query.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  int  $offset
     * @return string
     */
    protected function compileOffset(Builder $query, $offset)
    {
        return 'offset '.(int) $offset;
    }

    /**
     * Compile the "union" queries attached to the main query.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return string
     */
    protected function compileUnions(Builder $query)
    {
        $sql = '';

        foreach ($query->unions as $union) {
            $sql .= $this->compileUnion($union);
        }

        if (! empty($query->unionOrders)) {
            $sql .= ' '.$this->compileOrders($query, $query->unionOrders);
        }

        if (isset($query->unionLimit)) {
            $sql .= ' '.$this->compileLimit($query, $query->unionLimit);
        }

        if (isset($query->unionOffset)) {
            $sql .= ' '.$this->compileOffset($query, $query->unionOffset);
        }

        return ltrim($sql);
    }

    /**
     * Compile a single union statement.
     *
     * @param  array  $union
     * @return string
     */
    protected function compileUnion(array $union)
    {
        $conjunction = $union['all'] ? ' union all ' : ' union ';

        return $conjunction.$this->wrapUnion($union['query']->toSql());
    }

    /**
     * Wrap a union subquery in parentheses.
     *
     * @param  string  $sql
     * @return string
     */
    protected function wrapUnion($sql)
    {
        return '('.$sql.')';
    }

    /**
     * Compile a union aggregate query into SQL.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return string
     */
    protected function compileUnionAggregate(Builder $query)
    {
        $sql = $this->compileAggregate($query, $query->aggregate);

        $query->aggregate = null;

        return $sql.' from ('.$this->compileSelect($query).') as '.$this->wrapTable('temp_table');
    }

    /**
     * Compile an exists statement into SQL.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return string
     */
    public function compileExists(Builder $query)
    {
        $select = $this->compileSelect($query);

        return "select exists({$select}) as {$this->wrap('exists')}";
    }

    /**
     * Compile an insert statement into SQL.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $values
     * @return string
     */
    public function compileInsert(Builder $query, array $values)
    {
        // Essentially we will force every insert to be treated as a batch insert which
        // simply makes creating the SQL easier for us since we can utilize the same
        // basic routine regardless of an amount of records given to us to insert.
        $table = $this->wrapTable($query->from);

        if (empty($values)) {
            return "insert into {$table} default values";
        }

        if (! is_array(array_first($values))) {
            $values = [$values];
        }

        $columns = $this->columnize(array_keys(array_first($values)));

        // We need to build a list of parameter place-holders of values that are bound
        // to the query. Each insert should have the exact same number of parameter
        // bindings so we will loop through the record and parameterize them all.
        $parameters = (new Collection($values))
            ->map(fn ($record) => '('.$this->parameterize($record).')')
            ->implode(', ');

        return "insert into $table ($columns) values $parameters";
    }

    /**
     * Compile an insert ignore statement into SQL.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $values
     * @return string
     *
     * @throws \RuntimeException
     */
    public function compileInsertOrIgnore(Builder $query, array $values)
    {
        throw new RuntimeException('This database engine does not support inserting while ignoring errors.');
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
        return $this->compileInsert($query, $values);
    }

    /**
     * Compile an insert statement using a subquery into SQL.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $columns
     * @param  string  $sql
     * @return string
     */
    public function compileInsertUsing(Builder $query, array $columns, string $sql)
    {
        $table = $this->wrapTable($query->from);

        if (empty($columns) || $columns === ['*']) {
            return "insert into {$table} $sql";
        }

        return "insert into {$table} ({$this->columnize($columns)}) $sql";
    }

    /**
     * Compile an insert ignore statement using a subquery into SQL.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $columns
     * @param  string  $sql
     * @return string
     *
     * @throws \RuntimeException
     */
    public function compileInsertOrIgnoreUsing(Builder $query, array $columns, string $sql)
    {
        throw new RuntimeException('This database engine does not support inserting while ignoring errors.');
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
        $table = $this->wrapTable($query->from);

        $columns = $this->compileUpdateColumns($query, $values);

        $where = $this->compileWheres($query);

        return trim(
            isset($query->joins)
                ? $this->compileUpdateWithJoins($query, $table, $columns, $where)
                : $this->compileUpdateWithoutJoins($query, $table, $columns, $where)
        );
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
        return (new Collection($values))
            ->map(fn ($value, $key) => $this->wrap($key).' = '.$this->parameter($value))
            ->implode(', ');
    }

    /**
     * Compile an update statement without joins into SQL.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  string  $table
     * @param  string  $columns
     * @param  string  $where
     * @return string
     */
    protected function compileUpdateWithoutJoins(Builder $query, $table, $columns, $where)
    {
        return "update {$table} set {$columns} {$where}";
    }

    /**
     * Compile an update statement with joins into SQL.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  string  $table
     * @param  string  $columns
     * @param  string  $where
     * @return string
     */
    protected function compileUpdateWithJoins(Builder $query, $table, $columns, $where)
    {
        $joins = $this->compileJoins($query, $query->joins);

        return "update {$table} {$joins} set {$columns} {$where}";
    }

    /**
     * Compile an "upsert" statement into SQL.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $values
     * @param  array  $uniqueBy
     * @param  array  $update
     * @return string
     *
     * @throws \RuntimeException
     */
    public function compileUpsert(Builder $query, array $values, array $uniqueBy, array $update)
    {
        throw new RuntimeException('This database engine does not support upserts.');
    }

    /**
     * Prepare the bindings for an update statement.
     *
     * @param  array  $bindings
     * @param  array  $values
     * @return array
     */
    public function prepareBindingsForUpdate(array $bindings, array $values)
    {
        $cleanBindings = Arr::except($bindings, ['select', 'join']);

        $values = Arr::flatten(array_map(fn ($value) => value($value), $values));

        return array_values(
            array_merge($bindings['join'], $values, Arr::flatten($cleanBindings))
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
        $table = $this->wrapTable($query->from);

        $where = $this->compileWheres($query);

        return trim(
            isset($query->joins)
                ? $this->compileDeleteWithJoins($query, $table, $where)
                : $this->compileDeleteWithoutJoins($query, $table, $where)
        );
    }

    /**
     * Compile a delete statement without joins into SQL.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  string  $table
     * @param  string  $where
     * @return string
     */
    protected function compileDeleteWithoutJoins(Builder $query, $table, $where)
    {
        return "delete from {$table} {$where}";
    }

    /**
     * Compile a delete statement with joins into SQL.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  string  $table
     * @param  string  $where
     * @return string
     */
    protected function compileDeleteWithJoins(Builder $query, $table, $where)
    {
        $alias = last(explode(' as ', $table));

        $joins = $this->compileJoins($query, $query->joins);

        return "delete {$alias} from {$table} {$joins} {$where}";
    }

    /**
     * Prepare the bindings for a delete statement.
     *
     * @param  array  $bindings
     * @return array
     */
    public function prepareBindingsForDelete(array $bindings)
    {
        return Arr::flatten(
            Arr::except($bindings, 'select')
        );
    }

    /**
     * Compile a truncate table statement into SQL.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return array
     */
    public function compileTruncate(Builder $query)
    {
        return ['truncate table '.$this->wrapTable($query->from) => []];
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
        return is_string($value) ? $value : '';
    }

    /**
     * Compile a query to get the number of open connections for a database.
     *
     * @return string|null
     */
    public function compileThreadCount()
    {
        return null;
    }

    /**
     * Determine if the grammar supports savepoints.
     *
     * @return bool
     */
    public function supportsSavepoints()
    {
        return true;
    }

    /**
     * Compile the SQL statement to define a savepoint.
     *
     * @param  string  $name
     * @return string
     */
    public function compileSavepoint($name)
    {
        return 'SAVEPOINT '.$name;
    }

    /**
     * Compile the SQL statement to execute a savepoint rollback.
     *
     * @param  string  $name
     * @return string
     */
    public function compileSavepointRollBack($name)
    {
        return 'ROLLBACK TO SAVEPOINT '.$name;
    }

    /**
     * Wrap the given JSON selector for boolean values.
     *
     * @param  string  $value
     * @return string
     */
    protected function wrapJsonBooleanSelector($value)
    {
        return $this->wrapJsonSelector($value);
    }

    /**
     * Wrap the given JSON boolean value.
     *
     * @param  string  $value
     * @return string
     */
    protected function wrapJsonBooleanValue($value)
    {
        return $value;
    }

    /**
     * Concatenate an array of segments, removing empties.
     *
     * @param  array  $segments
     * @return string
     */
    protected function concatenate($segments)
    {
        return implode(' ', array_filter($segments, function ($value) {
            return (string) $value !== '';
        }));
    }

    /**
     * Remove the leading boolean from a statement.
     *
     * @param  string  $value
     * @return string
     */
    protected function removeLeadingBoolean($value)
    {
        return preg_replace('/and |or /i', '', $value, 1);
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
        $bindings = array_map(fn ($value) => $this->escape($value, is_resource($value) || gettype($value) === 'resource (closed)'), $bindings);

        $query = '';

        $isStringLiteral = false;

        for ($i = 0; $i < strlen($sql); $i++) {
            $char = $sql[$i];
            $nextChar = $sql[$i + 1] ?? null;

            // Single quotes can be escaped as '' according to the SQL standard while
            // MySQL uses \'. Postgres has operators like ?| that must get encoded
            // in PHP like ??|. We should skip over the escaped characters here.
            if (in_array($char.$nextChar, ["\'", "''", '??'])) {
                $query .= $char.$nextChar;
                $i += 1;
            } elseif ($char === "'") { // Starting / leaving string literal...
                $query .= $char;
                $isStringLiteral = ! $isStringLiteral;
            } elseif ($char === '?' && ! $isStringLiteral) { // Substitutable binding...
                $query .= array_shift($bindings) ?? '?';
            } else { // Normal character...
                $query .= $char;
            }
        }

        return $query;
    }

    /**
     * Get the grammar specific operators.
     *
     * @return array
     */
    public function getOperators()
    {
        return $this->operators;
    }

    /**
     * Get the grammar specific bitwise operators.
     *
     * @return array
     */
    public function getBitwiseOperators()
    {
        return $this->bitwiseOperators;
    }
}
