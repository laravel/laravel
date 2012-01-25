<?php namespace Laravel\Session\Drivers;

use Laravel\Crypter;

class Cookie implements Driver {

	/**
	 * The name of the cookie used to store the session payload.
	 *
	 * @var string
	 */
	const payload = 'session_payload';

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
		if (\Laravel\Cookie::has(Cookie::payload))
		{
			return unserialize(Crypter::decrypt(\Laravel\Cookie::get(Cookie::payload)));
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
		extract($config, EXTR_SKIP);

		$payload = Crypter::encrypt(serialize($session));

		// A cookie payload can't exceed 4096 bytes, so if the encrypted payload
		// is greater than that, we'll throw an exception so the developer can
		// switch to another session driver for the application.
		if (strlen($payload) > 4000)
		{
			throw new \Exception("Session payload too large for cookie.");
		}

		\Laravel\Cookie::put(Cookie::payload, $payload, $lifetime, $path, $domain);
	}

	/**
	 * Delete a session from storage by a given ID.
	 *
	 * @param  string  $id
	 * @return void
	 */
	public function delete($id)
	{
		\Laravel\Cookie::forget(Cookie::payload);
	}

}