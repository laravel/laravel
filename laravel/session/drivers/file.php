<?php namespace Laravel\Session\Drivers;

class File implements Driver, Sweeper {

	/**
	 * The file engine instance.
	 *
	 * @var Laravel\File
	 */
	private $file;

	/**
	 * The path to which the session files should be written.
	 *
	 * @var string
	 */
	private $path;

	/**
	 * Create a new File session driver instance.
	 *
	 * @param  Laravel\File  $file
	 * @param  string        $path
	 * @return void
	 */
	public function __construct(\Laravel\File $file, $path)
	{
		$this->file = $file;
		$this->path = $path;
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
		if ($this->file->exists($path = $this->path.$id)) return unserialize($this->file->get($path));
	}

	/**
	 * Save a given session to storage.
	 *
	 * @param  array  $session
	 * @param  array  $config
	 * @return void
	 */
	public function save($session, $config)
	{
		$this->file->put($this->path.$session['id'], serialize($session), LOCK_EX);
	}

	/**
	 * Delete a session from storage by a given ID.
	 *
	 * @param  string  $id
	 * @return void
	 */
	public function delete($id)
	{
		$this->file->delete($this->path.$id);
	}

	/**
	 * Delete all expired sessions from persistant storage.
	 *
	 * @param  int   $expiration
	 * @return void
	 */
	public function sweep($expiration)
	{
		foreach (glob($this->path.'*') as $file)
		{
			if ($this->file->type($file) == 'file' and $this->file->modified($file) < $expiration)
			{
				$this->file->delete($file);
			}
		}
	}
	
}