<?php namespace Laravel;

use Symfony\Component\HttpFoundation\Response as FoundationResponse;

class Response {

	/**
	 * The content of the response.
	 *
	 * @var mixed
	 */
	public $content;

	/**
	 * The Symfony HttpFoundation Response instance.
	 *
	 * @var HttpFoundation\Response
	 */
	public $foundation;

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
		$this->content = $content;

		$this->foundation = new FoundationResponse('', $status, $headers);
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
		// We'll need to force the response to be a string before closing
		// the session, since the developer may be utilizing the session
		// within the view, and we can't age it until rendering.
		if ( ! $response instanceof Response)
		{
			$response = new static($response);
		}

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
		// If the content is a stringable object, we'll go ahead and call
		// to toString method so that we can get the string content of
		// the content object. Otherwise we'll just cast to string.
		if (str_object($this->content))
		{
			$this->content = $this->content->__toString();
		}
		else
		{
			$this->content = (string) $this->content;
		}

		// Once we have the string content, we can set the content on
		// the HttpFoundation Response instance in preparation for
		// sending it back to client browser when all is done.
		$this->foundation->setContent($this->content);

		return $this->content;
	}

	/**
	 * Send the headers and content of the response to the browser.
	 *
	 * @return void
	 */
	public function send()
	{
		$this->foundation->prepare(Request::$foundation);

		$this->foundation->send();
	}

	/**
	 * Send all of the response headers to the browser.
	 *
	 * @return void
	 */
	public function send_headers()
	{
		$this->foundation->prepare(Request::$foundation);

		$this->foundation->sendHeaders();
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
		$this->foundation->headers->set($name, $value);

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
		$this->foundation->setStatusCode($status);

		return $this;
	}

}