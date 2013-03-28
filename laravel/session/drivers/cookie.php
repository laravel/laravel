<?php namespace Laravel\Session\Drivers; use Laravel\Crypter, Laravel\Cookie as C;

class Cookie extends Driver {

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
		if (C::has(Cookie::payload))
		{
			return unserialize(Crypter::decrypt(C::get(Cookie::payload)));
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

		C::put(Cookie::payload, $payload, $lifetime, $path, $domain, $secure);
	}

	/**
	 * Delete a session from storage by a given ID.
	 *
	 * @param  string  $id
	 * @return void
	 */
	public function delete($id)
	{
		C::forget(Cookie::payload);
	}

}
