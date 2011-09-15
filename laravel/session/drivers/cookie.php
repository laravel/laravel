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
	 * @return void
	 */
	protected function save()
	{
		if ( ! headers_sent())
		{
			$config = $this->config->get('session');

			extract($config);

			$payload = $this->crypter->encrypt(serialize($this->session));

			$this->cookie->put('session_payload', $payload, $lifetime, $path, $domain);
		}
	}

	/**
	 * Delete the session from persistant storage.
	 *
	 * @return void
	 */
	protected function delete()
	{
		$this->cookie->forget('session_payload');
	}

}