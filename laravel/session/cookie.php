<?php namespace Laravel\Session;

use Laravel\Security\Crypter;

class Cookie extends Driver {

	/**
	 * The cookie engine instance.
	 *
	 * @var Cookie
	 */
	private $cookie;

	/**
	 * The Crypter instance.
	 *
	 * @var Crypter
	 */
	private $crypter;

	/**
	 * The session configuration array.
	 *
	 * @var array
	 */
	private $config;

	/**
	 * Create a new Cookie session driver instance.
	 *
	 * @param  Crypter         $crypter
	 * @param  Laravel\Cookie  $cookie
	 * @param  array           $config
	 * @return void
	 */
	public function __construct(Crypter $crypter, \Laravel\Cookie $cookie, $config)
	{
		$this->cookie = $cookie;
		$this->config = $config;
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
			extract($this->config);

			$payload = $this->crypter->encrypt(serialize($this->session));

			$this->cookie->put('session_payload', $payload, $lifetime, $path, $domain, $https, $http_only);
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