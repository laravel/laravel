<?php namespace System\Session;

use System\Cache;

class APC implements Driver {

	/**
	 * Load a session by ID.
	 *
	 * @param  string  $id
	 * @return array
	 */
	public function load($id)
	{
		return Cache::driver('apc')->get($id);
	}

	/**
	 * Save a session.
	 *
	 * @param  array  $session
	 * @return void
	 */
	public function save($session)
	{
		Cache::driver('apc')->put($session['id'], $session, Config::get('session.lifetime'));
	}

	/**
	 * Delete a session by ID.
	 *
	 * @param  string  $id
	 * @return void
	 */
	public function delete($id)
	{
		Cache::driver('apc')->forget($id);
	}

}