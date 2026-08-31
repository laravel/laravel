<?php

namespace Illuminate\Database\Eloquent\Relations\Concerns;

use BackedEnum;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Collection as BaseCollection;

trait InteractsWithPivotTable
{
    /**
     * Toggles a model (or models) from the parent.
     *
     * Each existing model is detached, and non existing ones are attached.
     *
     * @param  mixed  $ids
     * @param  bool  $touch
     * @return array
     */
    public function toggle($ids, $touch = true)
    {
        $changes = [
            'attached' => [], 'detached' => [],
        ];

        $records = $this->formatRecordsList($this->parseIds($ids));

        // Next, we will determine which IDs should get removed from the join table by
        // checking which of the given ID/records is in the list of current records
        // and removing all of those rows from this "intermediate" joining table.
        $detach = array_values(array_intersect(
            $this->newPivotQuery()->pluck($this->relatedPivotKey)->all(),
            array_keys($records)
        ));

        if (count($detach) > 0) {
            $this->detach($detach, false);

            $changes['detached'] = $this->castKeys($detach);
        }

        // Finally, for all of the records which were not "detached", we'll attach the
        // records into the intermediate table. Then, we will add those attaches to
        // this change list and get ready to return these results to the callers.
        $attach = array_diff_key($records, array_flip($detach));

        if (count($attach) > 0) {
            $this->attach($attach, [], false);

            $changes['attached'] = array_keys($attach);
        }

        // Once we have finished attaching or detaching the records, we will see if we
        // have done any attaching or detaching, and if we have we will touch these
        // relationships if they are configured to touch on any database updates.
        if ($touch && (count($changes['attached']) ||
                       count($changes['detached']))) {
            $this->touchIfTouching();
        }

        return $changes;
    }

    /**
     * Toggles a model (or models) from the parent within a transaction.
     *
     * @param  mixed  $ids
     * @param  bool  $touch
     * @return array
     *
     * @throws \Throwable
     */
    public function toggleOrFail($ids, $touch = true)
    {
        return $this->parent->getConnection()->transaction(fn () => $this->toggle($ids, $touch));
    }

    /**
     * Sync the intermediate tables with a list of IDs without detaching.
     *
     * @param  \Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Model|array|int|string  $ids
     * @return array{attached: array, detached: array, updated: array}
     */
    public function syncWithoutDetaching($ids)
    {
        return $this->sync($ids, false);
    }

    /**
     * Sync the intermediate tables with a list of IDs or collection of models.
     *
     * @param  \Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Model|array|int|string  $ids
     * @param  bool  $detaching
     * @return array{attached: array, detached: array, updated: array}
     */
    public function sync($ids, $detaching = true)
    {
        $changes = [
            'attached' => [], 'detached' => [], 'updated' => [],
        ];

        $records = $this->formatRecordsList($this->parseIds($ids));

        if (empty($records) && ! $detaching) {
            return $changes;
        }

        // First we need to attach any of the associated models that are not currently
        // in this joining table. We'll spin through the given IDs, checking to see
        // if they exist in the array of current ones, and if not we will insert.
        $current = $this->getCurrentlyAttachedPivots()
            ->pluck($this->relatedPivotKey)->all();

        // Next, we will take the differences of the currents and given IDs and detach
        // all of the entities that exist in the "current" array but are not in the
        // array of the new IDs given to the method which will complete the sync.
        if ($detaching) {
            $detach = array_diff($current, array_keys($records));

            if (count($detach) > 0) {
                $this->detach($detach, false);

                $changes['detached'] = $this->castKeys($detach);
            }
        }

        // Now we are finally ready to attach the new records. Note that we'll disable
        // touching until after the entire operation is complete so we don't fire a
        // ton of touch operations until we are totally done syncing the records.
        $changes = array_merge(
            $changes, $this->attachNew($records, $current, false)
        );

        // Once we have finished attaching or detaching the records, we will see if we
        // have done any attaching or detaching, and if we have we will touch these
        // relationships if they are configured to touch on any database updates.
        if (count($changes['attached']) ||
            count($changes['updated']) ||
            count($changes['detached'])) {
            $this->touchIfTouching();
        }

        return $changes;
    }

    /**
     * Sync the intermediate tables with a list of IDs or collection of models within a transaction.
     *
     * @param  \Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Model|array  $ids
     * @param  bool  $detaching
     * @return array{attached: array, detached: array, updated: array}
     *
     * @throws \Throwable
     */
    public function syncOrFail($ids, $detaching = true)
    {
        return $this->parent->getConnection()->transaction(fn () => $this->sync($ids, $detaching));
    }

    /**
     * Sync the intermediate tables with a list of IDs without detaching within a transaction.
     *
     * @param  \Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Model|array  $ids
     * @return array{attached: array, detached: array, updated: array}
     *
     * @throws \Throwable
     */
    public function syncWithoutDetachingOrFail($ids)
    {
        return $this->syncOrFail($ids, false);
    }

    /**
     * Sync the intermediate tables with a list of IDs or collection of models with the given pivot values.
     *
     * @param  \Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Model|array|int|string  $ids
     * @param  array  $values
     * @param  bool  $detaching
     * @return array{attached: array, detached: array, updated: array}
     */
    public function syncWithPivotValues($ids, array $values, bool $detaching = true)
    {
        return $this->sync((new BaseCollection($this->parseIds($ids)))->mapWithKeys(function ($id) use ($values) {
            return [$id => $values];
        }), $detaching);
    }

    /**
     * Sync the intermediate tables with a list of IDs with the given pivot values within a transaction.
     *
     * @param  \Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Model|array|int|string  $ids
     * @param  array  $values
     * @param  bool  $detaching
     * @return array{attached: array, detached: array, updated: array}
     *
     * @throws \Throwable
     */
    public function syncWithPivotValuesOrFail($ids, array $values, bool $detaching = true)
    {
        return $this->parent->getConnection()->transaction(fn () => $this->syncWithPivotValues($ids, $values, $detaching));
    }

    /**
     * Format the sync / toggle record list so that it is keyed by ID.
     *
     * @param  array  $records
     * @return array
     */
    protected function formatRecordsList(array $records)
    {
        return (new BaseCollection($records))->mapWithKeys(function ($attributes, $id) {
            if (! is_array($attributes)) {
                [$id, $attributes] = [$attributes, []];
            }

            if ($id instanceof BackedEnum) {
                $id = $id->value;
            }

            return [$id => $attributes];
        })->all();
    }

    /**
     * Attach all of the records that aren't in the given current records.
     *
     * @param  array  $records
     * @param  array  $current
     * @param  bool  $touch
     * @return array
     */
    protected function attachNew(array $records, array $current, $touch = true)
    {
        $changes = ['attached' => [], 'updated' => []];

        foreach ($records as $id => $attributes) {
            // If the ID is not in the list of existing pivot IDs, we will insert a new pivot
            // record, otherwise, we will just update this existing record on this joining
            // table, so that the developers will easily update these records pain free.
            if (! in_array($id, $current)) {
                $this->attach($id, $attributes, $touch);

                $changes['attached'][] = $this->castKey($id);
            }

            // Now we'll try to update an existing pivot record with the attributes that were
            // given to the method. If the model is actually updated we will add it to the
            // list of updated pivot records so we return them back out to the consumer.
            elseif (count($attributes) > 0 &&
                $this->updateExistingPivot($id, $attributes, $touch)) {
                $changes['updated'][] = $this->castKey($id);
            }
        }

        return $changes;
    }

    /**
     * Update an existing pivot record on the table.
     *
     * @param  mixed  $id
     * @param  array  $attributes
     * @param  bool  $touch
     * @return int
     */
    public function updateExistingPivot($id, array $attributes, $touch = true)
    {
        if ($this->using) {
            return $this->updateExistingPivotUsingCustomClass($id, $attributes, $touch);
        }

        if ($this->hasPivotColumn($this->updatedAt())) {
            $attributes = $this->addTimestampsToAttachment($attributes, true);
        }

        $updated = $this->newPivotStatementForId($id)->update(
            $this->castAttributes($attributes)
        );

        if ($touch) {
            $this->touchIfTouching();
        }

        return $updated;
    }

    /**
     * Update an existing pivot record on the table within a transaction.
     *
     * @param  mixed  $id
     * @param  array  $attributes
     * @param  bool  $touch
     * @return int
     *
     * @throws \Throwable
     */
    public function updateExistingPivotOrFail($id, array $attributes, $touch = true)
    {
        return $this->parent->getConnection()->transaction(fn () => $this->updateExistingPivot($id, $attributes, $touch));
    }

    /**
     * Update an existing pivot record on the table via a custom class.
     *
     * @param  mixed  $id
     * @param  array  $attributes
     * @param  bool  $touch
     * @return int
     */
    protected function updateExistingPivotUsingCustomClass($id, array $attributes, $touch)
    {
        $pivot = $this->getCurrentlyAttachedPivotsForIds($id)->first();

        $updated = $pivot ? $pivot->fill($attributes)->isDirty() : false;

        if ($updated) {
            $pivot->save();
        }

        if ($touch) {
            $this->touchIfTouching();
        }

        return (int) $updated;
    }

    /**
     * Attach a model to the parent.
     *
     * @param  mixed  $ids
     * @param  array  $attributes
     * @param  bool  $touch
     * @return void
     */
    public function attach($ids, array $attributes = [], $touch = true)
    {
        if ($this->using) {
            $this->attachUsingCustomClass($ids, $attributes);
        } else {
            // Here we will insert the attachment records into the pivot table. Once we have
            // inserted the records, we will touch the relationships if necessary and the
            // function will return. We can parse the IDs before inserting the records.
            $this->newPivotStatement()->insert($this->formatAttachRecords(
                $this->parseIds($ids), $attributes
            ));
        }

        if ($touch) {
            $this->touchIfTouching();
        }
    }

    /**
     * Attach a model to the parent within a transaction.
     *
     * @param  mixed  $ids
     * @param  array  $attributes
     * @param  bool  $touch
     * @return void
     *
     * @throws \Throwable
     */
    public function attachOrFail($ids, array $attributes = [], $touch = true)
    {
        $this->parent->getConnection()->transaction(fn () => $this->attach($ids, $attributes, $touch));
    }

    /**
     * Attach a model to the parent using a custom class.
     *
     * @param  mixed  $ids
     * @param  array  $attributes
     * @return void
     */
    protected function attachUsingCustomClass($ids, array $attributes)
    {
        $records = $this->formatAttachRecords(
            $this->parseIds($ids), $attributes
        );

        foreach ($records as $record) {
            $this->newPivot($record, false)->save();
        }
    }

    /**
     * Create an array of records to insert into the pivot table.
     *
     * @param  array  $ids
     * @param  array  $attributes
     * @return array
     */
    protected function formatAttachRecords($ids, array $attributes)
    {
        $records = [];

        $hasTimestamps = ($this->hasPivotColumn($this->createdAt()) ||
                  $this->hasPivotColumn($this->updatedAt()));

        // To create the attachment records, we will simply spin through the IDs given
        // and create a new record to insert for each ID. Each ID may actually be a
        // key in the array, with extra attributes to be placed in other columns.
        foreach ($ids as $key => $value) {
            $records[] = $this->formatAttachRecord(
                $key, $value, $attributes, $hasTimestamps
            );
        }

        return $records;
    }

    /**
     * Create a full attachment record payload.
     *
     * @param  int  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @param  bool  $hasTimestamps
     * @return array
     */
    protected function formatAttachRecord($key, $value, $attributes, $hasTimestamps)
    {
        [$id, $attributes] = $this->extractAttachIdAndAttributes($key, $value, $attributes);

        return array_merge(
            $this->baseAttachRecord($id, $hasTimestamps), $this->castAttributes($attributes)
        );
    }

    /**
     * Get the attach record ID and extra attributes.
     *
     * @param  mixed  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return array
     */
    protected function extractAttachIdAndAttributes($key, $value, array $attributes)
    {
        return is_array($value)
            ? [$key, array_merge($value, $attributes)]
            : [$value, $attributes];
    }

    /**
     * Create a new pivot attachment record.
     *
     * @param  int  $id
     * @param  bool  $timed
     * @return array
     */
    protected function baseAttachRecord($id, $timed)
    {
        $record[$this->relatedPivotKey] = $id;

        $record[$this->foreignPivotKey] = $this->parent->{$this->parentKey};

        // If the record needs to have creation and update timestamps, we will make
        // them by calling the parent model's "freshTimestamp" method which will
        // provide us with a fresh timestamp in this model's preferred format.
        if ($timed) {
            $record = $this->addTimestampsToAttachment($record);
        }

        foreach ($this->pivotValues as $value) {
            $record[$value['column']] = $value['value'];
        }

        return $record;
    }

    /**
     * Set the creation and update timestamps on an attach record.
     *
     * @param  array  $record
     * @param  bool  $exists
     * @return array
     */
    protected function addTimestampsToAttachment(array $record, $exists = false)
    {
        $fresh = $this->parent->freshTimestamp();

        if ($this->using) {
            $pivotModel = new $this->using;

            $fresh = $pivotModel->fromDateTime($fresh);
        }

        if (! $exists && $this->hasPivotColumn($this->createdAt())) {
            $record[$this->createdAt()] = $fresh;
        }

        if ($this->hasPivotColumn($this->updatedAt())) {
            $record[$this->updatedAt()] = $fresh;
        }

        return $record;
    }

    /**
     * Determine whether the given column is defined as a pivot column.
     *
     * @param  string  $column
     * @return bool
     */
    public function hasPivotColumn($column)
    {
        return in_array($column, $this->pivotColumns);
    }

    /**
     * Detach models from the relationship.
     *
     * @param  mixed  $ids
     * @param  bool  $touch
     * @return int
     */
    public function detach($ids = null, $touch = true)
    {
        if ($this->using) {
            $results = $this->detachUsingCustomClass($ids);
        } else {
            $query = $this->newPivotQuery();

            // If associated IDs were passed to the method we will only delete those
            // associations, otherwise all of the association ties will be broken.
            // We'll return the numbers of affected rows when we do the deletes.
            if (! is_null($ids)) {
                $ids = $this->parseIds($ids);

                if (empty($ids)) {
                    return 0;
                }

                $query->whereIn($this->getQualifiedRelatedPivotKeyName(), (array) $ids);
            }

            // Once we have all of the conditions set on the statement, we are ready
            // to run the delete on the pivot table. Then, if the touch parameter
            // is true, we will go ahead and touch all related models to sync.
            $results = $query->delete();
        }

        if ($touch) {
            $this->touchIfTouching();
        }

        return $results;
    }

    /**
     * Detach models from the relationship within a transaction.
     *
     * @param  mixed  $ids
     * @param  bool  $touch
     * @return int
     *
     * @throws \Throwable
     */
    public function detachOrFail($ids = null, $touch = true)
    {
        return $this->parent->getConnection()->transaction(fn () => $this->detach($ids, $touch));
    }

    /**
     * Detach models from the relationship using a custom class.
     *
     * @param  mixed  $ids
     * @return int
     */
    protected function detachUsingCustomClass($ids)
    {
        $results = 0;

        $records = $this->getCurrentlyAttachedPivotsForIds($ids);

        foreach ($records as $record) {
            $results += $record->delete();
        }

        return $results;
    }

    /**
     * Get the pivot models that are currently attached.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getCurrentlyAttachedPivots()
    {
        return $this->getCurrentlyAttachedPivotsForIds();
    }

    /**
     * Get the pivot models that are currently attached, filtered by related model keys.
     *
     * @param  mixed  $ids
     * @return \Illuminate\Support\Collection
     */
    protected function getCurrentlyAttachedPivotsForIds($ids = null)
    {
        return $this->newPivotQuery()
            ->when(! is_null($ids), fn ($query) => $query->whereIn(
                $this->getQualifiedRelatedPivotKeyName(), $this->parseIds($ids)
            ))
            ->get()
            ->map(function ($record) {
                $class = $this->using ?: Pivot::class;

                $pivot = $class::fromRawAttributes($this->parent, (array) $record, $this->getTable(), true);

                return $pivot
                    ->setPivotKeys($this->foreignPivotKey, $this->relatedPivotKey)
                    ->setRelatedModel($this->related);
            });
    }

    /**
     * Create a new pivot model instance.
     *
     * @param  array  $attributes
     * @param  bool  $exists
     * @return \Illuminate\Database\Eloquent\Relations\Pivot
     */
    public function newPivot(array $attributes = [], $exists = false)
    {
        $attributes = array_merge(array_column($this->pivotValues, 'value', 'column'), $attributes);

        $pivot = $this->related->newPivot(
            $this->parent, $attributes, $this->table, $exists, $this->using
        );

        return $pivot
            ->setPivotKeys($this->foreignPivotKey, $this->relatedPivotKey)
            ->setRelatedModel($this->related);
    }

    /**
     * Create a new existing pivot model instance.
     *
     * @param  array  $attributes
     * @return \Illuminate\Database\Eloquent\Relations\Pivot
     */
    public function newExistingPivot(array $attributes = [])
    {
        return $this->newPivot($attributes, true);
    }

    /**
     * Get a new plain query builder for the pivot table.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function newPivotStatement()
    {
        return $this->query->getQuery()->newQuery()->from($this->table);
    }

    /**
     * Get a new pivot statement for a given "other" ID.
     *
     * @param  mixed  $id
     * @return \Illuminate\Database\Query\Builder
     */
    public function newPivotStatementForId($id)
    {
        return $this->newPivotQuery()->whereIn($this->getQualifiedRelatedPivotKeyName(), $this->parseIds($id));
    }

    /**
     * Create a new query builder for the pivot table.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function newPivotQuery()
    {
        $query = $this->newPivotStatement();

        foreach ($this->pivotWheres as $arguments) {
            $query->where(...$arguments);
        }

        foreach ($this->pivotWhereIns as $arguments) {
            $query->whereIn(...$arguments);
        }

        foreach ($this->pivotWhereNulls as $arguments) {
            $query->whereNull(...$arguments);
        }

        return $query->where($this->getQualifiedForeignPivotKeyName(), $this->parent->{$this->parentKey});
    }

    /**
     * Set the columns on the pivot table to retrieve.
     *
     * @param  mixed  $columns
     * @return $this
     */
    public function withPivot($columns)
    {
        $this->pivotColumns = array_merge(
            $this->pivotColumns, is_array($columns) ? $columns : func_get_args()
        );

        return $this;
    }

    /**
     * Get all of the IDs from the given mixed value.
     *
     * @param  mixed  $value
     * @return array
     */
    protected function parseIds($value)
    {
        if ($value instanceof Model) {
            return [$value->{$this->relatedKey}];
        }

        if ($value instanceof EloquentCollection) {
            return $value->pluck($this->relatedKey)->all();
        }

        if ($value instanceof BaseCollection || is_array($value)) {
            return (new BaseCollection($value))
                ->map(fn ($item) => $item instanceof Model ? $item->{$this->relatedKey} : $item)
                ->all();
        }

        return (array) $value;
    }

    /**
     * Get the ID from the given mixed value.
     *
     * @param  mixed  $value
     * @return mixed
     */
    protected function parseId($value)
    {
        return $value instanceof Model ? $value->{$this->relatedKey} : $value;
    }

    /**
     * Cast the given keys to integers if they are numeric and string otherwise.
     *
     * @param  array  $keys
     * @return array
     */
    protected function castKeys(array $keys)
    {
        return array_map(function ($v) {
            return $this->castKey($v);
        }, $keys);
    }

    /**
     * Cast the given key to convert to primary key type.
     *
     * @param  mixed  $key
     * @return mixed
     */
    protected function castKey($key)
    {
        return $this->getTypeSwapValue(
            $this->related->getKeyType(),
            $key
        );
    }

    /**
     * Cast the given pivot attributes.
     *
     * @param  array  $attributes
     * @return array
     */
    protected function castAttributes($attributes)
    {
        return $this->using
            ? $this->newPivot()->fill($attributes)->getAttributes()
            : $attributes;
    }

    /**
     * Converts a given value to a given type value.
     *
     * @param  string  $type
     * @param  mixed  $value
     * @return mixed
     */
    protected function getTypeSwapValue($type, $value)
    {
        return match (strtolower($type)) {
            'int', 'integer' => (int) $value,
            'real', 'float', 'double' => (float) $value,
            'string' => (string) $value,
            default => $value,
        };
    }
}
