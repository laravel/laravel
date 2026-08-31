<?php

namespace Illuminate\Database\Eloquent;

use BadMethodCallException;
use Closure;
use Exception;
use Illuminate\Contracts\Database\Eloquent\Builder as BuilderContract;
use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Concerns\BuildsQueries;
use Illuminate\Database\Eloquent\Concerns\QueriesRelationships;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\RecordsNotFoundException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection as BaseCollection;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\ForwardsCalls;
use ReflectionClass;
use ReflectionMethod;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 *
 * @property-read HigherOrderBuilderProxy|$this $orWhere
 * @property-read HigherOrderBuilderProxy|$this $whereNot
 * @property-read HigherOrderBuilderProxy|$this $orWhereNot
 *
 * @mixin \Illuminate\Database\Query\Builder
 */
class Builder implements BuilderContract
{
    /** @use \Illuminate\Database\Concerns\BuildsQueries<TModel> */
    use BuildsQueries, ForwardsCalls, QueriesRelationships {
        BuildsQueries::sole as baseSole;
    }

    /**
     * The base query builder instance.
     *
     * @var \Illuminate\Database\Query\Builder
     */
    protected $query;

    /**
     * The model being queried.
     *
     * @var TModel
     */
    protected $model;

    /**
     * The attributes that should be added to new models created by this builder.
     *
     * @var array
     */
    public $pendingAttributes = [];

    /**
     * The relationships that should be eager loaded.
     *
     * @var array
     */
    protected $eagerLoad = [];

    /**
     * All of the globally registered builder macros.
     *
     * @var array
     */
    protected static $macros = [];

    /**
     * All of the locally registered builder macros.
     *
     * @var array
     */
    protected $localMacros = [];

    /**
     * A replacement for the typical delete function.
     *
     * @var \Closure
     */
    protected $onDelete;

    /**
     * The properties that should be returned from query builder.
     *
     * @var string[]
     */
    protected $propertyPassthru = [
        'from',
    ];

    /**
     * The methods that should be returned from query builder.
     *
     * @var string[]
     */
    protected $passthru = [
        'aggregate',
        'average',
        'avg',
        'count',
        'dd',
        'ddrawsql',
        'doesntexist',
        'doesntexistor',
        'dump',
        'dumprawsql',
        'exists',
        'existsor',
        'explain',
        'getbindings',
        'getconnection',
        'getcountforpagination',
        'getgrammar',
        'getrawbindings',
        'implode',
        'insert',
        'insertgetid',
        'insertorignore',
        'insertusing',
        'insertorignoreusing',
        'max',
        'min',
        'numericaggregate',
        'raw',
        'rawvalue',
        'sum',
        'tosql',
        'torawsql',
    ];

    /**
     * Applied global scopes.
     *
     * @var array
     */
    protected $scopes = [];

    /**
     * Removed global scopes.
     *
     * @var array
     */
    protected $removedScopes = [];

    /**
     * The callbacks that should be invoked after retrieving data from the database.
     *
     * @var array
     */
    protected $afterQueryCallbacks = [];

    /**
     * The callbacks that should be invoked on clone.
     *
     * @var array
     */
    protected $onCloneCallbacks = [];

    /**
     * Create a new Eloquent query builder instance.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     */
    public function __construct(QueryBuilder $query)
    {
        $this->query = $query;
    }

    /**
     * Create and return an un-saved model instance.
     *
     * @param  array  $attributes
     * @return TModel
     */
    public function make(array $attributes = [])
    {
        return $this->newModelInstance($attributes);
    }

    /**
     * Register a new global scope.
     *
     * @param  string  $identifier
     * @param  \Illuminate\Database\Eloquent\Scope|\Closure  $scope
     * @return $this
     */
    public function withGlobalScope($identifier, $scope)
    {
        $this->scopes[$identifier] = $scope;

        if (method_exists($scope, 'extend')) {
            $scope->extend($this);
        }

        return $this;
    }

    /**
     * Remove a registered global scope.
     *
     * @param  \Illuminate\Database\Eloquent\Scope|string  $scope
     * @return $this
     */
    public function withoutGlobalScope($scope)
    {
        if (! is_string($scope)) {
            $scope = get_class($scope);
        }

        unset($this->scopes[$scope]);

        $this->removedScopes[] = $scope;

        return $this;
    }

    /**
     * Remove all or passed registered global scopes.
     *
     * @param  array|null  $scopes
     * @return $this
     */
    public function withoutGlobalScopes(?array $scopes = null)
    {
        if (! is_array($scopes)) {
            $scopes = array_keys($this->scopes);
        }

        foreach ($scopes as $scope) {
            $this->withoutGlobalScope($scope);
        }

        return $this;
    }

    /**
     * Remove all global scopes except the given scopes.
     *
     * @param  array  $scopes
     * @return $this
     */
    public function withoutGlobalScopesExcept(array $scopes = [])
    {
        $this->withoutGlobalScopes(
            array_diff(array_keys($this->scopes), $scopes)
        );

        return $this;
    }

    /**
     * Get an array of global scopes that were removed from the query.
     *
     * @return array
     */
    public function removedScopes()
    {
        return $this->removedScopes;
    }

    /**
     * Add a where clause on the primary key to the query.
     *
     * @param  mixed  $id
     * @return $this
     */
    public function whereKey($id)
    {
        if ($id instanceof Model) {
            $id = $id->getKey();
        }

        if (is_array($id) || $id instanceof Arrayable) {
            if (in_array($this->model->getKeyType(), ['int', 'integer'])) {
                $this->query->whereIntegerInRaw($this->model->getQualifiedKeyName(), $id);
            } else {
                $this->query->whereIn($this->model->getQualifiedKeyName(), $id);
            }

            return $this;
        }

        if ($id !== null && $this->model->getKeyType() === 'string') {
            $id = (string) $id;
        }

        return $this->where($this->model->getQualifiedKeyName(), '=', $id);
    }

    /**
     * Add a where clause on the primary key to the query.
     *
     * @param  mixed  $id
     * @return $this
     */
    public function whereKeyNot($id)
    {
        if ($id instanceof Model) {
            $id = $id->getKey();
        }

        if (is_array($id) || $id instanceof Arrayable) {
            if (in_array($this->model->getKeyType(), ['int', 'integer'])) {
                $this->query->whereIntegerNotInRaw($this->model->getQualifiedKeyName(), $id);
            } else {
                $this->query->whereNotIn($this->model->getQualifiedKeyName(), $id);
            }

            return $this;
        }

        if ($id !== null && $this->model->getKeyType() === 'string') {
            $id = (string) $id;
        }

        return $this->where($this->model->getQualifiedKeyName(), '!=', $id);
    }

    /**
     * Exclude the given models from the query results.
     *
     * @param  iterable|mixed  $models
     * @return static
     */
    public function except($models)
    {
        return $this->whereKeyNot(
            $models instanceof Model
                ? $models->getKey()
                : Collection::wrap($models)->modelKeys()
        );
    }

    /**
     * Add a basic where clause to the query.
     *
     * @param  (\Closure(static): mixed)|string|array|\Illuminate\Contracts\Database\Query\Expression  $column
     * @param  mixed  $operator
     * @param  mixed  $value
     * @param  string  $boolean
     * @return $this
     */
    public function where($column, $operator = null, $value = null, $boolean = 'and')
    {
        if ($column instanceof Closure && is_null($operator)) {
            $column($query = $this->model->newQueryWithoutRelationships());

            $this->eagerLoad = array_merge($this->eagerLoad, $query->getEagerLoads());

            $this->query->addNestedWhereQuery($query->getQuery(), $boolean);
        } else {
            $this->query->where(...func_get_args());
        }

        return $this;
    }

    /**
     * Add a basic where clause to the query, and return the first result.
     *
     * @param  (\Closure(static): mixed)|string|array|\Illuminate\Contracts\Database\Query\Expression  $column
     * @param  mixed  $operator
     * @param  mixed  $value
     * @param  string  $boolean
     * @return TModel|null
     */
    public function firstWhere($column, $operator = null, $value = null, $boolean = 'and')
    {
        return $this->where(...func_get_args())->first();
    }

    /**
     * Add an "or where" clause to the query.
     *
     * @param  (\Closure(static): mixed)|array|string|\Illuminate\Contracts\Database\Query\Expression  $column
     * @param  mixed  $operator
     * @param  mixed  $value
     * @return $this
     */
    public function orWhere($column, $operator = null, $value = null)
    {
        [$value, $operator] = $this->query->prepareValueAndOperator(
            $value, $operator, func_num_args() === 2
        );

        return $this->where($column, $operator, $value, 'or');
    }

    /**
     * Add a basic "where not" clause to the query.
     *
     * @param  (\Closure(static): mixed)|string|array|\Illuminate\Contracts\Database\Query\Expression  $column
     * @param  mixed  $operator
     * @param  mixed  $value
     * @param  string  $boolean
     * @return $this
     */
    public function whereNot($column, $operator = null, $value = null, $boolean = 'and')
    {
        return $this->where($column, $operator, $value, $boolean.' not');
    }

    /**
     * Add an "or where not" clause to the query.
     *
     * @param  (\Closure(static): mixed)|array|string|\Illuminate\Contracts\Database\Query\Expression  $column
     * @param  mixed  $operator
     * @param  mixed  $value
     * @return $this
     */
    public function orWhereNot($column, $operator = null, $value = null)
    {
        return $this->whereNot($column, $operator, $value, 'or');
    }

    /**
     * Add an "order by" clause for a timestamp to the query.
     *
     * @param  string|\Illuminate\Contracts\Database\Query\Expression  $column
     * @return $this
     */
    public function latest($column = null)
    {
        if (is_null($column)) {
            $column = $this->model->getCreatedAtColumn() ?? 'created_at';
        }

        $this->query->latest($column);

        return $this;
    }

    /**
     * Add an "order by" clause for a timestamp to the query.
     *
     * @param  string|\Illuminate\Contracts\Database\Query\Expression  $column
     * @return $this
     */
    public function oldest($column = null)
    {
        if (is_null($column)) {
            $column = $this->model->getCreatedAtColumn() ?? 'created_at';
        }

        $this->query->oldest($column);

        return $this;
    }

    /**
     * Create a collection of models from plain arrays.
     *
     * @param  array  $items
     * @return \Illuminate\Database\Eloquent\Collection<int, TModel>
     */
    public function hydrate(array $items)
    {
        $instance = $this->newModelInstance();

        return $instance->newCollection(array_map(function ($item) use ($items, $instance) {
            $model = $instance->newFromBuilder($item);

            if (count($items) > 1) {
                $model->preventsLazyLoading = Model::preventsLazyLoading();
            }

            return $model;
        }, $items));
    }

    /**
     * Insert into the database after merging the model's default attributes, setting timestamps, and casting values.
     *
     * @param  array<int, array<string, mixed>>  $values
     * @return bool
     */
    public function fillAndInsert(array $values)
    {
        return $this->insert($this->fillForInsert($values));
    }

    /**
     * Insert (ignoring errors) into the database after merging the model's default attributes, setting timestamps, and casting values.
     *
     * @param  array<int, array<string, mixed>>  $values
     * @return int
     */
    public function fillAndInsertOrIgnore(array $values)
    {
        return $this->insertOrIgnore($this->fillForInsert($values));
    }

    /**
     * Insert a record into the database and get its ID after merging the model's default attributes, setting timestamps, and casting values.
     *
     * @param  array<string, mixed>  $values
     * @return int
     */
    public function fillAndInsertGetId(array $values)
    {
        return $this->insertGetId($this->fillForInsert([$values])[0]);
    }

    /**
     * Enrich the given values by merging in the model's default attributes, adding timestamps, and casting values.
     *
     * @param  array<int, array<string, mixed>>  $values
     * @return array<int, array<string, mixed>>
     */
    public function fillForInsert(array $values)
    {
        if (empty($values)) {
            return [];
        }

        if (! is_array(array_first($values))) {
            $values = [$values];
        }

        $this->model->unguarded(function () use (&$values) {
            foreach ($values as $key => $rowValues) {
                $values[$key] = tap(
                    $this->newModelInstance($rowValues),
                    fn ($model) => $model->setUniqueIds()
                )->getAttributes();
            }
        });

        return $this->addTimestampsToUpsertValues($values);
    }

    /**
     * Create a collection of models from a raw query.
     *
     * @param  string  $query
     * @param  array  $bindings
     * @return \Illuminate\Database\Eloquent\Collection<int, TModel>
     */
    public function fromQuery($query, $bindings = [])
    {
        return $this->hydrate(
            $this->query->getConnection()->select($query, $bindings)
        );
    }

    /**
     * Find a model by its primary key.
     *
     * @param  mixed  $id
     * @param  array|string  $columns
     * @return ($id is (\Illuminate\Contracts\Support\Arrayable<array-key, mixed>|array<mixed>) ? \Illuminate\Database\Eloquent\Collection<int, TModel> : TModel|null)
     */
    public function find($id, $columns = ['*'])
    {
        if (is_array($id) || $id instanceof Arrayable) {
            return $this->findMany($id, $columns);
        }

        return $this->whereKey($id)->first($columns);
    }

    /**
     * Find a sole model by its primary key.
     *
     * @param  mixed  $id
     * @param  array|string  $columns
     * @return TModel
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException<TModel>
     * @throws \Illuminate\Database\MultipleRecordsFoundException
     */
    public function findSole($id, $columns = ['*'])
    {
        return $this->whereKey($id)->sole($columns);
    }

    /**
     * Find multiple models by their primary keys.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable|array  $ids
     * @param  array|string  $columns
     * @return \Illuminate\Database\Eloquent\Collection<int, TModel>
     */
    public function findMany($ids, $columns = ['*'])
    {
        $ids = $ids instanceof Arrayable ? $ids->toArray() : $ids;

        if (empty($ids)) {
            return $this->model->newCollection();
        }

        return $this->whereKey($ids)->get($columns);
    }

    /**
     * Find a model by its primary key or throw an exception.
     *
     * @param  mixed  $id
     * @param  array|string  $columns
     * @return ($id is (\Illuminate\Contracts\Support\Arrayable<array-key, mixed>|array<mixed>) ? \Illuminate\Database\Eloquent\Collection<int, TModel> : TModel)
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException<TModel>
     */
    public function findOrFail($id, $columns = ['*'])
    {
        $result = $this->find($id, $columns);

        $id = $id instanceof Arrayable ? $id->toArray() : $id;

        if (is_array($id)) {
            if (count($result) !== count(array_unique($id))) {
                throw (new ModelNotFoundException)->setModel(
                    get_class($this->model), array_diff($id, $result->modelKeys())
                );
            }

            return $result;
        }

        if (is_null($result)) {
            throw (new ModelNotFoundException)->setModel(
                get_class($this->model), $id
            );
        }

        return $result;
    }

    /**
     * Find a model by its primary key or return fresh model instance.
     *
     * @param  mixed  $id
     * @param  array|string  $columns
     * @return ($id is (\Illuminate\Contracts\Support\Arrayable<array-key, mixed>|array<mixed>) ? \Illuminate\Database\Eloquent\Collection<int, TModel> : TModel)
     */
    public function findOrNew($id, $columns = ['*'])
    {
        if (! is_null($model = $this->find($id, $columns))) {
            return $model;
        }

        return $this->newModelInstance();
    }

    /**
     * Find a model by its primary key or call a callback.
     *
     * @template TValue
     *
     * @param  mixed  $id
     * @param  (\Closure(): TValue)|list<string>|string  $columns
     * @param  (\Closure(): TValue)|null  $callback
     * @return (
     *     $id is (\Illuminate\Contracts\Support\Arrayable<array-key, mixed>|array<mixed>)
     *     ? \Illuminate\Database\Eloquent\Collection<int, TModel>
     *     : TModel|TValue
     * )
     */
    public function findOr($id, $columns = ['*'], ?Closure $callback = null)
    {
        if ($columns instanceof Closure) {
            $callback = $columns;

            $columns = ['*'];
        }

        if (! is_null($model = $this->find($id, $columns))) {
            return $model;
        }

        return $callback();
    }

    /**
     * Get the first record matching the attributes or instantiate it.
     *
     * @param  array  $attributes
     * @param  array  $values
     * @return TModel
     */
    public function firstOrNew(array $attributes = [], array $values = [])
    {
        if (! is_null($instance = $this->where($attributes)->first())) {
            return $instance;
        }

        return $this->newModelInstance(array_merge($attributes, $values));
    }

    /**
     * Get the first record matching the attributes. If the record is not found, create it.
     *
     * @param  array  $attributes
     * @param  (\Closure(): array)|array  $values
     * @return TModel
     */
    public function firstOrCreate(array $attributes = [], Closure|array $values = [])
    {
        if (! is_null($instance = (clone $this)->where($attributes)->first())) {
            return $instance;
        }

        return $this->createOrFirst($attributes, $values);
    }

    /**
     * Attempt to create the record. If a unique constraint violation occurs, attempt to find the matching record.
     *
     * @param  array  $attributes
     * @param  (\Closure(): array)|array  $values
     * @return TModel
     */
    public function createOrFirst(array $attributes = [], Closure|array $values = [])
    {
        try {
            return $this->withSavepointIfNeeded(fn () => $this->create(array_merge($attributes, value($values))));
        } catch (UniqueConstraintViolationException $e) {
            return $this->useWritePdo()->where($attributes)->first() ?? throw $e;
        }
    }

    /**
     * Create or update a record matching the attributes, and fill it with values.
     *
     * @param  array  $attributes
     * @param  array  $values
     * @return TModel
     */
    public function updateOrCreate(array $attributes, array $values = [])
    {
        return tap($this->firstOrCreate($attributes, $values), function ($instance) use ($values) {
            if (! $instance->wasRecentlyCreated) {
                $instance->fill($values)->save();
            }
        });
    }

    /**
     * Create a record matching the attributes, or increment the existing record.
     *
     * @param  array  $attributes
     * @param  string  $column
     * @param  int|float  $default
     * @param  int|float  $step
     * @param  array  $extra
     * @return TModel
     */
    public function incrementOrCreate(array $attributes, string $column = 'count', $default = 1, $step = 1, array $extra = [])
    {
        return tap($this->firstOrCreate($attributes, [$column => $default]), function ($instance) use ($column, $step, $extra) {
            if (! $instance->wasRecentlyCreated) {
                $instance->increment($column, $step, $extra);
            }
        });
    }

    /**
     * Execute the query and get the first result or throw an exception.
     *
     * @param  array|string  $columns
     * @return TModel
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException<TModel>
     */
    public function firstOrFail($columns = ['*'])
    {
        if (! is_null($model = $this->first($columns))) {
            return $model;
        }

        throw (new ModelNotFoundException)->setModel(get_class($this->model));
    }

    /**
     * Execute the query and get the first result or call a callback.
     *
     * @template TValue
     *
     * @param  (\Closure(): TValue)|list<string>  $columns
     * @param  (\Closure(): TValue)|null  $callback
     * @return TModel|TValue
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

    /**
     * Execute the query and get the first result if it's the sole matching record.
     *
     * @param  array|string  $columns
     * @return TModel
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException<TModel>
     * @throws \Illuminate\Database\MultipleRecordsFoundException
     */
    public function sole($columns = ['*'])
    {
        try {
            return $this->baseSole($columns);
        } catch (RecordsNotFoundException) {
            throw (new ModelNotFoundException)->setModel(get_class($this->model));
        }
    }

    /**
     * Get a single column's value from the first result of a query.
     *
     * @param  string|\Illuminate\Contracts\Database\Query\Expression  $column
     * @return mixed
     */
    public function value($column)
    {
        if ($result = $this->first([$column])) {
            $column = $column instanceof Expression ? $column->getValue($this->getGrammar()) : $column;

            return $result->{Str::afterLast($column, '.')};
        }
    }

    /**
     * Get a single column's value from the first result of a query if it's the sole matching record.
     *
     * @param  string|\Illuminate\Contracts\Database\Query\Expression  $column
     * @return mixed
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException<TModel>
     * @throws \Illuminate\Database\MultipleRecordsFoundException
     */
    public function soleValue($column)
    {
        $column = $column instanceof Expression ? $column->getValue($this->getGrammar()) : $column;

        return $this->sole([$column])->{Str::afterLast($column, '.')};
    }

    /**
     * Get a single column's value from the first result of the query or throw an exception.
     *
     * @param  string|\Illuminate\Contracts\Database\Query\Expression  $column
     * @return mixed
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException<TModel>
     */
    public function valueOrFail($column)
    {
        $column = $column instanceof Expression ? $column->getValue($this->getGrammar()) : $column;

        return $this->firstOrFail([$column])->{Str::afterLast($column, '.')};
    }

    /**
     * Execute the query as a "select" statement.
     *
     * @param  array|string  $columns
     * @return \Illuminate\Database\Eloquent\Collection<int, TModel>
     */
    public function get($columns = ['*'])
    {
        $builder = $this->applyScopes();

        // If we actually found models we will also eager load any relationships that
        // have been specified as needing to be eager loaded, which will solve the
        // n+1 query issue for the developers to avoid running a lot of queries.
        if (count($models = $builder->getModels($columns)) > 0) {
            $models = $builder->eagerLoadRelations($models);
        }

        return $this->applyAfterQueryCallbacks(
            $builder->getModel()->newCollection($models)
        );
    }

    /**
     * Get the hydrated models without eager loading.
     *
     * @param  array|string  $columns
     * @return array<int, TModel>
     */
    public function getModels($columns = ['*'])
    {
        return $this->model->hydrate(
            $this->query->get($columns)->all()
        )->all();
    }

    /**
     * Eager load the relationships for the models.
     *
     * @param  array<int, TModel>  $models
     * @return array<int, TModel>
     */
    public function eagerLoadRelations(array $models)
    {
        foreach ($this->eagerLoad as $name => $constraints) {
            // For nested eager loads we'll skip loading them here and they will be set as an
            // eager load on the query to retrieve the relation so that they will be eager
            // loaded on that query, because that is where they get hydrated as models.
            if (! str_contains($name, '.')) {
                $models = $this->eagerLoadRelation($models, $name, $constraints);
            }
        }

        return $models;
    }

    /**
     * Eagerly load the relationship on a set of models.
     *
     * @param  array  $models
     * @param  string  $name
     * @param  \Closure  $constraints
     * @return array
     */
    protected function eagerLoadRelation(array $models, $name, Closure $constraints)
    {
        // First we will "back up" the existing where conditions on the query so we can
        // add our eager constraints. Then we will merge the wheres that were on the
        // query back to it in order that any where conditions might be specified.
        $relation = $this->getRelation($name);

        $relation->addEagerConstraints($models);

        $constraints($relation);

        // Once we have the results, we just match those back up to their parent models
        // using the relationship instance. Then we just return the finished arrays
        // of models which have been eagerly hydrated and are readied for return.
        return $relation->match(
            $relation->initRelation($models, $name),
            $relation->getEager(), $name
        );
    }

    /**
     * Get the relation instance for the given relation name.
     *
     * @param  string  $name
     * @return \Illuminate\Database\Eloquent\Relations\Relation<\Illuminate\Database\Eloquent\Model, TModel, *>
     */
    public function getRelation($name)
    {
        // We want to run a relationship query without any constrains so that we will
        // not have to remove these where clauses manually which gets really hacky
        // and error prone. We don't want constraints because we add eager ones.
        $relation = Relation::noConstraints(function () use ($name) {
            try {
                return $this->getModel()->newInstance()->$name();
            } catch (BadMethodCallException) {
                throw RelationNotFoundException::make($this->getModel(), $name);
            }
        });

        $nested = $this->relationsNestedUnder($name);

        // If there are nested relationships set on the query, we will put those onto
        // the query instances so that they can be handled after this relationship
        // is loaded. In this way they will all trickle down as they are loaded.
        if (count($nested) > 0) {
            $relation->getQuery()->with($nested);
        }

        return $relation;
    }

    /**
     * Get the deeply nested relations for a given top-level relation.
     *
     * @param  string  $relation
     * @return array
     */
    protected function relationsNestedUnder($relation)
    {
        $nested = [];

        // We are basically looking for any relationships that are nested deeper than
        // the given top-level relationship. We will just check for any relations
        // that start with the given top relations and adds them to our arrays.
        foreach ($this->eagerLoad as $name => $constraints) {
            if ($this->isNestedUnder($relation, $name)) {
                $nested[substr($name, strlen($relation.'.'))] = $constraints;
            }
        }

        return $nested;
    }

    /**
     * Determine if the relationship is nested.
     *
     * @param  string  $relation
     * @param  string  $name
     * @return bool
     */
    protected function isNestedUnder($relation, $name)
    {
        return str_contains($name, '.') && str_starts_with($name, $relation.'.');
    }

    /**
     * Register a closure to be invoked after the query is executed.
     *
     * @param  \Closure  $callback
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
     * Get a lazy collection for the given query.
     *
     * @return \Illuminate\Support\LazyCollection<int, TModel>
     */
    public function cursor()
    {
        return $this->applyScopes()->query->cursor()->map(function ($record) {
            $model = $this->newModelInstance()->newFromBuilder($record);

            return $this->applyAfterQueryCallbacks($this->newModelInstance()->newCollection([$model]))->first();
        })->reject(fn ($model) => is_null($model));
    }

    /**
     * Add a generic "order by" clause if the query doesn't already have one.
     *
     * @return void
     */
    protected function enforceOrderBy()
    {
        if (empty($this->query->orders) && empty($this->query->unionOrders)) {
            $this->orderBy($this->model->getQualifiedKeyName(), 'asc');
        }
    }

    /**
     * Get a collection with the values of a given column.
     *
     * @param  string|\Illuminate\Contracts\Database\Query\Expression  $column
     * @param  string|null  $key
     * @return \Illuminate\Support\Collection<array-key, mixed>
     */
    public function pluck($column, $key = null)
    {
        $results = $this->toBase()->pluck($column, $key);

        $column = $column instanceof Expression ? $column->getValue($this->getGrammar()) : $column;

        $column = Str::after($column, "{$this->model->getTable()}.");

        // If the model has a mutator for the requested column, we will spin through
        // the results and mutate the values so that the mutated version of these
        // columns are returned as you would expect from these Eloquent models.
        if (! $this->model->hasAnyGetMutator($column) &&
            ! $this->model->hasCast($column) &&
            ! in_array($column, $this->model->getDates())) {
            return $this->applyAfterQueryCallbacks($results);
        }

        return $this->applyAfterQueryCallbacks(
            $results->map(function ($value) use ($column) {
                return $this->model->newFromBuilder([$column => $value])->{$column};
            })
        );
    }

    /**
     * Paginate the given query.
     *
     * @param  int|null|\Closure  $perPage
     * @param  array|string  $columns
     * @param  string  $pageName
     * @param  int|null  $page
     * @param  \Closure|int|null  $total
     * @return \Illuminate\Pagination\LengthAwarePaginator<int, TModel>
     *
     * @throws \InvalidArgumentException
     */
    public function paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null, $total = null)
    {
        $page = $page ?: Paginator::resolveCurrentPage($pageName);

        $total = value($total) ?? $this->toBase()->getCountForPagination();

        $perPage = value($perPage, $total) ?: $this->model->getPerPage();

        $results = $total
            ? $this->forPage($page, $perPage)->get($columns)
            : $this->model->newCollection();

        return $this->paginator($results, $total, $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => $pageName,
        ]);
    }

    /**
     * Paginate the given query into a simple paginator.
     *
     * @param  int|null  $perPage
     * @param  array|string  $columns
     * @param  string  $pageName
     * @param  int|null  $page
     * @return \Illuminate\Pagination\Paginator<int, TModel>
     */
    public function simplePaginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
    {
        $page = $page ?: Paginator::resolveCurrentPage($pageName);

        $perPage = $perPage ?: $this->model->getPerPage();

        // Next we will set the limit and offset for this query so that when we get the
        // results we get the proper section of results. Then, we'll create the full
        // paginator instances for these results with the given page and per page.
        $this->offset(($page - 1) * $perPage)->limit($perPage + 1);

        return $this->simplePaginator($this->get($columns), $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => $pageName,
        ]);
    }

    /**
     * Paginate the given query into a cursor paginator.
     *
     * @param  int|null  $perPage
     * @param  array|string  $columns
     * @param  string  $cursorName
     * @param  \Illuminate\Pagination\Cursor|string|null  $cursor
     * @return \Illuminate\Pagination\CursorPaginator<int, TModel>
     */
    public function cursorPaginate($perPage = null, $columns = ['*'], $cursorName = 'cursor', $cursor = null)
    {
        $perPage = $perPage ?: $this->model->getPerPage();

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
        if (empty($this->query->orders) && empty($this->query->unionOrders)) {
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
            $this->query->orders = (new BaseCollection($this->query->orders))->map($reverseDirection)->toArray();
            $this->query->unionOrders = (new BaseCollection($this->query->unionOrders))->map($reverseDirection)->toArray();
        }

        $orders = ! empty($this->query->unionOrders) ? $this->query->unionOrders : $this->query->orders;

        return (new BaseCollection($orders))
            ->filter(fn ($order) => Arr::has($order, 'direction'))
            ->values();
    }

    /**
     * Save a new model and return the instance.
     *
     * @param  array  $attributes
     * @return TModel
     */
    public function create(array $attributes = [])
    {
        return tap($this->newModelInstance($attributes), function ($instance) {
            $instance->save();
        });
    }

    /**
     * Save a new model and return the instance without raising model events.
     *
     * @param  array  $attributes
     * @return TModel
     */
    public function createQuietly(array $attributes = [])
    {
        return Model::withoutEvents(fn () => $this->create($attributes));
    }

    /**
     * Save a new model and return the instance. Allow mass-assignment.
     *
     * @param  array  $attributes
     * @return TModel
     */
    public function forceCreate(array $attributes)
    {
        return $this->model->unguarded(function () use ($attributes) {
            return $this->newModelInstance()->create($attributes);
        });
    }

    /**
     * Save a new model instance with mass assignment without raising model events.
     *
     * @param  array  $attributes
     * @return TModel
     */
    public function forceCreateQuietly(array $attributes = [])
    {
        return Model::withoutEvents(fn () => $this->forceCreate($attributes));
    }

    /**
     * Update records in the database.
     *
     * @param  array  $values
     * @return int
     */
    public function update(array $values)
    {
        return $this->toBase()->update($this->addUpdatedAtColumn($values));
    }

    /**
     * Insert new records or update the existing ones.
     *
     * @param  array  $values
     * @param  array|string  $uniqueBy
     * @param  array|null  $update
     * @return int
     */
    public function upsert(array $values, $uniqueBy, $update = null)
    {
        if (empty($values)) {
            return 0;
        }

        if (! is_array(array_first($values))) {
            $values = [$values];
        }

        if (is_null($update)) {
            $update = array_keys(array_first($values));
        }

        return $this->toBase()->upsert(
            $this->addTimestampsToUpsertValues($this->addUniqueIdsToUpsertValues($values)),
            $uniqueBy,
            $this->addUpdatedAtToUpsertColumns($update)
        );
    }

    /**
     * Update the column's update timestamp.
     *
     * @param  array|string|null  $column
     * @return int|false
     */
    public function touch($column = null)
    {
        $time = $this->model->freshTimestamp();

        if ($column) {
            $columns = (new BaseCollection(Arr::wrap($column)))->mapWithKeys(fn ($column) => [$column => $time])->all();

            return $this->toBase()->update($columns);
        }

        $column = $this->model->getUpdatedAtColumn();

        if (! $this->model->usesTimestamps() || is_null($column)) {
            return false;
        }

        return $this->toBase()->update([$column => $time]);
    }

    /**
     * Increment a column's value by a given amount.
     *
     * @param  string|\Illuminate\Contracts\Database\Query\Expression  $column
     * @param  float|int  $amount
     * @param  array  $extra
     * @return int
     */
    public function increment($column, $amount = 1, array $extra = [])
    {
        return $this->toBase()->increment(
            $column, $amount, $this->addUpdatedAtColumn($extra)
        );
    }

    /**
     * Decrement a column's value by a given amount.
     *
     * @param  string|\Illuminate\Contracts\Database\Query\Expression  $column
     * @param  float|int  $amount
     * @param  array  $extra
     * @return int
     */
    public function decrement($column, $amount = 1, array $extra = [])
    {
        return $this->toBase()->decrement(
            $column, $amount, $this->addUpdatedAtColumn($extra)
        );
    }

    /**
     * Add the "updated at" column to an array of values.
     *
     * @param  array  $values
     * @return array
     */
    protected function addUpdatedAtColumn(array $values)
    {
        if (! $this->model->usesTimestamps() ||
            is_null($this->model->getUpdatedAtColumn())) {
            return $values;
        }

        $column = $this->model->getUpdatedAtColumn();

        if (! array_key_exists($column, $values)) {
            $timestamp = $this->model->freshTimestampString();

            if (
                $this->model->hasSetMutator($column)
                || $this->model->hasAttributeSetMutator($column)
                || $this->model->hasCast($column)
            ) {
                $timestamp = $this->model->newInstance()
                    ->forceFill([$column => $timestamp])
                    ->getAttributes()[$column] ?? $timestamp;
            }

            $values = array_merge([$column => $timestamp], $values);
        }

        $segments = preg_split('/\s+as\s+/i', $this->query->from);

        $qualifiedColumn = array_last($segments).'.'.$column;

        $values[$qualifiedColumn] = Arr::get($values, $qualifiedColumn, $values[$column]);

        unset($values[$column]);

        return $values;
    }

    /**
     * Add unique IDs to the inserted values.
     *
     * @param  array  $values
     * @return array
     */
    protected function addUniqueIdsToUpsertValues(array $values)
    {
        if (! $this->model->usesUniqueIds()) {
            return $values;
        }

        foreach ($this->model->uniqueIds() as $uniqueIdAttribute) {
            foreach ($values as &$row) {
                if (! array_key_exists($uniqueIdAttribute, $row)) {
                    $row = array_merge([$uniqueIdAttribute => $this->model->newUniqueId()], $row);
                }
            }
        }

        return $values;
    }

    /**
     * Add timestamps to the inserted values.
     *
     * @param  array  $values
     * @return array
     */
    protected function addTimestampsToUpsertValues(array $values)
    {
        if (! $this->model->usesTimestamps()) {
            return $values;
        }

        $timestamp = $this->model->freshTimestampString();

        $columns = array_filter([
            $this->model->getCreatedAtColumn(),
            $this->model->getUpdatedAtColumn(),
        ]);

        foreach ($columns as $column) {
            foreach ($values as &$row) {
                $row = array_merge([$column => $timestamp], $row);
            }
        }

        return $values;
    }

    /**
     * Add the "updated at" column to the updated columns.
     *
     * @param  array  $update
     * @return array
     */
    protected function addUpdatedAtToUpsertColumns(array $update)
    {
        if (! $this->model->usesTimestamps()) {
            return $update;
        }

        $column = $this->model->getUpdatedAtColumn();

        if (! is_null($column) &&
            ! array_key_exists($column, $update) &&
            ! in_array($column, $update)) {
            $update[] = $column;
        }

        return $update;
    }

    /**
     * Delete records from the database.
     *
     * @return mixed
     */
    public function delete()
    {
        if (isset($this->onDelete)) {
            return call_user_func($this->onDelete, $this);
        }

        return $this->toBase()->delete();
    }

    /**
     * Run the default delete function on the builder.
     *
     * Since we do not apply scopes here, the row will actually be deleted.
     *
     * @return mixed
     */
    public function forceDelete()
    {
        return $this->query->delete();
    }

    /**
     * Register a replacement for the default delete function.
     *
     * @param  \Closure  $callback
     * @return void
     */
    public function onDelete(Closure $callback)
    {
        $this->onDelete = $callback;
    }

    /**
     * Determine if the given model has a scope.
     *
     * @param  string  $scope
     * @return bool
     */
    public function hasNamedScope($scope)
    {
        return $this->model && $this->model->hasNamedScope($scope);
    }

    /**
     * Call the given local model scopes.
     *
     * @param  array|string  $scopes
     * @return static|mixed
     */
    public function scopes($scopes)
    {
        $builder = $this;

        foreach (Arr::wrap($scopes) as $scope => $parameters) {
            // If the scope key is an integer, then the scope was passed as the value and
            // the parameter list is empty, so we will format the scope name and these
            // parameters here. Then, we'll be ready to call the scope on the model.
            if (is_int($scope)) {
                [$scope, $parameters] = [$parameters, []];
            }

            // Next we'll pass the scope callback to the callScope method which will take
            // care of grouping the "wheres" properly so the logical order doesn't get
            // messed up when adding scopes. Then we'll return back out the builder.
            $builder = $builder->callNamedScope(
                $scope, Arr::wrap($parameters)
            );
        }

        return $builder;
    }

    /**
     * Apply the scopes to the Eloquent builder instance and return it.
     *
     * @return static
     */
    public function applyScopes()
    {
        if (! $this->scopes) {
            return $this;
        }

        $builder = clone $this;

        foreach ($this->scopes as $identifier => $scope) {
            if (! isset($builder->scopes[$identifier])) {
                continue;
            }

            $builder->callScope(function (self $builder) use ($scope) {
                // If the scope is a Closure we will just go ahead and call the scope with the
                // builder instance. The "callScope" method will properly group the clauses
                // that are added to this query so "where" clauses maintain proper logic.
                if ($scope instanceof Closure) {
                    $scope($builder);
                }

                // If the scope is a scope object, we will call the apply method on this scope
                // passing in the builder and the model instance. After we run all of these
                // scopes we will return back the builder instance to the outside caller.
                if ($scope instanceof Scope) {
                    $scope->apply($builder, $this->getModel());
                }
            });
        }

        return $builder;
    }

    /**
     * Apply the given scope on the current builder instance.
     *
     * @param  callable  $scope
     * @param  array  $parameters
     * @return mixed
     */
    protected function callScope(callable $scope, array $parameters = [])
    {
        array_unshift($parameters, $this);

        $query = $this->getQuery();

        // We will keep track of how many wheres are on the query before running the
        // scope so that we can properly group the added scope constraints in the
        // query as their own isolated nested where statement and avoid issues.
        $originalWhereCount = is_null($query->wheres)
            ? 0
            : count($query->wheres);

        $result = $scope(...$parameters) ?? $this;

        if (count((array) $query->wheres) > $originalWhereCount) {
            $this->addNewWheresWithinGroup($query, $originalWhereCount);
        }

        return $result;
    }

    /**
     * Apply the given named scope on the current builder instance.
     *
     * @param  string  $scope
     * @param  array  $parameters
     * @return mixed
     */
    protected function callNamedScope($scope, array $parameters = [])
    {
        return $this->callScope(function (...$parameters) use ($scope) {
            return $this->model->callNamedScope($scope, $parameters);
        }, $parameters);
    }

    /**
     * Nest where conditions by slicing them at the given where count.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  int  $originalWhereCount
     * @return void
     */
    protected function addNewWheresWithinGroup(QueryBuilder $query, $originalWhereCount)
    {
        // Here, we totally remove all of the where clauses since we are going to
        // rebuild them as nested queries by slicing the groups of wheres into
        // their own sections. This is to prevent any confusing logic order.
        $allWheres = $query->wheres;

        $query->wheres = [];

        $this->groupWhereSliceForScope(
            $query, array_slice($allWheres, 0, $originalWhereCount)
        );

        $this->groupWhereSliceForScope(
            $query, array_slice($allWheres, $originalWhereCount)
        );
    }

    /**
     * Slice where conditions at the given offset and add them to the query as a nested condition.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $whereSlice
     * @return void
     */
    protected function groupWhereSliceForScope(QueryBuilder $query, $whereSlice)
    {
        $whereBooleans = (new BaseCollection($whereSlice))->pluck('boolean');

        // Here we'll check if the given subset of where clauses contains any "or"
        // booleans and in this case create a nested where expression. That way
        // we don't add any unnecessary nesting thus keeping the query clean.
        if ($whereBooleans->contains(fn ($logicalOperator) => str_contains($logicalOperator, 'or'))) {
            $query->wheres[] = $this->createNestedWhere(
                $whereSlice, str_replace(' not', '', $whereBooleans->first())
            );
        } else {
            $query->wheres = array_merge($query->wheres, $whereSlice);
        }
    }

    /**
     * Create a where array with nested where conditions.
     *
     * @param  array  $whereSlice
     * @param  string  $boolean
     * @return array
     */
    protected function createNestedWhere($whereSlice, $boolean = 'and')
    {
        $whereGroup = $this->getQuery()->forNestedWhere();

        $whereGroup->wheres = $whereSlice;

        return ['type' => 'Nested', 'query' => $whereGroup, 'boolean' => $boolean];
    }

    /**
     * Specify relationships that should be eager loaded.
     *
     * @param  array<array-key, array|(\Closure(\Illuminate\Database\Eloquent\Relations\Relation<*,*,*>): mixed)|string>|string  $relations
     * @param  (\Closure(\Illuminate\Database\Eloquent\Relations\Relation<*,*,*>): mixed)|string|null  $callback
     * @return $this
     */
    public function with($relations, $callback = null)
    {
        if ($callback instanceof Closure) {
            $eagerLoad = $this->parseWithRelations([$relations => $callback]);
        } else {
            $eagerLoad = $this->parseWithRelations(is_string($relations) ? func_get_args() : $relations);
        }

        $this->eagerLoad = array_merge($this->eagerLoad, $eagerLoad);

        return $this;
    }

    /**
     * Prevent the specified relations from being eager loaded.
     *
     * @param  mixed  $relations
     * @return $this
     */
    public function without($relations)
    {
        $this->eagerLoad = array_diff_key($this->eagerLoad, array_flip(
            is_string($relations) ? func_get_args() : $relations
        ));

        return $this;
    }

    /**
     * Set the relationships that should be eager loaded while removing any previously added eager loading specifications.
     *
     * @param  array<array-key, array|(\Closure(\Illuminate\Database\Eloquent\Relations\Relation<*,*,*>): mixed)|string>|string  $relations
     * @return $this
     */
    public function withOnly($relations)
    {
        $this->eagerLoad = [];

        return $this->with($relations);
    }

    /**
     * Create a new instance of the model being queried.
     *
     * @param  array  $attributes
     * @return TModel
     */
    public function newModelInstance($attributes = [])
    {
        $attributes = array_merge($this->pendingAttributes, $attributes);

        return $this->model->newInstance($attributes)->setConnection(
            $this->query->getConnection()->getName()
        );
    }

    /**
     * Parse a list of relations into individuals.
     *
     * @param  array  $relations
     * @return array
     */
    protected function parseWithRelations(array $relations)
    {
        if ($relations === []) {
            return [];
        }

        $results = [];

        foreach ($this->prepareNestedWithRelationships($relations) as $name => $constraints) {
            // We need to separate out any nested includes, which allows the developers
            // to load deep relationships using "dots" without stating each level of
            // the relationship with its own key in the array of eager-load names.
            $results = $this->addNestedWiths($name, $results);

            $results[$name] = $constraints;
        }

        return $results;
    }

    /**
     * Prepare nested with relationships.
     *
     * @param  array  $relations
     * @param  string  $prefix
     * @return array
     */
    protected function prepareNestedWithRelationships($relations, $prefix = '')
    {
        $preparedRelationships = [];

        if ($prefix !== '') {
            $prefix .= '.';
        }

        // If any of the relationships are formatted with the [$attribute => array()]
        // syntax, we shall loop over the nested relations and prepend each key of
        // this array while flattening into the traditional dot notation format.
        foreach ($relations as $key => $value) {
            if (! is_string($key) || ! is_array($value)) {
                continue;
            }

            [$attribute, $attributeSelectConstraint] = $this->parseNameAndAttributeSelectionConstraint($key);

            $preparedRelationships = array_merge(
                $preparedRelationships,
                ["{$prefix}{$attribute}" => $attributeSelectConstraint],
                $this->prepareNestedWithRelationships($value, "{$prefix}{$attribute}"),
            );

            unset($relations[$key]);
        }

        // We now know that the remaining relationships are in a dot notation format
        // and may be a string or Closure. We'll loop over them and ensure all of
        // the present Closures are merged + strings are made into constraints.
        foreach ($relations as $key => $value) {
            if (is_numeric($key) && is_string($value)) {
                [$key, $value] = $this->parseNameAndAttributeSelectionConstraint($value);
            }

            $preparedRelationships[$prefix.$key] = $this->combineConstraints([
                $value,
                $preparedRelationships[$prefix.$key] ?? static function () {
                    //
                },
            ]);
        }

        return $preparedRelationships;
    }

    /**
     * Combine an array of constraints into a single constraint.
     *
     * @param  array  $constraints
     * @return \Closure
     */
    protected function combineConstraints(array $constraints)
    {
        return function ($builder) use ($constraints) {
            foreach ($constraints as $constraint) {
                $builder = $constraint($builder) ?? $builder;
            }

            return $builder;
        };
    }

    /**
     * Parse the attribute select constraints from the name.
     *
     * @param  string  $name
     * @return array
     */
    protected function parseNameAndAttributeSelectionConstraint($name)
    {
        return str_contains($name, ':')
            ? $this->createSelectWithConstraint($name)
            : [$name, static function () {
                //
            }];
    }

    /**
     * Create a constraint to select the given columns for the relation.
     *
     * @param  string  $name
     * @return array
     */
    protected function createSelectWithConstraint($name)
    {
        return [explode(':', $name)[0], static function ($query) use ($name) {
            $query->select(array_map(static function ($column) use ($query) {
                return $query instanceof BelongsToMany
                    ? $query->getRelated()->qualifyColumn($column)
                    : $column;
            }, explode(',', explode(':', $name)[1])));
        }];
    }

    /**
     * Parse the nested relationships in a relation.
     *
     * @param  string  $name
     * @param  array  $results
     * @return array
     */
    protected function addNestedWiths($name, $results)
    {
        $progress = [];

        // If the relation has already been set on the result array, we will not set it
        // again, since that would override any constraints that were already placed
        // on the relationships. We will only set the ones that are not specified.
        foreach (explode('.', $name) as $segment) {
            $progress[] = $segment;

            if (! isset($results[$last = implode('.', $progress)])) {
                $results[$last] = static function () {
                    //
                };
            }
        }

        return $results;
    }

    /**
     * Specify attributes that should be added to any new models created by this builder.
     *
     * The given key / value pairs will also be added as where conditions to the query.
     *
     * @param  \Illuminate\Contracts\Database\Query\Expression|array|string  $attributes
     * @param  mixed  $value
     * @param  bool  $asConditions
     * @return $this
     */
    public function withAttributes(Expression|array|string $attributes, $value = null, $asConditions = true)
    {
        if (! is_array($attributes)) {
            $attributes = [$attributes => $value];
        }

        if ($asConditions) {
            foreach ($attributes as $column => $value) {
                $this->where($this->qualifyColumn($column), $value);
            }
        }

        $this->pendingAttributes = array_merge($this->pendingAttributes, $attributes);

        return $this;
    }

    /**
     * Apply query-time casts to the model instance.
     *
     * @param  array  $casts
     * @return $this
     */
    public function withCasts($casts)
    {
        $this->model->mergeCasts($casts);

        return $this;
    }

    /**
     * Execute the given Closure within a transaction savepoint if needed.
     *
     * @template TModelValue
     *
     * @param  \Closure(): TModelValue  $scope
     * @return TModelValue
     */
    public function withSavepointIfNeeded(Closure $scope): mixed
    {
        return $this->getQuery()->getConnection()->transactionLevel() > 0
            ? $this->getQuery()->getConnection()->transaction($scope)
            : $scope();
    }

    /**
     * Get the Eloquent builder instances that are used in the union of the query.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getUnionBuilders()
    {
        return isset($this->query->unions)
            ? (new BaseCollection($this->query->unions))->pluck('query')
            : new BaseCollection;
    }

    /**
     * Get the underlying query builder instance.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Set the underlying query builder instance.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return $this
     */
    public function setQuery($query)
    {
        $this->query = $query;

        return $this;
    }

    /**
     * Get a base query builder instance.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function toBase()
    {
        return $this->applyScopes()->getQuery();
    }

    /**
     * Get the relationships being eagerly loaded.
     *
     * @return array
     */
    public function getEagerLoads()
    {
        return $this->eagerLoad;
    }

    /**
     * Set the relationships being eagerly loaded.
     *
     * @param  array  $eagerLoad
     * @return $this
     */
    public function setEagerLoads(array $eagerLoad)
    {
        $this->eagerLoad = $eagerLoad;

        return $this;
    }

    /**
     * Indicate that the given relationships should not be eagerly loaded.
     *
     * @param  array  $relations
     * @return $this
     */
    public function withoutEagerLoad(array $relations)
    {
        $relations = array_diff(array_keys($this->model->getRelations()), $relations);

        return $this->with($relations);
    }

    /**
     * Flush the relationships being eagerly loaded.
     *
     * @return $this
     */
    public function withoutEagerLoads()
    {
        return $this->setEagerLoads([]);
    }

    /**
     * Get the "limit" value from the query or null if it's not set.
     *
     * @return mixed
     */
    public function getLimit()
    {
        return $this->query->getLimit();
    }

    /**
     * Get the "offset" value from the query or null if it's not set.
     *
     * @return mixed
     */
    public function getOffset()
    {
        return $this->query->getOffset();
    }

    /**
     * Get the default key name of the table.
     *
     * @return string
     */
    protected function defaultKeyName()
    {
        return $this->getModel()->getKeyName();
    }

    /**
     * Get the model instance being queried.
     *
     * @return TModel
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Set a model instance for the model being queried.
     *
     * @template TModelNew of \Illuminate\Database\Eloquent\Model
     *
     * @param  TModelNew  $model
     * @return static<TModelNew>
     */
    public function setModel(Model $model)
    {
        $this->model = $model;

        $this->query->from($model->getTable());

        return $this;
    }

    /**
     * Qualify the given column name by the model's table.
     *
     * @param  string|\Illuminate\Contracts\Database\Query\Expression  $column
     * @return string
     */
    public function qualifyColumn($column)
    {
        $column = $column instanceof Expression ? $column->getValue($this->getGrammar()) : $column;

        return $this->model->qualifyColumn($column);
    }

    /**
     * Qualify the given columns with the model's table.
     *
     * @param  array|\Illuminate\Contracts\Database\Query\Expression  $columns
     * @return array
     */
    public function qualifyColumns($columns)
    {
        return $this->model->qualifyColumns($columns);
    }

    /**
     * Get the given macro by name.
     *
     * @param  string  $name
     * @return \Closure
     */
    public function getMacro($name)
    {
        return Arr::get($this->localMacros, $name);
    }

    /**
     * Checks if a macro is registered.
     *
     * @param  string  $name
     * @return bool
     */
    public function hasMacro($name)
    {
        return isset($this->localMacros[$name]);
    }

    /**
     * Get the given global macro by name.
     *
     * @param  string  $name
     * @return \Closure
     */
    public static function getGlobalMacro($name)
    {
        return Arr::get(static::$macros, $name);
    }

    /**
     * Checks if a global macro is registered.
     *
     * @param  string  $name
     * @return bool
     */
    public static function hasGlobalMacro($name)
    {
        return isset(static::$macros[$name]);
    }

    /**
     * Dynamically access builder proxies.
     *
     * @param  string  $key
     * @return mixed
     *
     * @throws \Exception
     */
    public function __get($key)
    {
        if (in_array($key, ['orWhere', 'whereNot', 'orWhereNot'])) {
            return new HigherOrderBuilderProxy($this, $key);
        }

        if (in_array($key, $this->propertyPassthru)) {
            return $this->toBase()->{$key};
        }

        throw new Exception("Property [{$key}] does not exist on the Eloquent builder instance.");
    }

    /**
     * Dynamically handle calls into the query instance.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if ($method === 'macro') {
            $this->localMacros[$parameters[0]] = $parameters[1];

            return;
        }

        if ($this->hasMacro($method)) {
            array_unshift($parameters, $this);

            return $this->localMacros[$method](...$parameters);
        }

        if (static::hasGlobalMacro($method)) {
            $callable = static::$macros[$method];

            if ($callable instanceof Closure) {
                $callable = $callable->bindTo($this, static::class);
            }

            return $callable(...$parameters);
        }

        if ($this->hasNamedScope($method)) {
            return $this->callNamedScope($method, $parameters);
        }

        if (in_array(strtolower($method), $this->passthru)) {
            return $this->toBase()->{$method}(...$parameters);
        }

        $this->forwardCallTo($this->query, $method, $parameters);

        return $this;
    }

    /**
     * Dynamically handle calls into the query instance.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     *
     * @throws \BadMethodCallException
     */
    public static function __callStatic($method, $parameters)
    {
        if ($method === 'macro') {
            static::$macros[$parameters[0]] = $parameters[1];

            return;
        }

        if ($method === 'mixin') {
            return static::registerMixin($parameters[0], $parameters[1] ?? true);
        }

        if (! static::hasGlobalMacro($method)) {
            static::throwBadMethodCallException($method);
        }

        $callable = static::$macros[$method];

        if ($callable instanceof Closure) {
            $callable = $callable->bindTo(null, static::class);
        }

        return $callable(...$parameters);
    }

    /**
     * Register the given mixin with the builder.
     *
     * @param  string  $mixin
     * @param  bool  $replace
     * @return void
     */
    protected static function registerMixin($mixin, $replace)
    {
        $methods = (new ReflectionClass($mixin))->getMethods(
            ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED
        );

        foreach ($methods as $method) {
            if ($replace || ! static::hasGlobalMacro($method->name)) {
                static::macro($method->name, $method->invoke($mixin));
            }
        }
    }

    /**
     * Clone the Eloquent query builder.
     *
     * @return static
     */
    public function clone()
    {
        return clone $this;
    }

    /**
     * Register a closure to be invoked on a clone.
     *
     * @param  \Closure  $callback
     * @return $this
     */
    public function onClone(Closure $callback)
    {
        $this->onCloneCallbacks[] = $callback;

        return $this;
    }

    /**
     * Force a clone of the underlying query builder when cloning.
     *
     * @return void
     */
    public function __clone()
    {
        $this->query = clone $this->query;

        foreach ($this->onCloneCallbacks as $onCloneCallback) {
            $onCloneCallback($this);
        }
    }
}
