<?php namespace Laravel\Session\Drivers;

use Laravel\Database\Connection;

class Database extends Driver implements Sweeper {

	/**
	 * The database connection.
	 *
	 * @var Connection
	 */
	protected $connection;

	/**
	 * Create a new database session driver.
	 *
	 * @param  Connection  $connection
	 * @return void
	 */
	public function __construct(Connection $connection)
	{
		$this->connection = $connection;
	}

	/**
	 * Load a session by ID.
	 *
	 * @param  string  $id
	 * @return array
	 */
	protected function load($id)
	{
		$session = $this->table()->find($id);

		if ( ! is_null($session))
		{
			return array(
				'id'            => $session->id,
				'last_activity' => $session->last_activity,
				'data'          => unserialize($session->data)
			);
		}
	}

	/**
	 * Save the session to persistant storage.
	 *
	 * @return void
	 */
	protected function save()
	{
		$this->delete($this->session['id']);

		$this->table()->insert(array(
			'id'            => $this->session['id'], 
			'last_activity' => $this->session['last_activity'], 
			'data'          => serialize($this->session['data'])
		));
	}

	/**
	 * Delete the session from persistant storage.
	 *
	 * @return void
	 */
	protected function delete()
	{
		$this->table()->delete($this->session['id']);
	}

	/**
	 * Delete all expired sessions from persistant storage.
	 *
	 * @param  int   $expiration
	 * @return void
	 */
	public function sweep($expiration)
	{
		$this->table()->where('last_activity', '<', $expiration)->delete();
	}

	/**
	 * Get a session database query.
	 *
	 * @return Query
	 */
	protected function table()
	{
		return $this->connection->table($this->config->get('session.table'));		
	}
	
}