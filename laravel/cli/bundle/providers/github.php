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
		$repository = "git://github.com/{$bundle['repository']}.git";

		$this->add($bundle['name'], $repository);

		$this->update();
	}

	/**
	 * Add the given bundle as a Git submodule.
	 *
	 * @param  string  $name
	 * @param  string  $repository
	 * @return void
	 */
	protected function add($name, $repository)
	{
		passthru('git submodule add '.$repository.' '.BUNDLE_PATH.$name);
	}

	/**
	 * Update the Git submodules for the application.
	 *
	 * @return void
	 */
	protected function update()
	{
		passthru('git submodule update');
	}

}