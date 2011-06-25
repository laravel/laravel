<?php namespace System\Session\Driver;

class File implements \System\Session\Driver {

	/**
	 * Load a session by ID.
	 *
	 * @param  string  $id
	 * @return array
	 */
	public function load($id)
	{
		if (file_exists($path = APP_PATH.'storage/sessions/'.$id))
		{
			return unserialize(file_get_contents($path));
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
		file_put_contents(APP_PATH.'storage/sessions/'.$session['id'], serialize($session), LOCK_EX);
	}

	/**
	 * Delete a session by ID.
	 *
	 * @param  string  $id
	 * @return void
	 */
	public function delete($id)
	{
		@unlink(APP_PATH.'storage/sessions/'.$id);
	}

	/**
	 * Delete all expired sessions.
	 *
	 * @param  int   $expiration
	 * @return void
	 */
	public function sweep($expiration)
	{
		foreach (glob(APP_PATH.'storage/sessions/*') as $file)
		{
			if (filetype($file) == 'file' and filemtime($file) < $expiration)
			{
				@unlink($file);
			}			
		}
	}
	
}