<?php namespace System\Session\Driver;

class DB implements \System\Session\Driver {

	/**
	 * Load a session by ID.
	 *
	 * @param  string  $id
	 * @return array
	 */
	public function load($id)
	{
		$session = $this->query()->find($id);

		if ( ! is_null($session))
		{
			return array('id' => $session->id, 'last_activity' => $session->last_activity, 'data' => unserialize($session->data));
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
		$this->delete($session['id']);
		$this->query()->insert(array('id' => $session['id'], 'last_activity' => $session['last_activity'], 'data' => serialize($session['data'])));
	}

	/**
	 * Delete a session by ID.
	 *
	 * @param  string  $id
	 * @return void
	 */
	public function delete($id)
	{
		$this->query()->where('id', '=', $id)->delete();
	}

	/**
	 * Delete all expired sessions.
	 *
	 * @param  int   $expiration
	 * @return void
	 */
	public function sweep($expiration)
	{
		$this->query()->where('last_activity', '<', $expiration)->delete();
	}

	/**
	 * Get a session database query.
	 *
	 * @return Query
	 */
	private function query()
	{
		return \System\DB::table(\System\Config::get('session.table'));		
	}
	
}