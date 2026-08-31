<?php

namespace Illuminate\Support;

use ArrayIterator;
use Closure;
use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;
use Generator;
use Illuminate\Contracts\Support\CanBeEscapedWhenCastToString;
use Illuminate\Support\Traits\EnumeratesValues;
use Illuminate\Support\Traits\Macroable;
use InvalidArgumentException;
use IteratorAggregate;
use stdClass;
use Traversable;

/**
 * @template TKey of array-key
 *
 * @template-covariant TValue
 *
 * @implements \Illuminate\Support\Enumerable<TKey, TValue>
 */
class LazyCollection implements CanBeEscapedWhenCastToString, Enumerable
{
    /**
     * @use \Illuminate\Support\Traits\EnumeratesValues<TKey, TValue>
     */
    use EnumeratesValues, Macroable;

    /**
     * The source from which to generate items.
     *
     * @var (Closure(): \Generator<TKey, TValue, mixed, void>)|static|array<TKey, TValue>
     */
    public $source;

    /**
     * Create a new lazy collection instance.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<TKey, TValue>|iterable<TKey, TValue>|(Closure(): \Generator<TKey, TValue, mixed, void>)|self<TKey, TValue>|array<TKey, TValue>|null  $source
     */
    public function __construct($source = null)
    {
        if ($source instanceof Closure || $source instanceof self) {
            $this->source = $source;
        } elseif (is_null($source)) {
            $this->source = static::empty();
        } elseif ($source instanceof Generator) {
            throw new InvalidArgumentException(
                'Generators should not be passed directly to LazyCollection. Instead, pass a generator function.'
            );
        } else {
            $this->source = $this->getArrayableItems($source);
        }
    }

    /**
     * Create a new collection instance if the value isn't one already.
     *
     * @template TMakeKey of array-key
     * @template TMakeValue
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<TMakeKey, TMakeValue>|iterable<TMakeKey, TMakeValue>|(Closure(): \Generator<TMakeKey, TMakeValue, mixed, void>)|self<TMakeKey, TMakeValue>|array<TMakeKey, TMakeValue>|null  $items
     * @return static<TMakeKey, TMakeValue>
     */
    public static function make($items = [])
    {
        return new static($items);
    }

    /**
     * Create a collection with the given range.
     *
     * @param  int  $from
     * @param  int  $to
     * @param  int  $step
     * @return ($step is zero ? never : static<int, int>)
     *
     * @throws \InvalidArgumentException
     */
    public static function range($from, $to, $step = 1)
    {
        if ($step == 0) {
            throw new InvalidArgumentException('Step value cannot be zero.');
        }

        return new static(function () use ($from, $to, $step) {
            if ($from <= $to) {
                for (; $from <= $to; $from += abs($step)) {
                    yield $from;
                }
            } else {
                for (; $from >= $to; $from -= abs($step)) {
                    yield $from;
                }
            }
        });
    }

    /**
     * Get all items in the enumerable.
     *
     * @return array<TKey, TValue>
     */
    public function all()
    {
        if (is_array($this->source)) {
            return $this->source;
        }

        return iterator_to_array($this->getIterator());
    }

    /**
     * Eager load all items into a new lazy collection backed by an array.
     *
     * @return static<TKey, TValue>
     */
    public function eager()
    {
        return new static($this->all());
    }

    /**
     * Cache values as they're enumerated.
     *
     * @return static<TKey, TValue>
     */
    public function remember()
    {
        $iterator = $this->getIterator();

        $iteratorIndex = 0;

        $cache = [];

        return new static(function () use ($iterator, &$iteratorIndex, &$cache) {
            for ($index = 0; true; $index++) {
                if (array_key_exists($index, $cache)) {
                    yield $cache[$index][0] => $cache[$index][1];

                    continue;
                }

                if ($iteratorIndex < $index) {
                    $iterator->next();

                    $iteratorIndex++;
                }

                if (! $iterator->valid()) {
                    break;
                }

                $cache[$index] = [$iterator->key(), $iterator->current()];

                yield $cache[$index][0] => $cache[$index][1];
            }
        });
    }

    /**
     * Get the median of a given key.
     *
     * @param  string|array<array-key, string>|null  $key
     * @return float|int|null
     */
    public function median($key = null)
    {
        return $this->collect()->median($key);
    }

    /**
     * Get the mode of a given key.
     *
     * @param  string|array<string>|null  $key
     * @return array<int, float|int>|null
     */
    public function mode($key = null)
    {
        return $this->collect()->mode($key);
    }

    /**
     * Collapse the collection of items into a single array.
     *
     * @return static<int, mixed>
     */
    public function collapse()
    {
        return new static(function () {
            foreach ($this as $values) {
                if (is_array($values) || $values instanceof Enumerable) {
                    foreach ($values as $value) {
                        yield $value;
                    }
                }
            }
        });
    }

    /**
     * Collapse the collection of items into a single array while preserving its keys.
     *
     * @return static<mixed, mixed>
     */
    public function collapseWithKeys()
    {
        return new static(function () {
            foreach ($this as $values) {
                if (is_array($values) || $values instanceof Enumerable) {
                    foreach ($values as $key => $value) {
                        yield $key => $value;
                    }
                }
            }
        });
    }

    /**
     * Determine if an item exists in the enumerable.
     *
     * @param  (callable(TValue, TKey): bool)|TValue|string  $key
     * @param  mixed  $operator
     * @param  mixed  $value
     * @return bool
     */
    public function contains($key, $operator = null, $value = null)
    {
        if (func_num_args() === 1 && $this->useAsCallable($key)) {
            $placeholder = new stdClass;

            /** @var callable $key */
            return $this->first($key, $placeholder) !== $placeholder;
        }

        if (func_num_args() === 1) {
            $needle = $key;

            foreach ($this as $value) {
                if ($value == $needle) {
                    return true;
                }
            }

            return false;
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

        foreach ($this as $item) {
            if ($item === $key) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if an item is not contained in the enumerable.
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
     * {@inheritDoc}
     */
    #[\Override]
    public function crossJoin(...$arrays)
    {
        return $this->passthru(__FUNCTION__, func_get_args());
    }

    /**
     * Count the number of items in the collection by a field or using a callback.
     *
     * @param  (callable(TValue, TKey): (array-key|\UnitEnum))|string|null  $countBy
     * @return static<array-key, int>
     */
    public function countBy($countBy = null)
    {
        $countBy = is_null($countBy)
            ? $this->identity()
            : $this->valueRetriever($countBy);

        return new static(function () use ($countBy) {
            $counts = [];

            foreach ($this as $key => $value) {
                $group = enum_value($countBy($value, $key));

                if (empty($counts[$group])) {
                    $counts[$group] = 0;
                }

                $counts[$group]++;
            }

            yield from $counts;
        });
    }

    /**
     * {@inheritDoc}
     */
    #[\Override]
    public function diff($items)
    {
        return $this->passthru(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    #[\Override]
    public function diffUsing($items, callable $callback)
    {
        return $this->passthru(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    #[\Override]
    public function diffAssoc($items)
    {
        return $this->passthru(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    #[\Override]
    public function diffAssocUsing($items, callable $callback)
    {
        return $this->passthru(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    #[\Override]
    public function diffKeys($items)
    {
        return $this->passthru(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    #[\Override]
    public function diffKeysUsing($items, callable $callback)
    {
        return $this->passthru(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    #[\Override]
    public function duplicates($callback = null, $strict = false)
    {
        return $this->passthru(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    #[\Override]
    public function duplicatesStrict($callback = null)
    {
        return $this->passthru(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    #[\Override]
    public function except($keys)
    {
        return $this->passthru(__FUNCTION__, func_get_args());
    }

    /**
     * Run a filter over each of the items.
     *
     * @param  (callable(TValue, TKey): bool)|null  $callback
     * @return static
     */
    public function filter(?callable $callback = null)
    {
        if (is_null($callback)) {
            $callback = fn ($value) => (bool) $value;
        }

        return new static(function () use ($callback) {
            foreach ($this as $key => $value) {
                if ($callback($value, $key)) {
                    yield $key => $value;
                }
            }
        });
    }

    /**
     * Get the first item from the enumerable passing the given truth test.
     *
     * @template TFirstDefault
     *
     * @param  (callable(TValue): bool)|null  $callback
     * @param  TFirstDefault|(\Closure(): TFirstDefault)  $default
     * @return TValue|TFirstDefault
     */
    public function first(?callable $callback = null, $default = null)
    {
        $iterator = $this->getIterator();

        if (is_null($callback)) {
            if (! $iterator->valid()) {
                return value($default);
            }

            return $iterator->current();
        }

        foreach ($iterator as $key => $value) {
            if ($callback($value, $key)) {
                return $value;
            }
        }

        return value($default);
    }

    /**
     * Get a flattened list of the items in the collection.
     *
     * @param  int  $depth
     * @return static<int, mixed>
     */
    public function flatten($depth = INF)
    {
        $instance = new static(function () use ($depth) {
            foreach ($this as $item) {
                if (! is_array($item) && ! $item instanceof Enumerable) {
                    yield $item;
                } elseif ($depth === 1) {
                    yield from $item;
                } else {
                    yield from (new static($item))->flatten($depth - 1);
                }
            }
        });

        return $instance->values();
    }

    /**
     * Flip the items in the collection.
     *
     * @return static<TValue, TKey>
     */
    public function flip()
    {
        return new static(function () {
            foreach ($this as $key => $value) {
                yield $value => $key;
            }
        });
    }

    /**
     * Get an item by key.
     *
     * @template TGetDefault
     *
     * @param  TKey|null  $key
     * @param  TGetDefault|(\Closure(): TGetDefault)  $default
     * @return TValue|TGetDefault
     */
    public function get($key, $default = null)
    {
        if (is_null($key)) {
            return;
        }

        foreach ($this as $outerKey => $outerValue) {
            if ($outerKey == $key) {
                return $outerValue;
            }
        }

        return value($default);
    }

    /**
     * {@inheritDoc}
     *
     * @template TGroupKey of array-key|\UnitEnum|\Stringable
     *
     * @param  (callable(TValue, TKey): TGroupKey)|array|string  $groupBy
     * @return static<
     *  ($groupBy is (array|string)
     *      ? array-key
     *      : (TGroupKey is \UnitEnum ? array-key : (TGroupKey is \Stringable ? string : TGroupKey))),
     *  static<($preserveKeys is true ? TKey : int), ($groupBy is array ? mixed : TValue)>
     * >
     */
    #[\Override]
    public function groupBy($groupBy, $preserveKeys = false)
    {
        return $this->passthru(__FUNCTION__, func_get_args());
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
        return new static(function () use ($keyBy) {
            $keyBy = $this->valueRetriever($keyBy);

            foreach ($this as $key => $item) {
                $resolvedKey = $keyBy($item, $key);

                if (is_object($resolvedKey)) {
                    $resolvedKey = (string) $resolvedKey;
                }

                yield $resolvedKey => $item;
            }
        });
    }

    /**
     * Determine if an item exists in the collection by key.
     *
     * @param  mixed  $key
     * @return bool
     */
    public function has($key)
    {
        $keys = array_flip(is_array($key) ? $key : func_get_args());
        $count = count($keys);

        foreach ($this as $key => $value) {
            if (array_key_exists($key, $keys) && --$count == 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if any of the keys exist in the collection.
     *
     * @param  mixed  $key
     * @return bool
     */
    public function hasAny($key)
    {
        $keys = array_flip(is_array($key) ? $key : func_get_args());

        foreach ($this as $key => $value) {
            if (array_key_exists($key, $keys)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Concatenate values of a given key as a string.
     *
     * @param  (callable(TValue, TKey): mixed)|string  $value
     * @param  string|null  $glue
     * @return string
     */
    public function implode($value, $glue = null)
    {
        return $this->collect()->implode(...func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    #[\Override]
    public function intersect($items)
    {
        return $this->passthru(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    #[\Override]
    public function intersectUsing($items, callable $callback)
    {
        return $this->passthru(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    #[\Override]
    public function intersectAssoc($items)
    {
        return $this->passthru(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    #[\Override]
    public function intersectAssocUsing($items, callable $callback)
    {
        return $this->passthru(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    #[\Override]
    public function intersectByKeys($items)
    {
        return $this->passthru(__FUNCTION__, func_get_args());
    }

    /**
     * Determine if the items are empty or not.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return ! $this->getIterator()->valid();
    }

    /**
     * Determine if the collection contains a single item.
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
     * @return bool
     *
     * @deprecated 12.50.0 Use the `hasMany()` method instead.
     */
    public function containsManyItems(): bool
    {
        return $this->hasMany();
    }

    /**
     * Join all items from the collection using a string. The final items can use a separate glue string.
     *
     * @param  string  $glue
     * @param  string  $finalGlue
     * @return string
     */
    public function join($glue, $finalGlue = '')
    {
        return $this->collect()->join(...func_get_args());
    }

    /**
     * Get the keys of the collection items.
     *
     * @return static<int, TKey>
     */
    public function keys()
    {
        return new static(function () {
            foreach ($this as $key => $value) {
                yield $key;
            }
        });
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
        $needle = $placeholder = new stdClass;

        foreach ($this as $key => $value) {
            if (is_null($callback) || $callback($value, $key)) {
                $needle = $value;
            }
        }

        return $needle === $placeholder ? value($default) : $needle;
    }

    /**
     * Get the values of a given key.
     *
     * @param  string|array<array-key, string>  $value
     * @param  string|null  $key
     * @return static<array-key, mixed>
     */
    public function pluck($value, $key = null)
    {
        return new static(function () use ($value, $key) {
            [$value, $key] = $this->explodePluckParameters($value, $key);

            foreach ($this as $item) {
                $itemValue = $value instanceof Closure
                    ? $value($item)
                    : data_get($item, $value);

                if (is_null($key)) {
                    yield $itemValue;
                } else {
                    $itemKey = $key instanceof Closure
                        ? $key($item)
                        : data_get($item, $key);

                    if (is_object($itemKey) && method_exists($itemKey, '__toString')) {
                        $itemKey = (string) $itemKey;
                    }

                    yield $itemKey => $itemValue;
                }
            }
        });
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
        return new static(function () use ($callback) {
            foreach ($this as $key => $value) {
                yield $key => $callback($value, $key);
            }
        });
    }

    /**
     * {@inheritDoc}
     */
    #[\Override]
    public function mapToDictionary(callable $callback)
    {
        return $this->passthru(__FUNCTION__, func_get_args());
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
        return new static(function () use ($callback) {
            foreach ($this as $key => $value) {
                yield from $callback($value, $key);
            }
        });
    }

    /**
     * {@inheritDoc}
     */
    #[\Override]
    public function merge($items)
    {
        return $this->passthru(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    #[\Override]
    public function mergeRecursive($items)
    {
        return $this->passthru(__FUNCTION__, func_get_args());
    }

    /**
     * Multiply the items in the collection by the multiplier.
     *
     * @param  int  $multiplier
     * @return static
     */
    public function multiply(int $multiplier)
    {
        return $this->passthru(__FUNCTION__, func_get_args());
    }

    /**
     * Create a collection by using this collection for keys and another for its values.
     *
     * @template TCombineValue
     *
     * @param  \IteratorAggregate<array-key, TCombineValue>|array<array-key, TCombineValue>|(callable(): \Generator<array-key, TCombineValue>)  $values
     * @return static<TValue, TCombineValue>
     */
    public function combine($values)
    {
        return new static(function () use ($values) {
            $values = $this->makeIterator($values);

            $errorMessage = 'Both parameters should have an equal number of elements';

            foreach ($this as $key) {
                if (! $values->valid()) {
                    trigger_error($errorMessage, E_USER_WARNING);

                    break;
                }

                yield $key => $values->current();

                $values->next();
            }

            if ($values->valid()) {
                trigger_error($errorMessage, E_USER_WARNING);
            }
        });
    }

    /**
     * {@inheritDoc}
     */
    #[\Override]
    public function union($items)
    {
        return $this->passthru(__FUNCTION__, func_get_args());
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

        return new static(function () use ($step, $offset) {
            $position = 0;

            foreach ($this->slice($offset) as $item) {
                if ($position % $step === 0) {
                    yield $item;
                }

                $position++;
            }
        });
    }

    /**
     * Get the items with the specified keys.
     *
     * @param  \Illuminate\Support\Enumerable<array-key, TKey>|array<array-key, TKey>|string  $keys
     * @return static
     */
    public function only($keys)
    {
        if ($keys instanceof Enumerable) {
            $keys = $keys->all();
        } elseif (! is_null($keys)) {
            $keys = is_array($keys) ? $keys : func_get_args();
        }

        return new static(function () use ($keys) {
            if (is_null($keys)) {
                yield from $this;
            } else {
                $keys = array_flip($keys);

                foreach ($this as $key => $value) {
                    if (array_key_exists($key, $keys)) {
                        yield $key => $value;

                        unset($keys[$key]);

                        if (empty($keys)) {
                            break;
                        }
                    }
                }
            }
        });
    }

    /**
     * Select specific values from the items within the collection.
     *
     * @param  \Illuminate\Support\Enumerable<array-key, TKey>|array<array-key, TKey>|string  $keys
     * @return static
     */
    public function select($keys)
    {
        if ($keys instanceof Enumerable) {
            $keys = $keys->all();
        } elseif (! is_null($keys)) {
            $keys = is_array($keys) ? $keys : func_get_args();
        }

        return new static(function () use ($keys) {
            if (is_null($keys)) {
                yield from $this;
            } else {
                foreach ($this as $item) {
                    $result = [];

                    foreach ($keys as $key) {
                        if (Arr::accessible($item) && Arr::exists($item, $key)) {
                            $result[$key] = $item[$key];
                        } elseif (is_object($item) && isset($item->{$key})) {
                            $result[$key] = $item->{$key};
                        }
                    }

                    yield $result;
                }
            }
        });
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
        return (new static(function () use ($source) {
            yield from $this;
            yield from $source;
        }))->values();
    }

    /**
     * Get one or a specified number of items randomly from the collection.
     *
     * @param  int|null  $number
     * @param  bool  $preserveKeys
     * @return static<int, TValue>|TValue
     *
     * @throws \InvalidArgumentException
     */
    public function random($number = null, $preserveKeys = false)
    {
        $result = $this->collect()->random(...func_get_args());

        return is_null($number) ? $result : new static($result);
    }

    /**
     * Replace the collection items with the given items.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<TKey, TValue>|iterable<TKey, TValue>  $items
     * @return static
     */
    public function replace($items)
    {
        return new static(function () use ($items) {
            $items = $this->getArrayableItems($items);

            foreach ($this as $key => $value) {
                if (array_key_exists($key, $items)) {
                    yield $key => $items[$key];

                    unset($items[$key]);
                } else {
                    yield $key => $value;
                }
            }

            foreach ($items as $key => $value) {
                yield $key => $value;
            }
        });
    }

    /**
     * {@inheritDoc}
     */
    #[\Override]
    public function replaceRecursive($items)
    {
        return $this->passthru(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    #[\Override]
    public function reverse()
    {
        return $this->passthru(__FUNCTION__, func_get_args());
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
        /** @var (callable(TValue,TKey): bool) $predicate */
        $predicate = $this->useAsCallable($value)
            ? $value
            : function ($item) use ($value, $strict) {
                return $strict ? $item === $value : $item == $value;
            };

        foreach ($this as $key => $item) {
            if ($predicate($item, $key)) {
                return $key;
            }
        }

        return false;
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
        $previous = null;

        /** @var (callable(TValue,TKey): bool) $predicate */
        $predicate = $this->useAsCallable($value)
            ? $value
            : function ($item) use ($value, $strict) {
                return $strict ? $item === $value : $item == $value;
            };

        foreach ($this as $key => $item) {
            if ($predicate($item, $key)) {
                return $previous;
            }

            $previous = $item;
        }

        return null;
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
        $found = false;

        /** @var (callable(TValue,TKey): bool) $predicate */
        $predicate = $this->useAsCallable($value)
            ? $value
            : function ($item) use ($value, $strict) {
                return $strict ? $item === $value : $item == $value;
            };

        foreach ($this as $key => $item) {
            if ($found) {
                return $item;
            }

            if ($predicate($item, $key)) {
                $found = true;
            }
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    #[\Override]
    public function shuffle()
    {
        return $this->passthru(__FUNCTION__, []);
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

        return new static(function () use ($size, $step) {
            $iterator = $this->getIterator();

            $chunk = [];

            while ($iterator->valid()) {
                $chunk[$iterator->key()] = $iterator->current();

                if (count($chunk) == $size) {
                    yield (new static($chunk))->tap(function () use (&$chunk, $step) {
                        $chunk = array_slice($chunk, $step, null, true);
                    });

                    // If the $step between chunks is bigger than each chunk's $size
                    // we will skip the extra items (which should never be in any
                    // chunk) before we continue to the next chunk in the loop.
                    if ($step > $size) {
                        $skip = $step - $size;

                        for ($i = 0; $i < $skip && $iterator->valid(); $i++) {
                            $iterator->next();
                        }
                    }
                }

                $iterator->next();
            }
        });
    }

    /**
     * Skip the first {$count} items.
     *
     * @param  int  $count
     * @return static
     */
    public function skip($count)
    {
        return new static(function () use ($count) {
            $iterator = $this->getIterator();

            while ($iterator->valid() && $count--) {
                $iterator->next();
            }

            while ($iterator->valid()) {
                yield $iterator->key() => $iterator->current();

                $iterator->next();
            }
        });
    }

    /**
     * Skip items in the collection until the given condition is met.
     *
     * @param  TValue|callable(TValue,TKey): bool  $value
     * @return static
     */
    public function skipUntil($value)
    {
        $callback = $this->useAsCallable($value) ? $value : $this->equality($value);

        return $this->skipWhile($this->negate($callback));
    }

    /**
     * Skip items in the collection while the given condition is met.
     *
     * @param  TValue|callable(TValue,TKey): bool  $value
     * @return static
     */
    public function skipWhile($value)
    {
        $callback = $this->useAsCallable($value) ? $value : $this->equality($value);

        return new static(function () use ($callback) {
            $iterator = $this->getIterator();

            while ($iterator->valid() && $callback($iterator->current(), $iterator->key())) {
                $iterator->next();
            }

            while ($iterator->valid()) {
                yield $iterator->key() => $iterator->current();

                $iterator->next();
            }
        });
    }

    /**
     * {@inheritDoc}
     */
    #[\Override]
    public function slice($offset, $length = null)
    {
        if ($offset < 0 || $length < 0) {
            return $this->passthru(__FUNCTION__, func_get_args());
        }

        $instance = $this->skip($offset);

        return is_null($length) ? $instance : $instance->take($length);
    }

    /**
     * {@inheritDoc}
     *
     * @throws \InvalidArgumentException
     */
    #[\Override]
    public function split($numberOfGroups)
    {
        if ($numberOfGroups < 1) {
            throw new InvalidArgumentException('Number of groups must be at least 1.');
        }

        return $this->passthru(__FUNCTION__, func_get_args());
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

        return $this
            ->unless($filter == null)
            ->filter($filter)
            ->take(2)
            ->collect()
            ->sole();
    }

    /**
     * Determine if the collection contains a single item or a single item matching the given criteria.
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
            ->take(2)
            ->count() === 1;
    }

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
    public function firstOrFail($key = null, $operator = null, $value = null)
    {
        $filter = func_num_args() > 1
            ? $this->operatorForWhere(...func_get_args())
            : $key;

        return $this
            ->unless($filter == null)
            ->filter($filter)
            ->take(1)
            ->collect()
            ->firstOrFail();
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
            return static::empty();
        }

        $add = match ($preserveKeys) {
            true => fn (array &$chunk, Traversable $iterator) => $chunk[$iterator->key()] = $iterator->current(),
            false => fn (array &$chunk, Traversable $iterator) => $chunk[] = $iterator->current(),
        };

        return new static(function () use ($size, $add) {
            $iterator = $this->getIterator();

            while ($iterator->valid()) {
                $chunk = [];

                while (true) {
                    $add($chunk, $iterator);

                    if (count($chunk) < $size) {
                        $iterator->next();

                        if (! $iterator->valid()) {
                            break;
                        }
                    } else {
                        break;
                    }
                }

                yield new static($chunk);

                $iterator->next();
            }
        });
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
     * Chunk the collection into chunks with a callback.
     *
     * @param  callable(TValue, TKey, Collection<TKey, TValue>): bool  $callback
     * @return static<int, static<TKey, TValue>>
     */
    public function chunkWhile(callable $callback)
    {
        return new static(function () use ($callback) {
            $iterator = $this->getIterator();

            $chunk = new Collection;

            if ($iterator->valid()) {
                $chunk[$iterator->key()] = $iterator->current();

                $iterator->next();
            }

            while ($iterator->valid()) {
                if (! $callback($iterator->current(), $iterator->key(), $chunk)) {
                    yield new static($chunk);

                    $chunk = new Collection;
                }

                $chunk[$iterator->key()] = $iterator->current();

                $iterator->next();
            }

            if ($chunk->isNotEmpty()) {
                yield new static($chunk);
            }
        });
    }

    /**
     * {@inheritDoc}
     */
    #[\Override]
    public function sort($callback = null)
    {
        return $this->passthru(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    #[\Override]
    public function sortDesc($options = SORT_REGULAR)
    {
        return $this->passthru(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    #[\Override]
    public function sortBy($callback, $options = SORT_REGULAR, $descending = false)
    {
        return $this->passthru(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    #[\Override]
    public function sortByDesc($callback, $options = SORT_REGULAR)
    {
        return $this->passthru(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    #[\Override]
    public function sortKeys($options = SORT_REGULAR, $descending = false)
    {
        return $this->passthru(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    #[\Override]
    public function sortKeysDesc($options = SORT_REGULAR)
    {
        return $this->passthru(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    #[\Override]
    public function sortKeysUsing(callable $callback)
    {
        return $this->passthru(__FUNCTION__, func_get_args());
    }

    /**
     * Take the first or last {$limit} items.
     *
     * @param  int  $limit
     * @return static<TKey, TValue>
     */
    public function take($limit)
    {
        if ($limit < 0) {
            return new static(function () use ($limit) {
                $limit = abs($limit);
                $ringBuffer = [];
                $position = 0;

                foreach ($this as $key => $value) {
                    $ringBuffer[$position] = [$key, $value];
                    $position = ($position + 1) % $limit;
                }

                for ($i = 0, $end = min($limit, count($ringBuffer)); $i < $end; $i++) {
                    $pointer = ($position + $i) % $limit;
                    yield $ringBuffer[$pointer][0] => $ringBuffer[$pointer][1];
                }
            });
        }

        return new static(function () use ($limit) {
            $iterator = $this->getIterator();

            while ($limit--) {
                if (! $iterator->valid()) {
                    break;
                }

                yield $iterator->key() => $iterator->current();

                if ($limit) {
                    $iterator->next();
                }
            }
        });
    }

    /**
     * Take items in the collection until the given condition is met.
     *
     * @param  TValue|callable(TValue,TKey): bool  $value
     * @return static<TKey, TValue>
     */
    public function takeUntil($value)
    {
        /** @var callable(TValue, TKey): bool $callback */
        $callback = $this->useAsCallable($value) ? $value : $this->equality($value);

        return new static(function () use ($callback) {
            foreach ($this as $key => $item) {
                if ($callback($item, $key)) {
                    break;
                }

                yield $key => $item;
            }
        });
    }

    /**
     * Take items in the collection until a given point in time, with an optional callback on timeout.
     *
     * @param  \DateTimeInterface  $timeout
     * @param  callable(TValue|null, TKey|null): mixed|null  $callback
     * @return static<TKey, TValue>
     */
    public function takeUntilTimeout(DateTimeInterface $timeout, ?callable $callback = null)
    {
        $timeout = $timeout->getTimestamp();

        return new static(function () use ($timeout, $callback) {
            if ($this->now() >= $timeout) {
                if ($callback) {
                    $callback(null, null);
                }

                return;
            }

            foreach ($this as $key => $value) {
                yield $key => $value;

                if ($this->now() >= $timeout) {
                    if ($callback) {
                        $callback($value, $key);
                    }

                    break;
                }
            }
        });
    }

    /**
     * Take items in the collection while the given condition is met.
     *
     * @param  TValue|callable(TValue,TKey): bool  $value
     * @return static<TKey, TValue>
     */
    public function takeWhile($value)
    {
        /** @var callable(TValue, TKey): bool $callback */
        $callback = $this->useAsCallable($value) ? $value : $this->equality($value);

        return $this->takeUntil(fn ($item, $key) => ! $callback($item, $key));
    }

    /**
     * Pass each item in the collection to the given callback, lazily.
     *
     * @param  callable(TValue, TKey): mixed  $callback
     * @return static<TKey, TValue>
     */
    public function tapEach(callable $callback)
    {
        return new static(function () use ($callback) {
            foreach ($this as $key => $value) {
                $callback($value, $key);

                yield $key => $value;
            }
        });
    }

    /**
     * Throttle the values, releasing them at most once per the given seconds.
     *
     * @return static<TKey, TValue>
     */
    public function throttle(float $seconds)
    {
        return new static(function () use ($seconds) {
            $microseconds = $seconds * 1_000_000;

            foreach ($this as $key => $value) {
                $fetchedAt = $this->preciseNow();

                yield $key => $value;

                $sleep = $microseconds - ($this->preciseNow() - $fetchedAt);

                $this->usleep((int) $sleep);
            }
        });
    }

    /**
     * Flatten a multi-dimensional associative array with dots.
     *
     * @param  int  $depth
     * @return static
     */
    public function dot($depth = INF)
    {
        return $this->passthru(__FUNCTION__, [$depth]);
    }

    /**
     * {@inheritDoc}
     */
    #[\Override]
    public function undot()
    {
        return $this->passthru(__FUNCTION__, []);
    }

    /**
     * Return only unique items from the collection array.
     *
     * @param  (callable(TValue, TKey): mixed)|string|null  $key
     * @param  bool  $strict
     * @return static<TKey, TValue>
     */
    public function unique($key = null, $strict = false)
    {
        $callback = $this->valueRetriever($key);

        return new static(function () use ($callback, $strict) {
            $exists = [];

            foreach ($this as $key => $item) {
                if (! in_array($id = $callback($item, $key), $exists, $strict)) {
                    yield $key => $item;

                    $exists[] = $id;
                }
            }
        });
    }

    /**
     * Reset the keys on the underlying array.
     *
     * @return static<int, TValue>
     */
    public function values()
    {
        return new static(function () {
            foreach ($this as $item) {
                yield $item;
            }
        });
    }

    /**
     * Run the given callback every time the interval has passed.
     *
     * @return static<TKey, TValue>
     */
    public function withHeartbeat(DateInterval|int $interval, callable $callback)
    {
        $seconds = is_int($interval) ? $interval : $this->intervalSeconds($interval);

        return new static(function () use ($seconds, $callback) {
            $start = $this->now();

            foreach ($this as $key => $value) {
                $now = $this->now();

                if (($now - $start) >= $seconds) {
                    $callback();

                    $start = $now;
                }

                yield $key => $value;
            }
        });
    }

    /**
     * Get the total seconds from the given interval.
     */
    protected function intervalSeconds(DateInterval $interval): int
    {
        $start = new DateTimeImmutable();

        return $start->add($interval)->getTimestamp() - $start->getTimestamp();
    }

    /**
     * Zip the collection together with one or more arrays.
     *
     * e.g. new LazyCollection([1, 2, 3])->zip([4, 5, 6]);
     *      => [[1, 4], [2, 5], [3, 6]]
     *
     * @template TZipValue
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<array-key, TZipValue>|iterable<array-key, TZipValue>  ...$items
     * @return static<int, static<int, TValue|TZipValue>>
     */
    public function zip($items)
    {
        $iterables = func_get_args();

        return new static(function () use ($iterables) {
            $iterators = (new Collection($iterables))
                ->map(fn ($iterable) => $this->makeIterator($iterable))
                ->prepend($this->getIterator());

            while ($iterators->contains->valid()) {
                yield new static($iterators->map->current());

                $iterators->each->next();
            }
        });
    }

    /**
     * {@inheritDoc}
     */
    #[\Override]
    public function pad($size, $value)
    {
        if ($size < 0) {
            return $this->passthru(__FUNCTION__, func_get_args());
        }

        return new static(function () use ($size, $value) {
            $yielded = 0;

            foreach ($this as $index => $item) {
                yield $index => $item;

                $yielded++;
            }

            while ($yielded++ < $size) {
                yield $value;
            }
        });
    }

    /**
     * Get the values iterator.
     *
     * @return \Traversable<TKey, TValue>
     */
    public function getIterator(): Traversable
    {
        return $this->makeIterator($this->source);
    }

    /**
     * Count the number of items in the collection.
     *
     * @return int
     */
    public function count(): int
    {
        if (is_array($this->source)) {
            return count($this->source);
        }

        return iterator_count($this->getIterator());
    }

    /**
     * Make an iterator from the given source.
     *
     * @template TIteratorKey of array-key
     * @template TIteratorValue
     *
     * @param  \IteratorAggregate<TIteratorKey, TIteratorValue>|array<TIteratorKey, TIteratorValue>|(callable(): \Generator<TIteratorKey, TIteratorValue>)  $source
     * @return \Traversable<TIteratorKey, TIteratorValue>
     */
    protected function makeIterator($source)
    {
        if ($source instanceof IteratorAggregate) {
            return $source->getIterator();
        }

        if (is_array($source)) {
            return new ArrayIterator($source);
        }

        if (is_callable($source)) {
            $maybeTraversable = $source();

            return $maybeTraversable instanceof Traversable
                ? $maybeTraversable
                : new ArrayIterator(Arr::wrap($maybeTraversable));
        }

        return new ArrayIterator((array) $source);
    }

    /**
     * Explode the "value" and "key" arguments passed to "pluck".
     *
     * @param  string|string[]  $value
     * @param  string|string[]|null  $key
     * @return array{string[],string[]|null}
     */
    protected function explodePluckParameters($value, $key)
    {
        $value = is_string($value) ? explode('.', $value) : $value;

        $key = is_null($key) || is_array($key) || $key instanceof Closure ? $key : explode('.', $key);

        return [$value, $key];
    }

    /**
     * Pass this lazy collection through a method on the collection class.
     *
     * @param  string  $method
     * @param  array<mixed>  $params
     * @return static
     */
    protected function passthru($method, array $params)
    {
        return new static(function () use ($method, $params) {
            yield from $this->collect()->$method(...$params);
        });
    }

    /**
     * Get the current time.
     *
     * @return int
     */
    protected function now()
    {
        return class_exists(Carbon::class)
            ? Carbon::now()->timestamp
            : time();
    }

    /**
     * Get the precise current time.
     *
     * @return float
     */
    protected function preciseNow()
    {
        return class_exists(Carbon::class)
            ? Carbon::now()->getPreciseTimestamp()
            : microtime(true) * 1_000_000;
    }

    /**
     * Sleep for the given amount of microseconds.
     *
     * @return void
     */
    protected function usleep(int $microseconds)
    {
        if ($microseconds <= 0) {
            return;
        }

        class_exists(Sleep::class)
            ? Sleep::usleep($microseconds)
            : usleep($microseconds);
    }
}
