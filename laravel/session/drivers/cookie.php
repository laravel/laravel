<?php namespace Laravel\Session\Drivers;

use Laravel\Cookie as C;
use Laravel\Security\Crypter;

class Cookie implements Driver {

	/**
	 * The crypter instance.
	 *
	 * All session cookies have an encrypted payload. Since the session contains sensitive
	 * data that cannot be compromised, it is important that the payload be encrypted using
	 * the strong encryption provided by the Crypter class.
	 *
	 * @var Crypter
	 */
	private $crypter;

	/**
	 * Create a new Cookie session driver instance.
	 *
	 * @param  Crypter  $crypter
	 * @return void
	 */
	public function __construct(Crypter $crypter)
	{
		$this->crypter = $crypter;
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
		if (C::has('session_payload'))
		{
			return unserialize($this->crypter->decrypt(C::get('session_payload')));
		}
	}

	/**
	 * Save a given session to storage.
	 *
	 * @param  array  $session
	 * @param  array  $config
	 * @return void
	 */
	public function save($session, $config)
	{
		extract($config);

		$payload = $this->crypter->encrypt(serialize($session));

		C::put('session_payload', $payload, $lifetime, $path, $domain);
	}

	/**
	 * Delete a session from storage by a given ID.
	 *
	 * @param  string  $id
	 * @return void
	 */
	public function delete($id)
	{
		C::forget('session_payload');
	}

}