<?php namespace Laravel;

use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\LaravelResponse as FoundationResponse;

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
	 * Create a new JSON response.
	 *
	 * <code>
	 *		// Create a response instance with JSON
	 *		return Response::json($data, 200, array('header' => 'value'));
	 * </code>
	 *
	 * @param  mixed     $data
	 * @param  int       $status
	 * @param  array     $headers
	 * @return Response
	 */
	public static function json($data, $status = 200, $headers = array())
	{
		$headers['Content-Type'] = 'application/json; charset=utf-8';

		return new static(json_encode($data), $status, $headers);
	}

	/**
	 * Create a new response of JSON'd Eloquent models.
	 *
	 * <code>
	 *		// Create a new response instance with Eloquent models
	 *		return Response::eloquent($data, 200, array('header' => 'value'));
	 * </code>
	 *
	 * @param  Eloquent|array   $data
	 * @param  int              $status
	 * @param  array            $headers
	 * @return Response
	 */
	public static function eloquent($data, $status = 200, $headers = array())
	{
		$headers['Content-Type'] = 'application/json; charset=utf-8';

		return new static(eloquent_to_json($data), $status, $headers);
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

		// We'll set some sensible default headers, but merge the array given to
		// us so that the developer has the chance to override any of these
		// default headers with header values of their own liking.
		$headers = array_merge(array(
			'Content-Description'       => 'File Transfer',
			'Content-Type'              => File::mime(File::extension($path)),
			'Content-Transfer-Encoding' => 'binary',
			'Expires'                   => 0,
			'Cache-Control'             => 'must-revalidate, post-check=0, pre-check=0',
			'Pragma'                    => 'public',
			'Content-Length'            => File::size($path),
		), $headers);

		// Once we create the response, we need to set the content disposition
		// header on the response based on the file's name. We'll pass this
		// off to the HttpFoundation and let it create the header text.
		$response = new static(File::get($path), 200, $headers);

		$d = $response->disposition($name);

		return $response->header('Content-Disposition', $d);
	}

	/**
	 * Create the proper Content-Disposition header.
	 *
	 * @param  string  $file
	 * @return string
	 */
	public function disposition($file)
	{
		$type = ResponseHeaderBag::DISPOSITION_ATTACHMENT;

		return $this->foundation->headers->makeDisposition($type, $file);
	}

	/**
	 * Prepare a response from the given value.
	 *
	 * @param  mixed     $response
	 * @return Response
	 */
	public static function prepare($response)
	{
		// We will need to force the response to be a string before closing
		// the session since the developer may be utilizing the session
		// within the view, and we can't age it until rendering.
		if ( ! $response instanceof Response)
		{
			$response = new static($response);
		}

		return $response;
	}

	/**
	 * Send the headers and content of the response to the browser.
	 *
	 * @return void
	 */
	public function send()
	{
		$this->cookies();

		$this->foundation->prepare(Request::foundation());

		$this->foundation->send();
	}

	/**
	 * Convert the content of the Response to a string and return it.
	 *
	 * @return string
	 */
	public function render()
	{
		// If the content is a stringable object, we'll go ahead and call
		// the toString method so that we can get the string content of
		// the content object. Otherwise we'll just cast to string.
		if (str_object($this->content))
		{
			$this->content = $this->content->__toString();
		}
		else
		{
			$this->content = (string) $this->content;
		}

		// Once we obtain the string content, we can set the content on
		// the HttpFoundation's Response instance in preparation for
		// sending it back to client browser when all is finished.
		$this->foundation->setContent($this->content);

		return $this->content;
	}

	/**
	 * Send all of the response headers to the browser.
	 *
	 * @return void
	 */
	public function send_headers()
	{
		$this->foundation->prepare(Request::foundation());

		$this->foundation->sendHeaders();
	}

	/**
	 * Set the cookies on the HttpFoundation Response.
	 *
	 * @return void
	 */
	protected function cookies()
	{
		$ref = new \ReflectionClass('Symfony\Component\HttpFoundation\Cookie');

		// All of the cookies for the response are actually stored on the
		// Cookie class until we're ready to send the response back to
		// the browser. This allows our cookies to be set easily.
		foreach (Cookie::$jar as $name => $cookie)
		{
			$config = array_values($cookie);

			$this->headers()->setCookie($ref->newInstanceArgs($config));
		}
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
	 * Get the HttpFoundation Response headers.
	 *
	 * @return ResponseParameterBag
	 */
	public function headers()
	{
		return $this->foundation->headers;
	}

	/**
	 * Get / set the response status code.
	 *
	 * @param  int    $status
	 * @return mixed
	 */
	public function status($status = null)
	{
		if (is_null($status))
		{
			return $this->foundation->getStatusCode();
		}
		else
		{
			$this->foundation->setStatusCode($status);

			return $this;
		}
	}

	/**
	 * Render the response when cast to string
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->render();
	}

}