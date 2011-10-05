<?php namespace Laravel\Session\Drivers;

use Laravel\Security\Crypter;

class Cookie implements Driver {

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
		if (\Laravel\Cookie::has('session_payload'))
		{
			return unserialize(Crypter::decrypt(\Laravel\Cookie::get('session_payload')));
		}
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
		extract($config);

		$payload = Crypter::encrypt(serialize($session));

		\Laravel\Cookie::put('session_payload', $payload, $lifetime, $path, $domain);
	}

	/**
	 * Delete a session from storage by a given ID.
	 *
	 * @param  string  $id
	 * @return void
	 */
	public function delete($id)
	{
		\Laravel\Cookie::forget('session_payload');
	}

}