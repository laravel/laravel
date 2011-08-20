<?php namespace Laravel\Session;

use Laravel\Config;
use Laravel\Crypter;

class Cookie extends Driver {

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

	protected function load($id)
	{
		if (\System\Cookie::has('session_payload'))
		{
			return unserialize($this->crypter->decrypt(\System\Cookie::get('session_payload')));
		}
	}

	protected function save()
	{
		if ( ! headers_sent())
		{
			extract(Config::get('session'));

			$payload = $this->crypter->encrypt(serialize($this->session));

			\System\Cookie::put('session_payload', $payload, $lifetime, $path, $domain, $https, $http_only);
		}
	}

	protected function delete()
	{
		\System\Cookie::forget('session_payload');
	}

}