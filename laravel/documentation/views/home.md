# Views & Responses

## Contents

- [The Basics](#the-basics)
- [Binding Data To Views](#binding-data-to-views)
- [Nesting Views](#nesting-views)
- [Named Views](#named-views)
- [View Composers](#view-composers)
- [Redirects](#redirects)
- [Redirecting With Flash Data](#redirecting-with-flash-data)
- [Downloads](#downloads)
- [Errors](#errors)

<a name="the-basics"></a>
## The Basics

Views contain the HTML that is sent to the person using your application. By separating your view from the business logic of your application, your code will be cleaner and easier to maintain.

All views are stored within the **application/views** directory and use the PHP file extension. The **View** class provides a simple way to retrieve your views and return them to the client. Let's look at an example!

#### Creating the view:

	<html>
		I'm stored in views/home/index.php!
	</html>

#### Returning the view from a route:

	Route::get('/', function()
	{
		return View::make('home.index');
	});

#### Returning the view from a controller:

	public function action_index()
	{
		return View::make('home.index');
	});

#### Determining if a view exists:

	$exists = View::exists('home.index');

Sometimes you will need a little more control over the response sent to the browser. For example, you may need to set a custom header on the response, or change the HTTP status code. Here's how:

#### Returning a custom response:

	Route::get('/', function()
	{
		$headers = array('foo' => 'bar');

		return Response::make('Hello World!', 200, $headers);
	});

#### Returning a custom response containing a view, with binding data:

	return Response::view('home', array('foo' => 'bar'));

#### Returning a JSON response:

	return Response::json(array('name' => 'Batman'));

#### Returning a JSONP response:

	return Response::jsonp('myCallback', array('name' => 'Batman'));

#### Returning Eloquent models as JSON:

	return Response::eloquent(User::find(1));

<a name="binding-data-to-views"></a>
## Binding Data To Views

Typically, a route or controller will request data from a model that the view needs to display. So, we need a way to pass the data to the view. There are several ways to accomplish this, so just pick the way that you like best!

#### Binding data to a view:

	Route::get('/', function()
	{
		return View::make('home')->with('name', 'James');
	});

#### Accessing the bound data within a view:

	<html>
		Hello, <?php echo $name; ?>.
	</html>

#### Chaining the binding of data to a view:

	View::make('home')
		->with('name', 'James')
		->with('votes', 25);

#### Passing an array of data to bind data:

	View::make('home', array('name' => 'James'));

#### Using magic methods to bind data:

	$view->name  = 'James';
	$view->email = 'example@example.com';

#### Using the ArrayAccess interface methods to bind data:

	$view['name']  = 'James';
	$view['email'] = 'example@example.com';

<a name="nesting-views"></a>
## Nesting Views

Often you will want to nest views within views. Nested views are sometimes called "partials", and help you keep views small and modular.

#### Binding a nested view using the "nest" method:

	View::make('home')->nest('footer', 'partials.footer');

#### Passing data to a nested view:

	$view = View::make('home');

	$view->nest('content', 'orders', array('orders' => $orders));

Sometimes you may wish to directly include a view from within another view. You can use the **render** helper function:

#### Using the "render" helper to display a view:

	<div class="content">
		<?php echo render('user.profile'); ?>
	</div>

It is also very common to have a partial view that is responsible for display an instance of data in a list. For example, you may create a partial view responsible for displaying the details about a single order. Then, for example, you may loop through an array of orders, rendering the partial view for each order. This is made simpler using the **render_each** helper:

#### Rendering a partial view for each item in an array:

	<div class="orders">
		<?php echo render_each('partials.order', $orders, 'order');
	</div>

The first argument is the name of the partial view, the second is the array of data, and the third is the variable name that should be used when each array item is passed to the partial view.

<a name="named-views"></a>
## Named Views

Named views can help to make your code more expressive and organized. Using them is simple:

#### Registering a named view:

	View::name('layouts.default', 'layout');

#### Getting an instance of the named view:

	return View::of('layout');

#### Binding data to a named view:

	return View::of('layout', array('orders' => $orders));

<a name="view-composers"></a>
## View Composers

Each time a view is created, its "composer" event will be fired. You can listen for this event and use it to bind assets and common data to the view each time it is created. A common use-case for this functionality is a side-navigation partial that shows a list of random blog posts. You can nest your partial view by loading it in your layout view. Then, define a composer for that partial. The composer can then query the posts table and gather all of the necessary data to render your view. No more random logic strewn about! Composers are typically defined in **application/routes.php**. Here's an example:

#### Register a view composer for the "home" view:

	View::composer('home', function($view)
	{
		$view->nest('footer', 'partials.footer');
	});

Now each time the "home" view is created, an instance of the View will be passed to the registered Closure, allowing you to prepare the view however you wish.

#### Register a composer that handles multiple views:

	View::composer(array('home', 'profile'), function($view)
	{
		//
	});

> **Note:** A view can have more than one composer. Go wild!

<a name="redirects"></a>
## Redirects

It's important to note that both routes and controllers require responses to be returned with the 'return' directive. Instead of calling "Redirect::to()" where you'd like to redirect the user. You'd instead use "return Redirect::to()". This distinction is important as it's different than most other PHP frameworks and it could be easy to accidentally overlook the importance of this practice.

#### Redirecting to another URI:

	return Redirect::to('user/profile');

#### Redirecting with a specific status:

	return Redirect::to('user/profile', 301);

#### Redirecting to a secure URI:

	return Redirect::to_secure('user/profile');

#### Redirecting to the root of your application:

	return Redirect::home();

#### Redirecting back to the previous action:

	return Redirect::back();

#### Redirecting to a named route:

	return Redirect::to_route('profile');

#### Redirecting to a controller action:

	return Redirect::to_action('home@index');

Sometimes you may need to redirect to a named route, but also need to specify the values that should be used instead of the route's URI wildcards. It's easy to replace the wildcards with proper values:

#### Redirecting to a named route with wildcard values:

	return Redirect::to_route('profile', array($username));

#### Redirecting to an action with wildcard values:

	return Redirect::to_action('user@profile', array($username));

<a name="redirecting-with-flash-data"></a>
## Redirecting With Flash Data

After a user creates an account or signs into your application, it is common to display a welcome or status message. But, how can you set the status message so it is available for the next request? Use the with() method to send flash data along with the redirect response.

	return Redirect::to('profile')->with('status', 'Welcome Back!');

You can access your message from the view with the Session get method:

	$status = Session::get('status');

*Further Reading:*

- *[Sessions](/docs/session/config)*

<a name="downloads"></a>
## Downloads

#### Sending a file download response:

	return Response::download('file/path.jpg');

#### Sending a file download and assigning a file name:

	return Response::download('file/path.jpg', 'photo.jpg');

<a name="errors"></a>
## Errors

To generating proper error responses simply specify the response code that you wish to return. The corresponding view stored in **views/error** will automatically be returned.

#### Generating a 404 error response:

	return Response::error('404');

#### Generating a 500 error response:

	return Response::error('500');
