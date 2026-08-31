<?php

namespace Illuminate\Database\Query;

use BackedEnum;
use Closure;
use DatePeriod;
use DateTimeInterface;
use Illuminate\Contracts\Database\Query\Builder as BuilderContract;
use Illuminate\Contracts\Database\Query\ConditionExpression;
use Illuminate\Contracts\Database\Query\Expression as ExpressionContract;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Concerns\BuildsQueries;
use Illuminate\Database\Concerns\BuildsWhereDateClauses;
use Illuminate\Database\Concerns\ExplainsQueries;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\PostgresConnection;
use Illuminate\Database\Query\Grammars\Grammar;
use Illuminate\Database\Query\Processors\Processor;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\ForwardsCalls;
use Illuminate\Support\Traits\Macroable;
use InvalidArgumentException;
use LogicException;
use RuntimeException;
use UnitEnum;

use function Illuminate\Support\enum_value;

class Builder implements BuilderContract
{
    /** @use \Illuminate\Database\Concerns\BuildsQueries<\stdClass> */
    use BuildsWhereDateClauses, BuildsQueries, ExplainsQueries, ForwardsCalls, Macroable {
        __call as macroCall;
    }

    /**
     * The database connection instance.
     *
     * @var \Illuminate\Database\ConnectionInterface
     */
    public $connection;

    /**
     * The database query grammar instance.
     *
     * @var \Illuminate\Database\Query\Grammars\Grammar
     */
    public $grammar;

    /**
     * The database query post processor instance.
     *
     * @var \Illuminate\Database\Query\Processors\Processor
     */
    public $processor;

    /**
     * The current query value bindings.
     *
     * @var array{
     *     select: list<mixed>,
     *     from: list<mixed>,
     *     join: list<mixed>,
     *     where: list<mixed>,
     *     groupBy: list<mixed>,
     *     having: list<mixed>,
     *     order: list<mixed>,
     *     union: list<mixed>,
     *     unionOrder: list<mixed>,
     * }
     */
    public $bindings = [
        'select' => [],
        'from' => [],
        'join' => [],
        'where' => [],
        'groupBy' => [],
        'having' => [],
        'order' => [],
        'union' => [],
        'unionOrder' => [],
    ];

    /**
     * An aggregate function and column to be run.
     *
     * @var array{
     *     function: string,
     *     columns: array<\Illuminate\Contracts\Database\Query\Expression|string>
     * }|null
     */
    public $aggregate;

    /**
     * The columns that should be returned.
     *
     * @var array<string|\Illuminate\Contracts\Database\Query\Expression>|null
     */
    public $columns;

    /**
     * Indicates if the query returns distinct results.
     *
     * Occasionally contains the columns that should be distinct.
     *
     * @var bool|array
     */
    public $distinct = false;

    /**
     * The table which the query is targeting.
     *
     * @var \Illuminate\Database\Query\Expression|string
     */
    public $from;

    /**
     * The index hint for the query.
     *
     * @var \Illuminate\Database\Query\IndexHint|null
     */
    public $indexHint;

    /**
     * The table joins for the query.
     *
     * @var array|null
     */
    public $joins;

    /**
     * The where constraints for the query.
     *
     * @var array
     */
    public $wheres = [];

    /**
     * The groupings for the query.
     *
     * @var array|null
     */
    public $groups;

    /**
     * The having constraints for the query.
     *
     * @var array|null
     */
    public $havings;

    /**
     * The orderings for the query.
     *
     * @var array|null
     */
    public $orders;

    /**
     * The maximum number of records to return.
     *
     * @var int|null
     */
    public $limit;

    /**
     * The maximum number of records to return per group.
     *
     * @var array|null
     */
    public $groupLimit;

    /**
     * The number of records to skip.
     *
     * @var int|null
     */
    public $offset;

    /**
     * The query union statements.
     *
     * @var array|null
     */
    public $unions;

    /**
     * The maximum number of union records to return.
     *
     * @var int|null
     */
    public $unionLimit;

    /**
     * The number of union records to skip.
     *
     * @var int|null
     */
    public $unionOffset;

    /**
     * The orderings for the union query.
     *
     * @var array|null
     */
    public $unionOrders;

    /**
     * Indicates whether row locking is being used.
     *
     * @var string|bool|null
     */
    public $lock;

    /**
     * The query execution timeout in seconds.
     *
     * @var int|null
     */
    public $timeout;

    /**
     * The callbacks that should be invoked before the query is executed.
     *
     * @var array
     */
    public $beforeQueryCallbacks = [];

    /**
     * The callbacks that should be invoked after retrieving data from the database.
     *
     * @var array
     */
    protected $afterQueryCallbacks = [];

    /**
     * All of the available clause operators.
     *
     * @var string[]
     */
    public $operators = [
        '=', '<', '>', '<=', '>=', '<>', '!=', '<=>',
        'like', 'like binary', 'not like', 'ilike',
        '&', '|', '^', '<<', '>>', '&~', 'is', 'is not',
        'rlike', 'not rlike', 'regexp', 'not regexp',
        '~', '~*', '!~', '!~*', 'similar to',
        'not similar to', 'not ilike', '~~*', '!~~*',
    ];

    /**
     * All of the available bitwise operators.
     *
     * @var string[]
     */
    public $bitwiseOperators = [
        '&', '|', '^', '<<', '>>', '&~',
    ];

    /**
     * Whether to use write pdo for the select.
     *
     * @var bool
     */
    public $useWritePdo = false;

    /**
     * Create a new query builder instance.
     */
    public function __construct(
        ConnectionInterface $connection,
        ?Grammar $grammar = null,
        ?Processor $processor = null,
    ) {
        $this->connection = $connection;
        $this->grammar = $grammar ?: $connection->getQueryGrammar();
        $this->processor = $processor ?: $connection->getPostProcessor();
    }

    /**
     * Set the columns to be selected.
     *
     * @param  mixed  $columns
     * @return $this
     */
    public function select($columns = ['*'])
    {
        $this->columns = [];
        $this->bindings['select'] = [];

        $columns = is_array($columns) ? $columns : func_get_args();

        foreach ($columns as $as => $column) {
            if (is_string($as) && $this->isQueryable($column)) {
                $this->selectSub($column, $as);
            } else {
                $this->columns[] = $column;
            }
        }

        return $this;
    }

    /**
     * Add a subselect expression to the query.
     *
     * @param  \Closure|\Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder<*>|string  $query
     * @param  string  $as
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function selectSub($query, $as)
    {
        [$query, $bindings] = $this->createSub($query);

        return $this->selectRaw(
            '('.$query.') as '.$this->grammar->wrap($as), $bindings
        );
    }

    /**
     * Add a select expression to the query.
     *
     * @param  \Illuminate\Contracts\Database\Query\Expression|string  $expression
     * @param  string  $as
     * @return $this
     */
    public function selectExpression($expression, $as)
    {
        return $this->selectRaw(
            '('.$this->grammar->getValue($expression).') as '.$this->grammar->wrap($as)
        );
    }

    /**
     * Add a new "raw" select expression to the query.
     *
     * @param  string  $expression
     * @return $this
     */
    public function selectRaw($expression, array $bindings = [])
    {
        $this->addSelect(new Expression($expression));

        if ($bindings) {
            $this->addBinding($bindings, 'select');
        }

        return $this;
    }

    /**
     * Makes "from" fetch from a subquery.
     *
     * @param  \Closure|\Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder<*>|string  $query
     * @param  string  $as
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function fromSub($query, $as)
    {
        [$query, $bindings] = $this->createSub($query);

        return $this->fromRaw('('.$query.') as '.$this->grammar->wrapTable($as), $bindings);
    }

    /**
     * Add a raw "from" clause to the query.
     *
     * @param  string  $expression
     * @param  mixed  $bindings
     * @return $this
     */
    public function fromRaw($expression, $bindings = [])
    {
        $this->from = new Expression($expression);

        $this->addBinding($bindings, 'from');

        return $this;
    }

    /**
     * Creates a subquery and parse it.
     *
     * @param  \Closure|\Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder<*>|string  $query
     * @return array
     */
    protected function createSub($query)
    {
        // If the given query is a Closure, we will execute it while passing in a new
        // query instance to the Closure. This will give the developer a chance to
        // format and work with the query before we cast it to a raw SQL string.
        if ($query instanceof Closure) {
            $callback = $query;

            $callback($query = $this->forSubQuery());
        }

        return $this->parseSub($query);
    }

    /**
     * Parse the subquery into SQL and bindings.
     *
     * @param  mixed  $query
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    protected function parseSub($query)
    {
        if ($query instanceof self || $query instanceof EloquentBuilder || $query instanceof Relation) {
            $query = $this->prependDatabaseNameIfCrossDatabaseQuery($query);

            return [$query->toSql(), $query->getBindings()];
        } elseif (is_string($query)) {
            return [$query, []];
        } else {
            throw new InvalidArgumentException(
                'A subquery must be a query builder instance, a Closure, or a string.'
            );
        }
    }

    /**
     * Prepend the database name if the given query is on another database.
     *
     * @param  mixed  $query
     * @return mixed
     */
    protected function prependDatabaseNameIfCrossDatabaseQuery($query)
    {
        if ($query->getConnection()->getDatabaseName() !==
            $this->getConnection()->getDatabaseName()) {
            $databaseName = $query->getConnection()->getDatabaseName();

            if (! str_starts_with($query->from, $databaseName) && ! str_contains($query->from, '.')) {
                $query->from($databaseName.'.'.$query->from);
            }
        }

        return $query;
    }

    /**
     * Add a new select column to the query.
     *
     * @param  mixed  $column
     * @return $this
     */
    public function addSelect($column)
    {
        $columns = is_array($column) ? $column : func_get_args();

        foreach ($columns as $as => $column) {
            if (is_string($as) && $this->isQueryable($column)) {
                if (is_null($this->columns)) {
                    $this->select($this->from.'.*');
                }

                $this->selectSub($column, $as);
            } else {
                if (is_array($this->columns) && in_array($column, $this->columns, true)) {
                    continue;
                }

                $this->columns[] = $column;
            }
        }

        return $this;
    }

    /**
     * Add a vector-similarity selection to the query.
     *
     * @param  \Illuminate\Contracts\Database\Query\Expression|string  $column
     * @param  \Illuminate\Support\Collection<int, float>|\Illuminate\Contracts\Support\Arrayable|array<int, float>|string  $vector
     * @param  string|null  $as
     * @return $this
     */
    public function selectVectorDistance($column, $vector, $as = null)
    {
        $this->ensureConnectionSupportsVectors();

        if (is_string($vector)) {
            $vector = Str::of($vector)->toEmbeddings(cache: true);
        }

        $this->addBinding(
            json_encode(
                $vector instanceof Arrayable
                    ? $vector->toArray()
                    : $vector,
                flags: JSON_THROW_ON_ERROR
            ),
            'select',
        );

        $as = $this->getGrammar()->wrap($as ?? $column.'_distance');

        return $this->addSelect(
            new Expression("({$this->getGrammar()->wrap($column)} <=> ?) as {$as}")
        );
    }

    /**
     * Force the query to only return distinct results.
     *
     * @return $this
     */
    public function distinct()
    {
        $columns = func_get_args();

        if (count($columns) > 0) {
            $this->distinct = is_array($columns[0]) || is_bool($columns[0]) ? $columns[0] : $columns;
        } else {
            $this->distinct = true;
        }

        return $this;
    }

    /**
     * Set the table which the query is targeting.
     *
     * @param  \Closure|\Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder<*>|\Illuminate\Contracts\Database\Query\Expression|string  $table
     * @param  string|null  $as
     * @return $this
     */
    public function from($table, $as = null)
    {
        if ($this->isQueryable($table)) {
            return $this->fromSub($table, $as);
        }

        $this->from = $as ? "{$table} as {$as}" : $table;

        return $this;
    }

    /**
     * Add an index hint to suggest a query index.
     *
     * @param  string  $index
     * @return $this
     */
    public function useIndex($index)
    {
        $this->indexHint = new IndexHint('hint', $index);

        return $this;
    }

    /**
     * Add an index hint to force a query index.
     *
     * @param  string  $index
     * @return $this
     */
    public function forceIndex($index)
    {
        $this->indexHint = new IndexHint('force', $index);

        return $this;
    }

    /**
     * Add an index hint to ignore a query index.
     *
     * @param  string  $index
     * @return $this
     */
    public function ignoreIndex($index)
    {
        $this->indexHint = new IndexHint('ignore', $index);

        return $this;
    }

    /**
     * Add a "join" clause to the query.
     *
     * @param  \Illuminate\Contracts\Database\Query\Expression|string  $table
     * @param  \Closure|\Illuminate\Contracts\Database\Query\Expression|string  $first
     * @param  string|null  $operator
     * @param  \Illuminate\Contracts\Database\Query\Expression|string|null  $second
     * @param  string  $type
     * @param  bool  $where
     * @return $this
     */
    public function join($table, $first, $operator = null, $second = null, $type = 'inner', $where = false)
    {
        $join = $this->newJoinClause($this, $type, $table);

        // If the first "column" of the join is really a Closure instance the developer
        // is trying to build a join with a complex "on" clause containing more than
        // one condition, so we'll add the join and call a Closure with the query.
        if ($first instanceof Closure) {
            $first($join);

            $this->joins[] = $join;

            $this->addBinding($join->getBindings(), 'join');
        }

        // If the column is simply a string, we can assume the join simply has a basic
        // "on" clause with a single condition. So we will just build the join with
        // this simple join clauses attached to it. There is not a join callback.
        else {
            $method = $where ? 'where' : 'on';

            $this->joins[] = $join->$method($first, $operator, $second);

            $this->addBinding($join->getBindings(), 'join');
        }

        return $this;
    }

    /**
     * Add a "join where" clause to the query.
     *
     * @param  \Illuminate\Contracts\Database\Query\Expression|string  $table
     * @param  \Closure|\Illuminate\Contracts\Database\Query\Expression|string  $first
     * @param  string  $operator
     * @param  \Illuminate\Contracts\Database\Query\Expression|string  $second
     * @param  string  $type
     * @return $this
     */
    public function joinWhere($table, $first, $operator, $second, $type = 'inner')
    {
        return $this->join($table, $first, $operator, $second, $type, true);
    }

    /**
     * Add a "subquery join" clause to the query.
     *
     * @param  \Closure|\Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder<*>|string  $query
     * @param  string  $as
     * @param  \Closure|\Illuminate\Contracts\Database\Query\Expression|string  $first
     * @param  string|null  $operator
     * @param  \Illuminate\Contracts\Database\Query\Expression|string|null  $second
     * @param  string  $type
     * @param  bool  $where
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function joinSub($query, $as, $first, $operator = null, $second = null, $type = 'inner', $where = false)
    {
        [$query, $bindings] = $this->createSub($query);

        $expression = '('.$query.') as '.$this->grammar->wrapTable($as);

        $this->addBinding($bindings, 'join');

        return $this->join(new Expression($expression), $first, $operator, $second, $type, $where);
    }

    /**
     * Add a "lateral join" clause to the query.
     *
     * @param  \Closure|\Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder<*>|string  $query
     * @return $this
     */
    public function joinLateral($query, string $as, string $type = 'inner')
    {
        [$query, $bindings] = $this->createSub($query);

        $expression = '('.$query.') as '.$this->grammar->wrapTable($as);

        $this->addBinding($bindings, 'join');

        $this->joins[] = $this->newJoinLateralClause($this, $type, new Expression($expression));

        return $this;
    }

    /**
     * Add a lateral left join to the query.
     *
     * @param  \Closure|\Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder<*>|string  $query
     * @return $this
     */
    public function leftJoinLateral($query, string $as)
    {
        return $this->joinLateral($query, $as, 'left');
    }

    /**
     * Add a left join to the query.
     *
     * @param  \Illuminate\Contracts\Database\Query\Expression|string  $table
     * @param  \Closure|\Illuminate\Contracts\Database\Query\Expression|string  $first
     * @param  string|null  $operator
     * @param  \Illuminate\Contracts\Database\Query\Expression|string|null  $second
     * @return $this
     */
    public function leftJoin($table, $first, $operator = null, $second = null)
    {
        return $this->join($table, $first, $operator, $second, 'left');
    }

    /**
     * Add a "join where" clause to the query.
     *
     * @param  \Illuminate\Contracts\Database\Query\Expression|string  $table
     * @param  \Closure|\Illuminate\Contracts\Database\Query\Expression|string  $first
     * @param  string  $operator
     * @param  \Illuminate\Contracts\Database\Query\Expression|string|null  $second
     * @return $this
     */
    public function leftJoinWhere($table, $first, $operator, $second)
    {
        return $this->joinWhere($table, $first, $operator, $second, 'left');
    }

    /**
     * Add a subquery left join to the query.
     *
     * @param  \Closure|\Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder<*>|string  $query
     * @param  string  $as
     * @param  \Closure|\Illuminate\Contracts\Database\Query\Expression|string  $first
     * @param  string|null  $operator
     * @param  \Illuminate\Contracts\Database\Query\Expression|string|null  $second
     * @return $this
     */
    public function leftJoinSub($query, $as, $first, $operator = null, $second = null)
    {
        return $this->joinSub($query, $as, $first, $operator, $second, 'left');
    }

    /**
     * Add a right join to the query.
     *
     * @param  \Illuminate\Contracts\Database\Query\Expression|string  $table
     * @param  \Closure|string  $first
     * @param  string|null  $operator
     * @param  \Illuminate\Contracts\Database\Query\Expression|string|null  $second
     * @return $this
     */
    public function rightJoin($table, $first, $operator = null, $second = null)
    {
        return $this->join($table, $first, $operator, $second, 'right');
    }

    /**
     * Add a "right join where" clause to the query.
     *
     * @param  \Illuminate\Contracts\Database\Query\Expression|string  $table
     * @param  \Closure|\Illuminate\Contracts\Database\Query\Expression|string  $first
     * @param  string  $operator
     * @param  \Illuminate\Contracts\Database\Query\Expression|string  $second
     * @return $this
     */
    public function rightJoinWhere($table, $first, $operator, $second)
    {
        return $this->joinWhere($table, $first, $operator, $second, 'right');
    }

    /**
     * Add a subquery right join to the query.
     *
     * @param  \Closure|\Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder<*>|string  $query
     * @param  string  $as
     * @param  \Closure|\Illuminate\Contracts\Database\Query\Expression|string  $first
     * @param  string|null  $operator
     * @param  \Illuminate\Contracts\Database\Query\Expression|string|null  $second
     * @return $this
     */
    public function rightJoinSub($query, $as, $first, $operator = null, $second = null)
    {
        return $this->joinSub($query, $as, $first, $operator, $second, 'right');
    }

    /**
     * Add a "cross join" clause to the query.
     *
     * @param  \Illuminate\Contracts\Database\Query\Expression|string  $table
     * @param  \Closure|\Illuminate\Contracts\Database\Query\Expression|string|null  $first
     * @param  string|null  $operator
     * @param  \Illuminate\Contracts\Database\Query\Expression|string|null  $second
     * @return $this
     */
    public function crossJoin($table, $first = null, $operator = null, $second = null)
    {
        if ($first) {
            return $this->join($table, $first, $operator, $second, 'cross');
        }

        $this->joins[] = $this->newJoinClause($this, 'cross', $table);

        return $this;
    }

    /**
     * Add a subquery cross join to the query.
     *
     * @param  \Closure|\Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder<*>|string  $query
     * @param  string  $as
     * @return $this
     */
    public function crossJoinSub($query, $as)
    {
        [$query, $bindings] = $this->createSub($query);

        $expression = '('.$query.') as '.$this->grammar->wrapTable($as);

        $this->addBinding($bindings, 'join');

        $this->joins[] = $this->newJoinClause($this, 'cross', new Expression($expression));

        return $this;
    }

    /**
     * Get a new "join" clause.
     *
     * @param  string  $type
     * @param  \Illuminate\Contracts\Database\Query\Expression|string  $table
     * @return \Illuminate\Database\Query\JoinClause
     */
    protected function newJoinClause(self $parentQuery, $type, $table)
    {
        return new JoinClause($parentQuery, $type, $table);
    }

    /**
     * Get a new "join lateral" clause.
     *
     * @param  string  $type
     * @param  \Illuminate\Contracts\Database\Query\Expression|string  $table
     * @return \Illuminate\Database\Query\JoinLateralClause
     */
    protected function newJoinLateralClause(self $parentQuery, $type, $table)
    {
        return new JoinLateralClause($parentQuery, $type, $table);
    }

    /**
     * Merge an array of "where" clauses and bindings.
     *
     * @param  array  $wheres
     * @param  array  $bindings
     * @return $this
     */
    public function mergeWheres($wheres, $bindings)
    {
        $this->wheres = array_merge($this->wheres, (array) $wheres);

        $this->bindings['where'] = array_values(
            array_merge($this->bindings['where'], (array) $bindings)
        );

        return $this;
    }

    /**
     * Add a basic "where" clause to the query.
     *
     * @param  \Closure|string|array|\Illuminate\Contracts\Database\Query\Expression  $column
     * @param  mixed  $operator
     * @param  mixed  $value
     * @param  string  $boolean
     * @return $this
     */
    public function where($column, $operator = null, $value = null, $boolean = 'and')
    {
        if ($column instanceof ConditionExpression) {
            $type = 'Expression';

            $this->wheres[] = compact('type', 'column', 'boolean');

            return $this;
        }

        // If the column is an array, we will assume it is an array of key-value pairs
        // and can add them each as a where clause. We will maintain the boolean we
        // received when the method was called and pass it into the nested where.
        if (is_array($column)) {
            return $this->addArrayOfWheres($column, $boolean);
        }

        // Here we will make some assumptions about the operator. If only 2 values are
        // passed to the method, we will assume that the operator is an equals sign
        // and keep going. Otherwise, we'll require the operator to be passed in.
        [$value, $operator] = $this->prepareValueAndOperator(
            $value, $operator, func_num_args() === 2
        );

        // If the column is actually a Closure instance, we will assume the developer
        // wants to begin a nested where statement which is wrapped in parentheses.
        // We will add that Closure to the query and return back out immediately.
        if ($column instanceof Closure && is_null($operator)) {
            return $this->whereNested($column, $boolean);
        }

        // If the column is a Closure instance and there is an operator value, we will
        // assume the developer wants to run a subquery and then compare the result
        // of that subquery with the given value that was provided to the method.
        if ($this->isQueryable($column) && ! is_null($operator)) {
            [$sub, $bindings] = $this->createSub($column);

            return $this->addBinding($bindings, 'where')
                ->where(new Expression('('.$sub.')'), $operator, $value, $boolean);
        }

        // If the given operator is not found in the list of valid operators we will
        // assume that the developer is just short-cutting the '=' operators and
        // we will set the operators to '=' and set the values appropriately.
        if ($this->invalidOperator($operator)) {
            [$value, $operator] = [$operator, '='];
        }

        // If the value is a Closure, it means the developer is performing an entire
        // sub-select within the query and we will need to compile the sub-select
        // within the where clause to get the appropriate query record results.
        if ($this->isQueryable($value)) {
            return $this->whereSub($column, $operator, $value, $boolean);
        }

        // If the value is "null", we will just assume the developer wants to add a
        // where null clause to the query. So, we will allow a short-cut here to
        // that method for convenience so the developer doesn't have to check.
        if (is_null($value)) {
            return $this->whereNull($column, $boolean, ! in_array($operator, ['=', '<=>'], true));
        }

        $type = 'Basic';

        $columnString = ($column instanceof ExpressionContract)
            ? $this->grammar->getValue($column)
            : $column;

        // If the column is making a JSON reference we'll check to see if the value
        // is a boolean. If it is, we'll add the raw boolean string as an actual
        // value to the query to ensure this is properly handled by the query.
        if (str_contains($columnString, '->') && is_bool($value)) {
            $value = new Expression($value ? 'true' : 'false');

            if (is_string($column)) {
                $type = 'JsonBoolean';
            }
        }

        if ($this->isBitwiseOperator($operator)) {
            $type = 'Bitwise';
        }

        if ($operator === '<=>') {
            $type = 'NullSafeEquals';
        }

        // Now that we are working with just a simple query we can put the elements
        // in our array and add the query binding to our array of bindings that
        // will be bound to each SQL statements when it is finally executed.
        $this->wheres[] = compact(
            'type', 'column', 'operator', 'value', 'boolean'
        );

        if (! $value instanceof ExpressionContract) {
            $this->addBinding($this->flattenValue($value), 'where');
        }

        return $this;
    }

    /**
     * Add an array of "where" clauses to the query.
     *
     * @param  array  $column
     * @param  string  $boolean
     * @param  string  $method
     * @return $this
     */
    protected function addArrayOfWheres($column, $boolean, $method = 'where')
    {
        return $this->whereNested(function ($query) use ($column, $method, $boolean) {
            foreach ($column as $key => $value) {
                if (is_numeric($key) && is_array($value)) {
                    $query->{$method}(...array_values($value), boolean: $boolean);
                } else {
                    $query->{$method}($key, '=', $value, $boolean);
                }
            }
        }, $boolean);
    }

    /**
     * Prepare the value and operator for a where clause.
     *
     * @param  string  $value
     * @param  string  $operator
     * @param  bool  $useDefault
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    public function prepareValueAndOperator($value, $operator, $useDefault = false)
    {
        if ($useDefault) {
            return [$operator, '='];
        } elseif ($this->invalidOperatorAndValue($operator, $value)) {
            throw new InvalidArgumentException('Illegal operator and value combination.');
        }

        return [$value, $operator];
    }

    /**
     * Determine if the given operator and value combination is legal.
     *
     * Prevents using Null values with invalid operators.
     *
     * @param  string  $operator
     * @param  mixed  $value
     * @return bool
     */
    protected function invalidOperatorAndValue($operator, $value)
    {
        return is_null($value) && in_array($operator, $this->operators) &&
             ! in_array($operator, ['=', '<=>', '<>', '!=']);
    }

    /**
     * Determine if the given operator is supported.
     *
     * @param  string  $operator
     * @return bool
     */
    protected function invalidOperator($operator)
    {
        return ! is_string($operator) || (! in_array(strtolower($operator), $this->operators, true) &&
               ! in_array(strtolower($operator), $this->grammar->getOperators(), true));
    }

    /**
     * Determine if the operator is a bitwise operator.
     *
     * @param  string  $operator
     * @return bool
     */
    protected function isBitwiseOperator($operator)
    {
        return in_array(strtolower($operator), $this->bitwiseOperators, true) ||
               in_array(strtolower($operator), $this->grammar->getBitwiseOperators(), true);
    }

    /**
     * Add an "or where" clause to the query.
     *
     * @param  \Closure|string|array|\Illuminate\Contracts\Database\Query\Expression  $column
     * @param  mixed  $operator
     * @param  mixed  $value
     * @return $this
     */
    public function orWhere($column, $operator = null, $value = null)
    {
        [$value, $operator] = $this->prepareValueAndOperator(
            $value, $operator, func_num_args() === 2
        );

        return $this->where($column, $operator, $value, 'or');
    }

    /**
     * Add a basic "where not" clause to the query.
     *
     * @param  \Closure|string|array|\Illuminate\Contracts\Database\Query\Expression  $column
     * @param  mixed  $operator
     * @param  mixed  $value
     * @param  string  $boolean
     * @return $this
     */
    public function whereNot($column, $operator = null, $value = null, $boolean = 'and')
    {
        if (is_array($column)) {
            return $this->whereNested(function ($query) use ($column, $operator, $value, $boolean) {
                $query->where($column, $operator, $value, $boolean);
            }, $boolean.' not');
        }

        return $this->where($column, $operator, $value, $boolean.' not');
    }

    /**
     * Add an "or where not" clause to the query.
     *
     * @param  \Closure|string|array|\Illuminate\Contracts\Database\Query\Expression  $column
     * @param  mixed  $operator
     * @param  mixed  $value
     * @return $this
     */
    public function orWhereNot($column, $operator = null, $value = null)
    {
        return $this->whereNot($column, $operator, $value, 'or');
    }

    /**
     * Add a "where" clause comparing two columns to the query.
     *
     * @param  \Illuminate\Contracts\Database\Query\Expression|string|array  $first
     * @param  string|null  $operator
     * @param  string|null  $second
     * @param  string|null  $boolean
     * @return $this
     */
    public function whereColumn($first, $operator = null, $second = null, $boolean = 'and')
    {
        // If the column is an array, we will assume it is an array of key-value pairs
        // and can add them each as a where clause. We will maintain the boolean we
        // received when the method was called and pass it into the nested where.
        if (is_array($first)) {
            return $this->addArrayOfWheres($first, $boolean, 'whereColumn');
        }

        // If the given operator is not found in the list of valid operators we will
        // assume that the developer is just short-cutting the '=' operators and
        // we will set the operators to '=' and set the values appropriately.
        if ($this->invalidOperator($operator)) {
            [$second, $operator] = [$operator, '='];
        }

        // Finally, we will add this where clause into this array of clauses that we
        // are building for the query. All of them will be compiled via a grammar
        // once the query is about to be executed and run against the database.
        $type = 'Column';

        $this->wheres[] = compact(
            'type', 'first', 'operator', 'second', 'boolean'
        );

        return $this;
    }

    /**
     * Add an "or where" clause comparing two columns to the query.
     *
     * @param  \Illuminate\Contracts\Database\Query\Expression|string|array  $first
     * @param  string|null  $operator
     * @param  string|null  $second
     * @return $this
     */
    public function orWhereColumn($first, $operator = null, $second = null)
    {
        return $this->whereColumn($first, $operator, $second, 'or');
    }

    /**
     * Add a vector similarity clause to the query, filtering by minimum similarity and ordering by similarity.
     *
     * @param  \Illuminate\Contracts\Database\Query\Expression|string  $column
     * @param  \Illuminate\Support\Collection<int, float>|\Illuminate\Contracts\Support\Arrayable|array<int, float>|string  $vector
     * @param  float  $minSimilarity  A value between 0.0 and 1.0, where 1.0 is identical.
     * @param  bool  $order
     * @return $this
     */
    public function whereVectorSimilarTo($column, $vector, $minSimilarity = 0.6, $order = true)
    {
        if (is_string($vector)) {
            $vector = Str::of($vector)->toEmbeddings(cache: true);
        }

        $this->whereVectorDistanceLessThan($column, $vector, 1 - $minSimilarity);

        if ($order) {
            $this->orderByVectorDistance($column, $vector);
        }

        return $this;
    }

    /**
     * Add a vector distance "where" clause to the query.
     *
     * @param  \Illuminate\Contracts\Database\Query\Expression|string  $column
     * @param  \Illuminate\Support\Collection<int, float>|\Illuminate\Contracts\Support\Arrayable|array<int, float>|string  $vector
     * @param  float  $maxDistance
     * @param  string  $boolean
     * @return $this
     */
    public function whereVectorDistanceLessThan($column, $vector, $maxDistance, $boolean = 'and')
    {
        $this->ensureConnectionSupportsVectors();

        if (is_string($vector)) {
            $vector = Str::of($vector)->toEmbeddings(cache: true);
        }

        return $this->whereRaw(
            "({$this->getGrammar()->wrap($column)} <=> ?) <= ?",
            [
                json_encode(
                    $vector instanceof Arrayable
                        ? $vector->toArray()
                        : $vector,
                    flags: JSON_THROW_ON_ERROR
                ),
                $maxDistance,
            ],
            $boolean
        );
    }

    /**
     * Add a vector distance "or where" clause to the query.
     *
     * @param  \Illuminate\Contracts\Database\Query\Expression|string  $column
     * @param  \Illuminate\Support\Collection<int, float>|\Illuminate\Contracts\Support\Arrayable|array<int, float>|string  $vector
     * @param  float  $maxDistance
     * @return $this
     */
    public function orWhereVectorDistanceLessThan($column, $vector, $maxDistance)
    {
        return $this->whereVectorDistanceLessThan($column, $vector, $maxDistance, 'or');
    }

    /**
     * Add a raw "where" clause to the query.
     *
     * @param  \Illuminate\Contracts\Database\Query\Expression|string  $sql
     * @param  mixed  $bindings
     * @param  string  $boolean
     * @return $this
     */
    public function whereRaw($sql, $bindings = [], $boolean = 'and')
    {
        $this->wheres[] = ['type' => 'raw', 'sql' => $sql, 'boolean' => $boolean];

        $this->addBinding((array) $bindings, 'where');

        return $this;
    }

    /**
     * Add a raw "or where" clause to the query.
     *
     * @param  string  $sql
     * @param  mixed  $bindings
     * @return $this
     */
    public function orWhereRaw($sql, $bindings = [])
    {
        return $this->whereRaw($sql, $bindings, 'or');
    }

    /**
     * Add a "where like" clause to the query.
     *
     * @param  \Illuminate\Contracts\Database\Query\Expression|string  $column
     * @param  string  $value
     * @param  bool  $caseSensitive
     * @param  string  $boolean
     * @param  bool  $not
     * @return $this
     */
    public function whereLike($column, $value, $caseSensitive = false, $boolean = 'and', $not = false)
    {
        $type = 'Like';

        $this->wheres[] = compact('type', 'column', 'value', 'caseSensitive', 'boolean', 'not');

        if (method_exists($this->grammar, 'prepareWhereLikeBinding')) {
            $value = $this->grammar->prepareWhereLikeBinding($value, $caseSensitive);
        }

        $this->addBinding($value);

        return $this;
    }

    /**
     * Add an "or where like" clause to the query.
     *
     * @param  \Illuminate\Contracts\Database\Query\Expression|string  $column
     * @param  string  $value
     * @param  bool  $caseSensitive
     * @return $this
     */
    public function orWhereLike($column, $value, $caseSensitive = false)
    {
        return $this->whereLike($column, $value, $caseSensitive, 'or', false);
    }

    /**
     * Add a "where not like" clause to the query.
     *
     * @param  \Illuminate\Contracts\Database\Query\Expression|string  $column
     * @param  string  $value
     * @param  bool  $caseSensitive
     * @param  string  $boolean
     * @return $this
     */
    public function whereNotLike($column, $value, $caseSensitive = false, $boolean = 'and')
    {
        return $this->whereLike($column, $value, $caseSensitive, $boolean, true);
    }

    /**
     * Add an "or where not like" clause to the query.
     *
     * @param  \Illuminate\Contracts\Database\Query\Expression|string  $column
     * @param  string  $value
     * @param  bool  $caseSensitive
     * @return $this
     */
    public function orWhereNotLike($column, $value, $caseSensitive = false)
    {
        return $this->whereNotLike($column, $value, $caseSensitive, 'or');
    }

    /**
     * Add a "where null safe equals" clause to the query.
     *
     * @param  \Illuminate\Contracts\Database\Query\Expression|string  $column
     * @param  mixed  $value
     * @param  string  $boolean
     * @return $this
     */
    public function whereNullSafeEquals($column, $value, $boolean = 'and')
    {
        $type = 'NullSafeEquals';

        $this->wheres[] = compact('type', 'column', 'value', 'boolean');

        if (! $value instanceof ExpressionContract) {
            $this->addBinding($this->flattenValue($value), 'where');
        }

        return $this;
    }

    /**
     * Add an "or where null safe equals" clause to the query.
     *
     * @param  \Illuminate\Contracts\Database\Query\Expression|string  $column
     * @param  mixed  $value
     * @return $this
     */
    public function orWhereNullSafeEquals($column, $value)
    {
        return $this->whereNullSafeEquals($column, $value, 'or');
    }

    /**
     * Add a "where in" clause to the query.
     *
     * @param  \Illuminate\Contracts\Database\Query\Expression|string  $column
     * @param  mixed  $values
     * @param  string  $boolean
     * @param  bool  $not
     * @return $this
     */
    public function whereIn($column, $values, $boolean = 'and', $not = false)
    {
        $type = $not ? 'NotIn' : 'In';

        // If the value is a query builder instance we will assume the developer wants to
        // look for any values that exist within this given query. So, we will add the
        // query accordingly so that this query is properly executed when it is run.
        if ($this->isQueryable($values)) {
            [$query, $bindings] = $this->createSub($values);

            $values = [new Expression($query)];

            $this->addBinding($bindings, 'where');
        }

        // Next, if the value is Arrayable we need to cast it to its raw array form so we
        // have the underlying array value instead of an Arrayable object which is not
        // able to be added as a binding, etc. We will then add to the wheres array.
        if ($values instanceof Arrayable) {
            $values = $values->toArray();
        }

        $this->wheres[] = compact('type', 'column', 'values', 'boolean');

        if (count($values) !== count(Arr::flatten($values, 1))) {
            throw new InvalidArgumentException('Nested arrays may not be passed to whereIn method.');
        }

        // Finally, we'll add a binding for each value unless that value is an expression
        // in which case we will just skip over it since it will be the query as a raw
        // string and not as a parameterized place-holder to be replaced by the PDO.
        $this->addBinding($this->cleanBindings($values), 'where');

        return $this;
    }

    /**
     * Add an "or where in" clause to the query.
     *
     * @param  \Illuminate\Contracts\Database\Query\Expression|string  $column
     * @param  mixed  $values
     * @return $this
     */
    public function orWhereIn($column, $values)
    {
        return $this->whereIn($column, $values, 'or');
    }

    /**
     * Add a "where not in" clause to the query.
     *
     * @param  \Illuminate\Contracts\Database\Query\Expression|string  $column
     * @param  mixed  $values
     * @param  string  $boolean
     * @return $this
     */
    public function whereNotIn($column, $values, $boolean = 'and')
    {
        return $this->whereIn($column, $values, $boolean, true);
    }

    /**
     * Add an "or where not in" clause to the query.
     *
     * @param  \Illuminate\Contracts\Database\Query\Expression|string  $column
     * @param  mixed  $values
     * @return $this
     */
    public function orWhereNotIn($column, $values)
    {
        return $this->whereNotIn($column, $values, 'or');
    }

    /**
     * Add a "where in raw" clause for integer values to the query.
     *
     * @param  string  $column
     * @param  \Illuminate\Contracts\Support\Arrayable|array  $values
     * @param  string  $boolean
     * @param  bool  $not
     * @return $this
     */
    public function whereIntegerInRaw($column, $values, $boolean = 'and', $not = false)
    {
        $type = $not ? 'NotInRaw' : 'InRaw';

        if ($values instanceof Arrayable) {
            $values = $values->toArray();
        }

        $values = Arr::flatten($values);

        foreach ($values as &$value) {
            $value = (int) ($value instanceof BackedEnum ? $value->value : $value);
        }

        $this->wheres[] = compact('type', 'column', 'values', 'boolean');

        return $this;
    }

    /**
     * Add an "or where in raw" clause for integer values to the query.
     *
     * @param  string  $column
     * @param  \Illuminate\Contracts\Support\Arrayable|array  $values
     * @return $this
     */
    public function orWhereIntegerInRaw($column, $values)
    {
        return $this->whereIntegerInRaw($column, $values, 'or');
    }

    /**
     * Add a "where not in raw" clause for integer values to the query.
     *
     * @param  string  $column
     * @param  \Illuminate\Contracts\Support\Arrayable|array  $values
     * @param  string  $boolean
     * @return $this
     */
    public function whereIntegerNotInRaw($column, $values, $boolean = 'and')
    {
        return $this->whereIntegerInRaw($column, $values, $boolean, true);
    }

    /**
     * Add an "or where not in raw" clause for integer values to the query.
     *
     * @param  string  $column
     * @param  \Illuminate\Contracts\Support\Arrayable|array  $values
     * @return $this
     */
    public function orWhereIntegerNotInRaw($column, $values)
    {
        return $this->whereIntegerNotInRaw($column, $values, 'or');
    }

    /**
     * Add a "where null" clause to the query.
     *
     * @param  string|array|\Illuminate\Contracts\Database\Query\Expression  $columns
     * @param  string  $boolean
     * @param  bool  $not
     * @return $this
     */
    public function whereNull($columns, $boolean = 'and', $not = false)
    {
        $type = $not ? 'NotNull' : 'Null';

        foreach (Arr::wrap($columns) as $column) {
            $this->wheres[] = compact('type', 'column', 'boolean');
        }

        return $this;
    }

    /**
     * Add an "or where null" clause to the query.
     *
     * @param  string|array|\Illuminate\Contracts\Database\Query\Expression  $column
     * @return $this
     */
    public function orWhereNull($column)
    {
        return $this->whereNull($column, 'or');
    }

    /**
     * Add a "where not null" clause to the query.
     *
     * @param  string|array|\Illuminate\Contracts\Database\Query\Expression  $columns
     * @param  string  $boolean
     * @return $this
     */
    public function whereNotNull($columns, $boolean = 'and')
    {
        return $this->whereNull($columns, $boolean, true);
    }

    /**
     * Add a "where between" statement to the query.
     *
     * @param  \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder<*>|\Illuminate\Contracts\Database\Query\Expression|string  $column
     * @param  string  $boolean
     * @param  bool  $not
     * @return $this
     */
    public function whereBetween($column, iterable $values, $boolean = 'and', $not = false)
    {
        $type = 'between';

        if ($this->isQueryable($column)) {
            [$sub, $bindings] = $this->createSub($column);

            return $this->addBinding($bindings, 'where')
                ->whereBetween(new Expression('('.$sub.')'), $values, $boolean, $not);
        }

        if ($values instanceof DatePeriod) {
            $values = $this->resolveDatePeriodBounds($values);
        }

        $this->wheres[] = compact('type', 'column', 'values', 'boolean', 'not');

        $this->addBinding(array_slice($this->cleanBindings(Arr::flatten($values)), 0, 2), 'where');

        return $this;
    }

    /**
     * Add a "where between" statement using columns to the query.
     *
     * @param  \Illuminate\Contracts\Database\Query\Expression|string  $column
     * @param  string  $boolean
     * @param  bool  $not
     * @return $this
     */
    public function whereBetweenColumns($column, array $values, $boolean = 'and', $not = false)
    {
        $type = 'betweenColumns';

        if ($this->isQueryable($column)) {
            [$sub, $bindings] = $this->createSub($column);

            return $this->addBinding($bindings, 'where')
                ->whereBetweenColumns(new Expression('('.$sub.')'), $values, $boolean, $not);
        }

        $this->wheres[] = compact('type', 'column', 'values', 'boolean', 'not');

        return $this;
    }

    /**
     * Add an "or where between" statement to the query.
     *
     * @param  \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder<*>|\Illuminate\Contracts\Database\Query\Expression|string  $column
     * @return $this
     */
    public function orWhereBetween($column, iterable $values)
    {
        return $this->whereBetween($column, $values, 'or');
    }

    /**
     * Add an "or where between" statement using columns to the query.
     *
     * @param  \Illuminate\Contracts\Database\Query\Expression|string  $column
     * @return $this
     */
    public function orWhereBetweenColumns($column, array $values)
    {
        return $this->whereBetweenColumns($column, $values, 'or');
    }

    /**
     * Add a "where not between" statement to the query.
     *
     * @param  \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder<*>|\Illuminate\Contracts\Database\Query\Expression|string  $column
     * @param  string  $boolean
     * @return $this
     */
    public function whereNotBetween($column, iterable $values, $boolean = 'and')
    {
        return $this->whereBetween($column, $values, $boolean, true);
    }

    /**
     * Add a "where not between" statement using columns to the query.
     *
     * @param  \Illuminate\Contracts\Database\Query\Expression|string  $column
     * @param  string  $boolean
     * @return $this
     */
    public function whereNotBetweenColumns($column, array $values, $boolean = 'and')
    {
        return $this->whereBetweenColumns($column, $values, $boolean, true);
    }

    /**
     * Add an "or where not between" statement to the query.
     *
     * @param  \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder<*>|\Illuminate\Contracts\Database\Query\Expression|string  $column
     * @return $this
     */
    public function orWhereNotBetween($column, iterable $values)
    {
        return $this->whereNotBetween($column, $values, 'or');
    }

    /**
     * Add an "or where not between" statement using columns to the query.
     *
     * @param  \Illuminate\Contracts\Database\Query\Expression|string  $column
     * @return $this
     */
    public function orWhereNotBetweenColumns($column, array $values)
    {
        return $this->whereNotBetweenColumns($column, $values, 'or');
    }

    /**
     * Add a "where between columns" statement using a value to the query.
     *
     * @param  mixed  $value
     * @param  array{\Illuminate\Contracts\Database\Query\Expression|string, \Illuminate\Contracts\Database\Query\Expression|string}  $columns
     * @param  string  $boolean
     * @param  bool  $not
     * @return $this
     */
    public function whereValueBetween($value, array $columns, $boolean = 'and', $not = false)
    {
        $type = 'valueBetween';

        $this->wheres[] = compact('type', 'value', 'columns', 'boolean', 'not');

        $this->addBinding($value, 'where');

        return $this;
    }

    /**
     * Add an "or where between columns" statement using a value to the query.
     *
     * @param  mixed  $value
     * @param  array{\Illuminate\Contracts\Database\Query\Expression|string, \Illuminate\Contracts\Database\Query\Expression|string}  $columns
     * @return $this
     */
    public function orWhereValueBetween($value, array $columns)
    {
        return $this->whereValueBetween($value, $columns, 'or');
    }

    /**
     * Add a "where not between columns" statement using a value to the query.
     *
     * @param  mixed  $value
     * @param  array{\Illuminate\Contracts\Database\Query\Expression|string, \Illuminate\Contracts\Database\Query\Expression|string}  $columns
     * @param  string  $boolean
     * @return $this
     */
    public function whereValueNotBetween($value, array $columns, $boolean = 'and')
    {
        return $this->whereValueBetween($value, $columns, $boolean, true);
    }

    /**
     * Add an "or where not between columns" statement using a value to the query.
     *
     * @param  mixed  $value
     * @param  array{\Illuminate\Contracts\Database\Query\Expression|string, \Illuminate\Contracts\Database\Query\Expression|string}  $columns
     * @return $this
     */
    public function orWhereValueNotBetween($value, array $columns)
    {
        return $this->whereValueNotBetween($value, $columns, 'or');
    }

    /**
     * Add an "or where not null" clause to the query.
     *
     * @param  \Illuminate\Contracts\Database\Query\Expression|string  $column
     * @return $this
     */
    public function orWhereNotNull($column)
    {
        return $this->whereNotNull($column, 'or');
    }

    /**
     * Add a "where date" statement to the query.
     *
     * @param  \Illuminate\Contracts\Database\Query\Expression|string  $column
     * @param  \DateTimeInterface|string|null  $operator
     * @param  \DateTimeInterface|string|null  $value
     * @param  string  $boolean
     * @return $this
     */
    public function whereDate($column, $operator, $value = null, $boolean = 'and')
    {
        [$value, $operator] = $this->prepareValueAndOperator(
            $value, $operator, func_num_args() === 2
        );

        // If the given operator is not found in the list of valid operators we will
        // assume that the developer is just short-cutting the '=' operators and
        // we will set the operators to '=' and set the values appropriately.
        if ($this->invalidOperator($operator)) {
            [$value, $operator] = [$operator, '='];
        }

        $value = $this->flattenValue($value);

        if ($value instanceof DateTimeInterface) {
            $value = $value->format('Y-m-d');
        }

        return $this->addDateBasedWhere('Date', $column, $operator, $value, $boolean);
    }

    /**
     * Add an "or where date" statement to the query.
     *
     * @param  \Illuminate\Contracts\Database\Query\Expression|string  $column
     * @param  \DateTimeInterface|string|null  $operator
     * @param  \DateTimeInterface|string|null  $value
     * @return $this
     */
    public function orWhereDate($column, $operator, $value = null)
    {
        [$value, $operator] = $this->prepareValueAndOperator(
            $value, $operator, func_num_args() === 2
        );

        return $this->whereDate($column, $operator, $value, 'or');
    }

    /**
     * Add a "where time" statement to the query.
     *
     * @param  \Illuminate\Contracts\Database\Query\Expression|string  $column
     * @param  \DateTimeInterface|string|null  $operator
     * @param  \DateTimeInterface|string|null  $value
     * @param  string  $boolean
     * @return $this
     */
    public function whereTime($column, $operator, $value = null, $boolean = 'and')
    {
        [$value, $operator] = $this->prepareValueAndOperator(
            $value, $operator, func_num_args() === 2
        );

        // If the given operator is not found in the list of valid operators we will
        // assume that the developer is just short-cutting the '=' operators and
        // we will set the operators to '=' and set the values appropriately.
        if ($this->invalidOperator($operator)) {
            [$value, $operator] = [$operator, '='];
        }

        $value = $this->flattenValue($value);

        if ($value instanceof DateTimeInterface) {
            $value = $value->format('H:i:s');
        }

        return $this->addDateBasedWhere('Time', $column, $operator, $value, $boolean);
    }

    /**
     * Add an "or where time" statement to the query.
     *
     * @param  \Illuminate\Contracts\Database\Query\Expression|string  $column
     * @param  \DateTimeInterface|string|null  $operator
     * @param  \DateTimeInterface|string|null  $value
     * @return $this
     */
    public function orWhereTime($column, $operator, $value = null)
    {
        [$value, $operator] = $this->prepareValueAndOperator(
            $value, $operator, func_num_args() === 2
        );

        return $this->whereTime($column, $operator, $value, 'or');
    }

    /**
     * Add a "where day" statement to the query.
     *
     * @param  \Illuminate\Contracts\Database\Query\Expression|string  $column
     * @param  \DateTimeInterface|string|int|null  $operator
     * @param  \DateTimeInterface|string|int|null  $value
     * @param  string  $boolean
     * @return $this
     */
    public function whereDay($column, $operator, $value = null, $boolean = 'and')
    {
        [$value, $operator] = $this->prepareValueAndOperator(
            $value, $operator, func_num_args() === 2
        );

        // If the given operator is not found in the list of valid operators we will
        // assume that the developer is just short-cutting the '=' operators and
        // we will set the operators to '=' and set the values appropriately.
        if ($this->invalidOperator($operator)) {
            [$value, $operator] = [$operator, '='];
        }

        $value = $this->flattenValue($value);

        if ($value instanceof DateTimeInterface) {
            $value = $value->format('d');
        }

        if (! $value instanceof ExpressionContract) {
            $value = sprintf('%02d', $value);
        }

        return $this->addDateBasedWhere('Day', $column, $operator, $value, $boolean);
    }

    /**
     * Add an "or where day" statement to the query.
     *
     * @param  \Illuminate\Contracts\Database\Query\Expression|string  $column
     * @param  \DateTimeInterface|string|int|null  $operator
     * @param  \DateTimeInterface|string|int|null  $value
     * @return $this
     */
    public function orWhereDay($column, $operator, $value = null)
    {
        [$value, $operator] = $this->prepareValueAndOperator(
            $value, $operator, func_num_args() === 2
        );

        return $this->whereDay($column, $operator, $value, 'or');
    }

    /**
     * Add a "where month" statement to the query.
     *
     * @param  \Illuminate\Contracts\Database\Query\Expression|string  $column
     * @param  \DateTimeInterface|string|int|null  $operator
     * @param  \DateTimeInterface|string|int|null  $value
     * @param  string  $boolean
     * @return $this
     */
    public function whereMonth($column, $operator, $value = null, $boolean = 'and')
    {
        [$value, $operator] = $this->prepareValueAndOperator(
            $value, $operator, func_num_args() === 2
        );

        // If the given operator is not found in the list of valid operators we will
        // assume that the developer is just short-cutting the '=' operators and
        // we will set the operators to '=' and set the values appropriately.
        if ($this->invalidOperator($operator)) {
            [$value, $operator] = [$operator, '='];
        }

        $value = $this->flattenValue($value);

        if ($value instanceof DateTimeInterface) {
            $value = $value->format('m');
        }

        if (! $value instanceof ExpressionContract) {
            $value = sprintf('%02d', $value);
        }

        return $this->addDateBasedWhere('Month', $column, $operator, $value, $boolean);
    }

    /**
     * Add an "or where month" statement to the query.
     *
     * @param  \Illuminate\Contracts\Database\Query\Expression|string  $column
     * @param  \DateTimeInterface|string|int|null  $operator
     * @param  \DateTimeInterface|string|int|null  $value
     * @return $this
     */
    public function orWhereMonth($column, $operator, $value = null)
    {
        [$value, $operator] = $this->prepareValueAndOperator(
            $value, $operator, func_num_args() === 2
        );

        return $this->whereMonth($column, $operator, $value, 'or');
    }

    /**
     * Add a "where year" statement to the query.
     *
     * @param  \Illuminate\Contracts\Database\Query\Expression|string  $column
     * @param  \DateTimeInterface|string|int|null  $operator
     * @param  \DateTimeInterface|string|int|null  $value
     * @param  string  $boolean
     * @return $this
     */
    public function whereYear($column, $operator, $value = null, $boolean = 'and')
    {
        [$value, $operator] = $this->prepareValueAndOperator(
            $value, $operator, func_num_args() === 2
        );

        // If the given operator is not found in the list of valid operators we will
        // assume that the developer is just short-cutting the '=' operators and
        // we will set the operators to '=' and set the values appropriately.
        if ($this->invalidOperator($operator)) {
            [$value, $operator] = [$operator, '='];
        }

        $value = $this->flattenValue($value);

        if ($value instanceof DateTimeInterface) {
            $value = $value->format('Y');
        }

        return $this->addDateBasedWhere('Year', $column, $operator, $value, $boolean);
    }

    /**
     * Add an "or where year" statement to the query.
     *
     * @param  \Illuminate\Contracts\Database\Query\Expression|string  $column
     * @param  \DateTimeInterface|string|int|null  $operator
     * @param  \DateTimeInterface|string|int|null  $value
     * @return $this
     */
    public function orWhereYear($column, $operator, $value = null)
    {
        [$value, $operator] = $this->prepareValueAndOperator(
            $value, $operator, func_num_args() === 2
        );

        return $this->whereYear($column, $operator, $value, 'or');
    }

    /**
     * Add a date based (year, month, day, time) statement to the query.
     *
     * @param  string  $type
     * @param  \Illuminate\Contracts\Database\Query\Expression|string  $column
     * @param  string  $operator
     * @param  mixed  $value
     * @param  string  $boolean
     * @return $this
     */
    protected function addDateBasedWhere($type, $column, $operator, $value, $boolean = 'and')
    {
        $this->wheres[] = compact('column', 'type', 'boolean', 'operator', 'value');

        if (! $value instanceof ExpressionContract) {
            $this->addBinding($value, 'where');
        }

        return $this;
    }

    /**
     * Add a nested "where" statement to the query.
     *
     * @param  string  $boolean
     * @return $this
     */
    public function whereNested(Closure $callback, $boolean = 'and')
    {
        $callback($query = $this->forNestedWhere());

        return $this->addNestedWhereQuery($query, $boolean);
    }

    /**
     * Create a new query instance for nested where condition.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function forNestedWhere()
    {
        return $this->newQuery()->from($this->from);
    }

    /**
     * Add another query builder as a nested where to the query builder.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  string  $boolean
     * @return $this
     */
    public function addNestedWhereQuery($query, $boolean = 'and')
    {
        if (count($query->wheres)) {
            $type = 'Nested';

            $this->wheres[] = compact('type', 'query', 'boolean');

            $this->addBinding($query->getRawBindings()['where'], 'where');
        }

        return $this;
    }

    /**
     * Add a full sub-select to the query.
     *
     * @param  \Illuminate\Contracts\Database\Query\Expression|string  $column
     * @param  string  $operator
     * @param  \Closure|\Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder<*>  $callback
     * @param  string  $boolean
     * @return $this
     */
    protected function whereSub($column, $operator, $callback, $boolean)
    {
        $type = 'Sub';

        if ($callback instanceof Closure) {
            // Once we have the query instance we can simply execute it so it can add all
            // of the sub-select's conditions to itself, and then we can cache it off
            // in the array of where clauses for the "main" parent query instance.
            $callback($query = $this->forSubQuery());
        } else {
            $query = $callback instanceof EloquentBuilder ? $callback->toBase() : $callback;
        }

        $this->wheres[] = compact(
            'type', 'column', 'operator', 'query', 'boolean'
        );

        $this->addBinding($query->getBindings(), 'where');

        return $this;
    }

    /**
     * Add an "exists" clause to the query.
     *
     * @param  \Closure|\Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder<*>  $callback
     * @param  string  $boolean
     * @param  bool  $not
     * @return $this
     */
    public function whereExists($callback, $boolean = 'and', $not = false)
    {
        if ($callback instanceof Closure) {
            $query = $this->forSubQuery();

            // Similar to the sub-select clause, we will create a new query instance so
            // the developer may cleanly specify the entire exists query and we will
            // compile the whole thing in the grammar and insert it into the SQL.
            $callback($query);
        } else {
            $query = $callback instanceof EloquentBuilder ? $callback->toBase() : $callback;
        }

        return $this->addWhereExistsQuery($query, $boolean, $not);
    }

    /**
     * Add an "or where exists" clause to the query.
     *
     * @param  \Closure|\Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder<*>  $callback
     * @param  bool  $not
     * @return $this
     */
    public function orWhereExists($callback, $not = false)
    {
        return $this->whereExists($callback, 'or', $not);
    }

    /**
     * Add a "where not exists" clause to the query.
     *
     * @param  \Closure|\Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder<*>  $callback
     * @param  string  $boolean
     * @return $this
     */
    public function whereNotExists($callback, $boolean = 'and')
    {
        return $this->whereExists($callback, $boolean, true);
    }

    /**
     * Add an "or where not exists" clause to the query.
     *
     * @param  \Closure|\Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder<*>  $callback
     * @return $this
     */
    public function orWhereNotExists($callback)
    {
        return $this->orWhereExists($callback, true);
    }

    /**
     * Add an "exists" clause to the query.
     *
     * @param  string  $boolean
     * @param  bool  $not
     * @return $this
     */
    public function addWhereExistsQuery(self $query, $boolean = 'and', $not = false)
    {
        $type = $not ? 'NotExists' : 'Exists';

        $this->wheres[] = compact('type', 'query', 'boolean');

        $this->addBinding($query->getBindings(), 'where');

        return $this;
    }

    /**
     * Adds a where condition using row values.
     *
     * @param  array  $columns
     * @param  string  $operator
     * @param  array  $values
     * @param  string  $boolean
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function whereRowValues($columns, $operator, $values, $boolean = 'and')
    {
        if (count($columns) !== count($values)) {
            throw new InvalidArgumentException('The number of columns must match the number of values');
        }

        $type = 'RowValues';

        $this->wheres[] = compact('type', 'columns', 'operator', 'values', 'boolean');

        $this->addBinding($this->cleanBindings($values));

        return $this;
    }

    /**
     * Adds an or where condition using row values.
     *
     * @param  array  $columns
     * @param  string  $operator
     * @param  array  $values
     * @return $this
     */
    public function orWhereRowValues($columns, $operator, $values)
    {
        return $this->whereRowValues($columns, $operator, $values, 'or');
    }

    /**
     * Add a "where JSON contains" clause to the query.
     *
     * @param  string  $column
     * @param  mixed  $value
     * @param  string  $boolean
     * @param  bool  $not
     * @return $this
     */
    public function whereJsonContains($column, $value, $boolean = 'and', $not = false)
    {
        $type = 'JsonContains';

        $this->wheres[] = compact('type', 'column', 'value', 'boolean', 'not');

        if (! $value instanceof ExpressionContract) {
            $this->addBinding($this->grammar->prepareBindingForJsonContains($value));
        }

        return $this;
    }

    /**
     * Add an "or where JSON contains" clause to the query.
     *
     * @param  string  $column
     * @param  mixed  $value
     * @return $this
     */
    public function orWhereJsonContains($column, $value)
    {
        return $this->whereJsonContains($column, $value, 'or');
    }

    /**
     * Add a "where JSON not contains" clause to the query.
     *
     * @param  string  $column
     * @param  mixed  $value
     * @param  string  $boolean
     * @return $this
     */
    public function whereJsonDoesntContain($column, $value, $boolean = 'and')
    {
        return $this->whereJsonContains($column, $value, $boolean, true);
    }

    /**
     * Add an "or where JSON not contains" clause to the query.
     *
     * @param  string  $column
     * @param  mixed  $value
     * @return $this
     */
    public function orWhereJsonDoesntContain($column, $value)
    {
        return $this->whereJsonDoesntContain($column, $value, 'or');
    }

    /**
     * Add a "where JSON overlaps" clause to the query.
     *
     * @param  string  $column
     * @param  mixed  $value
     * @param  string  $boolean
     * @param  bool  $not
     * @return $this
     */
    public function whereJsonOverlaps($column, $value, $boolean = 'and', $not = false)
    {
        $type = 'JsonOverlaps';

        $this->wheres[] = compact('type', 'column', 'value', 'boolean', 'not');

        if (! $value instanceof ExpressionContract) {
            $this->addBinding($this->grammar->prepareBindingForJsonContains($value));
        }

        return $this;
    }

    /**
     * Add an "or where JSON overlaps" clause to the query.
     *
     * @param  string  $column
     * @param  mixed  $value
     * @return $this
     */
    public function orWhereJsonOverlaps($column, $value)
    {
        return $this->whereJsonOverlaps($column, $value, 'or');
    }

    /**
     * Add a "where JSON not overlap" clause to the query.
     *
     * @param  string  $column
     * @param  mixed  $value
     * @param  string  $boolean
     * @return $this
     */
    public function whereJsonDoesntOverlap($column, $value, $boolean = 'and')
    {
        return $this->whereJsonOverlaps($column, $value, $boolean, true);
    }

    /**
     * Add an "or where JSON not overlap" clause to the query.
     *
     * @param  string  $column
     * @param  mixed  $value
     * @return $this
     */
    public function orWhereJsonDoesntOverlap($column, $value)
    {
        return $this->whereJsonDoesntOverlap($column, $value, 'or');
    }

    /**
     * Add a clause that determines if a JSON path exists to the query.
     *
     * @param  string  $column
     * @param  string  $boolean
     * @param  bool  $not
     * @return $this
     */
    public function whereJsonContainsKey($column, $boolean = 'and', $not = false)
    {
        $type = 'JsonContainsKey';

        $this->wheres[] = compact('type', 'column', 'boolean', 'not');

        return $this;
    }

    /**
     * Add an "or" clause that determines if a JSON path exists to the query.
     *
     * @param  string  $column
     * @return $this
     */
    public function orWhereJsonContainsKey($column)
    {
        return $this->whereJsonContainsKey($column, 'or');
    }

    /**
     * Add a clause that determines if a JSON path does not exist to the query.
     *
     * @param  string  $column
     * @param  string  $boolean
     * @return $this
     */
    public function whereJsonDoesntContainKey($column, $boolean = 'and')
    {
        return $this->whereJsonContainsKey($column, $boolean, true);
    }

    /**
     * Add an "or" clause that determines if a JSON path does not exist to the query.
     *
     * @param  string  $column
     * @return $this
     */
    public function orWhereJsonDoesntContainKey($column)
    {
        return $this->whereJsonDoesntContainKey($column, 'or');
    }

    /**
     * Add a "where JSON length" clause to the query.
     *
     * @param  string  $column
     * @param  mixed  $operator
     * @param  mixed  $value
     * @param  string  $boolean
     * @return $this
     */
    public function whereJsonLength($column, $operator, $value = null, $boolean = 'and')
    {
        $type = 'JsonLength';

        [$value, $operator] = $this->prepareValueAndOperator(
            $value, $operator, func_num_args() === 2
        );

        // If the given operator is not found in the list of valid operators we will
        // assume that the developer is just short-cutting the '=' operators and
        // we will set the operators to '=' and set the values appropriately.
        if ($this->invalidOperator($operator)) {
            [$value, $operator] = [$operator, '='];
        }

        $this->wheres[] = compact('type', 'column', 'operator', 'value', 'boolean');

        if (! $value instanceof ExpressionContract) {
            $this->addBinding((int) $this->flattenValue($value));
        }

        return $this;
    }

    /**
     * Add an "or where JSON length" clause to the query.
     *
     * @param  string  $column
     * @param  mixed  $operator
     * @param  mixed  $value
     * @return $this
     */
    public function orWhereJsonLength($column, $operator, $value = null)
    {
        [$value, $operator] = $this->prepareValueAndOperator(
            $value, $operator, func_num_args() === 2
        );

        return $this->whereJsonLength($column, $operator, $value, 'or');
    }

    /**
     * Handles dynamic "where" clauses to the query.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return $this
     */
    public function dynamicWhere($method, $parameters)
    {
        $finder = substr($method, 5);

        $segments = preg_split(
            '/(And|Or)(?=[A-Z])/', $finder, -1, PREG_SPLIT_DELIM_CAPTURE
        );

        // The connector variable will determine which connector will be used for the
        // query condition. We will change it as we come across new boolean values
        // in the dynamic method strings, which could contain a number of these.
        $connector = 'and';

        $index = 0;

        foreach ($segments as $segment) {
            // If the segment is not a boolean connector, we can assume it is a column's name
            // and we will add it to the query as a new constraint as a where clause, then
            // we can keep iterating through the dynamic method string's segments again.
            if ($segment !== 'And' && $segment !== 'Or') {
                $this->addDynamic($segment, $connector, $parameters, $index);

                $index++;
            }

            // Otherwise, we will store the connector so we know how the next where clause we
            // find in the query should be connected to the previous ones, meaning we will
            // have the proper boolean connector to connect the next where clause found.
            else {
                $connector = $segment;
            }
        }

        return $this;
    }

    /**
     * Add a single dynamic "where" clause statement to the query.
     *
     * @param  string  $segment
     * @param  string  $connector
     * @param  array  $parameters
     * @param  int  $index
     * @return void
     */
    protected function addDynamic($segment, $connector, $parameters, $index)
    {
        // Once we have parsed out the columns and formatted the boolean operators we
        // are ready to add it to this query as a where clause just like any other
        // clause on the query. Then we'll increment the parameter index values.
        $bool = strtolower($connector);

        $this->where(Str::snake($segment), '=', $parameters[$index], $bool);
    }

    /**
     * Add a "where fulltext" clause to the query.
     *
     * @param  string|string[]  $columns
     * @param  string  $value
     * @param  string  $boolean
     * @return $this
     */
    public function whereFullText($columns, $value, array $options = [], $boolean = 'and')
    {
        $type = 'Fulltext';

        $columns = (array) $columns;

        $this->wheres[] = compact('type', 'columns', 'value', 'options', 'boolean');

        $this->addBinding($value);

        return $this;
    }

    /**
     * Add an "or where fulltext" clause to the query.
     *
     * @param  string|string[]  $columns
     * @param  string  $value
     * @return $this
     */
    public function orWhereFullText($columns, $value, array $options = [])
    {
        return $this->whereFullText($columns, $value, $options, 'or');
    }

    /**
     * Add a "where" clause to the query for multiple columns with "and" conditions between them.
     *
     * @param  \Illuminate\Contracts\Database\Query\Expression[]|\Closure[]|string[]  $columns
     * @param  mixed  $operator
     * @param  mixed  $value
     * @param  string  $boolean
     * @return $this
     */
    public function whereAll($columns, $operator = null, $value = null, $boolean = 'and')
    {
        [$value, $operator] = $this->prepareValueAndOperator(
            $value, $operator, func_num_args() === 2
        );

        $this->whereNested(function ($query) use ($columns, $operator, $value) {
            foreach ($columns as $column) {
                $query->where($column, $operator, $value, 'and');
            }
        }, $boolean);

        return $this;
    }

    /**
     * Add an "or where" clause to the query for multiple columns with "and" conditions between them.
     *
     * @param  \Illuminate\Contracts\Database\Query\Expression[]|\Closure[]|string[]  $columns
     * @param  mixed  $operator
     * @param  mixed  $value
     * @return $this
     */
    public function orWhereAll($columns, $operator = null, $value = null)
    {
        return $this->whereAll($columns, $operator, $value, 'or');
    }

    /**
     * Add a "where" clause to the query for multiple columns with "or" conditions between them.
     *
     * @param  \Illuminate\Contracts\Database\Query\Expression[]|\Closure[]|string[]  $columns
     * @param  mixed  $operator
     * @param  mixed  $value
     * @param  string  $boolean
     * @return $this
     */
    public function whereAny($columns, $operator = null, $value = null, $boolean = 'and')
    {
        [$value, $operator] = $this->prepareValueAndOperator(
            $value, $operator, func_num_args() === 2
        );

        $this->whereNested(function ($query) use ($columns, $operator, $value) {
            foreach ($columns as $column) {
                $query->where($column, $operator, $value, 'or');
            }
        }, $boolean);

        return $this;
    }

    /**
     * Add an "or where" clause to the query for multiple columns with "or" conditions between them.
     *
     * @param  \Illuminate\Contracts\Database\Query\Expression[]|\Closure[]|string[]  $columns
     * @param  mixed  $operator
     * @param  mixed  $value
     * @return $this
     */
    public function orWhereAny($columns, $operator = null, $value = null)
    {
        return $this->whereAny($columns, $operator, $value, 'or');
    }

    /**
     * Add a "where not" clause to the query for multiple columns where none of the conditions should be true.
     *
     * @param  \Illuminate\Contracts\Database\Query\Expression[]|\Closure[]|string[]  $columns
     * @param  mixed  $operator
     * @param  mixed  $value
     * @param  string  $boolean
     * @return $this
     */
    public function whereNone($columns, $operator = null, $value = null, $boolean = 'and')
    {
        return $this->whereAny($columns, $operator, $value, $boolean.' not');
    }

    /**
     * Add an "or where not" clause to the query for multiple columns where none of the conditions should be true.
     *
     * @param  \Illuminate\Contracts\Database\Query\Expression[]|\Closure[]|string[]  $columns
     * @param  mixed  $operator
     * @param  mixed  $value
     * @return $this
     */
    public function orWhereNone($columns, $operator = null, $value = null)
    {
        return $this->whereNone($columns, $operator, $value, 'or');
    }

    /**
     * Add a "group by" clause to the query.
     *
     * @param  array|\Illuminate\Contracts\Database\Query\Expression|string  ...$groups
     * @return $this
     */
    public function groupBy(...$groups)
    {
        foreach ($groups as $group) {
            $this->groups = array_merge(
                (array) $this->groups,
                Arr::wrap($group)
            );
        }

        return $this;
    }

    /**
     * Add a raw "groupBy" clause to the query.
     *
     * @param  string  $sql
     * @return $this
     */
    public function groupByRaw($sql, array $bindings = [])
    {
        $this->groups[] = new Expression($sql);

        $this->addBinding($bindings, 'groupBy');

        return $this;
    }

    /**
     * Add a "having" clause to the query.
     *
     * @param  \Illuminate\Contracts\Database\Query\Expression|\Closure|string  $column
     * @param  \DateTimeInterface|string|int|float|null  $operator
     * @param  \Illuminate\Contracts\Database\Query\Expression|\DateTimeInterface|string|int|float|null  $value
     * @param  string  $boolean
     * @return $this
     */
    public function having($column, $operator = null, $value = null, $boolean = 'and')
    {
        $type = 'Basic';

        if ($column instanceof ConditionExpression) {
            $type = 'Expression';

            $this->havings[] = compact('type', 'column', 'boolean');

            return $this;
        }

        // Here we will make some assumptions about the operator. If only 2 values are
        // passed to the method, we will assume that the operator is an equals sign
        // and keep going. Otherwise, we'll require the operator to be passed in.
        [$value, $operator] = $this->prepareValueAndOperator(
            $value, $operator, func_num_args() === 2
        );

        if ($column instanceof Closure && is_null($operator)) {
            return $this->havingNested($column, $boolean);
        }

        // If the given operator is not found in the list of valid operators we will
        // assume that the developer is just short-cutting the '=' operators and
        // we will set the operators to '=' and set the values appropriately.
        if ($this->invalidOperator($operator)) {
            [$value, $operator] = [$operator, '='];
        }

        if ($this->isBitwiseOperator($operator)) {
            $type = 'Bitwise';
        }

        $this->havings[] = compact('type', 'column', 'operator', 'value', 'boolean');

        if (! $value instanceof ExpressionContract) {
            $this->addBinding($this->flattenValue($value), 'having');
        }

        return $this;
    }

    /**
     * Add an "or having" clause to the query.
     *
     * @param  \Illuminate\Contracts\Database\Query\Expression|\Closure|string  $column
     * @param  \DateTimeInterface|string|int|float|null  $operator
     * @param  \Illuminate\Contracts\Database\Query\Expression|\DateTimeInterface|string|int|float|null  $value
     * @return $this
     */
    public function orHaving($column, $operator = null, $value = null)
    {
        [$value, $operator] = $this->prepareValueAndOperator(
            $value, $operator, func_num_args() === 2
        );

        return $this->having($column, $operator, $value, 'or');
    }

    /**
     * Add a nested "having" statement to the query.
     *
     * @param  string  $boolean
     * @return $this
     */
    public function havingNested(Closure $callback, $boolean = 'and')
    {
        $callback($query = $this->forNestedWhere());

        return $this->addNestedHavingQuery($query, $boolean);
    }

    /**
     * Add another query builder as a nested having to the query builder.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  string  $boolean
     * @return $this
     */
    public function addNestedHavingQuery($query, $boolean = 'and')
    {
        if (count($query->havings)) {
            $type = 'Nested';

            $this->havings[] = compact('type', 'query', 'boolean');

            $this->addBinding($query->getRawBindings()['having'], 'having');
        }

        return $this;
    }

    /**
     * Add a "having null" clause to the query.
     *
     * @param  array|string  $columns
     * @param  string  $boolean
     * @param  bool  $not
     * @return $this
     */
    public function havingNull($columns, $boolean = 'and', $not = false)
    {
        $type = $not ? 'NotNull' : 'Null';

        foreach (Arr::wrap($columns) as $column) {
            $this->havings[] = compact('type', 'column', 'boolean');
        }

        return $this;
    }

    /**
     * Add an "or having null" clause to the query.
     *
     * @param  string  $column
     * @return $this
     */
    public function orHavingNull($column)
    {
        return $this->havingNull($column, 'or');
    }

    /**
     * Add a "having not null" clause to the query.
     *
     * @param  array|string  $columns
     * @param  string  $boolean
     * @return $this
     */
    public function havingNotNull($columns, $boolean = 'and')
    {
        return $this->havingNull($columns, $boolean, true);
    }

    /**
     * Add an "or having not null" clause to the query.
     *
     * @param  string  $column
     * @return $this
     */
    public function orHavingNotNull($column)
    {
        return $this->havingNotNull($column, 'or');
    }

    /**
     * Add a "having between" clause to the query.
     *
     * @param  string  $column
     * @param  string  $boolean
     * @param  bool  $not
     * @return $this
     */
    public function havingBetween($column, iterable $values, $boolean = 'and', $not = false)
    {
        $type = 'between';

        if ($values instanceof DatePeriod) {
            $values = $this->resolveDatePeriodBounds($values);
        }

        $this->havings[] = compact('type', 'column', 'values', 'boolean', 'not');

        $this->addBinding(array_slice($this->cleanBindings(Arr::flatten($values)), 0, 2), 'having');

        return $this;
    }

    /**
     * Add a "having not between" clause to the query.
     *
     * @param  string  $column
     * @param  iterable  $values
     * @param  string  $boolean
     * @return $this
     */
    public function havingNotBetween($column, iterable $values, $boolean = 'and')
    {
        return $this->havingBetween($column, $values, $boolean, true);
    }

    /**
     * Add an "or having between" clause to the query.
     *
     * @param  string  $column
     * @param  iterable  $values
     * @return $this
     */
    public function orHavingBetween($column, iterable $values)
    {
        return $this->havingBetween($column, $values, 'or');
    }

    /**
     * Add an "or having not between" clause to the query.
     *
     * @param  string  $column
     * @param  iterable  $values
     * @return $this
     */
    public function orHavingNotBetween($column, iterable $values)
    {
        return $this->havingBetween($column, $values, 'or', true);
    }

    /**
     * Resolve the start and end dates from a DatePeriod.
     *
     * @param  \DatePeriod  $period
     * @return array{\DateTimeInterface, \DateTimeInterface}
     */
    protected function resolveDatePeriodBounds(DatePeriod $period)
    {
        [$start, $end] = [$period->getStartDate(), $period->getEndDate()];

        if ($end === null) {
            $end = clone $start;

            $recurrences = $period->getRecurrences();

            for ($i = 0; $i < $recurrences; $i++) {
                $end = $end->add($period->getDateInterval());
            }
        }

        return [$start, $end];
    }

    /**
     * Add a raw "having" clause to the query.
     *
     * @param  string  $sql
     * @param  string  $boolean
     * @return $this
     */
    public function havingRaw($sql, array $bindings = [], $boolean = 'and')
    {
        $type = 'Raw';

        $this->havings[] = compact('type', 'sql', 'boolean');

        $this->addBinding($bindings, 'having');

        return $this;
    }

    /**
     * Add a raw "or having" clause to the query.
     *
     * @param  string  $sql
     * @return $this
     */
    public function orHavingRaw($sql, array $bindings = [])
    {
        return $this->havingRaw($sql, $bindings, 'or');
    }

    /**
     * Add an "order by" clause to the query.
     *
     * @param  \Closure|\Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder<*>|\Illuminate\Contracts\Database\Query\Expression|string  $column
     * @param  string  $direction
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function orderBy($column, $direction = 'asc')
    {
        if ($this->isQueryable($column)) {
            [$query, $bindings] = $this->createSub($column);

            $column = new Expression('('.$query.')');

            $this->addBinding($bindings, $this->unions ? 'unionOrder' : 'order');
        }

        $direction = strtolower($direction);

        if (! in_array($direction, ['asc', 'desc'], true)) {
            throw new InvalidArgumentException('Order direction must be "asc" or "desc".');
        }

        $this->{$this->unions ? 'unionOrders' : 'orders'}[] = [
            'column' => $column,
            'direction' => $direction,
        ];

        return $this;
    }

    /**
     * Add a descending "order by" clause to the query.
     *
     * @param  \Closure|\Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder<*>|\Illuminate\Contracts\Database\Query\Expression|string  $column
     * @return $this
     */
    public function orderByDesc($column)
    {
        return $this->orderBy($column, 'desc');
    }

    /**
     * Add an "order by" clause for a timestamp to the query.
     *
     * @param  \Closure|\Illuminate\Database\Query\Builder|\Illuminate\Contracts\Database\Query\Expression|string  $column
     * @return $this
     */
    public function latest($column = 'created_at')
    {
        return $this->orderBy($column, 'desc');
    }

    /**
     * Add an "order by" clause for a timestamp to the query.
     *
     * @param  \Closure|\Illuminate\Database\Query\Builder|\Illuminate\Contracts\Database\Query\Expression|string  $column
     * @return $this
     */
    public function oldest($column = 'created_at')
    {
        return $this->orderBy($column, 'asc');
    }

    /**
     * Add a vector-distance "order by" clause to the query.
     *
     * @param  \Illuminate\Contracts\Database\Query\Expression|string  $column
     * @param  \Illuminate\Support\Collection<int, float>|\Illuminate\Contracts\Support\Arrayable|array<int, float>  $vector
     * @return $this
     */
    public function orderByVectorDistance($column, $vector)
    {
        $this->ensureConnectionSupportsVectors();

        if (is_string($vector)) {
            $vector = Str::of($vector)->toEmbeddings(cache: true);
        }

        $this->addBinding(
            json_encode(
                $vector instanceof Arrayable
                    ? $vector->toArray()
                    : $vector,
                flags: JSON_THROW_ON_ERROR
            ),
            $this->unions ? 'unionOrder' : 'order'
        );

        $this->{$this->unions ? 'unionOrders' : 'orders'}[] = [
            'column' => new Expression("({$this->getGrammar()->wrap($column)} <=> ?)"),
            'direction' => 'asc',
        ];

        return $this;
    }

    /**
     * Put the query's results in random order.
     *
     * @param  string|int  $seed
     * @return $this
     */
    public function inRandomOrder($seed = '')
    {
        return $this->orderByRaw($this->grammar->compileRandom($seed));
    }

    /**
     * Add an "order by" clause to order results by a given sequence of values.
     *
     * @param  \Illuminate\Contracts\Database\Query\Expression|string  $column
     * @param  \Illuminate\Contracts\Support\Arrayable|array  $values
     * @return $this
     */
    public function inOrderOf($column, $values)
    {
        if ($values instanceof Arrayable) {
            $values = $values->toArray();
        }

        $values = array_values($values);

        if (empty($values)) {
            return $this;
        }

        $this->{$this->unions ? 'unionOrders' : 'orders'}[] = [
            'type' => 'InOrderOf',
            'column' => $column,
            'values' => $values,
        ];

        $this->addBinding($this->cleanBindings($values), $this->unions ? 'unionOrder' : 'order');

        return $this;
    }

    /**
     * Add a raw "order by" clause to the query.
     *
     * @param  string  $sql
     * @param  array  $bindings
     * @return $this
     */
    public function orderByRaw($sql, $bindings = [])
    {
        $type = 'Raw';

        $this->{$this->unions ? 'unionOrders' : 'orders'}[] = compact('type', 'sql');

        $this->addBinding($bindings, $this->unions ? 'unionOrder' : 'order');

        return $this;
    }

    /**
     * Alias to set the "offset" value of the query.
     *
     * @param  int  $value
     * @return $this
     */
    public function skip($value)
    {
        return $this->offset($value);
    }

    /**
     * Set the "offset" value of the query.
     *
     * @param  int  $value
     * @return $this
     */
    public function offset($value)
    {
        $property = $this->unions ? 'unionOffset' : 'offset';

        $this->$property = max(0, (int) $value);

        return $this;
    }

    /**
     * Alias to set the "limit" value of the query.
     *
     * @param  int  $value
     * @return $this
     */
    public function take($value)
    {
        return $this->limit($value);
    }

    /**
     * Set the "limit" value of the query.
     *
     * @param  int  $value
     * @return $this
     */
    public function limit($value)
    {
        $property = $this->unions ? 'unionLimit' : 'limit';

        if ($value >= 0) {
            $this->$property = ! is_null($value) ? (int) $value : null;
        }

        return $this;
    }

    /**
     * Add a "group limit" clause to the query.
     *
     * @param  int  $value
     * @param  string  $column
     * @return $this
     */
    public function groupLimit($value, $column)
    {
        if ($value >= 0) {
            $this->groupLimit = compact('value', 'column');
        }

        return $this;
    }

    /**
     * Set the limit and offset for a given page.
     *
     * @param  int  $page
     * @param  int  $perPage
     * @return $this
     */
    public function forPage($page, $perPage = 15)
    {
        return $this->offset(($page - 1) * $perPage)->limit($perPage);
    }

    /**
     * Constrain the query to the previous "page" of results before a given ID.
     *
     * @param  int  $perPage
     * @param  int|null  $lastId
     * @param  string  $column
     * @return $this
     */
    public function forPageBeforeId($perPage = 15, $lastId = 0, $column = 'id')
    {
        $this->orders = $this->removeExistingOrdersFor($column);

        if (is_null($lastId)) {
            $this->whereNotNull($column);
        } else {
            $this->where($column, '<', $lastId);
        }

        return $this->orderBy($column, 'desc')
            ->limit($perPage);
    }

    /**
     * Constrain the query to the next "page" of results after a given ID.
     *
     * @param  int  $perPage
     * @param  int|null  $lastId
     * @param  string  $column
     * @return $this
     */
    public function forPageAfterId($perPage = 15, $lastId = 0, $column = 'id')
    {
        $this->orders = $this->removeExistingOrdersFor($column);

        if (is_null($lastId)) {
            $this->whereNotNull($column);
        } else {
            $this->where($column, '>', $lastId);
        }

        return $this->orderBy($column, 'asc')
            ->limit($perPage);
    }

    /**
     * Remove all existing orders and optionally add a new order.
     *
     * @param  \Closure|\Illuminate\Database\Query\Builder|\Illuminate\Contracts\Database\Query\Expression|string|null  $column
     * @param  string  $direction
     * @return $this
     */
    public function reorder($column = null, $direction = 'asc')
    {
        $this->orders = null;
        $this->unionOrders = null;
        $this->bindings['order'] = [];
        $this->bindings['unionOrder'] = [];

        if ($column) {
            return $this->orderBy($column, $direction);
        }

        return $this;
    }

    /**
     * Add descending "reorder" clause to the query.
     *
     * @param  \Closure|\Illuminate\Database\Query\Builder|\Illuminate\Contracts\Database\Query\Expression|string|null  $column
     * @return $this
     */
    public function reorderDesc($column)
    {
        return $this->reorder($column, 'desc');
    }

    /**
     * Get an array with all orders with a given column removed.
     *
     * @param  string  $column
     * @return array
     */
    protected function removeExistingOrdersFor($column)
    {
        return (new Collection($this->orders))
            ->reject(fn ($order) => isset($order['column']) && $order['column'] === $column)
            ->values()
            ->all();
    }

    /**
     * Add a "union" statement to the query.
     *
     * @param  \Closure|\Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder<*>  $query
     * @param  bool  $all
     * @return $this
     */
    public function union($query, $all = false)
    {
        if ($query instanceof Closure) {
            $query($query = $this->newQuery());
        }

        $this->unions[] = compact('query', 'all');

        $this->addBinding($query->getBindings(), 'union');

        return $this;
    }

    /**
     * Add a "union all" statement to the query.
     *
     * @param  \Closure|\Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder<*>  $query
     * @return $this
     */
    public function unionAll($query)
    {
        return $this->union($query, true);
    }

    /**
     * Lock the selected rows in the table.
     *
     * @param  string|bool  $value
     * @return $this
     */
    public function lock($value = true)
    {
        $this->lock = $value;

        if (! is_null($this->lock)) {
            $this->useWritePdo();
        }

        return $this;
    }

    /**
     * Lock the selected rows in the table for updating.
     *
     * @return $this
     */
    public function lockForUpdate()
    {
        return $this->lock(true);
    }

    /**
     * Share lock the selected rows in the table.
     *
     * @return $this
     */
    public function sharedLock()
    {
        return $this->lock(false);
    }

    /**
     * Set a query execution timeout in seconds.
     *
     * @param  int|null  $seconds
     * @return $this
     *
     * @throws InvalidArgumentException
     */
    public function timeout(?int $seconds): static
    {
        if ($seconds !== null && $seconds <= 0) {
            throw new InvalidArgumentException('Timeout must be greater than zero.');
        }

        $this->timeout = $seconds;

        return $this;
    }

    /**
     * Register a closure to be invoked before the query is executed.
     *
     * @return $this
     */
    public function beforeQuery(callable $callback)
    {
        $this->beforeQueryCallbacks[] = $callback;

        return $this;
    }

    /**
     * Invoke the "before query" modification callbacks.
     *
     * @return void
     */
    public function applyBeforeQueryCallbacks()
    {
        foreach ($this->beforeQueryCallbacks as $callback) {
            $callback($this);
        }

        $this->beforeQueryCallbacks = [];
    }

    /**
     * Register a closure to be invoked after the query is executed.
     *
     * @return $this
     */
    public function afterQuery(Closure $callback)
    {
        $this->afterQueryCallbacks[] = $callback;

        return $this;
    }

    /**
     * Invoke the "after query" modification callbacks.
     *
     * @param  mixed  $result
     * @return mixed
     */
    public function applyAfterQueryCallbacks($result)
    {
        foreach ($this->afterQueryCallbacks as $afterQueryCallback) {
            $result = $afterQueryCallback($result) ?: $result;
        }

        return $result;
    }

    /**
     * Get the SQL representation of the query.
     *
     * @return string
     */
    public function toSql()
    {
        $this->applyBeforeQueryCallbacks();

        return $this->grammar->compileSelect($this);
    }

    /**
     * Get the raw SQL representation of the query with embedded bindings.
     *
     * @return string
     */
    public function toRawSql()
    {
        return $this->grammar->substituteBindingsIntoRawSql(
            $this->toSql(), $this->connection->prepareBindings($this->getBindings())
        );
    }

    /**
     * Execute a query for a single record by ID.
     *
     * @param  int|string  $id
     * @param  string|\Illuminate\Contracts\Database\Query\Expression|array<string|\Illuminate\Contracts\Database\Query\Expression>  $columns
     * @return \stdClass|null
     */
    public function find($id, $columns = ['*'])
    {
        return $this->where('id', '=', $id)->first($columns);
    }

    /**
     * Execute a query for a single record by ID or call a callback.
     *
     * @template TValue
     *
     * @param  mixed  $id
     * @param  (\Closure(): TValue)|string|\Illuminate\Contracts\Database\Query\Expression|array<string|\Illuminate\Contracts\Database\Query\Expression>  $columns
     * @param  (\Closure(): TValue)|null  $callback
     * @return \stdClass|TValue
     */
    public function findOr($id, $columns = ['*'], ?Closure $callback = null)
    {
        if ($columns instanceof Closure) {
            $callback = $columns;

            $columns = ['*'];
        }

        if (! is_null($data = $this->find($id, $columns))) {
            return $data;
        }

        return $callback();
    }

    /**
     * Get a single column's value from the first result of a query.
     *
     * @param  string  $column
     * @return mixed
     */
    public function value($column)
    {
        $result = (array) $this->first([$column]);

        return count($result) > 0 ? array_first($result) : null;
    }

    /**
     * Get a single expression value from the first result of a query.
     *
     * @return mixed
     */
    public function rawValue(string $expression, array $bindings = [])
    {
        $result = (array) $this->selectRaw($expression, $bindings)->first();

        return count($result) > 0 ? array_first($result) : null;
    }

    /**
     * Get a single column's value from the first result of a query if it's the sole matching record.
     *
     * @param  string  $column
     * @return mixed
     *
     * @throws \Illuminate\Database\RecordsNotFoundException
     * @throws \Illuminate\Database\MultipleRecordsFoundException
     */
    public function soleValue($column)
    {
        $result = (array) $this->sole([$column]);

        return array_first($result);
    }

    /**
     * Execute the query as a "select" statement.
     *
     * @param  string|\Illuminate\Contracts\Database\Query\Expression|array<string|\Illuminate\Contracts\Database\Query\Expression>  $columns
     * @return \Illuminate\Support\Collection<int, \stdClass>
     */
    public function get($columns = ['*'])
    {
        $items = new Collection($this->onceWithColumns(Arr::wrap($columns), function () {
            return $this->processor->processSelect($this, $this->runSelect());
        }));

        return $this->applyAfterQueryCallbacks(
            isset($this->groupLimit) ? $this->withoutGroupLimitKeys($items) : $items
        );
    }

    /**
     * Run the query as a "select" statement against the connection.
     *
     * @return array
     */
    protected function runSelect()
    {
        return $this->connection->select(
            $this->toSql(), $this->getBindings(), ! $this->useWritePdo
        );
    }

    /**
     * Remove the group limit keys from the results in the collection.
     *
     * @param  \Illuminate\Support\Collection  $items
     * @return \Illuminate\Support\Collection
     */
    protected function withoutGroupLimitKeys($items)
    {
        $keysToRemove = ['laravel_row'];

        if (is_string($this->groupLimit['column'])) {
            $column = last(explode('.', $this->groupLimit['column']));

            $keysToRemove[] = '@laravel_group := '.$this->grammar->wrap($column);
            $keysToRemove[] = '@laravel_group := '.$this->grammar->wrap('pivot_'.$column);
        }

        $items->each(function ($item) use ($keysToRemove) {
            foreach ($keysToRemove as $key) {
                unset($item->$key);
            }
        });

        return $items;
    }

    /**
     * Paginate the given query into a simple paginator.
     *
     * @param  int|\Closure  $perPage
     * @param  string|\Illuminate\Contracts\Database\Query\Expression|array<string|\Illuminate\Contracts\Database\Query\Expression>  $columns
     * @param  string  $pageName
     * @param  int|null  $page
     * @param  \Closure|int|null  $total
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginate($perPage = 15, $columns = ['*'], $pageName = 'page', $page = null, $total = null)
    {
        $page = $page ?: Paginator::resolveCurrentPage($pageName);

        $total = value($total) ?? $this->getCountForPagination();

        $perPage = value($perPage, $total);

        $results = $total ? $this->forPage($page, $perPage)->get($columns) : new Collection;

        return $this->paginator($results, $total, $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => $pageName,
        ]);
    }

    /**
     * Get a paginator only supporting simple next and previous links.
     *
     * This is more efficient on larger data-sets, etc.
     *
     * @param  int  $perPage
     * @param  string|\Illuminate\Contracts\Database\Query\Expression|array<string|\Illuminate\Contracts\Database\Query\Expression>  $columns
     * @param  string  $pageName
     * @param  int|null  $page
     * @return \Illuminate\Contracts\Pagination\Paginator
     */
    public function simplePaginate($perPage = 15, $columns = ['*'], $pageName = 'page', $page = null)
    {
        $page = $page ?: Paginator::resolveCurrentPage($pageName);

        $this->offset(($page - 1) * $perPage)->limit($perPage + 1);

        return $this->simplePaginator($this->get($columns), $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => $pageName,
        ]);
    }

    /**
     * Get a paginator only supporting simple next and previous links.
     *
     * This is more efficient on larger data-sets, etc.
     *
     * @param  int|null  $perPage
     * @param  string|\Illuminate\Contracts\Database\Query\Expression|array<string|\Illuminate\Contracts\Database\Query\Expression>  $columns
     * @param  string  $cursorName
     * @param  \Illuminate\Pagination\Cursor|string|null  $cursor
     * @return \Illuminate\Contracts\Pagination\CursorPaginator
     */
    public function cursorPaginate($perPage = 15, $columns = ['*'], $cursorName = 'cursor', $cursor = null)
    {
        return $this->paginateUsingCursor($perPage, $columns, $cursorName, $cursor);
    }

    /**
     * Ensure the proper order by required for cursor pagination.
     *
     * @param  bool  $shouldReverse
     * @return \Illuminate\Support\Collection
     */
    protected function ensureOrderForCursorPagination($shouldReverse = false)
    {
        if (empty($this->orders) && empty($this->unionOrders)) {
            $this->enforceOrderBy();
        }

        $reverseDirection = function ($order) {
            if (! isset($order['direction'])) {
                return $order;
            }

            $order['direction'] = $order['direction'] === 'asc' ? 'desc' : 'asc';

            return $order;
        };

        if ($shouldReverse) {
            $this->orders = (new Collection($this->orders))->map($reverseDirection)->toArray();
            $this->unionOrders = (new Collection($this->unionOrders))->map($reverseDirection)->toArray();
        }

        $orders = ! empty($this->unionOrders) ? $this->unionOrders : $this->orders;

        return (new Collection($orders))
            ->filter(fn ($order) => Arr::has($order, 'direction'))
            ->values();
    }

    /**
     * Get the count of the total records for the paginator.
     *
     * @param  array<string|\Illuminate\Contracts\Database\Query\Expression>  $columns
     * @return int<0, max>
     */
    public function getCountForPagination($columns = ['*'])
    {
        $results = $this->runPaginationCountQuery($columns);

        // Once we have run the pagination count query, we will get the resulting count and
        // take into account what type of query it was. When there is a group by we will
        // just return the count of the entire results set since that will be correct.
        if (! isset($results[0])) {
            return 0;
        } elseif (is_object($results[0])) {
            return (int) $results[0]->aggregate;
        }

        return (int) array_change_key_case((array) $results[0])['aggregate'];
    }

    /**
     * Run a pagination count query.
     *
     * @param  array<string|\Illuminate\Contracts\Database\Query\Expression>  $columns
     * @return array<mixed>
     */
    protected function runPaginationCountQuery($columns = ['*'])
    {
        if ($this->groups || $this->havings) {
            $clone = $this->cloneForPaginationCount();

            if (is_null($clone->columns) && ! empty($this->joins)) {
                $clone->select($this->from.'.*');
            }

            return $this->newQuery()
                ->from(new Expression('('.$clone->toSql().') as '.$this->grammar->wrap('aggregate_table')))
                ->mergeBindings($clone)
                ->setAggregate('count', $this->withoutSelectAliases($columns))
                ->get()->all();
        }

        $without = $this->unions ? ['unionOrders', 'unionLimit', 'unionOffset'] : ['columns', 'orders', 'limit', 'offset'];

        return $this->cloneWithout($without)
            ->cloneWithoutBindings($this->unions ? ['unionOrder'] : ['select', 'order'])
            ->setAggregate('count', $this->withoutSelectAliases($columns))
            ->get()->all();
    }

    /**
     * Clone the existing query instance for usage in a pagination subquery.
     *
     * @return self
     */
    protected function cloneForPaginationCount()
    {
        return $this->cloneWithout(['orders', 'limit', 'offset'])
            ->cloneWithoutBindings(['order']);
    }

    /**
     * Remove the column aliases since they will break count queries.
     *
     * @param  array<string|\Illuminate\Contracts\Database\Query\Expression>  $columns
     * @return array<string|\Illuminate\Contracts\Database\Query\Expression>
     */
    protected function withoutSelectAliases(array $columns)
    {
        return array_map(function ($column) {
            return is_string($column) && ($aliasPosition = stripos($column, ' as ')) !== false
                ? substr($column, 0, $aliasPosition)
                : $column;
        }, $columns);
    }

    /**
     * Get a lazy collection for the given query.
     *
     * @return \Illuminate\Support\LazyCollection<int, \stdClass>
     */
    public function cursor()
    {
        if (is_null($this->columns)) {
            $this->columns = ['*'];
        }

        return (new LazyCollection(function () {
            yield from $this->connection->cursor(
                $this->toSql(), $this->getBindings(), ! $this->useWritePdo
            );
        }))->map(function ($item) {
            return $this->applyAfterQueryCallbacks(new Collection([$item]))->first();
        })->reject(fn ($item) => is_null($item));
    }

    /**
     * Throw an exception if the query doesn't have an orderBy clause.
     *
     * @return void
     *
     * @throws \RuntimeException
     */
    protected function enforceOrderBy()
    {
        if (empty($this->orders) && empty($this->unionOrders)) {
            throw new RuntimeException('You must specify an orderBy clause when using this function.');
        }
    }

    /**
     * Get a collection instance containing the values of a given column.
     *
     * @param  \Illuminate\Contracts\Database\Query\Expression|string  $column
     * @param  string|null  $key
     * @return \Illuminate\Support\Collection<array-key, mixed>
     */
    public function pluck($column, $key = null)
    {
        // First, we will need to select the results of the query accounting for the
        // given columns / key. Once we have the results, we will be able to take
        // the results and get the exact data that was requested for the query.
        $queryResult = $this->onceWithColumns(
            is_null($key) || $key === $column ? [$column] : [$column, $key],
            function () {
                return $this->processor->processSelect(
                    $this, $this->runSelect()
                );
            }
        );

        if (empty($queryResult)) {
            return new Collection;
        }

        // If the columns are qualified with a table or have an alias, we cannot use
        // those directly in the "pluck" operations since the results from the DB
        // are only keyed by the column itself. We'll strip the table out here.
        $column = $this->stripTableForPluck($column);

        $key = $this->stripTableForPluck($key);

        return $this->applyAfterQueryCallbacks(
            is_array($queryResult[0])
                ? $this->pluckFromArrayColumn($queryResult, $column, $key)
                : $this->pluckFromObjectColumn($queryResult, $column, $key)
        );
    }

    /**
     * Strip off the table name or alias from a column identifier.
     *
     * @param  string  $column
     * @return string|null
     */
    protected function stripTableForPluck($column)
    {
        if (is_null($column)) {
            return $column;
        }

        $columnString = $column instanceof ExpressionContract
            ? $this->grammar->getValue($column)
            : $column;

        $separator = str_contains(strtolower($columnString), ' as ') ? ' as ' : '\.';

        return last(preg_split('~'.$separator.'~i', $columnString));
    }

    /**
     * Retrieve column values from rows represented as objects.
     *
     * @param  array  $queryResult
     * @param  string  $column
     * @param  string  $key
     * @return \Illuminate\Support\Collection
     */
    protected function pluckFromObjectColumn($queryResult, $column, $key)
    {
        $results = [];

        if (is_null($key)) {
            foreach ($queryResult as $row) {
                $results[] = $row->$column;
            }
        } else {
            foreach ($queryResult as $row) {
                $results[$row->$key] = $row->$column;
            }
        }

        return new Collection($results);
    }

    /**
     * Retrieve column values from rows represented as arrays.
     *
     * @param  array  $queryResult
     * @param  string  $column
     * @param  string  $key
     * @return \Illuminate\Support\Collection
     */
    protected function pluckFromArrayColumn($queryResult, $column, $key)
    {
        $results = [];

        if (is_null($key)) {
            foreach ($queryResult as $row) {
                $results[] = $row[$column];
            }
        } else {
            foreach ($queryResult as $row) {
                $results[$row[$key]] = $row[$column];
            }
        }

        return new Collection($results);
    }

    /**
     * Concatenate values of a given column as a string.
     *
     * @param  string  $column
     * @param  string  $glue
     * @return string
     */
    public function implode($column, $glue = '')
    {
        return $this->pluck($column)->implode($glue);
    }

    /**
     * Determine if any rows exist for the current query.
     *
     * @return bool
     */
    public function exists()
    {
        $this->applyBeforeQueryCallbacks();

        $results = $this->connection->select(
            $this->grammar->compileExists($this), $this->getBindings(), ! $this->useWritePdo
        );

        // If the results have rows, we will get the row and see if the exists column is a
        // boolean true. If there are no results for this query we will return false as
        // there are no rows for this query at all, and we can return that info here.
        if (isset($results[0])) {
            $results = (array) $results[0];

            return (bool) $results['exists'];
        }

        return false;
    }

    /**
     * Determine if no rows exist for the current query.
     *
     * @return bool
     */
    public function doesntExist()
    {
        return ! $this->exists();
    }

    /**
     * Execute the given callback if no rows exist for the current query.
     *
     * @return mixed
     */
    public function existsOr(Closure $callback)
    {
        return $this->exists() ? true : $callback();
    }

    /**
     * Execute the given callback if rows exist for the current query.
     *
     * @return mixed
     */
    public function doesntExistOr(Closure $callback)
    {
        return $this->doesntExist() ? true : $callback();
    }

    /**
     * Retrieve the "count" result of the query.
     *
     * @param  \Illuminate\Contracts\Database\Query\Expression|string  $columns
     * @return int<0, max>
     */
    public function count($columns = '*')
    {
        return (int) $this->aggregate(__FUNCTION__, Arr::wrap($columns));
    }

    /**
     * Retrieve the minimum value of a given column.
     *
     * @param  \Illuminate\Contracts\Database\Query\Expression|string  $column
     * @return mixed
     */
    public function min($column)
    {
        return $this->aggregate(__FUNCTION__, [$column]);
    }

    /**
     * Retrieve the maximum value of a given column.
     *
     * @param  \Illuminate\Contracts\Database\Query\Expression|string  $column
     * @return mixed
     */
    public function max($column)
    {
        return $this->aggregate(__FUNCTION__, [$column]);
    }

    /**
     * Retrieve the sum of the values of a given column.
     *
     * @param  \Illuminate\Contracts\Database\Query\Expression|string  $column
     * @return mixed
     */
    public function sum($column)
    {
        $result = $this->aggregate(__FUNCTION__, [$column]);

        return $result ?: 0;
    }

    /**
     * Retrieve the average of the values of a given column.
     *
     * @param  \Illuminate\Contracts\Database\Query\Expression|string  $column
     * @return mixed
     */
    public function avg($column)
    {
        return $this->aggregate(__FUNCTION__, [$column]);
    }

    /**
     * Alias for the "avg" method.
     *
     * @param  \Illuminate\Contracts\Database\Query\Expression|string  $column
     * @return mixed
     */
    public function average($column)
    {
        return $this->avg($column);
    }

    /**
     * Execute an aggregate function on the database.
     *
     * @param  string  $function
     * @param  array  $columns
     * @return mixed
     */
    public function aggregate($function, $columns = ['*'])
    {
        $results = $this->cloneWithout($this->unions || $this->havings ? [] : ['columns'])
            ->cloneWithoutBindings($this->unions || $this->havings ? [] : ['select'])
            ->setAggregate($function, $columns)
            ->get($columns);

        if (! $results->isEmpty()) {
            return array_change_key_case((array) $results[0])['aggregate'];
        }
    }

    /**
     * Execute a numeric aggregate function on the database.
     *
     * @param  string  $function
     * @param  array  $columns
     * @return float|int
     */
    public function numericAggregate($function, $columns = ['*'])
    {
        $result = $this->aggregate($function, $columns);

        // If there is no result, we can obviously just return 0 here. Next, we will check
        // if the result is an integer or float. If it is already one of these two data
        // types we can just return the result as-is, otherwise we will convert this.
        if (! $result) {
            return 0;
        }

        if (is_int($result) || is_float($result)) {
            return $result;
        }

        // If the result doesn't contain a decimal place, we will assume it is an int then
        // cast it to one. When it does we will cast it to a float since it needs to be
        // cast to the expected data type for the developers out of pure convenience.
        return ! str_contains((string) $result, '.')
            ? (int) $result
            : (float) $result;
    }

    /**
     * Set the aggregate property without running the query.
     *
     * @param  string  $function
     * @param  array<\Illuminate\Contracts\Database\Query\Expression|string>  $columns
     * @return $this
     */
    protected function setAggregate($function, $columns)
    {
        $this->aggregate = compact('function', 'columns');

        if (empty($this->groups)) {
            $this->orders = null;

            $this->bindings['order'] = [];
        }

        return $this;
    }

    /**
     * Execute the given callback while selecting the given columns.
     *
     * After running the callback, the columns are reset to the original value.
     *
     * @template TResult
     *
     * @param  array<string|\Illuminate\Contracts\Database\Query\Expression>  $columns
     * @param  callable(): TResult  $callback
     * @return TResult
     */
    protected function onceWithColumns($columns, $callback)
    {
        $original = $this->columns;

        if (is_null($original)) {
            $this->columns = $columns;
        }

        $result = $callback();

        $this->columns = $original;

        return $result;
    }

    /**
     * Insert new records into the database.
     *
     * @return bool
     */
    public function insert(array $values)
    {
        // Since every insert gets treated like a batch insert, we will make sure the
        // bindings are structured in a way that is convenient when building these
        // inserts statements by verifying these elements are actually an array.
        if (empty($values)) {
            return true;
        }

        if (! is_array(array_first($values))) {
            $values = [$values];
        }

        // Here, we will sort the insert keys for every record so that each insert is
        // in the same order for the record. We need to make sure this is the case
        // so there are not any errors or problems when inserting these records.
        else {
            foreach ($values as $key => $value) {
                ksort($value);

                $values[$key] = $value;
            }
        }

        $this->applyBeforeQueryCallbacks();

        // Finally, we will run this query against the database connection and return
        // the results. We will need to also flatten these bindings before running
        // the query so they are all in one huge, flattened array for execution.
        return $this->connection->insert(
            $this->grammar->compileInsert($this, $values),
            $this->cleanBindings(Arr::flatten($values, 1))
        );
    }

    /**
     * Insert new records into the database while ignoring errors.
     *
     * @return int<0, max>
     */
    public function insertOrIgnore(array $values)
    {
        if (empty($values)) {
            return 0;
        }

        if (! is_array(array_first($values))) {
            $values = [$values];
        } else {
            foreach ($values as $key => $value) {
                ksort($value);

                $values[$key] = $value;
            }
        }

        $this->applyBeforeQueryCallbacks();

        return $this->connection->affectingStatement(
            $this->grammar->compileInsertOrIgnore($this, $values),
            $this->cleanBindings(Arr::flatten($values, 1))
        );
    }

    /**
     * Insert a new record and get the value of the primary key.
     *
     * @param  string|null  $sequence
     * @return int
     */
    public function insertGetId(array $values, $sequence = null)
    {
        $this->applyBeforeQueryCallbacks();

        $sql = $this->grammar->compileInsertGetId($this, $values, $sequence);

        $values = $this->cleanBindings($values);

        return $this->processor->processInsertGetId($this, $sql, $values, $sequence);
    }

    /**
     * Insert new records into the table using a subquery.
     *
     * @param  \Closure|\Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder<*>|string  $query
     * @return int
     */
    public function insertUsing(array $columns, $query)
    {
        $this->applyBeforeQueryCallbacks();

        [$sql, $bindings] = $this->createSub($query);

        return $this->connection->affectingStatement(
            $this->grammar->compileInsertUsing($this, $columns, $sql),
            $this->cleanBindings($bindings)
        );
    }

    /**
     * Insert new records into the table using a subquery while ignoring errors.
     *
     * @param  \Closure|\Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder<*>|string  $query
     * @return int
     */
    public function insertOrIgnoreUsing(array $columns, $query)
    {
        $this->applyBeforeQueryCallbacks();

        [$sql, $bindings] = $this->createSub($query);

        return $this->connection->affectingStatement(
            $this->grammar->compileInsertOrIgnoreUsing($this, $columns, $sql),
            $this->cleanBindings($bindings)
        );
    }

    /**
     * Update records in the database.
     *
     * @return int<0, max>
     */
    public function update(array $values)
    {
        $this->applyBeforeQueryCallbacks();

        $values = (new Collection($values))->map(function ($value) {
            if (! $value instanceof self && ! $value instanceof EloquentBuilder && ! $value instanceof Relation) {
                return ['value' => $value, 'bindings' => match (true) {
                    $value instanceof Collection => $value->all(),
                    $value instanceof UnitEnum => enum_value($value),
                    default => $value,
                }];
            }

            [$query, $bindings] = $this->parseSub($value);

            return ['value' => new Expression("({$query})"), 'bindings' => fn () => $bindings];
        });

        $sql = $this->grammar->compileUpdate($this, $values->map(fn ($value) => $value['value'])->all());

        return $this->connection->update($sql, $this->cleanBindings(
            $this->grammar->prepareBindingsForUpdate($this->bindings, $values->map(fn ($value) => $value['bindings'])->all())
        ));
    }

    /**
     * Update records in a PostgreSQL database using the update from syntax.
     *
     * @return int
     */
    public function updateFrom(array $values)
    {
        if (! method_exists($this->grammar, 'compileUpdateFrom')) {
            throw new LogicException('This database engine does not support the updateFrom method.');
        }

        $this->applyBeforeQueryCallbacks();

        $sql = $this->grammar->compileUpdateFrom($this, $values);

        return $this->connection->update($sql, $this->cleanBindings(
            $this->grammar->prepareBindingsForUpdateFrom($this->bindings, $values)
        ));
    }

    /**
     * Insert or update a record matching the attributes, and fill it with values.
     *
     * @return bool
     */
    public function updateOrInsert(array $attributes, array|callable $values = [])
    {
        $exists = $this->where($attributes)->exists();

        if ($values instanceof Closure) {
            $values = $values($exists);
        }

        if (! $exists) {
            return $this->insert(array_merge($attributes, $values));
        }

        if (empty($values)) {
            return true;
        }

        return (bool) $this->limit(1)->update($values);
    }

    /**
     * Insert new records or update the existing ones.
     *
     * @return int
     */
    public function upsert(array $values, array|string $uniqueBy, ?array $update = null)
    {
        if (empty($values)) {
            return 0;
        } elseif ($update === []) {
            return (int) $this->insert($values);
        }

        if (! is_array(array_first($values))) {
            $values = [$values];
        } else {
            foreach ($values as $key => $value) {
                ksort($value);

                $values[$key] = $value;
            }
        }

        if (is_null($update)) {
            $update = array_keys(array_first($values));
        }

        $this->applyBeforeQueryCallbacks();

        $bindings = $this->cleanBindings(array_merge(
            Arr::flatten($values, 1),
            (new Collection($update))
                ->reject(fn ($value, $key) => is_int($key))
                ->all()
        ));

        return $this->connection->affectingStatement(
            $this->grammar->compileUpsert($this, $values, (array) $uniqueBy, $update),
            $bindings
        );
    }

    /**
     * Increment a column's value by a given amount.
     *
     * @param  string  $column
     * @param  float|int  $amount
     * @return int<0, max>
     *
     * @throws \InvalidArgumentException
     */
    public function increment($column, $amount = 1, array $extra = [])
    {
        if (! is_numeric($amount)) {
            throw new InvalidArgumentException('Non-numeric value passed to increment method.');
        }

        return $this->incrementEach([$column => $amount], $extra);
    }

    /**
     * Increment the given column's values by the given amounts.
     *
     * @param  array<string, float|int|numeric-string>  $columns
     * @param  array<string, mixed>  $extra
     * @return int<0, max>
     *
     * @throws \InvalidArgumentException
     */
    public function incrementEach(array $columns, array $extra = [])
    {
        foreach ($columns as $column => $amount) {
            if (! is_numeric($amount)) {
                throw new InvalidArgumentException("Non-numeric value passed as increment amount for column: '$column'.");
            } elseif (! is_string($column)) {
                throw new InvalidArgumentException('Non-associative array passed to incrementEach method.');
            }

            $columns[$column] = $this->raw("{$this->grammar->wrap($column)} + $amount");
        }

        return $this->update(array_merge($columns, $extra));
    }

    /**
     * Decrement a column's value by a given amount.
     *
     * @param  string  $column
     * @param  float|int  $amount
     * @return int<0, max>
     *
     * @throws \InvalidArgumentException
     */
    public function decrement($column, $amount = 1, array $extra = [])
    {
        if (! is_numeric($amount)) {
            throw new InvalidArgumentException('Non-numeric value passed to decrement method.');
        }

        return $this->decrementEach([$column => $amount], $extra);
    }

    /**
     * Decrement the given column's values by the given amounts.
     *
     * @param  array<string, float|int|numeric-string>  $columns
     * @param  array<string, mixed>  $extra
     * @return int<0, max>
     *
     * @throws \InvalidArgumentException
     */
    public function decrementEach(array $columns, array $extra = [])
    {
        foreach ($columns as $column => $amount) {
            if (! is_numeric($amount)) {
                throw new InvalidArgumentException("Non-numeric value passed as decrement amount for column: '$column'.");
            } elseif (! is_string($column)) {
                throw new InvalidArgumentException('Non-associative array passed to decrementEach method.');
            }

            $columns[$column] = $this->raw("{$this->grammar->wrap($column)} - $amount");
        }

        return $this->update(array_merge($columns, $extra));
    }

    /**
     * Delete records from the database.
     *
     * @param  mixed  $id
     * @return int
     */
    public function delete($id = null)
    {
        // If an ID is passed to the method, we will set the where clause to check the
        // ID to let developers to simply and quickly remove a single row from this
        // database without manually specifying the "where" clauses on the query.
        if (! is_null($id)) {
            $this->where($this->from.'.id', '=', $id);
        }

        $this->applyBeforeQueryCallbacks();

        return $this->connection->delete(
            $this->grammar->compileDelete($this), $this->cleanBindings(
                $this->grammar->prepareBindingsForDelete($this->bindings)
            )
        );
    }

    /**
     * Run a "truncate" statement on the table.
     *
     * @return void
     */
    public function truncate()
    {
        $this->applyBeforeQueryCallbacks();

        foreach ($this->grammar->compileTruncate($this) as $sql => $bindings) {
            $this->connection->statement($sql, $bindings);
        }
    }

    /**
     * Get a new instance of the query builder.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function newQuery()
    {
        return new static($this->connection, $this->grammar, $this->processor);
    }

    /**
     * Create a new query instance for a sub-query.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function forSubQuery()
    {
        return $this->newQuery();
    }

    /**
     * Get all of the query builder's columns in a text-only array with all expressions evaluated.
     *
     * @return list<string>
     */
    public function getColumns()
    {
        return ! is_null($this->columns)
            ? array_map(fn ($column) => $this->grammar->getValue($column), $this->columns)
            : [];
    }

    /**
     * Create a raw database expression.
     *
     * @param  mixed  $value
     * @return \Illuminate\Contracts\Database\Query\Expression
     */
    public function raw($value)
    {
        return $this->connection->raw($value);
    }

    /**
     * Get the query builder instances that are used in the union of the query.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getUnionBuilders()
    {
        return isset($this->unions)
            ? (new Collection($this->unions))->pluck('query')
            : new Collection;
    }

    /**
     * Get the "limit" value for the query or null if it's not set.
     *
     * @return mixed
     */
    public function getLimit()
    {
        $value = $this->unions ? $this->unionLimit : $this->limit;

        return ! is_null($value) ? (int) $value : null;
    }

    /**
     * Get the "offset" value for the query or null if it's not set.
     *
     * @return mixed
     */
    public function getOffset()
    {
        $value = $this->unions ? $this->unionOffset : $this->offset;

        return ! is_null($value) ? (int) $value : null;
    }

    /**
     * Get the current query value bindings in a flattened array.
     *
     * @return list<mixed>
     */
    public function getBindings()
    {
        return Arr::flatten($this->bindings);
    }

    /**
     * Get the raw array of bindings.
     *
     * @return array{
     *      select: list<mixed>,
     *      from: list<mixed>,
     *      join: list<mixed>,
     *      where: list<mixed>,
     *      groupBy: list<mixed>,
     *      having: list<mixed>,
     *      order: list<mixed>,
     *      union: list<mixed>,
     *      unionOrder: list<mixed>,
     * }
     */
    public function getRawBindings()
    {
        return $this->bindings;
    }

    /**
     * Set the bindings on the query builder.
     *
     * @param  list<mixed>  $bindings
     * @param  "select"|"from"|"join"|"where"|"groupBy"|"having"|"order"|"union"|"unionOrder"  $type
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function setBindings(array $bindings, $type = 'where')
    {
        if (! array_key_exists($type, $this->bindings)) {
            throw new InvalidArgumentException("Invalid binding type: {$type}.");
        }

        $this->bindings[$type] = $bindings;

        return $this;
    }

    /**
     * Add a binding to the query.
     *
     * @param  mixed  $value
     * @param  "select"|"from"|"join"|"where"|"groupBy"|"having"|"order"|"union"|"unionOrder"  $type
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function addBinding($value, $type = 'where')
    {
        if (! array_key_exists($type, $this->bindings)) {
            throw new InvalidArgumentException("Invalid binding type: {$type}.");
        }

        if (is_array($value)) {
            $this->bindings[$type] = array_values(array_map(
                $this->castBinding(...),
                array_merge($this->bindings[$type], $value),
            ));
        } else {
            $this->bindings[$type][] = $this->castBinding($value);
        }

        return $this;
    }

    /**
     * Cast the given binding value.
     *
     * @param  mixed  $value
     * @return mixed
     */
    public function castBinding($value)
    {
        return enum_value($value);
    }

    /**
     * Merge an array of bindings into our bindings.
     *
     * @param  self  $query
     * @return $this
     */
    public function mergeBindings(self $query)
    {
        $this->bindings = array_merge_recursive($this->bindings, $query->bindings);

        return $this;
    }

    /**
     * Remove all of the expressions from a list of bindings.
     *
     * @param  array<mixed>  $bindings
     * @return list<mixed>
     */
    public function cleanBindings(array $bindings)
    {
        return (new Collection($bindings))
            ->reject(function ($binding) {
                return $binding instanceof ExpressionContract;
            })
            ->map($this->castBinding(...))
            ->values()
            ->all();
    }

    /**
     * Get a scalar type value from an unknown type of input.
     *
     * @param  mixed  $value
     * @return mixed
     */
    protected function flattenValue($value)
    {
        return is_array($value) ? head(Arr::flatten($value)) : $value;
    }

    /**
     * Get the default key name of the table.
     *
     * @return string
     */
    protected function defaultKeyName()
    {
        return 'id';
    }

    /**
     * Get the database connection instance.
     *
     * @return \Illuminate\Database\ConnectionInterface
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Ensure the database connection supports vector queries.
     *
     * @return void
     */
    protected function ensureConnectionSupportsVectors()
    {
        if (! $this->connection instanceof PostgresConnection) {
            throw new RuntimeException('Vector distance queries are only supported by Postgres.');
        }
    }

    /**
     * Get the database query processor instance.
     *
     * @return \Illuminate\Database\Query\Processors\Processor
     */
    public function getProcessor()
    {
        return $this->processor;
    }

    /**
     * Get the query grammar instance.
     *
     * @return \Illuminate\Database\Query\Grammars\Grammar
     */
    public function getGrammar()
    {
        return $this->grammar;
    }

    /**
     * Use the "write" PDO connection when executing the query.
     *
     * @return $this
     */
    public function useWritePdo()
    {
        $this->useWritePdo = true;

        return $this;
    }

    /**
     * Determine if the value is a query builder instance or a Closure.
     *
     * @param  mixed  $value
     * @return bool
     */
    protected function isQueryable($value)
    {
        return $value instanceof self ||
               $value instanceof EloquentBuilder ||
               $value instanceof Relation ||
               $value instanceof Closure;
    }

    /**
     * Clone the query.
     *
     * @return static
     */
    public function clone()
    {
        return clone $this;
    }

    /**
     * Clone the query without the given properties.
     *
     * @return static
     */
    public function cloneWithout(array $properties)
    {
        return tap($this->clone(), function ($clone) use ($properties) {
            foreach ($properties as $property) {
                $clone->{$property} = null;
            }
        });
    }

    /**
     * Clone the query without the given bindings.
     *
     * @return static
     */
    public function cloneWithoutBindings(array $except)
    {
        return tap($this->clone(), function ($clone) use ($except) {
            foreach ($except as $type) {
                $clone->bindings[$type] = [];
            }
        });
    }

    /**
     * Dump the current SQL and bindings.
     *
     * @param  mixed  ...$args
     * @return $this
     */
    public function dump(...$args)
    {
        dump(
            $this->toSql(),
            $this->getBindings(),
            ...$args,
        );

        return $this;
    }

    /**
     * Dump the raw current SQL with embedded bindings.
     *
     * @return $this
     */
    public function dumpRawSql()
    {
        dump($this->toRawSql());

        return $this;
    }

    /**
     * Die and dump the current SQL and bindings.
     *
     * @return never
     */
    public function dd()
    {
        dd($this->toSql(), $this->getBindings());
    }

    /**
     * Die and dump the current SQL with embedded bindings.
     *
     * @return never
     */
    public function ddRawSql()
    {
        dd($this->toRawSql());
    }

    /**
     * Handle dynamic method calls into the method.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     *
     * @throws \BadMethodCallException
     */
    public function __call($method, $parameters)
    {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        if (str_starts_with($method, 'where')) {
            return $this->dynamicWhere($method, $parameters);
        }

        static::throwBadMethodCallException($method);
    }
}
