<?php namespace System\Session;

class Factory {

	/**
	 * Create a session driver instance.
	 *
	 * @param  string  $driver
	 * @return Driver
	 */
	public static function make($driver)
	{
		switch ($driver)
		{
			case 'file':
				return new Driver\File;

			case 'db':
				return new Driver\DB;

			case 'memcached':
				return new Driver\Memcached;

			default:
				throw new \Exception("Session driver [$driver] is not supported.");
		}
	}

}