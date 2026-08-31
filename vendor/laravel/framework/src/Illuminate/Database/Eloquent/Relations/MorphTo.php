<?php

namespace Illuminate\Database\Eloquent\Relations;

use BadMethodCallException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Concerns\InteractsWithDictionary;
use Illuminate\Support\Arr;

/**
 * @template TRelatedModel of \Illuminate\Database\Eloquent\Model
 * @template TDeclaringModel of \Illuminate\Database\Eloquent\Model
 *
 * @extends \Illuminate\Database\Eloquent\Relations\BelongsTo<TRelatedModel, TDeclaringModel>
 */
class MorphTo extends BelongsTo
{
    use InteractsWithDictionary;

    /**
     * The type of the polymorphic relation.
     *
     * @var string
     */
    protected $morphType;

    /**
     * The associated key on the parent model.
     *
     * @var string|null
     */
    protected $ownerKey;

    /**
     * The models whose relations are being eager loaded.
     *
     * @var \Illuminate\Database\Eloquent\Collection<int, TDeclaringModel>
     */
    protected $models;

    /**
     * All of the models keyed by ID.
     *
     * @var array
     */
    protected $dictionary = [];

    /**
     * A buffer of dynamic calls to query macros.
     *
     * @var array
     */
    protected $macroBuffer = [];

    /**
     * A map of relations to load for each individual morph type.
     *
     * @var array
     */
    protected $morphableEagerLoads = [];

    /**
     * A map of relationship counts to load for each individual morph type.
     *
     * @var array
     */
    protected $morphableEagerLoadCounts = [];

    /**
     * A map of constraints to apply for each individual morph type.
     *
     * @var array
     */
    protected $morphableConstraints = [];

    /**
     * Create a new morph to relationship instance.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<TRelatedModel>  $query
     * @param  TDeclaringModel  $parent
     * @param  string  $foreignKey
     * @param  string|null  $ownerKey
     * @param  string  $type
     * @param  string  $relation
     */
    public function __construct(Builder $query, Model $parent, $foreignKey, $ownerKey, $type, $relation)
    {
        $this->morphType = $type;

        parent::__construct($query, $parent, $foreignKey, $ownerKey, $relation);
    }

    /** @inheritDoc */
    #[\Override]
    public function addEagerConstraints(array $models)
    {
        $this->buildDictionary($this->models = new EloquentCollection($models));
    }

    /**
     * Build a dictionary with the models.
     *
     * @param  \Illuminate\Database\Eloquent\Collection<int, TRelatedModel>  $models
     * @return void
     */
    protected function buildDictionary(EloquentCollection $models)
    {
        $isAssociative = Arr::isAssoc($models->all());

        foreach ($models as $key => $model) {
            if ($model->{$this->morphType}) {
                $morphTypeKey = $this->getDictionaryKey($model->{$this->morphType});
                $foreignKeyKey = $this->getDictionaryKey($model->{$this->foreignKey});

                if ($morphTypeKey === null || $foreignKeyKey === null) {
                    continue;
                }

                if ($isAssociative) {
                    $this->dictionary[$morphTypeKey][$foreignKeyKey][$key] = $model;
                } else {
                    $this->dictionary[$morphTypeKey][$foreignKeyKey][] = $model;
                }
            }
        }
    }

    /**
     * Get the results of the relationship.
     *
     * Called via eager load method of Eloquent query builder.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, TDeclaringModel>
     */
    public function getEager()
    {
        foreach (array_keys($this->dictionary) as $type) {
            $this->matchToMorphParents($type, $this->getResultsByType($type));
        }

        return $this->models;
    }

    /**
     * Get all of the relation results for a type.
     *
     * @param  string  $type
     * @return \Illuminate\Database\Eloquent\Collection<int, TRelatedModel>
     */
    protected function getResultsByType($type)
    {
        $instance = $this->createModelByType($type);

        $ownerKey = $this->ownerKey ?? $instance->getKeyName();

        $query = $this->replayMacros($instance->newQuery())
            ->mergeConstraintsFrom($this->getQuery())
            ->with(array_merge(
                $this->getQuery()->getEagerLoads(),
                (array) ($this->morphableEagerLoads[get_class($instance)] ?? [])
            ))
            ->withCount(
                (array) ($this->morphableEagerLoadCounts[get_class($instance)] ?? [])
            );

        if ($callback = ($this->morphableConstraints[get_class($instance)] ?? null)) {
            $callback($query);
        }

        $whereIn = $this->whereInMethod($instance, $ownerKey);

        return $query->{$whereIn}(
            $instance->qualifyColumn($ownerKey), $this->gatherKeysByType($type, $instance->getKeyType())
        )->get();
    }

    /**
     * Gather all of the foreign keys for a given type.
     *
     * @param  string  $type
     * @param  string  $keyType
     * @return array
     */
    protected function gatherKeysByType($type, $keyType)
    {
        return $keyType !== 'string'
            ? array_keys($this->dictionary[$type])
            : array_map(function ($modelId) {
                return (string) $modelId;
            }, array_filter(array_keys($this->dictionary[$type])));
    }

    /**
     * Create a new model instance by type.
     *
     * @param  string  $type
     * @return TRelatedModel
     */
    public function createModelByType($type)
    {
        $class = Model::getActualClassNameForMorph($type);

        return tap(new $class, function ($instance) {
            if (! $instance->getConnectionName()) {
                $instance->setConnection($this->getConnection()->getName());
            }
        });
    }

    /** @inheritDoc */
    #[\Override]
    public function match(array $models, EloquentCollection $results, $relation)
    {
        return $models;
    }

    /**
     * Match the results for a given type to their parents.
     *
     * @param  string  $type
     * @param  \Illuminate\Database\Eloquent\Collection<int, TRelatedModel>  $results
     * @return void
     */
    protected function matchToMorphParents($type, EloquentCollection $results)
    {
        foreach ($results as $result) {
            $ownerKey = ! is_null($this->ownerKey) ? $this->getDictionaryKey($result->{$this->ownerKey}) : $result->getKey();

            if ($ownerKey !== null && isset($this->dictionary[$type][$ownerKey])) {
                foreach ($this->dictionary[$type][$ownerKey] as $model) {
                    $model->setRelation($this->relationName, $result);
                }
            }
        }
    }

    /**
     * Associate the model instance to the given parent.
     *
     * @param  TRelatedModel|null  $model
     * @return TDeclaringModel
     */
    #[\Override]
    public function associate($model)
    {
        if ($model instanceof Model) {
            $foreignKey = $this->ownerKey && $model->{$this->ownerKey}
                ? $this->ownerKey
                : $model->getKeyName();
        }

        $this->parent->setAttribute(
            $this->foreignKey, $model instanceof Model ? $model->{$foreignKey} : null
        );

        $this->parent->setAttribute(
            $this->morphType, $model instanceof Model ? $model->getMorphClass() : null
        );

        return $this->parent->setRelation($this->relationName, $model);
    }

    /**
     * Dissociate previously associated model from the given parent.
     *
     * @return TDeclaringModel
     */
    #[\Override]
    public function dissociate()
    {
        $this->parent->setAttribute($this->foreignKey, null);

        $this->parent->setAttribute($this->morphType, null);

        return $this->parent->setRelation($this->relationName, null);
    }

    /** @inheritDoc */
    #[\Override]
    public function touch()
    {
        if (! is_null($this->getParentKey())) {
            parent::touch();
        }
    }

    /** @inheritDoc */
    #[\Override]
    protected function newRelatedInstanceFor(Model $parent)
    {
        return $parent->{$this->getRelationName()}()->getRelated()->newInstance();
    }

    /**
     * Get the foreign key "type" name.
     *
     * @return string
     */
    public function getMorphType()
    {
        return $this->morphType;
    }

    /**
     * Get the dictionary used by the relationship.
     *
     * @return array
     */
    public function getDictionary()
    {
        return $this->dictionary;
    }

    /**
     * Specify which relations to load for a given morph type.
     *
     * @param  array  $with
     * @return $this
     */
    public function morphWith(array $with)
    {
        $this->morphableEagerLoads = array_merge(
            $this->morphableEagerLoads, $with
        );

        return $this;
    }

    /**
     * Specify which relationship counts to load for a given morph type.
     *
     * @param  array  $withCount
     * @return $this
     */
    public function morphWithCount(array $withCount)
    {
        $this->morphableEagerLoadCounts = array_merge(
            $this->morphableEagerLoadCounts, $withCount
        );

        return $this;
    }

    /**
     * Specify constraints on the query for a given morph type.
     *
     * @param  array  $callbacks
     * @return $this
     */
    public function constrain(array $callbacks)
    {
        $this->morphableConstraints = array_merge(
            $this->morphableConstraints, $callbacks
        );

        return $this;
    }

    /**
     * Indicate that soft deleted models should be included in the results.
     *
     * @return $this
     */
    public function withTrashed()
    {
        $callback = fn ($query) => $query->hasMacro('withTrashed') ? $query->withTrashed() : $query;

        $this->macroBuffer[] = [
            'method' => 'when',
            'parameters' => [true, $callback],
        ];

        return $this->when(true, $callback);
    }

    /**
     * Indicate that soft deleted models should not be included in the results.
     *
     * @return $this
     */
    public function withoutTrashed()
    {
        $callback = fn ($query) => $query->hasMacro('withoutTrashed') ? $query->withoutTrashed() : $query;

        $this->macroBuffer[] = [
            'method' => 'when',
            'parameters' => [true, $callback],
        ];

        return $this->when(true, $callback);
    }

    /**
     * Indicate that only soft deleted models should be included in the results.
     *
     * @return $this
     */
    public function onlyTrashed()
    {
        $callback = fn ($query) => $query->hasMacro('onlyTrashed') ? $query->onlyTrashed() : $query;

        $this->macroBuffer[] = [
            'method' => 'when',
            'parameters' => [true, $callback],
        ];

        return $this->when(true, $callback);
    }

    /**
     * Replay stored macro calls on the actual related instance.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<TRelatedModel>  $query
     * @return \Illuminate\Database\Eloquent\Builder<TRelatedModel>
     */
    protected function replayMacros(Builder $query)
    {
        foreach ($this->macroBuffer as $macro) {
            $query->{$macro['method']}(...$macro['parameters']);
        }

        return $query;
    }

    /** @inheritDoc */
    #[\Override]
    public function getQualifiedOwnerKeyName()
    {
        if (is_null($this->ownerKey)) {
            return '';
        }

        return parent::getQualifiedOwnerKeyName();
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
        try {
            $result = parent::__call($method, $parameters);

            if (in_array($method, ['select', 'selectRaw', 'selectSub', 'addSelect', 'withoutGlobalScopes'])) {
                $this->macroBuffer[] = compact('method', 'parameters');
            }

            return $result;
        }

        // If we tried to call a method that does not exist on the parent Builder instance,
        // we'll assume that we want to call a query macro (e.g. withTrashed) that only
        // exists on related models. We will just store the call and replay it later.
        catch (BadMethodCallException) {
            $this->macroBuffer[] = compact('method', 'parameters');

            return $this;
        }
    }
}
