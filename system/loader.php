<?php

/**
 * This function is registered on the auto-loader stack by the front controller.
 *
 * All namespace slashes will be replaced with directory slashes since all Laravel
 * system classes are organized using a namespace to directory convention.
 */
return function($class) {

	$file = strtolower(str_replace('\\', '/', $class));

	if (array_key_exists($class, $aliases = System\Config::get('aliases')))
	{
		return class_alias($aliases[$class], $class);
	}

	if (file_exists($path = BASE_PATH.$file.EXT))
	{
		require $path;
	}
	elseif (file_exists($path = APP_PATH.'models/'.$file.EXT))
	{
		require $path;
	}
	elseif (file_exists($path = APP_PATH.'libraries/'.$file.EXT))
	{
		require $path;
	}
	elseif (file_exists($path = APP_PATH.$file.EXT))
	{
		require $path;
	}

};