<?php namespace Laravel;

class Error {

	/**
	 * Handle an exception and display the exception report.
	 *
	 * @param  Exception  $exception
	 * @param  bool       $trace
	 * @return void
	 */
	public static function exception($exception, $trace = true)
	{
		static::log($exception);

		ob_get_level() and ob_end_clean();

		// If detailed errors are enabled, we'll just format the exception into
		// a simple error message and display it on the screen. We don't use a
		// View in case the problem is in the View class.
		if (Config::get('error.detail'))
		{
			echo "<html><h2>Unhandled Exception</h2>
				  <h3>Message:</h3>
				  <pre>".$exception->getMessage()."</pre>
				  <h3>Location:</h3>
				  <pre>".$exception->getFile()." on line ".$exception->getLine()."</pre>";

			if ($trace)
			{
				echo "
				  <h3>Stack Trace:</h3>
				  <pre>".$exception->getTraceAsString()."</pre></html>";
			}
		}

		// If we're not using detailed error messages, we'll use the event
		// system to get the response that should be sent to the browser.
		// Using events gives the developer more freedom.
		else
		{
			$response = Event::first('500');

			return Response::prepare($response)->send();
		}

		exit(1);
	}

	/**
	 * Handle a native PHP error as an ErrorException.
	 *
	 * @param  int     $code
	 * @param  string  $error
	 * @param  string  $file
	 * @param  int     $line
	 * @return void
	 */
	public static function native($code, $error, $file, $line)
	{
		if (error_reporting() === 0) return;

		// For a PHP error, we'll create an ErrorExcepetion and then feed that
		// exception to the exception method, which will create a simple view
		// of the exception details for the developer.
		$exception = new \ErrorException($error, $code, 0, $file, $line);

		if (in_array($code, Config::get('error.ignore')))
		{
			return static::log($exception);
		}

		static::exception($exception);
	}

	/**
	 * Handle the PHP shutdown event.
	 *
	 * @return void
	 */
	public static function shutdown()
	{
		// If a fatal error occured that we have not handled yet, we will
		// create an ErrorException and feed it to the exception handler,
		// as it will not yet have been handled.
		$error = error_get_last();

		if ( ! is_null($error))
		{
			extract($error, EXTR_SKIP);

			static::exception(new \ErrorException($message, $type, 0, $file, $line), false);
		}
	}

	/**
	 * Log an exception.
	 *
	 * @param  Exception  $exception
	 * @return void
	 */
	public static function log($exception)
	{
		if (Config::get('error.log'))
		{
			call_user_func(Config::get('error.logger'), $exception);
		}
	}

}