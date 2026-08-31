<?php

namespace Illuminate\Database\Eloquent\Relations\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use InvalidArgumentException;

trait CanBeOneOfMany
{
    /**
     * Determines whether the relationship is one-of-many.
     *
     * @var bool
     */
    protected $isOneOfMany = false;

    /**
     * The name of the relationship.
     *
     * @var string
     */
    protected $relationName;

    /**
     * The one of many inner join subselect query builder instance.
     *
     * @var \Illuminate\Database\Eloquent\Builder<*>|null
     */
    protected $oneOfManySubQuery;

    /**
     * Add constraints for inner join subselect for one of many relationships.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<*>  $query
     * @param  string|null  $column
     * @param  string|null  $aggregate
     * @return void
     */
    abstract public function addOneOfManySubQueryConstraints(Builder $query, $column = null, $aggregate = null);

    /**
     * Get the columns the determine the relationship groups.
     *
     * @return array|string
     */
    abstract public function getOneOfManySubQuerySelectColumns();

    /**
     * Add join query constraints for one of many relationships.
     *
     * @param  \Illuminate\Database\Query\JoinClause  $join
     * @return void
     */
    abstract public function addOneOfManyJoinSubQueryConstraints(JoinClause $join);

    /**
     * Indicate that the relation is a single result of a larger one-to-many relationship.
     *
     * @param  string|array|null  $column
     * @param  string|\Closure|null  $aggregate
     * @param  string|null  $relation
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function ofMany($column = 'id', $aggregate = 'MAX', $relation = null)
    {
        $this->isOneOfMany = true;

        $this->relationName = $relation ?: $this->getDefaultOneOfManyJoinAlias(
            $this->guessRelationship()
        );

        $keyName = $this->query->getModel()->getKeyName();

        $columns = is_string($columns = $column) ? [
            $column => $aggregate,
            $keyName => $aggregate,
        ] : $column;

        if (! array_key_exists($keyName, $columns)) {
            $columns[$keyName] = 'MAX';
        }

        if ($aggregate instanceof Closure) {
            $closure = $aggregate;
        }

        foreach ($columns as $column => $aggregate) {
            if (! in_array(strtolower($aggregate), ['min', 'max'])) {
                throw new InvalidArgumentException("Invalid aggregate [{$aggregate}] used within ofMany relation. Available aggregates: MIN, MAX");
            }

            $subQuery = $this->newOneOfManySubQuery(
                $this->getOneOfManySubQuerySelectColumns(),
                array_merge([$column], $previous['columns'] ?? []),
                $aggregate,
            );

            if (isset($previous)) {
                $this->addOneOfManyJoinSubQuery(
                    $subQuery,
                    $previous['subQuery'],
                    $previous['columns'],
                );
            }

            if (isset($closure)) {
                $closure($subQuery);
            }

            if (! isset($previous)) {
                $this->oneOfManySubQuery = $subQuery;
            }

            if (array_key_last($columns) == $column) {
                $this->addOneOfManyJoinSubQuery(
                    $this->query,
                    $subQuery,
                    array_merge([$column], $previous['columns'] ?? []),
                );
            }

            $previous = [
                'subQuery' => $subQuery,
                'columns' => array_merge([$column], $previous['columns'] ?? []),
            ];
        }

        $this->addConstraints();

        $columns = $this->query->getQuery()->columns;

        if (is_null($columns) || $columns === ['*']) {
            $this->select([$this->qualifyColumn('*')]);
        }

        return $this;
    }

    /**
     * Indicate that the relation is the latest single result of a larger one-to-many relationship.
     *
     * @param  string|array|null  $column
     * @param  string|null  $relation
     * @return $this
     */
    public function latestOfMany($column = 'id', $relation = null)
    {
        return $this->ofMany(Collection::wrap($column)->mapWithKeys(function ($column) {
            return [$column => 'MAX'];
        })->all(), 'MAX', $relation);
    }

    /**
     * Indicate that the relation is the oldest single result of a larger one-to-many relationship.
     *
     * @param  string|array|null  $column
     * @param  string|null  $relation
     * @return $this
     */
    public function oldestOfMany($column = 'id', $relation = null)
    {
        return $this->ofMany(Collection::wrap($column)->mapWithKeys(function ($column) {
            return [$column => 'MIN'];
        })->all(), 'MIN', $relation);
    }

    /**
     * Get the default alias for the one of many inner join clause.
     *
     * @param  string  $relation
     * @return string
     */
    protected function getDefaultOneOfManyJoinAlias($relation)
    {
        return $relation == $this->query->getModel()->getTable()
            ? $relation.'_of_many'
            : $relation;
    }

    /**
     * Get a new query for the related model, grouping the query by the given column, often the foreign key of the relationship.
     *
     * @param  string|array  $groupBy
     * @param  array<string>|null  $columns
     * @param  string|null  $aggregate
     * @return \Illuminate\Database\Eloquent\Builder<*>
     */
    protected function newOneOfManySubQuery($groupBy, $columns = null, $aggregate = null)
    {
        $subQuery = $this->query->getModel()
            ->newQuery()
            ->withoutGlobalScopes($this->removedScopes());

        foreach (Arr::wrap($groupBy) as $group) {
            $subQuery->groupBy($this->qualifyRelatedColumn($group));
        }

        if (! is_null($columns)) {
            foreach ($columns as $key => $column) {
                $aggregatedColumn = $subQuery->getQuery()->grammar->wrap($subQuery->qualifyColumn($column));

                if ($key === 0) {
                    $aggregatedColumn = "{$aggregate}({$aggregatedColumn})";
                } else {
                    $aggregatedColumn = "min({$aggregatedColumn})";
                }

                $subQuery->selectRaw($aggregatedColumn.' as '.$subQuery->getQuery()->grammar->wrap($column.'_aggregate'));
            }
        }

        $this->addOneOfManySubQueryConstraints($subQuery, column: null, aggregate: $aggregate);

        return $subQuery;
    }

    /**
     * Add the join subquery to the given query on the given column and the relationship's foreign key.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<*>  $parent
     * @param  \Illuminate\Database\Eloquent\Builder<*>  $subQuery
     * @param  array<string>  $on
     * @return void
     */
    protected function addOneOfManyJoinSubQuery(Builder $parent, Builder $subQuery, $on)
    {
        $parent->beforeQuery(function ($parent) use ($subQuery, $on) {
            $subQuery->applyBeforeQueryCallbacks();

            $parent->joinSub($subQuery, $this->relationName, function ($join) use ($on) {
                foreach ($on as $onColumn) {
                    $join->on($this->qualifySubSelectColumn($onColumn.'_aggregate'), '=', $this->qualifyRelatedColumn($onColumn));
                }

                $this->addOneOfManyJoinSubQueryConstraints($join);
            });
        });
    }

    /**
     * Merge the relationship query joins to the given query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<*>  $query
     * @return void
     */
    protected function mergeOneOfManyJoinsTo(Builder $query)
    {
        $query->getQuery()->beforeQueryCallbacks = $this->query->getQuery()->beforeQueryCallbacks;

        $query->applyBeforeQueryCallbacks();
    }

    /**
     * Get the query builder that will contain the relationship constraints.
     *
     * @return \Illuminate\Database\Eloquent\Builder<*>
     */
    protected function getRelationQuery()
    {
        return $this->isOneOfMany()
            ? $this->oneOfManySubQuery
            : $this->query;
    }

    /**
     * Get the one of many inner join subselect builder instance.
     *
     * @return \Illuminate\Database\Eloquent\Builder<*>|void
     */
    public function getOneOfManySubQuery()
    {
        return $this->oneOfManySubQuery;
    }

    /**
     * Get the qualified column name for the one-of-many relationship using the subselect join query's alias.
     *
     * @param  string  $column
     * @return string
     */
    public function qualifySubSelectColumn($column)
    {
        return $this->getRelationName().'.'.last(explode('.', $column));
    }

    /**
     * Qualify related column using the related table name if it is not already qualified.
     *
     * @param  string  $column
     * @return string
     */
    protected function qualifyRelatedColumn($column)
    {
        return $this->query->getModel()->qualifyColumn($column);
    }

    /**
     * Guess the "hasOne" relationship's name via backtrace.
     *
     * @return string
     */
    protected function guessRelationship()
    {
        return debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3)[2]['function'];
    }

    /**
     * Determine whether the relationship is a one-of-many relationship.
     *
     * @return bool
     */
    public function isOneOfMany()
    {
        return $this->isOneOfMany;
    }

    /**
     * Get the name of the relationship.
     *
     * @return string
     */
    public function getRelationName()
    {
        return $this->relationName;
    }
}
