<?php namespace Laravel\CLI\Tasks\Bundle\Providers;

interface Provider {

	/**
	 * Install the given bundle into the application.
	 *
	 * @param  string  $bundle
	 * @return void
	 */
	public function install($bundle);

}