<?php namespace System\Session;

use System\Config;
use System\Crypter;

class Cookie implements Driver {

	/**
	 * The Crypter instance.
	 *
	 * @var Crypter
	 */
	private $crypter;

	/**
	 * Create a new Cookie session driver instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->crypter = new Crypter;

		if (Config::get('application.key') == '')
		{
			throw new \Exception("You must set an application key before using the Cookie session driver.");
		}
	}

	/**
	 * Load a session by ID.
	 *
	 * @param  string  $id
	 * @return array
	 */
	public function load($id)
	{
		if (\System\Cookie::has('session_payload'))
		{
			return unserialize($this->crypter->decrypt(\System\Cookie::get('session_payload')));
		}
	}

	/**
	 * Save a session.
	 *
	 * @param  array  $session
	 * @return void
	 */
	public function save($session)
	{
		if ( ! headers_sent())
		{
			extract(Config::get('session'));

			$payload = $this->crypter->encrypt(serialize($session));

			\System\Cookie::put('session_payload', $payload, $lifetime, $path, $domain, $https, $http_only);
		}
	}

	/**
	 * Delete a session by ID.
	 *
	 * @param  string  $id
	 * @return void
	 */
	public function delete($id)
	{
		\System\Cookie::forget('session_payload');
	}

}