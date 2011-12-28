<?php namespace Laravel\Cache\Drivers; use DB;

class MySQL extends Driver {

	/**
	 * The database table to which the cache should be stored.
	 *
	 * @var string
	 */
	protected $table;

	/**
	 * Create a new MySQL cache driver instance.
	 *
	 * @param  string  $path
	 * @return void
	 */
	public function __construct($table)
	{
		$this->table = $table;
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
		$cache = DB::table($this->table)->where_key($key)->first();
		if ( is_null($cache) ) return null;

		if (time() >= strtotime($cache->time)) {
			return $this->forget($key);
		}

		return unserialize($cache->value);
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
		$time = date( 'Y-m-d H:i:s', time() + ($minutes * 60) );
		$value = serialize($value);
		DB::table($this->table)->insert( compact('key', 'value', 'time') );
	}

	/**
	 * Delete an item from the cache.
	 *
	 * @param  string  $key
	 * @return void
	 */
	public function forget($key)
	{
		DB::table($this->table)->where_key($key)->delete();
	}

}