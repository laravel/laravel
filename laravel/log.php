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
	 * @param  string  $type
	 * @param  string  $message
	 * @return void
	 */
	public static function write($type, $message)
	{
		$type = Str::upper($type);

		$message = date('Y-m-d H:i:s')." {$type} - {$message}".PHP_EOL;

		File::append(STORAGE_PATH.'logs/'.date('Y-m').'.log', $message);
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