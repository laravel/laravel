<?php namespace Laravel\CLI\Bundle\Providers;

class Github implements Provider {

	/**
	 * Install the given bundle into the application.
	 *
	 * @param  string  $bundle
	 * @return void
	 */
	public function install($bundle)
	{
		echo "Installed {$bundle['name']}!";
		// Install the bundle...
	}

}