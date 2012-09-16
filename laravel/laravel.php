<?php namespace Laravel;

/*
|--------------------------------------------------------------------------
| Bootstrap The Framework Core
|--------------------------------------------------------------------------
|
| By including this file, the core of the framework will be setup which
| includes the class auto-loader, and the registration of any bundles.
| Basically, once this file has been included, the entire framework
| may be used by the developer.
|
*/

require 'core.php';

/*
|--------------------------------------------------------------------------
| Setup Error & Exception Handling
|--------------------------------------------------------------------------
|
| Next we'll register custom handlers for all errors and exceptions so we
| can display a clean error message for all errors, as well as do any
| custom error logging that may be setup by the developer.
|
*/

set_exception_handler(function($e)
{
	require_once path('sys').'error'.EXT;

	Error::exception($e);
});


set_error_handler(function($code, $error, $file, $line)
{
	require_once path('sys').'error'.EXT;

	Error::native($code, $error, $file, $line);
});


register_shutdown_function(function()
{
	require_once path('sys').'error'.EXT;

	Error::shutdown();
});

/*
|--------------------------------------------------------------------------
| Report All Errors
|--------------------------------------------------------------------------
|
| By setting error reporting to -1, we essentially force PHP to report
| every error, and this is guaranteed to show every error on future
| releases of PHP. This allows everything to be fixed early!
|
*/

error_reporting(-1);

/*
|--------------------------------------------------------------------------
| Start The Application Bundle
|--------------------------------------------------------------------------
|
| The application "bundle" is the default bundle for the installation and
| we'll fire it up first. In this bundle's bootstrap, more configuration
| will take place and the developer can hook into some of the core
| framework events such as the configuration loader.
|
*/

Bundle::start(DEFAULT_BUNDLE);

/*
|--------------------------------------------------------------------------
| Auto-Start Other Bundles
|--------------------------------------------------------------------------
|
| Bundles that are used throughout the application may be auto-started
| so they are immediately available on every request without needing
| to explicitly start them within the application.
|
*/

foreach (Bundle::$bundles as $bundle => $config)
{
	if ($config['auto']) Bundle::start($bundle);
}

/*
|--------------------------------------------------------------------------
| Register The Catch-All Route
|--------------------------------------------------------------------------
|
| This route will catch all requests that do not hit another route in
| the application, and will raise the 404 error event so the error
| can be handled by the developer in their 404 event listener.
|
*/

Routing\Router::register('*', '(:all)', function()
{
	return Event::first('404');
});

/*
|--------------------------------------------------------------------------
| Gather The URI And Locales
|--------------------------------------------------------------------------
|
| When routing, we'll need to grab the URI and the supported locales for
| the route so we can properly set the language and route the request
| to the proper end-point in the application.
|
*/

$uri = URI::current();

$languages = Config::get('application.languages', array());

$languages[] = Config::get('application.language');

/*
|--------------------------------------------------------------------------
| Set The Locale Based On The Route
|--------------------------------------------------------------------------
|
| If the URI starts with one of the supported languages, we will set
| the default lagnauge to match that URI segment and shorten the
| URI we'll pass to the router to not include the lang segment.
|
*/

foreach ($languages as $language)
{
	if (preg_match("#^{$language}(?:$|/)#i", $uri))
	{
		Config::set('application.language', $language);

		$uri = trim(substr($uri, strlen($language)), '/'); break;
	}
}

if ($uri == '') $uri = '/';

URI::$uri = $uri;

/*
|--------------------------------------------------------------------------
| Route The Incoming Request
|--------------------------------------------------------------------------
|
| Phew! We can finally route the request to the appropriate route and
| execute the route to get the response. This will give an instance
| of the Response object that we can send back to the browser
|
*/

Request::$route = Routing\Router::route(Request::method(), $uri);

$response = Request::$route->call();

/*
|--------------------------------------------------------------------------
| "Render" The Response
|--------------------------------------------------------------------------
|
| The render method evaluates the content of the response and converts it
| to a string. This evaluates any views and sub-responses within the
| content and sets the raw string result as the new response.
|
*/

$response->render();

/*
|--------------------------------------------------------------------------
| Persist The Session To Storage
|--------------------------------------------------------------------------
|
| If a session driver has been configured, we will save the session to
| storage so it is available for the next request. This will also set
| the session cookie in the cookie jar to be sent to the user.
|
*/

if (Config::get('session.driver') !== '')
{
	Session::save();
}

/*
|--------------------------------------------------------------------------
| Send The Response To The Browser
|--------------------------------------------------------------------------
|
| We'll send the response back to the browser here. This method will also
| send all of the response headers to the browser as well as the string
| content of the Response. This should make the view available to the
| browser and show something pretty to the user.
|
*/

$response->send();

/*
|--------------------------------------------------------------------------
| And We're Done!
|--------------------------------------------------------------------------
|
| Raise the "done" event so extra output can be attached to the response.
| This allows the adding of debug toolbars, etc. to the view, or may be
| used to do some kind of logging by the application.
|
*/

Event::fire('laravel.done', array($response));

/*
|--------------------------------------------------------------------------
| Finish the request for PHP-FastCGI
|--------------------------------------------------------------------------
|
| Stopping the PHP process for PHP-FastCGI users to speed up some
| PHP queries. Acceleration is possible when there are actions in the
| process of script execution that do not affect server response.
| For example, saving the session in memcached can occur after the page
| has been formed and passed to a web server.
*/

$response->foundation->finish();
