# Input & Cookies

## Contents

- [Input](#input)
- [JSON Input](#json)
- [Files](#files)
- [Old Input](#old-input)
- [Redirecting With Old Input](#redirecting-with-old-input)
- [Cookies](#cookies)
- [Merging & Replacing](#merge)

<a name="input"></a>
## Input

The **Input** class handles input that comes into your application via GET, POST, PUT, or DELETE requests. Here are some examples of how to access input data using the Input class:

#### Retrieve a value from the input array:

	$email = Input::get('email');

> **Note:** The "get" method is used for all request types (GET, POST, PUT, and DELETE), not just GET requests.

#### Retrieve all input from the input array:

	$input = Input::get();

#### Retrieve all input including the $_FILES array:

	$input = Input::all();

By default, *null* will be returned if the input item does not exist. However, you may pass a different default value as a second parameter to the method:

#### Returning a default value if the requested input item doesn't exist:

	$name = Input::get('name', 'Fred');

#### Using a Closure to return a default value:

	$name = Input::get('name', function() {return 'Fred';});

#### Determining if the input contains a given item:

	if (Input::has('name')) ...

> **Note:** The "has" method will return *false* if the input item is an empty string.

<a name="json"></a>
## JSON Input

When working with JavaScript MVC frameworks like Backbone.js, you will need to get the JSON posted by the application. To make your life easier, we've included the `Input::json` method:

#### Get JSON input to the application:

	$data = Input::json();

<a name="files"></a>
## Files

#### Retrieving all items from the $_FILES array:

	$files = Input::file();

#### Retrieving an item from the $_FILES array:

	$picture = Input::file('picture');

#### Retrieving a specific item from a $_FILES array:

	$size = Input::file('picture.size');

<a name="old-input"></a>
## Old Input

You'll commonly need to re-populate forms after invalid form submissions. Laravel's Input class was designed with this problem in mind. Here's an example of how you can easily retrieve the input from the previous request. First, you need to flash the input data to the session:

#### Flashing input to the session:

	Input::flash();

#### Flashing selected input to the session:

	Input::flash('only', array('username', 'email'));

	Input::flash('except', array('password', 'credit_card'));

#### Retrieving a flashed input item from the previous request:

	$name = Input::old('name');

> **Note:** You must specify a session driver before using the "old" method.

*Further Reading:*

- *[Sessions](/docs/session/config)*

<a name="redirecting-with-old-input"></a>
## Redirecting With Old Input

Now that you know how to flash input to the session. Here's a shortcut that you can use when redirecting that prevents you from having to micro-manage your old input in that way:

#### Flashing input from a Redirect instance:

	return Redirect::to('login')->with_input();

#### Flashing selected input from a Redirect instance:

	return Redirect::to('login')->with_input('only', array('username'));

	return Redirect::to('login')->with_input('except', array('password'));

<a name="cookies"></a>
## Cookies

Laravel provides a nice wrapper around the $_COOKIE array. However, there are a few things you should be aware of before using it. First, all Laravel cookies contain a "signature hash". This allows the framework to verify that the cookie has not been modified on the client. Secondly, when setting cookies, the cookies are not immediately sent to the browser, but are pooled until the end of the request and then sent together. This means that you will not be able to both set a cookie and retrieve the value that you set in the same request.

#### Retrieving a cookie value:

	$name = Cookie::get('name');

#### Returning a default value if the requested cookie doesn't exist:

	$name = Cookie::get('name', 'Fred');

#### Setting a cookie that lasts for 60 minutes:

	Cookie::put('name', 'Fred', 60);

#### Creating a "permanent" cookie that lasts five years:

	Cookie::forever('name', 'Fred');

#### Deleting a cookie:

	Cookie::forget('name');

<a name="merge"></a>
## Merging & Replacing

Sometimes you may wish to merge or replace the current input. Here's how:

#### Merging new data into the current input:

	Input::merge(array('name' => 'Spock'));

#### Replacing the entire input array with new data:

	Input::merge(array('doctor' => 'Bones', 'captain' => 'Kirk'));