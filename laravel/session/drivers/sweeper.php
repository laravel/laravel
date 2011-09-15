<?php namespace Laravel\Session\Drivers;

interface Sweeper {

	/**
	 * Delete all expired sessions from persistant storage.
	 *
	 * @param  int   $expiration
	 * @return void
	 */
	public function sweep($expiration);

}