<?php namespace Laravel;

class File {

	/**
	 * All of the MIME types understood by the manager.
	 *
	 * @var array
	 */
	private $mimes;

	/**
	 * Create a new file engine instance.
	 *
	 * @param  array  $mimes
	 * @return void
	 */
	public function __construct($mimes)
	{
		$this->mimes = $mimes;
	}

	/**
	 * Determine if a file exists.
	 *
	 * @param  string  $path
	 * @return bool
	 */
	public function exists($path)
	{
		return file_exists($path);
	}

	/**
	 * Get the contents of a file.
	 *
	 * @param  string  $path
	 * @return string
	 */
	public function get($path)
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
	public function put($path, $data)
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
	public function append($path, $data)
	{
		return file_put_contents($path, $data, LOCK_EX | FILE_APPEND);
	}

	/**
	 * Delete a file.
	 *
	 * @param  string  $path
	 * @return void
	 */
	public function delete($path)
	{
		@unlink($path);
	}

	/**
	 * Extract the file extension from a file path.
	 * 
	 * @param  string  $path
	 * @return string
	 */
	public function extension($path)
	{
		return pathinfo($path, PATHINFO_EXTENSION);
	}

	/**
	 * Get the file type of a given file.
	 *
	 * @param  string  $path
	 * @return string
	 */
	public function type($path)
	{
		return filetype($path);
	}

	/**
	 * Get the file size of a given file.
	 *
	 * @param  string  $file
	 * @return int
	 */
	public function size($path)
	{
		return filesize($path);
	}

	/**
	 * Get the file's last modification time.
	 *
	 * @param  string  $path
	 * @return int
	 */
	public function modified($path)
	{
		return filemtime($path);
	}

	/**
	 * Move an uploaded file to permanenet storage.
	 *
	 * @param  string  $key
	 * @param  string  $path
	 * @param  array   $files
	 * @return bool
	 */
	public function upload($key, $path, $files)
	{
		return move_uploaded_file($files[$key]['tmp_name'], $path);
	}

	/**
	 * Get a file MIME type by extension.
	 *
	 * If the MIME type can't be determined, "application/octet-stream" will be returned.
	 *
	 * <code>
	 *		// Returns 'application/x-tar'
	 *		$mime = File::mime('path/to/file.tar');
	 * </code>
	 *
	 * @param  string  $extension
	 * @param  string  $default
	 * @return string
	 */
	public function mime($extension, $default = 'application/octet-stream')
	{
		if ( ! array_key_exists($extension, $this->mimes)) return $default;

		return (is_array($this->mimes[$extension])) ? $this->mimes[$extension][0] : $this->mimes[$extension];
	}

	/**
	 * Determine if a file is a given type.
	 *
	 * The Fileinfo PHP extension will be used to determine the MIME type of the file.
	 *
	 * <code>
	 *		// Determine if a file is a JPG image
	 *		$image = File::is('jpg', 'path/to/image.jpg');
	 *
	 *		// Determine if a file is any one of an array of types
	 *		$image = File::is(array('jpg', 'png', 'gif'), 'path/to/image.jpg');
	 * </code>
	 *
	 * @param  array|string  $extension
	 * @param  string        $path
	 * @return bool
	 */
	public function is($extensions, $path)
	{
		foreach ((array) $extensions as $extension)
		{
			$mime = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $path);

			if (isset($this->mimes[$extension]) and in_array((array) $this->mimes[$extension])) return true;
		}

		return false;
	}

}