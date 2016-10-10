<?php namespace Illuminate\Support\Facades;

use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response as IlluminateResponse;
use Illuminate\Support\Contracts\ArrayableInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class Response {

	/**
	 * An array of registered Response macros.
	 *
	 * @var array
	 */
	protected static $macros = array();

	/**
	 * Return a new response from the application.
	 *
	 * @param  string  $content
	 * @param  int     $status
	 * @param  array   $headers
	 * @return \Illuminate\Http\Response
	 */
	public static function make($content = '', $status = 200, array $headers = array())
	{
		return new IlluminateResponse($content, $status, $headers);
	}

	/**
	 * Return a new view response from the application.
	 *
	 * @param  string  $view
	 * @param  array   $data
	 * @param  int     $status
	 * @param  array   $headers
	 * @return \Illuminate\Http\Response
	 */
	public static function view($view, $data = array(), $status = 200, array $headers = array())
	{
		$app = Facade::getFacadeApplication();

		return static::make($app['view']->make($view, $data), $status, $headers);
	}

	/**
	 * Return a new JSON response from the application.
	 *
	 * @param  string|array  $data
	 * @param  int    $status
	 * @param  array  $headers
	 * @param  int    $options
	 * @return \Illuminate\Http\JsonResponse
	 */
	public static function json($data = array(), $status = 200, array $headers = array(), $options = 0)
	{
		if ($data instanceof ArrayableInterface)
		{
			$data = $data->toArray();
		}

		return new JsonResponse($data, $status, $headers, $options);
	}

	/**
	 * Return a new streamed response from the application.
	 *
	 * @param  \Closure  $callback
	 * @param  int      $status
	 * @param  array    $headers
	 * @return \Symfony\Component\HttpFoundation\StreamedResponse
	 */
	public static function stream($callback, $status = 200, array $headers = array())
	{
		return new StreamedResponse($callback, $status, $headers);
	}

	/**
	 * Create a new file download response.
	 *
	 * @param  \SplFileInfo|string  $file
	 * @param  string  $name
	 * @param  array   $headers
	 * @param  null|string  $disposition
	 * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
	 */
	public static function download($file, $name = null, array $headers = array(), $disposition = 'attachment')
	{
		$response = new BinaryFileResponse($file, 200, $headers, true, $disposition);

		if ( ! is_null($name))
		{
			return $response->setContentDisposition($disposition, $name, Str::ascii($name));
		}

		return $response;
	}

	/**
	 * Register a macro with the Response class.
	 *
	 * @param  string  $name
	 * @param  callable  $callback
	 * @return void
	 */
	public static function macro($name, $callback)
	{
		static::$macros[$name] = $callback;
	}

	/**
	 * Handle dynamic calls into Response macros.
	 *
	 * @param  string  $method
	 * @param  array  $parameters
	 * @return mixed
	 *
	 * @throws \BadMethodCallException
	 */
	public static function __callStatic($method, $parameters)
	{
		if (isset(static::$macros[$method]))
		{
			return call_user_func_array(static::$macros[$method], $parameters);
		}

		throw new \BadMethodCallException("Call to undefined method $method");
	}

}
