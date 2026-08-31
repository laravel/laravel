<?php

namespace Illuminate\Auth\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as AuthFactory;

class AuthenticateWithBasicAuth
{
    /**
     * The guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     */
    public function __construct(AuthFactory $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Specify the guard and field for the middleware.
     *
     * @param  string|null  $guard
     * @param  string|null  $field
     * @return string
     *
     * @named-arguments-supported
     */
    public static function using($guard = null, $field = null)
    {
        return static::class.':'.implode(',', func_get_args());
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @param  string|null  $field
     * @return mixed
     *
     * @throws \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException
     */
    public function handle($request, Closure $next, $guard = null, $field = null)
    {
        $this->auth->guard($guard)->basic($field ?: 'email');

        return $next($request);
    }
}
