<?php
/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylorotwell@gmail.com>
 */

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader
| for our application. We just need to utilize it! We'll require it
| into the script here so that we do not have to worry about the
| loading of any our classes "manually". Feels great to relax.
|
*/

require __DIR__.'/../bootstrap/autoload.php';

/*
|--------------------------------------------------------------------------
| Turn On The Lights
|--------------------------------------------------------------------------
|
| We need to illuminate PHP development, so let's turn on the lights.
| This bootstraps the framework and gets it ready for use, then it
| will load up this application so that we can run it and send
| the responses back to the browser and delight these users.
|
*/

$app = require_once __DIR__.'/../bootstrap/start.php';

/*
|--------------------------------------------------------------------------
| Capture The Request
|--------------------------------------------------------------------------
|
| Next we will capture the HTTP request into an instance of the Symfony
| request class. We will then pass that to a Laravel application for
| processing and return the response we receive back from the app.
|
*/

use Symfony\Component\HttpFoundation\Request;

$request = Request::createFromGlobals();

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can simply call the run method,
| which will execute the request and send the response back to
| the client's browser allowing them to enjoy the creative
| and wonderful application we have whipped up for them.
|
*/

$response = with(new Stack\Builder)
				->push('Illuminate\Foundation\TrailingSlashRedirector')
				->resolve($app)
				->handle($request);

/*
|--------------------------------------------------------------------------
| Close The Application & Send Response
|--------------------------------------------------------------------------
|
| When closing the application, the session cookies will be set on the
| request. Also, this is an opportunity to finish up any other work
| that needs to be done before sending this response to browsers.
|
*/

$app->callCloseCallbacks($request, $response);

$response->send();

/*
|--------------------------------------------------------------------------
| Shutdown The Application
|--------------------------------------------------------------------------
|
| Once the app has finished running we'll fire off the shutdown events
| so that any end work may be done by an application before we shut
| off the process. This is the final thing to happen to requests.
|
*/

$app->terminate($request, $response);