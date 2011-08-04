## Laravel - A Clean & Classy PHP Framework

Laravel is a clean and classy framework for PHP web development. Freeing you from spaghetti code, Laravel helps you create wonderful applications using simple, expressive syntax. Development should be a creative experience that you enjoy, not something that is painful. Enjoy the fresh air.

### Beautifully Expressive Syntax

Stay true to the web with RESTful routing:

	'GET /' => function()
	{
		return View::make('home/index');
	}

Redirect to a named route and flash something to the session:

	return Redirect::to_profile()->with('message', 'Welcome Back!');

Retrieve a blog post and eagerly load the comments using Eloquent ORM:

	$posts = Post::with('comments')->find(1);

Get input from the previous request to re-populate a form:

	echo Input::old('email');

### Ready To Learn More?

Check out the [official documentation](http://laravel.com).