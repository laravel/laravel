<?php namespace Laravel\Session;

use Laravel\Config;
use Laravel\Security\Crypter;

class Cookie extends Driver {

	/**
	 * The cookie engine instance.
	 *
	 * @var Cookie_Engine
	 */
	private $cookie;

	/**
	 * The Crypter instance.
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

		if (Config::get('application.key') == '')
		{
			throw new \Exception("You must set an application key before using the Cookie session driver.");
		}
	}

	/**
	 * Load a session by ID.
	 *
	 * The session will be retrieved from persistant storage and returned as an array.
	 * The array contains the session ID, last activity UNIX timestamp, and session data.
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
			extract(Config::get('session'));

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