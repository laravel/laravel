<?php namespace Laravel\Session;

use Laravel\Cache;
use Laravel\Config;

class APC extends Driver {

	protected function load($id)
	{
		return Cache::driver('apc')->get($id);
	}

	protected function save()
	{
		Cache::driver('apc')->put($this->session['id'], $this->session, Config::get('session.lifetime'));
	}

	protected function delete()
	{
		Cache::driver('apc')->forget($this->session['id']);
	}

}