<?php

/**
 * This function is registered on the auto-loader stack by the front controller.
 */
return function($class) {
	
	// ----------------------------------------------------------
	// Replace namespace slashes with directory slashes.
	// ----------------------------------------------------------
	$file = System\Str::lower(str_replace('\\', '/', $class));

	// ----------------------------------------------------------
	// Should the class be aliased?
	// ----------------------------------------------------------
	if (array_key_exists($class, $aliases = System\Config::get('application.aliases')))
	{
		return class_alias($aliases[$class], $class);
	}

	// ----------------------------------------------------------
	// Is the class a Laravel framework class?
	// ----------------------------------------------------------
	if (file_exists($path = BASE_PATH.$file.EXT))
	{
		require $path;
	}
	// ----------------------------------------------------------
	// Is the class in the application/models directory?
	// ----------------------------------------------------------
	elseif (file_exists($path = APP_PATH.'models/'.$file.EXT))
	{
		require $path;
	}
	// ----------------------------------------------------------
	// Is the class in the application/libraries directory?
	// ----------------------------------------------------------
	elseif (file_exists($path = APP_PATH.'libraries/'.$file.EXT))
	{
		require $path;
	}
	// ----------------------------------------------------------
	// Is the class anywhere in the application directory?
	// ----------------------------------------------------------
	elseif (file_exists($path = APP_PATH.$file.EXT))
	{
		require $path;
	}

};