<?php namespace Illuminate\Cache;

use Closure;
use DateTime;
use ArrayAccess;
use Carbon\Carbon;

class Repository implements ArrayAccess {

	/**
	 * The cache store implementation.
	 *
	 * @var \Illuminate\Cache\StoreInterface
	 */
	protected $store;

	/**
	 * The default number of minutes to store items.
	 *
	 * @var int
	 */
	protected $default = 60;

	/**
	 * An array of registered Cache macros.
	 *
	 * @var array
	 */
	protected $macros = array();

	/**
	 * Create a new cache repository instance.
	 *
	 * @param  \Illuminate\Cache\StoreInterface  $store
	 */
	public function __construct(StoreInterface $store)
	{
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
		$value = $this->store->get($key);

		return ! is_null($value) ? $value : value($default);
	}

	/**
	 * Store an item in the cache.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @param  \DateTime|int  $minutes
	 * @return void
	 */
	public function put($key, $value, $minutes)
	{
		$minutes = $this->getMinutes($minutes);

		$this->store->put($key, $value, $minutes);
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
	 * Get an item from the cache, or store the default value.
	 *
	 * @param  string  $key
	 * @param  \DateTime|int  $minutes
	 * @param  Closure  $callback
	 * @return mixed
	 */
	public function remember($key, $minutes, Closure $callback)
	{
		// If the item exists in the cache we will just return this immediately
		// otherwise we will execute the given Closure and cache the result
		// of that execution for the given number of minutes in storage.
		if ( ! is_null($value = $this->get($key)))
		{
			return $value;
		}

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
		if ( ! is_null($value = $this->get($key)))
		{
			return $value;
		}

		$this->forever($key, $value = $callback());

		return $value;
	}

	/**
	 * Get the default cache time.
	 *
	 * @return int
	 */
	public function getDefaultCacheTime()
	{
		return $this->default;
	}

	/**
	 * Set the default cache time in minutes.
	 *
	 * @param  int   $minutes
	 * @return void
	 */
	public function setDefaultCacheTime($minutes)
	{
		$this->default = $minutes;
	}

	/**
	 * Get the cache store implementation.
	 *
	 * @return \Illuminate\Cache\StoreInterface
	 */
	public function getStore()
	{
		return $this->store;
	}

	/**
	 * Determine if a cached value exists.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public function offsetExists($key)
	{
		return $this->has($key);
	}

	/**
	 * Retrieve an item from the cache by key.
	 *
	 * @param  string  $key
	 * @return mixed
	 */
	public function offsetGet($key)
	{
		return $this->get($key);
	}

	/**
	 * Store an item in the cache for the default time.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 */
	public function offsetSet($key, $value)
	{
		$this->put($key, $value, $this->default);
	}

	/**
	 * Remove an item from the cache.
	 *
	 * @param  string  $key
	 * @return void
	 */
	public function offsetUnset($key)
	{
		return $this->forget($key);
	}

	/**
	 * Calculate the number of minutes with the given duration.
	 *
	 * @param  \DateTime|int  $duration
	 * @return int
	 */
	protected function getMinutes($duration)
	{
		if ($duration instanceof DateTime)
		{
			$duration = Carbon::instance($duration);

			return max(0, Carbon::now()->diffInMinutes($duration, false));
		}
		else
		{
			return is_string($duration) ? intval($duration) : $duration;
		}
	}

	/**
	 * Register a macro with the Cache class.
	 *
	 * @param  string    $name
	 * @param  callable  $callback
	 * @return void
	 */
	public function macro($name, $callback)
	{
		$this->macros[$name] = $callback;
	}

	/**
	 * Handle dynamic calls into macros or pass missing methods to the store.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return mixed
	 */
	public function __call($method, $parameters)
	{
		if (isset($this->macros[$method]))
		{
			return call_user_func_array($this->macros[$method], $parameters);
		}
		else
		{
			return call_user_func_array(array($this->store, $method), $parameters);
		}
	}

}
