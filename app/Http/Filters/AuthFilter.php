<?php namespace App\Http\Filters;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Contracts\Auth\Authenticator;
use Illuminate\Contracts\Routing\ResponseFactory;

class AuthFilter {

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
     * @param  ResponseFactory  $response
     * @return void
     */
    public function __construct(Authenticator $auth,
                                ResponseFactory $response)
    {
        $this->auth = $auth;
        $this->response = $response;
    }

    /**
     * Run the request filter.
     *
     * @param  \Illuminate\Routing\Route  $route
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function filter(Route $route, Request $request)
    {
        if ($this->auth->guest())
        {
            if ($request->ajax())
            {
                return $this->response->make('Unauthorized', 401);
            }
            else
            {
                return $this->response->redirectGuest('auth/login');
            }
        }
    }

}
