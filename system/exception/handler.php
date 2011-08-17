<?php namespace System\Exception;

use System\View;
use System\Config;
use System\Response;

class Handler {

	/**
	 * The exception wrapper for the exception being handled.
	 *
	 * @var Wrapper
	 */
	public $exception;

	/**
	 * Create a new exception handler instance.
	 *
	 * @param  Exception  $e
	 * @return void
	 */
	public function __construct($e)
	{
		$this->exception = new Wrapper($e);
	}

	/**
	 * Create a new exception handler instance.
	 *
	 * @param  Exception  $e
	 * @return Handler
	 */
	public static function make($e)
	{
		return new static($e);
	}

	/**
	 * Handle the exception and display the error report.
	 *
	 * The exception will be logged if error logging is enabled.
	 *
	 * The output buffer will be cleaned so nothing is sent to the browser except the
	 * error message. This prevents any views that have already been rendered from
	 * being shown in an incomplete or erroneous state.
	 *
	 * After the exception is displayed, the request will be halted.
	 *
	 * @return void
	 */
	public function handle()
	{
		if (ob_get_level() > 0) ob_clean();

		if (Config::get('error.log')) $this->log();

		$this->get_response(Config::get('error.detail'))->send();

		exit(1);
	}

	/**
	 * Log the exception using the logger closure specified in the error configuration.
	 *
	 * @return void
	 */
	private function log()
	{
		$parameters = array(
			$this->exception->severity(),
			$this->exception->message(),
			$this->exception->getTraceAsString(),
		);

		call_user_func_array(Config::get('error.logger'), $parameters);
	}

	/**
	 * Get the error report response for the exception.
	 *
	 * @param  bool      $detailed
	 * @return Resposne
	 */
	private function get_response($detailed)
	{
		return ($detailed) ? $this->detailed_response() : Response::error('500');
	}

	/**
	 * Get the detailed error report for the exception.
	 *
	 * @return Response
	 */
	private function detailed_response()
	{
		$data = array(
			'severity' => $this->exception->severity(),
			'message'  => $this->exception->message(),
			'line'     => $this->exception->getLine(),
			'trace'    => $this->exception->getTraceAsString(),
			'contexts' => $this->exception->context(),
		);

		return Response::make(View::make('error.exception', $data), 500);
	}

}