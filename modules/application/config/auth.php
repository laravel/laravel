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

);