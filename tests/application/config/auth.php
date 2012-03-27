<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Retrieve The Current User
	|--------------------------------------------------------------------------
	|
	| This closure is called by the Auth class' "user" method when trying to
	| retrieve a user by the ID that is stored in their session. If you find
	| the user, just return the user object, but make sure it has an "id"
	| property. If you can't find the user, just return null.
	|
	| Of course, a simple and elegant authentication solution has already
	| been provided for you using the query builder and hashing engine.
	| We love making your life as easy as possible.
	|
	*/

	'user' => function($id)
	{
		if (filter_var($id, FILTER_VALIDATE_INT) !== false)
		{
			return DB::table('users')->find($id);
		} 
	},

	/*
	|--------------------------------------------------------------------------
	| Authenticate User Credentials
	|--------------------------------------------------------------------------
	|
	| This closure is called by the Auth::attempt() method when attempting to
	| authenticate a user that is logging into your application. It's like a
	| super buff bouncer to your application.
	|
	| If the provided credentials are correct, simply return an object that
	| represents the user being authenticated. As long as it has a property
	| for the "id", any object will work. If the credentials are not valid,
	| you don't meed to return anything.
	|
	*/

	'attempt' => function($username, $password)
	{
		$user = DB::table('users')->where_username($username)->first();

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

	'logout' => function($user) {},

	/*
	|--------------------------------------------------------------------------
	| "Remember Me" Cookie Name
	|--------------------------------------------------------------------------
	|
	| Here you may specify the cookie name that will be used for the cookie
	| that serves as the "remember me" token. Of course, a sensible default
	| has been set for you, so you probably don't need to change it.
	|
	*/

	'cookie' => 'laravel_remember',

);