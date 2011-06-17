<?php namespace System;

class Log {

	/**
	 * Write an info message to the log.
	 *
	 * @param  string  $message
	 * @return void
	 */
	public static function info($message)
	{
		static::write('Info', $message);
	}

	/**
	 * Write a debug message to the log.
	 *
	 * @param  string  $message
	 * @return void
	 */
	public static function debug($message)
	{
		static::write('Debug', $message);
	}

	/**
	 * Write an error message to the logs.
	 *
	 * @param  string  $message
	 * @return void
	 */
	public static function error($message)
	{
		static::write('Error', $message);
	}

	/**
	 * Write a message to the logs.
	 *
	 * @param  string  $type
	 * @param  string  $message
	 * @return void
	 */
	public static function write($type, $message)
	{
		// -----------------------------------------------------
		// Create the yearly and monthly directories if needed.
		// -----------------------------------------------------
		static::make_directory($directory = APP_PATH.'logs/'.date('Y'));
		static::make_directory($directory .= '/'.date('m'));

		// -----------------------------------------------------
		// Each day has its own log file.
		// -----------------------------------------------------
		$file = $directory.'/'.date('d').EXT;

		// -----------------------------------------------------
		// Append to the log file and set the permissions.
		// -----------------------------------------------------
		file_put_contents($file, date('Y-m-d H:i:s').' '.$type.' - '.$message.PHP_EOL, LOCK_EX | FILE_APPEND);

		chmod($file, 0666);
	}

	/**
	 * Create a log directory.
	 *
	 * If the directory already exists, no action will be taken.
	 *
	 * @param  string  $directory
	 * @return void
	 */
	private static function make_directory($directory)
	{
		if ( ! is_dir($directory))
		{
			mkdir($directory, 02777);
			chmod($directory, 02777);
		}
	}

}