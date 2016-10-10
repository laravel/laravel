<?php namespace Illuminate\Exception;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class PlainDisplayer implements ExceptionDisplayerInterface {

	/**
	 * Display the given exception to the user.
	 *
	 * @param  \Exception  $exception
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function display(Exception $exception)
	{
		$status = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : 500;

		$headers = $exception instanceof HttpExceptionInterface ? $exception->getHeaders() : array();

		return new Response(file_get_contents(__DIR__.'/resources/plain.html'), $status, $headers);
	}

}
