<?php namespace Illuminate\Support\Facades;

use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Traits\MacroableTrait;
use Illuminate\Http\Response as IlluminateResponse;
use Illuminate\Support\Contracts\ArrayableInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class Response {

	use MacroableTrait;

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
	 * Return a new JSONP response from the application.
	 *
	 * @param  string  $callback
	 * @param  string|array  $data
	 * @param  int    $status
	 * @param  array  $headers
	 * @param  int    $options
	 * @return \Illuminate\Http\JsonResponse
	 */
	public static function jsonp($callback, $data = [], $status = 200, array $headers = [], $options = 0)
	{
		return static::json($data, $status, $headers, $options)->setCallback($callback);
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
			return $response->setContentDisposition($disposition, $name, str_replace('%', '', Str::ascii($name)));
		}

		return $response;
	}

}
