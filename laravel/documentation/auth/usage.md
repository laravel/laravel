# Authentication Usage

## Contents

- [Salting & Hashing](#hash)
- [Logging In](#login)
- [Protecting Routes](#filter)
- [Retrieving The Logged In User](#user)
- [Logging Out](#logout)
- [Writing Custom Drivers](#drivers)

> **Note:** Before using the Auth class, you must [specify a session driver](/docs/session/config).

<a name="hash"></a>
## Salting & Hashing

If you are using the Auth class, you are strongly encouraged to hash and salt all passwords. Web development must be done responsibly. Salted, hashed passwords make a rainbow table attack against your user's passwords impractical.

Salting and hashing passwords is done using the **Hash** class. The Hash class is uses the **bcrypt** hashing algorithm. Check out this example:

	$password = Hash::make('secret');

The **make** method of the Hash class will return a 60 character hashed string.

You can compare an unhashed value against a hashed one using the **check** method on the **Hash** class:

	if (Hash::check('secret', $hashed_value))
	{
		return 'The password is valid!';
	}

<a name="login"></a>
## Logging In

Logging a user into your application is simple using the **attempt** method on the Auth class. Simply pass the username and password of the user to the method. The credentials should be contained in an array, which allows for maximum flexibility across drivers, as some drivers may require a different number of arguments. The login method will return **true** if the credentials are valid. Otherwise, **false** will be returned:

	$credentials = array('username' => 'example@gmail.com', 'password' => 'secret');

	if (Auth::attempt($credentials))
	{
	     return Redirect::to('user/profile');
	}

If the user's credentials are valid, the user ID will be stored in the session and the user will be considered "logged in" on subsequent requests to your application.

To determine if the user of your application is logged in, call the **check** method:

	if (Auth::check())
	{
	     return "You're logged in!";
	}

Use the **login** method to login a user without checking their credentials, such as after a user first registers to use your application. Just pass your user object or the user's ID:

	Auth::login($user);

	Auth::login(15);

<a name="filter"></a>
## Protecting Routes

It is common to limit access to certain routes only to logged in users. In Laravel this is accomplished using the [auth filter](/docs/routing#filters). If the user is logged in, the request will proceed as normal; however, if the user is not logged in, they will be redirected to the "login" [named route](/docs/routing#named-routes).

To protect a route, simply attach the **auth** filter:

	Route::get('admin', array('before' => 'auth', function() {});

> **Note:** You are free to edit the **auth** filter however you like. A default implementation is located in **application/routes.php**.

<a name="user"></a>
## Retrieving The Logged In User

Once a user has logged in to your application, you can access the user model via the **user** method on the Auth class:

	return Auth::user()->email;

> **Note:** If the user is not logged in, the **user** method will return NULL.

<a name="logout"></a>
## Logging Out

Ready to log the user out of your application?

	Auth::logout();

This method will remove the user ID from the session, and the user will no longer be considered logged in on subsequent requests to your application.