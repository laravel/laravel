<?php namespace Illuminate\Database\Eloquent;

use Illuminate\Support\Collection as BaseCollection;

class Collection extends BaseCollection {

	/**
	 * Find a model in the collection by key.
	 *
	 * @param  mixed  $key
	 * @param  mixed  $default
	 * @return \Illuminate\Database\Eloquent\Model
	 */
	public function find($key, $default = null)
	{
		if ($key instanceof Model)
		{
			$key = $key->getKey();
		}

		return array_first($this->items, function($itemKey, $model) use ($key)
		{
			return $model->getKey() == $key;

		}, $default);
	}

	/**
	 * Load a set of relationships onto the collection.
	 *
	 * @param  dynamic  $relations
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function load($relations)
	{
		if (count($this->items) > 0)
		{
			if (is_string($relations)) $relations = func_get_args();

			$query = $this->first()->newQuery()->with($relations);

			$this->items = $query->eagerLoadRelations($this->items);
		}

		return $this;
	}

	/**
	 * Add an item to the collection.
	 *
	 * @param  mixed  $item
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function add($item)
	{
		$this->items[] = $item;

		return $this;
	}

	/**
	 * Determine if a key exists in the collection.
	 *
	 * @param  mixed  $key
	 * @return bool
	 */
	public function contains($key)
	{
		return ! is_null($this->find($key));
	}

	/**
	 * Fetch a nested element of the collection.
	 *
	 * @param  string  $key
	 * @return \Illuminate\Support\Collection
	 */
	public function fetch($key)
	{
		return new static(array_fetch($this->toArray(), $key));
	}

	/**
	 * Get the max value of a given key.
	 *
	 * @param  string  $key
	 * @return mixed
	 */
	public function max($key)
	{
		return $this->reduce(function($result, $item) use ($key)
		{
			return (is_null($result) || $item->{$key} > $result) ? $item->{$key} : $result;
		});
	}

	/**
	 * Get the min value of a given key.
	 *
	 * @param  string  $key
	 * @return mixed
	 */
	public function min($key)
	{
		return $this->reduce(function($result, $item) use ($key)
		{
			return (is_null($result) || $item->{$key} < $result) ? $item->{$key} : $result;
		});
	}

	/**
	 * Get the array of primary keys
	 *
	 * @return array
	 */
	public function modelKeys()
	{
		return array_map(function($m) { return $m->getKey(); }, $this->items);
	}

	/**
	 * Merge the collection with the given items.
	 *
	 * @param  \Illuminate\Support\Collection|\Illuminate\Support\Contracts\ArrayableInterface|array  $items
	 * @return \Illuminate\Support\Collection
	 */
	public function merge($collection)
	{
		$dictionary = $this->getDictionary();

		foreach ($collection as $item)
		{
			$dictionary[$item->getKey()] = $item;
		}

		return new static(array_values($dictionary));
	}

	/**
	 * Diff the collection with the given items.
	 *
	 * @param  \Illuminate\Support\Collection|\Illuminate\Support\Contracts\ArrayableInterface|array  $items
	 * @return \Illuminate\Support\Collection
	 */
	public function diff($collection)
	{
		$diff = new static;

		$dictionary = $this->getDictionary($collection);

		foreach ($this->items as $item)
		{
			if ( ! isset($dictionary[$item->getKey()]))
			{
				$diff->add($item);
			}
		}

		return $diff;
	}

	/**
	 * Intersect the collection with the given items.
	 *
 	 * @param  \Illuminate\Support\Collection|\Illuminate\Support\Contracts\ArrayableInterface|array  $items
	 * @return \Illuminate\Support\Collection
	 */
	public function intersect($collection)
	{
		$intersect = new static;

		$dictionary = $this->getDictionary($collection);

		foreach ($this->items as $item)
		{
			if (isset($dictionary[$item->getKey()]))
			{
				$intersect->add($item);
			}
		}

		return $intersect;
	}

	/**
	 * Return only unique items from the collection.
	 *
	 * @return \Illuminate\Support\Collection
	 */
	public function unique()
	{
		$dictionary = $this->getDictionary();

		return new static(array_values($dictionary));
	}

	/**
	 * Returns only the models from the collection with the specified keys.
	 *
	 * @param  mixed  $keys
	 * @return \Illuminate\Support\Collection
	 */
	public function only($keys)
	{
		$dictionary = array_only($this->getDictionary($this), $keys);

		return new static(array_values($dictionary));
	}

	/**
	 * Returns all models in the collection except the models with specified keys.
	 *
	 * @param  mixed  $keys
	 * @return \Illuminate\Support\Collection
	 */
	public function except($keys)
	{
	    $dictionary = array_except($this->getDictionary($this), $keys);

	    return new static(array_values($dictionary));
	}

	/**
	 * Get a dictionary keyed by primary keys.
	 *
	 * @param  \Illuminate\Support\Collection  $collection
	 * @return array
	 */
	public function getDictionary($collection = null)
	{
		$collection = $collection ?: $this;

		$dictionary = array();

		foreach ($collection as $value)
		{
			$dictionary[$value->getKey()] = $value;
		}

		return $dictionary;
	}

	/**
	 * Get a base Support collection instance from this collection.
	 *
	 * @return \Illuminate\Support\Collection
	 */
	public function toBase()
	{
		return new BaseCollection($this->items);
	}

}
