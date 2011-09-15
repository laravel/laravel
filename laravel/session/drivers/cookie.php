<?php namespace Laravel\Session\Drivers;

use Laravel\Security\Crypter;

class Cookie extends Driver {

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
	 * Load a session by ID.
	 *
	 * This method is responsible for retrieving the session from persistant storage. If the
	 * session does not exist in storage, nothing should be returned from the method, in which
	 * case a new session will be created by the base driver.
	 *
	 * @param  string  $id
	 * @return array
	 */
	protected function load($id)
	{
		if ($this->cookie->has('session_payload'))
		{
			return unserialize($this->crypter->decrypt($this->cookie->get('session_payload')));
		}
	}

	/**
	 * Save the session to persistant storage.
	 *
	 * @param  array  $session
	 * @return void
	 */
	protected function save($session)
	{
		if ( ! headers_sent())
		{
			$config = $this->config->get('session');

			extract($config);

			$payload = $this->crypter->encrypt(serialize($session));

			$this->cookie->put('session_payload', $payload, $lifetime, $path, $domain);
		}
	}

	/**
	 * Delete the session from persistant storage.
	 *
	 * @param  string  $id
	 * @return void
	 */
	protected function delete($id)
	{
		$this->cookie->forget('session_payload');
	}

}