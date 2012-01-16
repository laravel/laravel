<?php namespace Laravel;

class Error {

	/**
	 * Handle an exception and display the exception report.
	 *
	 * @param  Exception  $exception
	 * @return void
	 */
	public static function exception($exception)
	{
		static::log($exception);

		// If detailed errors are enabled, we'll just format the exception into
		// a simple error message and display it on the screen. We don't use a
		// View in case the problem is in the View class itself so we can not
		// run into a white screen of death situation.
		if (Config::get('error.detail'))
		{
			echo "<html><h2>Unhandled Exception</h2>
				  <h3>Message:</h3>
				  <pre>".$exception->getMessage()."</pre>
				  <h3>Location:</h3>
				  <pre>".$exception->getFile()." on line ".$exception->getLine()."</pre>
				  <h3>Stack Trace:</h3>
				  <pre>".$exception->getTraceAsString()."</pre></html>";
		}
		else
		{
			Response::error('500')->send();
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
		// of the exception details. The ErrorException class is built-in to
		// PHP for converting native errors to Exceptions.
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
		// as it will not have been handled by the error handler.
		if ( ! is_null($error = error_get_last()))
		{
			extract($error, EXTR_SKIP);

			static::exception(new \ErrorException($message, $type, 0, $file, $line));
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