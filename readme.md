## Laravel - A PHP Framework For Web Artisans

Laravel is a clean and classy framework for PHP web development. Freeing you from spaghetti code, Laravel helps you create wonderful applications using simple, expressive syntax. Development should be a creative experience that you enjoy, not something that is painful. Enjoy the fresh air.

### Quickly Build Beautiful Applications

Stay true to the web with RESTful routing:

	'GET /' => function()
	{
		return View::make('home.index');
	}

Laravel serves as a great framework for writing everything from JSON APIs to full web applications. You can use RESTful routes and anonymous functions to quickly build beautiful applications, or use controllers to organize your creation:

	class Home_Controller extends Controller {
		
		public function action_index()
		{
			return View::make('home.index');
		}

	}

### Wonderfully Expressive Syntax

Laravel strives to provide intuitive, expressive syntax. Code should be immediately readable and understandable. Need to redirect to a named route and flash something to the session? Here's how:

	return Redirect::to_profile()->with('message', 'Welcome Back!');

Laravel makes common tasks refreshingly simple. What other frameworks make drudgery Laravel makes blissful. Need to flash input to the session and redirect to a form? It's a breeze:

	return Redirect::to('register')->with_input();

Then access the previous request's input using the Input class:

	echo Input::old('email');

### Dead Simple Data Access

Laravel provides several ways to access your data, ranging from raw SQL to a beautiful little ORM. MySQL, Postgres, and SQLite are supported out of the box.

Retrieve a blog post and eagerly load the comments using Eloquent ORM:

	$posts = Post::with('comments')->find(1);

Use the fluent query builder to execute SQL statements:

	$post = DB::table('posts')->where('id', '=', 1)->first();

	DB::table('users')->where_id(1)->update(array('name' => 'Taylor'));

### Hassle Free Sessions and Caching

Use one of the six wonderful session providrs: Cookie, File, Database, APC, Memcached, and Redis.

Utilizing sessions couldn't be easier:

	Session::put('name', 'Taylor');

	$name = Session::get('name');

Need to cache some data from your database? It couldn't be simpler to use the cache drivers:

	Cache::put('users', $users, $minutes);

	$users = Cache::get('users');

	$users = Cache::driver('memcached')->get('users');

### There's so much more to learn:

Check out the [official documentation](http://laravel.com).