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
	        return User::find($id);
	    }
	},

	'attempt' => function($email, $password)
	{
	    $user = User::where_email($email)->first();

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