<?php namespace Laravel\Session;

interface Sweeper {

	/**
	 * Delete all expired sessions.
	 *
	 * @param  int   $expiration
	 * @return void
	 */
	public function sweep($expiration);

}