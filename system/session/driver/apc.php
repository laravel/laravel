<?php namespace System\Session\Driver;

class APC implements \System\Session\Driver {

	/**
	 * Load a session by ID.
	 *
	 * @param  string  $id
	 * @return array
	 */
	public function load($id)
	{
		return \System\Cache::driver('apc')->get($id);
	}

	/**
	 * Save a session.
	 *
	 * @param  array  $session
	 * @return void
	 */
	public function save($session)
	{
		\System\Cache::driver('apc')->put($session['id'], $session, \System\Config::get('session.lifetime'));
	}

	/**
	 * Delete a session by ID.
	 *
	 * @param  string  $id
	 * @return void
	 */
	public function delete($id)
	{
		\System\Cache::driver('apc')->forget($id);
	}

	/**
	 * Delete all expired sessions.
	 *
	 * @param  int   $expiration
	 * @return void
	 */
	public function sweep($expiration)
	{
		// APC sessions will expire automatically.
	}

}