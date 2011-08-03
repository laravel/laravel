<?php namespace System\Session;

use System\Crypt;
use System\Config;

class Cookie implements Driver {

	public function __construct()
	{
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
			return unserialize(Crypt::decrypt(\System\Cookie::get('session_payload')));
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
		$c = \System\Config::get('session');

		\System\Cookie::put('session_payload', Crypt::encrypt(serialize($session)), $c['lifetime'], $c['path'], $c['domain'], $c['https'], $c['http_only']);
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