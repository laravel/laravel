<?php namespace Laravel\Auth\Drivers; use Laravel\Hash, Laravel\Config;

class Eloquent extends Driver {

	/**
	 * Get the current user of the application.
	 *
	 * If the user is a guest, null should be returned.
	 *
	 * @param  int         $id
	 * @return mixed|null
	 */
	public function retrieve($id)
	{
		if (filter_var($id, FILTER_VALIDATE_INT) !== false)
		{
			return $this->model()->find($id);
		}
	}

	/**
	 * Login the user assigned to the given token.
	 *
	 * The token is typically a numeric ID for the user.
	 *
	 * @param  mixed   $token
	 * @param  bool    $remember
	 * @return bool
	 */
	public function login($token, $remember = false)
	{
		// if the token is an Eloquent model get the primary key
		if ($token instanceof \Eloquent) $token = $token->get_key();

		$this->token = $token;

		$this->store($token);

		if ($remember) $this->remember($token);

		return true;
	}

	/**
	 * Attempt to log a user into the application.
	 *
	 * @param  array  $arguments
	 * @return void
	 */
	public function attempt($arguments = array())
	{
		$username = Config::get('auth.username');

		$user = $this->model()->where($username, '=', $arguments['username'])->first();

		// This driver uses a basic username and password authentication scheme
		// so if the credentials match what is in the database we will just
		// log the user into the application and remember them if asked.
		$password = $arguments['password'];

		if ( ! is_null($user) and Hash::check($password, $user->password))
		{
			return $this->login($user->id, array_get($arguments, 'remember'));
		}

		return false;
	}

	/**
	 * Get a fresh model instance.
	 *
	 * @return Eloquent
	 */
	protected function model()
	{
		$model = Config::get('auth.model');

		return new $model;
	}

}