<?php namespace Laravel\Session\Drivers;

class Redis implements Driver {

	/**
	 * The Redis cache driver instance.
	 *
	 * @var Laravel\Cache\Drivers\Redis
	 */
	protected $redis;

	/**
	 * Create a new Redis session driver.
	 *
	 * @param  Laravel\Cache\Drivers\Redis  $redis
	 * @return void
	 */
	public function __construct(\Laravel\Cache\Drivers\Redis $redis)
	{
		$this->redis = $redis;
	}

	/**
	 * Load a session from storage by a given ID.
	 *
	 * If no session is found for the ID, null will be returned.
	 *
	 * @param  string  $id
	 * @return array
	 */
	public function load($id)
	{
		return $this->redis->get($id);
	}

	/**
	 * Save a given session to storage.
	 *
	 * @param  array  $session
	 * @param  array  $config
	 * @param  bool   $exists
	 * @return void
	 */
	public function save($session, $config, $exists)
	{
		$this->redis->put($session['id'], $session, $config['lifetime']);
	}

	/**
	 * Delete a session from storage by a given ID.
	 *
	 * @param  string  $id
	 * @return void
	 */
	public function delete($id)
	{
		$this->redis->forget($id);
	}

}