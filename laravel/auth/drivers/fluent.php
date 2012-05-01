<?php namespace Laravel\Auth\Drivers; use Laravel\Hash, Laravel\Database as DB;

class Fluent extends Driver {

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
			DB::table(Config::get('auth.table'))->find($id);
		} 
	}

	/**
	 * Attempt to log a user into the application.
	 *
	 * @param  array  $arguments
	 * @return void
	 */
	public function attempt($arguments = array())
	{
		$user = $this->get_user($arguments['username']);

		// This driver uses a basic username and password authentication scheme
		// so if the credentials mmatch what is in the database we will just
		// log the user into the application and remember them if asked.
		$password = $arguments['password'];

		if ( ! is_null($user) and Hash::check($password, $user->password))
		{
			return $this->login($user->id, array_get($arguments, 'remember'));
		}

		return false;
	}

	/**
	 * Get the user from the database table by username.
	 *
	 * @param  mixed  $value
	 * @return mixed
	 */
	protected function get_user($value)
	{
		$table = Config::get('auth.table');

		$username = Config::get('auth.username');

		return DB::table($table)->where($username, '=', $value)->first();
	}

}