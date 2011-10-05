<?php namespace Laravel\Session\Transporters;

/**
 * Session transporters are responsible for getting the session identifier
 * to the client. This can be done via cookies or some other means.
 */
interface Transporter {

	/**
	 * Get the session identifier for the request.
	 *
	 * @param  array   $config
	 * @return string
	 */
	public function get($config);

	/**
	 * Store the session identifier for the request.
	 *
	 * @param  string  $id
	 * @param  array   $config
	 * @return void
	 */
	public function put($id, $config);

}