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
	protected $status;

	/**
	 * The response headers.
	 *
	 * @var array
	 */
	protected $headers = array();

	/**
	 * HTTP status codes.
	 *
	 * @var array
	 */
	protected $statuses = array(
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
	 *		return Respone::make(json_encode($user), 200, array('content-type' => 'application/json'));
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
		return new static(IoC::container()->core('view')->make($view, $data));
	}

	/**
	 * Create a new response instance containing a named view.
	 *
	 * <code>
	 *		// Create a response with the "layout" named view
	 *		return Response::of('layout');
	 *
	 *		// Create a response with the "layout" named view and data
	 *		return Response::of('layout', array('name' => 'Taylor'));
	 * </code>
	 *
	 * @param  string    $name
	 * @param  array     $data
	 * @return Response
	 */
	public static function of($name, $data = array())
	{
		return new static(IoC::container()->core('view')->of($name, $data));
	}

	/**
	 * Create a new error response instance.
	 *
	 * The response status code will be set using the specified code.
	 *
	 * Note: The specified error code should correspond to a view in your views/error directory.
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
		return new static(IoC::container()->core('view')->make('error/'.$code, $data), $code);
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
	 * All of the response header will be sent to the browser first, followed by
	 * the content of the response instance, which will be evaluated and rendered
	 * by the render method.
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
	 *		// Add a header to a response instance
	 *		return Response::make('foo')->header('content-type', 'application/json');
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

	/**
	 * Magic Method for handling the dynamic creation of Responses containing named views.
	 *
	 * <code>
	 *		// Create a response instance with the "layout" named view
	 *		return Response::of_layout();
	 *
	 *		// Create a response instance with a named view and data
	 *		return Response::of_layout(array('name' => 'Taylor'));
	 * </code>
	 */
	public static function __callStatic($method, $parameters)
	{
		if (strpos($method, 'of_') === 0)
		{
			return static::with(substr($method, 3), Arr::get($parameters, 0, array()));
		}
	}

}