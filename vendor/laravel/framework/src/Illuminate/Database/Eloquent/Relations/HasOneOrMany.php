<?php

namespace Illuminate\Database\Eloquent\Relations;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Concerns\InteractsWithDictionary;
use Illuminate\Database\Eloquent\Relations\Concerns\SupportsInverseRelations;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Arr;

/**
 * @template TRelatedModel of \Illuminate\Database\Eloquent\Model
 * @template TDeclaringModel of \Illuminate\Database\Eloquent\Model
 * @template TResult
 *
 * @extends \Illuminate\Database\Eloquent\Relations\Relation<TRelatedModel, TDeclaringModel, TResult>
 */
abstract class HasOneOrMany extends Relation
{
    use InteractsWithDictionary, SupportsInverseRelations;

    /**
     * The foreign key of the parent model.
     *
     * @var string
     */
    protected $foreignKey;

    /**
     * The local key of the parent model.
     *
     * @var string
     */
    protected $localKey;

    /**
     * Create a new has one or many relationship instance.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<TRelatedModel>  $query
     * @param  TDeclaringModel  $parent
     * @param  string  $foreignKey
     * @param  string  $localKey
     */
    public function __construct(Builder $query, Model $parent, $foreignKey, $localKey)
    {
        $this->localKey = $localKey;
        $this->foreignKey = $foreignKey;

        parent::__construct($query, $parent);
    }

    /**
     * Create and return an un-saved instance of the related model.
     *
     * @param  array  $attributes
     * @return TRelatedModel
     */
    public function make(array $attributes = [])
    {
        return tap($this->related->newInstance($attributes), function ($instance) {
            $this->setForeignAttributesForCreate($instance);
            $this->applyInverseRelationToModel($instance);
        });
    }

    /**
     * Create and return an un-saved instance of the related models.
     *
     * @param  iterable  $records
     * @return \Illuminate\Database\Eloquent\Collection<int, TRelatedModel>
     */
    public function makeMany($records)
    {
        $instances = $this->related->newCollection();

        foreach ($records as $record) {
            $instances->push($this->make($record));
        }

        return $instances;
    }

    /**
     * Set the base constraints on the relation query.
     *
     * @return void
     */
    public function addConstraints()
    {
        if (static::$constraints) {
            $query = $this->getRelationQuery();

            $query->where($this->foreignKey, '=', $this->getParentKey());

            $query->whereNotNull($this->foreignKey);
        }
    }

    /** @inheritDoc */
    public function addEagerConstraints(array $models)
    {
        $whereIn = $this->whereInMethod($this->parent, $this->localKey);

        $this->whereInEager(
            $whereIn,
            $this->foreignKey,
            $this->getKeys($models, $this->localKey),
            $this->getRelationQuery()
        );
    }

    /**
     * Match the eagerly loaded results to their single parents.
     *
     * @param  array<int, TDeclaringModel>  $models
     * @param  \Illuminate\Database\Eloquent\Collection<int, TRelatedModel>  $results
     * @param  string  $relation
     * @return array<int, TDeclaringModel>
     */
    public function matchOne(array $models, EloquentCollection $results, $relation)
    {
        return $this->matchOneOrMany($models, $results, $relation, 'one');
    }

    /**
     * Match the eagerly loaded results to their many parents.
     *
     * @param  array<int, TDeclaringModel>  $models
     * @param  \Illuminate\Database\Eloquent\Collection<int, TRelatedModel>  $results
     * @param  string  $relation
     * @return array<int, TDeclaringModel>
     */
    public function matchMany(array $models, EloquentCollection $results, $relation)
    {
        return $this->matchOneOrMany($models, $results, $relation, 'many');
    }

    /**
     * Match the eagerly loaded results to their many parents.
     *
     * @param  array<int, TDeclaringModel>  $models
     * @param  \Illuminate\Database\Eloquent\Collection<int, TRelatedModel>  $results
     * @param  string  $relation
     * @param  string  $type
     * @return array<int, TDeclaringModel>
     */
    protected function matchOneOrMany(array $models, EloquentCollection $results, $relation, $type)
    {
        $dictionary = $this->buildDictionary($results);

        // Once we have the dictionary we can simply spin through the parent models to
        // link them up with their children using the keyed dictionary to make the
        // matching very convenient and easy work. Then we'll just return them.
        foreach ($models as $model) {
            $key = $this->getDictionaryKey($model->getAttribute($this->localKey));

            if ($key !== null && isset($dictionary[$key])) {
                $related = $this->getRelationValue($dictionary, $key, $type);

                $model->setRelation($relation, $related);

                // Apply the inverse relation if we have one...
                $type === 'one'
                    ? $this->applyInverseRelationToModel($related, $model)
                    : $this->applyInverseRelationToCollection($related, $model);
            }
        }

        return $models;
    }

    /**
     * Get the value of a relationship by one or many type.
     *
     * @param  array  $dictionary
     * @param  string  $key
     * @param  string  $type
     * @return mixed
     */
    protected function getRelationValue(array $dictionary, $key, $type)
    {
        $value = $dictionary[$key];

        return $type === 'one' ? reset($value) : $this->related->newCollection($value);
    }

    /**
     * Build model dictionary keyed by the relation's foreign key.
     *
     * @param  \Illuminate\Database\Eloquent\Collection<int, TRelatedModel>  $results
     * @return array<array<array-key, TRelatedModel>>
     */
    protected function buildDictionary(EloquentCollection $results)
    {
        $foreign = $this->getForeignKeyName();

        $dictionary = [];

        $isAssociative = Arr::isAssoc($results->all());

        foreach ($results as $key => $item) {
            $pairKey = $this->getDictionaryKey($item->{$foreign});

            if ($pairKey === null) {
                continue;
            }

            if ($isAssociative) {
                $dictionary[$pairKey][$key] = $item;
            } else {
                $dictionary[$pairKey][] = $item;
            }
        }

        return $dictionary;
    }

    /**
     * Find a model by its primary key or return a new instance of the related model.
     *
     * @param  mixed  $id
     * @param  array  $columns
     * @return ($id is (\Illuminate\Contracts\Support\Arrayable<array-key, mixed>|array<mixed>) ? \Illuminate\Database\Eloquent\Collection<int, TRelatedModel> : TRelatedModel)
     */
    public function findOrNew($id, $columns = ['*'])
    {
        if (is_null($instance = $this->find($id, $columns))) {
            $instance = $this->related->newInstance();

            $this->setForeignAttributesForCreate($instance);
        }

        return $instance;
    }

    /**
     * Get the first related model record matching the attributes or instantiate it.
     *
     * @param  array  $attributes
     * @param  array  $values
     * @return TRelatedModel
     */
    public function firstOrNew(array $attributes = [], array $values = [])
    {
        if (is_null($instance = $this->where($attributes)->first())) {
            $instance = $this->related->newInstance(array_merge($attributes, $values));

            $this->setForeignAttributesForCreate($instance);
        }

        return $instance;
    }

    /**
     * Get the first record matching the attributes. If the record is not found, create it.
     *
     * @param  array  $attributes
     * @param  (\Closure(): array)|array  $values
     * @return TRelatedModel
     */
    public function firstOrCreate(array $attributes = [], Closure|array $values = [])
    {
        if (is_null($instance = (clone $this)->where($attributes)->first())) {
            $instance = $this->createOrFirst($attributes, $values);
        }

        return $instance;
    }

    /**
     * Attempt to create the record. If a unique constraint violation occurs, attempt to find the matching record.
     *
     * @param  array  $attributes
     * @param  (\Closure(): array)|array  $values
     * @return TRelatedModel
     */
    public function createOrFirst(array $attributes = [], Closure|array $values = [])
    {
        try {
            return $this->getQuery()->withSavepointIfNeeded(fn () => $this->create(array_merge($attributes, value($values))));
        } catch (UniqueConstraintViolationException $e) {
            return $this->useWritePdo()->where($attributes)->first() ?? throw $e;
        }
    }

    /**
     * Create or update a related record matching the attributes, and fill it with values.
     *
     * @param  array  $attributes
     * @param  array  $values
     * @return TRelatedModel
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
     * Insert new records or update the existing ones.
     *
     * @param  array  $values
     * @param  array|string  $uniqueBy
     * @param  array|null  $update
     * @return int
     */
    public function upsert(array $values, $uniqueBy, $update = null)
    {
        if (! empty($values) && ! is_array(array_first($values))) {
            $values = [$values];
        }

        foreach ($values as $key => $value) {
            $values[$key][$this->getForeignKeyName()] = $this->getParentKey();
        }

        return $this->getQuery()->upsert($values, $uniqueBy, $update);
    }

    /**
     * Attach a model instance to the parent model.
     *
     * @param  TRelatedModel  $model
     * @return TRelatedModel|false
     */
    public function save(Model $model)
    {
        $this->setForeignAttributesForCreate($model);

        return $model->save() ? $model : false;
    }

    /**
     * Attach a model instance without raising any events to the parent model.
     *
     * @param  TRelatedModel  $model
     * @return TRelatedModel|false
     */
    public function saveQuietly(Model $model)
    {
        return Model::withoutEvents(function () use ($model) {
            return $this->save($model);
        });
    }

    /**
     * Attach a collection of models to the parent instance.
     *
     * @param  iterable<TRelatedModel>  $models
     * @return iterable<TRelatedModel>
     */
    public function saveMany($models)
    {
        foreach ($models as $model) {
            $this->save($model);
        }

        return $models;
    }

    /**
     * Attach a collection of models to the parent instance without raising any events to the parent model.
     *
     * @param  iterable<TRelatedModel>  $models
     * @return iterable<TRelatedModel>
     */
    public function saveManyQuietly($models)
    {
        return Model::withoutEvents(function () use ($models) {
            return $this->saveMany($models);
        });
    }

    /**
     * Create a new instance of the related model.
     *
     * @param  array  $attributes
     * @return TRelatedModel
     */
    public function create(array $attributes = [])
    {
        return tap($this->related->newInstance($attributes), function ($instance) {
            $this->setForeignAttributesForCreate($instance);

            $instance->save();

            $this->applyInverseRelationToModel($instance);
        });
    }

    /**
     * Create a new instance of the related model without raising any events to the parent model.
     *
     * @param  array  $attributes
     * @return TRelatedModel
     */
    public function createQuietly(array $attributes = [])
    {
        return Model::withoutEvents(fn () => $this->create($attributes));
    }

    /**
     * Create a new instance of the related model. Allow mass-assignment.
     *
     * @param  array  $attributes
     * @return TRelatedModel
     */
    public function forceCreate(array $attributes = [])
    {
        $attributes[$this->getForeignKeyName()] = $this->getParentKey();

        return $this->applyInverseRelationToModel($this->related->forceCreate($attributes));
    }

    /**
     * Create a new instance of the related model with mass assignment without raising model events.
     *
     * @param  array  $attributes
     * @return TRelatedModel
     */
    public function forceCreateQuietly(array $attributes = [])
    {
        return Model::withoutEvents(fn () => $this->forceCreate($attributes));
    }

    /**
     * Create a Collection of new instances of the related model.
     *
     * @param  iterable  $records
     * @return \Illuminate\Database\Eloquent\Collection<int, TRelatedModel>
     */
    public function createMany(iterable $records)
    {
        $instances = $this->related->newCollection();

        foreach ($records as $record) {
            $instances->push($this->create($record));
        }

        return $instances;
    }

    /**
     * Create a Collection of new instances of the related model without raising any events to the parent model.
     *
     * @param  iterable  $records
     * @return \Illuminate\Database\Eloquent\Collection<int, TRelatedModel>
     */
    public function createManyQuietly(iterable $records)
    {
        return Model::withoutEvents(fn () => $this->createMany($records));
    }

    /**
     * Create a Collection of new instances of the related model, allowing mass-assignment.
     *
     * @param  iterable  $records
     * @return \Illuminate\Database\Eloquent\Collection<int, TRelatedModel>
     */
    public function forceCreateMany(iterable $records)
    {
        $instances = $this->related->newCollection();

        foreach ($records as $record) {
            $instances->push($this->forceCreate($record));
        }

        return $instances;
    }

    /**
     * Create a Collection of new instances of the related model, allowing mass-assignment and without raising any events to the parent model.
     *
     * @param  iterable  $records
     * @return \Illuminate\Database\Eloquent\Collection<int, TRelatedModel>
     */
    public function forceCreateManyQuietly(iterable $records)
    {
        return Model::withoutEvents(fn () => $this->forceCreateMany($records));
    }

    /**
     * Set the foreign ID for creating a related model.
     *
     * @param  TRelatedModel  $model
     * @return void
     */
    protected function setForeignAttributesForCreate(Model $model)
    {
        $model->setAttribute($this->getForeignKeyName(), $this->getParentKey());

        foreach ($this->getQuery()->pendingAttributes as $key => $value) {
            $attributes ??= $model->getAttributes();

            if (! array_key_exists($key, $attributes)) {
                $model->setAttribute($key, $value);
            }
        }

        $this->applyInverseRelationToModel($model);
    }

    /** @inheritDoc */
    public function getRelationExistenceQuery(Builder $query, Builder $parentQuery, $columns = ['*'])
    {
        if ($query->getQuery()->from == $parentQuery->getQuery()->from) {
            return $this->getRelationExistenceQueryForSelfRelation($query, $parentQuery, $columns);
        }

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
    public function getRelationExistenceQueryForSelfRelation(Builder $query, Builder $parentQuery, $columns = ['*'])
    {
        $query->from($query->getModel()->getTable().' as '.$hash = $this->getRelationCountHash());

        $query->getModel()->setTable($hash);

        return $query->select($columns)->whereColumn(
            $this->getQualifiedParentKeyName(), '=', $hash.'.'.$this->getForeignKeyName()
        );
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
            $this->query->groupLimit($value, $this->getExistenceCompareKey());
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
        return $this->getQualifiedForeignKeyName();
    }

    /**
     * Get the key value of the parent's local key.
     *
     * @return mixed
     */
    public function getParentKey()
    {
        return $this->parent->getAttribute($this->localKey);
    }

    /**
     * Get the fully-qualified parent key name.
     *
     * @return string
     */
    public function getQualifiedParentKeyName()
    {
        return $this->parent->qualifyColumn($this->localKey);
    }

    /**
     * Get the plain foreign key.
     *
     * @return string
     */
    public function getForeignKeyName()
    {
        $segments = explode('.', $this->getQualifiedForeignKeyName());

        return array_last($segments);
    }

    /**
     * Get the foreign key for the relationship.
     *
     * @return string
     */
    public function getQualifiedForeignKeyName()
    {
        return $this->foreignKey;
    }

    /**
     * Get the local key for the relationship.
     *
     * @return string
     */
    public function getLocalKeyName()
    {
        return $this->localKey;
    }
}
