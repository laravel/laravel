<?php

namespace Illuminate\Support;

use ArgumentCountError;
use ArrayAccess;
use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Traits\Macroable;
use InvalidArgumentException;
use JsonSerializable;
use Random\Randomizer;
use Traversable;
use WeakMap;

class Arr
{
    use Macroable;

    /**
     * Determine whether the given value is array accessible.
     *
     * @param  mixed  $value
     * @return bool
     */
    public static function accessible($value)
    {
        return is_array($value) || $value instanceof ArrayAccess;
    }

    /**
     * Determine whether the given value is arrayable.
     *
     * @param  mixed  $value
     * @return ($value is array
     *     ? true
     *     : ($value is \Illuminate\Contracts\Support\Arrayable
     *         ? true
     *         : ($value is \Traversable
     *             ? true
     *             : ($value is \Illuminate\Contracts\Support\Jsonable
     *                 ? true
     *                 : ($value is \JsonSerializable ? true : false)
     *             )
     *         )
     *     )
     * )
     */
    public static function arrayable($value)
    {
        return is_array($value)
            || $value instanceof Arrayable
            || $value instanceof Traversable
            || $value instanceof Jsonable
            || $value instanceof JsonSerializable;
    }

    /**
     * Add an element to an array using "dot" notation if it doesn't exist.
     *
     * @param  array  $array
     * @param  string|int|float  $key
     * @param  mixed  $value
     * @return array
     */
    public static function add($array, $key, $value)
    {
        if (is_null(static::get($array, $key))) {
            static::set($array, $key, $value);
        }

        return $array;
    }

    /**
     * Get an array item from an array using "dot" notation.
     *
     * @throws \InvalidArgumentException
     */
    public static function array(ArrayAccess|array $array, string|int|null $key, ?array $default = null): array
    {
        $value = Arr::get($array, $key, $default);

        if (! is_array($value)) {
            throw new InvalidArgumentException(
                sprintf('Array value for key [%s] must be an array, %s found.', $key, gettype($value))
            );
        }

        return $value;
    }

    /**
     * Get a boolean item from an array using "dot" notation.
     *
     * @throws \InvalidArgumentException
     */
    public static function boolean(ArrayAccess|array $array, string|int|null $key, ?bool $default = null): bool
    {
        $value = Arr::get($array, $key, $default);

        if (! is_bool($value)) {
            throw new InvalidArgumentException(
                sprintf('Array value for key [%s] must be a boolean, %s found.', $key, gettype($value))
            );
        }

        return $value;
    }

    /**
     * Collapse an array of arrays into a single array.
     *
     * @param  iterable  $array
     * @return array
     */
    public static function collapse($array)
    {
        $results = [];

        foreach ($array as $values) {
            if ($values instanceof Collection) {
                $results[] = $values->all();
            } elseif (is_array($values)) {
                $results[] = $values;
            }
        }

        return array_merge([], ...$results);
    }

    /**
     * Cross join the given arrays, returning all possible permutations.
     *
     * @template TValue
     *
     * @param  iterable<TValue>  ...$arrays
     * @return array<int, array<array-key, TValue>>
     */
    public static function crossJoin(...$arrays)
    {
        $results = [[]];

        foreach ($arrays as $index => $array) {
            $append = [];

            foreach ($results as $product) {
                foreach ($array as $item) {
                    $product[$index] = $item;

                    $append[] = $product;
                }
            }

            $results = $append;
        }

        return $results;
    }

    /**
     * Divide an array into two arrays. One with keys and the other with values.
     *
     * @template TKey of array-key
     * @template TValue
     *
     * @param  array<TKey, TValue>  $array
     * @return array{TKey[], TValue[]}
     */
    public static function divide($array)
    {
        return [array_keys($array), array_values($array)];
    }

    /**
     * Flatten a multi-dimensional associative array with dots.
     *
     * @param  iterable  $array
     * @param  string  $prepend
     * @param  int  $depth
     * @return array
     */
    public static function dot($array, $prepend = '', $depth = INF)
    {
        $results = [];

        $flatten = function ($data, $prefix, $currentDepth) use (&$results, &$flatten, $depth): void {
            foreach ($data as $key => $value) {
                $newKey = $prefix.$key;

                if (is_array($value) && ! empty($value) && $currentDepth < $depth) {
                    $flatten($value, $newKey.'.', $currentDepth + 1);
                } else {
                    $results[$newKey] = $value;
                }
            }
        };

        $flatten($array, $prepend, 0);

        // Destroy self-referencing closure to avoid memory leak...
        $flatten = null;

        return $results;
    }

    /**
     * Convert a flatten "dot" notation array into an expanded array.
     *
     * @param  iterable  $array
     * @return array
     */
    public static function undot($array)
    {
        $results = [];

        foreach ($array as $key => $value) {
            static::set($results, $key, $value);
        }

        return $results;
    }

    /**
     * Get all of the given array except for a specified array of keys.
     *
     * @param  array  $array
     * @param  array|string|int|float  $keys
     * @return array
     */
    public static function except($array, $keys)
    {
        static::forget($array, $keys);

        return $array;
    }

    /**
     * Get all of the given array except for a specified array of values.
     *
     * @param  array  $array
     * @param  mixed  $values
     * @param  bool  $strict
     * @return array
     */
    public static function exceptValues($array, $values, $strict = false)
    {
        $values = (array) $values;

        return array_filter($array, function ($value) use ($values, $strict) {
            return ! in_array($value, $values, $strict);
        });
    }

    /**
     * Determine if the given key exists in the provided array.
     *
     * @param  \ArrayAccess|array  $array
     * @param  string|int|float  $key
     * @return bool
     */
    public static function exists($array, $key)
    {
        if ($array instanceof Enumerable) {
            return $array->has($key);
        }

        if ($array instanceof ArrayAccess) {
            return $array->offsetExists($key);
        }

        if (is_float($key) || is_null($key)) {
            $key = (string) $key;
        }

        return array_key_exists($key, $array);
    }

    /**
     * Return the first element in an iterable passing a given truth test.
     *
     * @template TKey
     * @template TValue
     * @template TFirstDefault
     *
     * @param  iterable<TKey, TValue>  $array
     * @param  (callable(TValue, TKey): bool)|null  $callback
     * @param  TFirstDefault|(\Closure(): TFirstDefault)  $default
     * @return TValue|TFirstDefault
     */
    public static function first($array, ?callable $callback = null, $default = null)
    {
        if (is_null($callback)) {
            if (empty($array)) {
                return value($default);
            }

            if (is_array($array)) {
                return array_first($array);
            }

            foreach ($array as $item) {
                return $item;
            }

            return value($default);
        }

        $array = static::from($array);

        $key = array_find_key($array, $callback);

        return $key !== null ? $array[$key] : value($default);
    }

    /**
     * Return the last element in an array passing a given truth test.
     *
     * @template TKey
     * @template TValue
     * @template TLastDefault
     *
     * @param  iterable<TKey, TValue>  $array
     * @param  (callable(TValue, TKey): bool)|null  $callback
     * @param  TLastDefault|(\Closure(): TLastDefault)  $default
     * @return TValue|TLastDefault
     */
    public static function last($array, ?callable $callback = null, $default = null)
    {
        if (is_null($callback)) {
            return empty($array) ? value($default) : array_last($array);
        }

        return static::first(array_reverse($array, true), $callback, $default);
    }

    /**
     * Take the first or last {$limit} items from an array.
     *
     * @param  array  $array
     * @param  int  $limit
     * @return array
     */
    public static function take($array, $limit)
    {
        if ($limit < 0) {
            return array_slice($array, $limit, abs($limit));
        }

        return array_slice($array, 0, $limit);
    }

    /**
     * Flatten a multi-dimensional array into a single level.
     *
     * @param  iterable  $array
     * @param  int  $depth
     * @return array
     */
    public static function flatten($array, $depth = INF)
    {
        $result = [];

        foreach ($array as $item) {
            $item = $item instanceof Collection ? $item->all() : $item;

            if (! is_array($item)) {
                $result[] = $item;
            } else {
                $values = $depth === 1
                    ? array_values($item)
                    : static::flatten($item, $depth - 1);

                foreach ($values as $value) {
                    $result[] = $value;
                }
            }
        }

        return $result;
    }

    /**
     * Get a float item from an array using "dot" notation.
     *
     * @throws \InvalidArgumentException
     */
    public static function float(ArrayAccess|array $array, string|int|null $key, ?float $default = null): float
    {
        $value = Arr::get($array, $key, $default);

        if (! is_float($value)) {
            throw new InvalidArgumentException(
                sprintf('Array value for key [%s] must be a float, %s found.', $key, gettype($value))
            );
        }

        return $value;
    }

    /**
     * Remove one or many array items from a given array using "dot" notation.
     *
     * @param  array  $array
     * @param  array|string|int|float  $keys
     * @return void
     */
    public static function forget(&$array, $keys)
    {
        $original = &$array;

        $keys = (array) $keys;

        if (count($keys) === 0) {
            return;
        }

        foreach ($keys as $key) {
            // if the exact key exists in the top-level, remove it
            if (static::exists($array, $key)) {
                unset($array[$key]);

                continue;
            }

            $parts = explode('.', $key);

            // clean up before each pass
            $array = &$original;

            while (count($parts) > 1) {
                $part = array_shift($parts);

                if (isset($array[$part]) && static::accessible($array[$part])) {
                    $array = &$array[$part];
                } else {
                    continue 2;
                }
            }

            unset($array[array_shift($parts)]);
        }
    }

    /**
     * Get the underlying array of items from the given argument.
     *
     * @template TKey of array-key = array-key
     * @template TValue = mixed
     *
     * @param  array<TKey, TValue>|Enumerable<TKey, TValue>|Arrayable<TKey, TValue>|WeakMap<object, TValue>|Traversable<TKey, TValue>|Jsonable|JsonSerializable|object  $items
     * @return ($items is WeakMap ? list<TValue> : array<TKey, TValue>)
     *
     * @throws \InvalidArgumentException
     */
    public static function from($items)
    {
        return match (true) {
            is_array($items) => $items,
            $items instanceof Enumerable => $items->all(),
            $items instanceof Arrayable => $items->toArray(),
            $items instanceof WeakMap => iterator_to_array($items, false),
            $items instanceof Traversable => iterator_to_array($items),
            $items instanceof Jsonable => json_decode($items->toJson(), true),
            $items instanceof JsonSerializable => (array) $items->jsonSerialize(),
            is_object($items) => (array) $items,
            default => throw new InvalidArgumentException('Items cannot be represented by a scalar value.'),
        };
    }

    /**
     * Get an item from an array using "dot" notation.
     *
     * @param  \ArrayAccess|array  $array
     * @param  string|int|null  $key
     * @param  mixed  $default
     * @return mixed
     */
    public static function get($array, $key, $default = null)
    {
        if (! static::accessible($array)) {
            return value($default);
        }

        if (is_null($key)) {
            return $array;
        }

        if (static::exists($array, $key)) {
            return $array[$key];
        }

        if (! str_contains($key, '.')) {
            return value($default);
        }

        foreach (explode('.', $key) as $segment) {
            if (static::accessible($array) && static::exists($array, $segment)) {
                $array = $array[$segment];
            } else {
                return value($default);
            }
        }

        return $array;
    }

    /**
     * Check if an item or items exist in an array using "dot" notation.
     *
     * @param  \ArrayAccess|array  $array
     * @param  string|array  $keys
     * @return bool
     */
    public static function has($array, $keys)
    {
        $keys = (array) $keys;

        if (! $array || $keys === []) {
            return false;
        }

        foreach ($keys as $key) {
            $subKeyArray = $array;

            if (static::exists($array, $key)) {
                continue;
            }

            foreach (explode('.', $key) as $segment) {
                if (static::accessible($subKeyArray) && static::exists($subKeyArray, $segment)) {
                    $subKeyArray = $subKeyArray[$segment];
                } else {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Determine if all keys exist in an array using "dot" notation.
     *
     * @param  \ArrayAccess|array  $array
     * @param  string|array  $keys
     * @return bool
     */
    public static function hasAll($array, $keys)
    {
        $keys = (array) $keys;

        if (! $array || $keys === []) {
            return false;
        }

        foreach ($keys as $key) {
            if (! static::has($array, $key)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine if any of the keys exist in an array using "dot" notation.
     *
     * @param  \ArrayAccess|array  $array
     * @param  string|array  $keys
     * @return bool
     */
    public static function hasAny($array, $keys)
    {
        if (is_null($keys)) {
            return false;
        }

        $keys = (array) $keys;

        if (! $array) {
            return false;
        }

        if ($keys === []) {
            return false;
        }

        foreach ($keys as $key) {
            if (static::has($array, $key)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if all items pass the given truth test.
     *
     * @param  iterable  $array
     * @param  (callable(mixed, array-key): bool)  $callback
     * @return bool
     */
    public static function every($array, callable $callback)
    {
        return array_all($array, $callback);
    }

    /**
     * Determine if some items pass the given truth test.
     *
     * @param  iterable  $array
     * @param  (callable(mixed, array-key): bool)  $callback
     * @return bool
     */
    public static function some($array, callable $callback)
    {
        return array_any($array, $callback);
    }

    /**
     * Get an integer item from an array using "dot" notation.
     *
     * @throws \InvalidArgumentException
     */
    public static function integer(ArrayAccess|array $array, string|int|null $key, ?int $default = null): int
    {
        $value = Arr::get($array, $key, $default);

        if (! is_int($value)) {
            throw new InvalidArgumentException(
                sprintf('Array value for key [%s] must be an integer, %s found.', $key, gettype($value))
            );
        }

        return $value;
    }

    /**
     * Determines if an array is associative.
     *
     * An array is "associative" if it doesn't have sequential numerical keys beginning with zero.
     *
     * @param  array  $array
     * @return ($array is list ? false : true)
     */
    public static function isAssoc(array $array)
    {
        return ! array_is_list($array);
    }

    /**
     * Determines if an array is a list.
     *
     * An array is a "list" if all array keys are sequential integers starting from 0 with no gaps in between.
     *
     * @param  array  $array
     * @return ($array is list ? true : false)
     */
    public static function isList($array)
    {
        return array_is_list($array);
    }

    /**
     * Join all items using a string. The final items can use a separate glue string.
     *
     * @param  array  $array
     * @param  string  $glue
     * @param  string  $finalGlue
     * @return string
     */
    public static function join($array, $glue, $finalGlue = '')
    {
        if ($finalGlue === '') {
            return implode($glue, $array);
        }

        if (count($array) === 0) {
            return '';
        }

        if (count($array) === 1) {
            return array_last($array);
        }

        $finalItem = array_pop($array);

        return implode($glue, $array).$finalGlue.$finalItem;
    }

    /**
     * Key an associative array by a field or using a callback.
     *
     * @param  iterable  $array
     * @param  callable|array|string  $keyBy
     * @return array
     */
    public static function keyBy($array, $keyBy)
    {
        return (new Collection($array))->keyBy($keyBy)->all();
    }

    /**
     * Prepend the key names of an associative array.
     *
     * @param  array  $array
     * @param  string  $prependWith
     * @return array
     */
    public static function prependKeysWith($array, $prependWith)
    {
        return static::mapWithKeys($array, fn ($item, $key) => [$prependWith.$key => $item]);
    }

    /**
     * Get a subset of the items from the given array.
     *
     * @param  array  $array
     * @param  array|string  $keys
     * @return array
     */
    public static function only($array, $keys)
    {
        return array_intersect_key($array, array_flip((array) $keys));
    }

    /**
     * Get a subset of the items from the given array by value.
     *
     * @param  array  $array
     * @param  mixed  $values
     * @param  bool  $strict
     * @return array
     */
    public static function onlyValues($array, $values, $strict = false)
    {
        $values = (array) $values;

        return array_filter($array, function ($value) use ($values, $strict) {
            return in_array($value, $values, $strict);
        });
    }

    /**
     * Select an array of values from an array.
     *
     * @param  array  $array
     * @param  array|string  $keys
     * @return array
     */
    public static function select($array, $keys)
    {
        $keys = static::wrap($keys);

        return static::map($array, function ($item) use ($keys) {
            $result = [];

            foreach ($keys as $key) {
                if (Arr::accessible($item) && Arr::exists($item, $key)) {
                    $result[$key] = $item[$key];
                } elseif (is_object($item) && isset($item->{$key})) {
                    $result[$key] = $item->{$key};
                }
            }

            return $result;
        });
    }

    /**
     * Pluck an array of values from an array.
     *
     * @param  iterable  $array
     * @param  string|array|int|Closure|null  $value
     * @param  string|array|Closure|null  $key
     * @return array
     */
    public static function pluck($array, $value, $key = null)
    {
        $results = [];

        [$value, $key] = static::explodePluckParameters($value, $key);

        foreach ($array as $item) {
            $itemValue = $value instanceof Closure
                ? $value($item)
                : data_get($item, $value);

            // If the key is "null", we will just append the value to the array and keep
            // looping. Otherwise we will key the array using the value of the key we
            // received from the developer. Then we'll return the final array form.
            if (is_null($key)) {
                $results[] = $itemValue;
            } else {
                $itemKey = $key instanceof Closure
                    ? $key($item)
                    : data_get($item, $key);

                if (is_object($itemKey) && method_exists($itemKey, '__toString')) {
                    $itemKey = (string) $itemKey;
                }

                $results[$itemKey] = $itemValue;
            }
        }

        return $results;
    }

    /**
     * Explode the "value" and "key" arguments passed to "pluck".
     *
     * @param  Closure|array|string  $value
     * @param  string|array|Closure|null  $key
     * @return array
     */
    protected static function explodePluckParameters($value, $key)
    {
        $value = is_string($value) ? explode('.', $value) : $value;

        $key = is_null($key) || is_array($key) || $key instanceof Closure ? $key : explode('.', $key);

        return [$value, $key];
    }

    /**
     * Run a map over each of the items in the array.
     *
     * @param  array  $array
     * @param  callable  $callback
     * @return array
     */
    public static function map(array $array, callable $callback)
    {
        $keys = array_keys($array);

        try {
            $items = array_map($callback, $array, $keys);
        } catch (ArgumentCountError) {
            $items = array_map($callback, $array);
        }

        return array_combine($keys, $items);
    }

    /**
     * Run an associative map over each of the items.
     *
     * The callback should return an associative array with a single key/value pair.
     *
     * @template TKey
     * @template TValue
     * @template TMapWithKeysKey of array-key
     * @template TMapWithKeysValue
     *
     * @param  array<TKey, TValue>  $array
     * @param  callable(TValue, TKey): array<TMapWithKeysKey, TMapWithKeysValue>  $callback
     * @return array
     */
    public static function mapWithKeys(array $array, callable $callback)
    {
        $result = [];

        foreach ($array as $key => $value) {
            $assoc = $callback($value, $key);

            foreach ($assoc as $mapKey => $mapValue) {
                $result[$mapKey] = $mapValue;
            }
        }

        return $result;
    }

    /**
     * Run a map over each nested chunk of items.
     *
     * @template TKey
     * @template TValue
     *
     * @param  array<TKey, array>  $array
     * @param  callable(mixed...): TValue  $callback
     * @return array<TKey, TValue>
     */
    public static function mapSpread(array $array, callable $callback)
    {
        return static::map($array, function ($chunk, $key) use ($callback) {
            $chunk[] = $key;

            return $callback(...$chunk);
        });
    }

    /**
     * Push an item onto the beginning of an array.
     *
     * @param  array  $array
     * @param  mixed  $value
     * @param  mixed  $key
     * @return array
     */
    public static function prepend($array, $value, $key = null)
    {
        if (func_num_args() == 2) {
            array_unshift($array, $value);
        } else {
            $array = [$key => $value] + $array;
        }

        return $array;
    }

    /**
     * Get a value from the array, and remove it.
     *
     * @param  array  $array
     * @param  string|int  $key
     * @param  mixed  $default
     * @return mixed
     */
    public static function pull(&$array, $key, $default = null)
    {
        $value = static::get($array, $key, $default);

        static::forget($array, $key);

        return $value;
    }

    /**
     * Convert the array into a query string.
     *
     * @param  array  $array
     * @return string
     */
    public static function query($array)
    {
        return http_build_query($array, '', '&', PHP_QUERY_RFC3986);
    }

    /**
     * Get one or a specified number of random values from an array.
     *
     * @param  array  $array
     * @param  int|null  $number
     * @param  bool  $preserveKeys
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    public static function random($array, $number = null, $preserveKeys = false)
    {
        $requested = is_null($number) ? 1 : $number;

        $count = count($array);

        if ($requested > $count) {
            throw new InvalidArgumentException(
                "You requested {$requested} items, but there are only {$count} items available."
            );
        }

        if (empty($array) || (! is_null($number) && $number <= 0)) {
            return is_null($number) ? null : [];
        }

        $keys = (new Randomizer)->pickArrayKeys($array, $requested);

        if (is_null($number)) {
            return $array[$keys[0]];
        }

        $results = [];

        if ($preserveKeys) {
            foreach ($keys as $key) {
                $results[$key] = $array[$key];
            }
        } else {
            foreach ($keys as $key) {
                $results[] = $array[$key];
            }
        }

        return $results;
    }

    /**
     * Set an array item to a given value using "dot" notation.
     *
     * If no key is given to the method, the entire array will be replaced.
     *
     * @param  array  $array
     * @param  string|int|null  $key
     * @param  mixed  $value
     * @return array
     */
    public static function set(&$array, $key, $value)
    {
        if (is_null($key)) {
            return $array = $value;
        }

        $keys = explode('.', $key);

        foreach ($keys as $i => $key) {
            if (count($keys) === 1) {
                break;
            }

            unset($keys[$i]);

            // If the key doesn't exist at this depth, we will just create an empty array
            // to hold the next value, allowing us to create the arrays to hold final
            // values at the correct depth. Then we'll keep digging into the array.
            if (! isset($array[$key]) || ! is_array($array[$key])) {
                $array[$key] = [];
            }

            $array = &$array[$key];
        }

        $array[array_shift($keys)] = $value;

        return $array;
    }

    /**
     * Push an item into an array using "dot" notation.
     *
     * @param  \ArrayAccess|array  $array
     * @param  string|int|null  $key
     * @param  mixed  $values
     * @return array
     */
    public static function push(ArrayAccess|array &$array, string|int|null $key, mixed ...$values): array
    {
        $target = static::array($array, $key, []);

        array_push($target, ...$values);

        return static::set($array, $key, $target);
    }

    /**
     * Shuffle the given array and return the result.
     *
     * @param  array  $array
     * @return array
     */
    public static function shuffle($array)
    {
        return (new Randomizer)->shuffleArray($array);
    }

    /**
     * Get the first item in the array, but only if exactly one item exists. Otherwise, throw an exception.
     *
     * @param  array  $array
     * @param  (callable(mixed, array-key): array)|null  $callback
     *
     * @throws \Illuminate\Support\ItemNotFoundException
     * @throws \Illuminate\Support\MultipleItemsFoundException
     */
    public static function sole($array, ?callable $callback = null)
    {
        if ($callback) {
            $array = static::where($array, $callback);
        }

        $count = count($array);

        if ($count === 0) {
            throw new ItemNotFoundException;
        }

        if ($count > 1) {
            throw new MultipleItemsFoundException($count);
        }

        return static::first($array);
    }

    /**
     * Sort the array using the given callback or "dot" notation.
     *
     * @template TKey of array-key
     * @template TValue
     *
     * @param  iterable<TKey, TValue>  $array
     * @param  callable|string|null|array<int, (callable(TValue, TValue): -1|0|1)|array{string, 'asc'|'desc'}>  $callback
     * @return array<TKey, TValue>
     */
    public static function sort($array, $callback = null)
    {
        return (new Collection($array))->sortBy($callback)->all();
    }

    /**
     * Sort the array in descending order using the given callback or "dot" notation.
     *
     * @template TKey of array-key
     * @template TValue
     *
     * @param  iterable<TKey, TValue>  $array
     * @param  callable|string|null|array<int, (callable(TValue, TValue): -1|0|1)|array{string, 'asc'|'desc'}>  $callback
     * @return array<TKey, TValue>
     */
    public static function sortDesc($array, $callback = null)
    {
        return (new Collection($array))->sortByDesc($callback)->all();
    }

    /**
     * Recursively sort an array by keys and values.
     *
     * @template TKey of array-key
     * @template TValue
     *
     * @param  array<TKey, TValue>  $array
     * @param  int-mask-of<SORT_REGULAR|SORT_NUMERIC|SORT_STRING|SORT_LOCALE_STRING|SORT_NATURAL|SORT_FLAG_CASE>  $options
     * @param  bool  $descending
     * @return array<TKey, TValue>
     */
    public static function sortRecursive($array, $options = SORT_REGULAR, $descending = false)
    {
        foreach ($array as &$value) {
            if (is_array($value)) {
                $value = static::sortRecursive($value, $options, $descending);
            }
        }

        if (! array_is_list($array)) {
            $descending
                ? krsort($array, $options)
                : ksort($array, $options);
        } else {
            $descending
                ? rsort($array, $options)
                : sort($array, $options);
        }

        return $array;
    }

    /**
     * Recursively sort an array by keys and values in descending order.
     *
     * @template TKey of array-key
     * @template TValue
     *
     * @param  array<TKey, TValue>  $array
     * @param  int-mask-of<SORT_REGULAR|SORT_NUMERIC|SORT_STRING|SORT_LOCALE_STRING|SORT_NATURAL|SORT_FLAG_CASE>  $options
     * @return array<TKey, TValue>
     */
    public static function sortRecursiveDesc($array, $options = SORT_REGULAR)
    {
        return static::sortRecursive($array, $options, true);
    }

    /**
     * Get a string item from an array using "dot" notation.
     *
     * @throws \InvalidArgumentException
     */
    public static function string(ArrayAccess|array $array, string|int|null $key, ?string $default = null): string
    {
        $value = Arr::get($array, $key, $default);

        if (! is_string($value)) {
            throw new InvalidArgumentException(
                sprintf('Array value for key [%s] must be a string, %s found.', $key, gettype($value))
            );
        }

        return $value;
    }

    /**
     * Conditionally compile classes from an array into a CSS class list.
     *
     * @param  array<string, bool>|array<int, string|int>|string  $array
     * @return ($array is array<string, false> ? '' : ($array is '' ? '' : ($array is array{} ? '' : non-empty-string)))
     */
    public static function toCssClasses($array)
    {
        $classList = static::wrap($array);

        $classes = [];

        foreach ($classList as $class => $constraint) {
            if (is_numeric($class)) {
                $classes[] = $constraint;
            } elseif ($constraint) {
                $classes[] = $class;
            }
        }

        return implode(' ', $classes);
    }

    /**
     * Conditionally compile styles from an array into a style list.
     *
     * @param  array<string, bool>|array<int, string|int>|string  $array
     * @return ($array is array<string, false> ? '' : ($array is '' ? '' : ($array is array{} ? '' : non-empty-string)))
     */
    public static function toCssStyles($array)
    {
        $styleList = static::wrap($array);

        $styles = [];

        foreach ($styleList as $class => $constraint) {
            if (is_numeric($class)) {
                $styles[] = Str::finish($constraint, ';');
            } elseif ($constraint) {
                $styles[] = Str::finish($class, ';');
            }
        }

        return implode(' ', $styles);
    }

    /**
     * Filter the array using the given callback.
     *
     * @template TKey of array-key
     * @template TValue
     *
     * @param  array<TKey, TValue>  $array
     * @param  callable(TValue, TKey): bool  $callback
     * @return array<TKey, TValue>
     */
    public static function where($array, callable $callback)
    {
        return array_filter($array, $callback, ARRAY_FILTER_USE_BOTH);
    }

    /**
     * Filter the array using the negation of the given callback.
     *
     * @template TKey of array-key
     * @template TValue
     *
     * @param  array<TKey, TValue>  $array
     * @param  callable(TValue, TKey): bool  $callback
     * @return array<TKey, TValue>
     */
    public static function reject($array, callable $callback)
    {
        return static::where($array, fn ($value, $key) => ! $callback($value, $key));
    }

    /**
     * Partition the array into two arrays using the given callback.
     *
     * @template TKey of array-key
     * @template TValue of mixed
     *
     * @param  iterable<TKey, TValue>  $array
     * @param  callable(TValue, TKey): bool  $callback
     * @return array<int<0, 1>, array<TKey, TValue>>
     */
    public static function partition($array, callable $callback)
    {
        $passed = [];
        $failed = [];

        foreach ($array as $key => $item) {
            if ($callback($item, $key)) {
                $passed[$key] = $item;
            } else {
                $failed[$key] = $item;
            }
        }

        return [$passed, $failed];
    }

    /**
     * Filter items where the value is not null.
     *
     * @param  array  $array
     * @return array
     */
    public static function whereNotNull($array)
    {
        return static::where($array, fn ($value) => ! is_null($value));
    }

    /**
     * If the given value is not an array and not null, wrap it in one.
     *
     * @template TKey of array-key = array-key
     * @template TValue
     *
     * @param  array<TKey, TValue>|TValue|null  $value
     * @return ($value is null ? array{} : ($value is array ? array<TKey, TValue> : array{TValue}))
     */
    public static function wrap($value)
    {
        if (is_null($value)) {
            return [];
        }

        return is_array($value) ? $value : [$value];
    }
}
