<?php namespace Laravel\Session;

use Laravel\Cache;
use Laravel\Config;

class Memcached extends Driver {

	protected function load($id)
	{
		return Cache::driver('memcached')->get($id);
	}

	protected function save()
	{
		Cache::driver('memcached')->put($this->session['id'], $this->session, Config::get('session.lifetime'));
	}

	protected function delete()
	{
		Cache::driver('memcached')->forget($this->session['id']);
	}

}