<?php namespace Laravel;

/**
 * The Proxy class, like the File class, is primarily intended to get rid of
 * the testability problems introduced by PHP's global functions.
 *
 * For instance, the APC cache driver calls the APC global functions. Instead of
 * calling those functions directory in the driver, we inject a Proxy instance into
 * the class, which allows us to stub the global functions.
 */
class Proxy {

	/**
	 * Magic Method for calling any global function.
	 */
	public function __call($method, $parameters)
	{
		return call_user_func_array($method, $parameters);
	}

}