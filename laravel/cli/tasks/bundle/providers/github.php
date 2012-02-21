<?php namespace Laravel\CLI\Tasks\Bundle\Providers; use Laravel\Request;

/**
 * The Github class is a provider class which allows the downloading of
 * bundles directly from github.
 *
 * @package  	Laravel
 * @author  	Taylor Otwell <taylorotwell@gmail.com>
 * @copyright  	2012 Taylor Otwell
 * @license 	MIT License <http://www.opensource.org/licenses/mit>
 */
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
		$url = "http://nodeload.github.com/{$bundle['location']}/zipball/master";

		parent::zipball($url, $bundle, $path);
	}

}
