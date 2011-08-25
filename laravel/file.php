<?php namespace Laravel;

class File {

	/**
	 * Get the contents of a file.
	 *
	 * @param  string  $path
	 * @return string
	 */
	public static function get($path)
	{
		return file_get_contents($path);
	}

	/**
	 * Write to a file.
	 *
	 * @param  string  $path
	 * @param  string  $data
	 * @return int
	 */
	public static function put($path, $data)
	{
		return file_put_contents($path, $data, LOCK_EX);
	}

	/**
	 * Append to a file.
	 *
	 * @param  string  $path
	 * @param  string  $data
	 * @return int
	 */
	public static function append($path, $data)
	{
		return file_put_contents($path, $data, LOCK_EX | FILE_APPEND);
	}

	/**
	 * Extract the file extension from a file path.
	 * 
	 * @param  string  $path
	 * @return string
	 */
	public static function extension($path)
	{
		return pathinfo($path, PATHINFO_EXTENSION);
	}

	/**
	 * Get the lines surrounding a given line in a file.
	 *
	 * The amount of padding with which to surround the line may also be specified.
	 *
	 * <code>
	 *		// Get lines 10 - 20 of the "routes.php" file
	 *		$lines = File::snapshot(APP_PATH.'routes'.EXT, 15, 5);
	 * </code>
	 *
	 * @param  string  $path
	 * @param  int     $line
	 * @param  int     $padding
	 * @return array
	 */
	public static function snapshot($path, $line, $padding = 5)
	{
		if ( ! file_exists($path)) return array();

		$file = file($path, FILE_IGNORE_NEW_LINES);

		array_unshift($file, '');

		$start = $line - $padding;

		$length = ($line - $start) + $padding + 1;

		return array_slice($file, ($start > 0) ? $start : 0, ($length > 0) ? $length : 0, true);
	}

	/**
	 * Get a file MIME type by extension.
	 *
	 * Any extension in the MIMEs configuration file may be passed to the method.
	 *
	 * <code>
	 *		// Returns "application/x-tar"
	 *		$mime = File::mime('tar');
	 * </code>
	 *
	 * @param  string  $extension
	 * @param  string  $default
	 * @return string
	 */
	public static function mime($extension, $default = 'application/octet-stream')
	{
		$mimes = Config::get('mimes');

		if ( ! array_key_exists($extension, $mimes)) return $default;

		return (is_array($mimes[$extension])) ? $mimes[$extension][0] : $mimes[$extension];
	}

	/**
	 * Determine if a file is a given type.
	 *
	 * The Fileinfo PHP extension will be used to determine the MIME type of the file. Any extension
	 * in the MIMEs configuration file may be passed as a type.
	 *
	 * <code>
	 *		// Determine if the file is a JPG image
	 *		$image = File::is('jpg', 'path/to/image.jpg');
	 * </code>
	 *
	 * @param  string  $extension
	 * @param  string  $path
	 * @return bool
	 */
	public static function is($extension, $path)
	{
		$mimes = Config::get('mimes');

		if ( ! array_key_exists($extension, $mimes))
		{
			throw new \Exception("File extension [$extension] is unknown. Cannot determine file type.");
		}

		$mime = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $path);

		return (is_array($mimes[$extension])) ? in_array($mime, $mimes[$extension]) : $mime === $mimes[$extension];
	}

}