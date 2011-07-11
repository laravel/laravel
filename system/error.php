<?php namespace System;

class Error {

	/**
	 * Error levels and descriptions.
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
		if (ob_get_level() > 0)
		{
			ob_clean();
		}

		$severity = (array_key_exists($e->getCode(), static::$levels)) ? static::$levels[$e->getCode()] : $e->getCode();

		$file = static::file($e);

		$message = rtrim($e->getMessage(), '.');

		if (Config::get('error.log'))
		{
			Log::error($message.' in '.$e->getFile().' on line '.$e->getLine());
		}

		static::show($e, $severity, $message, $file);

		exit(1);
	}

	/**
	 * Get the path to the file in which an exception occured.
	 *
	 * @param  Exception  $e
	 * @return string
	 */
	private static function file($e)
	{
		if (strpos($e->getFile(), 'view.php') !== false and strpos($e->getFile(), "eval()'d code") !== false)
		{
			return APP_PATH.'views/'.View::$last.EXT;
		}

		return $e->getFile();
	}

	/**
	 * Show the error view.
	 *
	 * @param  Exception  $e
	 * @param  string     $severity
	 * @param  string     $message
	 * @param  string     $file
	 * @return void
	 */
	private static function show($e, $severity, $message, $file)
	{
		if (Config::get('error.detail'))
		{
			$view = View::make('exception')
                                   ->bind('severity', $severity)
                                   ->bind('message', $message)
                                   ->bind('file', $file)
                                   ->bind('line', $e->getLine())
                                   ->bind('trace', $e->getTraceAsString());
			
			Response::make($view, 500)->send();
		}
		else
		{
			Response::make(View::make('error/500'), 500)->send();
		}
	}

}