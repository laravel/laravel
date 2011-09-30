<?php namespace Laravel\Session\Drivers;

class Memcached implements Driver {

	/**
	 * The Memcache cache driver instance.
	 *
	 * @var Memcached
	 */
	private $memcached;

	/**
	 * Create a new Memcached session driver instance.
	 *
	 * @param  Memcached  $memcached
	 * @return void
	 */
	public function __construct(\Laravel\Cache\Drivers\Memcached $memcached)
	{
		$this->memcached = $memcached;
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
		return $this->memcached->get($id);
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
		$this->memcached->put($session['id'], $session, $config['lifetime']);
	}

	/**
	 * Delete a session from storage by a given ID.
	 *
	 * @param  string  $id
	 * @return void
	 */
	public function delete($id)
	{
		$this->memcached->forget($id);
	}

}