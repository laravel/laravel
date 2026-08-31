<?php

namespace Illuminate\Database\Eloquent;

use Illuminate\Contracts\Queue\QueueableCollection;
use Illuminate\Contracts\Queue\QueueableEntity;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Relations\Concerns\InteractsWithDictionary;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection as BaseCollection;
use LogicException;

/**
 * @template TKey of array-key
 * @template TModel of \Illuminate\Database\Eloquent\Model
 *
 * @extends \Illuminate\Support\Collection<TKey, TModel>
 */
class Collection extends BaseCollection implements QueueableCollection
{
    use InteractsWithDictionary;

    /**
     * Find a model in the collection by key.
     *
     * @template TFindDefault
     *
     * @param  mixed  $key
     * @param  TFindDefault  $default
     * @return ($key is (\Illuminate\Contracts\Support\Arrayable<array-key, mixed>|array<mixed>) ? static : TModel|TFindDefault)
     */
    public function find($key, $default = null)
    {
        if ($key instanceof Model) {
            $key = $key->getKey();
        }

        if ($key instanceof Arrayable) {
            $key = $key->toArray();
        }

        if (is_array($key)) {
            if ($this->isEmpty()) {
                return new static;
            }

            return $this->whereIn($this->first()->getKeyName(), $key);
        }

        return Arr::first($this->items, fn ($model) => $model->getKey() == $key, $default);
    }

    /**
     * Find a model in the collection by key or throw an exception.
     *
     * @param  mixed  $key
     * @return TModel
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOrFail($key)
    {
        $result = $this->find($key);

        if (is_array($key) && count($result) === count(array_unique($key))) {
            return $result;
        } elseif (! is_array($key) && ! is_null($result)) {
            return $result;
        }

        $exception = new ModelNotFoundException;

        if (! $model = head($this->items)) {
            throw $exception;
        }

        $ids = is_array($key) ? array_diff($key, $result->modelKeys()) : $key;

        $exception->setModel(get_class($model), $ids);

        throw $exception;
    }

    /**
     * Load a set of relationships onto the collection.
     *
     * @param  array<array-key, array|(callable(\Illuminate\Database\Eloquent\Relations\Relation<*, *, *>): mixed)|string>|string  $relations
     * @return $this
     */
    public function load($relations)
    {
        if ($this->isNotEmpty()) {
            if (is_string($relations)) {
                $relations = func_get_args();
            }

            $query = $this->first()->newQueryWithoutRelationships()->with($relations);

            $this->items = $query->eagerLoadRelations($this->items);
        }

        return $this;
    }

    /**
     * Load a set of aggregations over relationship's column onto the collection.
     *
     * @param  array<array-key, array|(callable(\Illuminate\Database\Eloquent\Relations\Relation<*, *, *>): mixed)|string>|string  $relations
     * @param  string  $column
     * @param  string|null  $function
     * @return $this
     */
    public function loadAggregate($relations, $column, $function = null)
    {
        if ($this->isEmpty()) {
            return $this;
        }

        $models = $this->first()->newModelQuery()
            ->whereKey($this->modelKeys())
            ->select($this->first()->getKeyName())
            ->withAggregate($relations, $column, $function)
            ->get()
            ->keyBy($this->first()->getKeyName());

        $attributes = Arr::except(
            array_keys($models->first()->getAttributes()),
            $models->first()->getKeyName()
        );

        $this->each(function ($model) use ($models, $attributes) {
            $extraAttributes = Arr::only($models->get($model->getKey())->getAttributes(), $attributes);

            $model->forceFill($extraAttributes)
                ->syncOriginalAttributes($attributes)
                ->mergeCasts($models->get($model->getKey())->getCasts());
        });

        return $this;
    }

    /**
     * Load a set of relationship counts onto the collection.
     *
     * @param  array<array-key, array|(callable(\Illuminate\Database\Eloquent\Relations\Relation<*, *, *>): mixed)|string>|string  $relations
     * @return $this
     */
    public function loadCount($relations)
    {
        return $this->loadAggregate($relations, '*', 'count');
    }

    /**
     * Load a set of relationship's max column values onto the collection.
     *
     * @param  array<array-key, array|(callable(\Illuminate\Database\Eloquent\Relations\Relation<*, *, *>): mixed)|string>|string  $relations
     * @param  string  $column
     * @return $this
     */
    public function loadMax($relations, $column)
    {
        return $this->loadAggregate($relations, $column, 'max');
    }

    /**
     * Load a set of relationship's min column values onto the collection.
     *
     * @param  array<array-key, array|(callable(\Illuminate\Database\Eloquent\Relations\Relation<*, *, *>): mixed)|string>|string  $relations
     * @param  string  $column
     * @return $this
     */
    public function loadMin($relations, $column)
    {
        return $this->loadAggregate($relations, $column, 'min');
    }

    /**
     * Load a set of relationship's column summations onto the collection.
     *
     * @param  array<array-key, array|(callable(\Illuminate\Database\Eloquent\Relations\Relation<*, *, *>): mixed)|string>|string  $relations
     * @param  string  $column
     * @return $this
     */
    public function loadSum($relations, $column)
    {
        return $this->loadAggregate($relations, $column, 'sum');
    }

    /**
     * Load a set of relationship's average column values onto the collection.
     *
     * @param  array<array-key, array|(callable(\Illuminate\Database\Eloquent\Relations\Relation<*, *, *>): mixed)|string>|string  $relations
     * @param  string  $column
     * @return $this
     */
    public function loadAvg($relations, $column)
    {
        return $this->loadAggregate($relations, $column, 'avg');
    }

    /**
     * Load a set of related existences onto the collection.
     *
     * @param  array<array-key, array|(callable(\Illuminate\Database\Eloquent\Relations\Relation<*, *, *>): mixed)|string>|string  $relations
     * @return $this
     */
    public function loadExists($relations)
    {
        return $this->loadAggregate($relations, '*', 'exists');
    }

    /**
     * Load a set of relationships onto the collection if they are not already eager loaded.
     *
     * @param  array<array-key, array|(callable(\Illuminate\Database\Eloquent\Relations\Relation<*, *, *>): mixed)|string>|string  $relations
     * @return $this
     */
    public function loadMissing($relations)
    {
        if (is_string($relations)) {
            $relations = func_get_args();
        }

        if ($this->isNotEmpty()) {
            $query = $this->first()->newQueryWithoutRelationships()->with($relations);

            foreach ($query->getEagerLoads() as $key => $value) {
                $segments = explode('.', explode(':', $key)[0]);

                if (str_contains($key, ':')) {
                    $segments[count($segments) - 1] .= ':'.explode(':', $key)[1];
                }

                $path = [];

                foreach ($segments as $segment) {
                    $path[] = [$segment => $segment];
                }

                if (is_callable($value)) {
                    $path[count($segments) - 1][array_last($segments)] = $value;
                }

                $this->loadMissingRelation($this, $path);
            }
        }

        return $this;
    }

    /**
     * Load a relationship path for models of the given type if it is not already eager loaded.
     *
     * @param  array<int, <string, class-string>>  $tuples
     * @return void
     */
    public function loadMissingRelationshipChain(array $tuples)
    {
        [$relation, $class] = array_shift($tuples);

        $this->filter(function ($model) use ($relation, $class) {
            return ! is_null($model) &&
                ! $model->relationLoaded($relation) &&
                $model::class === $class;
        })->load($relation);

        if (empty($tuples)) {
            return;
        }

        $models = $this->pluck($relation)->whereNotNull();

        if ($models->first() instanceof BaseCollection) {
            $models = $models->collapse();
        }

        (new static($models))->loadMissingRelationshipChain($tuples);
    }

    /**
     * Load a relationship path if it is not already eager loaded.
     *
     * @param  \Illuminate\Database\Eloquent\Collection<int, TModel>  $models
     * @param  array  $path
     * @return void
     */
    protected function loadMissingRelation(self $models, array $path)
    {
        $relation = array_shift($path);

        $name = explode(':', key($relation))[0];

        if (is_string(reset($relation))) {
            $relation = reset($relation);
        }

        $models->filter(fn ($model) => ! is_null($model) && ! $model->relationLoaded($name))->load($relation);

        if (empty($path)) {
            return;
        }

        $models = $models->pluck($name)->filter();

        if ($models->first() instanceof BaseCollection) {
            $models = $models->collapse();
        }

        $this->loadMissingRelation(new static($models), $path);
    }

    /**
     * Load a set of relationships onto the mixed relationship collection.
     *
     * @param  string  $relation
     * @param  array<array-key, array|(callable(\Illuminate\Database\Eloquent\Relations\Relation<*, *, *>): mixed)|string>  $relations
     * @return $this
     */
    public function loadMorph($relation, $relations)
    {
        $this->pluck($relation)
            ->filter()
            ->groupBy(fn ($model) => get_class($model))
            ->each(fn ($models, $className) => static::make($models)->load($relations[$className] ?? []));

        return $this;
    }

    /**
     * Load a set of relationship counts onto the mixed relationship collection.
     *
     * @param  string  $relation
     * @param  array<array-key, array|(callable(\Illuminate\Database\Eloquent\Relations\Relation<*, *, *>): mixed)|string>  $relations
     * @return $this
     */
    public function loadMorphCount($relation, $relations)
    {
        $this->pluck($relation)
            ->filter()
            ->groupBy(fn ($model) => get_class($model))
            ->each(fn ($models, $className) => static::make($models)->loadCount($relations[$className] ?? []));

        return $this;
    }

    /**
     * Determine if a key exists in the collection.
     *
     * @param  (callable(TModel, TKey): bool)|TModel|string|int  $key
     * @param  mixed  $operator
     * @param  mixed  $value
     * @return bool
     */
    public function contains($key, $operator = null, $value = null)
    {
        if (func_num_args() > 1 || $this->useAsCallable($key)) {
            return parent::contains(...func_get_args());
        }

        if ($key instanceof Model) {
            return parent::contains(fn ($model) => $model->is($key));
        }

        return parent::contains(fn ($model) => $model->getKey() == $key);
    }

    /**
     * Determine if a key does not exist in the collection.
     *
     * @param  (callable(TModel, TKey): bool)|TModel|string|int  $key
     * @param  mixed  $operator
     * @param  mixed  $value
     * @return bool
     */
    public function doesntContain($key, $operator = null, $value = null)
    {
        return ! $this->contains(...func_get_args());
    }

    /**
     * Get the array of primary keys.
     *
     * @return array<int, array-key>
     */
    public function modelKeys()
    {
        return array_map(fn ($model) => $model->getKey(), $this->items);
    }

    /**
     * Merge the collection with the given items.
     *
     * @param  iterable<array-key, TModel>  $items
     * @return static
     */
    public function merge($items)
    {
        $dictionary = $this->getDictionary();

        foreach ($items as $item) {
            $key = $this->getDictionaryKey($item->getKey());

            if ($key !== null) {
                $dictionary[$key] = $item;
            }
        }

        return new static(array_values($dictionary));
    }

    /**
     * Run a map over each of the items.
     *
     * @template TMapValue
     *
     * @param  callable(TModel, TKey): TMapValue  $callback
     * @return \Illuminate\Support\Collection<TKey, TMapValue>|static<TKey, TMapValue>
     */
    public function map(callable $callback)
    {
        $result = parent::map($callback);

        return $result->contains(fn ($item) => ! $item instanceof Model) ? $result->toBase() : $result;
    }

    /**
     * Run an associative map over each of the items.
     *
     * The callback should return an associative array with a single key / value pair.
     *
     * @template TMapWithKeysKey of array-key
     * @template TMapWithKeysValue
     *
     * @param  callable(TModel, TKey): array<TMapWithKeysKey, TMapWithKeysValue>  $callback
     * @return \Illuminate\Support\Collection<TMapWithKeysKey, TMapWithKeysValue>|static<TMapWithKeysKey, TMapWithKeysValue>
     */
    public function mapWithKeys(callable $callback)
    {
        $result = parent::mapWithKeys($callback);

        return $result->contains(fn ($item) => ! $item instanceof Model) ? $result->toBase() : $result;
    }

    /**
     * Reload a fresh model instance from the database for all the entities.
     *
     * @param  array<array-key, string>|string  $with
     * @return static
     */
    public function fresh($with = [])
    {
        if ($this->isEmpty()) {
            return new static;
        }

        $model = $this->first();

        $freshModels = $model->newQueryWithoutScopes()
            ->with(is_string($with) ? func_get_args() : $with)
            ->whereIn($model->getKeyName(), $this->modelKeys())
            ->get()
            ->getDictionary();

        return $this->filter(fn ($model) => $model->exists && isset($freshModels[$model->getKey()]))
            ->map(fn ($model) => $freshModels[$model->getKey()]);
    }

    /**
     * Diff the collection with the given items.
     *
     * @param  iterable<array-key, TModel>  $items
     * @return static
     */
    public function diff($items)
    {
        $diff = new static;

        $dictionary = $this->getDictionary($items);

        foreach ($this->items as $item) {
            $key = $this->getDictionaryKey($item->getKey());

            if ($key === null || ! isset($dictionary[$key])) {
                $diff->add($item);
            }
        }

        return $diff;
    }

    /**
     * Intersect the collection with the given items.
     *
     * @param  iterable<array-key, TModel>  $items
     * @return static
     */
    public function intersect($items)
    {
        $intersect = new static;

        if (empty($items)) {
            return $intersect;
        }

        $dictionary = $this->getDictionary($items);

        foreach ($this->items as $item) {
            $key = $this->getDictionaryKey($item->getKey());

            if ($key !== null && isset($dictionary[$key])) {
                $intersect->add($item);
            }
        }

        return $intersect;
    }

    /**
     * Return only unique items from the collection.
     *
     * @param  (callable(TModel, TKey): mixed)|string|null  $key
     * @param  bool  $strict
     * @return static
     */
    public function unique($key = null, $strict = false)
    {
        if (! is_null($key)) {
            return parent::unique($key, $strict);
        }

        return new static(array_values($this->getDictionary()));
    }

    /**
     * Returns only the models from the collection with the specified keys.
     *
     * @param  array<array-key, mixed>|null  $keys
     * @return static
     */
    public function only($keys)
    {
        if (is_null($keys)) {
            return new static($this->items);
        }

        $dictionary = Arr::only($this->getDictionary(), array_map($this->getDictionaryKey(...), (array) $keys));

        return new static(array_values($dictionary));
    }

    /**
     * Returns all models in the collection except the models with specified keys.
     *
     * @param  array<array-key, mixed>|null  $keys
     * @return static
     */
    public function except($keys)
    {
        if (is_null($keys)) {
            return new static($this->items);
        }

        $dictionary = Arr::except($this->getDictionary(), array_map($this->getDictionaryKey(...), (array) $keys));

        return new static(array_values($dictionary));
    }

    /**
     * Make the given, typically visible, attributes hidden across the entire collection.
     *
     * @param  array<array-key, string>|string  $attributes
     * @return $this
     */
    public function makeHidden($attributes)
    {
        return $this->each->makeHidden($attributes);
    }

    /**
     * Merge the given, typically visible, attributes hidden across the entire collection.
     *
     * @param  array<array-key, string>|string  $attributes
     * @return $this
     */
    public function mergeHidden($attributes)
    {
        return $this->each->mergeHidden($attributes);
    }

    /**
     * Set the hidden attributes across the entire collection.
     *
     * @param  array<int, string>  $hidden
     * @return $this
     */
    public function setHidden($hidden)
    {
        return $this->each->setHidden($hidden);
    }

    /**
     * Make the given, typically hidden, attributes visible across the entire collection.
     *
     * @param  array<array-key, string>|string  $attributes
     * @return $this
     */
    public function makeVisible($attributes)
    {
        return $this->each->makeVisible($attributes);
    }

    /**
     * Merge the given, typically hidden, attributes visible across the entire collection.
     *
     * @param  array<array-key, string>|string  $attributes
     * @return $this
     */
    public function mergeVisible($attributes)
    {
        return $this->each->mergeVisible($attributes);
    }

    /**
     * Set the visible attributes across the entire collection.
     *
     * @param  array<int, string>  $visible
     * @return $this
     */
    public function setVisible($visible)
    {
        return $this->each->setVisible($visible);
    }

    /**
     * Append an attribute across the entire collection.
     *
     * @param  array<array-key, string>|string  $attributes
     * @return $this
     */
    public function append($attributes)
    {
        return $this->each->append($attributes);
    }

    /**
     * Sets the appends on every element of the collection, overwriting the existing appends for each.
     *
     * @param  array<array-key, mixed>  $appends
     * @return $this
     */
    public function setAppends(array $appends)
    {
        return $this->each->setAppends($appends);
    }

    /**
     * Remove appended properties from every element in the collection.
     *
     * @return $this
     */
    public function withoutAppends()
    {
        return $this->setAppends([]);
    }

    /**
     * Get a dictionary keyed by primary keys.
     *
     * @param  iterable<array-key, TModel>|null  $items
     * @return array<array-key, TModel>
     */
    public function getDictionary($items = null)
    {
        $items = is_null($items) ? $this->items : $items;

        $dictionary = [];

        foreach ($items as $value) {
            $key = $this->getDictionaryKey($value->getKey());

            if ($key !== null) {
                $dictionary[$key] = $value;
            }
        }

        return $dictionary;
    }

    /**
     * The following methods are intercepted to always return base collections.
     */

    /**
     * {@inheritDoc}
     *
     * @return \Illuminate\Support\Collection<array-key, int>
     */
    #[\Override]
    public function countBy($countBy = null)
    {
        return $this->toBase()->countBy($countBy);
    }

    /**
     * {@inheritDoc}
     *
     * @return \Illuminate\Support\Collection<int, mixed>
     */
    #[\Override]
    public function collapse()
    {
        return $this->toBase()->collapse();
    }

    /**
     * {@inheritDoc}
     *
     * @return \Illuminate\Support\Collection<int, mixed>
     */
    #[\Override]
    public function flatten($depth = INF)
    {
        return $this->toBase()->flatten($depth);
    }

    /**
     * {@inheritDoc}
     *
     * @return \Illuminate\Support\Collection<TModel, TKey>
     */
    #[\Override]
    public function flip()
    {
        return $this->toBase()->flip();
    }

    /**
     * {@inheritDoc}
     *
     * @return \Illuminate\Support\Collection<int, TKey>
     */
    #[\Override]
    public function keys()
    {
        return $this->toBase()->keys();
    }

    /**
     * {@inheritDoc}
     *
     * @template TPadValue
     *
     * @return \Illuminate\Support\Collection<int, TModel|TPadValue>
     */
    #[\Override]
    public function pad($size, $value)
    {
        return $this->toBase()->pad($size, $value);
    }

    /**
     * {@inheritDoc}
     *
     * @return \Illuminate\Support\Collection<int<0, 1>, static<TKey, TModel>>
     */
    #[\Override]
    public function partition($key, $operator = null, $value = null)
    {
        return parent::partition(...func_get_args())->toBase();
    }

    /**
     * {@inheritDoc}
     *
     * @return \Illuminate\Support\Collection<array-key, mixed>
     */
    #[\Override]
    public function pluck($value, $key = null)
    {
        return $this->toBase()->pluck($value, $key);
    }

    /**
     * {@inheritDoc}
     *
     * @template TZipValue
     *
     * @return \Illuminate\Support\Collection<int, \Illuminate\Support\Collection<int, TModel|TZipValue>>
     */
    #[\Override]
    public function zip($items)
    {
        return $this->toBase()->zip(...func_get_args());
    }

    /**
     * Get the comparison function to detect duplicates.
     *
     * @return callable(TModel, TModel): bool
     */
    protected function duplicateComparator($strict)
    {
        return fn ($a, $b) => $a->is($b);
    }

    /**
     * Enable relationship autoloading for all models in this collection.
     *
     * @return $this
     */
    public function withRelationshipAutoloading()
    {
        $callback = fn ($tuples) => $this->loadMissingRelationshipChain($tuples);

        foreach ($this as $model) {
            if (! $model->hasRelationAutoloadCallback()) {
                $model->autoloadRelationsUsing($callback, $this);
            }
        }

        return $this;
    }

    /**
     * Get the type of the entities being queued.
     *
     * @return string|null
     *
     * @throws \LogicException
     */
    public function getQueueableClass()
    {
        if ($this->isEmpty()) {
            return;
        }

        $class = $this->getQueueableModelClass($this->first());

        $this->each(function ($model) use ($class) {
            if ($this->getQueueableModelClass($model) !== $class) {
                throw new LogicException('Queueing collections with multiple model types is not supported.');
            }
        });

        return $class;
    }

    /**
     * Get the queueable class name for the given model.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return string
     */
    protected function getQueueableModelClass($model)
    {
        return method_exists($model, 'getQueueableClassName')
            ? $model->getQueueableClassName()
            : get_class($model);
    }

    /**
     * Get the identifiers for all of the entities.
     *
     * @return array<int, mixed>
     */
    public function getQueueableIds()
    {
        if ($this->isEmpty()) {
            return [];
        }

        return $this->first() instanceof QueueableEntity
            ? $this->map->getQueueableId()->all()
            : $this->modelKeys();
    }

    /**
     * Get the relationships of the entities being queued.
     *
     * @return array<int, string>
     */
    public function getQueueableRelations()
    {
        if ($this->isEmpty()) {
            return [];
        }

        $relations = $this->map->getQueueableRelations()->all();

        if (count($relations) === 0 || $relations === [[]]) {
            return [];
        } elseif (count($relations) === 1) {
            return reset($relations);
        } else {
            return array_intersect(...array_values($relations));
        }
    }

    /**
     * Get the connection of the entities being queued.
     *
     * @return string|null
     *
     * @throws \LogicException
     */
    public function getQueueableConnection()
    {
        if ($this->isEmpty()) {
            return;
        }

        $connection = $this->first()->getConnectionName();

        $this->each(function ($model) use ($connection) {
            if ($model->getConnectionName() !== $connection) {
                throw new LogicException('Queueing collections with multiple model connections is not supported.');
            }
        });

        return $connection;
    }

    /**
     * Get the Eloquent query builder from the collection.
     *
     * @return \Illuminate\Database\Eloquent\Builder<TModel>
     *
     * @throws \LogicException
     */
    public function toQuery()
    {
        $model = $this->first();

        if (! $model) {
            throw new LogicException('Unable to create query for empty collection.');
        }

        $class = get_class($model);

        if ($this->reject(fn ($model) => $model instanceof $class)->isNotEmpty()) {
            throw new LogicException('Unable to create query for collection with mixed types.');
        }

        return $model->newModelQuery()->whereKey($this->modelKeys());
    }
}
