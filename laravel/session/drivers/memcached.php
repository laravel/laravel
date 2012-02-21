<?php namespace Laravel\Session\Drivers;

/**
 * The Memcached class provides support for storing session data with Memcached.
 *
 * @package  	Laravel
 * @author  	Taylor Otwell <taylorotwell@gmail.com>
 * @copyright  	2012 Taylor Otwell
 * @license 	MIT License <http://www.opensource.org/licenses/mit>
 * @see  		http://memcached.org/
 */
class Memcached implements Driver {

	/**
	 * The Memcache cache driver instance.
	 *
	 * @var Laravel\Cache\Drivers\Memcached
	 */
	private $memcached;

	/**
	 * Create a new Memcached session driver instance.
	 *
	 * @param  Laravel\Cache\Drivers\Memcached  $memcached
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
