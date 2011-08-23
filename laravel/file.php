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

		if (($start = $line - $padding) < 0) $start = 0;

		if (($length = ($line - $start) + $padding + 1) < 0) $length = 0;

		return array_slice($file, $start, $length, true);
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

		if (array_key_exists($extension, $mimes))
		{
			return (is_array($mimes[$extension])) ? $mimes[$extension][0] : $mimes[$extension];
		}

		return $default;
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

	/**
	 * Create a response that will force a file to be downloaded.
	 *
	 * @param  string  $path
	 * @param  string  $name
	 * @return Response
	 */
	public static function download($path, $name = null)
	{
		if (is_null($name))
		{
			$name = basename($path);
		}

		$response = Response::make(static::get($path));

		$response->header('Content-Description', 'File Transfer');
		$response->header('Content-Type', static::mime(static::extension($path)));
		$response->header('Content-Disposition', 'attachment; filename="'.$name.'"');
		$response->header('Content-Transfer-Encoding', 'binary');
		$response->header('Expires', 0);
		$response->header('Cache-Control', 'must-revalidate, post-check=0, pre-check=0');
		$response->header('Pragma', 'public');
		$response->header('Content-Length', filesize($path));

		return $response;
	}

}