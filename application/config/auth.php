<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Default Authentication Driver
	|--------------------------------------------------------------------------
	|
	| Laravel uses a flexible driver-based system to handle authentication.
	| You are free to register your own drivers using the Auth::extend
	| method. Of course, a few great drivers are provided out of
	| box to handle basic authentication simply and easily.
	|
	| Drivers: 'fluent', 'eloquent'.
	|
	*/

	'driver' => 'eloquent',

	/*
	|--------------------------------------------------------------------------
	| Authentication Username
	|--------------------------------------------------------------------------
	|
	| Here you may specify the database column that should be considered the
	| "username" for your users. Typically, this will either be "username"
	| or "email". Of course, you're free to change the value to anything.
	|
	*/

	'username' => 'email',

	/*
	|--------------------------------------------------------------------------
	| Authentication Password
	|--------------------------------------------------------------------------
	|
	| Here you may specify the database column that should be considered the
	| "password" for your users. Typically, this will be "password" but,
	| again, you're free to change the value to anything you see fit.
	|
	*/

	'password' => 'password',

	/*
	|--------------------------------------------------------------------------
	| Authentication Model
	|--------------------------------------------------------------------------
	|
	| When using the "eloquent" authentication driver, you may specify the
	| model that should be considered the "User" model. This model will
	| be used to authenticate and load the users of your application.
	|
	*/

	'model' => 'User',

	/*
	|--------------------------------------------------------------------------
	| Authentication Table
	|--------------------------------------------------------------------------
	|
	| When using the "fluent" authentication driver, the database table used
	| to load users may be specified here. This table will be used in by
	| the fluent query builder to authenticate and load your users.
	|
	*/

	'table' => 'users',

);