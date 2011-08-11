<?php namespace System;

class Error {

	/**
	 * Human-readable error levels and descriptions.
	 *
	 * @var array
	 */
	public static $levels = array(
		0                  => 'Error',
		E_ERROR            => 'Error',
		E_WARNING          => 'Warning',
		E_PARSE            => 'Parsing Error',
		E_NOTICE           => 'Notice',
		E_CORE_ERROR       => 'Core Error',
		E_CORE_WARNING     => 'Core Warning',
		E_COMPILE_ERROR    => 'Compile Error',
		E_COMPILE_WARNING  => 'Compile Warning',
		E_USER_ERROR       => 'User Error',
		E_USER_WARNING     => 'User Warning',
		E_USER_NOTICE      => 'User Notice',
		E_STRICT           => 'Runtime Notice'
	);

	/**
	 * Handle an exception.
	 *
	 * @param  Exception  $e
	 * @return void
	 */
	public static function handle($e)
	{
		// Clear the output buffer so nothing is sent to the browser except the error
		// message. This prevents any views that have already been rendered from being
		// in an incomplete or erroneous state.
		if (ob_get_level() > 0) ob_clean();

		$severity = (array_key_exists($e->getCode(), static::$levels)) ? static::$levels[$e->getCode()] : $e->getCode();

		$message = rtrim($e->getMessage(), '.').' in '.str_replace(array(APP_PATH, SYS_PATH), array('APP_PATH/', 'SYS_PATH/'), $e->getFile()).' on line '.$e->getLine().'.';

		if (Config::get('error.log'))
		{
			call_user_func(Config::get('error.logger'), $severity, $message, $e->getTraceAsString());
		}

		static::show($e, $severity, $message);

		exit(1);
	}

	/**
	 * Show the error view.
	 *
	 * @param  Exception  $e
	 * @param  string     $severity
	 * @param  string     $message
	 * @return void
	 */
	private static function show($e, $severity, $message)
	{
		if (Config::get('error.detail'))
		{
			$view = View::make('error/exception')
                                   ->bind('severity', $severity)
                                   ->bind('message', $message)
                                   ->bind('line', $e->getLine())
                                   ->bind('trace', $e->getTraceAsString())
                                   ->bind('contexts', static::context($e->getFile(), $e->getLine()));

			Response::make($view, 500)->send();
		}
		else
		{
			Response::error('500')->send();
		}
	}

	/**
	 * Get the code surrounding a given line in a file.
	 *
	 * @param  string  $path
	 * @param  int     $line
	 * @param  int     $padding
	 * @return string
	 */
	private static function context($path, $line, $padding = 5)
	{
		if (file_exists($path))
		{
			$file = file($path, FILE_IGNORE_NEW_LINES);

			array_unshift($file, '');
		
			if (($start = $line - $padding) < 0) $start = 0;

			if (($length = ($line - $start) + $padding + 1) < 0) $length = 0;

			return array_slice($file, $start, $length, true);
		}

		return array();
	}

}