<?php namespace Laravel\Session;

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
	 * @return void
	 */
	protected function save()
	{
		$this->file->put($this->path.$this->session['id'], serialize($this->session), LOCK_EX);
	}

	/**
	 * Delete the session from persistant storage.
	 *
	 * @return void
	 */
	protected function delete()
	{
		$this->file->delete($this->path.$this->session['id']);
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