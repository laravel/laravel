<?php namespace System;

class Asset {

	/**
	 * All of the asset containers.
	 *
	 * @var array
	 */
	public static $containers = array();

	/**
	 * Get an asset container instance.
	 *
	 * @param  string     $container
	 * @return Container
	 */
	public static function container($container = 'default')
	{
		if ( ! isset(static::$containers[$container]))
		{
			static::$containers[$container] = new Asset_Container($container);
		}

		return static::$containers[$container];
	}

	/**
	 * Magic Method for calling methods on the default Asset container.
	 */
	public static function __callStatic($method, $parameters)
	{
		return call_user_func_array(array(static::container(), $method), $parameters);
	}

}