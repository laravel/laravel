<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Authentication Model
	|--------------------------------------------------------------------------
	|
	| This model will be used by the Auth class when retrieving the users of
	| your application. Feel free to change it to the name of your user model.
	|
	| Note: The authentication model must be an Eloquent model.
	|
	*/

	'model' => 'User',

	/*
	|--------------------------------------------------------------------------
	| Authentication Username
	|--------------------------------------------------------------------------
	|
	| The authentication username is the column on your users table that
	| is considered the username of the user. Typically, this is either "email"
	| or "username". However, you are free to make it whatever you wish.
	|
	*/

	'username' => 'email',

);