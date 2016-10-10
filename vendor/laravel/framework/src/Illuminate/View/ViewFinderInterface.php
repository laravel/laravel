<?php namespace Illuminate\View;

interface ViewFinderInterface {

	/**
	 * Get the fully qualified location of the view.
	 *
	 * @param  string  $view
	 * @return string
	 */
	public function find($view);

	/**
	 * Add a location to the finder.
	 *
	 * @param  string  $location
	 * @return void
	 */
	public function addLocation($location);

	/**
	 * Add a namespace hint to the finder.
	 *
	 * @param  string  $namespace
	 * @param  string  $hint
	 * @return void
	 */
	public function addNamespace($namespace, $hint);

	/**
	 * Add a valid view extension to the finder.
	 *
	 * @param  string  $extension
	 * @return void
	 */
	public function addExtension($extension);

}
