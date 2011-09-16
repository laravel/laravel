<?php namespace Laravel\Session\Drivers;

use Laravel\Security\Crypter;

class Cookie implements Driver {

	/**
	 * The cookie manager instance.
	 *
	 * @var Cookie
	 */
	private $cookie;

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
	 * @param  Crypter         $crypter
	 * @param  Laravel\Cookie  $cookie
	 * @return void
	 */
	public function __construct(Crypter $crypter, \Laravel\Cookie $cookie)
	{
		$this->cookie = $cookie;
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
		if ($this->cookie->has('session_payload'))
		{
			return unserialize($this->crypter->decrypt($this->cookie->get('session_payload')));
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
		if ( ! headers_sent())
		{
			extract($config);

			$payload = $this->crypter->encrypt(serialize($session));

			$this->cookie->put('session_payload', $payload, $lifetime, $path, $domain);
		}
	}

	/**
	 * Delete a session from storage by a given ID.
	 *
	 * @param  string  $id
	 * @return void
	 */
	public function delete($id)
	{
		$this->cookie->forget('session_payload');
	}

}