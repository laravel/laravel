<?php

namespace Illuminate\Database\Eloquent\Concerns;

use Closure;
use Illuminate\Database\ClassMorphViolationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\PendingHasThroughRelationship;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait HasRelationships
{
    /**
     * The loaded relationships for the model.
     *
     * @var array
     */
    protected $relations = [];

    /**
     * The relationships that should be touched on save.
     *
     * @var array
     */
    protected $touches = [];

    /**
     * The relationship autoloader callback.
     *
     * @var \Closure|null
     */
    protected $relationAutoloadCallback = null;

    /**
     * The relationship autoloader callback context.
     *
     * @var mixed
     */
    protected $relationAutoloadContext = null;

    /**
     * The many to many relationship methods.
     *
     * @var string[]
     */
    public static $manyMethods = [
        'belongsToMany', 'morphToMany', 'morphedByMany',
    ];

    /**
     * The relation resolver callbacks.
     *
     * @var array
     */
    protected static $relationResolvers = [];

    /**
     * Get the dynamic relation resolver if defined or inherited, or return null.
     *
     * @template TRelatedModel of \Illuminate\Database\Eloquent\Model
     *
     * @param  class-string<TRelatedModel>  $class
     * @param  string  $key
     * @return Closure|null
     */
    public function relationResolver($class, $key)
    {
        if ($resolver = static::$relationResolvers[$class][$key] ?? null) {
            return $resolver;
        }

        if ($parent = get_parent_class($class)) {
            return $this->relationResolver($parent, $key);
        }

        return null;
    }

    /**
     * Define a dynamic relation resolver.
     *
     * @param  string  $name
     * @param  \Closure  $callback
     * @return void
     */
    public static function resolveRelationUsing($name, Closure $callback)
    {
        static::$relationResolvers = array_replace_recursive(
            static::$relationResolvers,
            [static::class => [$name => $callback]]
        );
    }

    /**
     * Determine if a relationship autoloader callback has been defined.
     *
     * @return bool
     */
    public function hasRelationAutoloadCallback()
    {
        return ! is_null($this->relationAutoloadCallback);
    }

    /**
     * Define an automatic relationship autoloader callback for this model and its relations.
     *
     * @param  \Closure  $callback
     * @param  mixed  $context
     * @return $this
     */
    public function autoloadRelationsUsing(Closure $callback, $context = null)
    {
        // Prevent circular relation autoloading...
        if ($context && $this->relationAutoloadContext === $context) {
            return $this;
        }

        $this->relationAutoloadCallback = $callback;
        $this->relationAutoloadContext = $context;

        foreach ($this->relations as $key => $value) {
            $this->propagateRelationAutoloadCallbackToRelation($key, $value);
        }

        return $this;
    }

    /**
     * Attempt to autoload the given relationship using the autoload callback.
     *
     * @param  string  $key
     * @return bool
     */
    protected function attemptToAutoloadRelation($key)
    {
        if (! $this->hasRelationAutoloadCallback()) {
            return false;
        }

        $this->invokeRelationAutoloadCallbackFor($key, []);

        return $this->relationLoaded($key);
    }

    /**
     * Invoke the relationship autoloader callback for the given relationships.
     *
     * @param  string  $key
     * @param  array  $tuples
     * @return void
     */
    protected function invokeRelationAutoloadCallbackFor($key, $tuples)
    {
        $tuples = array_merge([[$key, get_class($this)]], $tuples);

        call_user_func($this->relationAutoloadCallback, $tuples);
    }

    /**
     * Propagate the relationship autoloader callback to the given related models.
     *
     * @param  string  $key
     * @param  mixed  $models
     * @return void
     */
    protected function propagateRelationAutoloadCallbackToRelation($key, $models)
    {
        if (! $this->hasRelationAutoloadCallback() || ! $models) {
            return;
        }

        if ($models instanceof Model) {
            $models = [$models];
        }

        if (! is_iterable($models)) {
            return;
        }

        $callback = fn (array $tuples) => $this->invokeRelationAutoloadCallbackFor($key, $tuples);

        foreach ($models as $model) {
            $model->autoloadRelationsUsing($callback, $this->relationAutoloadContext);
        }
    }

    /**
     * Define a one-to-one relationship.
     *
     * @template TRelatedModel of \Illuminate\Database\Eloquent\Model
     *
     * @param  class-string<TRelatedModel>  $related
     * @param  string|null  $foreignKey
     * @param  string|null  $localKey
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<TRelatedModel, $this>
     */
    public function hasOne($related, $foreignKey = null, $localKey = null)
    {
        $instance = $this->newRelatedInstance($related);

        $foreignKey = $foreignKey ?: $this->getForeignKey();

        $localKey = $localKey ?: $this->getKeyName();

        return $this->newHasOne($instance->newQuery(), $this, $instance->qualifyColumn($foreignKey), $localKey);
    }

    /**
     * Instantiate a new HasOne relationship.
     *
     * @template TRelatedModel of \Illuminate\Database\Eloquent\Model
     * @template TDeclaringModel of \Illuminate\Database\Eloquent\Model
     *
     * @param  \Illuminate\Database\Eloquent\Builder<TRelatedModel>  $query
     * @param  TDeclaringModel  $parent
     * @param  string  $foreignKey
     * @param  string  $localKey
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<TRelatedModel, TDeclaringModel>
     */
    protected function newHasOne(Builder $query, Model $parent, $foreignKey, $localKey)
    {
        return new HasOne($query, $parent, $foreignKey, $localKey);
    }

    /**
     * Define a has-one-through relationship.
     *
     * @template TRelatedModel of \Illuminate\Database\Eloquent\Model
     * @template TIntermediateModel of \Illuminate\Database\Eloquent\Model
     *
     * @param  class-string<TRelatedModel>  $related
     * @param  class-string<TIntermediateModel>  $through
     * @param  string|null  $firstKey
     * @param  string|null  $secondKey
     * @param  string|null  $localKey
     * @param  string|null  $secondLocalKey
     * @return \Illuminate\Database\Eloquent\Relations\HasOneThrough<TRelatedModel, TIntermediateModel, $this>
     */
    public function hasOneThrough($related, $through, $firstKey = null, $secondKey = null, $localKey = null, $secondLocalKey = null)
    {
        $through = $this->newRelatedThroughInstance($through);

        $firstKey = $firstKey ?: $this->getForeignKey();

        $secondKey = $secondKey ?: $through->getForeignKey();

        return $this->newHasOneThrough(
            $this->newRelatedInstance($related)->newQuery(),
            $this,
            $through,
            $firstKey,
            $secondKey,
            $localKey ?: $this->getKeyName(),
            $secondLocalKey ?: $through->getKeyName(),
        );
    }

    /**
     * Instantiate a new HasOneThrough relationship.
     *
     * @template TRelatedModel of \Illuminate\Database\Eloquent\Model
     * @template TIntermediateModel of \Illuminate\Database\Eloquent\Model
     * @template TDeclaringModel of \Illuminate\Database\Eloquent\Model
     *
     * @param  \Illuminate\Database\Eloquent\Builder<TRelatedModel>  $query
     * @param  TDeclaringModel  $farParent
     * @param  TIntermediateModel  $throughParent
     * @param  string  $firstKey
     * @param  string  $secondKey
     * @param  string  $localKey
     * @param  string  $secondLocalKey
     * @return \Illuminate\Database\Eloquent\Relations\HasOneThrough<TRelatedModel, TIntermediateModel, TDeclaringModel>
     */
    protected function newHasOneThrough(Builder $query, Model $farParent, Model $throughParent, $firstKey, $secondKey, $localKey, $secondLocalKey)
    {
        return new HasOneThrough($query, $farParent, $throughParent, $firstKey, $secondKey, $localKey, $secondLocalKey);
    }

    /**
     * Define a polymorphic one-to-one relationship.
     *
     * @template TRelatedModel of \Illuminate\Database\Eloquent\Model
     *
     * @param  class-string<TRelatedModel>  $related
     * @param  string  $name
     * @param  string|null  $type
     * @param  string|null  $id
     * @param  string|null  $localKey
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne<TRelatedModel, $this>
     */
    public function morphOne($related, $name, $type = null, $id = null, $localKey = null)
    {
        $instance = $this->newRelatedInstance($related);

        [$type, $id] = $this->getMorphs($name, $type, $id);

        $localKey = $localKey ?: $this->getKeyName();

        return $this->newMorphOne($instance->newQuery(), $this, $instance->qualifyColumn($type), $instance->qualifyColumn($id), $localKey);
    }

    /**
     * Instantiate a new MorphOne relationship.
     *
     * @template TRelatedModel of \Illuminate\Database\Eloquent\Model
     * @template TDeclaringModel of \Illuminate\Database\Eloquent\Model
     *
     * @param  \Illuminate\Database\Eloquent\Builder<TRelatedModel>  $query
     * @param  TDeclaringModel  $parent
     * @param  string  $type
     * @param  string  $id
     * @param  string  $localKey
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne<TRelatedModel, TDeclaringModel>
     */
    protected function newMorphOne(Builder $query, Model $parent, $type, $id, $localKey)
    {
        return new MorphOne($query, $parent, $type, $id, $localKey);
    }

    /**
     * Define an inverse one-to-one or many relationship.
     *
     * @template TRelatedModel of \Illuminate\Database\Eloquent\Model
     *
     * @param  class-string<TRelatedModel>  $related
     * @param  string|null  $foreignKey
     * @param  string|null  $ownerKey
     * @param  string|null  $relation
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<TRelatedModel, $this>
     */
    public function belongsTo($related, $foreignKey = null, $ownerKey = null, $relation = null)
    {
        // If no relation name was given, we will use this debug backtrace to extract
        // the calling method's name and use that as the relationship name as most
        // of the time this will be what we desire to use for the relationships.
        if (is_null($relation)) {
            $relation = $this->guessBelongsToRelation();
        }

        $instance = $this->newRelatedInstance($related);

        // If no foreign key was supplied, we can use a backtrace to guess the proper
        // foreign key name by using the name of the relationship function, which
        // when combined with an "_id" should conventionally match the columns.
        if (is_null($foreignKey)) {
            $foreignKey = Str::snake($relation).'_'.$instance->getKeyName();
        }

        // Once we have the foreign key names we'll just create a new Eloquent query
        // for the related models and return the relationship instance which will
        // actually be responsible for retrieving and hydrating every relation.
        $ownerKey = $ownerKey ?: $instance->getKeyName();

        return $this->newBelongsTo(
            $instance->newQuery(), $this, $foreignKey, $ownerKey, $relation
        );
    }

    /**
     * Instantiate a new BelongsTo relationship.
     *
     * @template TRelatedModel of \Illuminate\Database\Eloquent\Model
     * @template TDeclaringModel of \Illuminate\Database\Eloquent\Model
     *
     * @param  \Illuminate\Database\Eloquent\Builder<TRelatedModel>  $query
     * @param  TDeclaringModel  $child
     * @param  string  $foreignKey
     * @param  string  $ownerKey
     * @param  string  $relation
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<TRelatedModel, TDeclaringModel>
     */
    protected function newBelongsTo(Builder $query, Model $child, $foreignKey, $ownerKey, $relation)
    {
        return new BelongsTo($query, $child, $foreignKey, $ownerKey, $relation);
    }

    /**
     * Define a polymorphic, inverse one-to-one or many relationship.
     *
     * @param  string|null  $name
     * @param  string|null  $type
     * @param  string|null  $id
     * @param  string|null  $ownerKey
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo<\Illuminate\Database\Eloquent\Model, $this>
     */
    public function morphTo($name = null, $type = null, $id = null, $ownerKey = null)
    {
        // If no name is provided, we will use the backtrace to get the function name
        // since that is most likely the name of the polymorphic interface. We can
        // use that to get both the class and foreign key that will be utilized.
        $name = $name ?: $this->guessBelongsToRelation();

        [$type, $id] = $this->getMorphs(
            Str::snake($name), $type, $id
        );

        // If the type value is null it is probably safe to assume we're eager loading
        // the relationship. In this case we'll just pass in a dummy query where we
        // need to remove any eager loads that may already be defined on a model.
        return is_null($class = $this->getAttributeFromArray($type)) || $class === ''
            ? $this->morphEagerTo($name, $type, $id, $ownerKey)
            : $this->morphInstanceTo($class, $name, $type, $id, $ownerKey);
    }

    /**
     * Define a polymorphic, inverse one-to-one or many relationship.
     *
     * @param  string  $name
     * @param  string  $type
     * @param  string  $id
     * @param  string|null  $ownerKey
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo<\Illuminate\Database\Eloquent\Model, $this>
     */
    protected function morphEagerTo($name, $type, $id, $ownerKey)
    {
        return $this->newMorphTo(
            $this->newQuery()->setEagerLoads([]), $this, $id, $ownerKey, $type, $name
        );
    }

    /**
     * Define a polymorphic, inverse one-to-one or many relationship.
     *
     * @param  string  $target
     * @param  string  $name
     * @param  string  $type
     * @param  string  $id
     * @param  string|null  $ownerKey
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo<\Illuminate\Database\Eloquent\Model, $this>
     */
    protected function morphInstanceTo($target, $name, $type, $id, $ownerKey)
    {
        $instance = $this->newRelatedInstance(
            static::getActualClassNameForMorph($target)
        );

        return $this->newMorphTo(
            $instance->newQuery(), $this, $id, $ownerKey ?? $instance->getKeyName(), $type, $name
        );
    }

    /**
     * Instantiate a new MorphTo relationship.
     *
     * @template TRelatedModel of \Illuminate\Database\Eloquent\Model
     * @template TDeclaringModel of \Illuminate\Database\Eloquent\Model
     *
     * @param  \Illuminate\Database\Eloquent\Builder<TRelatedModel>  $query
     * @param  TDeclaringModel  $parent
     * @param  string  $foreignKey
     * @param  string|null  $ownerKey
     * @param  string  $type
     * @param  string  $relation
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo<TRelatedModel, TDeclaringModel>
     */
    protected function newMorphTo(Builder $query, Model $parent, $foreignKey, $ownerKey, $type, $relation)
    {
        return new MorphTo($query, $parent, $foreignKey, $ownerKey, $type, $relation);
    }

    /**
     * Retrieve the actual class name for a given morph class.
     *
     * @param  string  $class
     * @return string
     */
    public static function getActualClassNameForMorph($class)
    {
        return Arr::get(Relation::morphMap() ?: [], $class, $class);
    }

    /**
     * Guess the "belongs to" relationship name.
     *
     * @return string
     */
    protected function guessBelongsToRelation()
    {
        [, , $caller] = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);

        return $caller['function'];
    }

    /**
     * Create a pending has-many-through or has-one-through relationship.
     *
     * @template TIntermediateModel of \Illuminate\Database\Eloquent\Model
     *
     * @param  string|\Illuminate\Database\Eloquent\Relations\HasMany<TIntermediateModel, covariant $this>|\Illuminate\Database\Eloquent\Relations\HasOne<TIntermediateModel, covariant $this>  $relationship
     * @return (
     *     $relationship is string
     *     ? \Illuminate\Database\Eloquent\PendingHasThroughRelationship<\Illuminate\Database\Eloquent\Model, $this>
     *     : (
     *          $relationship is \Illuminate\Database\Eloquent\Relations\HasMany<TIntermediateModel, $this>
     *          ? \Illuminate\Database\Eloquent\PendingHasThroughRelationship<TIntermediateModel, $this, \Illuminate\Database\Eloquent\Relations\HasMany<TIntermediateModel, $this>>
     *          : \Illuminate\Database\Eloquent\PendingHasThroughRelationship<TIntermediateModel, $this, \Illuminate\Database\Eloquent\Relations\HasOne<TIntermediateModel, $this>>
     *     )
     * )
     */
    public function through($relationship)
    {
        if (is_string($relationship)) {
            $relationship = $this->{$relationship}();
        }

        return new PendingHasThroughRelationship($this, $relationship);
    }

    /**
     * Define a one-to-many relationship.
     *
     * @template TRelatedModel of \Illuminate\Database\Eloquent\Model
     *
     * @param  class-string<TRelatedModel>  $related
     * @param  string|null  $foreignKey
     * @param  string|null  $localKey
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<TRelatedModel, $this>
     */
    public function hasMany($related, $foreignKey = null, $localKey = null)
    {
        $instance = $this->newRelatedInstance($related);

        $foreignKey = $foreignKey ?: $this->getForeignKey();

        $localKey = $localKey ?: $this->getKeyName();

        return $this->newHasMany(
            $instance->newQuery(), $this, $instance->qualifyColumn($foreignKey), $localKey
        );
    }

    /**
     * Instantiate a new HasMany relationship.
     *
     * @template TRelatedModel of \Illuminate\Database\Eloquent\Model
     * @template TDeclaringModel of \Illuminate\Database\Eloquent\Model
     *
     * @param  \Illuminate\Database\Eloquent\Builder<TRelatedModel>  $query
     * @param  TDeclaringModel  $parent
     * @param  string  $foreignKey
     * @param  string  $localKey
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<TRelatedModel, TDeclaringModel>
     */
    protected function newHasMany(Builder $query, Model $parent, $foreignKey, $localKey)
    {
        return new HasMany($query, $parent, $foreignKey, $localKey);
    }

    /**
     * Define a has-many-through relationship.
     *
     * @template TRelatedModel of \Illuminate\Database\Eloquent\Model
     * @template TIntermediateModel of \Illuminate\Database\Eloquent\Model
     *
     * @param  class-string<TRelatedModel>  $related
     * @param  class-string<TIntermediateModel>  $through
     * @param  string|null  $firstKey
     * @param  string|null  $secondKey
     * @param  string|null  $localKey
     * @param  string|null  $secondLocalKey
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough<TRelatedModel, TIntermediateModel, $this>
     */
    public function hasManyThrough($related, $through, $firstKey = null, $secondKey = null, $localKey = null, $secondLocalKey = null)
    {
        $through = $this->newRelatedThroughInstance($through);

        $firstKey = $firstKey ?: $this->getForeignKey();

        $secondKey = $secondKey ?: $through->getForeignKey();

        return $this->newHasManyThrough(
            $this->newRelatedInstance($related)->newQuery(),
            $this,
            $through,
            $firstKey,
            $secondKey,
            $localKey ?: $this->getKeyName(),
            $secondLocalKey ?: $through->getKeyName()
        );
    }

    /**
     * Instantiate a new HasManyThrough relationship.
     *
     * @template TRelatedModel of \Illuminate\Database\Eloquent\Model
     * @template TIntermediateModel of \Illuminate\Database\Eloquent\Model
     * @template TDeclaringModel of \Illuminate\Database\Eloquent\Model
     *
     * @param  \Illuminate\Database\Eloquent\Builder<TRelatedModel>  $query
     * @param  TDeclaringModel  $farParent
     * @param  TIntermediateModel  $throughParent
     * @param  string  $firstKey
     * @param  string  $secondKey
     * @param  string  $localKey
     * @param  string  $secondLocalKey
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough<TRelatedModel, TIntermediateModel, TDeclaringModel>
     */
    protected function newHasManyThrough(Builder $query, Model $farParent, Model $throughParent, $firstKey, $secondKey, $localKey, $secondLocalKey)
    {
        return new HasManyThrough($query, $farParent, $throughParent, $firstKey, $secondKey, $localKey, $secondLocalKey);
    }

    /**
     * Define a polymorphic one-to-many relationship.
     *
     * @template TRelatedModel of \Illuminate\Database\Eloquent\Model
     *
     * @param  class-string<TRelatedModel>  $related
     * @param  string  $name
     * @param  string|null  $type
     * @param  string|null  $id
     * @param  string|null  $localKey
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<TRelatedModel, $this>
     */
    public function morphMany($related, $name, $type = null, $id = null, $localKey = null)
    {
        $instance = $this->newRelatedInstance($related);

        // Here we will gather up the morph type and ID for the relationship so that we
        // can properly query the intermediate table of a relation. Finally, we will
        // get the table and create the relationship instances for the developers.
        [$type, $id] = $this->getMorphs($name, $type, $id);

        $localKey = $localKey ?: $this->getKeyName();

        return $this->newMorphMany($instance->newQuery(), $this, $instance->qualifyColumn($type), $instance->qualifyColumn($id), $localKey);
    }

    /**
     * Instantiate a new MorphMany relationship.
     *
     * @template TRelatedModel of \Illuminate\Database\Eloquent\Model
     * @template TDeclaringModel of \Illuminate\Database\Eloquent\Model
     *
     * @param  \Illuminate\Database\Eloquent\Builder<TRelatedModel>  $query
     * @param  TDeclaringModel  $parent
     * @param  string  $type
     * @param  string  $id
     * @param  string  $localKey
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<TRelatedModel, TDeclaringModel>
     */
    protected function newMorphMany(Builder $query, Model $parent, $type, $id, $localKey)
    {
        return new MorphMany($query, $parent, $type, $id, $localKey);
    }

    /**
     * Define a many-to-many relationship.
     *
     * @template TRelatedModel of \Illuminate\Database\Eloquent\Model
     *
     * @param  class-string<TRelatedModel>  $related
     * @param  string|class-string<\Illuminate\Database\Eloquent\Model>|null  $table
     * @param  string|null  $foreignPivotKey
     * @param  string|null  $relatedPivotKey
     * @param  string|null  $parentKey
     * @param  string|null  $relatedKey
     * @param  string|null  $relation
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<TRelatedModel, $this, \Illuminate\Database\Eloquent\Relations\Pivot>
     */
    public function belongsToMany(
        $related,
        $table = null,
        $foreignPivotKey = null,
        $relatedPivotKey = null,
        $parentKey = null,
        $relatedKey = null,
        $relation = null,
    ) {
        // If no relationship name was passed, we will pull backtraces to get the
        // name of the calling function. We will use that function name as the
        // title of this relation since that is a great convention to apply.
        if (is_null($relation)) {
            $relation = $this->guessBelongsToManyRelation();
        }

        // First, we'll need to determine the foreign key and "other key" for the
        // relationship. Once we have determined the keys we'll make the query
        // instances as well as the relationship instances we need for this.
        $instance = $this->newRelatedInstance($related);

        $foreignPivotKey = $foreignPivotKey ?: $this->getForeignKey();

        $relatedPivotKey = $relatedPivotKey ?: $instance->getForeignKey();

        // If no table name was provided, we can guess it by concatenating the two
        // models using underscores in alphabetical order. The two model names
        // are transformed to snake case from their default CamelCase also.
        if (is_null($table)) {
            $table = $this->joiningTable($related, $instance);
        }

        return $this->newBelongsToMany(
            $instance->newQuery(),
            $this,
            $table,
            $foreignPivotKey,
            $relatedPivotKey,
            $parentKey ?: $this->getKeyName(),
            $relatedKey ?: $instance->getKeyName(),
            $relation,
        );
    }

    /**
     * Instantiate a new BelongsToMany relationship.
     *
     * @template TRelatedModel of \Illuminate\Database\Eloquent\Model
     * @template TDeclaringModel of \Illuminate\Database\Eloquent\Model
     *
     * @param  \Illuminate\Database\Eloquent\Builder<TRelatedModel>  $query
     * @param  TDeclaringModel  $parent
     * @param  string|class-string<\Illuminate\Database\Eloquent\Model>  $table
     * @param  string  $foreignPivotKey
     * @param  string  $relatedPivotKey
     * @param  string  $parentKey
     * @param  string  $relatedKey
     * @param  string|null  $relationName
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<TRelatedModel, TDeclaringModel, \Illuminate\Database\Eloquent\Relations\Pivot>
     */
    protected function newBelongsToMany(
        Builder $query,
        Model $parent,
        $table,
        $foreignPivotKey,
        $relatedPivotKey,
        $parentKey,
        $relatedKey,
        $relationName = null,
    ) {
        return new BelongsToMany($query, $parent, $table, $foreignPivotKey, $relatedPivotKey, $parentKey, $relatedKey, $relationName);
    }

    /**
     * Define a polymorphic many-to-many relationship.
     *
     * @template TRelatedModel of \Illuminate\Database\Eloquent\Model
     *
     * @param  class-string<TRelatedModel>  $related
     * @param  string  $name
     * @param  string|null  $table
     * @param  string|null  $foreignPivotKey
     * @param  string|null  $relatedPivotKey
     * @param  string|null  $parentKey
     * @param  string|null  $relatedKey
     * @param  string|null  $relation
     * @param  bool  $inverse
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany<TRelatedModel, $this>
     */
    public function morphToMany(
        $related,
        $name,
        $table = null,
        $foreignPivotKey = null,
        $relatedPivotKey = null,
        $parentKey = null,
        $relatedKey = null,
        $relation = null,
        $inverse = false,
    ) {
        $relation = $relation ?: $this->guessBelongsToManyRelation();

        // First, we will need to determine the foreign key and "other key" for the
        // relationship. Once we have determined the keys we will make the query
        // instances, as well as the relationship instances we need for these.
        $instance = $this->newRelatedInstance($related);

        $foreignPivotKey = $foreignPivotKey ?: $name.'_id';

        $relatedPivotKey = $relatedPivotKey ?: $instance->getForeignKey();

        // Now we're ready to create a new query builder for the related model and
        // the relationship instances for this relation. This relation will set
        // appropriate query constraints then entirely manage the hydrations.
        if (! $table) {
            $words = preg_split('/(_)/u', $name, -1, PREG_SPLIT_DELIM_CAPTURE);

            $lastWord = array_pop($words);

            $table = implode('', $words).Str::plural($lastWord);
        }

        return $this->newMorphToMany(
            $instance->newQuery(),
            $this,
            $name,
            $table,
            $foreignPivotKey,
            $relatedPivotKey,
            $parentKey ?: $this->getKeyName(),
            $relatedKey ?: $instance->getKeyName(),
            $relation,
            $inverse,
        );
    }

    /**
     * Instantiate a new MorphToMany relationship.
     *
     * @template TRelatedModel of \Illuminate\Database\Eloquent\Model
     * @template TDeclaringModel of \Illuminate\Database\Eloquent\Model
     *
     * @param  \Illuminate\Database\Eloquent\Builder<TRelatedModel>  $query
     * @param  TDeclaringModel  $parent
     * @param  string  $name
     * @param  string  $table
     * @param  string  $foreignPivotKey
     * @param  string  $relatedPivotKey
     * @param  string  $parentKey
     * @param  string  $relatedKey
     * @param  string|null  $relationName
     * @param  bool  $inverse
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany<TRelatedModel, TDeclaringModel>
     */
    protected function newMorphToMany(
        Builder $query,
        Model $parent,
        $name,
        $table,
        $foreignPivotKey,
        $relatedPivotKey,
        $parentKey,
        $relatedKey,
        $relationName = null,
        $inverse = false,
    ) {
        return new MorphToMany(
            $query,
            $parent,
            $name,
            $table,
            $foreignPivotKey,
            $relatedPivotKey,
            $parentKey,
            $relatedKey,
            $relationName,
            $inverse,
        );
    }

    /**
     * Define a polymorphic, inverse many-to-many relationship.
     *
     * @template TRelatedModel of \Illuminate\Database\Eloquent\Model
     *
     * @param  class-string<TRelatedModel>  $related
     * @param  string  $name
     * @param  string|null  $table
     * @param  string|null  $foreignPivotKey
     * @param  string|null  $relatedPivotKey
     * @param  string|null  $parentKey
     * @param  string|null  $relatedKey
     * @param  string|null  $relation
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany<TRelatedModel, $this>
     */
    public function morphedByMany(
        $related,
        $name,
        $table = null,
        $foreignPivotKey = null,
        $relatedPivotKey = null,
        $parentKey = null,
        $relatedKey = null,
        $relation = null,
    ) {
        $foreignPivotKey = $foreignPivotKey ?: $this->getForeignKey();

        // For the inverse of the polymorphic many-to-many relations, we will change
        // the way we determine the foreign and other keys, as it is the opposite
        // of the morph-to-many method since we're figuring out these inverses.
        $relatedPivotKey = $relatedPivotKey ?: $name.'_id';

        return $this->morphToMany(
            $related,
            $name,
            $table,
            $foreignPivotKey,
            $relatedPivotKey,
            $parentKey,
            $relatedKey,
            $relation,
            true,
        );
    }

    /**
     * Get the relationship name of the belongsToMany relationship.
     *
     * @return string|null
     */
    protected function guessBelongsToManyRelation()
    {
        $caller = Arr::first(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), function ($trace) {
            return ! in_array(
                $trace['function'],
                array_merge(static::$manyMethods, ['guessBelongsToManyRelation'])
            );
        });

        return $caller['function'] ?? null;
    }

    /**
     * Get the joining table name for a many-to-many relation.
     *
     * @param  string  $related
     * @param  \Illuminate\Database\Eloquent\Model|null  $instance
     * @return string
     */
    public function joiningTable($related, $instance = null)
    {
        // The joining table name, by convention, is simply the snake cased models
        // sorted alphabetically and concatenated with an underscore, so we can
        // just sort the models and join them together to get the table name.
        $segments = [
            $instance
                ? $instance->joiningTableSegment()
                : Str::snake(class_basename($related)),
            $this->joiningTableSegment(),
        ];

        // Now that we have the model names in an array we can just sort them and
        // use the implode function to join them together with an underscores,
        // which is typically used by convention within the database system.
        sort($segments);

        return strtolower(implode('_', $segments));
    }

    /**
     * Get this model's half of the intermediate table name for belongsToMany relationships.
     *
     * @return string
     */
    public function joiningTableSegment()
    {
        return Str::snake(class_basename($this));
    }

    /**
     * Determine if the model touches a given relation.
     *
     * @param  string  $relation
     * @return bool
     */
    public function touches($relation)
    {
        return in_array($relation, $this->getTouchedRelations());
    }

    /**
     * Touch the owning relations of the model.
     *
     * @return void
     */
    public function touchOwners()
    {
        $this->withoutRecursion(function () {
            foreach ($this->getTouchedRelations() as $relation) {
                $this->$relation()->touch();

                if ($this->$relation instanceof self) {
                    $this->$relation->fireModelEvent('saved', false);

                    $this->$relation->touchOwners();
                } elseif ($this->$relation instanceof EloquentCollection) {
                    $this->$relation->each->touchOwners();
                }
            }
        });
    }

    /**
     * Get the polymorphic relationship columns.
     *
     * @param  string  $name
     * @param  string  $type
     * @param  string  $id
     * @return array
     */
    protected function getMorphs($name, $type, $id)
    {
        return [$type ?: $name.'_type', $id ?: $name.'_id'];
    }

    /**
     * Get the class name for polymorphic relations.
     *
     * @return string
     */
    public function getMorphClass()
    {
        $morphMap = Relation::morphMap();

        if (! empty($morphMap) && in_array(static::class, $morphMap)) {
            return array_search(static::class, $morphMap, true);
        }

        if (static::class === Pivot::class) {
            return static::class;
        }

        if (Relation::requiresMorphMap()) {
            throw new ClassMorphViolationException($this);
        }

        return static::class;
    }

    /**
     * Create a new model instance for a related model.
     *
     * @template TRelatedModel of \Illuminate\Database\Eloquent\Model
     *
     * @param  class-string<TRelatedModel>  $class
     * @return TRelatedModel
     */
    protected function newRelatedInstance($class)
    {
        return tap(new $class, function ($instance) {
            if (! $instance->getConnectionName()) {
                $instance->setConnection($this->connection);
            }
        });
    }

    /**
     * Create a new model instance for a related "through" model.
     *
     * @template TRelatedModel of \Illuminate\Database\Eloquent\Model
     *
     * @param  class-string<TRelatedModel>  $class
     * @return TRelatedModel
     */
    protected function newRelatedThroughInstance($class)
    {
        return new $class;
    }

    /**
     * Get all the loaded relations for the instance.
     *
     * @return array
     */
    public function getRelations()
    {
        return $this->relations;
    }

    /**
     * Get a specified relationship.
     *
     * @param  string  $relation
     * @return mixed
     */
    public function getRelation($relation)
    {
        return $this->relations[$relation];
    }

    /**
     * Determine if the given relation is loaded.
     *
     * @param  string  $key
     * @return bool
     */
    public function relationLoaded($key)
    {
        return array_key_exists($key, $this->relations);
    }

    /**
     * Set the given relationship on the model.
     *
     * @param  string  $relation
     * @param  mixed  $value
     * @return $this
     */
    public function setRelation($relation, $value)
    {
        $this->relations[$relation] = $value;

        $this->propagateRelationAutoloadCallbackToRelation($relation, $value);

        return $this;
    }

    /**
     * Unset a loaded relationship.
     *
     * @param  string  $relation
     * @return $this
     */
    public function unsetRelation($relation)
    {
        unset($this->relations[$relation]);

        return $this;
    }

    /**
     * Set the entire relations array on the model.
     *
     * @param  array  $relations
     * @return $this
     */
    public function setRelations(array $relations)
    {
        $this->relations = $relations;

        return $this;
    }

    /**
     * Enable relationship autoloading for this model.
     *
     * @return $this
     */
    public function withRelationshipAutoloading()
    {
        $this->newCollection([$this])->withRelationshipAutoloading();

        return $this;
    }

    /**
     * Duplicate the instance and unset all the loaded relations.
     *
     * @return $this
     */
    public function withoutRelations()
    {
        $model = clone $this;

        return $model->unsetRelations();
    }

    /**
     * Duplicate the instance and unset the given loaded relations.
     *
     * @param  array|string  $relations
     * @return $this
     */
    public function withoutRelation($relations)
    {
        $model = clone $this;

        foreach ((array) $relations as $relation) {
            $model->unsetRelation($relation);
        }

        return $model;
    }

    /**
     * Unset all the loaded relations for the instance.
     *
     * @return $this
     */
    public function unsetRelations()
    {
        $this->relations = [];

        return $this;
    }

    /**
     * Get the relationships that are touched on save.
     *
     * @return array
     */
    public function getTouchedRelations()
    {
        return $this->touches;
    }

    /**
     * Set the relationships that are touched on save.
     *
     * @param  array  $touches
     * @return $this
     */
    public function setTouchedRelations(array $touches)
    {
        $this->touches = $touches;

        return $this;
    }
}
