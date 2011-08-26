<?php namespace Laravel\Session;

use Laravel\Config;

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
	public function __construct(\Laravel\Cache\Memcached $memcached)
	{
		$this->memcached = $memcached;
	}

	/**
	 * Load a session by ID.
	 *
	 * The session will be retrieved from persistant storage and returned as an array.
	 * The array contains the session ID, last activity UNIX timestamp, and session data.
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
	 * @return void
	 */
	protected function save()
	{
		$this->memcached->put($this->session['id'], $this->session, Config::get('session.lifetime'));
	}

	/**
	 * Delete the session from persistant storage.
	 *
	 * @return void
	 */
	protected function delete()
	{
		$this->memcached->forget($this->session['id']);
	}

}