# Building Forms

## Contents

- [Opening A Form](#opening-a-form)
- [CSRF Protection](#csrf-protection)
- [Labels](#labels)
- [Text, Text Area, Password & Hidden Fields](#text)
- [Checkboxes and Radio Buttons](#checkboxes-and-radio-buttons)
- [Drop-Down Lists](#drop-down-lists)
- [Buttons](#buttons)
- [Custom Macros](#custom-macros)

> **Note:** All input data displayed in form elements is filtered through the HTML::entities method.

<a name="opening-a-form"></a>
## Opening A Form

#### Opening a form to POST to the current URL:

	echo Form::open();

#### Opening a form using a given URI and request method:

	echo Form::open('user/profile', 'PUT');

#### Opening a Form that POSTS to a HTTPS URL:

	echo Form::open_secure('user/profile');

#### Specifying extra HTML attributes on a form open tag:

	echo Form::open('user/profile', 'POST', array('class' => 'awesome'));

#### Opening a form that accepts file uploads:

	echo Form::open_for_files('users/profile');

#### Opening a form that accepts file uploads and uses HTTPS:

	echo Form::open_secure_for_files('users/profile');

#### Closing a form:

	echo Form::close();

<a name="csrf-protection"></a>
## CSRF Protection

Laravel provides an easy method of protecting your application from cross-site request forgeries. First, a random token is placed in your user's session. Don't sweat it, this is done automatically. Next, use the token method to generate a hidden form input field containing the random token on your form:

#### Generating a hidden field containing the session's CSRF token:

	echo Form::token();

#### Attaching the CSRF filter to a route:

	Route::post('profile', array('before' => 'csrf', function()
	{
		//
	}));

#### Retrieving the CSRF token string:

	$token = Session::token();

> **Note:** You must specify a session driver before using the Laravel CSRF protection facilities.

*Further Reading:*

- [Route Filters](/docs/routing#filters)
- [Cross-Site Request Forgery](http://en.wikipedia.org/wiki/Cross-site_request_forgery)

<a name="labels"></a>
## Labels

#### Generating a label element:

	echo Form::label('email', 'E-Mail Address');

#### Specifying extra HTML attributes for a label:

	echo Form::label('email', 'E-Mail Address', array('class' => 'awesome'));

> **Note:** After creating a label, any form element you create with a name matching the label name will automatically receive an ID matching the label name as well.

<a name="text"></a>
## Text, Text Area, Password & Hidden Fields

#### Generate a text input element:

	echo Form::text('username');

#### Specifying a default value for a text input element:

	echo Form::text('email', 'example@gmail.com');

> **Note:** The *hidden* and *textarea* methods have the same signature as the *text* method. You just learned three methods for the price of one!

#### Generating a password input element:

	echo Form::password('password');

<a name="checkboxes-and-radio-buttons"></a>
## Checkboxes and Radio Buttons

#### Generating a checkbox input element:

	echo Form::checkbox('name', 'value');

#### Generating a checkbox that is checked by default:

	echo Form::checkbox('name', 'value', true);

> **Note:** The *radio* method has the same signature as the *checkbox* method. Two for one!

<a name="drop-down-lists"></a>
## Drop-Down Lists

#### Generating a drop-down list from an array of items:

	echo Form::select('size', array('L' => 'Large', 'S' => 'Small'));

#### Generating a drop-down list with an item selected by default:

	echo Form::select('size', array('L' => 'Large', 'S' => 'Small'), 'S');

<a name="buttons"></a>
## Buttons

#### Generating a submit button element:

	echo Form::submit('Click Me!');

> **Note:** Need to create a button element? Try the *button* method. It has the same signature as *submit*.

<a name="custom-macros"></a>
## Custom Macros

It's easy to define your own custom Form class helpers called "macros". Here's how it works. First, simply register the macro with a given name and a Closure:

#### Registering a Form macro:

	Form::macro('my_field', function()
	{
		return '<input type="awesome">';
	});

Now you can call your macro using its name:

#### Calling a custom Form macro:

	echo Form::my_field();