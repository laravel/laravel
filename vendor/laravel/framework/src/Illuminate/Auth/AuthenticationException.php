<?php

namespace Illuminate\Auth;

use Exception;
use Illuminate\Http\Request;

class AuthenticationException extends Exception
{
    /**
     * All of the guards that were checked.
     *
     * @var array
     */
    protected $guards;

    /**
     * The path the user should be redirected to.
     *
     * @var string|null
     */
    protected $redirectTo;

    /**
     * The callback that should be used to generate the authentication redirect path.
     *
     * @var callable
     */
    protected static $redirectToCallback;

    /**
     * Create a new authentication exception.
     *
     * @param  string  $message
     * @param  array  $guards
     * @param  string|null  $redirectTo
     */
    public function __construct($message = 'Unauthenticated.', array $guards = [], $redirectTo = null)
    {
        parent::__construct($message);

        $this->guards = $guards;
        $this->redirectTo = $redirectTo;
    }

    /**
     * Get the guards that were checked.
     *
     * @return array
     */
    public function guards()
    {
        return $this->guards;
    }

    /**
     * Get the path the user should be redirected to.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function redirectTo(Request $request)
    {
        if ($this->redirectTo) {
            return $this->redirectTo;
        }

        if (static::$redirectToCallback) {
            return call_user_func(static::$redirectToCallback, $request);
        }
    }

    /**
     * Specify the callback that should be used to generate the redirect path.
     *
     * @param  callable  $redirectToCallback
     * @return void
     */
    public static function redirectUsing(callable $redirectToCallback)
    {
        static::$redirectToCallback = $redirectToCallback;
    }
}
