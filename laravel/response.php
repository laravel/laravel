<?php namespace Laravel;

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
	public $status = 200;

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
	public static $statuses = array(
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
	 * @param  array  $headers
	 * @return void
	 */
	public function __construct($content, $status = 200, $headers = array())
	{
		$this->status = $status;
		$this->content = $content;
		$this->headers = $headers;
	}

	/**
	 * Create a new response instance.
	 *
	 * <code>
	 *		// Create a response instance with string content
	 *		return Response::make(json_encode($user));
	 *
	 *		// Create a response instance with a given status
	 *		return Response::make('Not Found', 404);
	 *
	 *		// Create a response with some custom headers
	 *		return Response::make(json_encode($user), 200, array('header' => 'value'));
	 * </code>
	 *
	 * @param  mixed     $content
	 * @param  int       $status
	 * @param  array     $headers
	 * @return Response
	 */
	public static function make($content, $status = 200, $headers = array())
	{
		return new static($content, $status, $headers);
	}

	/**
	 * Create a new response instance containing a view.
	 *
	 * <code>
	 *		// Create a response instance with a view
	 *		return Response::view('home.index');
	 *
	 *		// Create a response instance with a view and data
	 *		return Response::view('home.index', array('name' => 'Taylor'));
	 * </code>
	 *
	 * @param  string    $view
	 * @param  array     $data
	 * @return Response
	 */
	public static function view($view, $data = array())
	{
		return new static(View::make($view, $data));
	}

	/**
	 * Create a new error response instance.
	 *
	 * The response status code will be set using the specified code.
	 *
	 * The specified error should match a view in your views/error directory.
	 *
	 * <code>
	 *		// Create a 404 response
	 *		return Response::error('404');
	 *
	 *		// Create a 404 response with data
	 *		return Response::error('404', array('message' => 'Not Found'));
	 * </code>
	 *
	 * @param  int       $code
	 * @param  array     $data
	 * @return Response
	 */
	public static function error($code, $data = array())
	{
		return new static(View::make('error.'.$code, $data), $code);
	}

	/**
	 * Create a new download response instance.
	 *
	 * <code>
	 *		// Create a download response to a given file
	 *		return Response::download('path/to/file.jpg');
	 *
	 *		// Create a download response with a given file name
	 *		return Response::download('path/to/file.jpg', 'your_file.jpg');
	 * </code>
	 *
	 * @param  string    $path
	 * @param  string    $name
	 * @param  array     $headers
	 * @return Response
	 */
	public static function download($path, $name = null, $headers = array())
	{
		if (is_null($name)) $name = basename($path);

		$headers = array_merge(array(
			'Content-Description'       => 'File Transfer',
			'Content-Type'              => File::mime(File::extension($path)),
			'Content-Disposition'       => 'attachment; filename="'.$name.'"',
			'Content-Transfer-Encoding' => 'binary',
			'Expires'                   => 0,
			'Cache-Control'             => 'must-revalidate, post-check=0, pre-check=0',
			'Pragma'                    => 'public',
			'Content-Length'            => File::size($path),
		), $headers);

		return new static(File::get($path), 200, $headers);
	}

	/**
	 * Prepare a response from the given value.
	 *
	 * If the value is not a response, it will be converted into a response
	 * instance and the content will be cast to a string.
	 *
	 * @param  mixed     $response
	 * @return Response
	 */
	public static function prepare($response)
	{
		if ( ! $response instanceof Response) $response = new static($response);

		// We'll need to force the response to be a string before closing the session,
		// since the developer may be using the session within a view, and we can't
		// age the flash data until the view is rendered.
		//
		// Since this method is used by both the Route and Controller classes, it is
		// a convenient spot to cast the application response to a string before it
		// is returned to the main request handler.
		$response->render();

		return $response;
	}

	/**
	 * Convert the content of the Response to a string and return it.
	 *
	 * @return string
	 */
	public function render()
	{
		if (is_object($this->content) and method_exists($this->content, '__toString'))
		{
			$this->content = $this->content->__toString();
		}
		else
		{
			$this->content = (string) $this->content;
		}

		return $this->content;
	}

	/**
	 * Send the headers and content of the response to the browser.
	 *
	 * @return void
	 */
	public function send()
	{
		if ( ! headers_sent()) $this->send_headers();

		echo (string) $this->content;
	}

	/**
	 * Send all of the response headers to the browser.
	 *
	 * @return void
	 */
	public function send_headers()
	{
		// If the server is using FastCGI, we need to send a slightly different
		// protocol and status header than we normally would. Otherwise it will
		// not call any custom scripts setup to handle 404 responses.
		//
		// The status header will contain both the code and the status message,
		// such as "OK" or "Not Found". For typical servers, the HTTP protocol
		// will also be included with the status.
		if (isset($_SERVER['FCGI_SERVER_VERSION']))
		{
			header('Status: '.$this->status.' '.$this->message());
		}
		else
		{
			header(Request::protocol().' '.$this->status.' '.$this->message());
		}

		// If the content type was not set by the developer, we will set the
		// header to a default value that indicates to the browser that the
		// response is HTML and that it uses the default encoding.
		if ( ! isset($this->headers['Content-Type']))
		{
			$encoding = Config::get('application.encoding');

			$this->header('Content-Type', 'text/html; charset='.$encoding);
		}

		// Once the framework controlled headers have been sentm, we can
		// simply iterate over the developer's headers and send each one
		// back to the browser for the response.
		foreach ($this->headers as $name => $value)
		{
			header("{$name}: {$value}", true);
		}
	}

	/**
	 * Get the status code message for the response.
	 *
	 * @return string
	 */
	public function message()
	{
		return static::$statuses[$this->status];
	}

	/**
	 * Add a header to the array of response headers.
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

	/**
	 * Set the response status code.
	 *
	 * @param  int       $status
	 * @return Response
	 */
	public function status($status)
	{
		$this->status = $status;
		return $this;
	}

}