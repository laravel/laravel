<?php namespace Laravel;

class Download extends Response {

	/**
	 * Create a new download response instance.
	 *
	 * <code>
	 *		// Return a download response for a given file
	 *		return new Download('path/to/image.jpg');
	 *
	 *		// Return a download response for a given file and assign a name
	 *		return new Download('path/to/image.jpg', 'you.jpg');
	 * </code>
	 *
	 * @param  string  $path
	 * @param  string  $name
	 */
	public function __construct($path, $name = null)
	{
		if (is_null($name)) $name = basename($path);

		parent::__construct(file_get_contents($path));

		$this->header('Content-Description', 'File Transfer');
		$this->header('Content-Type', File::mime(File::extension($path)));
		$this->header('Content-Disposition', 'attachment; filename="'.$name.'"');
		$this->header('Content-Transfer-Encoding', 'binary');
		$this->header('Expires', 0);
		$this->header('Cache-Control', 'must-revalidate, post-check=0, pre-check=0');
		$this->header('Pragma', 'public');
		$this->header('Content-Length', filesize($path));
	}

}