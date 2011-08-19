<?php namespace Laravel\Session;

class File implements Driver, Sweeper {

	/**
	 * Load a session by ID.
	 *
	 * @param  string  $id
	 * @return array
	 */
	public function load($id)
	{
		if (file_exists($path = SESSION_PATH.$id)) return unserialize(file_get_contents($path));
	}

	/**
	 * Save a session.
	 *
	 * @param  array  $session
	 * @return void
	 */
	public function save($session)
	{
		file_put_contents(SESSION_PATH.$session['id'], serialize($session), LOCK_EX);
	}

	/**
	 * Delete a session by ID.
	 *
	 * @param  string  $id
	 * @return void
	 */
	public function delete($id)
	{
		@unlink(SESSION_PATH.$id);
	}

	/**
	 * Delete all expired sessions.
	 *
	 * @param  int   $expiration
	 * @return void
	 */
	public function sweep($expiration)
	{
		foreach (glob(SESSION_PATH.'*') as $file)
		{
			if (filetype($file) == 'file' and filemtime($file) < $expiration) @unlink($file);
		}
	}
	
}