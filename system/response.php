<?php namespace System;

class Response {

	/**
	 * The content of the response.
	 *
	 * @var mixed
	 */
	public $content;

	/**
	 * The HTTP status code.
	 *
	 * @var int
	 */
	public $status;

	/**
	 * The response headers.
	 *
	 * @var array
	 */
	private $headers = array();

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
	 */	
	public function __construct($content, $status = 200)
	{
		$this->content = $content;
		$this->status = $status;		
	}	

	/**
	 * Factory for creating new response instances.
	 *
	 * @param  string    $content
	 * @param  int       $status
	 * @return Response
	 */
	public static function make($content, $status = 200)
	{
		return new static($content, $status);
	}

	/**
	 * Factory for creating new view response instances.
	 *
	 * @param  string    $view
	 * @param  int       $status
	 * @return Response
	 */
	public static function view($view, $status = 200)
	{
		return static::make(View::make($view), $status);
	}

	/**
	 * Send the response to the browser.
	 *
	 * @return void
	 */
	public function send()
	{
		if ( ! array_key_exists('Content-Type', $this->headers))
		{
			$this->header('Content-Type', 'text/html; charset=utf-8');
		}

		// -------------------------------------------------
		// Send the headers to the browser.
		// -------------------------------------------------
		if ( ! headers_sent())
		{
			$protocol = (isset($_SERVER['SERVER_PROTOCOL'])) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1';

			header($protocol.' '.$this->status.' '.$this->statuses[$this->status]);

			foreach ($this->headers as $name => $value)
			{	
				header($name.': '.$value, true);
			}
		}

		// -------------------------------------------------
		// Send the content of the response to the browser.
		// -------------------------------------------------
		echo (string) $this->content;
	}

	/**
	 * Add a header to the response.
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
	 * Add an item to the session flash data.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return Response
	 */
	public function with($key, $value)
	{
		if (Config::get('session.driver') != '')
		{
			Session::flash($key, $value);
		}

		return $this;
	}

	/**
	 * Determine if the response is a redirect.
	 *
	 * @return bool
	 */
	public function is_redirect()
	{
		return $this->status == 301 or $this->status == 302;
	}

	/**
	 * Magic Method for getting response View data.
	 */
	public function __get($key)
	{
		// ------------------------------------------------------
		// Attempt to get the data from the View.
		// ------------------------------------------------------
		if ($this->content instanceof View)
		{
			return $this->content->$key;
		}
	}

	/**
	 * Magic Method for setting response View data.
	 */
	public function __set($key, $value)
	{
		// ------------------------------------------------------
		// Attempt to set the data on the View.
		// ------------------------------------------------------
		if ($this->content instanceof View)
		{
			$this->content->bind($key, $value);
		}
	}

	/**
	 * Magic Method for handling dynamic method calls.
	 */
	public function __call($method, $parameters)
	{
		// ------------------------------------------------------
		// Attempt to the pass the method to the View instance.
		// ------------------------------------------------------
		if ($this->content instanceof View and method_exists($this->content, $method))
		{
			call_user_func_array(array($this->content, $method), $parameters);

			return $this;
		}

		throw new \Exception("Method [$method] does not exist on the Response class.");
	}

	/**
	 * Get the content of the response.
	 */
	public function __toString()
	{
		return (string) $this->content;
	}

}