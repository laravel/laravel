<?php namespace Laravel\Cache;

use Laravel\Config;
use Laravel\Memcached as Mem;

class Memcached extends Driver {

	public function has($key)
	{
		return ( ! is_null($this->get($key)));
	}

	public function get($key, $default = null)
	{
		$item = (($cache = Mem::instance()->get(Config::get('cache.key').$key)) !== false) ? $cache : null;

		return $this->prepare($item, $default);
	}

	public function put($key, $value, $minutes)
	{
		Mem::instance()->set(Config::get('cache.key').$key, $value, 0, $minutes * 60);
	}

	public function forget($key)
	{
		Mem::instance()->delete(Config::get('cache.key').$key);
	}

}