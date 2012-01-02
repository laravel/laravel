<?php namespace Laravel\CLI\Bundle;

class Repository {

	/**
	 * Get the decoded JSON information for a bundle.
	 *
	 * @param  string  $bundle
	 * @return array
	 */
	public function get($bundle)
	{
		return array('name' => 'mongor', 'repository' => 'mikelbring/mongor', 'provider' => 'github');
	}

}