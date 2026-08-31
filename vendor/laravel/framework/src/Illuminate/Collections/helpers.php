<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

if (! function_exists('collect')) {
    /**
     * Create a collection from the given value.
     *
     * @template TKey of array-key
     * @template TValue
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<TKey, TValue>|iterable<TKey, TValue>|null  $value
     * @return \Illuminate\Support\Collection<TKey, TValue>
     */
    function collect($value = []): Collection
    {
        return new Collection($value);
    }
}

if (! function_exists('data_fill')) {
    /**
     * Fill in data where it's missing.
     *
     * @param  mixed  $target
     * @param  string|array  $key
     * @param  mixed  $value
     * @return mixed
     */
    function data_fill(&$target, $key, $value)
    {
        return data_set($target, $key, $value, false);
    }
}

if (! function_exists('data_has')) {
    /**
     * Determine if a key / property exists on an array or object using "dot" notation.
     *
     * @param  mixed  $target
     * @param  string|array|int|null  $key
     * @return bool
     */
    function data_has($target, $key): bool
    {
        if (is_null($key) || $key === []) {
            return false;
        }

        $key = is_array($key) ? $key : explode('.', $key);

        foreach ($key as $segment) {
            if (Arr::accessible($target) && Arr::exists($target, $segment)) {
                $target = $target[$segment];
            } elseif (is_object($target) && property_exists($target, $segment)) {
                $target = $target->{$segment};
            } else {
                return false;
            }
        }

        return true;
    }
}

if (! function_exists('data_get')) {
    /**
     * Get an item from an array or object using "dot" notation.
     *
     * @param  mixed  $target
     * @param  string|array|int|null  $key
     * @param  mixed  $default
     * @return mixed
     */
    function data_get($target, $key, $default = null)
    {
        if (is_null($key)) {
            return $target;
        }

        $key = is_array($key) ? $key : explode('.', $key);

        foreach ($key as $i => $segment) {
            unset($key[$i]);

            if (is_null($segment)) {
                return $target;
            }

            if ($segment === '*') {
                if ($target instanceof Collection) {
                    $target = $target->all();
                } elseif (! is_iterable($target)) {
                    return value($default);
                }

                $result = [];

                foreach ($target as $item) {
                    $result[] = data_get($item, $key);
                }

                return in_array('*', $key) ? Arr::collapse($result) : $result;
            }

            $segment = match ($segment) {
                '\*' => '*',
                '\{first}' => '{first}',
                '{first}' => array_key_first(Arr::from($target)),
                '\{last}' => '{last}',
                '{last}' => array_key_last(Arr::from($target)),
                default => $segment,
            };

            if (Arr::accessible($target) && Arr::exists($target, $segment)) {
                $target = $target[$segment];
            } elseif (is_object($target) && isset($target->{$segment})) {
                $target = $target->{$segment};
            } else {
                return value($default);
            }
        }

        return $target;
    }
}

if (! function_exists('data_set')) {
    /**
     * Set an item on an array or object using dot notation.
     *
     * @param  mixed  $target
     * @param  string|array  $key
     * @param  mixed  $value
     * @param  bool  $overwrite
     * @return mixed
     */
    function data_set(&$target, $key, $value, $overwrite = true)
    {
        $segments = is_array($key) ? $key : explode('.', $key);

        if (($segment = array_shift($segments)) === '*') {
            if (! Arr::accessible($target)) {
                $target = [];
            }

            if ($segments) {
                foreach ($target as &$inner) {
                    data_set($inner, $segments, $value, $overwrite);
                }
            } elseif ($overwrite) {
                foreach ($target as &$inner) {
                    $inner = $value;
                }
            }
        } elseif (Arr::accessible($target)) {
            if ($segments) {
                if (! Arr::exists($target, $segment)) {
                    $target[$segment] = [];
                }

                data_set($target[$segment], $segments, $value, $overwrite);
            } elseif ($overwrite || ! Arr::exists($target, $segment)) {
                $target[$segment] = $value;
            }
        } elseif (is_object($target)) {
            if ($segments) {
                if (! isset($target->{$segment})) {
                    $target->{$segment} = [];
                }

                data_set($target->{$segment}, $segments, $value, $overwrite);
            } elseif ($overwrite || ! isset($target->{$segment})) {
                $target->{$segment} = $value;
            }
        } else {
            $target = [];

            if ($segments) {
                data_set($target[$segment], $segments, $value, $overwrite);
            } elseif ($overwrite) {
                $target[$segment] = $value;
            }
        }

        return $target;
    }
}

if (! function_exists('data_forget')) {
    /**
     * Remove / unset an item from an array or object using "dot" notation.
     *
     * @param  mixed  $target
     * @param  string|array|int|null  $key
     * @return mixed
     */
    function data_forget(&$target, $key)
    {
        $segments = is_array($key) ? $key : explode('.', $key);

        if (($segment = array_shift($segments)) === '*' && Arr::accessible($target)) {
            if ($segments) {
                foreach ($target as &$inner) {
                    data_forget($inner, $segments);
                }
            }
        } elseif (Arr::accessible($target)) {
            if ($segments && Arr::exists($target, $segment)) {
                data_forget($target[$segment], $segments);
            } else {
                Arr::forget($target, $segment);
            }
        } elseif (is_object($target)) {
            if ($segments && isset($target->{$segment})) {
                data_forget($target->{$segment}, $segments);
            } elseif (isset($target->{$segment})) {
                unset($target->{$segment});
            }
        }

        return $target;
    }
}

if (! function_exists('head')) {
    /**
     * Get the first element of an array. Useful for method chaining.
     *
     * @param  array  $array
     * @return mixed
     */
    function head($array)
    {
        return empty($array) ? false : array_first($array);
    }
}

if (! function_exists('last')) {
    /**
     * Get the last element from an array.
     *
     * @param  array  $array
     * @return mixed
     */
    function last($array)
    {
        return empty($array) ? false : array_last($array);
    }
}

if (! function_exists('value')) {
    /**
     * Return the default value of the given value.
     *
     * @template TValue
     * @template TArgs
     *
     * @param  TValue|\Closure(TArgs): TValue  $value
     * @param  TArgs  ...$args
     * @return TValue
     */
    function value($value, ...$args)
    {
        return $value instanceof Closure ? $value(...$args) : $value;
    }
}

if (! function_exists('when')) {
    /**
     * Return a value if the given condition is true.
     *
     * @template TValue
     * @template TArgs
     * @template TDefault
     *
     * @param  mixed  $condition
     * @param  TValue|\Closure(TArgs): TValue  $value
     * @param  TDefault|\Closure(): TDefault  $default
     * @return ($condition is true|positive-int|non-falsy-string|non-empty-array ? TValue : ($condition is callable ? TValue|TDefault : TDefault))
     */
    function when($condition, $value, $default = null)
    {
        $condition = $condition instanceof Closure ? $condition() : $condition;

        if ($condition) {
            return value($value, $condition);
        }

        return value($default, $condition);
    }
}
