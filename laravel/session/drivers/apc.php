<?php namespace Laravel\Session\Drivers;

class APC extends Driver {

	/**
	 * The APC cache driver instance.
	 *
	 * This session driver relies on the APC cache driver to provide an interface for
	 * working with an APC equipped server. The cache driver will provide all of the
	 * functionality for retrieving and storing items in APC.
	 *
	 * @var Cache\Drivers\APC
	 */
	protected $apc;

	/**
	 * Create a new APC session driver instance.
	 *
	 * @param  Cache\Drivers\APC  $apc
	 * @return void
	 */
	public function __construct(\Laravel\Cache\Drivers\APC $apc)
	{
		$this->apc = $apc;
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
		return $this->apc->get($id);
	}

	/**
	 * Save the session to persistant storage.
	 *
	 * @param  array  $session
	 * @return void
	 */
	protected function save($session)
	{
		$this->apc->put($session['id'], $session, $this->config->get('session.lifetime'));
	}

	/**
	 * Delete the session from persistant storage.
	 *
	 * @param  string  $id
	 * @return void
	 */
	protected function delete($id)
	{
		$this->apc->forget($id);
	}

}