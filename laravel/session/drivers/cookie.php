<?php namespace Laravel\Session\Drivers;

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
	 * The cookie manager instance.
	 *
	 * @var Cookie
	 */
	private $cookies;

	/**
	 * Create a new Cookie session driver instance.
	 *
	 * @param  Crypter  $crypter
	 * @param  Cookie   $cookies
	 * @return void
	 */
	public function __construct(Crypter $crypter, \Laravel\Cookie $cookies)
	{
		$this->crypter = $crypter;
		$this->cookies = $cookies;
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
		if ($this->cookies->has('session_payload'))
		{
			return unserialize($this->crypter->decrypt($this->cookies->get('session_payload')));
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

		$payload = $this->crypter->encrypt(serialize($session));

		$this->cookies->put('session_payload', $payload, $lifetime, $path, $domain);
	}

	/**
	 * Delete a session from storage by a given ID.
	 *
	 * @param  string  $id
	 * @return void
	 */
	public function delete($id)
	{
		$this->cookies->forget('session_payload');
	}

}