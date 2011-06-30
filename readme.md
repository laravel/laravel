## Laravel - A Clean & Classy PHP Framework

### For more information, visit [http://laravel.com](http://laravel.com)

### For complete documentation, visit [http://docs.laravel.com](http://docs.laravel.com)

Laravel is a clean and classy framework for PHP web development. Freeing you from spaghetti code, Laravel helps you create wonderful applications using simple, expressive syntax. Development should be a creative experience that you enjoy, not something that is painful. Enjoy the fresh air.

#### Simple, Expressive Syntax

Stay true to the web with RESTful routing:

	'GET /' => function()
	{
		return View::make('home/index');
	}

Redirect to a named route and flash something to the session:

	return Redirect::to_profile()->with('message', 'Welcome Back!');

Retrieve recent blog posts and eagerly load the comments using Eloquent ORM:

	$posts = Post::with('comments')->order_by('created_at', 'desc')->take(10)->get();

Get input from the previous request to re-populate a form:

	echo Input::old('email');