<?php namespace Laravel\Session\Drivers;

class Memcached extends Driver {

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
	 * Load a session by ID.
	 *
	 * This method is responsible for retrieving the session from persistant storage. If the
	 * session does not exist in storage, nothing should be returned from the method, in which
	 * case a new session will be created by the base driver.
	 *
	 * @param  string  $id
	 * @return array
	 */
	protected function load($id)
	{
		return $this->memcached->get($id);
	}

	/**
	 * Save the session to persistant storage.
	 *
	 * @param  array  $session
	 * @return void
	 */
	protected function save($session)
	{
		$this->memcached->put($session['id'], $session, $this->config->get('session.lifetime'));
	}

	/**
	 * Delete the session from persistant storage.
	 *
	 * @param  string  $id
	 * @return void
	 */
	protected function delete($id)
	{
		$this->memcached->forget($id);
	}

}