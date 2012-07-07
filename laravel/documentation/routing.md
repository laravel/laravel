# Routing

## Contents

- [The Basics](#the-basics)
- [Wildcards](#wildcards)
- [The 404 Events](#the-404-event)
- [Filters](#filters)
- [Pattern Filters](#pattern-filters)
- [Global Filters](#global-filters)
- [Route Groups](#route-groups)
- [Named Routes](#named-routes)
- [HTTPS Routes](#https-routes)
- [Bundle Routes](#bundle-routes)
- [Controller Routing](#controller-routing)
- [CLI Route Testing](#cli-route-testing)

<a name="the-basics"></a>
## The Basics

Laravel uses the latest features of PHP 5.3 to make routing simple and expressive. It's important that building everything from APIs to complex web applications is as easy as possible. Routes are typically defined in **application/routes.php**.

Unlike many other frameworks with Laravel it's possible to embed application logic in two ways. While controllers are the most common way to implement application logic it's also possible to embed your logic directly into routes. This is **especially** nice for small sites that contain only a few pages as you don't have to create a bunch of controllers just to expose half a dozen methods or put a handful of unrelated methods into the same controller and then have to manually designate routes that point to them.

In the following example the first parameter is the route that you're "registering" with the router. The second parameter is the function containing the logic for that route. Routes are defined without a front-slash. The only exception to this is the default route which is represented with **only** a front-slash.

> **Note:** Routes are evaluated in the order that they are registered, so register any "catch-all" routes at the bottom of your **routes.php** file.

#### Registering a route that responds to "GET /":

	Route::get('/', function()
	{
		return "Hello World!";
	});

#### Registering a route that is valid for any HTTP verb (GET, POST, PUT, and DELETE):

	Route::any('/', function()
	{
		return "Hello World!";
	});

#### Registering routes for other request methods:

	Route::post('user', function()
	{
		//
	});

	Route::put('user/(:num)', function($id)
	{
		//
	});

	Route::delete('user/(:num)', function($id)
	{
		//
	});

**Registering a single URI for multiple HTTP verbs:**

	Router::register(array('GET', 'POST'), $uri, $callback);

<a name="wildcards"></a>
## Wildcards

#### Forcing a URI segment to be any digit:

	Route::get('user/(:num)', function($id)
	{
		//
	});

#### Allowing a URI segment to be any alpha-numeric string:

	Route::get('post/(:any)', function($title)
	{
		//
	});

#### Allowing a URI segment to be optional:

	Route::get('page/(:any?)', function($page = 'index')
	{
		//
	});

<a name="the-404-event"></a>
## The 404 Event

If a request enters your application but does not match any existing route, the 404 event will be raised. You can find the default event handler in your **application/routes.php** file.

#### The default 404 event handler:

	Event::listen('404', function()
	{
		return Response::error('404');
	});

You are free to change this to fit the needs of your application!

*Futher Reading:*

- *[Events](/docs/events)*

<a name="filters"></a>
## Filters

Route filters may be run before or after a route is executed. If a "before" filter returns a value, that value is considered the response to the request and the route is not executed, which is conveniont when implementing authentication filters, etc. Filters are typically defined in **application/routes.php**.

#### Registering a filter:

	Route::filter('filter', function()
	{
		return Redirect::to('home');
	});

#### Attaching a filter to a route:

	Route::get('blocked', array('before' => 'filter', function()
	{
		return View::make('blocked');
	}));

#### Attaching an "after" filter to a route:

	Route::get('download', array('after' => 'log', function()
	{
		//
	}));

#### Attaching multiple filters to a route:

	Route::get('create', array('before' => 'auth|csrf', function()
	{
		//
	}));

#### Passing parameters to filters:

	Route::get('panel', array('before' => 'role:admin', function()
	{
		//
	}));

<a name="pattern-filters"></a>
## Pattern Filters

Sometimes you may want to attach a filter to all requests that begin with a given URI. For example, you may want to attach the "auth" filter to all requests with URIs that begin with "admin". Here's how to do it:

#### Defining a URI pattern based filter:

	Route::filter('pattern: admin/*', 'auth');

Optionally you can register filters directly when attaching filters to a given URI by supplying an array with the name of the filter and a callback.

#### Defining a filter and URI pattern based filter in one:

    Route::filter('pattern: admin/*', array('name' => 'auth', function()
    {
        // 
    }));

<a name="global-filters"></a>
## Global Filters

Laravel has two "global" filters that run **before** and **after** every request to your application. You can find them both in the **application/routes.php** file. These filters make great places to start common bundles or add global assets.

> **Note:** The **after** filter receives the **Response** object for the current request.

<a name="route-groups"></a>
## Route Groups

Route groups allow you to attach a set of attributes to a group of routes, allowing you to keep your code neat and tidy.

	Route::group(array('before' => 'auth'), function()
	{
		Route::get('panel', function()
		{
			//
		});

		Route::get('dashboard', function()
		{
			//
		});
	});

<a name="named-routes"></a>
## Named Routes

Constantly generating URLs or redirects using a route's URI can cause problems when routes are later changed. Assigning the route a name gives you a convenient way to refer to the route throughout your application. When a route change occurs the generated links will point to the new route with no further configuration needed.

#### Registering a named route:

	Route::get('/', array('as' => 'home', function()
	{
		return "Hello World";
	}));

#### Generating a URL to a named route:

	$url = URL::to_route('home');

#### Redirecting to the named route:

	return Redirect::to_route('home');

Once you have named a route, you may easily check if the route handling the current request has a given name.

#### Determine if the route handling the request has a given name:

	if (Request::route()->is('home'))
	{
		// The "home" route is handling the request!
	}

<a name="https-routes"></a>
## HTTPS Routes

When defining routes, you may use the "https" attribute to indicate that the HTTPS protocol should be used when generating a URL or Redirect to that route.

#### Defining an HTTPS route:

	Route::get('login', array('https' => true, function()
	{
		return View::make('login');
	}));

#### Using the "secure" short-cut method:

	Route::secure('GET', 'login', function()
	{
		return View::make('login');
	});

<a name="bundle-routes"></a>
## Bundle Routes

Bundles are Laravel's modular package system. Bundles can easily be configured to handle requests to your application. We'll be going over [bundles in more detail](/docs/bundles) in another document. For now, read through this section and just be aware that not only can routes be used to expose functionality in bundles, but they can also be registered from within bundles.

Let's open the **application/bundles.php** file and add something:

#### Registering a bundle to handle routes:

	return array(

		'admin' => array('handles' => 'admin'),

	);

Notice the new **handles** option in our bundle configuration array? This tells Laravel to load the Admin bundle on any requests where the URI begins with "admin".

Now you're ready to register some routes for your bundle, so create a **routes.php** file within the root directory of your bundle and add the following:

#### Registering a root route for a bundle:

	Route::get('(:bundle)', function()
	{
		return 'Welcome to the Admin bundle!';
	});

Let's explore this example. Notice the **(:bundle)** place-holder? That will be replaced with the value of the **handles** clause that you used to register your bundle. This keeps your code [D.R.Y.](http://en.wikipedia.org/wiki/Don't_repeat_yourself) and allows those who use your bundle to change it's root URI without breaking your routes! Nice, right?

Of course, you can use the **(:bundle)** place-holder for all of your routes, not just your root route.

#### Registering bundle routes:

	Route::get('(:bundle)/panel', function()
	{
		return "I handle requests to admin/panel!";
	});

<a name="controller-routing"></a>
## Controller Routing

Controllers provide another way to manage your application logic. If you're unfamiliar with controllers you may want to [read about controllers](/docs/controllers) and return to this section.

It is important to be aware that all routes in Laravel must be explicitly defined, including routes to controllers. This means that controller methods that have not been exposed through route registration **cannot** be accessed. It's possible to automatically expose all methods within a controller using controller route registration. Controller route registrations are typically defined in **application/routes.php**.

Most likely, you just want to register all of the controllers in your application's "controllers" directory. You can do it in one simple statement. Here's how:

#### Register all controllers for the application:

	Route::controller(Controller::detect());

The **Controller::detect** method simply returns an array of all of the controllers defined for the application.

If you wish to automatically detect the controllers in a bundle, just pass the bundle name to the method. If no bundle is specified, the application folder's controller directory will be searched.

#### Register all controllers for the "admin" bundle:

	Route::controller(Controller::detect('admin'));

#### Registering the "home" controller with the Router:

	Route::controller('home');

#### Registering several controllers with the router:

	Route::controller(array('dashboard.panel', 'admin'));

Once a controller is registered, you may access its methods using a simple URI convention:

	http://localhost/controller/method/arguments

This convention is similar to that employed by CodeIgniter and other popular frameworks, where the first segment is the controller name, the second is the method, and the remaining segments are passed to the method as arguments. If no method segment is present, the "index" method will be used.

This routing convention may not be desirable for every situation, so you may also explicitly route URIs to controller actions using a simple, intuitive syntax.

#### Registering a route that points to a controller action:

	Route::get('welcome', 'home@index');

#### Registering a filtered route that points to a controller action:

	Route::get('welcome', array('after' => 'log', 'uses' => 'home@index'));

#### Registering a named route that points to a controller action:

	Route::get('welcome', array('as' => 'home.welcome', 'uses' => 'home@index'));

<a name="cli-route-testing"></a>
## CLI Route Testing

You may test your routes using Laravel's "Artisan" CLI. Simple specify the request method and URI you want to use. The route response will be var_dump'd back to the CLI.

#### Calling a route via the Artisan CLI:

	php artisan route:call get api/user/1