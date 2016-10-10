<?php namespace Illuminate\Http;

use Symfony\Component\HttpFoundation\Cookie;
use Illuminate\Support\Contracts\JsonableInterface;

class JsonResponse extends \Symfony\Component\HttpFoundation\JsonResponse {

	/**
	 * The json encoding options.
	 *
	 * @var int
	 */
	protected $jsonOptions;

	/**
	 * Constructor.
	 *
	 * @param  mixed  $data
	 * @param  int    $status
	 * @param  array  $headers
	 * @param  int    $options
	*/
	public function __construct($data = null, $status = 200, $headers = array(), $options = 0)
	{
		$this->jsonOptions = $options;

		parent::__construct($data, $status, $headers);
	}

	/**
	 * Get the json_decoded data from the response
	 *
	 * @param  bool $assoc
	 * @param  int  $depth
	 * @return mixed
	 */
	public function getData($assoc = false, $depth = 512)
	{
		return json_decode($this->data, $assoc, $depth);
	}

	/**
	 * {@inheritdoc}
	 */
	public function setData($data = array())
	{
		$this->data = $data instanceof JsonableInterface
                                   ? $data->toJson($this->jsonOptions)
                                   : json_encode($data, $this->jsonOptions);

		return $this->update();
	}

	/**
	 * Set a header on the Response.
	 *
	 * @param  string  $key
	 * @param  string  $value
	 * @param  bool    $replace
	 * @return \Illuminate\Http\Response
	 */
	public function header($key, $value, $replace = true)
	{
		$this->headers->set($key, $value, $replace);

		return $this;
	}

	/**
	 * Add a cookie to the response.
	 *
	 * @param  \Symfony\Component\HttpFoundation\Cookie  $cookie
	 * @return \Illuminate\Http\Response
	 */
	public function withCookie(Cookie $cookie)
	{
		$this->headers->setCookie($cookie);

		return $this;
	}

}
