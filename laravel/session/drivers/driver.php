<?php namespace Laravel\Session\Drivers; use Laravel\Config, Laravel\Str;

abstract class Driver {

	/**
	 * Load a session from storage by a given ID.
	 *
	 * If no session is found for the ID, null will be returned.
	 *
	 * @param  string  $id
	 * @return array
	 */
	abstract public function load($id);

	/**
	 * Save a given session to storage.
	 *
	 * @param  array  $session
	 * @param  array  $config
	 * @param  bool   $exists
	 * @return void
	 */
	abstract public function save($session, $config, $exists);

	/**
	 * Delete a session from storage by a given ID.
	 *
	 * @param  string  $id
	 * @return void
	 */
	abstract public function delete($id);

	/**
	 * Create a fresh session array with a unique ID.
	 *
	 * @return array
	 */
	public function fresh()
	{
		// We will simply generate an empty session payload array, using an ID
		// that is not currently assigned to any existing session within the
		// application and return it to the driver.
		return array('id' => $this->id(), 'data' => array(
			':new:' => array(),
			':old:' => array(),
		));
	}

	/**
	 * Get a new session ID that isn't assigned to any current session.
	 *
	 * @return string
	 */
	public function id()
	{
		$session = array();

		// If the driver is an instance of the Cookie driver, we are able to
		// just return any string since the Cookie driver has no real idea
		// of a server side persisted session with an ID.
		if ($this instanceof Cookie)
		{
			return Str::random(40);
		}

		// We'll continue generating random IDs until we find an ID that is
		// not currently assigned to a session. This is almost definitely
		// going to happen on the first iteration.
		do {

			$session = $this->load($id = Str::random(40));			

		} while ( ! is_null($session));

		return $id;
	}

}