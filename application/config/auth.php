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
	| Perform Password Salt
	|--------------------------------------------------------------------------
	|
	| When true this let the Auth::login() method to use the following
	| hash_salt function on the password provided in the login form.
	|
	|
	| Note: Your User model should contain a "salt" attribute
	|
	*/

	'perform_salt' => true,



	/*
	|--------------------------------------------------------------------------
	| Salt Password
	|--------------------------------------------------------------------------
	|
	| If the previous field is set to true this method is called by 
	| the Auth::login() method when attempting to login a user, such as when 
	| checking credentials received from a login 
	| form .
	|
	| It shall salt the password which hashed should match the one saved in the
	| Db for the current user.
	|
	| You are free to change this method for your application however you wish.
	|
	| Note: This method must return an string that will be hashed 
	|
	*/

	'salt' => function($password, $salt)
	{
		return $password.$salt;
	},




);