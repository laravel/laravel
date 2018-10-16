<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * the guard name of current auth middleware.
     * @var string
     */
    protected $guard;

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            $config = config("auth.guards.{$this->guard}");
            if (empty($config['path'])) {
                throw new InvalidArgumentException("Path of auth guard [{$this->guard}] is not defined.");
            }
            $path = $config['path'];

            if (Route::has($path)) {
                return route($path);
            } else {
                return $path;
            }
        }
    }

    /**
     * Determine if the user is logged in to any of the given guards.
     * Save the first guard name if it exists, in case the user is not logged in to any of them.
     * And redirectedTo() reads its path.
     * @param  [type] $request [description]
     * @param  array  $guards  [description]
     * @return [type]          [description]
     */
    protected function authenticate($request, array $guards)
    {
        $this->guard = empty($guards) ? $this->auth->getDefaultDriver() : $guards[0];
        parent::authenticate($request, $guards);
    }
}
