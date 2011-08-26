<?php namespace Laravel\Session;

use Laravel\Config;

class APC extends Driver {

	/**
	 * The APC cache driver instance.
	 *
	 * @var Cache\APC
	 */
	private $apc;

	/**
	 * Create a new APC session driver instance.
	 *
	 * @param  Cache\APC  $apc
	 * @return void
	 */
	public function __construct(\Laravel\Cache\APC $apc)
	{
		$this->apc = $apc;
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
		return $this->apc->get($id);
	}

	/**
	 * Save the session to persistant storage.
	 *
	 * @return void
	 */
	protected function save()
	{
		$this->apc->put($this->session['id'], $this->session, Config::get('session.lifetime'));
	}

	/**
	 * Delete the session from persistant storage.
	 *
	 * @return void
	 */
	protected function delete()
	{
		$this->apc->forget($this->session['id']);
	}

}