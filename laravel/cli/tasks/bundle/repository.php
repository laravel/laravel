<?php namespace Laravel\CLI\Tasks\Bundle;

class Repository {

	/**
	 * The root of the Laravel bundle API.
	 *
	 * @var string
	 */
	protected $api = 'http://bundles.laravel.com/api/';

	/**
	 * Get the decoded JSON information for a bundle.
	 *
	 * @param  string|int  $bundle
	 * @return array
	 */
	public function get($bundle)
	{
		// The Bundle API will return a JSON string that we can decode and
		// pass back to the consumer. The decoded array will contain info
		// regarding the bundle's provider and location, as well as all
		// of the bundle's dependencies.
		$bundle = @file_get_contents($this->api.$bundle);

		return json_decode($bundle, true);
	}

}