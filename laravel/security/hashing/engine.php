<?php namespace Laravel\Security\Hashing;

interface Engine {

	/**
	 * Perform a one-way hash on a string.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public function hash($value);

	/**
	 * Determine if an unhashed value matches a given hash.
	 *
	 * @param  string  $value
	 * @param  string  $hash
	 * @return bool
	 */
	public function check($value, $hash);

}