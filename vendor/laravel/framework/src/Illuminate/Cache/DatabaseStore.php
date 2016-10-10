<?php namespace Illuminate\Cache;

use Illuminate\Database\Connection;
use Illuminate\Encryption\Encrypter;

class DatabaseStore implements StoreInterface {

	/**
	 * The database connection instance.
	 *
	 * @var \Illuminate\Database\Connection
	 */
	protected $connection;

	/**
	 * The encrypter instance.
	 *
	 * @var \Illuminate\Encryption\Encrypter
	 */
	protected $encrypter;

	/**
	 * The name of the cache table.
	 *
	 * @var string
	 */
	protected $table;

	/**
	 * A string that should be prepended to keys.
	 *
	 * @var string
	 */
	protected $prefix;

	/**
	 * Create a new database store.
	 *
	 * @param  \Illuminate\Database\Connection  $connection
	 * @param  \Illuminate\Encryption\Encrypter  $encrypter
	 * @param  string  $table
	 * @param  string  $prefix
	 * @return void
	 */
	public function __construct(Connection $connection, Encrypter $encrypter, $table, $prefix = '')
	{
		$this->table = $table;
		$this->prefix = $prefix;
		$this->encrypter = $encrypter;
		$this->connection = $connection;
	}

	/**
	 * Retrieve an item from the cache by key.
	 *
	 * @param  string  $key
	 * @return mixed
	 */
	public function get($key)
	{
		$prefixed = $this->prefix.$key;

		$cache = $this->table()->where('key', '=', $prefixed)->first();

		// If we have a cache record we will check the expiration time against current
		// time on the system and see if the record has expired. If it has, we will
		// remove the records from the database table so it isn't returned again.
		if ( ! is_null($cache))
		{
			if (is_array($cache)) $cache = (object) $cache;

			if (time() >= $cache->expiration)
			{
				return $this->forget($key);
			}

			return $this->encrypter->decrypt($cache->value);
		}
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
		$key = $this->prefix.$key;

		// All of the cached values in the database are encrypted in case this is used
		// as a session data store by the consumer. We'll also calculate the expire
		// time and place that on the table so we will check it on our retrieval.
		$value = $this->encrypter->encrypt($value);

		$expiration = $this->getTime() + ($minutes * 60);

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
	 * Increment the value of an item in the cache.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 *
	 * @throws \LogicException
	 */
	public function increment($key, $value = 1)
	{
		throw new \LogicException("Increment operations not supported by this driver.");
	}

	/**
	 * Increment the value of an item in the cache.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 *
	 * @throws \LogicException
	 */
	public function decrement($key, $value = 1)
	{
		throw new \LogicException("Decrement operations not supported by this driver.");
	}

	/**
	 * Get the current system time.
	 *
	 * @return int
	 */
	protected function getTime()
	{
		return time();
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
		return $this->put($key, $value, 5256000);
	}

	/**
	 * Remove an item from the cache.
	 *
	 * @param  string  $key
	 * @return void
	 */
	public function forget($key)
	{
		$this->table()->where('key', '=', $this->prefix.$key)->delete();
	}

	/**
	 * Remove all items from the cache.
	 *
	 * @return void
	 */
	public function flush()
	{
		$this->table()->delete();
	}

	/**
	 * Get a query builder for the cache table.
	 *
	 * @return \Illuminate\Database\Query\Builder
	 */
	protected function table()
	{
		return $this->connection->table($this->table);
	}

	/**
	 * Get the underlying database connection.
	 *
	 * @return \Illuminate\Database\Connection
	 */
	public function getConnection()
	{
		return $this->connection;
	}

	/**
	 * Get the encrypter instance.
	 *
	 * @return \Illuminate\Encryption\Encrypter
	 */
	public function getEncrypter()
	{
		return $this->encrypter;
	}

	/**
	 * Get the cache key prefix.
	 *
	 * @return string
	 */
	public function getPrefix()
	{
		return $this->prefix;
	}

}
