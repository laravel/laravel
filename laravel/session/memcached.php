<?php namespace Laravel\Session;

use Laravel\Cache;
use Laravel\Config;

class Memcached implements Driver {

	/**
	 * Load a session by ID.
	 *
	 * @param  string  $id
	 * @return array
	 */
	public function load($id)
	{
		return Cache::driver('memcached')->get($id);
	}

	/**
	 * Save a session.
	 *
	 * @param  array  $session
	 * @return void
	 */
	public function save($session)
	{
		Cache::driver('memcached')->put($session['id'], $session, Config::get('session.lifetime'));
	}

	/**
	 * Delete a session by ID.
	 *
	 * @param  string  $id
	 * @return void
	 */
	public function delete($id)
	{
		Cache::driver('memcached')->forget($id);
	}

}