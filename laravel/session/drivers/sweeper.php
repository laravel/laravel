<?php namespace Laravel\Session\Drivers;

/**
 * The Sweeper interface can be implemented to provide a method of sweeping
 * expired sessions.
 *
 * @package  	Laravel
 * @author  	Taylor Otwell <taylorotwell@gmail.com>
 * @copyright  	2012 Taylor Otwell
 * @license 	MIT License <http://www.opensource.org/licenses/mit>
 */
interface Sweeper {

	/**
	 * Delete all expired sessions from persistant storage.
	 *
	 * @param  int   $expiration
	 * @return void
	 */
	public function sweep($expiration);

}