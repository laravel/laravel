<?php namespace Laravel\Session\Drivers;

class File extends Driver implements Sweeper {

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
	 * Load a session by ID.
	 *
	 * This method is responsible for retrieving the session from persistant storage. If the
	 * session does not exist in storage, nothing should be returned from the method, in which
	 * case a new session will be created by the base driver.
	 *
	 * @param  string  $id
	 * @return array
	 */
	protected function load($id)
	{
		if ($this->file->exists($path = $this->path.$id)) return unserialize($this->file->get($path));
	}

	/**
	 * Save the session to persistant storage.
	 *
	 * @param  array  $session
	 * @return void
	 */
	protected function save($session)
	{
		$this->file->put($this->path.$session['id'], serialize($session), LOCK_EX);
	}

	/**
	 * Delete the session from persistant storage.
	 *
	 * @param  string  $id
	 * @return void
	 */
	protected function delete($id)
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