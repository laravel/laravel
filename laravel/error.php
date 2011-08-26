<?php namespace Laravel;

class Error extends Response {

	/**
	 * Create a new error response instance.
	 *
	 * The response status code will be set using the specified code.
	 *
	 * Note: The specified error code should correspond to a view in your views/error directory.
	 *
	 * <code>
	 *		// Return a 404 error response
	 *		return new Error('404');
	 * </code>
	 *
	 * @param  int       $code
	 * @param  array     $data
	 * @return void
	 */
	public function __construct($code, $data = array())
	{
		return parent::__construct(View::make('error/'.$code, $data), $code);
	}

}