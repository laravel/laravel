<?php namespace Laravel\Routing;

interface Destination {

	/**
	 * Get an array of filter names defined for the destination.
	 *
	 * @param  string  $name
	 * @return array
	 */
	public function filters($name);

}