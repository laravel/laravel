<?php namespace Laravel;

class Log {

	/**
	 * Log an exception to the log file.
	 *
	 * @param  Exception  $e
	 * @return void
	 */
	public static function exception($e)
	{
		static::write('error', static::exception_line($e));
	}

	/**
	 * Format a log friendly message from the given exception.
	 *
	 * @param  Exception  $e
	 * @return string
	 */
	protected static function exception_line($e)
	{
		return $e->getMessage().' in '.$e->getFile().' on line '.$e->getLine();
	}

	/**
	 * Write a message to the log file.
	 *
	 * <code>
	 *		// Write an "error" message to the log file
	 *		Log::write('error', 'Something went horribly wrong!');
	 *
	 *		// Write an "error" message using the class' magic method
	 *		Log::error('Something went horribly wrong!');
	 * </code>
	 *
	 * @param  string  $type
	 * @param  string  $message
	 * @return void
	 */
	public static function write($type, $message)
	{
		// If there is a listener for the log event, we'll delegate the logging
		// to the event and not write to the log files. This allows for quick
		// swapping of log implementations for debugging.
		if (Event::listeners('laravel.log'))
		{
			Event::fire('laravel.log', array($type, $message));
		}

		// If there aren't listeners on the log event, we'll just write to the
		// log files using the default conventions, writing one log file per
		// day so the files don't get too crowded.
		else
		{
			$message = static::format($type, $message);

			File::append(path('storage').'logs/'.date('Y-m-d').'.log', $message);
		}
	}

	/**
	 * Format a log message for logging.
	 *
	 * @param  string  $type
	 * @param  string  $message
	 * @return string
	 */
	protected static function format($type, $message)
	{
		return date('Y-m-d H:i:s').' '.Str::upper($type)." - {$message}".PHP_EOL;
	}

	/**
	 * Dynamically write a log message.
	 *
	 * <code>
	 *		// Write an "error" message to the log file
	 *		Log::error('This is an error!');
	 *
	 *		// Write a "warning" message to the log file
	 *		Log::warning('This is a warning!');
	 * </code>
	 */
	public static function __callStatic($method, $parameters)
	{
		static::write($method, $parameters[0]);
	}

}