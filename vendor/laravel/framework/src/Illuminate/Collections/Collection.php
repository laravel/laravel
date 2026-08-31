<?php

namespace Illuminate\Support;

use ArrayAccess;
use ArrayIterator;
use Illuminate\Contracts\Support\CanBeEscapedWhenCastToString;
use Illuminate\Support\Traits\EnumeratesValues;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Support\Traits\TransformsToResourceCollection;
use InvalidArgumentException;
use stdClass;
use Traversable;

/**
 * @template TKey of array-key
 *
 * @template-covariant TValue
 *
 * @implements \ArrayAccess<TKey, TValue>
 * @implements \Illuminate\Support\Enumerable<TKey, TValue>
 */
class Collection implements ArrayAccess, CanBeEscapedWhenCastToString, Enumerable
{
    /**
     * @use \Illuminate\Support\Traits\EnumeratesValues<TKey, TValue>
     */
    use EnumeratesValues, Macroable, TransformsToResourceCollection;

    /**
     * The items contained in the collection.
     *
     * @var array<TKey, TValue>
     */
    protected $items = [];

    /**
     * Create a new collection.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<TKey, TValue>|iterable<TKey, TValue>|null  $items
     */
    public function __construct($items = [])
    {
        $this->items = $this->getArrayableItems($items);
    }

    /**
     * Create a collection with the given range.
     *
     * @param  int  $from
     * @param  int  $to
     * @param  int  $step
     * @return static<int, int>
     */
    public static function range($from, $to, $step = 1)
    {
        return new static(range($from, $to, $step));
    }

    /**
     * Get all of the items in the collection.
     *
     * @return array<TKey, TValue>
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * Get a lazy collection for the items in this collection.
     *
     * @return \Illuminate\Support\LazyCollection<TKey, TValue>
     */
    public function lazy()
    {
        return new LazyCollection($this->items);
    }

    /**
     * Get the median of a given key.
     *
     * @param  string|array<array-key, string>|null  $key
     * @return float|int|null
     */
    public function median($key = null)
    {
        $values = (isset($key) ? $this->pluck($key) : $this)
            ->reject(fn ($item) => is_null($item))
            ->sort()->values();

        $count = $values->count();

        if ($count === 0) {
            return;
        }

        $middle = intdiv($count, 2);

        if ($count % 2) {
            return $values->get($middle);
        }

        return (new static([
            $values->get($middle - 1), $values->get($middle),
        ]))->average();
    }

    /**
     * Get the mode of a given key.
     *
     * @param  string|array<array-key, string>|null  $key
     * @return array<int, float|int>|null
     */
    public function mode($key = null)
    {
        if ($this->count() === 0) {
            return;
        }

        $collection = isset($key) ? $this->pluck($key) : $this;

        $counts = new static;

        $collection->each(fn ($value) => $counts[$value] = isset($counts[$value]) ? $counts[$value] + 1 : 1);

        $sorted = $counts->sort();

        $highestValue = $sorted->last();

        return $sorted->filter(fn ($value) => $value == $highestValue)
            ->sort()->keys()->all();
    }

    /**
     * Collapse the collection of items into a single array.
     *
     * @return static<int, mixed>
     */
    public function collapse()
    {
        return new static(Arr::collapse($this->items));
    }

    /**
     * Collapse the collection of items into a single array while preserving its keys.
     *
     * @return static<mixed, mixed>
     */
    public function collapseWithKeys()
    {
        if (! $this->items) {
            return new static;
        }

        $results = [];

        foreach ($this->items as $key => $values) {
            if ($values instanceof Collection) {
                $values = $values->all();
            } elseif (! is_array($values)) {
                continue;
            }

            $results[$key] = $values;
        }

        if (! $results) {
            return new static;
        }

        return new static(array_replace(...$results));
    }

    /**
     * Determine if an item exists in the collection.
     *
     * @param  (callable(TValue, TKey): bool)|TValue|string  $key
     * @param  mixed  $operator
     * @param  mixed  $value
     * @return bool
     */
    public function contains($key, $operator = null, $value = null)
    {
        if (func_num_args() === 1) {
            if ($this->useAsCallable($key)) {
                return array_any($this->items, $key);
            }

            return in_array($key, $this->items);
        }

        return $this->contains($this->operatorForWhere(...func_get_args()));
    }

    /**
     * Determine if an item exists, using strict comparison.
     *
     * @param  (callable(TValue): bool)|TValue|array-key  $key
     * @param  TValue|null  $value
     * @return bool
     */
    public function containsStrict($key, $value = null)
    {
        if (func_num_args() === 2) {
            return $this->contains(fn ($item) => data_get($item, $key) === $value);
        }

        if ($this->useAsCallable($key)) {
            return ! is_null($this->first($key));
        }

        return in_array($key, $this->items, true);
    }

    /**
     * Determine if an item is not contained in the collection.
     *
     * @param  mixed  $key
     * @param  mixed  $operator
     * @param  mixed  $value
     * @return bool
     */
    public function doesntContain($key, $operator = null, $value = null)
    {
        return ! $this->contains(...func_get_args());
    }

    /**
     * Determine if an item is not contained in the enumerable, using strict comparison.
     *
     * @param  mixed  $key
     * @param  mixed  $operator
     * @param  mixed  $value
     * @return bool
     */
    public function doesntContainStrict($key, $operator = null, $value = null)
    {
        return ! $this->containsStrict(...func_get_args());
    }

    /**
     * Cross join with the given lists, returning all possible permutations.
     *
     * @template TCrossJoinKey
     * @template TCrossJoinValue
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<TCrossJoinKey, TCrossJoinValue>|iterable<TCrossJoinKey, TCrossJoinValue>  ...$lists
     * @return static<int, array<int, TValue|TCrossJoinValue>>
     */
    public function crossJoin(...$lists)
    {
        return new static(Arr::crossJoin(
            $this->items, ...array_map($this->getArrayableItems(...), $lists)
        ));
    }

    /**
     * Get the items in the collection that are not present in the given items.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<array-key, TValue>|iterable<array-key, TValue>  $items
     * @return static
     */
    public function diff($items)
    {
        return new static(array_diff($this->items, $this->getArrayableItems($items)));
    }

    /**
     * Get the items in the collection that are not present in the given items, using the callback.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<array-key, TValue>|iterable<array-key, TValue>  $items
     * @param  callable(TValue, TValue): int  $callback
     * @return static
     */
    public function diffUsing($items, callable $callback)
    {
        return new static(array_udiff($this->items, $this->getArrayableItems($items), $callback));
    }

    /**
     * Get the items in the collection whose keys and values are not present in the given items.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<TKey, TValue>|iterable<TKey, TValue>  $items
     * @return static
     */
    public function diffAssoc($items)
    {
        return new static(array_diff_assoc($this->items, $this->getArrayableItems($items)));
    }

    /**
     * Get the items in the collection whose keys and values are not present in the given items, using the callback.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<TKey, TValue>|iterable<TKey, TValue>  $items
     * @param  callable(TKey, TKey): int  $callback
     * @return static
     */
    public function diffAssocUsing($items, callable $callback)
    {
        return new static(array_diff_uassoc($this->items, $this->getArrayableItems($items), $callback));
    }

    /**
     * Get the items in the collection whose keys are not present in the given items.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<TKey, mixed>|iterable<TKey, mixed>  $items
     * @return static
     */
    public function diffKeys($items)
    {
        return new static(array_diff_key($this->items, $this->getArrayableItems($items)));
    }

    /**
     * Get the items in the collection whose keys are not present in the given items, using the callback.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<TKey, mixed>|iterable<TKey, mixed>  $items
     * @param  callable(TKey, TKey): int  $callback
     * @return static
     */
    public function diffKeysUsing($items, callable $callback)
    {
        return new static(array_diff_ukey($this->items, $this->getArrayableItems($items), $callback));
    }

    /**
     * Retrieve duplicate items from the collection.
     *
     * @template TMapValue
     *
     * @param  (callable(TValue): TMapValue)|string|null  $callback
     * @param  bool  $strict
     * @return static
     */
    public function duplicates($callback = null, $strict = false)
    {
        $items = $this->map($this->valueRetriever($callback));

        $uniqueItems = $items->unique(null, $strict);

        $compare = $this->duplicateComparator($strict);

        $duplicates = new static;

        foreach ($items as $key => $value) {
            if ($uniqueItems->isNotEmpty() && $compare($value, $uniqueItems->first())) {
                $uniqueItems->shift();
            } else {
                $duplicates[$key] = $value;
            }
        }

        return $duplicates;
    }

    /**
     * Retrieve duplicate items from the collection using strict comparison.
     *
     * @template TMapValue
     *
     * @param  (callable(TValue): TMapValue)|string|null  $callback
     * @return static
     */
    public function duplicatesStrict($callback = null)
    {
        return $this->duplicates($callback, true);
    }

    /**
     * Get the comparison function to detect duplicates.
     *
     * @param  bool  $strict
     * @return callable(TValue, TValue): bool
     */
    protected function duplicateComparator($strict)
    {
        if ($strict) {
            return fn ($a, $b) => $a === $b;
        }

        return fn ($a, $b) => $a == $b;
    }

    /**
     * Get all items except for those with the specified keys.
     *
     * @param  \Illuminate\Support\Enumerable<array-key, TKey>|array<array-key, TKey>|string  $keys
     * @return static
     */
    public function except($keys)
    {
        if (is_null($keys)) {
            return new static($this->items);
        }

        if ($keys instanceof Enumerable) {
            $keys = $keys->all();
        } elseif (! is_array($keys)) {
            $keys = func_get_args();
        }

        return new static(Arr::except($this->items, $keys));
    }

    /**
     * Run a filter over each of the items.
     *
     * @param  (callable(TValue, TKey): bool)|null  $callback
     * @return static
     */
    public function filter(?callable $callback = null)
    {
        if ($callback) {
            return new static(Arr::where($this->items, $callback));
        }

        return new static(array_filter($this->items));
    }

    /**
     * Get the first item from the collection passing the given truth test.
     *
     * @template TFirstDefault
     *
     * @param  (callable(TValue, TKey): bool)|null  $callback
     * @param  TFirstDefault|(\Closure(): TFirstDefault)  $default
     * @return TValue|TFirstDefault
     */
    public function first(?callable $callback = null, $default = null)
    {
        return Arr::first($this->items, $callback, $default);
    }

    /**
     * Get a flattened array of the items in the collection.
     *
     * @param  int  $depth
     * @return static<int, mixed>
     */
    public function flatten($depth = INF)
    {
        return new static(Arr::flatten($this->items, $depth));
    }

    /**
     * Flip the items in the collection.
     *
     * @return static<TValue, TKey>
     */
    public function flip()
    {
        return new static(array_flip($this->items));
    }

    /**
     * Remove an item from the collection by key.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<array-key, TValue>|iterable<array-key, TKey>|TKey  $keys
     * @return $this
     */
    public function forget($keys)
    {
        foreach ($this->getArrayableItems($keys) as $key) {
            $this->offsetUnset($key);
        }

        return $this;
    }

    /**
     * Get an item from the collection by key.
     *
     * @template TGetDefault
     *
     * @param  TKey|null  $key
     * @param  TGetDefault|(\Closure(): TGetDefault)  $default
     * @return TValue|TGetDefault
     */
    public function get($key, $default = null)
    {
        $key ??= '';

        if (array_key_exists($key, $this->items)) {
            return $this->items[$key];
        }

        return value($default);
    }

    /**
     * Get an item from the collection by key or add it to collection if it does not exist.
     *
     * @template TGetOrPutValue
     *
     * @param  mixed  $key
     * @param  TGetOrPutValue|(\Closure(): TGetOrPutValue)  $value
     * @return TValue|TGetOrPutValue
     */
    public function getOrPut($key, $value)
    {
        if (array_key_exists($key ?? '', $this->items)) {
            return $this->items[$key ?? ''];
        }

        $this->offsetSet($key, $value = value($value));

        return $value;
    }

    /**
     * Group an associative array by a field or using a callback.
     *
     * @template TGroupKey of array-key|\UnitEnum|\Stringable
     *
     * @param  (callable(TValue, TKey): TGroupKey)|array|string  $groupBy
     * @param  bool  $preserveKeys
     * @return static<
     *  ($groupBy is (array|string)
     *      ? array-key
     *      : (TGroupKey is \UnitEnum ? array-key : (TGroupKey is \Stringable ? string : TGroupKey))),
     *  static<($preserveKeys is true ? TKey : int), ($groupBy is array ? mixed : TValue)>
     * >
     */
    public function groupBy($groupBy, $preserveKeys = false)
    {
        if (! $this->useAsCallable($groupBy) && is_array($groupBy)) {
            $nextGroups = $groupBy;

            $groupBy = array_shift($nextGroups);
        }

        $groupBy = $this->valueRetriever($groupBy);

        $results = [];

        foreach ($this->items as $key => $value) {
            $groupKeys = $groupBy($value, $key);

            if (! is_array($groupKeys)) {
                $groupKeys = [$groupKeys];
            }

            foreach ($groupKeys as $groupKey) {
                $groupKey = match (true) {
                    is_bool($groupKey) => (int) $groupKey,
                    $groupKey instanceof \UnitEnum => enum_value($groupKey),
                    $groupKey instanceof \Stringable, is_null($groupKey) => (string) $groupKey,
                    default => $groupKey,
                };

                if (! array_key_exists($groupKey, $results)) {
                    $results[$groupKey] = new static;
                }

                $results[$groupKey]->offsetSet($preserveKeys ? $key : null, $value);
            }
        }

        $result = new static($results);

        if (! empty($nextGroups)) {
            return $result->map->groupBy($nextGroups, $preserveKeys);
        }

        return $result;
    }

    /**
     * Key an associative array by a field or using a callback.
     *
     * @template TNewKey of array-key|\UnitEnum
     *
     * @param  (callable(TValue, TKey): TNewKey)|array|string  $keyBy
     * @return static<($keyBy is (array|string) ? array-key : (TNewKey is \UnitEnum ? array-key : TNewKey)), TValue>
     */
    public function keyBy($keyBy)
    {
        $keyBy = $this->valueRetriever($keyBy);

        $results = [];

        foreach ($this->items as $key => $item) {
            $resolvedKey = $keyBy($item, $key);

            if ($resolvedKey instanceof \UnitEnum) {
                $resolvedKey = enum_value($resolvedKey);
            }

            if (is_object($resolvedKey)) {
                $resolvedKey = (string) $resolvedKey;
            }

            $results[$resolvedKey] = $item;
        }

        return new static($results);
    }

    /**
     * Determine if an item exists in the collection by key.
     *
     * @param  TKey|array<array-key, TKey>  $key
     * @return bool
     */
    public function has($key)
    {
        $keys = is_array($key) ? $key : func_get_args();

        return array_all($keys, fn ($key) => array_key_exists($key ?? '', $this->items));
    }

    /**
     * Determine if any of the keys exist in the collection.
     *
     * @param  TKey|array<array-key, TKey>  $key
     * @return bool
     */
    public function hasAny($key)
    {
        if ($this->isEmpty()) {
            return false;
        }

        $keys = is_array($key) ? $key : func_get_args();

        return array_any($keys, fn ($key) => array_key_exists($key ?? '', $this->items));
    }

    /**
     * Concatenate values of a given key as a string.
     *
     * @param  (callable(TValue, TKey): mixed)|string|null  $value
     * @param  string|null  $glue
     * @return string
     */
    public function implode($value, $glue = null)
    {
        if ($this->useAsCallable($value)) {
            return implode($glue ?? '', $this->map($value)->all());
        }

        $first = $this->first();

        if (is_array($first) || (is_object($first) && ! $first instanceof Stringable)) {
            return implode($glue ?? '', $this->pluck($value)->all());
        }

        return implode($value ?? '', $this->items);
    }

    /**
     * Intersect the collection with the given items.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<TKey, TValue>|iterable<TKey, TValue>  $items
     * @return static
     */
    public function intersect($items)
    {
        return new static(array_intersect($this->items, $this->getArrayableItems($items)));
    }

    /**
     * Intersect the collection with the given items, using the callback.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<array-key, TValue>|iterable<array-key, TValue>  $items
     * @param  callable(TValue, TValue): int  $callback
     * @return static
     */
    public function intersectUsing($items, callable $callback)
    {
        return new static(array_uintersect($this->items, $this->getArrayableItems($items), $callback));
    }

    /**
     * Intersect the collection with the given items with additional index check.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<TKey, TValue>|iterable<TKey, TValue>  $items
     * @return static
     */
    public function intersectAssoc($items)
    {
        return new static(array_intersect_assoc($this->items, $this->getArrayableItems($items)));
    }

    /**
     * Intersect the collection with the given items with additional index check, using the callback.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<array-key, TValue>|iterable<array-key, TValue>  $items
     * @param  callable(TValue, TValue): int  $callback
     * @return static
     */
    public function intersectAssocUsing($items, callable $callback)
    {
        return new static(array_intersect_uassoc($this->items, $this->getArrayableItems($items), $callback));
    }

    /**
     * Intersect the collection with the given items by key.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<TKey, mixed>|iterable<TKey, mixed>  $items
     * @return static
     */
    public function intersectByKeys($items)
    {
        return new static(array_intersect_key(
            $this->items, $this->getArrayableItems($items)
        ));
    }

    /**
     * Determine if the collection is empty or not.
     *
     * @phpstan-assert-if-true null $this->first()
     * @phpstan-assert-if-true null $this->last()
     *
     * @phpstan-assert-if-false TValue $this->first()
     * @phpstan-assert-if-false TValue $this->last()
     *
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->items);
    }

    /**
     * Determine if the collection contains exactly one item. If a callback is provided, determine if exactly one item matches the condition.
     *
     * @param  (callable(TValue, TKey): bool)|null  $callback
     * @return bool
     *
     * @deprecated 12.49.0 Use the `hasSole()` method instead.
     */
    public function containsOneItem(?callable $callback = null): bool
    {
        return $this->hasSole($callback);
    }

    /**
     * Determine if the collection contains multiple items.
     *
     * @param  (callable(TValue, TKey): bool)|null  $callback
     * @return bool
     *
     * @deprecated 12.50.0 Use the `hasMany()` method instead.
     */
    public function containsManyItems(?callable $callback = null): bool
    {
        return $this->hasMany($callback);
    }

    /**
     * Join all items from the collection using a string. The final items can use a separate glue string.
     *
     * @param  string  $glue
     * @param  string  $finalGlue
     * @return TValue|string
     */
    public function join($glue, $finalGlue = '')
    {
        if ($finalGlue === '') {
            return $this->implode($glue);
        }

        $count = $this->count();

        if ($count === 0) {
            return '';
        }

        if ($count === 1) {
            return $this->last();
        }

        $collection = new static($this->items);

        $finalItem = $collection->pop();

        return $collection->implode($glue).$finalGlue.$finalItem;
    }

    /**
     * Get the keys of the collection items.
     *
     * @return static<int, TKey>
     */
    public function keys()
    {
        return new static(array_keys($this->items));
    }

    /**
     * Get the last item from the collection.
     *
     * @template TLastDefault
     *
     * @param  (callable(TValue, TKey): bool)|null  $callback
     * @param  TLastDefault|(\Closure(): TLastDefault)  $default
     * @return TValue|TLastDefault
     */
    public function last(?callable $callback = null, $default = null)
    {
        return Arr::last($this->items, $callback, $default);
    }

    /**
     * Get the values of a given key.
     *
     * @param  \Closure|string|int|array<array-key, string>|null  $value
     * @param  \Closure|string|null  $key
     * @return static<array-key, mixed>
     */
    public function pluck($value, $key = null)
    {
        return new static(Arr::pluck($this->items, $value, $key));
    }

    /**
     * Run a map over each of the items.
     *
     * @template TMapValue
     *
     * @param  callable(TValue, TKey): TMapValue  $callback
     * @return static<TKey, TMapValue>
     */
    public function map(callable $callback)
    {
        return new static(Arr::map($this->items, $callback));
    }

    /**
     * Run a dictionary map over the items.
     *
     * The callback should return an associative array with a single key/value pair.
     *
     * @template TMapToDictionaryKey of array-key
     * @template TMapToDictionaryValue
     *
     * @param  callable(TValue, TKey): array<TMapToDictionaryKey, TMapToDictionaryValue>  $callback
     * @return static<TMapToDictionaryKey, array<int, TMapToDictionaryValue>>
     */
    public function mapToDictionary(callable $callback)
    {
        $dictionary = [];

        foreach ($this->items as $key => $item) {
            $pair = $callback($item, $key);

            $key = key($pair);

            $value = reset($pair);

            if (! isset($dictionary[$key])) {
                $dictionary[$key] = [];
            }

            $dictionary[$key][] = $value;
        }

        return new static($dictionary);
    }

    /**
     * Run an associative map over each of the items.
     *
     * The callback should return an associative array with a single key/value pair.
     *
     * @template TMapWithKeysKey of array-key
     * @template TMapWithKeysValue
     *
     * @param  callable(TValue, TKey): array<TMapWithKeysKey, TMapWithKeysValue>  $callback
     * @return static<TMapWithKeysKey, TMapWithKeysValue>
     */
    public function mapWithKeys(callable $callback)
    {
        return new static(Arr::mapWithKeys($this->items, $callback));
    }

    /**
     * Merge the collection with the given items.
     *
     * @template TMergeValue
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<TKey, TMergeValue>|iterable<TKey, TMergeValue>  $items
     * @return static<TKey, TValue|TMergeValue>
     */
    public function merge($items)
    {
        return new static(array_merge($this->items, $this->getArrayableItems($items)));
    }

    /**
     * Recursively merge the collection with the given items.
     *
     * @template TMergeRecursiveValue
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<TKey, TMergeRecursiveValue>|iterable<TKey, TMergeRecursiveValue>  $items
     * @return static<TKey, TValue|TMergeRecursiveValue>
     */
    public function mergeRecursive($items)
    {
        return new static(array_merge_recursive($this->items, $this->getArrayableItems($items)));
    }

    /**
     * Multiply the items in the collection by the multiplier.
     *
     * @param  int  $multiplier
     * @return static
     */
    public function multiply(int $multiplier)
    {
        $new = new static;

        for ($i = 0; $i < $multiplier; $i++) {
            $new->push(...$this->items);
        }

        return $new;
    }

    /**
     * Create a collection by using this collection for keys and another for its values.
     *
     * @template TCombineValue
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<array-key, TCombineValue>|iterable<array-key, TCombineValue>  $values
     * @return static<TValue, TCombineValue>
     */
    public function combine($values)
    {
        return new static(array_combine($this->all(), $this->getArrayableItems($values)));
    }

    /**
     * Union the collection with the given items.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<TKey, TValue>|iterable<TKey, TValue>  $items
     * @return static
     */
    public function union($items)
    {
        return new static($this->items + $this->getArrayableItems($items));
    }

    /**
     * Create a new collection consisting of every n-th element.
     *
     * @param  int  $step
     * @param  int  $offset
     * @return ($step is positive-int ? static : never)
     *
     * @throws \InvalidArgumentException
     */
    public function nth($step, $offset = 0)
    {
        if ($step < 1) {
            throw new InvalidArgumentException('Step value must be at least 1.');
        }

        $new = [];

        $position = 0;

        foreach ($this->slice($offset)->items as $item) {
            if ($position % $step === 0) {
                $new[] = $item;
            }

            $position++;
        }

        return new static($new);
    }

    /**
     * Get the items with the specified keys.
     *
     * @param  \Illuminate\Support\Enumerable<array-key, TKey>|array<array-key, TKey>|string|null  $keys
     * @return static
     */
    public function only($keys)
    {
        if (is_null($keys)) {
            return new static($this->items);
        }

        if ($keys instanceof Enumerable) {
            $keys = $keys->all();
        }

        $keys = is_array($keys) ? $keys : func_get_args();

        return new static(Arr::only($this->items, $keys));
    }

    /**
     * Select specific values from the items within the collection.
     *
     * @param  \Illuminate\Support\Enumerable<array-key, TKey>|array<array-key, TKey>|string|null  $keys
     * @return static
     */
    public function select($keys)
    {
        if (is_null($keys)) {
            return new static($this->items);
        }

        if ($keys instanceof Enumerable) {
            $keys = $keys->all();
        }

        $keys = is_array($keys) ? $keys : func_get_args();

        return new static(Arr::select($this->items, $keys));
    }

    /**
     * Get and remove the last N items from the collection.
     *
     * @param  int  $count
     * @return ($count is 1 ? TValue|null : static<int, TValue>)
     */
    public function pop($count = 1)
    {
        if ($count < 1) {
            return new static;
        }

        if ($count === 1) {
            return array_pop($this->items);
        }

        if ($this->isEmpty()) {
            return new static;
        }

        $results = [];

        $collectionCount = $this->count();

        foreach (range(1, min($count, $collectionCount)) as $item) {
            $results[] = array_pop($this->items);
        }

        return new static($results);
    }

    /**
     * Push an item onto the beginning of the collection.
     *
     * @param  TValue  $value
     * @param  TKey  $key
     * @return $this
     */
    public function prepend($value, $key = null)
    {
        $this->items = Arr::prepend($this->items, ...(func_num_args() > 1 ? func_get_args() : [$value]));

        return $this;
    }

    /**
     * Push one or more items onto the end of the collection.
     *
     * @param  TValue  ...$values
     * @return $this
     */
    public function push(...$values)
    {
        foreach ($values as $value) {
            $this->items[] = $value;
        }

        return $this;
    }

    /**
     * Prepend one or more items to the beginning of the collection.
     *
     * @param  TValue  ...$values
     * @return $this
     */
    public function unshift(...$values)
    {
        array_unshift($this->items, ...$values);

        return $this;
    }

    /**
     * Push all of the given items onto the collection.
     *
     * @template TConcatKey of array-key
     * @template TConcatValue
     *
     * @param  iterable<TConcatKey, TConcatValue>  $source
     * @return static<TKey|TConcatKey, TValue|TConcatValue>
     */
    public function concat($source)
    {
        $result = new static($this);

        foreach ($source as $item) {
            $result->push($item);
        }

        return $result;
    }

    /**
     * Get and remove an item from the collection.
     *
     * @template TPullDefault
     *
     * @param  TKey  $key
     * @param  TPullDefault|(\Closure(): TPullDefault)  $default
     * @return TValue|TPullDefault
     */
    public function pull($key, $default = null)
    {
        return Arr::pull($this->items, $key, $default);
    }

    /**
     * Put an item in the collection by key.
     *
     * @param  TKey  $key
     * @param  TValue  $value
     * @return $this
     */
    public function put($key, $value)
    {
        $this->offsetSet($key, $value);

        return $this;
    }

    /**
     * Get one or a specified number of items randomly from the collection.
     *
     * @param  (callable(self<TKey, TValue>): int)|int|null  $number
     * @param  bool  $preserveKeys
     * @return ($number is null ? TValue : static<int, TValue>)
     *
     * @throws \InvalidArgumentException
     */
    public function random($number = null, $preserveKeys = false)
    {
        if (is_null($number)) {
            return Arr::random($this->items);
        }

        if (is_callable($number)) {
            return new static(Arr::random($this->items, $number($this), $preserveKeys));
        }

        return new static(Arr::random($this->items, $number, $preserveKeys));
    }

    /**
     * Replace the collection items with the given items.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<TKey, TValue>|iterable<TKey, TValue>  $items
     * @return static
     */
    public function replace($items)
    {
        return new static(array_replace($this->items, $this->getArrayableItems($items)));
    }

    /**
     * Recursively replace the collection items with the given items.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<TKey, TValue>|iterable<TKey, TValue>  $items
     * @return static
     */
    public function replaceRecursive($items)
    {
        return new static(array_replace_recursive($this->items, $this->getArrayableItems($items)));
    }

    /**
     * Reverse items order.
     *
     * @return static
     */
    public function reverse()
    {
        return new static(array_reverse($this->items, true));
    }

    /**
     * Search the collection for a given value and return the corresponding key if successful.
     *
     * @param  TValue|(callable(TValue,TKey): bool)  $value
     * @param  bool  $strict
     * @return TKey|false
     */
    public function search($value, $strict = false)
    {
        if (! $this->useAsCallable($value)) {
            return array_search($value, $this->items, $strict);
        }

        return array_find_key($this->items, $value) ?? false;
    }

    /**
     * Get the item before the given item.
     *
     * @param  TValue|(callable(TValue,TKey): bool)  $value
     * @param  bool  $strict
     * @return TValue|null
     */
    public function before($value, $strict = false)
    {
        $key = $this->search($value, $strict);

        if ($key === false) {
            return null;
        }

        $position = ($keys = $this->keys())->search($key);

        if ($position === 0) {
            return null;
        }

        return $this->get($keys->get($position - 1));
    }

    /**
     * Get the item after the given item.
     *
     * @param  TValue|(callable(TValue,TKey): bool)  $value
     * @param  bool  $strict
     * @return TValue|null
     */
    public function after($value, $strict = false)
    {
        $key = $this->search($value, $strict);

        if ($key === false) {
            return null;
        }

        $position = ($keys = $this->keys())->search($key);

        if ($position === $keys->count() - 1) {
            return null;
        }

        return $this->get($keys->get($position + 1));
    }

    /**
     * Get and remove the first N items from the collection.
     *
     * @param  int<0, max>  $count
     * @return ($count is 1 ? TValue|null : static<int, TValue>)
     *
     * @throws \InvalidArgumentException
     */
    public function shift($count = 1)
    {
        if ($count < 0) {
            throw new InvalidArgumentException('Number of shifted items may not be less than zero.');
        }

        if ($this->isEmpty()) {
            return null;
        }

        if ($count === 0) {
            return new static;
        }

        if ($count === 1) {
            return array_shift($this->items);
        }

        $results = [];

        $collectionCount = $this->count();

        foreach (range(1, min($count, $collectionCount)) as $item) {
            $results[] = array_shift($this->items);
        }

        return new static($results);
    }

    /**
     * Shuffle the items in the collection.
     *
     * @return static
     */
    public function shuffle()
    {
        return new static(Arr::shuffle($this->items));
    }

    /**
     * Create chunks representing a "sliding window" view of the items in the collection.
     *
     * @param  positive-int  $size
     * @param  positive-int  $step
     * @return static<int, static>
     *
     * @throws \InvalidArgumentException
     */
    public function sliding($size = 2, $step = 1)
    {
        if ($size < 1) {
            throw new InvalidArgumentException('Size value must be at least 1.');
        } elseif ($step < 1) {
            throw new InvalidArgumentException('Step value must be at least 1.');
        }

        $chunks = floor(($this->count() - $size) / $step) + 1;

        return static::times($chunks, fn ($number) => $this->slice(($number - 1) * $step, $size));
    }

    /**
     * Skip the first {$count} items.
     *
     * @param  int  $count
     * @return static
     */
    public function skip($count)
    {
        return $this->slice($count);
    }

    /**
     * Skip items in the collection until the given condition is met.
     *
     * @param  TValue|callable(TValue,TKey): bool  $value
     * @return static
     */
    public function skipUntil($value)
    {
        return new static($this->lazy()->skipUntil($value)->all());
    }

    /**
     * Skip items in the collection while the given condition is met.
     *
     * @param  TValue|callable(TValue,TKey): bool  $value
     * @return static
     */
    public function skipWhile($value)
    {
        return new static($this->lazy()->skipWhile($value)->all());
    }

    /**
     * Slice the underlying collection array.
     *
     * @param  int  $offset
     * @param  int|null  $length
     * @return static
     */
    public function slice($offset, $length = null)
    {
        return new static(array_slice($this->items, $offset, $length, true));
    }

    /**
     * Split a collection into a certain number of groups.
     *
     * @param  int  $numberOfGroups
     * @return ($numberOfGroups is positive-int ? static<int, static> : never)
     *
     * @throws \InvalidArgumentException
     */
    public function split($numberOfGroups)
    {
        if ($numberOfGroups < 1) {
            throw new InvalidArgumentException('Number of groups must be at least 1.');
        }

        if ($this->isEmpty()) {
            return new static;
        }

        $groups = new static;

        $groupSize = floor($this->count() / $numberOfGroups);

        $remain = $this->count() % $numberOfGroups;

        $start = 0;

        for ($i = 0; $i < $numberOfGroups; $i++) {
            $size = $groupSize;

            if ($i < $remain) {
                $size++;
            }

            if ($size) {
                $groups->push(new static(array_slice($this->items, $start, $size)));

                $start += $size;
            }
        }

        return $groups;
    }

    /**
     * Split a collection into a certain number of groups, and fill the first groups completely.
     *
     * @param  int  $numberOfGroups
     * @return ($numberOfGroups is positive-int ? static<int, static> : never)
     *
     * @throws \InvalidArgumentException
     */
    public function splitIn($numberOfGroups)
    {
        if ($numberOfGroups < 1) {
            throw new InvalidArgumentException('Number of groups must be at least 1.');
        }

        return $this->chunk((int) ceil($this->count() / $numberOfGroups));
    }

    /**
     * Get the first item in the collection, but only if exactly one item exists. Otherwise, throw an exception.
     *
     * @param  (callable(TValue, TKey): bool)|string|null  $key
     * @param  mixed  $operator
     * @param  mixed  $value
     * @return TValue
     *
     * @throws \Illuminate\Support\ItemNotFoundException
     * @throws \Illuminate\Support\MultipleItemsFoundException
     */
    public function sole($key = null, $operator = null, $value = null)
    {
        $filter = func_num_args() > 1
            ? $this->operatorForWhere(...func_get_args())
            : $key;

        $items = $this->unless($filter == null)->filter($filter);

        $count = $items->count();

        if ($count === 0) {
            throw new ItemNotFoundException;
        }

        if ($count > 1) {
            throw new MultipleItemsFoundException($count);
        }

        return $items->first();
    }

    /**
     * Determine if the collection contains a single item, optionally matching the given criteria.
     *
     * @param  (callable(TValue, TKey): bool)|string|null  $key
     * @param  mixed  $operator
     * @param  mixed  $value
     * @return bool
     */
    public function hasSole($key = null, $operator = null, $value = null): bool
    {
        $filter = func_num_args() > 1
            ? $this->operatorForWhere(...func_get_args())
            : $key;

        return $this
            ->unless($filter == null)
            ->filter($filter)
            ->count() === 1;
    }

    /**
     * Get the first item in the collection but throw an exception if no matching items exist.
     *
     * @param  (callable(TValue, TKey): bool)|string  $key
     * @param  mixed  $operator
     * @param  mixed  $value
     * @return TValue
     *
     * @throws \Illuminate\Support\ItemNotFoundException
     */
    public function firstOrFail($key = null, $operator = null, $value = null)
    {
        $filter = func_num_args() > 1
            ? $this->operatorForWhere(...func_get_args())
            : $key;

        $placeholder = new stdClass();

        $item = $this->first($filter, $placeholder);

        if ($item === $placeholder) {
            throw new ItemNotFoundException;
        }

        return $item;
    }

    /**
     * Chunk the collection into chunks of the given size.
     *
     * @param  int  $size
     * @param  bool  $preserveKeys
     * @return ($preserveKeys is true ? static<int, static> : static<int, static<int, TValue>>)
     */
    public function chunk($size, $preserveKeys = true)
    {
        if ($size <= 0) {
            return new static;
        }

        $chunks = [];

        foreach (array_chunk($this->items, $size, $preserveKeys) as $chunk) {
            $chunks[] = new static($chunk);
        }

        return new static($chunks);
    }

    /**
     * Chunk the collection into chunks with a callback.
     *
     * @param  callable(TValue, TKey, static<TKey, TValue>): bool  $callback
     * @return static<int, static<TKey, TValue>>
     */
    public function chunkWhile(callable $callback)
    {
        return new static(
            $this->lazy()->chunkWhile($callback)->mapInto(static::class)
        );
    }

    /**
     * Sort through each item with a callback.
     *
     * @param  (callable(TValue, TValue): int)|null|int  $callback
     * @return static
     */
    public function sort($callback = null)
    {
        $items = $this->items;

        $callback && is_callable($callback)
            ? uasort($items, $callback)
            : asort($items, $callback ?? SORT_REGULAR);

        return new static($items);
    }

    /**
     * Sort items in descending order.
     *
     * @param  int  $options
     * @return static
     */
    public function sortDesc($options = SORT_REGULAR)
    {
        $items = $this->items;

        arsort($items, $options);

        return new static($items);
    }

    /**
     * Sort the collection using the given callback.
     *
     * @param  array<array-key, (callable(TValue, TValue): mixed)|(callable(TValue, TKey): mixed)|string|array{string, string}>|(callable(TValue, TKey): mixed)|string  $callback
     * @param  int  $options
     * @param  bool  $descending
     * @return static
     */
    public function sortBy($callback, $options = SORT_REGULAR, $descending = false)
    {
        if (is_array($callback) && ! is_callable($callback)) {
            return $this->sortByMany($callback, $options);
        }

        $results = [];

        $callback = $this->valueRetriever($callback);

        // First we will loop through the items and get the comparator from a callback
        // function which we were given. Then, we will sort the returned values and
        // grab all the corresponding values for the sorted keys from this array.
        foreach ($this->items as $key => $value) {
            $results[$key] = $callback($value, $key);
        }

        $descending ? arsort($results, $options)
            : asort($results, $options);

        // Once we have sorted all of the keys in the array, we will loop through them
        // and grab the corresponding model so we can set the underlying items list
        // to the sorted version. Then we'll just return the collection instance.
        foreach (array_keys($results) as $key) {
            $results[$key] = $this->items[$key];
        }

        return new static($results);
    }

    /**
     * Sort the collection using multiple comparisons.
     *
     * @param  array<array-key, (callable(TValue, TValue): mixed)|(callable(TValue, TKey): mixed)|string|array{string, string}>  $comparisons
     * @param  int  $options
     * @return static
     */
    protected function sortByMany(array $comparisons = [], int $options = SORT_REGULAR)
    {
        $items = $this->items;

        uasort($items, function ($a, $b) use ($comparisons, $options) {
            foreach ($comparisons as $comparison) {
                $comparison = Arr::wrap($comparison);

                $prop = $comparison[0];

                $ascending = Arr::get($comparison, 1, true) === true ||
                             Arr::get($comparison, 1, true) === 'asc';

                if (! is_string($prop) && is_callable($prop)) {
                    $result = $prop($a, $b);
                } else {
                    $values = [data_get($a, $prop), data_get($b, $prop)];

                    if (! $ascending) {
                        $values = array_reverse($values);
                    }

                    if (($options & SORT_FLAG_CASE) === SORT_FLAG_CASE) {
                        if (($options & SORT_NATURAL) === SORT_NATURAL) {
                            $result = strnatcasecmp($values[0], $values[1]);
                        } else {
                            $result = strcasecmp($values[0], $values[1]);
                        }
                    } else {
                        $result = match ($options) {
                            SORT_NUMERIC => (int) $values[0] <=> (int) $values[1],
                            SORT_STRING => strcmp($values[0], $values[1]),
                            SORT_NATURAL => strnatcmp((string) $values[0], (string) $values[1]),
                            SORT_LOCALE_STRING => strcoll($values[0], $values[1]),
                            default => $values[0] <=> $values[1],
                        };
                    }
                }

                if ($result === 0) {
                    continue;
                }

                return $result;
            }
        });

        return new static($items);
    }

    /**
     * Sort the collection in descending order using the given callback.
     *
     * @param  array<array-key, (callable(TValue, TValue): mixed)|(callable(TValue, TKey): mixed)|string|array{string, string}>|(callable(TValue, TKey): mixed)|string  $callback
     * @param  int  $options
     * @return static
     */
    public function sortByDesc($callback, $options = SORT_REGULAR)
    {
        if (is_array($callback) && ! is_callable($callback)) {
            foreach ($callback as $index => $key) {
                $comparison = Arr::wrap($key);

                $comparison[1] = 'desc';

                $callback[$index] = $comparison;
            }
        }

        return $this->sortBy($callback, $options, true);
    }

    /**
     * Sort the collection keys.
     *
     * @param  int  $options
     * @param  bool  $descending
     * @return static
     */
    public function sortKeys($options = SORT_REGULAR, $descending = false)
    {
        $items = $this->items;

        $descending ? krsort($items, $options) : ksort($items, $options);

        return new static($items);
    }

    /**
     * Sort the collection keys in descending order.
     *
     * @param  int  $options
     * @return static
     */
    public function sortKeysDesc($options = SORT_REGULAR)
    {
        return $this->sortKeys($options, true);
    }

    /**
     * Sort the collection keys using a callback.
     *
     * @param  callable(TKey, TKey): int  $callback
     * @return static
     */
    public function sortKeysUsing(callable $callback)
    {
        $items = $this->items;

        uksort($items, $callback);

        return new static($items);
    }

    /**
     * Splice a portion of the underlying collection array.
     *
     * @param  int  $offset
     * @param  int|null  $length
     * @param  array<array-key, TValue>  $replacement
     * @return static
     */
    public function splice($offset, $length = null, $replacement = [])
    {
        if (func_num_args() === 1) {
            return new static(array_splice($this->items, $offset));
        }

        return new static(array_splice($this->items, $offset, $length, $this->getArrayableItems($replacement)));
    }

    /**
     * Take the first or last {$limit} items.
     *
     * @param  int  $limit
     * @return static
     */
    public function take($limit)
    {
        if ($limit < 0) {
            return $this->slice($limit, abs($limit));
        }

        return $this->slice(0, $limit);
    }

    /**
     * Take items in the collection until the given condition is met.
     *
     * @param  TValue|callable(TValue,TKey): bool  $value
     * @return static
     */
    public function takeUntil($value)
    {
        return new static($this->lazy()->takeUntil($value)->all());
    }

    /**
     * Take items in the collection while the given condition is met.
     *
     * @param  TValue|callable(TValue,TKey): bool  $value
     * @return static
     */
    public function takeWhile($value)
    {
        return new static($this->lazy()->takeWhile($value)->all());
    }

    /**
     * Transform each item in the collection using a callback.
     *
     * @template TMapValue
     *
     * @param  callable(TValue, TKey): TMapValue  $callback
     * @return $this
     *
     * @phpstan-this-out static<TKey, TMapValue>
     */
    public function transform(callable $callback)
    {
        $this->items = $this->map($callback)->all();

        return $this;
    }

    /**
     * Flatten a multi-dimensional associative array with dots.
     *
     * @param  int  $depth
     * @return static
     */
    public function dot($depth = INF)
    {
        return new static(Arr::dot($this->all(), '', $depth));
    }

    /**
     * Convert a flatten "dot" notation array into an expanded array.
     *
     * @return static
     */
    public function undot()
    {
        return new static(Arr::undot($this->all()));
    }

    /**
     * Return only unique items from the collection array.
     *
     * @param  (callable(TValue, TKey): mixed)|string|null  $key
     * @param  bool  $strict
     * @return static
     */
    public function unique($key = null, $strict = false)
    {
        if (is_null($key) && $strict === false) {
            return new static(array_unique($this->items, SORT_REGULAR));
        }

        $callback = $this->valueRetriever($key);

        $exists = [];

        return $this->reject(function ($item, $key) use ($callback, $strict, &$exists) {
            if (in_array($id = $callback($item, $key), $exists, $strict)) {
                return true;
            }

            $exists[] = $id;
        });
    }

    /**
     * Reset the keys on the underlying array.
     *
     * @return static<int, TValue>
     */
    public function values()
    {
        return new static(array_values($this->items));
    }

    /**
     * Zip the collection together with one or more arrays.
     *
     * e.g. new Collection([1, 2, 3])->zip([4, 5, 6]);
     *      => [[1, 4], [2, 5], [3, 6]]
     *
     * @template TZipValue
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<array-key, TZipValue>|iterable<array-key, TZipValue>  ...$items
     * @return static<int, static<int, TValue|TZipValue>>
     */
    public function zip($items)
    {
        $arrayableItems = array_map(fn ($items) => $this->getArrayableItems($items), func_get_args());

        $params = array_merge([fn () => new static(func_get_args()), $this->items], $arrayableItems);

        return new static(array_map(...$params));
    }

    /**
     * Pad collection to the specified length with a value.
     *
     * @template TPadValue
     *
     * @param  int  $size
     * @param  TPadValue  $value
     * @return static<int, TValue|TPadValue>
     */
    public function pad($size, $value)
    {
        return new static(array_pad($this->items, $size, $value));
    }

    /**
     * Get an iterator for the items.
     *
     * @return \ArrayIterator<TKey, TValue>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }

    /**
     * Count the number of items in the collection.
     *
     * @return int<0, max>
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * Count the number of items in the collection by a field or using a callback.
     *
     * @param  (callable(TValue, TKey): (array-key|\UnitEnum))|string|null  $countBy
     * @return static<array-key, int>
     */
    public function countBy($countBy = null)
    {
        return new static($this->lazy()->countBy($countBy)->all());
    }

    /**
     * Add an item to the collection.
     *
     * @param  TValue  $item
     * @return $this
     */
    public function add($item)
    {
        $this->items[] = $item;

        return $this;
    }

    /**
     * Get a base Support collection instance from this collection.
     *
     * @return \Illuminate\Support\Collection<TKey, TValue>
     */
    public function toBase()
    {
        return new self($this);
    }

    /**
     * Determine if an item exists at an offset.
     *
     * @param  TKey  $key
     * @return bool
     */
    public function offsetExists($key): bool
    {
        return isset($this->items[$key]);
    }

    /**
     * Get an item at a given offset.
     *
     * @param  TKey  $key
     * @return TValue
     */
    public function offsetGet($key): mixed
    {
        return $this->items[$key];
    }

    /**
     * Set the item at a given offset.
     *
     * @param  TKey|null  $key
     * @param  TValue  $value
     * @return void
     */
    public function offsetSet($key, $value): void
    {
        if (is_null($key)) {
            $this->items[] = $value;
        } else {
            $this->items[$key] = $value;
        }
    }

    /**
     * Unset the item at a given offset.
     *
     * @param  TKey  $key
     * @return void
     */
    public function offsetUnset($key): void
    {
        unset($this->items[$key]);
    }
}
