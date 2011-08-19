<?php namespace Laravel\Cache;

use Laravel\Config;

class APC extends Driver {

	public function has($key)
	{
		return ( ! is_null($this->get($key)));
	}

	public function get($key, $default = null)
	{
		$item = ( ! is_null($cache = apc_fetch(Config::get('cache.key').$key))) ? $cache : null;

		return $this->prepare($item, $default);
	}

	public function put($key, $value, $minutes)
	{
		apc_store(Config::get('cache.key').$key, $value, $minutes * 60);
	}

	public function forget($key)
	{
		apc_delete(Config::get('cache.key').$key);
	}

}