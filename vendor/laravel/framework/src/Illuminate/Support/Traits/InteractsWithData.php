<?php

namespace Illuminate\Support\Traits;

use Carbon\CarbonInterval;
use Carbon\Unit;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Number;
use Illuminate\Support\Str;
use stdClass;

use function Illuminate\Support\enum_value;

trait InteractsWithData
{
    /**
     * Retrieve all data from the instance.
     *
     * @param  mixed  $keys
     * @return array
     */
    abstract public function all($keys = null);

    /**
     * Retrieve data from the instance.
     *
     * @param  string|null  $key
     * @param  mixed  $default
     * @return mixed
     */
    abstract protected function data($key = null, $default = null);

    /**
     * Determine if the data contains a given key.
     *
     * @param  string|array  $key
     * @return bool
     */
    public function exists($key)
    {
        return $this->has($key);
    }

    /**
     * Determine if the data contains a given key.
     *
     * @param  string|array  $key
     * @return bool
     */
    public function has($key)
    {
        $keys = is_array($key) ? $key : func_get_args();

        $data = $this->all();

        foreach ($keys as $value) {
            if (! Arr::has($data, $value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine if the instance contains any of the given keys.
     *
     * @param  string|array  $keys
     * @return bool
     */
    public function hasAny($keys)
    {
        $keys = is_array($keys) ? $keys : func_get_args();

        $data = $this->all();

        return Arr::hasAny($data, $keys);
    }

    /**
     * Apply the callback if the instance contains the given key.
     *
     * @param  string  $key
     * @param  callable  $callback
     * @param  callable|null  $default
     * @return $this|mixed
     */
    public function whenHas($key, callable $callback, ?callable $default = null)
    {
        if ($this->has($key)) {
            return $callback(data_get($this->all(), $key)) ?: $this;
        }

        if ($default) {
            return $default();
        }

        return $this;
    }

    /**
     * Determine if the instance contains a non-empty value for the given key.
     *
     * @param  string|array  $key
     * @return bool
     */
    public function filled($key)
    {
        $keys = is_array($key) ? $key : func_get_args();

        foreach ($keys as $value) {
            if ($this->isEmptyString($value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine if the instance contains an empty value for the given key.
     *
     * @param  string|array  $key
     * @return bool
     */
    public function isNotFilled($key)
    {
        $keys = is_array($key) ? $key : func_get_args();

        foreach ($keys as $value) {
            if (! $this->isEmptyString($value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine if the instance contains a non-empty value for any of the given keys.
     *
     * @param  string|array  $keys
     * @return bool
     */
    public function anyFilled($keys)
    {
        $keys = is_array($keys) ? $keys : func_get_args();

        foreach ($keys as $key) {
            if ($this->filled($key)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Apply the callback if the instance contains a non-empty value for the given key.
     *
     * @param  string  $key
     * @param  callable  $callback
     * @param  callable|null  $default
     * @return $this|mixed
     */
    public function whenFilled($key, callable $callback, ?callable $default = null)
    {
        if ($this->filled($key)) {
            return $callback(data_get($this->all(), $key)) ?: $this;
        }

        if ($default) {
            return $default();
        }

        return $this;
    }

    /**
     * Determine if the instance is missing a given key.
     *
     * @param  string|array  $key
     * @return bool
     */
    public function missing($key)
    {
        $keys = is_array($key) ? $key : func_get_args();

        return ! $this->has($keys);
    }

    /**
     * Apply the callback if the instance is missing the given key.
     *
     * @param  string  $key
     * @param  callable  $callback
     * @param  callable|null  $default
     * @return $this|mixed
     */
    public function whenMissing($key, callable $callback, ?callable $default = null)
    {
        if ($this->missing($key)) {
            return $callback(data_get($this->all(), $key)) ?: $this;
        }

        if ($default) {
            return $default();
        }

        return $this;
    }

    /**
     * Determine if the given key is an empty string for "filled".
     *
     * @param  string  $key
     * @return bool
     */
    protected function isEmptyString($key)
    {
        $value = $this->data($key);

        return ! is_bool($value) && ! is_array($value) && trim((string) $value) === '';
    }

    /**
     * Retrieve data from the instance as a Stringable instance.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return \Illuminate\Support\Stringable
     */
    public function str($key, $default = null)
    {
        return $this->string($key, $default);
    }

    /**
     * Retrieve data from the instance as a Stringable instance.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return \Illuminate\Support\Stringable
     */
    public function string($key, $default = null)
    {
        return Str::of($this->data($key, $default));
    }

    /**
     * Retrieve data as a boolean value.
     *
     * Returns true when value is "1", "true", "on", and "yes". Otherwise, returns false.
     *
     * @param  string|null  $key
     * @param  bool  $default
     * @return bool
     */
    public function boolean($key = null, $default = false)
    {
        return filter_var($this->data($key, $default), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Retrieve data as an integer value.
     *
     * @param  string  $key
     * @param  int  $default
     * @return int
     */
    public function integer($key, $default = 0)
    {
        return (int) $this->data($key, $default);
    }

    /**
     * Retrieve data as a float value.
     *
     * @param  string  $key
     * @param  float  $default
     * @return float
     */
    public function float($key, $default = 0.0)
    {
        return (float) $this->data($key, $default);
    }

    /**
     * Retrieve data clamped between min and max values.
     *
     * @param  string  $key
     * @param  int|float  $min
     * @param  int|float  $max
     * @param  int|float  $default
     * @return float|int
     */
    public function clamp($key, $min, $max, $default = 0)
    {
        return Number::clamp($this->data($key, $default), $min, $max);
    }

    /**
     * Retrieve data from the instance as a Carbon instance.
     *
     * @param  string  $key
     * @param  string|null  $format
     * @param  \UnitEnum|string|null  $tz
     * @return \Illuminate\Support\Carbon|null
     *
     * @throws \Carbon\Exceptions\InvalidFormatException
     */
    public function date($key, $format = null, $tz = null)
    {
        $tz = enum_value($tz);

        if ($this->isNotFilled($key)) {
            return null;
        }

        if (is_null($format)) {
            return Date::parse($this->data($key), $tz);
        }

        return Date::createFromFormat($format, $this->data($key), $tz);
    }

    /**
     * Retrieve data from the instance as a CarbonInterval instance.
     *
     * @param  string  $key
     * @param  \Carbon\Unit|string|null  $unit
     * @return \Carbon\CarbonInterval|null
     */
    public function interval($key, $unit = null)
    {
        if ($this->isNotFilled($key)) {
            return null;
        }

        $value = $this->data($key);

        if (is_null($unit)) {
            return CarbonInterval::make($value);
        }

        $unit = $unit instanceof Unit ? $unit : Unit::fromName($unit);

        return $unit->interval((float) $value);
    }

    /**
     * Retrieve data from the instance as an enum.
     *
     * @template TEnum of \BackedEnum
     * @template TDefault of TEnum|null
     *
     * @param  string  $key
     * @param  class-string<TEnum>  $enumClass
     * @param  TDefault  $default
     * @return TEnum|TDefault
     */
    public function enum($key, $enumClass, $default = null)
    {
        if ($this->isNotFilled($key) || ! $this->isBackedEnum($enumClass)) {
            return value($default);
        }

        return $enumClass::tryFrom($this->data($key)) ?: value($default);
    }

    /**
     * Retrieve data from the instance as an array of enums.
     *
     * @template TEnum of \BackedEnum
     *
     * @param  string  $key
     * @param  class-string<TEnum>  $enumClass
     * @return TEnum[]
     */
    public function enums($key, $enumClass)
    {
        if ($this->isNotFilled($key) || ! $this->isBackedEnum($enumClass)) {
            return [];
        }

        return $this->collect($key)
            ->map(fn ($value) => $enumClass::tryFrom($value))
            ->filter()
            ->all();
    }

    /**
     * Determine if the given enum class is backed.
     *
     * @param  class-string  $enumClass
     * @return bool
     */
    protected function isBackedEnum($enumClass)
    {
        return is_a($enumClass, \BackedEnum::class, true);
    }

    /**
     * Retrieve data from the instance as an array.
     *
     * @param  array|string|null  $key
     * @return array
     */
    public function array($key = null)
    {
        return (array) (is_array($key) ? $this->only($key) : $this->data($key));
    }

    /**
     * Retrieve data from the instance as a collection.
     *
     * @param  array|string|null  $key
     * @return \Illuminate\Support\Collection
     */
    public function collect($key = null)
    {
        return new Collection(is_array($key) ? $this->only($key) : $this->data($key));
    }

    /**
     * Get a subset containing the provided keys with values from the instance data.
     *
     * @param  mixed  $keys
     * @return array
     */
    public function only($keys)
    {
        $results = [];

        $data = $this->all();

        $placeholder = new stdClass;

        foreach (is_array($keys) ? $keys : func_get_args() as $key) {
            $value = data_get($data, $key, $placeholder);

            if ($value !== $placeholder) {
                Arr::set($results, $key, $value);
            }
        }

        return $results;
    }

    /**
     * Get all of the data except for a specified array of items.
     *
     * @param  mixed  $keys
     * @return array
     */
    public function except($keys)
    {
        $keys = is_array($keys) ? $keys : func_get_args();

        $results = $this->all();

        Arr::forget($results, $keys);

        return $results;
    }
}
