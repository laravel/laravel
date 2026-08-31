<?php

namespace Illuminate\Database\Eloquent\Concerns;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Date;

trait HasTimestamps
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The list of models classes that have timestamps temporarily disabled.
     *
     * @var array
     */
    protected static $ignoreTimestampsOn = [];

    /**
     * Update the model's update timestamp.
     *
     * @param  array|string|null  $attribute
     * @return bool
     */
    public function touch($attribute = null)
    {
        if ($attribute) {
            $time = $this->freshTimestamp();

            foreach (Arr::wrap($attribute) as $column) {
                $this->{$column} = $time;
            }

            return $this->save();
        }

        if (! $this->usesTimestamps()) {
            return false;
        }

        $this->updateTimestamps();

        return $this->save();
    }

    /**
     * Update the model's update timestamp without raising any events.
     *
     * @param  array|string|null  $attribute
     * @return bool
     */
    public function touchQuietly($attribute = null)
    {
        return static::withoutEvents(fn () => $this->touch($attribute));
    }

    /**
     * Update the creation and update timestamps.
     *
     * @return $this
     */
    public function updateTimestamps()
    {
        $time = $this->freshTimestamp();

        $updatedAtColumn = $this->getUpdatedAtColumn();

        if (! is_null($updatedAtColumn) && ! $this->isDirty($updatedAtColumn)) {
            $this->setUpdatedAt($time);
        }

        $createdAtColumn = $this->getCreatedAtColumn();

        if (! $this->exists && ! is_null($createdAtColumn) && ! $this->isDirty($createdAtColumn)) {
            $this->setCreatedAt($time);
        }

        return $this;
    }

    /**
     * Set the value of the "created at" attribute.
     *
     * @param  mixed  $value
     * @return $this
     */
    public function setCreatedAt($value)
    {
        $this->{$this->getCreatedAtColumn()} = $value;

        return $this;
    }

    /**
     * Set the value of the "updated at" attribute.
     *
     * @param  mixed  $value
     * @return $this
     */
    public function setUpdatedAt($value)
    {
        $this->{$this->getUpdatedAtColumn()} = $value;

        return $this;
    }

    /**
     * Get a fresh timestamp for the model.
     *
     * @return \Illuminate\Support\Carbon
     */
    public function freshTimestamp()
    {
        return Date::now();
    }

    /**
     * Get a fresh timestamp for the model.
     *
     * @return string
     */
    public function freshTimestampString()
    {
        return $this->fromDateTime($this->freshTimestamp());
    }

    /**
     * Determine if the model uses timestamps.
     *
     * @return bool
     */
    public function usesTimestamps()
    {
        return $this->timestamps && ! static::isIgnoringTimestamps($this::class);
    }

    /**
     * Get the name of the "created at" column.
     *
     * @return string|null
     */
    public function getCreatedAtColumn()
    {
        return static::CREATED_AT;
    }

    /**
     * Get the name of the "updated at" column.
     *
     * @return string|null
     */
    public function getUpdatedAtColumn()
    {
        return static::UPDATED_AT;
    }

    /**
     * Get the fully-qualified "created at" column.
     *
     * @return string|null
     */
    public function getQualifiedCreatedAtColumn()
    {
        $column = $this->getCreatedAtColumn();

        return $column ? $this->qualifyColumn($column) : null;
    }

    /**
     * Get the fully-qualified "updated at" column.
     *
     * @return string|null
     */
    public function getQualifiedUpdatedAtColumn()
    {
        $column = $this->getUpdatedAtColumn();

        return $column ? $this->qualifyColumn($column) : null;
    }

    /**
     * Disable timestamps for the current class during the given callback scope.
     *
     * @param  callable  $callback
     * @return mixed
     */
    public static function withoutTimestamps(callable $callback)
    {
        return static::withoutTimestampsOn([static::class], $callback);
    }

    /**
     * Disable timestamps for the given model classes during the given callback scope.
     *
     * @param  array  $models
     * @param  callable  $callback
     * @return mixed
     */
    public static function withoutTimestampsOn($models, $callback)
    {
        static::$ignoreTimestampsOn = array_values(array_merge(static::$ignoreTimestampsOn, $models));

        try {
            return $callback();
        } finally {
            foreach ($models as $model) {
                if (($key = array_search($model, static::$ignoreTimestampsOn, true)) !== false) {
                    unset(static::$ignoreTimestampsOn[$key]);
                }
            }
        }
    }

    /**
     * Determine if the given model is ignoring timestamps / touches.
     *
     * @param  string|null  $class
     * @return bool
     */
    public static function isIgnoringTimestamps($class = null)
    {
        $class ??= static::class;

        foreach (static::$ignoreTimestampsOn as $ignoredClass) {
            if ($class === $ignoredClass || is_subclass_of($class, $ignoredClass)) {
                return true;
            }
        }

        return false;
    }
}
