<?php

namespace Illuminate\Database\Eloquent\Relations;

use Closure;
use Illuminate\Contracts\Database\Eloquent\Builder as BuilderContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\MultipleRecordsFoundException;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Collection as BaseCollection;
use Illuminate\Support\Traits\ForwardsCalls;
use Illuminate\Support\Traits\Macroable;

/**
 * @template TRelatedModel of \Illuminate\Database\Eloquent\Model
 * @template TDeclaringModel of \Illuminate\Database\Eloquent\Model
 * @template TResult
 *
 * @mixin \Illuminate\Database\Eloquent\Builder<TRelatedModel>
 */
abstract class Relation implements BuilderContract
{
    use ForwardsCalls, Macroable {
        Macroable::__call as macroCall;
    }

    /**
     * The Eloquent query builder instance.
     *
     * @var \Illuminate\Database\Eloquent\Builder<TRelatedModel>
     */
    protected $query;

    /**
     * The parent model instance.
     *
     * @var TDeclaringModel
     */
    protected $parent;

    /**
     * The related model instance.
     *
     * @var TRelatedModel
     */
    protected $related;

    /**
     * Indicates whether the eagerly loaded relation should implicitly return an empty collection.
     *
     * @var bool
     */
    protected $eagerKeysWereEmpty = false;

    /**
     * Indicates if the relation is adding constraints.
     *
     * @var bool
     */
    protected static $constraints = true;

    /**
     * An array to map morph names to their class names in the database.
     *
     * @var array<string, class-string<\Illuminate\Database\Eloquent\Model>>
     */
    public static $morphMap = [];

    /**
     * Prevents morph relationships without a morph map.
     *
     * @var bool
     */
    protected static $requireMorphMap = false;

    /**
     * The count of self joins.
     *
     * @var int
     */
    protected static $selfJoinCount = 0;

    /**
     * Create a new relation instance.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<TRelatedModel>  $query
     * @param  TDeclaringModel  $parent
     */
    public function __construct(Builder $query, Model $parent)
    {
        $this->query = $query;
        $this->parent = $parent;
        $this->related = $query->getModel();

        $this->addConstraints();
    }

    /**
     * Run a callback with constraints disabled on the relation.
     *
     * @template TReturn of mixed
     *
     * @param  Closure(): TReturn  $callback
     * @return TReturn
     */
    public static function noConstraints(Closure $callback)
    {
        $previous = static::$constraints;

        static::$constraints = false;

        // When resetting the relation where clause, we want to shift the first element
        // off of the bindings, leaving only the constraints that the developers put
        // as "extra" on the relationships, and not original relation constraints.
        try {
            return $callback();
        } finally {
            static::$constraints = $previous;
        }
    }

    /**
     * Set the base constraints on the relation query.
     *
     * @return void
     */
    abstract public function addConstraints();

    /**
     * Set the constraints for an eager load of the relation.
     *
     * @param  array<int, TDeclaringModel>  $models
     * @return void
     */
    abstract public function addEagerConstraints(array $models);

    /**
     * Initialize the relation on a set of models.
     *
     * @param  array<int, TDeclaringModel>  $models
     * @param  string  $relation
     * @return array<int, TDeclaringModel>
     */
    abstract public function initRelation(array $models, $relation);

    /**
     * Match the eagerly loaded results to their parents.
     *
     * @param  array<int, TDeclaringModel>  $models
     * @param  \Illuminate\Database\Eloquent\Collection<int, TRelatedModel>  $results
     * @param  string  $relation
     * @return array<int, TDeclaringModel>
     */
    abstract public function match(array $models, EloquentCollection $results, $relation);

    /**
     * Get the results of the relationship.
     *
     * @return TResult
     */
    abstract public function getResults();

    /**
     * Get the relationship for eager loading.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, TRelatedModel>
     */
    public function getEager()
    {
        return $this->eagerKeysWereEmpty
            ? $this->related->newCollection()
            : $this->get();
    }

    /**
     * Execute the query and get the first result if it's the sole matching record.
     *
     * @param  array|string  $columns
     * @return TRelatedModel
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException<TRelatedModel>
     * @throws \Illuminate\Database\MultipleRecordsFoundException
     */
    public function sole($columns = ['*'])
    {
        $result = $this->limit(2)->get($columns);

        $count = $result->count();

        if ($count === 0) {
            throw (new ModelNotFoundException)->setModel(get_class($this->related));
        }

        if ($count > 1) {
            throw new MultipleRecordsFoundException($count);
        }

        return $result->first();
    }

    /**
     * Execute the query as a "select" statement.
     *
     * @param  array  $columns
     * @return \Illuminate\Database\Eloquent\Collection<int, TRelatedModel>
     */
    public function get($columns = ['*'])
    {
        return $this->query->get($columns);
    }

    /**
     * Touch all of the related models for the relationship.
     *
     * @return void
     */
    public function touch()
    {
        $model = $this->getRelated();

        if (! $model::isIgnoringTouch()) {
            $this->rawUpdate([
                $model->getUpdatedAtColumn() => $model->freshTimestampString(),
            ]);
        }
    }

    /**
     * Run a raw update against the base query.
     *
     * @param  array  $attributes
     * @return int
     */
    public function rawUpdate(array $attributes = [])
    {
        return $this->query->withoutGlobalScopes()->update($attributes);
    }

    /**
     * Add the constraints for a relationship count query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<TRelatedModel>  $query
     * @param  \Illuminate\Database\Eloquent\Builder<TDeclaringModel>  $parentQuery
     * @return \Illuminate\Database\Eloquent\Builder<TRelatedModel>
     */
    public function getRelationExistenceCountQuery(Builder $query, Builder $parentQuery)
    {
        return $this->getRelationExistenceQuery(
            $query, $parentQuery, new Expression('count(*)')
        )->setBindings([], 'select');
    }

    /**
     * Add the constraints for an internal relationship existence query.
     *
     * Essentially, these queries compare on column names like whereColumn.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<TRelatedModel>  $query
     * @param  \Illuminate\Database\Eloquent\Builder<TDeclaringModel>  $parentQuery
     * @param  mixed  $columns
     * @return \Illuminate\Database\Eloquent\Builder<TRelatedModel>
     */
    public function getRelationExistenceQuery(Builder $query, Builder $parentQuery, $columns = ['*'])
    {
        return $query->select($columns)->whereColumn(
            $this->getQualifiedParentKeyName(), '=', $this->getExistenceCompareKey()
        );
    }

    /**
     * Get a relationship join table hash.
     *
     * @param  bool  $incrementJoinCount
     * @return string
     */
    public function getRelationCountHash($incrementJoinCount = true)
    {
        return 'laravel_reserved_'.($incrementJoinCount ? static::$selfJoinCount++ : static::$selfJoinCount);
    }

    /**
     * Get all of the primary keys for an array of models.
     *
     * @param  array<int, TDeclaringModel>  $models
     * @param  string|null  $key
     * @return array<int, int|string|null>
     */
    protected function getKeys(array $models, $key = null)
    {
        return (new BaseCollection($models))->map(function ($value) use ($key) {
            return $key ? $value->getAttribute($key) : $value->getKey();
        })->values()->unique(null, true)->sort()->all();
    }

    /**
     * Get the query builder that will contain the relationship constraints.
     *
     * @return \Illuminate\Database\Eloquent\Builder<TRelatedModel>
     */
    protected function getRelationQuery()
    {
        return $this->query;
    }

    /**
     * Get the underlying query for the relation.
     *
     * @return \Illuminate\Database\Eloquent\Builder<TRelatedModel>
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Get the base query builder driving the Eloquent builder.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function getBaseQuery()
    {
        return $this->query->getQuery();
    }

    /**
     * Get a base query builder instance.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function toBase()
    {
        return $this->query->toBase();
    }

    /**
     * Get the parent model of the relation.
     *
     * @return TDeclaringModel
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Get the fully-qualified parent key name.
     *
     * @return string
     */
    public function getQualifiedParentKeyName()
    {
        return $this->parent->getQualifiedKeyName();
    }

    /**
     * Get the related model of the relation.
     *
     * @return TRelatedModel
     */
    public function getRelated()
    {
        return $this->related;
    }

    /**
     * Get the name of the "created at" column.
     *
     * @return string
     */
    public function createdAt()
    {
        return $this->parent->getCreatedAtColumn();
    }

    /**
     * Get the name of the "updated at" column.
     *
     * @return string
     */
    public function updatedAt()
    {
        return $this->parent->getUpdatedAtColumn();
    }

    /**
     * Get the name of the related model's "updated at" column.
     *
     * @return string
     */
    public function relatedUpdatedAt()
    {
        return $this->related->getUpdatedAtColumn();
    }

    /**
     * Add a whereIn eager constraint for the given set of model keys to be loaded.
     *
     * @param  string  $whereIn
     * @param  string  $key
     * @param  array  $modelKeys
     * @param  \Illuminate\Database\Eloquent\Builder<TRelatedModel>|null  $query
     * @return void
     */
    protected function whereInEager(string $whereIn, string $key, array $modelKeys, ?Builder $query = null)
    {
        ($query ?? $this->query)->{$whereIn}($key, $modelKeys);

        if ($modelKeys === []) {
            $this->eagerKeysWereEmpty = true;
        }
    }

    /**
     * Get the name of the "where in" method for eager loading.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @return string
     */
    protected function whereInMethod(Model $model, $key)
    {
        return $model->getKeyName() === last(explode('.', $key))
            && in_array($model->getKeyType(), ['int', 'integer'])
                ? 'whereIntegerInRaw'
                : 'whereIn';
    }

    /**
     * Prevent polymorphic relationships from being used without model mappings.
     *
     * @param  bool  $requireMorphMap
     * @return void
     */
    public static function requireMorphMap($requireMorphMap = true)
    {
        static::$requireMorphMap = $requireMorphMap;
    }

    /**
     * Determine if polymorphic relationships require explicit model mapping.
     *
     * @return bool
     */
    public static function requiresMorphMap()
    {
        return static::$requireMorphMap;
    }

    /**
     * Define the morph map for polymorphic relations and require all morphed models to be explicitly mapped.
     *
     * @param  array<array-key, class-string<\Illuminate\Database\Eloquent\Model>>  $map
     * @param  bool  $merge
     * @return array
     */
    public static function enforceMorphMap(array $map, $merge = true)
    {
        static::requireMorphMap();

        return static::morphMap($map, $merge);
    }

    /**
     * Set or get the morph map for polymorphic relations.
     *
     * @param  array<array-key, class-string<\Illuminate\Database\Eloquent\Model>>|null  $map
     * @param  bool  $merge
     * @return array<string, class-string<\Illuminate\Database\Eloquent\Model>>
     */
    public static function morphMap(?array $map = null, $merge = true)
    {
        $map = static::buildMorphMapFromModels($map);

        if (is_array($map)) {
            static::$morphMap = $merge && static::$morphMap
                ? $map + static::$morphMap
                : $map;
        }

        return static::$morphMap;
    }

    /**
     * Builds a table-keyed array from model class names.
     *
     * @param  array<array-key, class-string<\Illuminate\Database\Eloquent\Model>>|null  $models
     * @return array<string, class-string<\Illuminate\Database\Eloquent\Model>>|null
     */
    protected static function buildMorphMapFromModels(?array $models = null)
    {
        if (is_null($models) || ! array_is_list($models)) {
            return $models;
        }

        return array_combine(array_map(function ($model) {
            return (new $model)->getTable();
        }, $models), $models);
    }

    /**
     * Get the model associated with a custom polymorphic type.
     *
     * @param  string  $alias
     * @return class-string<\Illuminate\Database\Eloquent\Model>|null
     */
    public static function getMorphedModel($alias)
    {
        return static::$morphMap[$alias] ?? null;
    }

    /**
     * Get the alias associated with a custom polymorphic class.
     *
     * @param  class-string<\Illuminate\Database\Eloquent\Model>  $className
     * @return int|string
     */
    public static function getMorphAlias(string $className)
    {
        return array_search($className, static::$morphMap, strict: true) ?: $className;
    }

    /**
     * Handle dynamic method calls to the relationship.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        return $this->forwardDecoratedCallTo($this->query, $method, $parameters);
    }

    /**
     * Force a clone of the underlying query builder when cloning.
     *
     * @return void
     */
    public function __clone()
    {
        $this->query = clone $this->query;
    }
}
