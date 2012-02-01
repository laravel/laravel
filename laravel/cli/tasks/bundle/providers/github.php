<?php namespace Laravel\CLI\Tasks\Bundle\Providers;

class Github implements Provider {

	/**
	 * Install the given bundle into the application.
	 *
	 * @param  string  $bundle
	 * @return void
	 */
	public function install($bundle)
	{
		$repository = "git@github.com:{$bundle['location']}.git";

		$path = array_get($bundle, 'path', $bundle['name']);

		// If the installation target directory doesn't exist, we will create
		// it recursively so that we can properly add the Git submodule for
		// the bundle when we install.
		if ( ! is_dir($target = dirname(path('bundle').$path)))
		{
			mkdir($target, 0777, true);
		}

		// We need to just extract the basename of the bundle path when
		// adding the submodule. Of course, we can't add a submodule to
		// a location outside of the Git repository, so we don't need
		// the full bundle path.
		$root = basename(path('bundle')).'/';

		passthru('git submodule add '.$repository.' '.$root.$path);

		passthru('git submodule update');
	}

}