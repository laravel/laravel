<?php namespace Laravel\Session;

use Laravel\Config;

class DB extends Driver implements Sweeper {

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

	protected function save()
	{
		$this->delete($this->session['id']);

		$this->table()->insert(array(
			'id'            => $this->session['id'], 
			'last_activity' => $this->session['last_activity'], 
			'data'          => serialize($this->session['data'])
		));
	}

	protected function delete()
	{
		$this->table()->delete($this->session['id']);
	}

	public function sweep($expiration)
	{
		$this->table()->where('last_activity', '<', $expiration)->delete();
	}

	/**
	 * Get a session database query.
	 *
	 * @return Query
	 */
	private function table()
	{
		return \System\DB::connection()->table(Config::get('session.table'));		
	}
	
}