<?php namespace Laravel\Session;

class File extends Driver implements Sweeper {

	/**
	 * The file manager instance.
	 *
	 * @var Laravel\File
	 */
	private $file;

	/**
	 * Create a new File session driver instance.
	 *
	 * @param  Laravel\File  $file
	 * @return void
	 */
	public function __construct(\Laravel\File $file)
	{
		$this->file = $file;
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
		if ($this->file->exists($path = SESSION_PATH.$id)) return unserialize($this->file->get($path));
	}

	/**
	 * Save the session to persistant storage.
	 *
	 * @return void
	 */
	protected function save()
	{
		$this->file->put(SESSION_PATH.$this->session['id'], serialize($this->session), LOCK_EX);
	}

	/**
	 * Delete the session from persistant storage.
	 *
	 * @return void
	 */
	protected function delete()
	{
		$this->file->delete(SESSION_PATH.$this->session['id']);
	}

	/**
	 * Delete all expired sessions from persistant storage.
	 *
	 * @param  int   $expiration
	 * @return void
	 */
	public function sweep($expiration)
	{
		foreach (glob(SESSION_PATH.'*') as $file)
		{
			if ($this->file->type($file) == 'file' and $this->file->modified($file) < $expiration) $this->file->delete($file);
		}
	}
	
}