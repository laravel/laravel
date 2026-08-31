<?php

namespace Illuminate\Database\Eloquent\Relations;

use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\Concerns\AsPivot;
use Illuminate\Database\Eloquent\Relations\Concerns\InteractsWithDictionary;
use Illuminate\Database\Eloquent\Relations\Concerns\InteractsWithPivotTable;
use Illuminate\Database\Query\Grammars\MySqlGrammar;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection as BaseCollection;
use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * @template TRelatedModel of \Illuminate\Database\Eloquent\Model
 * @template TDeclaringModel of \Illuminate\Database\Eloquent\Model
 * @template TPivotModel of \Illuminate\Database\Eloquent\Relations\Pivot = \Illuminate\Database\Eloquent\Relations\Pivot
 * @template TAccessor of string = 'pivot'
 *
 * @extends \Illuminate\Database\Eloquent\Relations\Relation<TRelatedModel, TDeclaringModel, \Illuminate\Database\Eloquent\Collection<int, TRelatedModel&object{pivot: TPivotModel}>>
 *
 * @todo use TAccessor when PHPStan bug is fixed: https://github.com/phpstan/phpstan/issues/12756
 */
class BelongsToMany extends Relation
{
    use InteractsWithDictionary, InteractsWithPivotTable;

    /**
     * The intermediate table for the relation.
     *
     * @var string
     */
    protected $table;

    /**
     * The foreign key of the parent model.
     *
     * @var string
     */
    protected $foreignPivotKey;

    /**
     * The associated key of the relation.
     *
     * @var string
     */
    protected $relatedPivotKey;

    /**
     * The key name of the parent model.
     *
     * @var string
     */
    protected $parentKey;

    /**
     * The key name of the related model.
     *
     * @var string
     */
    protected $relatedKey;

    /**
     * The "name" of the relationship.
     *
     * @var string
     */
    protected $relationName;

    /**
     * The pivot table columns to retrieve.
     *
     * @var array<string|\Illuminate\Contracts\Database\Query\Expression>
     */
    protected $pivotColumns = [];

    /**
     * Any pivot table restrictions for where clauses.
     *
     * @var array
     */
    protected $pivotWheres = [];

    /**
     * Any pivot table restrictions for whereIn clauses.
     *
     * @var array
     */
    protected $pivotWhereIns = [];

    /**
     * Any pivot table restrictions for whereNull clauses.
     *
     * @var array
     */
    protected $pivotWhereNulls = [];

    /**
     * The default values for the pivot columns.
     *
     * @var array
     */
    protected $pivotValues = [];

    /**
     * Indicates if timestamps are available on the pivot table.
     *
     * @var bool
     */
    public $withTimestamps = false;

    /**
     * The custom pivot table column for the created_at timestamp.
     *
     * @var string|null
     */
    protected $pivotCreatedAt;

    /**
     * The custom pivot table column for the updated_at timestamp.
     *
     * @var string|null
     */
    protected $pivotUpdatedAt;

    /**
     * The class name of the custom pivot model to use for the relationship.
     *
     * @var class-string<TPivotModel>
     */
    protected $using;

    /**
     * The name of the accessor to use for the "pivot" relationship.
     *
     * @var TAccessor
     */
    protected $accessor = 'pivot';

    /**
     * Create a new belongs to many relationship instance.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<TRelatedModel>  $query
     * @param  TDeclaringModel  $parent
     * @param  string|class-string<TRelatedModel>  $table
     * @param  string  $foreignPivotKey
     * @param  string  $relatedPivotKey
     * @param  string  $parentKey
     * @param  string  $relatedKey
     * @param  string|null  $relationName
     */
    public function __construct(
        Builder $query,
        Model $parent,
        $table,
        $foreignPivotKey,
        $relatedPivotKey,
        $parentKey,
        $relatedKey,
        $relationName = null,
    ) {
        $this->parentKey = $parentKey;
        $this->relatedKey = $relatedKey;
        $this->relationName = $relationName;
        $this->relatedPivotKey = $relatedPivotKey;
        $this->foreignPivotKey = $foreignPivotKey;
        $this->table = $this->resolveTableName($table);

        parent::__construct($query, $parent);
    }

    /**
     * Attempt to resolve the intermediate table name from the given string.
     *
     * @param  string  $table
     * @return string
     */
    protected function resolveTableName($table)
    {
        if (! str_contains($table, '\\') || ! class_exists($table)) {
            return $table;
        }

        $model = new $table;

        if (! $model instanceof Model) {
            return $table;
        }

        if (in_array(AsPivot::class, class_uses_recursive($model))) {
            $this->using($table);
        }

        return $model->getTable();
    }

    /**
     * Set the base constraints on the relation query.
     *
     * @return void
     */
    public function addConstraints()
    {
        $this->performJoin();

        if (static::$constraints) {
            $this->addWhereConstraints();
        }
    }

    /**
     * Set the join clause for the relation query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<TRelatedModel>|null  $query
     * @return $this
     */
    protected function performJoin($query = null)
    {
        $query = $query ?: $this->query;

        // We need to join to the intermediate table on the related model's primary
        // key column with the intermediate table's foreign key for the related
        // model instance. Then we can set the "where" for the parent models.
        $query->join(
            $this->table,
            $this->getQualifiedRelatedKeyName(),
            '=',
            $this->getQualifiedRelatedPivotKeyName()
        );

        return $this;
    }

    /**
     * Set the where clause for the relation query.
     *
     * @return $this
     */
    protected function addWhereConstraints()
    {
        $this->query->where(
            $this->getQualifiedForeignPivotKeyName(), '=', $this->parent->{$this->parentKey}
        );

        return $this;
    }

    /** @inheritDoc */
    public function addEagerConstraints(array $models)
    {
        $whereIn = $this->whereInMethod($this->parent, $this->parentKey);

        $this->whereInEager(
            $whereIn,
            $this->getQualifiedForeignPivotKeyName(),
            $this->getKeys($models, $this->parentKey)
        );
    }

    /** @inheritDoc */
    public function initRelation(array $models, $relation)
    {
        foreach ($models as $model) {
            $model->setRelation($relation, $this->related->newCollection());
        }

        return $models;
    }

    /** @inheritDoc */
    public function match(array $models, EloquentCollection $results, $relation)
    {
        $dictionary = $this->buildDictionary($results);

        // Once we have an array dictionary of child objects we can easily match the
        // children back to their parent using the dictionary and the keys on the
        // parent models. Then we should return these hydrated models back out.
        foreach ($models as $model) {
            $key = $this->getDictionaryKey($model->{$this->parentKey});

            if ($key !== null && isset($dictionary[$key])) {
                $model->setRelation(
                    $relation, $this->related->newCollection($dictionary[$key])
                );
            }
        }

        return $models;
    }

    /**
     * Build model dictionary keyed by the relation's foreign key.
     *
     * @param  \Illuminate\Database\Eloquent\Collection<int, TRelatedModel>  $results
     * @return array<array<array-key, TRelatedModel>>
     */
    protected function buildDictionary(EloquentCollection $results)
    {
        // First we'll build a dictionary of child models keyed by the foreign key
        // of the relation so that we will easily and quickly match them to the
        // parents without having a possibly slow inner loop for every model.
        $dictionary = [];

        $isAssociative = Arr::isAssoc($results->all());

        foreach ($results as $key => $result) {
            $value = $this->getDictionaryKey($result->{$this->accessor}->{$this->foreignPivotKey});

            if ($value === null) {
                continue;
            }

            if ($isAssociative) {
                $dictionary[$value][$key] = $result;
            } else {
                $dictionary[$value][] = $result;
            }
        }

        return $dictionary;
    }

    /**
     * Get the class being used for pivot models.
     *
     * @return class-string<TPivotModel>
     */
    public function getPivotClass()
    {
        return $this->using ?? Pivot::class;
    }

    /**
     * Specify the custom pivot model to use for the relationship.
     *
     * @template TNewPivotModel of \Illuminate\Database\Eloquent\Relations\Pivot
     *
     * @param  class-string<TNewPivotModel>  $class
     * @return $this
     *
     * @phpstan-this-out static<TRelatedModel, TDeclaringModel, TNewPivotModel, TAccessor>
     */
    public function using($class)
    {
        $this->using = $class;

        return $this;
    }

    /**
     * Specify the custom pivot accessor to use for the relationship.
     *
     * @template TNewAccessor of string
     *
     * @param  TNewAccessor  $accessor
     * @return $this
     *
     * @phpstan-this-out static<TRelatedModel, TDeclaringModel, TPivotModel, TNewAccessor>
     */
    public function as($accessor)
    {
        $this->accessor = $accessor;

        return $this;
    }

    /**
     * Set a where clause for a pivot table column.
     *
     * @param  string|\Illuminate\Contracts\Database\Query\Expression  $column
     * @param  mixed  $operator
     * @param  mixed  $value
     * @param  string  $boolean
     * @return $this
     */
    public function wherePivot($column, $operator = null, $value = null, $boolean = 'and')
    {
        $this->pivotWheres[] = func_get_args();

        return $this->where($this->qualifyPivotColumn($column), $operator, $value, $boolean);
    }

    /**
     * Set a "where between" clause for a pivot table column.
     *
     * @param  string|\Illuminate\Contracts\Database\Query\Expression  $column
     * @param  array  $values
     * @param  string  $boolean
     * @param  bool  $not
     * @return $this
     */
    public function wherePivotBetween($column, array $values, $boolean = 'and', $not = false)
    {
        return $this->whereBetween($this->qualifyPivotColumn($column), $values, $boolean, $not);
    }

    /**
     * Set a "or where between" clause for a pivot table column.
     *
     * @param  string|\Illuminate\Contracts\Database\Query\Expression  $column
     * @param  array  $values
     * @return $this
     */
    public function orWherePivotBetween($column, array $values)
    {
        return $this->wherePivotBetween($column, $values, 'or');
    }

    /**
     * Set a "where pivot not between" clause for a pivot table column.
     *
     * @param  string|\Illuminate\Contracts\Database\Query\Expression  $column
     * @param  array  $values
     * @param  string  $boolean
     * @return $this
     */
    public function wherePivotNotBetween($column, array $values, $boolean = 'and')
    {
        return $this->wherePivotBetween($column, $values, $boolean, true);
    }

    /**
     * Set a "or where not between" clause for a pivot table column.
     *
     * @param  string|\Illuminate\Contracts\Database\Query\Expression  $column
     * @param  array  $values
     * @return $this
     */
    public function orWherePivotNotBetween($column, array $values)
    {
        return $this->wherePivotBetween($column, $values, 'or', true);
    }

    /**
     * Set a "where in" clause for a pivot table column.
     *
     * @param  string|\Illuminate\Contracts\Database\Query\Expression  $column
     * @param  mixed  $values
     * @param  string  $boolean
     * @param  bool  $not
     * @return $this
     */
    public function wherePivotIn($column, $values, $boolean = 'and', $not = false)
    {
        $this->pivotWhereIns[] = func_get_args();

        return $this->whereIn($this->qualifyPivotColumn($column), $values, $boolean, $not);
    }

    /**
     * Set an "or where" clause for a pivot table column.
     *
     * @param  string|\Illuminate\Contracts\Database\Query\Expression  $column
     * @param  mixed  $operator
     * @param  mixed  $value
     * @return $this
     */
    public function orWherePivot($column, $operator = null, $value = null)
    {
        return $this->wherePivot($column, $operator, $value, 'or');
    }

    /**
     * Set a where clause for a pivot table column.
     *
     * In addition, new pivot records will receive this value.
     *
     * @param  string|\Illuminate\Contracts\Database\Query\Expression|array<string, string>  $column
     * @param  mixed  $value
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function withPivotValue($column, $value = null)
    {
        if (is_array($column)) {
            foreach ($column as $name => $value) {
                $this->withPivotValue($name, $value);
            }

            return $this;
        }

        if (is_null($value)) {
            throw new InvalidArgumentException('The provided value may not be null.');
        }

        $this->pivotValues[] = compact('column', 'value');

        return $this->wherePivot($column, '=', $value);
    }

    /**
     * Set an "or where in" clause for a pivot table column.
     *
     * @param  string  $column
     * @param  mixed  $values
     * @return $this
     */
    public function orWherePivotIn($column, $values)
    {
        return $this->wherePivotIn($column, $values, 'or');
    }

    /**
     * Set a "where not in" clause for a pivot table column.
     *
     * @param  string|\Illuminate\Contracts\Database\Query\Expression  $column
     * @param  mixed  $values
     * @param  string  $boolean
     * @return $this
     */
    public function wherePivotNotIn($column, $values, $boolean = 'and')
    {
        return $this->wherePivotIn($column, $values, $boolean, true);
    }

    /**
     * Set an "or where not in" clause for a pivot table column.
     *
     * @param  string  $column
     * @param  mixed  $values
     * @return $this
     */
    public function orWherePivotNotIn($column, $values)
    {
        return $this->wherePivotNotIn($column, $values, 'or');
    }

    /**
     * Set a "where null" clause for a pivot table column.
     *
     * @param  string|\Illuminate\Contracts\Database\Query\Expression  $column
     * @param  string  $boolean
     * @param  bool  $not
     * @return $this
     */
    public function wherePivotNull($column, $boolean = 'and', $not = false)
    {
        $this->pivotWhereNulls[] = func_get_args();

        return $this->whereNull($this->qualifyPivotColumn($column), $boolean, $not);
    }

    /**
     * Set a "where not null" clause for a pivot table column.
     *
     * @param  string|\Illuminate\Contracts\Database\Query\Expression  $column
     * @param  string  $boolean
     * @return $this
     */
    public function wherePivotNotNull($column, $boolean = 'and')
    {
        return $this->wherePivotNull($column, $boolean, true);
    }

    /**
     * Set a "or where null" clause for a pivot table column.
     *
     * @param  string|\Illuminate\Contracts\Database\Query\Expression  $column
     * @param  bool  $not
     * @return $this
     */
    public function orWherePivotNull($column, $not = false)
    {
        return $this->wherePivotNull($column, 'or', $not);
    }

    /**
     * Set a "or where not null" clause for a pivot table column.
     *
     * @param  string|\Illuminate\Contracts\Database\Query\Expression  $column
     * @return $this
     */
    public function orWherePivotNotNull($column)
    {
        return $this->orWherePivotNull($column, true);
    }

    /**
     * Add an "order by" clause for a pivot table column.
     *
     * @param  string|\Illuminate\Contracts\Database\Query\Expression  $column
     * @param  string  $direction
     * @return $this
     */
    public function orderByPivot($column, $direction = 'asc')
    {
        return $this->orderBy($this->qualifyPivotColumn($column), $direction);
    }

    /**
     * Add an "order by desc" clause for a pivot table column.
     *
     * @param  string|\Illuminate\Contracts\Database\Query\Expression  $column
     * @return $this
     */
    public function orderByPivotDesc($column)
    {
        return $this->orderBy($this->qualifyPivotColumn($column), 'desc');
    }

    /**
     * Find a related model by its primary key or return a new instance of the related model.
     *
     * @param  mixed  $id
     * @param  array  $columns
     * @return (
     *     $id is (\Illuminate\Contracts\Support\Arrayable<array-key, mixed>|array<mixed>)
     *     ? \Illuminate\Database\Eloquent\Collection<int, TRelatedModel&object{pivot: TPivotModel}>
     *     : TRelatedModel&object{pivot: TPivotModel}
     * )
     */
    public function findOrNew($id, $columns = ['*'])
    {
        if (is_null($instance = $this->find($id, $columns))) {
            $instance = $this->related->newInstance();
        }

        return $instance;
    }

    /**
     * Get the first related model record matching the attributes or instantiate it.
     *
     * @param  array  $attributes
     * @param  array  $values
     * @return TRelatedModel&object{pivot: TPivotModel}
     */
    public function firstOrNew(array $attributes = [], array $values = [])
    {
        if (is_null($instance = $this->related->where($attributes)->first())) {
            $instance = $this->related->newInstance(array_merge($attributes, $values));
        }

        return $instance;
    }

    /**
     * Get the first record matching the attributes. If the record is not found, create it.
     *
     * @param  array  $attributes
     * @param  (\Closure(): array)|array  $values
     * @param  array  $joining
     * @param  bool  $touch
     * @return TRelatedModel&object{pivot: TPivotModel}
     */
    public function firstOrCreate(array $attributes = [], Closure|array $values = [], array $joining = [], $touch = true)
    {
        if (is_null($instance = (clone $this)->where($attributes)->first())) {
            if (is_null($instance = $this->related->where($attributes)->first())) {
                $instance = $this->createOrFirst($attributes, $values, $joining, $touch);
            } else {
                try {
                    $this->getQuery()->withSavepointIfNeeded(fn () => $this->attach($instance, $joining, $touch));
                } catch (UniqueConstraintViolationException) {
                    // Nothing to do, the model was already attached...
                }
            }
        }

        return $instance;
    }

    /**
     * Attempt to create the record. If a unique constraint violation occurs, attempt to find the matching record.
     *
     * @param  array  $attributes
     * @param  (\Closure(): array)|array  $values
     * @param  array  $joining
     * @param  bool  $touch
     * @return TRelatedModel&object{pivot: TPivotModel}
     */
    public function createOrFirst(array $attributes = [], Closure|array $values = [], array $joining = [], $touch = true)
    {
        try {
            return $this->getQuery()->withSavepointIfNeeded(fn () => $this->create(array_merge($attributes, value($values)), $joining, $touch));
        } catch (UniqueConstraintViolationException $e) {
            // ...
        }

        try {
            return tap($this->related->where($attributes)->first() ?? throw $e, function ($instance) use ($joining, $touch) {
                $this->getQuery()->withSavepointIfNeeded(fn () => $this->attach($instance, $joining, $touch));
            });
        } catch (UniqueConstraintViolationException $e) {
            return (clone $this)->useWritePdo()->where($attributes)->first() ?? throw $e;
        }
    }

    /**
     * Create or update a related record matching the attributes, and fill it with values.
     *
     * @param  array  $attributes
     * @param  array  $values
     * @param  array  $joining
     * @param  bool  $touch
     * @return TRelatedModel&object{pivot: TPivotModel}
     */
    public function updateOrCreate(array $attributes, array $values = [], array $joining = [], $touch = true)
    {
        return tap($this->firstOrCreate($attributes, $values, $joining, $touch), function ($instance) use ($values) {
            if (! $instance->wasRecentlyCreated) {
                $instance->fill($values);

                $instance->save(['touch' => false]);
            }
        });
    }

    /**
     * Find a related model by its primary key.
     *
     * @param  mixed  $id
     * @param  array  $columns
     * @return (
     *     $id is (\Illuminate\Contracts\Support\Arrayable<array-key, mixed>|array<mixed>)
     *     ? \Illuminate\Database\Eloquent\Collection<int, TRelatedModel&object{pivot: TPivotModel}>
     *     : (TRelatedModel&object{pivot: TPivotModel})|null
     * )
     */
    public function find($id, $columns = ['*'])
    {
        if (! $id instanceof Model && (is_array($id) || $id instanceof Arrayable)) {
            return $this->findMany($id, $columns);
        }

        return $this->where(
            $this->getRelated()->getQualifiedKeyName(), '=', $this->parseId($id)
        )->first($columns);
    }

    /**
     * Find a sole related model by its primary key.
     *
     * @param  mixed  $id
     * @param  array  $columns
     * @return TRelatedModel&object{pivot: TPivotModel}
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException<TRelatedModel>
     * @throws \Illuminate\Database\MultipleRecordsFoundException
     */
    public function findSole($id, $columns = ['*'])
    {
        return $this->where(
            $this->getRelated()->getQualifiedKeyName(), '=', $this->parseId($id)
        )->sole($columns);
    }

    /**
     * Find multiple related models by their primary keys.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable|array  $ids
     * @param  array  $columns
     * @return \Illuminate\Database\Eloquent\Collection<int, TRelatedModel&object{pivot: TPivotModel}>
     */
    public function findMany($ids, $columns = ['*'])
    {
        $ids = $ids instanceof Arrayable ? $ids->toArray() : $ids;

        if (empty($ids)) {
            return $this->getRelated()->newCollection();
        }

        return $this->whereKey(
            $this->parseIds($ids)
        )->get($columns);
    }

    /**
     * Find a related model by its primary key or throw an exception.
     *
     * @param  mixed  $id
     * @param  array  $columns
     * @return (
     *     $id is (\Illuminate\Contracts\Support\Arrayable<array-key, mixed>|array<mixed>)
     *     ? \Illuminate\Database\Eloquent\Collection<int, TRelatedModel&object{pivot: TPivotModel}>
     *     : TRelatedModel&object{pivot: TPivotModel}
     * )
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException<TRelatedModel>
     */
    public function findOrFail($id, $columns = ['*'])
    {
        $result = $this->find($id, $columns);

        $id = $id instanceof Arrayable ? $id->toArray() : $id;

        if (is_array($id)) {
            if (count($result) === count(array_unique($id))) {
                return $result;
            }
        } elseif (! is_null($result)) {
            return $result;
        }

        throw (new ModelNotFoundException)->setModel(get_class($this->related), $id);
    }

    /**
     * Find a related model by its primary key or call a callback.
     *
     * @template TValue
     *
     * @param  mixed  $id
     * @param  (\Closure(): TValue)|list<string>|string  $columns
     * @param  (\Closure(): TValue)|null  $callback
     * @return (
     *     $id is (\Illuminate\Contracts\Support\Arrayable<array-key, mixed>|array<mixed>)
     *     ? \Illuminate\Database\Eloquent\Collection<int, TRelatedModel&object{pivot: TPivotModel}>|TValue
     *     : (TRelatedModel&object{pivot: TPivotModel})|TValue
     * )
     */
    public function findOr($id, $columns = ['*'], ?Closure $callback = null)
    {
        if ($columns instanceof Closure) {
            $callback = $columns;

            $columns = ['*'];
        }

        $result = $this->find($id, $columns);

        $id = $id instanceof Arrayable ? $id->toArray() : $id;

        if (is_array($id)) {
            if (count($result) === count(array_unique($id))) {
                return $result;
            }
        } elseif (! is_null($result)) {
            return $result;
        }

        return $callback();
    }

    /**
     * Add a basic where clause to the query, and return the first result.
     *
     * @param  \Closure|string|array  $column
     * @param  mixed  $operator
     * @param  mixed  $value
     * @param  string  $boolean
     * @return (TRelatedModel&object{pivot: TPivotModel})|null
     */
    public function firstWhere($column, $operator = null, $value = null, $boolean = 'and')
    {
        return $this->where($column, $operator, $value, $boolean)->first();
    }

    /**
     * Execute the query and get the first result.
     *
     * @param  array  $columns
     * @return (TRelatedModel&object{pivot: TPivotModel})|null
     */
    public function first($columns = ['*'])
    {
        $results = $this->limit(1)->get($columns);

        return count($results) > 0 ? $results->first() : null;
    }

    /**
     * Execute the query and get the first result or throw an exception.
     *
     * @param  array  $columns
     * @return TRelatedModel&object{pivot: TPivotModel}
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException<TRelatedModel>
     */
    public function firstOrFail($columns = ['*'])
    {
        if (! is_null($model = $this->first($columns))) {
            return $model;
        }

        throw (new ModelNotFoundException)->setModel(get_class($this->related));
    }

    /**
     * Execute the query and get the first result or call a callback.
     *
     * @template TValue
     *
     * @param  (\Closure(): TValue)|list<string>  $columns
     * @param  (\Closure(): TValue)|null  $callback
     * @return (TRelatedModel&object{pivot: TPivotModel})|TValue
     */
    public function firstOr($columns = ['*'], ?Closure $callback = null)
    {
        if ($columns instanceof Closure) {
            $callback = $columns;

            $columns = ['*'];
        }

        if (! is_null($model = $this->first($columns))) {
            return $model;
        }

        return $callback();
    }

    /** @inheritDoc */
    public function getResults()
    {
        return ! is_null($this->parent->{$this->parentKey})
            ? $this->get()
            : $this->related->newCollection();
    }

    /** @inheritDoc */
    public function get($columns = ['*'])
    {
        // First we'll add the proper select columns onto the query so it is run with
        // the proper columns. Then, we will get the results and hydrate our pivot
        // models with the result of those columns as a separate model relation.
        $builder = $this->query->applyScopes();

        $columns = $builder->getQuery()->columns ? [] : $columns;

        $models = $builder->addSelect(
            $this->shouldSelect($columns)
        )->getModels();

        $this->hydratePivotRelation($models);

        // If we actually found models we will also eager load any relationships that
        // have been specified as needing to be eager loaded. This will solve the
        // n + 1 query problem for the developer and also increase performance.
        if (count($models) > 0) {
            $models = $builder->eagerLoadRelations($models);
        }

        return $this->query->applyAfterQueryCallbacks(
            $this->related->newCollection($models)
        );
    }

    /**
     * Get the select columns for the relation query.
     *
     * @param  array  $columns
     * @return array
     */
    protected function shouldSelect(array $columns = ['*'])
    {
        if ($columns == ['*']) {
            $columns = [$this->related->qualifyColumn('*')];
        }

        return array_merge($columns, $this->aliasedPivotColumns());
    }

    /**
     * Get the pivot columns for the relation.
     *
     * "pivot_" is prefixed at each column for easy removal later.
     *
     * @return array
     */
    protected function aliasedPivotColumns()
    {
        return (new BaseCollection([
            $this->foreignPivotKey,
            $this->relatedPivotKey,
            ...$this->pivotColumns,
        ]))
            ->map(fn ($column) => $this->qualifyPivotColumn($column).' as pivot_'.$column)
            ->unique()
            ->all();
    }

    /**
     * Get a paginator for the "select" statement.
     *
     * @param  int|null  $perPage
     * @param  array  $columns
     * @param  string  $pageName
     * @param  int|null  $page
     * @return \Illuminate\Pagination\LengthAwarePaginator<int, TRelatedModel&object{pivot: TPivotModel}>
     */
    public function paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
    {
        $this->query->addSelect($this->shouldSelect($columns));

        return tap($this->query->paginate($perPage, $columns, $pageName, $page), function ($paginator) {
            $this->hydratePivotRelation($paginator->items());
        });
    }

    /**
     * Paginate the given query into a simple paginator.
     *
     * @param  int|null  $perPage
     * @param  array  $columns
     * @param  string  $pageName
     * @param  int|null  $page
     * @return \Illuminate\Contracts\Pagination\Paginator<int, TRelatedModel&object{pivot: TPivotModel}>
     */
    public function simplePaginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
    {
        $this->query->addSelect($this->shouldSelect($columns));

        return tap($this->query->simplePaginate($perPage, $columns, $pageName, $page), function ($paginator) {
            $this->hydratePivotRelation($paginator->items());
        });
    }

    /**
     * Paginate the given query into a cursor paginator.
     *
     * @param  int|null  $perPage
     * @param  array  $columns
     * @param  string  $cursorName
     * @param  string|null  $cursor
     * @return \Illuminate\Contracts\Pagination\CursorPaginator<int, TRelatedModel&object{pivot: TPivotModel}>
     */
    public function cursorPaginate($perPage = null, $columns = ['*'], $cursorName = 'cursor', $cursor = null)
    {
        $this->query->addSelect($this->shouldSelect($columns));

        return tap($this->query->cursorPaginate($perPage, $columns, $cursorName, $cursor), function ($paginator) {
            $this->hydratePivotRelation($paginator->items());
        });
    }

    /**
     * Chunk the results of the query.
     *
     * @param  int  $count
     * @param  callable  $callback
     * @return bool
     */
    public function chunk($count, callable $callback)
    {
        return $this->prepareQueryBuilder()->chunk($count, function ($results, $page) use ($callback) {
            $this->hydratePivotRelation($results->all());

            return $callback($results, $page);
        });
    }

    /**
     * Chunk the results of a query by comparing numeric IDs.
     *
     * @param  int  $count
     * @param  callable  $callback
     * @param  string|null  $column
     * @param  string|null  $alias
     * @return bool
     */
    public function chunkById($count, callable $callback, $column = null, $alias = null)
    {
        return $this->orderedChunkById($count, $callback, $column, $alias);
    }

    /**
     * Chunk the results of a query by comparing IDs in descending order.
     *
     * @param  int  $count
     * @param  callable  $callback
     * @param  string|null  $column
     * @param  string|null  $alias
     * @return bool
     */
    public function chunkByIdDesc($count, callable $callback, $column = null, $alias = null)
    {
        return $this->orderedChunkById($count, $callback, $column, $alias, descending: true);
    }

    /**
     * Execute a callback over each item while chunking by ID.
     *
     * @param  callable  $callback
     * @param  int  $count
     * @param  string|null  $column
     * @param  string|null  $alias
     * @return bool
     */
    public function eachById(callable $callback, $count = 1000, $column = null, $alias = null)
    {
        return $this->chunkById($count, function ($results, $page) use ($callback, $count) {
            foreach ($results as $key => $value) {
                if ($callback($value, (($page - 1) * $count) + $key) === false) {
                    return false;
                }
            }
        }, $column, $alias);
    }

    /**
     * Chunk the results of a query by comparing IDs in a given order.
     *
     * @param  int  $count
     * @param  callable  $callback
     * @param  string|null  $column
     * @param  string|null  $alias
     * @param  bool  $descending
     * @return bool
     */
    public function orderedChunkById($count, callable $callback, $column = null, $alias = null, $descending = false)
    {
        $column ??= $this->getRelated()->qualifyColumn(
            $this->getRelatedKeyName()
        );

        $alias ??= $this->getRelatedKeyName();

        return $this->prepareQueryBuilder()->orderedChunkById($count, function ($results, $page) use ($callback) {
            $this->hydratePivotRelation($results->all());

            return $callback($results, $page);
        }, $column, $alias, $descending);
    }

    /**
     * Execute a callback over each item while chunking.
     *
     * @param  callable  $callback
     * @param  int  $count
     * @return bool
     */
    public function each(callable $callback, $count = 1000)
    {
        return $this->chunk($count, function ($results) use ($callback) {
            foreach ($results as $key => $value) {
                if ($callback($value, $key) === false) {
                    return false;
                }
            }
        });
    }

    /**
     * Query lazily, by chunks of the given size.
     *
     * @param  int  $chunkSize
     * @return \Illuminate\Support\LazyCollection<int, TRelatedModel&object{pivot: TPivotModel}>
     */
    public function lazy($chunkSize = 1000)
    {
        return $this->prepareQueryBuilder()->lazy($chunkSize)->map(function ($model) {
            $this->hydratePivotRelation([$model]);

            return $model;
        });
    }

    /**
     * Query lazily, by chunking the results of a query by comparing IDs.
     *
     * @param  int  $chunkSize
     * @param  string|null  $column
     * @param  string|null  $alias
     * @return \Illuminate\Support\LazyCollection<int, TRelatedModel&object{pivot: TPivotModel}>
     */
    public function lazyById($chunkSize = 1000, $column = null, $alias = null)
    {
        $column ??= $this->getRelated()->qualifyColumn(
            $this->getRelatedKeyName()
        );

        $alias ??= $this->getRelatedKeyName();

        return $this->prepareQueryBuilder()->lazyById($chunkSize, $column, $alias)->map(function ($model) {
            $this->hydratePivotRelation([$model]);

            return $model;
        });
    }

    /**
     * Query lazily, by chunking the results of a query by comparing IDs in descending order.
     *
     * @param  int  $chunkSize
     * @param  string|null  $column
     * @param  string|null  $alias
     * @return \Illuminate\Support\LazyCollection<int, TRelatedModel&object{pivot: TPivotModel}>
     */
    public function lazyByIdDesc($chunkSize = 1000, $column = null, $alias = null)
    {
        $column ??= $this->getRelated()->qualifyColumn(
            $this->getRelatedKeyName()
        );

        $alias ??= $this->getRelatedKeyName();

        return $this->prepareQueryBuilder()->lazyByIdDesc($chunkSize, $column, $alias)->map(function ($model) {
            $this->hydratePivotRelation([$model]);

            return $model;
        });
    }

    /**
     * Get a lazy collection for the given query.
     *
     * @return \Illuminate\Support\LazyCollection<int, TRelatedModel&object{pivot: TPivotModel}>
     */
    public function cursor()
    {
        return $this->prepareQueryBuilder()->cursor()->map(function ($model) {
            $this->hydratePivotRelation([$model]);

            return $model;
        });
    }

    /**
     * Prepare the query builder for query execution.
     *
     * @return \Illuminate\Database\Eloquent\Builder<TRelatedModel>
     */
    protected function prepareQueryBuilder()
    {
        return $this->query->addSelect($this->shouldSelect());
    }

    /**
     * Hydrate the pivot table relationship on the models.
     *
     * @param  array<int, TRelatedModel>  $models
     * @return void
     */
    protected function hydratePivotRelation(array $models)
    {
        // To hydrate the pivot relationship, we will just gather the pivot attributes
        // and create a new Pivot model, which is basically a dynamic model that we
        // will set the attributes, table, and connections on it so it will work.
        foreach ($models as $model) {
            $model->setRelation($this->accessor, $this->newExistingPivot(
                $this->migratePivotAttributes($model)
            ));
        }
    }

    /**
     * Get the pivot attributes from a model.
     *
     * @param  TRelatedModel  $model
     * @return array
     */
    protected function migratePivotAttributes(Model $model)
    {
        $values = [];

        foreach ($model->getAttributes() as $key => $value) {
            // To get the pivots attributes we will just take any of the attributes which
            // begin with "pivot_" and add those to this arrays, as well as unsetting
            // them from the parent's models since they exist in a different table.
            if (str_starts_with($key, 'pivot_')) {
                $values[substr($key, 6)] = $value;

                unset($model->$key);
            }
        }

        return $values;
    }

    /**
     * If we're touching the parent model, touch.
     *
     * @return void
     */
    public function touchIfTouching()
    {
        if ($this->touchingParent()) {
            $this->getParent()->touch();
        }

        if ($this->getParent()->touches($this->relationName)) {
            $this->touch();
        }
    }

    /**
     * Determine if we should touch the parent on sync.
     *
     * @return bool
     */
    protected function touchingParent()
    {
        return $this->getRelated()->touches($this->guessInverseRelation());
    }

    /**
     * Attempt to guess the name of the inverse of the relation.
     *
     * @return string
     */
    protected function guessInverseRelation()
    {
        return Str::camel(Str::pluralStudly(class_basename($this->getParent())));
    }

    /**
     * Touch all of the related models for the relationship.
     *
     * E.g.: Touch all roles associated with this user.
     *
     * @return void
     */
    public function touch()
    {
        if ($this->related->isIgnoringTouch()) {
            return;
        }

        $columns = [
            $this->related->getUpdatedAtColumn() => $this->related->freshTimestampString(),
        ];

        // If we actually have IDs for the relation, we will run the query to update all
        // the related model's timestamps, to make sure these all reflect the changes
        // to the parent models. This will help us keep any caching synced up here.
        if (count($ids = $this->allRelatedIds()) > 0) {
            $this->getRelated()->newQueryWithoutRelationships()->whereKey($ids)->update($columns);
        }
    }

    /**
     * Get all of the IDs for the related models.
     *
     * @return \Illuminate\Support\Collection<int, int|string>
     */
    public function allRelatedIds()
    {
        return $this->newPivotQuery()->pluck($this->relatedPivotKey);
    }

    /**
     * Save a new model and attach it to the parent model.
     *
     * @param  TRelatedModel  $model
     * @param  array  $pivotAttributes
     * @param  bool  $touch
     * @return TRelatedModel&object{pivot: TPivotModel}
     */
    public function save(Model $model, array $pivotAttributes = [], $touch = true)
    {
        $model->save(['touch' => false]);

        $this->attach($model, $pivotAttributes, $touch);

        return $model;
    }

    /**
     * Save a new model without raising any events and attach it to the parent model.
     *
     * @param  TRelatedModel  $model
     * @param  array  $pivotAttributes
     * @param  bool  $touch
     * @return TRelatedModel&object{pivot: TPivotModel}
     */
    public function saveQuietly(Model $model, array $pivotAttributes = [], $touch = true)
    {
        return Model::withoutEvents(function () use ($model, $pivotAttributes, $touch) {
            return $this->save($model, $pivotAttributes, $touch);
        });
    }

    /**
     * Save an array of new models and attach them to the parent model.
     *
     * @template TContainer of \Illuminate\Support\Collection<array-key, TRelatedModel>|array<array-key, TRelatedModel>
     *
     * @param  TContainer  $models
     * @param  array  $pivotAttributes
     * @return TContainer
     */
    public function saveMany($models, array $pivotAttributes = [])
    {
        foreach ($models as $key => $model) {
            $this->save($model, (array) ($pivotAttributes[$key] ?? []), false);
        }

        $this->touchIfTouching();

        return $models;
    }

    /**
     * Save an array of new models without raising any events and attach them to the parent model.
     *
     * @template TContainer of \Illuminate\Support\Collection<array-key, TRelatedModel>|array<array-key, TRelatedModel>
     *
     * @param  TContainer  $models
     * @param  array  $pivotAttributes
     * @return TContainer
     */
    public function saveManyQuietly($models, array $pivotAttributes = [])
    {
        return Model::withoutEvents(function () use ($models, $pivotAttributes) {
            return $this->saveMany($models, $pivotAttributes);
        });
    }

    /**
     * Create a new instance of the related model.
     *
     * @param  array  $attributes
     * @param  array  $joining
     * @param  bool  $touch
     * @return TRelatedModel&object{pivot: TPivotModel}
     */
    public function create(array $attributes = [], array $joining = [], $touch = true)
    {
        $attributes = array_merge($this->getQuery()->pendingAttributes, $attributes);

        $instance = $this->related->newInstance($attributes);

        // Once we save the related model, we need to attach it to the base model via
        // through intermediate table so we'll use the existing "attach" method to
        // accomplish this which will insert the record and any more attributes.
        $instance->save(['touch' => false]);

        $this->attach($instance, $joining, $touch);

        return $instance;
    }

    /**
     * Create an array of new instances of the related models.
     *
     * @param  iterable  $records
     * @param  array  $joinings
     * @return array<int, TRelatedModel&object{pivot: TPivotModel}>
     */
    public function createMany(iterable $records, array $joinings = [])
    {
        $instances = [];

        foreach ($records as $key => $record) {
            $instances[] = $this->create($record, (array) ($joinings[$key] ?? []), false);
        }

        $this->touchIfTouching();

        return $instances;
    }

    /** @inheritDoc */
    public function getRelationExistenceQuery(Builder $query, Builder $parentQuery, $columns = ['*'])
    {
        if ($parentQuery->getQuery()->from == $query->getQuery()->from) {
            return $this->getRelationExistenceQueryForSelfJoin($query, $parentQuery, $columns);
        }

        $this->performJoin($query);

        return parent::getRelationExistenceQuery($query, $parentQuery, $columns);
    }

    /**
     * Add the constraints for a relationship query on the same table.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<TRelatedModel>  $query
     * @param  \Illuminate\Database\Eloquent\Builder<TDeclaringModel>  $parentQuery
     * @param  mixed  $columns
     * @return \Illuminate\Database\Eloquent\Builder<TRelatedModel>
     */
    public function getRelationExistenceQueryForSelfJoin(Builder $query, Builder $parentQuery, $columns = ['*'])
    {
        $query->select($columns);

        $query->from($this->related->getTable().' as '.$hash = $this->getRelationCountHash());

        $this->related->setTable($hash);

        $this->performJoin($query);

        return parent::getRelationExistenceQuery($query, $parentQuery, $columns);
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
        if ($this->parent->exists) {
            $this->query->limit($value);
        } else {
            $column = $this->getExistenceCompareKey();

            $grammar = $this->query->getQuery()->getGrammar();

            if ($grammar instanceof MySqlGrammar && $grammar->useLegacyGroupLimit($this->query->getQuery())) {
                $column = 'pivot_'.last(explode('.', $column));
            }

            $this->query->groupLimit($value, $column);
        }

        return $this;
    }

    /**
     * Get the key for comparing against the parent key in "has" query.
     *
     * @return string
     */
    public function getExistenceCompareKey()
    {
        return $this->getQualifiedForeignPivotKeyName();
    }

    /**
     * Specify that the pivot table has creation and update timestamps.
     *
     * @param  string|null|false  $createdAt
     * @param  string|null|false  $updatedAt
     * @return $this
     */
    public function withTimestamps($createdAt = null, $updatedAt = null)
    {
        $this->pivotCreatedAt = $createdAt !== false ? $createdAt : null;
        $this->pivotUpdatedAt = $updatedAt !== false ? $updatedAt : null;

        $pivots = array_filter([
            $createdAt !== false ? $this->createdAt() : null,
            $updatedAt !== false ? $this->updatedAt() : null,
        ]);

        $this->withTimestamps = ! empty($pivots);

        return $this->withTimestamps ? $this->withPivot($pivots) : $this;
    }

    /**
     * Get the name of the "created at" column.
     *
     * @return string
     */
    public function createdAt()
    {
        return $this->pivotCreatedAt ?? $this->parent->getCreatedAtColumn() ?? Model::CREATED_AT;
    }

    /**
     * Get the name of the "updated at" column.
     *
     * @return string
     */
    public function updatedAt()
    {
        return $this->pivotUpdatedAt ?? $this->parent->getUpdatedAtColumn() ?? Model::UPDATED_AT;
    }

    /**
     * Get the foreign key for the relation.
     *
     * @return string
     */
    public function getForeignPivotKeyName()
    {
        return $this->foreignPivotKey;
    }

    /**
     * Get the fully-qualified foreign key for the relation.
     *
     * @return string
     */
    public function getQualifiedForeignPivotKeyName()
    {
        return $this->qualifyPivotColumn($this->foreignPivotKey);
    }

    /**
     * Get the "related key" for the relation.
     *
     * @return string
     */
    public function getRelatedPivotKeyName()
    {
        return $this->relatedPivotKey;
    }

    /**
     * Get the fully-qualified "related key" for the relation.
     *
     * @return string
     */
    public function getQualifiedRelatedPivotKeyName()
    {
        return $this->qualifyPivotColumn($this->relatedPivotKey);
    }

    /**
     * Get the parent key for the relationship.
     *
     * @return string
     */
    public function getParentKeyName()
    {
        return $this->parentKey;
    }

    /**
     * Get the fully-qualified parent key name for the relation.
     *
     * @return string
     */
    public function getQualifiedParentKeyName()
    {
        return $this->parent->qualifyColumn($this->parentKey);
    }

    /**
     * Get the related key for the relationship.
     *
     * @return string
     */
    public function getRelatedKeyName()
    {
        return $this->relatedKey;
    }

    /**
     * Get the fully-qualified related key name for the relation.
     *
     * @return string
     */
    public function getQualifiedRelatedKeyName()
    {
        return $this->related->qualifyColumn($this->relatedKey);
    }

    /**
     * Get the intermediate table for the relationship.
     *
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Get the relationship name for the relationship.
     *
     * @return string
     */
    public function getRelationName()
    {
        return $this->relationName;
    }

    /**
     * Get the name of the pivot accessor for this relationship.
     *
     * @return TAccessor
     */
    public function getPivotAccessor()
    {
        return $this->accessor;
    }

    /**
     * Get the pivot columns for this relationship.
     *
     * @return array
     */
    public function getPivotColumns()
    {
        return $this->pivotColumns;
    }

    /**
     * Qualify the given column name by the pivot table.
     *
     * @param  string|\Illuminate\Contracts\Database\Query\Expression  $column
     * @return string|\Illuminate\Contracts\Database\Query\Expression
     */
    public function qualifyPivotColumn($column)
    {
        if ($this->query->getQuery()->getGrammar()->isExpression($column)) {
            return $column;
        }

        return str_contains($column, '.')
            ? $column
            : $this->table.'.'.$column;
    }
}
