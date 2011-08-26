<?php namespace Laravel\Exception;

use Laravel\View;
use Laravel\Config;
use Laravel\Response;

class Handler {

	/**
	 * The exception examiner for the exception being handled.
	 *
	 * @var Examiner
	 */
	public $examiner;

	/**
	 * Create a new exception handler instance.
	 *
	 * @param  Examiner  $examiner
	 * @return void
	 */
	public function __construct(Examiner $examiner)
	{
		$this->examiner = $examiner;
	}

	/**
	 * Create a new exception handler instance.
	 *
	 * @param  Examiner  $examiner
	 * @return Handler
	 */
	public static function make(Examiner $examiner)
	{
		return new static($examiner);
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
		$parameters = array($this->examiner->severity(), $this->examiner->message(), $this->examiner->getTraceAsString());

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
		return ($detailed) ? $this->detailed_response() : new Error('500');
	}

	/**
	 * Get the detailed error report for the exception.
	 *
	 * @return Response
	 */
	private function detailed_response()
	{
		$data = array(
			'severity' => $this->examiner->severity(),
			'message'  => $this->examiner->message(),
			'line'     => $this->examiner->getLine(),
			'trace'    => $this->examiner->getTraceAsString(),
			'contexts' => $this->examiner->context(),
		);

		return Response::make(View::make('error.exception', $data), 500);
	}

}