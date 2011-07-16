## Routes

- [Defining Routes](/docs/start/routes#define)
- [Wildcard URI Segments](/docs/start/routes#segments)
- [Named Routes](/docs/start/routes#named)
- [Route Filters](/docs/start/routes#filters)
- [Organizing Routes](/docs/start/routes#organize)

Unlike other PHP frameworks, Laravel places routes and their corresponding functions in one file: **application/routes.php**. This file contains the "definition", or public API, of your application. To add functionality to your application, you add to the array located in this file. It's a breeze.

<a name="define"></a>
## Defining Routes

All you need to do is tell Laravel the request methods and URIs it should respond to. You define the behavior of the route using an anonymous method:

	'GET /home' => function()
	{
		// Handles GET requests to http://example.com/index.php/home
	},


You can easily define a route to handle requests to more than one URI. Just use commas:

	'POST /, POST /home' => function()
	{
		// Handles POST requests to http://example.com and http://example.com/home
	}

> **Note:** The routes.php file replaces the "controllers" found in most frameworks. Have a fat model and keep this file light and clean. Thank us later.

<a name="segments"></a>
## Wildcard URI Segments

Laravel makes matching wildcard URI segments a breeze using the **(:num)** and **(:any)** place-holders. Check out these routes:

	'PUT /user/(:num)' => function($id) {}

	'DELETE /user/(:any)' => function($username) {}

Laravel will automatically pass the value of the wildcard segment into your route function.

> **Note:** The **(:any)** place-holder matches letters, number, dashes, and underscores.

Want to make an URI segment optional? No problem. Just put a **?** in the place-holder:

	'GET /download/(:any?)' => function($branch = 'master') {}

If you need more power and precision (or just want to be extra nerdy), you can even use regular expressions:

	'GET /product/([0-9]+)' => function($id) {}

<a name="named"></a>
## Named Routes

Once you start using named routes, you won't be able to live without them. They are that great. Here's how to do it:

	'GET /user/login' => array('name' => 'login', 'do' => function() {})

Notice the route now has an array value with two keys: **name** and **do**. As you learned while studying filters, the **do** value is the method that will be executed by the route. As you have probably guessed, the **name** value is the name of the route.

Now that you have named the route, you can [generate URLs](/docs/start/views#urls) and [perform redirects](/docs/start/views#redirect) using the route name instead of the route URI. This means that you can change the route URI as much as you want and the links to that route on your views will always be correct. It's beautiful, isn't it?

<a name="filters"></a>
## Route Filters

Filters are methods that run before and after a request to your application. "Before" filters can even halt the request cycle by returning a response, providing an amazingly simple way to implement common tasks like redirecting a user to a login view. Let's dig in.

All filters are defined in the **application/filters.php** file. Intuitive, right? If you open the file, you will see that four filters have already been defined for you: **before**, **after**, **auth**, and **csrf**. The **before** and **after** filters are the two "global" filters. They are always executed on every request, regardless of the request method or URI.

All other filters must be attached to individual routes. Don't worry, you'll learn how to do this soon. The built-in **auth** and **csrf** filters handle two scenarios that are common to almost every web application: redirecting users to a login page and protecting against cross-site request forgeries.

### Defining Filters

To define your own filter, simply add it to the array in the **application/filters.php** file:

	'my_filter' => function()
	{
		return 'Filtered!';
	}

### Attaching Filters To Routes

Alright, ready to attach the filter to a route? Do it like this:

	'GET /user' => array('before' => 'my_filter', 'do' => function() 
	{
		//	
	})

Notice the route now has an array value with two keys: **before** and **do**. The **do** value is the method that will be executed by the route, while the **before** value contains the names of any filters that should be run before the method is executed.

Why stop with one filter? You can define multiple filters for a single route by separating the filter names with commas:

	'POST /user' => array('before' => 'auth, csrf', 'do' => function() {})

Remember, if a "before" filter returns a value, that value will be considered the output of the request. For example, the built-in **auth** filter checks if the user has logged in to your application. If they haven't, a [Redirect](/docs/start/views#redirect) to the login page is sent to the browser. Isn't the simplicity refreshing?

Of course, adding filters to run after the request is just as easy:

	'my_filter' => function($response) {}

	'GET /user' => array('after' => 'my_filter', 'do' => function() {})

> **Note:** "After" filters receive the response returned by the route function that handled the request.

<a name="organize"></a>
## Organizing Routes

So, you're building the next monolithic web application and your **application/routes.php** file is getting a little cramped? Don't worry, we have you covered.

Here's what to do. First, create an **application/routes** directory. Great! You're almost there. Now, just add route files to **application/routes** corresponding to the base URIs of your application. So, a **photo.php** file within **application/routes** would handle all requests to URIs beginning with **/photo**. Similarly, a **user.php** file handles all requests to URIs beginning with **/user**. For example, check out this **user.php** file:

	<?php

	return array(

		'GET /user/profile/(:num)' => function($id)
		{
			return View::make('user/profile');
		}

	);

The **application/routes.php** file will continue to be loaded on every request, so any "catch-all" routes can still be placed in that file. The **application/routes.php** file should also still contain the route for the root of your application.