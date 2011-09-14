<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Retrieve Users By ID
	|--------------------------------------------------------------------------
	|
	| This method is called by the Auth::user() method when attempting to
	| retrieve a user by their user ID, such as when retrieving a user by the
	| user ID stored in the session.
	|
	| You are free to change this method for your application however you wish.
	|
	*/

	'by_id' => function($id)
	{
		return User::find($id);
	},

	/*
	|--------------------------------------------------------------------------
	| Retrieve Users By Username
	|--------------------------------------------------------------------------
	|
	| This method is called by the Auth::check() method when attempting to
	| retrieve a user by their username, such as when checking credentials
	| received from a login form.
	|
	| You are free to change this method for your application however you wish.
	|
	| Note: This method must return an object that has "id" and "password"
	|       properties. The type of object returned does not matter.
	|
	*/

	'by_username' => function($username)
	{
		return User::where_email($username)->first();
	},

	/*
	|--------------------------------------------------------------------------
	| Authenticates a user.
	|--------------------------------------------------------------------------
	|
	| This method is called by the Auth::login() method.
	|
	| With the default closure, the password passed to the method should be plain text, 
	| as it will be hashed by the Hash class when authenticating.
	|
	| Note: This method must return the User object of the authenticated user 
	|       or false
	*/

	'login' => function($username, $password)
	{
		if ( ! is_null($user = call_user_func(Config::get('auth.by_username'), $username)))
		{
			if (Hash::check($password, $user->password))
			{
				return $user;
			}
		}

		return false;
	},

);