<?php namespace Laravel\Session;

class Memcached extends Driver {

	/**
	 * The Memcache cache driver instance.
	 *
	 * @var Memcached
	 */
	private $memcached;

	/**
	 * The session lifetime.
	 *
	 * @var int
	 */
	private $lifetime;

	/**
	 * Create a new Memcached session driver instance.
	 *
	 * @param  Memcached  $memcached
	 * @return void
	 */
	public function __construct(\Laravel\Cache\Memcached $memcached, $lifetime)
	{
		$this->lifetime = $lifetime;
		$this->memcached = $memcached;
	}

	/**
	 * Load a session by ID.
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
		$this->memcached->put($this->session['id'], $this->session, $this->lifetime);
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