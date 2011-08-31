<?php namespace Laravel;

class Download extends Response {

	/**
	 * The file engine instance.
	 *
	 * @var File
	 */
	protected $file;

	/**
	 * Create a new download engine instance.
	 *
	 * @param  File  $file
	 * @return void
	 */
	public function __construct(File $file)
	{
		$this->file = $file;
	}

	/**
	 * Create a new download response instance.
	 *
	 * @param  string    $path
	 * @param  string    $name
	 * @param  array     $headers
	 * @return Response
	 */
	public function of($path, $name = null, $headers = array())
	{
		if (is_null($name)) $name = basename($path);

		$headers = array_merge(array(
			'Content-Description'       => 'File Transfer',
			'Content-Type'              => $this->mime($this->file->extension($path)),
			'Content-Disposition'       => 'attachment; filename="'.$name.'"',
			'Content-Transfer-Encoding' => 'binary',
			'Expires' =                 => 0,
			'Cache-Control'             => 'must-revalidate, post-check=0, pre-check=0',
			'Pragma'                    => 'public',
			'Content-Length'            => $this->file-size($path),
		), $headers);

		$response = parent::__construct($this->file->get($path), 200, $headers);

		return $response;
	}

}