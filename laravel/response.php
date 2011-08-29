<?php namespace Laravel;

class Response_Factory {

	/**
	 * The view factory instance.
	 *
	 * @var View_Factory
	 */
	private $view;

	/**
	 * The file manager instance.
	 *
	 * @var File
	 */
	private $file;

	/**
	 * Create a new response factory instance.
	 *
	 * @param  File  $file
	 * @return void
	 */
	public function __construct(View_Factory $view, File $file)
	{
		$this->view = $view;
		$this->file = $file;
	}

	/**
	 * Create a new response instance.
	 *
	 * @param  mixed     $content
	 * @param  int       $status
	 * @return Response
	 */
	public function make($content, $status = 200)
	{
		return new Response($content, $status);
	}

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
	 * @param  string    $path
	 * @param  string    $name
	 * @return Response
	 */
	public function download($path, $name = null)
	{
		if (is_null($name)) $name = basename($path);

		$response = new Response($this->file->get($path));

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

	/**
	 * Create a new error response instance.
	 *
	 * The response status code will be set using the specified code.
	 *
	 * Note: The specified error code should correspond to a view in your views/error directory.
	 *
	 * <code>
	 *		// Return a 404 error response
	 *		return new Error('404');
	 * </code>
	 *
	 * @param  int       $code
	 * @param  array     $data
	 * @return void
	 */
	public function error($code, $data = array())
	{
		return new Response($this->view->make('error/'.$code, $data), $code);
	}

}

class Response {

	/**
	 * The content of the response.
	 *
	 * @var mixed
	 */
	public $content;

	/**
	 * The HTTP status code of the response.
	 *
	 * @var int
	 */
	public $status;

	/**
	 * The response headers.
	 *
	 * @var array
	 */
	public $headers = array();

	/**
	 * HTTP status codes.
	 *
	 * @var array
	 */
	private $statuses = array(
		100 => 'Continue',
		101 => 'Switching Protocols',
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',
		207 => 'Multi-Status',
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found',
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		307 => 'Temporary Redirect',
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Timeout',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large',
		414 => 'Request-URI Too Long',
		415 => 'Unsupported Media Type',
		416 => 'Requested Range Not Satisfiable',
		417 => 'Expectation Failed',
		422 => 'Unprocessable Entity',
		423 => 'Locked',
		424 => 'Failed Dependency',
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Timeout',
		505 => 'HTTP Version Not Supported',
		507 => 'Insufficient Storage',
		509 => 'Bandwidth Limit Exceeded'
	);

	/**
	 * Create a new response instance.
	 *
	 * @param  mixed  $content
	 * @param  int    $status
	 * @return void
	 */
	public function __construct($content, $status = 200)
	{
		$this->content = $content;
		$this->status = $status;
	}	

	/**
	 * Get the evaluated string contents of the response.
	 *
	 * @return string
	 */
	public function render()
	{
		return ($this->content instanceof View) ? $this->content->render() : (string) $this->content;
	}

	/**
	 * Send the response to the browser.
	 *
	 * All of the response header will be sent to the browser first, followed by the content
	 * of the response instance, which will be evaluated and rendered by the render method.
	 *
	 * @return void
	 */
	public function send()
	{
		if ( ! isset($this->headers['Content-Type'])) $this->header('Content-Type', 'text/html; charset=utf-8');

		if ( ! headers_sent()) $this->send_headers();

		echo $this->render();
	}

	/**
	 * Send the response headers to the browser.
	 *
	 * @return void
	 */
	public function send_headers()
	{
		$protocol = (isset($_SERVER['SERVER_PROTOCOL'])) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1';

		header($protocol.' '.$this->status.' '.$this->statuses[$this->status]);

		foreach ($this->headers as $name => $value)
		{	
			header($name.': '.$value, true);
		}
	}

	/**
	 * Add a header to the response.
	 *
	 * <code>
	 *		// Add a "location" header to a response
	 *		$response->header('Location', 'http://google.com');
	 * </code>
	 *
	 * @param  string    $name
	 * @param  string    $value
	 * @return Response
	 */
	public function header($name, $value)
	{
		$this->headers[$name] = $value;
		return $this;
	}

}