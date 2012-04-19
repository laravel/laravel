<?php namespace Laravel\Session\Drivers; use Laravel\Crypter, Laravel\Cookie as C;

class Cookie extends Driver {

	/**
	 * The name of the cookie used to store the session payload.
	 *
	 * @var string
	 */
	const payload = 'session_payload';

	/**
	 * Indicates that a session have been loaded for the current request.
	 *
	 * @var boolean
	 */
	static $loaded = false;

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
		if (!static::$loaded and \Laravel\Cookie::has(Cookie::payload))
		{
			static::$loaded = true;
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

		C::put(Cookie::payload, $payload, $lifetime, $path, $domain);
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