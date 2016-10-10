<?php namespace Illuminate\Support;

use Closure;
use Countable;
use ArrayAccess;
use ArrayIterator;
use CachingIterator;
use IteratorAggregate;
use Illuminate\Support\Contracts\JsonableInterface;
use Illuminate\Support\Contracts\ArrayableInterface;

class Collection implements ArrayAccess, ArrayableInterface, Countable, IteratorAggregate, JsonableInterface {

	/**
	 * The items contained in the collection.
	 *
	 * @var array
	 */
	protected $items = array();

	/**
	 * Create a new collection.
	 *
	 * @param  array  $items
	 * @return void
	 */
	public function __construct(array $items = array())
	{
		$this->items = $items;
	}

	/**
	 * Create a new collection instance if the value isn't one already.
	 *
	 * @param  mixed  $items
	 * @return \Illuminate\Support\Collection
	 */
	public static function make($items)
	{
		if (is_null($items)) return new static;

		if ($items instanceof Collection) return $items;

		return new static(is_array($items) ? $items : array($items));
	}

	/**
	 * Get all of the items in the collection.
	 *
	 * @return array
	 */
	public function all()
	{
		return $this->items;
	}

	/**
	 * Collapse the collection items into a single array.
	 *
	 * @return \Illuminate\Support\Collection
	 */
	public function collapse()
	{
		$results = array();

		foreach ($this->items as $values)
		{
			$results = array_merge($results, $values);
		}

		return new static($results);
	}

	/**
	 * Diff the collection with the given items.
	 *
	 * @param  \Illuminate\Support\Collection|\Illuminate\Support\Contracts\ArrayableInterface|array  $items
	 * @return \Illuminate\Support\Collection
	 */
	public function diff($items)
	{
		return new static(array_diff($this->items, $this->getArrayableItems($items)));
	}

	/**
	 * Execute a callback over each item.
	 *
	 * @param  Closure  $callback
	 * @return \Illuminate\Support\Collection
	 */
	public function each(Closure $callback)
	{
		array_map($callback, $this->items);

		return $this;
	}

	/**
	 * Fetch a nested element of the collection.
	 *
	 * @param  string  $key
	 * @return \Illuminate\Support\Collection
	 */
	public function fetch($key)
	{
		return new static(array_fetch($this->items, $key));
	}

	/**
	 * Run a filter over each of the items.
	 *
	 * @param  Closure  $callback
	 * @return \Illuminate\Support\Collection
	 */
	public function filter(Closure $callback)
	{
		return new static(array_filter($this->items, $callback));
	}

	/**
	 * Get the first item from the collection.
	 *
	 * @param  \Closure   $callback
	 * @param  mixed      $default
	 * @return mixed|null
	 */
	public function first(Closure $callback = null, $default = null)
	{
		if (is_null($callback))
		{
			return count($this->items) > 0 ? reset($this->items) : null;
		}
		else
		{
			return array_first($this->items, $callback, $default);
		}
	}

	/**
	 * Get a flattened array of the items in the collection.
	 *
	 * @return array
	 */
	public function flatten()
	{
		return new static(array_flatten($this->items));
	}

	/**
	 * Remove an item from the collection by key.
	 *
	 * @param  mixed  $key
	 * @return void
	 */
	public function forget($key)
	{
		unset($this->items[$key]);
	}

	/**
	 * Get an item from the collection by key.
	 *
	 * @param  mixed  $key
	 * @param  mixed  $default
	 * @return mixed
	 */
	public function get($key, $default = null)
	{
		if (array_key_exists($key, $this->items))
		{
			return $this->items[$key];
		}

		return value($default);
	}

	/**
	 * Group an associative array by a field or Closure value.
	 *
	 * @param  callable|string  $groupBy
	 * @return \Illuminate\Support\Collection
	 */
	public function groupBy($groupBy)
	{
		$results = array();

		foreach ($this->items as $key => $value)
		{
			$key = is_callable($groupBy) ? $groupBy($value, $key) : data_get($value, $groupBy);

			$results[$key][] = $value;
		}

		return new static($results);
	}

	/**
	 * Determine if an item exists in the collection by key.
	 *
	 * @param  mixed  $key
	 * @return bool
	 */
	public function has($key)
	{
		return array_key_exists($key, $this->items);
	}

	/**
	 * Concatenate values of a given key as a string.
	 *
	 * @param  string  $value
	 * @param  string  $glue
	 * @return string
	 */
	public function implode($value, $glue = null)
	{
		if (is_null($glue)) return implode($this->lists($value));

		return implode($glue, $this->lists($value));
	}

	/**
	 * Intersect the collection with the given items.
	 *
 	 * @param  \Illuminate\Support\Collection|\Illuminate\Support\Contracts\ArrayableInterface|array  $items
	 * @return \Illuminate\Support\Collection
	 */
	public function intersect($items)
	{
		return new static(array_intersect($this->items, $this->getArrayableItems($items)));
	}

	/**
	 * Determine if the collection is empty or not.
	 *
	 * @return bool
	 */
	public function isEmpty()
	{
		return empty($this->items);
	}

	/**
	* Get the last item from the collection.
	*
	* @return mixed|null
	*/
	public function last()
	{
		return count($this->items) > 0 ? end($this->items) : null;
	}

	/**
	 * Get an array with the values of a given key.
	 *
	 * @param  string  $value
	 * @param  string  $key
	 * @return array
	 */
	public function lists($value, $key = null)
	{
		return array_pluck($this->items, $value, $key);
	}

	/**
	 * Run a map over each of the items.
	 *
	 * @param  Closure  $callback
	 * @return \Illuminate\Support\Collection
	 */
	public function map(Closure $callback)
	{
		return new static(array_map($callback, $this->items, array_keys($this->items)));
	}

	/**
	 * Merge the collection with the given items.
	 *
	 * @param  \Illuminate\Support\Collection|\Illuminate\Support\Contracts\ArrayableInterface|array  $items
	 * @return \Illuminate\Support\Collection
	 */
	public function merge($items)
	{
		return new static(array_merge($this->items, $this->getArrayableItems($items)));
	}

	/**
	 * Get and remove the last item from the collection.
	 *
	 * @return mixed|null
	 */
	public function pop()
	{
		return array_pop($this->items);
	}

	/**
	 * Push an item onto the beginning of the collection.
	 *
	 * @param  mixed  $value
	 * @return void
	 */
	public function prepend($value)
	{
		array_unshift($this->items, $value);
	}

	/**
	 * Push an item onto the end of the collection.
	 *
	 * @param  mixed  $value
	 * @return void
	 */
	public function push($value)
	{
		$this->items[] = $value;
	}

	/**
	 * Pulls an item from the collection.
	 *
	 * @param  mixed  $key
	 * @param  mixed  $default
	 * @return mixed
	 */
	public function pull($key, $default = null)
	{
		return array_pull($this->items, $key, $default);
	}

	/**
	 * Put an item in the collection by key.
	 *
	 * @param  mixed  $key
	 * @param  mixed  $value
	 * @return void
	 */
	public function put($key, $value)
	{
		$this->items[$key] = $value;
	}

	/**
	 * Reduce the collection to a single value.
	 *
	 * @param  callable  $callback
	 * @param  mixed  $initial
	 * @return mixed
	 */
	public function reduce($callback, $initial = null)
	{
		return array_reduce($this->items, $callback, $initial);
	}

    /**
     * Get one or more items randomly from the collection.
     *
     * @param  int $amount
     * @return mixed
     */
    public function random($amount = 1)
    {
        $keys = array_rand($this->items, $amount);

        return is_array($keys) ? array_intersect_key($this->items, array_flip($keys)) : $this->items[$keys];
    }

	/**
	 * Reverse items order.
	 *
	 * @return \Illuminate\Support\Collection
	 */
	public function reverse()
	{
		return new static(array_reverse($this->items));
	}

	/**
	 * Get and remove the first item from the collection.
	 *
	 * @return mixed|null
	 */
	public function shift()
	{
		return array_shift($this->items);
	}

	/**
	 * Slice the underlying collection array.
	 *
	 * @param  int   $offset
	 * @param  int   $length
	 * @param  bool  $preserveKeys
	 * @return \Illuminate\Support\Collection
	 */
	public function slice($offset, $length = null, $preserveKeys = false)
	{
		return new static(array_slice($this->items, $offset, $length, $preserveKeys));
	}

	/**
	 * Chunk the underlying collection array.
	 *
	 * @param  int $size
	 * @param  bool  $preserveKeys
	 * @return \Illuminate\Support\Collection
	 */
	public function chunk($size, $preserveKeys = false)
	{
		$chunks = new static;

		foreach (array_chunk($this->items, $size, $preserveKeys) as $chunk)
		{
			$chunks->push(new static($chunk));
		}

		return $chunks;
	}

	/**
	 * Sort through each item with a callback.
	 *
	 * @param  Closure  $callback
	 * @return \Illuminate\Support\Collection
	 */
	public function sort(Closure $callback)
	{
		uasort($this->items, $callback);

		return $this;
	}

	/**
	 * Sort the collection using the given Closure.
	 *
	 * @param  \Closure|string  $callback
	 * @param  int              $options
	 * @param  bool             $descending
	 * @return \Illuminate\Support\Collection
	 */
	public function sortBy($callback, $options = SORT_REGULAR, $descending = false)
	{
		$results = array();

		if (is_string($callback)) $callback =
                          $this->valueRetriever($callback);

		// First we will loop through the items and get the comparator from a callback
		// function which we were given. Then, we will sort the returned values and
		// and grab the corresponding values for the sorted keys from this array.
		foreach ($this->items as $key => $value)
		{
			$results[$key] = $callback($value);
		}

		$descending ? arsort($results, $options)
                    : asort($results, $options);

		// Once we have sorted all of the keys in the array, we will loop through them
		// and grab the corresponding model so we can set the underlying items list
		// to the sorted version. Then we'll just return the collection instance.
		foreach (array_keys($results) as $key)
		{
			$results[$key] = $this->items[$key];
		}

		$this->items = $results;

		return $this;
	}

	/**
	 * Sort the collection in descending order using the given Closure.
	 *
	 * @param  \Closure|string  $callback
	 * @param  int              $options
	 * @return \Illuminate\Support\Collection
	 */
	public function sortByDesc($callback, $options = SORT_REGULAR)
	{
		return $this->sortBy($callback, $options, true);
	}

	/**
	 * Splice portion of the underlying collection array.
	 *
	 * @param  int    $offset
	 * @param  int    $length
	 * @param  mixed  $replacement
	 * @return \Illuminate\Support\Collection
	 */
	public function splice($offset, $length = 0, $replacement = array())
	{
		return new static(array_splice($this->items, $offset, $length, $replacement));
	}

	/**
	 * Get the sum of the given values.
	 *
	 * @param  \Closure  $callback
	 * @param  string  $callback
	 * @return mixed
	 */
	public function sum($callback)
	{
		if (is_string($callback))
		{
			$callback = $this->valueRetriever($callback);
		}

		return $this->reduce(function($result, $item) use ($callback)
		{
			return $result += $callback($item);

		}, 0);
	}

	/**
	 * Take the first or last {$limit} items.
	 *
	 * @param  int  $limit
	 * @return \Illuminate\Support\Collection
	 */
	public function take($limit = null)
	{
		if ($limit < 0) return $this->slice($limit, abs($limit));

		return $this->slice(0, $limit);
	}

	/**
	 * Transform each item in the collection using a callback.
	 *
	 * @param  Closure  $callback
	 * @return \Illuminate\Support\Collection
	 */
	public function transform(Closure $callback)
	{
		$this->items = array_map($callback, $this->items);

		return $this;
	}

	/**
	 * Return only unique items from the collection array.
	 *
	 * @return \Illuminate\Support\Collection
	 */
	public function unique()
	{
		return new static(array_unique($this->items));
	}

	/**
	 * Reset the keys on the underlying array.
	 *
	 * @return \Illuminate\Support\Collection
	 */
	public function values()
	{
		$this->items = array_values($this->items);

		return $this;
	}

	/**
	 * Get a value retrieving callback.
	 *
	 * @param  string  $value
	 * @return \Closure
	 */
	protected function valueRetriever($value)
	{
		return function($item) use ($value)
		{
			return data_get($item, $value);
		};
	}

	/**
	 * Get the collection of items as a plain array.
	 *
	 * @return array
	 */
	public function toArray()
	{
		return array_map(function($value)
		{
			return $value instanceof ArrayableInterface ? $value->toArray() : $value;

		}, $this->items);
	}

	/**
	 * Get the collection of items as JSON.
	 *
	 * @param  int  $options
	 * @return string
	 */
	public function toJson($options = 0)
	{
		return json_encode($this->toArray(), $options);
	}

	/**
	 * Get an iterator for the items.
	 *
	 * @return ArrayIterator
	 */
	public function getIterator()
	{
		return new ArrayIterator($this->items);
	}

	/**
	 * Get a CachingIterator instance.
	 *
	 * @return \CachingIterator
	 */
	public function getCachingIterator($flags = CachingIterator::CALL_TOSTRING)
	{
		return new CachingIterator($this->getIterator(), $flags);
	}

	/**
	 * Count the number of items in the collection.
	 *
	 * @return int
	 */
	public function count()
	{
		return count($this->items);
	}

	/**
	 * Determine if an item exists at an offset.
	 *
	 * @param  mixed  $key
	 * @return bool
	 */
	public function offsetExists($key)
	{
		return array_key_exists($key, $this->items);
	}

	/**
	 * Get an item at a given offset.
	 *
	 * @param  mixed  $key
	 * @return mixed
	 */
	public function offsetGet($key)
	{
		return $this->items[$key];
	}

	/**
	 * Set the item at a given offset.
	 *
	 * @param  mixed  $key
	 * @param  mixed  $value
	 * @return void
	 */
	public function offsetSet($key, $value)
	{
		if (is_null($key))
		{
			$this->items[] = $value;
		}
		else
		{
			$this->items[$key] = $value;
		}
	}

	/**
	 * Unset the item at a given offset.
	 *
	 * @param  string  $key
	 * @return void
	 */
	public function offsetUnset($key)
	{
		unset($this->items[$key]);
	}

	/**
	 * Convert the collection to its string representation.
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->toJson();
	}

	/**
	 * Results array of items from Collection or ArrayableInterface.
	 *
  	 * @param  \Illuminate\Support\Collection|\Illuminate\Support\Contracts\ArrayableInterface|array  $items
	 * @return array
	 */
	private function getArrayableItems($items)
	{
		if ($items instanceof Collection)
		{
			$items = $items->all();
		}
		elseif ($items instanceof ArrayableInterface)
		{
			$items = $items->toArray();
		}

		return $items;
	}

}
