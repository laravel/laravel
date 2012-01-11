<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Authentication Username
	|--------------------------------------------------------------------------
	|
	| This option should be set to the "username" property of your users which
	| is typically named either "email" or "username". However, you have the
	| freedom to use whatever you wish.
	|
	| The value of this property will be used by the "attempt" closure when
	| searching for users by their username. It will also be used when the
	| user is set to be "remembered", as the username is embedded into the
	| encrypted cookie and is used to verify the user's identity.
	|
	*/

	'username' => 'email',

	/*
	|--------------------------------------------------------------------------
	| Retrieve The Current User
	|--------------------------------------------------------------------------
	|
	| This closure is called by the Auth::user() method when attempting to
	| retrieve a user by the ID that is stored in their session.
	|
	| Simply return an object representing the user with the given ID. Or, if
	| no user with the given ID is registered to use your application, you do
	| not need to return anything.
	|
	| Of course, a simple, elegant authentication solution is already provided
	| for you using Eloquent and the default Laravel hashing engine.
	|
	*/

	'user' => function($id)
	{
		if ( ! is_null($id) and filter_var($id, FILTER_VALIDATE_INT) !== false)
		{
			return User::find($id);
		} 
	},

	/*
	|--------------------------------------------------------------------------
	| Authenticate User Credentials
	|--------------------------------------------------------------------------
	|
	| This closure is called by the Auth::attempt() method when attempting to
	| authenticate a user that is logging into your application.
	|
	| If the provided credentials are correct, simply return an object that
	| represents the user being authenticated. As long as it has a property
	| for the "id", any object will work. If the credentials are not valid,
	| you don't meed to return anything.
	|
	*/

	'attempt' => function($username, $password, $config)
	{
		$user = User::where($config['username'], '=', $username)->first();

		if ( ! is_null($user) and Hash::check($password, $user->password))
		{
			return $user;
		}
	},

	/*
	|--------------------------------------------------------------------------
	| Logout The Current User
	|--------------------------------------------------------------------------
	|
	| Here you may do anything that needs to be done when a user logs out of
	| your application, such as call the logout method on a third-party API
	| you are using for authentication or anything else you desire.
	|
	*/

	'logout' => function($user) {}

);