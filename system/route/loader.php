<?php namespace System\Route;

class Loader {

	/**
	 * Load the route file based on the first segment of the URI.
	 *
	 * @param  string  $uri
	 * @return void
	 */
	public static function load($uri)
	{
		// --------------------------------------------------------------
		// If a single route file is being used, return it.
		// --------------------------------------------------------------
		if ( ! is_dir(APP_PATH.'routes'))
		{
			return require APP_PATH.'routes'.EXT;			
		}

		if ( ! file_exists(APP_PATH.'routes/home'.EXT))
		{
			throw new \Exception("A [home] route file is required when using a route directory.");					
		}

		// --------------------------------------------------------------
		// If the request is to the root, load the "home" routes file.
		// --------------------------------------------------------------
		if ($uri == '/')
		{
			return require APP_PATH.'routes/home'.EXT;
		}
		// --------------------------------------------------------------
		// Load the route file matching the first segment of the URI.
		// --------------------------------------------------------------
		else
		{
			$segments = explode('/', trim($uri, '/'));

			if ( ! file_exists(APP_PATH.'routes/'.$segments[0].EXT))
			{
				return require APP_PATH.'routes/home'.EXT;
			}

			return array_merge(require APP_PATH.'routes/'.$segments[0].EXT, require APP_PATH.'routes/home'.EXT);
		}
	}

}