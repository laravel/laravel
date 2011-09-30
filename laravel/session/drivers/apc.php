<?php namespace Laravel\Session\Drivers;

class APC implements Driver {

	/**
	 * The APC cache driver instance.
	 *
	 * This session driver relies on the APC cache driver to provide an interface for
	 * working with an APC equipped server. The cache driver will provide all of the
	 * functionality for retrieving and storing items in APC.
	 *
	 * @var Cache\Drivers\APC
	 */
	private $apc;

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
	 * Load a session from storage by a given ID.
	 *
	 * If no session is found for the ID, null will be returned.
	 *
	 * @param  string  $id
	 * @return array
	 */
	public function load($id)
	{
		return $this->apc->get($id);
	}

	/**
	 * Save a given session to storage.
	 *
	 * @param  array  $session
	 * @param  array  $config
	 * @param  bool   $exists
	 * @return void
	 */
	public function save($session, $config, $exists)
	{
		$this->apc->put($session['id'], $session, $config['lifetime']);
	}

	/**
	 * Delete a session from storage by a given ID.
	 *
	 * @param  string  $id
	 * @return void
	 */
	public function delete($id)
	{
		$this->apc->forget($id);
	}

}