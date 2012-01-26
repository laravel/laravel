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
		static::write('error', static::format($e));
	}

	/**
	 * Format a log friendly message from the given exception.
	 *
	 * @param  Exception  $e
	 * @return string
	 */
	protected static function format($e)
	{
		return $e->getMessage().' in '.$e->getFile().' on line '.$e->getLine();
	}

	/**
	 * Write a message to the log file.
	 *
	 * <code>
	 *		// Write an "error" messge to the log file
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
		$message = date('Y-m-d H:i:s').' '.Str::upper($type)." - {$message}".PHP_EOL;

		File::append(STORAGE_PATH.'logs/'.date('Y-m-d').'.log', $message);
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