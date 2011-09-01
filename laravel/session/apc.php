<?php namespace Laravel\Session;

class APC extends Driver {

	/**
	 * The APC cache driver instance.
	 *
	 * @var Cache\APC
	 */
	private $apc;

	/**
	 * The session lifetime.
	 *
	 * @var int
	 */
	private $lifetime;

	/**
	 * Create a new APC session driver instance.
	 *
	 * @param  Cache\APC  $apc
	 * @param  int        $lifetime
	 * @return void
	 */
	public function __construct(\Laravel\Cache\APC $apc, $lifetime)
	{
		$this->apc = $apc;
		$this->lifetime = $lifetime;
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
		$this->apc->put($this->session['id'], $this->session, $this->lifetime);
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