<?php namespace Laravel\Auth\Drivers; 
use Laravel\Hash, Laravel\Config;


class Redis extends Driver {
	/**
	 * Structure USER --> hashes
	 *  hash key name user:id:username
	 *  hash field:
	 * 		id
	 * 		password like Hash::make("userpassword")
	 * 		... etc.
	 *  username and password fields are determined application/config/auth.php
	 * */
	/**
	 * Get hashes of user to object
	 *
	 * @param   array $userkey (use only first field of this array!!!)
	 * @return  mixed|NULL
	 */

  protected $redis;

  public function __construct(\Laravel\Redis $redis)
  {
    $this->redis = $redis;
    parent::__construct();
  }

	protected function user_to_array($userkey)
	{
		if (count($userkey) > 0)
		{
			$user_row = $this->redis->hgetall($userkey[0]);
			for ($i = 0; $i<count($user_row); $i+=2)
			{
				$user_array[$user_row[$i]] = $user_row[$i+1]; 
			}
			$user = (object) $user_array;// user is object with fields
			return $user;
		}
		return;
	}
	
	/**
	 * Get the current user of the application.
	 *
	 * If the user is a guest, null should be returned.
	 *
	 * @param  int  $id
	 * @return mixed|null
	 */

	public function retrieve($token)
	{
		if (filter_var($token, FILTER_VALIDATE_INT) !== false)
		{
			$userkey = $this->redis->keys("user:" . $token . "*");
			$user = $this->user_to_array($userkey);
			return $user;
		}
	}
	
	/**
	 * Attempt to log a user into the application.
	 *
	 * @param  array $arguments
	 * @return void
	 */
	public function attempt($arguments = array())
	{
		// search a user by name, if not found, returns an empty array
		$userkey = $this->redis->keys("user*" . $arguments['username']);
		$user = $this->user_to_array($userkey);
		$password = $arguments['password'];
		$password_field = Config::get('auth.password', 'password');
		
		if ( ! is_null($user) and Hash::check($password, $user->{$password_field}) )
			{
				return $this->login($user->id, array_get($arguments, 'remember'));
			}
			return false;
	}
	
}
