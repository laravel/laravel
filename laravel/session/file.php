<?php namespace Laravel\Session;

class File extends Driver implements Sweeper {

	protected function load($id)
	{
		if (file_exists($path = SESSION_PATH.$id)) return unserialize(file_get_contents($path));
	}

	protected function save()
	{
		file_put_contents(SESSION_PATH.$this->session['id'], serialize($this->session), LOCK_EX);
	}

	protected function delete()
	{
		@unlink(SESSION_PATH.$this->session['id']);
	}

	public function sweep($expiration)
	{
		foreach (glob(SESSION_PATH.'*') as $file)
		{
			if (filetype($file) == 'file' and filemtime($file) < $expiration) @unlink($file);
		}
	}
	
}