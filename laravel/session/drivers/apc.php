<?php namespace Laravel\Session\Drivers;

class APC extends Driver {

	/**
	 * The APC cache driver instance.
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
		$this->apc->put($this->session['id'], $this->session, $this->config->get('session.lifetime'));
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