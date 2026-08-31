<?php

namespace Illuminate\Support;

use CachingIterator;
use Countable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use IteratorAggregate;
use JsonSerializable;
use Traversable;

/**
 * @template TKey of array-key
 *
 * @template-covariant TValue
 *
 * @extends \Illuminate\Contracts\Support\Arrayable<TKey, TValue>
 * @extends \IteratorAggregate<TKey, TValue>
 */
interface Enumerable extends Arrayable, Countable, IteratorAggregate, Jsonable, JsonSerializable
{
    /**
     * Create a new collection instance if the value isn't one already.
     *
     * @template TMakeKey of array-key
     * @template TMakeValue
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<TMakeKey, TMakeValue>|iterable<TMakeKey, TMakeValue>|null  $items
     * @return static<TMakeKey, TMakeValue>
     */
    public static function make($items = []);

    /**
     * Create a new instance by invoking the callback a given amount of times.
     *
     * @param  int  $number
     * @param  callable|null  $callback
     * @return static
     */
    public static function times($number, ?callable $callback = null);

    /**
     * Create a collection with the given range.
     *
     * @param  int  $from
     * @param  int  $to
     * @param  int  $step
     * @return static
     */
    public static function range($from, $to, $step = 1);

    /**
     * Wrap the given value in a collection if applicable.
     *
     * @template TWrapValue
     *
     * @param  iterable<array-key, TWrapValue>|TWrapValue  $value
     * @return static<array-key, TWrapValue>
     */
    public static function wrap($value);

    /**
     * Get the underlying items from the given collection if applicable.
     *
     * @template TUnwrapKey of array-key
     * @template TUnwrapValue
     *
     * @param  array<TUnwrapKey, TUnwrapValue>|static<TUnwrapKey, TUnwrapValue>  $value
     * @return array<TUnwrapKey, TUnwrapValue>
     */
    public static function unwrap($value);

    /**
     * Create a new instance with no items.
     *
     * @return static
     */
    public static function empty();

    /**
     * Get all items in the enumerable.
     *
     * @return array
     */
    public function all();

    /**
     * Alias for the "avg" method.
     *
     * @param  (callable(TValue): float|int)|string|null  $callback
     * @return float|int|null
     */
    public function average($callback = null);

    /**
     * Get the median of a given key.
     *
     * @param  string|array<array-key, string>|null  $key
     * @return float|int|null
     */
    public function median($key = null);

    /**
     * Get the mode of a given key.
     *
     * @param  string|array<array-key, string>|null  $key
     * @return array<int, float|int>|null
     */
    public function mode($key = null);

    /**
     * Collapse the items into a single enumerable.
     *
     * @return static<int, mixed>
     */
    public function collapse();

    /**
     * Alias for the "contains" method.
     *
     * @param  (callable(TValue, TKey): bool)|TValue|string  $key
     * @param  mixed  $operator
     * @param  mixed  $value
     * @return bool
     */
    public function some($key, $operator = null, $value = null);

    /**
     * Determine if an item exists, using strict comparison.
     *
     * @param  (callable(TValue): bool)|TValue|array-key  $key
     * @param  TValue|null  $value
     * @return bool
     */
    public function containsStrict($key, $value = null);

    /**
     * Get the average value of a given key.
     *
     * @param  (callable(TValue): float|int)|string|null  $callback
     * @return float|int|null
     */
    public function avg($callback = null);

    /**
     * Determine if an item exists in the enumerable.
     *
     * @param  (callable(TValue, TKey): bool)|TValue|string  $key
     * @param  mixed  $operator
     * @param  mixed  $value
     * @return bool
     */
    public function contains($key, $operator = null, $value = null);

    /**
     * Determine if an item is not contained in the collection.
     *
     * @param  mixed  $key
     * @param  mixed  $operator
     * @param  mixed  $value
     * @return bool
     */
    public function doesntContain($key, $operator = null, $value = null);

    /**
     * Cross join with the given lists, returning all possible permutations.
     *
     * @template TCrossJoinKey
     * @template TCrossJoinValue
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<TCrossJoinKey, TCrossJoinValue>|iterable<TCrossJoinKey, TCrossJoinValue>  ...$lists
     * @return static<int, array<int, TValue|TCrossJoinValue>>
     */
    public function crossJoin(...$lists);

    /**
     * Dump the collection and end the script.
     *
     * @param  mixed  ...$args
     * @return never
     */
    public function dd(...$args);

    /**
     * Dump the collection.
     *
     * @param  mixed  ...$args
     * @return $this
     */
    public function dump(...$args);

    /**
     * Get the items that are not present in the given items.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<array-key, TValue>|iterable<array-key, TValue>  $items
     * @return static
     */
    public function diff($items);

    /**
     * Get the items that are not present in the given items, using the callback.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<array-key, TValue>|iterable<array-key, TValue>  $items
     * @param  callable(TValue, TValue): int  $callback
     * @return static
     */
    public function diffUsing($items, callable $callback);

    /**
     * Get the items whose keys and values are not present in the given items.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<TKey, TValue>|iterable<TKey, TValue>  $items
     * @return static
     */
    public function diffAssoc($items);

    /**
     * Get the items whose keys and values are not present in the given items, using the callback.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<TKey, TValue>|iterable<TKey, TValue>  $items
     * @param  callable(TKey, TKey): int  $callback
     * @return static
     */
    public function diffAssocUsing($items, callable $callback);

    /**
     * Get the items whose keys are not present in the given items.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<TKey, mixed>|iterable<TKey, mixed>  $items
     * @return static
     */
    public function diffKeys($items);

    /**
     * Get the items whose keys are not present in the given items, using the callback.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<TKey, mixed>|iterable<TKey, mixed>  $items
     * @param  callable(TKey, TKey): int  $callback
     * @return static
     */
    public function diffKeysUsing($items, callable $callback);

    /**
     * Retrieve duplicate items.
     *
     * @param  (callable(TValue): bool)|string|null  $callback
     * @param  bool  $strict
     * @return static
     */
    public function duplicates($callback = null, $strict = false);

    /**
     * Retrieve duplicate items using strict comparison.
     *
     * @param  (callable(TValue): bool)|string|null  $callback
     * @return static
     */
    public function duplicatesStrict($callback = null);

    /**
     * Execute a callback over each item.
     *
     * @param  callable(TValue, TKey): mixed  $callback
     * @return $this
     */
    public function each(callable $callback);

    /**
     * Execute a callback over each nested chunk of items.
     *
     * @param  callable  $callback
     * @return static
     */
    public function eachSpread(callable $callback);

    /**
     * Determine if all items pass the given truth test.
     *
     * @param  (callable(TValue, TKey): bool)|TValue|string  $key
     * @param  mixed  $operator
     * @param  mixed  $value
     * @return bool
     */
    public function every($key, $operator = null, $value = null);

    /**
     * Get all items except for those with the specified keys.
     *
     * @param  \Illuminate\Support\Enumerable<array-key, TKey>|array<array-key, TKey>  $keys
     * @return static
     */
    public function except($keys);

    /**
     * Run a filter over each of the items.
     *
     * @param  (callable(TValue): bool)|null  $callback
     * @return static
     */
    public function filter(?callable $callback = null);

    /**
     * Apply the callback if the given "value" is (or resolves to) truthy.
     *
     * @template TWhenReturnType as null
     *
     * @param  bool  $value
     * @param  (callable($this): TWhenReturnType)|null  $callback
     * @param  (callable($this): TWhenReturnType)|null  $default
     * @return $this|TWhenReturnType
     */
    public function when($value, ?callable $callback = null, ?callable $default = null);

    /**
     * Apply the callback if the collection is empty.
     *
     * @template TWhenEmptyReturnType
     *
     * @param  (callable($this): TWhenEmptyReturnType)  $callback
     * @param  (callable($this): TWhenEmptyReturnType)|null  $default
     * @return $this|TWhenEmptyReturnType
     */
    public function whenEmpty(callable $callback, ?callable $default = null);

    /**
     * Apply the callback if the collection is not empty.
     *
     * @template TWhenNotEmptyReturnType
     *
     * @param  callable($this): TWhenNotEmptyReturnType  $callback
     * @param  (callable($this): TWhenNotEmptyReturnType)|null  $default
     * @return $this|TWhenNotEmptyReturnType
     */
    public function whenNotEmpty(callable $callback, ?callable $default = null);

    /**
     * Apply the callback if the given "value" is (or resolves to) falsy.
     *
     * @template TUnlessReturnType
     *
     * @param  bool  $value
     * @param  (callable($this): TUnlessReturnType)  $callback
     * @param  (callable($this): TUnlessReturnType)|null  $default
     * @return $this|TUnlessReturnType
     */
    public function unless($value, callable $callback, ?callable $default = null);

    /**
     * Apply the callback unless the collection is empty.
     *
     * @template TUnlessEmptyReturnType
     *
     * @param  callable($this): TUnlessEmptyReturnType  $callback
     * @param  (callable($this): TUnlessEmptyReturnType)|null  $default
     * @return $this|TUnlessEmptyReturnType
     */
    public function unlessEmpty(callable $callback, ?callable $default = null);

    /**
     * Apply the callback unless the collection is not empty.
     *
     * @template TUnlessNotEmptyReturnType
     *
     * @param  callable($this): TUnlessNotEmptyReturnType  $callback
     * @param  (callable($this): TUnlessNotEmptyReturnType)|null  $default
     * @return $this|TUnlessNotEmptyReturnType
     */
    public function unlessNotEmpty(callable $callback, ?callable $default = null);

    /**
     * Filter items by the given key value pair.
     *
     * @param  string  $key
     * @param  mixed  $operator
     * @param  mixed  $value
     * @return static
     */
    public function where($key, $operator = null, $value = null);

    /**
     * Filter items where the value for the given key is null.
     *
     * @param  string|null  $key
     * @return static
     */
    public function whereNull($key = null);

    /**
     * Filter items where the value for the given key is not null.
     *
     * @param  string|null  $key
     * @return static
     */
    public function whereNotNull($key = null);

    /**
     * Filter items by the given key value pair using strict comparison.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return static
     */
    public function whereStrict($key, $value);

    /**
     * Filter items by the given key value pair.
     *
     * @param  string  $key
     * @param  \Illuminate\Contracts\Support\Arrayable|iterable  $values
     * @param  bool  $strict
     * @return static
     */
    public function whereIn($key, $values, $strict = false);

    /**
     * Filter items by the given key value pair using strict comparison.
     *
     * @param  string  $key
     * @param  \Illuminate\Contracts\Support\Arrayable|iterable  $values
     * @return static
     */
    public function whereInStrict($key, $values);

    /**
     * Filter items such that the value of the given key is between the given values.
     *
     * @param  string  $key
     * @param  \Illuminate\Contracts\Support\Arrayable|iterable  $values
     * @return static
     */
    public function whereBetween($key, $values);

    /**
     * Filter items such that the value of the given key is not between the given values.
     *
     * @param  string  $key
     * @param  \Illuminate\Contracts\Support\Arrayable|iterable  $values
     * @return static
     */
    public function whereNotBetween($key, $values);

    /**
     * Filter items by the given key value pair.
     *
     * @param  string  $key
     * @param  \Illuminate\Contracts\Support\Arrayable|iterable  $values
     * @param  bool  $strict
     * @return static
     */
    public function whereNotIn($key, $values, $strict = false);

    /**
     * Filter items by the given key value pair using strict comparison.
     *
     * @param  string  $key
     * @param  \Illuminate\Contracts\Support\Arrayable|iterable  $values
     * @return static
     */
    public function whereNotInStrict($key, $values);

    /**
     * Filter the items, removing any items that don't match the given type(s).
     *
     * @template TWhereInstanceOf
     *
     * @param  class-string<TWhereInstanceOf>|array<array-key, class-string<TWhereInstanceOf>>  $type
     * @return static<TKey, TWhereInstanceOf>
     */
    public function whereInstanceOf($type);

    /**
     * Get the first item from the enumerable passing the given truth test.
     *
     * @template TFirstDefault
     *
     * @param  (callable(TValue,TKey): bool)|null  $callback
     * @param  TFirstDefault|(\Closure(): TFirstDefault)  $default
     * @return TValue|TFirstDefault
     */
    public function first(?callable $callback = null, $default = null);

    /**
     * Get the first item by the given key value pair.
     *
     * @param  string  $key
     * @param  mixed  $operator
     * @param  mixed  $value
     * @return TValue|null
     */
    public function firstWhere($key, $operator = null, $value = null);

    /**
     * Get a flattened array of the items in the collection.
     *
     * @param  int  $depth
     * @return static
     */
    public function flatten($depth = INF);

    /**
     * Flip the values with their keys.
     *
     * @return static<TValue, TKey>
     */
    public function flip();

    /**
     * Get an item from the collection by key.
     *
     * @template TGetDefault
     *
     * @param  TKey  $key
     * @param  TGetDefault|(\Closure(): TGetDefault)  $default
     * @return TValue|TGetDefault
     */
    public function get($key, $default = null);

    /**
     * Group an associative array by a field or using a callback.
     *
     * @template TGroupKey of array-key
     *
     * @param  (callable(TValue, TKey): TGroupKey)|array|string  $groupBy
     * @param  bool  $preserveKeys
     * @return static<($groupBy is string ? array-key : ($groupBy is array ? array-key : TGroupKey)), static<($preserveKeys is true ? TKey : int), ($groupBy is array ? mixed : TValue)>>
     */
    public function groupBy($groupBy, $preserveKeys = false);

    /**
     * Key an associative array by a field or using a callback.
     *
     * @template TNewKey of array-key
     *
     * @param  (callable(TValue, TKey): TNewKey)|array|string  $keyBy
     * @return static<($keyBy is string ? array-key : ($keyBy is array ? array-key : TNewKey)), TValue>
     */
    public function keyBy($keyBy);

    /**
     * Determine if an item exists in the collection by key.
     *
     * @param  TKey|array<array-key, TKey>  $key
     * @return bool
     */
    public function has($key);

    /**
     * Determine if any of the keys exist in the collection.
     *
     * @param  mixed  $key
     * @return bool
     */
    public function hasAny($key);

    /**
     * Concatenate values of a given key as a string.
     *
     * @param  (callable(TValue, TKey): mixed)|string  $value
     * @param  string|null  $glue
     * @return string
     */
    public function implode($value, $glue = null);

    /**
     * Intersect the collection with the given items.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<TKey, TValue>|iterable<TKey, TValue>  $items
     * @return static
     */
    public function intersect($items);

    /**
     * Intersect the collection with the given items, using the callback.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<array-key, TValue>|iterable<array-key, TValue>  $items
     * @param  callable(TValue, TValue): int  $callback
     * @return static
     */
    public function intersectUsing($items, callable $callback);

    /**
     * Intersect the collection with the given items with additional index check.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<TKey, TValue>|iterable<TKey, TValue>  $items
     * @return static
     */
    public function intersectAssoc($items);

    /**
     * Intersect the collection with the given items with additional index check, using the callback.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<array-key, TValue>|iterable<array-key, TValue>  $items
     * @param  callable(TValue, TValue): int  $callback
     * @return static
     */
    public function intersectAssocUsing($items, callable $callback);

    /**
     * Intersect the collection with the given items by key.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<TKey, mixed>|iterable<TKey, mixed>  $items
     * @return static
     */
    public function intersectByKeys($items);

    /**
     * Determine if the collection is empty or not.
     *
     * @return bool
     */
    public function isEmpty();

    /**
     * Determine if the collection is not empty.
     *
     * @return bool
     */
    public function isNotEmpty();

    /**
     * Determine if the collection contains a single item.
     *
     * @return bool
     */
    public function containsOneItem();

    /**
     * Determine if the collection contains multiple items.
     *
     * @return bool
     */
    public function containsManyItems();

    /**
     * Join all items from the collection using a string. The final items can use a separate glue string.
     *
     * @param  string  $glue
     * @param  string  $finalGlue
     * @return string
     */
    public function join($glue, $finalGlue = '');

    /**
     * Get the keys of the collection items.
     *
     * @return static<int, TKey>
     */
    public function keys();

    /**
     * Get the last item from the collection.
     *
     * @template TLastDefault
     *
     * @param  (callable(TValue, TKey): bool)|null  $callback
     * @param  TLastDefault|(\Closure(): TLastDefault)  $default
     * @return TValue|TLastDefault
     */
    public function last(?callable $callback = null, $default = null);

    /**
     * Run a map over each of the items.
     *
     * @template TMapValue
     *
     * @param  callable(TValue, TKey): TMapValue  $callback
     * @return static<TKey, TMapValue>
     */
    public function map(callable $callback);

    /**
     * Run a map over each nested chunk of items.
     *
     * @param  callable  $callback
     * @return static
     */
    public function mapSpread(callable $callback);

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
    public function mapToDictionary(callable $callback);

    /**
     * Run a grouping map over the items.
     *
     * The callback should return an associative array with a single key/value pair.
     *
     * @template TMapToGroupsKey of array-key
     * @template TMapToGroupsValue
     *
     * @param  callable(TValue, TKey): array<TMapToGroupsKey, TMapToGroupsValue>  $callback
     * @return static<TMapToGroupsKey, static<int, TMapToGroupsValue>>
     */
    public function mapToGroups(callable $callback);

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
    public function mapWithKeys(callable $callback);

    /**
     * Map a collection and flatten the result by a single level.
     *
     * @template TFlatMapKey of array-key
     * @template TFlatMapValue
     *
     * @param  callable(TValue, TKey): (\Illuminate\Support\Collection<TFlatMapKey, TFlatMapValue>|array<TFlatMapKey, TFlatMapValue>)  $callback
     * @return static<TFlatMapKey, TFlatMapValue>
     */
    public function flatMap(callable $callback);

    /**
     * Map the values into a new class.
     *
     * @template TMapIntoValue
     *
     * @param  class-string<TMapIntoValue>  $class
     * @return static<TKey, TMapIntoValue>
     */
    public function mapInto($class);

    /**
     * Merge the collection with the given items.
     *
     * @template TMergeValue
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<TKey, TMergeValue>|iterable<TKey, TMergeValue>  $items
     * @return static<TKey, TValue|TMergeValue>
     */
    public function merge($items);

    /**
     * Recursively merge the collection with the given items.
     *
     * @template TMergeRecursiveValue
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<TKey, TMergeRecursiveValue>|iterable<TKey, TMergeRecursiveValue>  $items
     * @return static<TKey, TValue|TMergeRecursiveValue>
     */
    public function mergeRecursive($items);

    /**
     * Create a collection by using this collection for keys and another for its values.
     *
     * @template TCombineValue
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<array-key, TCombineValue>|iterable<array-key, TCombineValue>  $values
     * @return static<TValue, TCombineValue>
     */
    public function combine($values);

    /**
     * Union the collection with the given items.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<TKey, TValue>|iterable<TKey, TValue>  $items
     * @return static
     */
    public function union($items);

    /**
     * Get the min value of a given key.
     *
     * @param  (callable(TValue):mixed)|string|null  $callback
     * @return mixed
     */
    public function min($callback = null);

    /**
     * Get the max value of a given key.
     *
     * @param  (callable(TValue):mixed)|string|null  $callback
     * @return mixed
     */
    public function max($callback = null);

    /**
     * Create a new collection consisting of every n-th element.
     *
     * @param  int  $step
     * @param  int  $offset
     * @return static
     */
    public function nth($step, $offset = 0);

    /**
     * Get the items with the specified keys.
     *
     * @param  \Illuminate\Support\Enumerable<array-key, TKey>|array<array-key, TKey>|string  $keys
     * @return static
     */
    public function only($keys);

    /**
     * "Paginate" the collection by slicing it into a smaller collection.
     *
     * @param  int  $page
     * @param  int  $perPage
     * @return static
     */
    public function forPage($page, $perPage);

    /**
     * Partition the collection into two arrays using the given callback or key.
     *
     * @param  (callable(TValue, TKey): bool)|TValue|string  $key
     * @param  mixed  $operator
     * @param  mixed  $value
     * @return static<int<0, 1>, static<TKey, TValue>>
     */
    public function partition($key, $operator = null, $value = null);

    /**
     * Push all of the given items onto the collection.
     *
     * @template TConcatKey of array-key
     * @template TConcatValue
     *
     * @param  iterable<TConcatKey, TConcatValue>  $source
     * @return static<TKey|TConcatKey, TValue|TConcatValue>
     */
    public function concat($source);

    /**
     * Get one or a specified number of items randomly from the collection.
     *
     * @param  int|null  $number
     * @return static<int, TValue>|TValue
     *
     * @throws \InvalidArgumentException
     */
    public function random($number = null);

    /**
     * Reduce the collection to a single value.
     *
     * @template TReduceInitial
     * @template TReduceReturnType
     *
     * @param  callable(TReduceInitial|TReduceReturnType, TValue, TKey): TReduceReturnType  $callback
     * @param  TReduceInitial  $initial
     * @return TReduceInitial|TReduceReturnType
     */
    public function reduce(callable $callback, $initial = null);

    /**
     * Reduce the collection to multiple aggregate values.
     *
     * @param  callable  $callback
     * @param  mixed  ...$initial
     * @return array
     *
     * @throws \UnexpectedValueException
     */
    public function reduceSpread(callable $callback, ...$initial);

    /**
     * Replace the collection items with the given items.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<TKey, TValue>|iterable<TKey, TValue>  $items
     * @return static
     */
    public function replace($items);

    /**
     * Recursively replace the collection items with the given items.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<TKey, TValue>|iterable<TKey, TValue>  $items
     * @return static
     */
    public function replaceRecursive($items);

    /**
     * Reverse items order.
     *
     * @return static
     */
    public function reverse();

    /**
     * Search the collection for a given value and return the corresponding key if successful.
     *
     * @param  TValue|callable(TValue,TKey): bool  $value
     * @param  bool  $strict
     * @return TKey|bool
     */
    public function search($value, $strict = false);

    /**
     * Get the item before the given item.
     *
     * @param  TValue|(callable(TValue,TKey): bool)  $value
     * @param  bool  $strict
     * @return TValue|null
     */
    public function before($value, $strict = false);

    /**
     * Get the item after the given item.
     *
     * @param  TValue|(callable(TValue,TKey): bool)  $value
     * @param  bool  $strict
     * @return TValue|null
     */
    public function after($value, $strict = false);

    /**
     * Shuffle the items in the collection.
     *
     * @return static
     */
    public function shuffle();

    /**
     * Create chunks representing a "sliding window" view of the items in the collection.
     *
     * @param  int  $size
     * @param  int  $step
     * @return static<int, static>
     */
    public function sliding($size = 2, $step = 1);

    /**
     * Skip the first {$count} items.
     *
     * @param  int  $count
     * @return static
     */
    public function skip($count);

    /**
     * Skip items in the collection until the given condition is met.
     *
     * @param  TValue|callable(TValue,TKey): bool  $value
     * @return static
     */
    public function skipUntil($value);

    /**
     * Skip items in the collection while the given condition is met.
     *
     * @param  TValue|callable(TValue,TKey): bool  $value
     * @return static
     */
    public function skipWhile($value);

    /**
     * Get a slice of items from the enumerable.
     *
     * @param  int  $offset
     * @param  int|null  $length
     * @return static
     */
    public function slice($offset, $length = null);

    /**
     * Split a collection into a certain number of groups.
     *
     * @param  int  $numberOfGroups
     * @return static<int, static>
     */
    public function split($numberOfGroups);

    /**
     * Get the first item in the collection, but only if exactly one item exists. Otherwise, throw an exception.
     *
     * @param  (callable(TValue, TKey): bool)|string  $key
     * @param  mixed  $operator
     * @param  mixed  $value
     * @return TValue
     *
     * @throws \Illuminate\Support\ItemNotFoundException
     * @throws \Illuminate\Support\MultipleItemsFoundException
     */
    public function sole($key = null, $operator = null, $value = null);

    /**
     * Get the first item in the collection but throw an exception if no matching items exist.
     *
     * @param  (callable(TValue, TKey): bool)|string|null  $key
     * @param  mixed  $operator
     * @param  mixed  $value
     * @return TValue
     *
     * @throws \Illuminate\Support\ItemNotFoundException
     */
    public function firstOrFail($key = null, $operator = null, $value = null);

    /**
     * Chunk the collection into chunks of the given size.
     *
     * @param  int  $size
     * @return static<int, static>
     */
    public function chunk($size);

    /**
     * Chunk the collection into chunks with a callback.
     *
     * @param  callable(TValue, TKey, static<int, TValue>): bool  $callback
     * @return static<int, static<int, TValue>>
     */
    public function chunkWhile(callable $callback);

    /**
     * Split a collection into a certain number of groups, and fill the first groups completely.
     *
     * @param  int  $numberOfGroups
     * @return static<int, static>
     */
    public function splitIn($numberOfGroups);

    /**
     * Sort through each item with a callback.
     *
     * @param  (callable(TValue, TValue): int)|null|int  $callback
     * @return static
     */
    public function sort($callback = null);

    /**
     * Sort items in descending order.
     *
     * @param  int  $options
     * @return static
     */
    public function sortDesc($options = SORT_REGULAR);

    /**
     * Sort the collection using the given callback.
     *
     * @param  array<array-key, (callable(TValue, TValue): mixed)|(callable(TValue, TKey): mixed)|string|array{string, string}>|(callable(TValue, TKey): mixed)|string  $callback
     * @param  int  $options
     * @param  bool  $descending
     * @return static
     */
    public function sortBy($callback, $options = SORT_REGULAR, $descending = false);

    /**
     * Sort the collection in descending order using the given callback.
     *
     * @param  array<array-key, (callable(TValue, TValue): mixed)|(callable(TValue, TKey): mixed)|string|array{string, string}>|(callable(TValue, TKey): mixed)|string  $callback
     * @param  int  $options
     * @return static
     */
    public function sortByDesc($callback, $options = SORT_REGULAR);

    /**
     * Sort the collection keys.
     *
     * @param  int  $options
     * @param  bool  $descending
     * @return static
     */
    public function sortKeys($options = SORT_REGULAR, $descending = false);

    /**
     * Sort the collection keys in descending order.
     *
     * @param  int  $options
     * @return static
     */
    public function sortKeysDesc($options = SORT_REGULAR);

    /**
     * Sort the collection keys using a callback.
     *
     * @param  callable(TKey, TKey): int  $callback
     * @return static
     */
    public function sortKeysUsing(callable $callback);

    /**
     * Get the sum of the given values.
     *
     * @param  (callable(TValue): mixed)|string|null  $callback
     * @return mixed
     */
    public function sum($callback = null);

    /**
     * Take the first or last {$limit} items.
     *
     * @param  int  $limit
     * @return static
     */
    public function take($limit);

    /**
     * Take items in the collection until the given condition is met.
     *
     * @param  TValue|callable(TValue,TKey): bool  $value
     * @return static
     */
    public function takeUntil($value);

    /**
     * Take items in the collection while the given condition is met.
     *
     * @param  TValue|callable(TValue,TKey): bool  $value
     * @return static
     */
    public function takeWhile($value);

    /**
     * Pass the collection to the given callback and then return it.
     *
     * @param  callable(TValue): mixed  $callback
     * @return $this
     */
    public function tap(callable $callback);

    /**
     * Pass the enumerable to the given callback and return the result.
     *
     * @template TPipeReturnType
     *
     * @param  callable($this): TPipeReturnType  $callback
     * @return TPipeReturnType
     */
    public function pipe(callable $callback);

    /**
     * Pass the collection into a new class.
     *
     * @template TPipeIntoValue
     *
     * @param  class-string<TPipeIntoValue>  $class
     * @return TPipeIntoValue
     */
    public function pipeInto($class);

    /**
     * Pass the collection through a series of callable pipes and return the result.
     *
     * @param  array<callable>  $pipes
     * @return mixed
     */
    public function pipeThrough($pipes);

    /**
     * Get the values of a given key.
     *
     * @param  string|array<array-key, string>  $value
     * @param  string|null  $key
     * @return static<array-key, mixed>
     */
    public function pluck($value, $key = null);

    /**
     * Create a collection of all elements that do not pass a given truth test.
     *
     * @param  (callable(TValue, TKey): bool)|bool|TValue  $callback
     * @return static
     */
    public function reject($callback = true);

    /**
     * Convert a flatten "dot" notation array into an expanded array.
     *
     * @return static
     */
    public function undot();

    /**
     * Return only unique items from the collection array.
     *
     * @param  (callable(TValue, TKey): mixed)|string|null  $key
     * @param  bool  $strict
     * @return static
     */
    public function unique($key = null, $strict = false);

    /**
     * Return only unique items from the collection array using strict comparison.
     *
     * @param  (callable(TValue, TKey): mixed)|string|null  $key
     * @return static
     */
    public function uniqueStrict($key = null);

    /**
     * Reset the keys on the underlying array.
     *
     * @return static<int, TValue>
     */
    public function values();

    /**
     * Pad collection to the specified length with a value.
     *
     * @template TPadValue
     *
     * @param  int  $size
     * @param  TPadValue  $value
     * @return static<int, TValue|TPadValue>
     */
    public function pad($size, $value);

    /**
     * Get the values iterator.
     *
     * @return \Traversable<TKey, TValue>
     */
    public function getIterator(): Traversable;

    /**
     * Count the number of items in the collection.
     *
     * @return int
     */
    public function count(): int;

    /**
     * Count the number of items in the collection by a field or using a callback.
     *
     * @param  (callable(TValue, TKey): array-key)|string|null  $countBy
     * @return static<array-key, int>
     */
    public function countBy($countBy = null);

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
    public function zip($items);

    /**
     * Collect the values into a collection.
     *
     * @return \Illuminate\Support\Collection<TKey, TValue>
     */
    public function collect();

    /**
     * Get the collection of items as a plain array.
     *
     * @return array<TKey, mixed>
     */
    public function toArray();

    /**
     * Convert the object into something JSON serializable.
     *
     * @return mixed
     */
    public function jsonSerialize(): mixed;

    /**
     * Get the collection of items as JSON.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0);

    /**
     * Get the collection of items as pretty print formatted JSON.
     *
     *
     * @param  int  $options
     * @return string
     */
    public function toPrettyJson(int $options = 0);

    /**
     * Get a CachingIterator instance.
     *
     * @param  int  $flags
     * @return \CachingIterator
     */
    public function getCachingIterator($flags = CachingIterator::CALL_TOSTRING);

    /**
     * Convert the collection to its string representation.
     *
     * @return string
     */
    public function __toString();

    /**
     * Indicate that the model's string representation should be escaped when __toString is invoked.
     *
     * @param  bool  $escape
     * @return $this
     */
    public function escapeWhenCastingToString($escape = true);

    /**
     * Add a method to the list of proxied methods.
     *
     * @param  string  $method
     * @return void
     */
    public static function proxy($method);

    /**
     * Dynamically access collection proxies.
     *
     * @param  string  $key
     * @return mixed
     *
     * @throws \Exception
     */
    public function __get($key);
}
