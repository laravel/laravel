<?php

namespace Illuminate\Queue;

use Illuminate\Contracts\Database\ModelIdentifier;
use Illuminate\Contracts\Queue\QueueableCollection;
use Illuminate\Contracts\Queue\QueueableEntity;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Relations\Concerns\AsPivot;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Collection;

trait SerializesAndRestoresModelIdentifiers
{
    /**
     * Get the property value prepared for serialization.
     *
     * @param  mixed  $value
     * @param  bool  $withRelations
     * @return mixed
     */
    protected function getSerializedPropertyValue($value, $withRelations = true)
    {
        if ($value instanceof QueueableCollection) {
            return (new ModelIdentifier(
                $value->getQueueableClass(),
                $value->getQueueableIds(),
                $withRelations ? $value->getQueueableRelations() : [],
                $value->getQueueableConnection()
            ))->useCollectionClass(
                ($collectionClass = get_class($value)) !== EloquentCollection::class
                    ? $collectionClass
                    : null
            );
        }

        if ($value instanceof QueueableEntity) {
            return new ModelIdentifier(
                get_class($value),
                $value->getQueueableId(),
                $withRelations ? $value->getQueueableRelations() : [],
                $value->getQueueableConnection()
            );
        }

        return $value;
    }

    /**
     * Get the restored property value after deserialization.
     *
     * @param  mixed  $value
     * @return mixed
     */
    protected function getRestoredPropertyValue($value)
    {
        if (! $value instanceof ModelIdentifier) {
            return $value;
        }

        return is_array($value->id)
            ? $this->restoreCollection($value)
            : $this->restoreModel($value);
    }

    /**
     * Restore a queueable collection instance.
     *
     * @param  \Illuminate\Contracts\Database\ModelIdentifier  $value
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function restoreCollection($value)
    {
        $class = $value->getClass();

        if (! $class || count($value->id) === 0) {
            return ! is_null($value->collectionClass ?? null)
                ? new $value->collectionClass
                : new EloquentCollection;
        }

        $collection = $this->getQueryForModelRestoration(
            (new $class)->setConnection($value->connection), $value->id
        )->useWritePdo()->get();

        if (is_a($class, Pivot::class, true) ||
            in_array(AsPivot::class, class_uses($class))) {
            return $collection;
        }

        $collection = $collection->keyBy->getKey();

        $collectionClass = get_class($collection);

        return new $collectionClass(
            (new Collection($value->id))
                ->map(fn ($id) => $collection[$id] ?? null)
                ->filter()
        );
    }

    /**
     * Restore the model from the model identifier instance.
     *
     * @param  \Illuminate\Contracts\Database\ModelIdentifier  $value
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function restoreModel($value)
    {
        return $this->getQueryForModelRestoration(
            (new ($value->getClass()))->setConnection($value->connection), $value->id
        )->useWritePdo()->firstOrFail()->loadMissing($value->relations ?? []);
    }

    /**
     * Get the query for model restoration.
     *
     * @template TModel of \Illuminate\Database\Eloquent\Model
     *
     * @param  TModel  $model
     * @param  array|int  $ids
     * @return \Illuminate\Database\Eloquent\Builder<TModel>
     */
    protected function getQueryForModelRestoration($model, $ids)
    {
        return $model->newQueryForRestoration($ids);
    }
}
