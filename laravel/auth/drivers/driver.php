<?php namespace Laravel\Auth\Drivers;

use Laravel\Str;
use Laravel\Cookie;
use Laravel\Config;
use Laravel\Session;

abstract class Driver {

	/**
	 * The user currently being managed by the driver.
	 *
	 * @var mixed
	 */
	public $user;

	/**
	 * The current value of the user's token.
	 *
	 * @var string|null
	 */
	public $token;

	/**
	 * Create a new login auth driver instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		if (Session::started())
		{
			$this->token = Session::get($this->token());
		}

		// If a token did not exist in the session for the user, we will attempt
		// to load the value of a "remember me" cookie for the driver, which
		// serves as a long-lived client side authenticator for the user.
		if (is_null($this->token))
		{
			$this->token = $this->recall();
		}
	}

	/**
	 * Determine if the user of the application is not logged in.
	 *
	 * This method is the inverse of the "check" method.
	 *
	 * @return bool
	 */
	public function guest()
	{
		return ! $this->check();
	}

	/**
	 * Determine if the user is logged in.
	 *
	 * @return bool
	 */
	public function check()
	{
		return ! is_null($this->user());
	}

	/**
	 * Get the current user of the application.
	 *
	 * If the user is a guest, null should be returned.
	 *
	 * @return mixed|null
	 */
	public function user()
	{
		if ( ! is_null($this->user)) return $this->user;

		return $this->user = $this->retrieve($this->token);
	}

	/**
	 * Get the a given application user by ID.
	 *
	 * @param  int    $id
	 * @return mixed
	 */
	abstract public function retrieve($id);

	/**
	 * Attempt to log a user into the application.
	 *
	 * @param  array  $arguments
	 * @return void
	 */
	abstract public function attempt($arguments = array());

	/**
	 * Login the user assigned to the given token.
	 *
	 * The token is typically a numeric ID for the user.
	 *
	 * @param  string  $token
	 * @param  bool    $remember
	 * @return bool
	 */
	public function login($token, $remember = false)
	{
		$this->store($token);

		if ($remember) $this->remember($token);

		return true;
	}

	/**
	 * Log the user out of the driver's auth context.
	 *
	 * @return void
	 */
	public function logout()
	{
		$this->user = null;

		$this->cookie($this->recaller(), null, -2000);

		Session::forget($this->token());
	}

	/**
	 * Store a user's token in the session.
	 *
	 * @param  string  $token
	 * @return void
	 */
	protected function store($token)
	{
		Session::put($this->token(), $token);
	}

	/**
	 * Store a user's token in a long-lived cookie.
	 *
	 * @param  string  $token
	 * @return void
	 */
	protected function remember($token)
	{
		$token = Crypter::encrypt($token.'|'.Str::random(40));

		$this->cookie($this->recaller(), $token, Cookie::forever);
	}

	/**
	 * Attempt to find a "remember me" cookie for the user.
	 *
	 * @return string|null
	 */
	protected function recall()
	{
		$cookie = Cookie::get($this->recaller());

		// By default, "remember me" cookies are encrypted and contain the user
		// token as well as a random string. If it exists, we'll decrypt it
		// and return the first segment, which is the user's ID token.
		if ( ! is_null($cookie))
		{
			return head(explode('|', Crypter::decrypt($cookie)));
		}
	}

	/**
	 * Store an authentication cookie.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  int     $minutes
	 * @return void
	 */
	protected function cookie($name, $value, $minutes)
	{
		// When setting the default implementation of an authentication
		// cookie we'll use the same settings as the session cookie.
		// This typically makes sense as they both are sensitive.
		$config = Config::get('session');

		extract($config);

		Cookie::put($name, $minutes, $value, $path, $domain, $secure);
	}

	/**
	 * Get session key name used to store the token.
	 *
	 * @return string
	 */
	protected function token()
	{
		return $this->name().'_login';
	}

	/**
	 * Get the name used for the "remember me" cookie.
	 *
	 * @return string
	 */
	protected function recaller()
	{
		return $this->name().'_remember';
	}

	/**
	 * Get the name of the driver in a storage friendly format.
	 *
	 * @return string
	 */
	protected function name()
	{
		return strtolower(str_replace('\\', '_', get_class($this)));
	}

}