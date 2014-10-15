<?php

/*
|--------------------------------------------------------------------------
| Load Environment Variables
|--------------------------------------------------------------------------
|
| Next we will load the environment variables for the application which
| are stored in the ".env" file. These variables will be loaded into
| the $_ENV and "putenv" facilities of PHP so they stay available.
|
*/

if (file_exists(__DIR__.'/../.env'))
{
	Dotenv::load(__DIR__.'/../');

	//Dotenv::required('APP_ENV');
}


/*
|--------------------------------------------------------------------------
| Detect The Application Environment
|--------------------------------------------------------------------------
|
| Laravel takes a dead simple approach to your application environments
| so you may simply return the environment from the Closure. We will
| assume we are using the "APP_ENV" variable for this environment.
|
*/

$env = $app->detectEnvironment(function()
{
	return getenv('APP_ENV') ?: 'production';
});
