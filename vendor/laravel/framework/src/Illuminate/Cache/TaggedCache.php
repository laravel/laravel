<?php namespace Illuminate\Cache;

use Closure;

class TaggedCache implements StoreInterface {

	/**
	 * The cache store implementation.
	 *
	 * @var \Illuminate\Cache\StoreInterface
	 */
	protected $store;

	/**
	 * The tag set instance.
	 *
	 * @var \Illuminate\Cache\TagSet
	 */
	protected $tags;

	/**
	 * Create a new tagged cache instance.
	 *
	 * @param  \Illuminate\Cache\StoreInterface  $store
	 * @param  \Illuminate\Cache\TagSet  $tags
	 * @return void
	 */
	public function __construct(StoreInterface $store, TagSet $tags)
	{
		$this->tags = $tags;
		$this->store = $store;
	}

	/**
	 * Determine if an item exists in the cache.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public function has($key)
	{
		return ! is_null($this->get($key));
	}

	/**
	 * Retrieve an item from the cache by key.
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return mixed
	 */
	public function get($key, $default = null)
	{
		$value = $this->store->get($this->taggedItemKey($key));

		return ! is_null($value) ? $value : value($default);
	}

	/**
	 * Store an item in the cache for a given number of minutes.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @param  int     $minutes
	 * @return void
	 */
	public function put($key, $value, $minutes)
	{
		return $this->store->put($this->taggedItemKey($key), $value, $minutes);
	}

	/**
	 * Store an item in the cache if the key does not exist.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @param  \DateTime|int  $minutes
	 * @return bool
	 */
	public function add($key, $value, $minutes)
	{
		if (is_null($this->get($key)))
		{
			$this->put($key, $value, $minutes); return true;
		}

		return false;
	}

	/**
	 * Increment the value of an item in the cache.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 */
	public function increment($key, $value = 1)
	{
		$this->store->increment($this->taggedItemKey($key), $value);
	}

	/**
	 * Increment the value of an item in the cache.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 */
	public function decrement($key, $value = 1)
	{
		$this->store->decrement($this->taggedItemKey($key), $value);
	}

	/**
	 * Store an item in the cache indefinitely.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 */
	public function forever($key, $value)
	{
		$this->store->forever($this->taggedItemKey($key), $value);
	}

	/**
	 * Remove an item from the cache.
	 *
	 * @param  string  $key
	 * @return void
	 */
	public function forget($key)
	{
		$this->store->forget($this->taggedItemKey($key));
	}

	/**
	 * Remove all items from the cache.
	 *
	 * @return void
	 */
	public function flush()
	{
		$this->tags->reset();
	}

	/**
	 * Get an item from the cache, or store the default value.
	 *
	 * @param  string   $key
	 * @param  int      $minutes
	 * @param  Closure  $callback
	 * @return mixed
	 */
	public function remember($key, $minutes, Closure $callback)
	{
		// If the item exists in the cache we will just return this immediately
		// otherwise we will execute the given Closure and cache the result
		// of that execution for the given number of minutes in storage.
		if ($this->has($key)) return $this->get($key);

		$this->put($key, $value = $callback(), $minutes);

		return $value;
	}

	/**
	 * Get an item from the cache, or store the default value forever.
	 *
	 * @param  string   $key
	 * @param  Closure  $callback
	 * @return mixed
	 */
	public function sear($key, Closure $callback)
	{
		return $this->rememberForever($key, $callback);
	}

	/**
	 * Get an item from the cache, or store the default value forever.
	 *
	 * @param  string   $key
	 * @param  Closure  $callback
	 * @return mixed
	 */
	public function rememberForever($key, Closure $callback)
	{
		// If the item exists in the cache we will just return this immediately
		// otherwise we will execute the given Closure and cache the result
		// of that execution for the given number of minutes. It's easy.
		if ($this->has($key)) return $this->get($key);

		$this->forever($key, $value = $callback());

		return $value;
	}

	/**
	 * Get a fully qualified key for a tagged item.
	 *
	 * @param  string  $key
	 * @return string
	 */
	public function taggedItemKey($key)
	{
		return $this->getPrefix().sha1($this->tags->getNamespace()).':'.$key;
	}

	/**
	 * Get the cache key prefix.
	 *
	 * @return string
	 */
	public function getPrefix()
	{
		return $this->store->getPrefix();
	}

}
