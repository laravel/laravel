<?php

namespace Illuminate\Http\Resources;

use Illuminate\Support\Arr;
use Illuminate\Support\Stringable;

trait ConditionallyLoadsAttributes
{
    /**
     * Filter the given data, removing any optional values.
     *
     * @param  array  $data
     * @return array
     */
    protected function filter($data)
    {
        $index = -1;

        foreach ($data as $key => $value) {
            $index++;

            if (is_array($value)) {
                $data[$key] = $this->filter($value);

                continue;
            }

            if (is_numeric($key) && $value instanceof MergeValue) {
                return $this->mergeData(
                    $data, $index, $this->filter($value->data),
                    array_values($value->data) === $value->data
                );
            }

            if ($value instanceof self && is_null($value->resource)) {
                $data[$key] = null;
            }
        }

        return $this->removeMissingValues($data);
    }

    /**
     * Merge the given data in at the given index.
     *
     * @param  array  $data
     * @param  int  $index
     * @param  array  $merge
     * @param  bool  $numericKeys
     * @return array
     */
    protected function mergeData($data, $index, $merge, $numericKeys)
    {
        if ($numericKeys) {
            return $this->removeMissingValues(array_merge(
                array_merge(array_slice($data, 0, $index, true), $merge),
                $this->filter(array_values(array_slice($data, $index + 1, null, true)))
            ));
        }

        return $this->removeMissingValues(array_slice($data, 0, $index, true) +
                $merge +
                $this->filter(array_slice($data, $index + 1, null, true)));
    }

    /**
     * Remove the missing values from the filtered data.
     *
     * @param  array  $data
     * @return array
     */
    protected function removeMissingValues($data)
    {
        $numericKeys = true;

        foreach ($data as $key => $value) {
            if (($value instanceof PotentiallyMissing && $value->isMissing()) ||
                ($value instanceof self &&
                $value->resource instanceof PotentiallyMissing &&
                $value->isMissing())) {
                unset($data[$key]);
            } else {
                $numericKeys = $numericKeys && is_numeric($key);
            }
        }

        if (property_exists($this, 'preserveKeys') && $this->preserveKeys === true) {
            return $data;
        }

        return $numericKeys ? array_values($data) : $data;
    }

    /**
     * Retrieve a value if the given "condition" is truthy.
     *
     * @param  bool  $condition
     * @param  mixed  $value
     * @param  mixed  $default
     * @return \Illuminate\Http\Resources\MissingValue|mixed
     */
    protected function when($condition, $value, $default = new MissingValue)
    {
        if ($condition) {
            return value($value);
        }

        return func_num_args() === 3 ? value($default) : $default;
    }

    /**
     * Retrieve a value if the given "condition" is falsy.
     *
     * @param  bool  $condition
     * @param  mixed  $value
     * @param  mixed  $default
     * @return \Illuminate\Http\Resources\MissingValue|mixed
     */
    public function unless($condition, $value, $default = new MissingValue)
    {
        $arguments = func_num_args() === 2 ? [$value] : [$value, $default];

        return $this->when(! $condition, ...$arguments);
    }

    /**
     * Merge a value into the array.
     *
     * @param  mixed  $value
     * @return \Illuminate\Http\Resources\MergeValue|mixed
     */
    protected function merge($value)
    {
        return $this->mergeWhen(true, $value);
    }

    /**
     * Merge a value if the given condition is truthy.
     *
     * @param  bool  $condition
     * @param  mixed  $value
     * @param  mixed  $default
     * @return \Illuminate\Http\Resources\MergeValue|mixed
     */
    protected function mergeWhen($condition, $value, $default = new MissingValue)
    {
        if ($condition) {
            return new MergeValue(value($value));
        }

        return func_num_args() === 3 ? new MergeValue(value($default)) : $default;
    }

    /**
     * Merge a value unless the given condition is truthy.
     *
     * @param  bool  $condition
     * @param  mixed  $value
     * @param  mixed  $default
     * @return \Illuminate\Http\Resources\MergeValue|mixed
     */
    protected function mergeUnless($condition, $value, $default = new MissingValue)
    {
        $arguments = func_num_args() === 2 ? [$value] : [$value, $default];

        return $this->mergeWhen(! $condition, ...$arguments);
    }

    /**
     * Merge the given attributes.
     *
     * @param  array  $attributes
     * @return \Illuminate\Http\Resources\MergeValue
     */
    protected function attributes($attributes)
    {
        return new MergeValue(
            Arr::only($this->resource->toArray(), $attributes)
        );
    }

    /**
     * Retrieve an attribute if it exists on the resource.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  mixed  $default
     * @return \Illuminate\Http\Resources\MissingValue|mixed
     */
    public function whenHas($attribute, $value = null, $default = new MissingValue)
    {
        if (! array_key_exists($attribute, $this->resource->getAttributes())) {
            return value($default);
        }

        return func_num_args() === 1
            ? $this->resource->{$attribute}
            : value($value, $this->resource->{$attribute});
    }

    /**
     * Retrieve a model attribute if it is null.
     *
     * @param  mixed  $value
     * @param  mixed  $default
     * @return \Illuminate\Http\Resources\MissingValue|mixed
     */
    protected function whenNull($value, $default = new MissingValue)
    {
        $arguments = func_num_args() == 1 ? [$value] : [$value, $default];

        return $this->when(is_null($value), ...$arguments);
    }

    /**
     * Retrieve a model attribute if it is not null.
     *
     * @param  mixed  $value
     * @param  mixed  $default
     * @return \Illuminate\Http\Resources\MissingValue|mixed
     */
    protected function whenNotNull($value, $default = new MissingValue)
    {
        $arguments = func_num_args() == 1 ? [$value] : [$value, $default];

        return $this->when(! is_null($value), ...$arguments);
    }

    /**
     * Retrieve an accessor when it has been appended.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  mixed  $default
     * @return \Illuminate\Http\Resources\MissingValue|mixed
     */
    protected function whenAppended($attribute, $value = null, $default = new MissingValue)
    {
        if ($this->resource->hasAppended($attribute)) {
            return func_num_args() >= 2 ? value($value) : $this->resource->$attribute;
        }

        return func_num_args() === 3 ? value($default) : $default;
    }

    /**
     * Retrieve a relationship if it has been loaded.
     *
     * @param  string  $relationship
     * @param  mixed  $value
     * @param  mixed  $default
     * @return \Illuminate\Http\Resources\MissingValue|mixed
     */
    protected function whenLoaded($relationship, $value = null, $default = new MissingValue)
    {
        if (! $this->resource->relationLoaded($relationship)) {
            return value($default);
        }

        $loadedValue = $this->resource->{$relationship};

        if (func_num_args() === 1) {
            return $loadedValue;
        }

        if ($loadedValue === null) {
            return;
        }

        if ($value === null) {
            $value = value(...);
        }

        return value($value, $loadedValue);
    }

    /**
     * Retrieve a relationship count if it exists.
     *
     * @param  string  $relationship
     * @param  mixed  $value
     * @param  mixed  $default
     * @return \Illuminate\Http\Resources\MissingValue|mixed
     */
    public function whenCounted($relationship, $value = null, $default = new MissingValue)
    {
        $attribute = (new Stringable($relationship))->snake()->finish('_count')->value();

        if (! array_key_exists($attribute, $this->resource->getAttributes())) {
            return value($default);
        }

        if (func_num_args() === 1) {
            return $this->resource->{$attribute};
        }

        if ($this->resource->{$attribute} === null) {
            return;
        }

        if ($value === null) {
            $value = value(...);
        }

        return value($value, $this->resource->{$attribute});
    }

    /**
     * Retrieve a relationship aggregated value if it exists.
     *
     * @param  string  $relationship
     * @param  string  $column
     * @param  string  $aggregate
     * @param  mixed  $value
     * @param  mixed  $default
     * @return \Illuminate\Http\Resources\MissingValue|mixed
     */
    public function whenAggregated($relationship, $column, $aggregate, $value = null, $default = new MissingValue)
    {
        $attribute = (new Stringable($relationship))->snake()->append('_')->append($aggregate)->append('_')->finish($column)->value();

        if (! array_key_exists($attribute, $this->resource->getAttributes())) {
            return value($default);
        }

        if (func_num_args() === 3) {
            return $this->resource->{$attribute};
        }

        if ($this->resource->{$attribute} === null) {
            return;
        }

        if ($value === null) {
            $value = value(...);
        }

        return value($value, $this->resource->{$attribute});
    }

    /**
     * Retrieve a relationship existence check if it exists.
     *
     * @param  string  $relationship
     * @param  mixed  $value
     * @param  mixed  $default
     * @return \Illuminate\Http\Resources\MissingValue|mixed
     */
    public function whenExistsLoaded($relationship, $value = null, $default = new MissingValue)
    {
        $attribute = (new Stringable($relationship))->snake()->finish('_exists')->value();

        if (! array_key_exists($attribute, $this->resource->getAttributes())) {
            return value($default);
        }

        if (func_num_args() === 1) {
            return $this->resource->{$attribute};
        }

        if ($this->resource->{$attribute} === null) {
            return;
        }

        return value($value, $this->resource->{$attribute});
    }

    /**
     * Execute a callback if the given pivot table has been loaded.
     *
     * @param  string  $table
     * @param  mixed  $value
     * @param  mixed  $default
     * @return \Illuminate\Http\Resources\MissingValue|mixed
     */
    protected function whenPivotLoaded($table, $value, $default = new MissingValue)
    {
        return $this->whenPivotLoadedAs('pivot', ...func_get_args());
    }

    /**
     * Execute a callback if the given pivot table with a custom accessor has been loaded.
     *
     * @param  string  $accessor
     * @param  string  $table
     * @param  mixed  $value
     * @param  mixed  $default
     * @return \Illuminate\Http\Resources\MissingValue|mixed
     */
    protected function whenPivotLoadedAs($accessor, $table, $value, $default = new MissingValue)
    {
        return $this->when(
            $this->hasPivotLoadedAs($accessor, $table),
            $value,
            $default,
        );
    }

    /**
     * Determine if the resource has the specified pivot table loaded.
     *
     * @param  string  $table
     * @return bool
     */
    protected function hasPivotLoaded($table)
    {
        return $this->hasPivotLoadedAs('pivot', $table);
    }

    /**
     * Determine if the resource has the specified pivot table loaded with a custom accessor.
     *
     * @param  string  $accessor
     * @param  string  $table
     * @return bool
     */
    protected function hasPivotLoadedAs($accessor, $table)
    {
        return isset($this->resource->$accessor) &&
            ($this->resource->$accessor instanceof $table ||
                $this->resource->$accessor->getTable() === $table);
    }

    /**
     * Transform the given value if it is present.
     *
     * @param  mixed  $value
     * @param  callable  $callback
     * @param  mixed  $default
     * @return mixed
     */
    protected function transform($value, callable $callback, $default = new MissingValue)
    {
        return transform(
            $value, $callback, $default
        );
    }
}
