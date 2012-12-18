<?php namespace Laravel\CLI\Tasks\Bundle\Providers; use Laravel\Request;

class Github extends Provider {

	/**
	 * Install the given bundle into the application.
	 *
	 * @param  string  $bundle
	 * @param  string  $path
	 * @return void
	 */
	public function install($bundle, $path)
	{
		$url = "http://github.com/{$bundle['location']}/zipball/master";

		parent::zipball($url, $bundle, $path);
	}

}