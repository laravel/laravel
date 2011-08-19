<?php namespace Laravel\Cache;

class File extends Driver {

	public function has($key)
	{
		return ( ! is_null($this->get($key)));
	}

	public function get($key, $default = null)
	{
		if ( ! file_exists(CACHE_PATH.$key))
		{
			return $this->prepare(null, $default);
		}

		$cache = file_get_contents(CACHE_PATH.$key);

		// The cache expiration date is stored as a UNIX timestamp at the beginning
		// of the cache file. We'll extract it out and check it here.
		if (time() >= substr($cache, 0, 10))
		{
			$this->forget($key);

			return $this->prepare(null, $default);
		}

		return $this->prepare(unserialize(substr($cache, 10)), $default);
	}

	public function put($key, $value, $minutes)
	{
		file_put_contents(CACHE_PATH.$key, (time() + ($minutes * 60)).serialize($value), LOCK_EX);
	}

	public function forget($key)
	{
		@unlink(CACHE_PATH.$key);
	}

}