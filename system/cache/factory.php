<?php namespace System\Cache;

class Factory {

	/**
	 * Create a cache driver instance.
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

			case 'memcached':
				return new Driver\Memcached;

			case 'apc':
				return new Driver\APC;

			default:
				throw new \Exception("Cache driver [$driver] is not supported.");
		}
	}

}