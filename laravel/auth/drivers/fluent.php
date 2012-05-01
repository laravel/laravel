<?php namespace Laravel\Auth\Drivers; use Laravel\Hash, Laravel\Database;

class Fluent extends Driver {

	/**
	 * The "users" table used by the application.
	 *
	 * @var string
	 */
	public $table;

	/**
	 * Create a new fluent authentication driver.
	 *
	 * @param  string  $table
	 * @return void
	 */
	public function __construct($table)
	{
		$this->table = $table;

		parent::__construct();
	}

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
			Database::table($this->table)->find($id);
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
		$user = Database::table($this->table)->where_email($arguments['email'])->first();

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

}