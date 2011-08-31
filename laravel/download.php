<?php namespace Laravel;

class Download_Facade extends Facade { public static $resolve = 'download'; }

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
	 * @return Response
	 */
	public function of($path, $name = null)
	{
		if (is_null($name)) $name = basename($path);

		$response = parent::__construct($this->file->get($path));

		$response->header('Content-Description', 'File Transfer');
		$response->header('Content-Type', $this->file->mime($this->file->extension($path)));
		$response->header('Content-Disposition', 'attachment; filename="'.$name.'"');
		$response->header('Content-Transfer-Encoding', 'binary');
		$response->header('Expires', 0);
		$response->header('Cache-Control', 'must-revalidate, post-check=0, pre-check=0');
		$response->header('Pragma', 'public');
		$response->header('Content-Length', $this->file->size($path));

		return $response;
	}

}