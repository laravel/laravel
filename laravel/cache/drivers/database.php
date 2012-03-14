<?php namespace Laravel\Cache\Drivers;

use Laravel\Config;
use Laravel\Database as DB;
use Laravel\Database\Connection;

class Database extends Driver {

	/**
	 * The cache key from the cache configuration file.
	 *
	 * @var string
	 */
	protected $key;

	/**
	 * Create a new database cache driver instance.
	 *
	 * @param  string  $key
	 * @return void
	 */
	public function __construct($key)
	{
		$this->key = $key;
	}

	/**
	 * Determine if an item exists in the cache.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public function has($key)
	{
		return ( ! is_null($this->get($key)));
	}

	/**
	 * Retrieve an item from the cache driver.
	 *
	 * @param  string  $key
	 * @return mixed
	 */
	protected function retrieve($key)
	{
		$cache = $this->table()->where('key', '=', $this->key.$key)->first();

		if ( ! is_null($cache))
		{
			if (time() >= $cache->expiration) return $this->forget($key);

			return unserialize($cache->value);
		}
	}

	/**
	 * Write an item to the cache for a given number of minutes.
	 *
	 * <code>
	 *		// Put an item in the cache for 15 minutes
	 *		Cache::put('name', 'Taylor', 15);
	 * </code>
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @param  int     $minutes
	 * @return void
	 */
	public function put($key, $value, $minutes)
	{
		$key = $this->key.$key;

		$value = serialize($value);

		$expiration = $this->expiration($minutes);

		// To update the value, we'll first attempt an insert against the
		// database and if we catch an exception, we'll assume that the
		// primary key already exists in the table and update.
		try
		{
			$this->table()->insert(compact('key', 'value', 'expiration'));
		}
		catch (\Exception $e)
		{
			$this->table()->where('key', '=', $key)->update(compact('value', 'expiration'));
		}
	}

	/**
	 * Write an item to the cache for five years.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 */
	public function forever($key, $value)
	{
		return $this->put($key, $value, 2628000);
	}

	/**
	 * Delete an item from the cache.
	 *
	 * @param  string  $key
	 * @return void
	 */
	public function forget($key)
	{
		$this->table()->where('key', '=', $this->key.$key)->delete();
	}

	/**
	 * Get a query builder for the database table.
	 *
	 * @return Laravel\Database\Query
	 */
	protected function table()
	{
		$connection = DB::connection(Config::get('cache.database.connection'));

		return $connection->table(Config::get('cache.database.table'));
	}

}