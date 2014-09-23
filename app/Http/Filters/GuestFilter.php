<?php namespace App\Http\Filters;

use Illuminate\Contracts\Routing\ResponseFactory;

class GuestFilter {

	/**
	 * The authenticator implementation.
	 *
	 * @var Authenticator
	 */
	protected $auth;

    /**
     * The response factory implementation.
     *
     * @var ResponseFactory
     */
    protected $response;

	/**
	 * Create a new filter instance.
	 *
	 * @param  Authenticator  $auth
	 * @return void
	 */
	public function __construct(Authenticator $auth,
                                ResponseFacotry $response)
	{
		$this->auth = $auth;
		$this->response = $response;
	}

	/**
	 * Run the request filter.
	 *
	 * @return mixed
	 */
	public function filter()
	{
		if ($this->auth->check())
		{
			return $this->response->redirectTo('/');
		}
	}

}