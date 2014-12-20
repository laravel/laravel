<?php namespace App\Exceptions;

use Exception;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler {

	/**
	 * A list of the exception types that should not be reported.
	 *
	 * @var array
	 */
	protected $dontReport = [
		'Symfony\Component\HttpKernel\Exception\HttpException'
	];

	/**
	 * Report or log an exception.
	 *
	 * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
	 *
	 * @param  \Exception  $e
	 * @return void
	 */
	public function report(Exception $e)
	{
		return parent::report($e);
	}

	/**
	 * Render an exception into an HTTP response.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Exception  $e
	 * @return \Illuminate\Http\Response
	 */
	public function render($request, Exception $e)
	{
		switch (true)
		{
			case $this->isHttpException($e):
				return $request->ajax() ?
						response()->json($e->getMessage(), $e->getStatusCode()) :
						$this->renderHttpException($e);

			case $e instanceof TokenMismatchException:
				if ( ! \App::isLocal())
					return $request->ajax() ?
						response()->json('Token expired', 498) :
						redirect()->back()->exceptInput('_token')->withErrors(['_token'=>'Session expired.']);

			default:
				return parent::render($request, $e);
		}
	}

}
