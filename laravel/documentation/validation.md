# Validation

## Contents

- [The Basics](#the-basics)
- [Validation Rules](#validation-rules)
- [Retrieving Error Message](#retrieving-error-messages)
- [Validation Walkthrough](#validation-walkthrough)
- [Custom Error Messages](#custom-error-messages)
- [Custom Validation Rules](#custom-validation-rules)

<a name="the-basics"></a>
## The Basics

Almost every interactive web application needs to validate data. For instance, a registration form probably requires the password to be confirmed. Maybe the e-mail address must be unique. Validating data can be a cumbersome process. Thankfully, it isn't in Laravel. The Validator class provides an awesome array of validation helpers to make validating your data a breeze. Let's walk through an example:

#### Get an array of data you want to validate:

	$input = Input::all();

#### Define the validation rules for your data:

	$rules = array(
		'name'  => 'required|max:50',
		'email' => 'required|email|unique:users',
	);

#### Create a Validator instance and validate the data:

	$validation = Validator::make($input, $rules);

	if ($validation->fails())
	{
		return $validation->errors;
	}

With the *errors* property, you can access a simple message collector class that makes working with your error messages a piece of cake. Of course, default error messages have been setup for all validation rules. The default messages live at **language/en/validation.php**.

Now you are familiar with the basic usage of the Validator class. You're ready to dig in and learn about the rules you can use to validate your data!

<a name="validation-rules"></a>
## Validation Rules

- [Required](#rule-required)
- [Alpha, Alpha Numeric, & Alpha Dash](#rule-alpha)
- [Size](#rule-size)
- [Numeric](#rule-numeric)
- [Inclusion & Exclusion](#rule-in)
- [Confirmation](#rule-confirmation)
- [Acceptance](#rule-acceptance)
- [Same & Different](#same-and-different)
- [Regular Expression Match](#regex-match)
- [Uniqueness & Existence](#rule-unique)
- [Dates](#dates)
- [E-Mail Addresses](#rule-email)
- [URLs](#rule-url)
- [Uploads](#rule-uploads)
- [Arrays](#rule-arrays)

<a name="rule-required"></a>
### Required

#### Validate that an attribute is present and is not an empty string:

	'name' => 'required'

#### Validate that an attribute is present, when another attribute is present:
	'last_name' => 'required_with:first_name'

<a name="rule-alpha"></a>
### Alpha, Alpha Numeric, & Alpha Dash

#### Validate that an attribute consists solely of letters:

	'name' => 'alpha'

#### Validate that an attribute consists of letters and numbers:

	'username' => 'alpha_num'

#### Validate that an attribute only contains letters, numbers, dashes, or underscores:

	'username' => 'alpha_dash'

<a name="rule-size"></a>
### Size

#### Validate that an attribute is a given length, or, if an attribute is numeric, is a given value:

	'name' => 'size:10'

#### Validate that an attribute size is within a given range:

	'payment' => 'between:10,50'

> **Note:** All minimum and maximum checks are inclusive.

#### Validate that an attribute is at least a given size:

	'payment' => 'min:10'

#### Validate that an attribute is no greater than a given size:

	'payment' => 'max:50'

<a name="rule-numeric"></a>
### Numeric

#### Validate that an attribute is numeric:

	'payment' => 'numeric'

#### Validate that an attribute is an integer:

	'payment' => 'integer'

<a name="rule-in"></a>
### Inclusion & Exclusion

#### Validate that an attribute is contained in a list of values:

	'size' => 'in:small,medium,large'

#### Validate that an attribute is not contained in a list of values:

	'language' => 'not_in:cobol,assembler'

<a name="rule-confirmation"></a>
### Confirmation

The *confirmed* rule validates that, for a given attribute, a matching *attribute_confirmation* attribute exists.

#### Validate that an attribute is confirmed:

	'password' => 'confirmed'

Given this example, the Validator will make sure that the *password* attribute matches the *password_confirmation* attribute in the array being validated.

<a name="rule-acceptance"></a>
### Acceptance

The *accepted* rule validates that an attribute is equal to *yes* or *1*. This rule is helpful for validating checkbox form fields such as "terms of service".

#### Validate that an attribute is accepted:

	'terms' => 'accepted'

<a name="same-and-different"></a>
## Same & Different

#### Validate that an attribute matches another attribute:

	'token1' => 'same:token2'

#### Validate that two attributes have different values:

	'password' => 'different:old_password',

<a name="regex-match"></a>
### Regular Expression Match

The *match* rule validates that an attribute matches a given regular expression.

#### Validate that an attribute matches a regular expression:

	'username' => 'match:/[a-z]+/';

<a name="rule-unique"></a>
### Uniqueness & Existence

#### Validate that an attribute is unique on a given database table:

	'email' => 'unique:users'

In the example above, the *email* attribute will be checked for uniqueness on the *users* table. Need to verify uniqueness on a column name other than the attribute name? No problem:

#### Specify a custom column name for the unique rule:

	'email' => 'unique:users,email_address'

Many times, when updating a record, you want to use the unique rule, but exclude the row being updated. For example, when updating a user's profile, you may allow them to change their e-mail address. But, when the *unique* rule runs, you want it to skip the given user since they may not have changed their address, thus causing the *unique* rule to fail. It's easy:

#### Forcing the unique rule to ignore a given ID:

	'email' => 'unique:users,email_address,10'

#### Validate that an attribute exists on a given database table:

	'state' => 'exists:states'

#### Specify a custom column name for the exists rule:

	'state' => 'exists:states,abbreviation'

<a name="dates"></a>
### Dates

#### Validate that a date attribute is before a given date:

	'birthdate' => 'before:1986-05-28';

#### Validate that a date attribute is after a given date:

	'birthdate' => 'after:1986-05-28';

> **Note:** The **before** and **after** validation rules use the **strtotime** PHP function to convert your date to something the rule can understand.

#### Validate that a date attribute conforms to a given format:

    'start_date' => 'date_format:H\\:i'),

> **Note:** The backslash escapes the colon so that it does not count as a parameter separator.

The formatting options for the date format are described in the [PHP documentation](http://php.net/manual/en/datetime.createfromformat.php#refsect1-datetime.createfromformat-parameters).

<a name="rule-email"></a>
### E-Mail Addresses

#### Validate that an attribute is an e-mail address:

	'address' => 'email'

> **Note:** This rule uses the PHP built-in *filter_var* method.

<a name="rule-url"></a>
### URLs

#### Validate that an attribute is a URL:

	'link' => 'url'

#### Validate that an attribute is an active URL:

	'link' => 'active_url'

> **Note:** The *active_url* rule uses *checkdnsr* to verify the URL is active.

<a name="rule-uploads"></a>
### Uploads

The *mimes* rule validates that an uploaded file has a given MIME type. This rule uses the PHP Fileinfo extension to read the contents of the file and determine the actual MIME type. Any extension defined in the *config/mimes.php* file may be passed to this rule as a parameter:

#### Validate that a file is one of the given types:

	'picture' => 'mimes:jpg,gif'

> **Note:** When validating files, be sure to use Input::file() or Input::all() to gather the input.

#### Validate that a file is an image:

	'picture' => 'image'

#### Validate that a file is no more than a given size in kilobytes:

	'picture' => 'image|max:100'

<a name="rule-arrays"></a>
### Arrays

#### Validate that an attribute is an array

	'categories' => 'array'

#### Validate that an attribute is an array, and has exactly 3 elements

	'categories' => 'array|count:3'

#### Validate that an attribute is an array, and has between 1 and 3 elements

	'categories' => 'array|countbetween:1,3'

#### Validate that an attribute is an array, and has at least 2 elements

	'categories' => 'array|countmin:2'

#### Validate that an attribute is an array, and has at most 2 elements

	'categories' => 'array|countmax:2'

<a name="retrieving-error-messages"></a>
## Retrieving Error Messages

Laravel makes working with your error messages a cinch using a simple error collector class. After calling the *passes* or *fails* method on a Validator instance, you may access the errors via the *errors* property. The error collector has several simple functions for retrieving your messages:

#### Determine if an attribute has an error message:

	if ($validation->errors->has('email'))
	{
		// The e-mail attribute has errors...
	}

#### Retrieve the first error message for an attribute:

	echo $validation->errors->first('email');

Sometimes you may need to format the error message by wrapping it in HTML. No problem. Along with the :message place-holder, pass the format as the second parameter to the method.

#### Format an error message:

	echo $validation->errors->first('email', '<p>:message</p>');

#### Get all of the error messages for a given attribute:

	$messages = $validation->errors->get('email');

#### Format all of the error messages for an attribute:

	$messages = $validation->errors->get('email', '<p>:message</p>');

#### Get all of the error messages for all attributes:

	$messages = $validation->errors->all();

#### Format all of the error messages for all attributes:

	$messages = $validation->errors->all('<p>:message</p>');

<a name="validation-walkthrough"></a>
## Validation Walkthrough

Once you have performed your validation, you need an easy way to get the errors back to the view. Laravel makes it amazingly simple. Let's walk through a typical scenario. We'll define two routes:

	Route::get('register', function()
	{
		return View::make('user.register');
	});

	Route::post('register', function()
	{
		$rules = array(...);

		$validation = Validator::make(Input::all(), $rules);

		if ($validation->fails())
		{
			return Redirect::to('register')->with_errors($validation);
		}
	});

Great! So, we have two simple registration routes. One to handle displaying the form, and one to handle the posting of the form. In the POST route, we run some validation over the input. If the validation fails, we redirect back to the registration form and flash the validation errors to the session so they will be available for us to display.

**But, notice we are not explicitly binding the errors to the view in our GET route**. However, an errors variable ($errors) will still be available in the view. Laravel intelligently determines if errors exist in the session, and if they do, binds them to the view for you. If no errors exist in the session, an empty message container will still be bound to the view. In your views, this allows you to always assume you have a message container available via the errors variable. We love making your life easier.

For example, if email address validation failed, we can look for 'email' within the $errors session var.

	$errors->has('email')

Using Blade, we can then conditionally add error messages to our view.

	{{ $errors->has('email') ? 'Invalid Email Address' : 'Condition is false. Can be left blank' }}

This will also work great when we need to conditionally add classes when using something like Twitter Bootstrap.
For example, if the email address failed validation, we may want to add the "error" class from Bootstrap to our *div class="control-group"* statement.

	<div class="control-group {{ $errors->has('email') ? 'error' : '' }}">

When the validation fails, our rendered view will have the appended *error* class.

	<div class="control-group error">



<a name="custom-error-messages"></a>
## Custom Error Messages

Want to use an error message other than the default? Maybe you even want to use a custom error message for a given attribute and rule. Either way, the Validator class makes it easy.

#### Create an array of custom messages for the Validator:

	$messages = array(
		'required' => 'The :attribute field is required.',
	);

	$validation = Validator::make(Input::get(), $rules, $messages);

Great! Now our custom message will be used anytime a required validation check fails. But, what is this **:attribute** stuff in our message? To make your life easier, the Validator class will replace the **:attribute** place-holder with the actual name of the attribute! It will even remove underscores from the attribute name.

You may also use the **:other**, **:size**, **:min**, **:max**, and **:values** place-holders when constructing your error messages:

#### Other validation message place-holders:

	$messages = array(
		'same'    => 'The :attribute and :other must match.',
		'size'    => 'The :attribute must be exactly :size.',
		'between' => 'The :attribute must be between :min - :max.',
		'in'      => 'The :attribute must be one of the following types: :values',
	);

So, what if you need to specify a custom required message, but only for the email attribute? No problem. Just specify the message using an **attribute_rule** naming convention:

#### Specifying a custom error message for a given attribute:

	$messages = array(
		'email_required' => 'We need to know your e-mail address!',
	);

In the example above, the custom required message will be used for the email attribute, while the default message will be used for all other attributes.

However, if you are using many custom error messages, specifying inline may become cumbersome and messy. For that reason, you can specify your custom messages in the **custom** array within the validation language file:

#### Adding custom error messages to the validation language file:

	'custom' => array(
		'email_required' => 'We need to know your e-mail address!',
	)

<a name="custom-validation-rules"></a>
## Custom Validation Rules

Laravel provides a number of powerful validation rules. However, it's very likely that you'll need to eventually create some of your own. There are two simple methods for creating validation rules. Both are solid so use whichever you think best fits your project.

#### Registering a custom validation rule:

	Validator::register('awesome', function($attribute, $value, $parameters)
	{
	    return $value == 'awesome';
	});

In this example we're registering a new validation rule with the validator. The rule receives three arguments. The first is the name of the attribute being validated, the second is the value of the attribute being validated, and the third is an array of parameters that were specified for the rule.

Here is how your custom validation rule looks when called:

	$rules = array(
    	'username' => 'required|awesome',
	);

Of course, you will need to define an error message for your new rule. You can do this either in an ad-hoc messages array:

	$messages = array(
    	'awesome' => 'The attribute value must be awesome!',
	);

	$validator = Validator::make(Input::get(), $rules, $messages);

Or by adding an entry for your rule in the **language/en/validation.php** file:

	'awesome' => 'The attribute value must be awesome!',

As mentioned above, you may even specify and receive a list of parameters in your custom rule:

	// When building your rules array...

	$rules = array(
	    'username' => 'required|awesome:yes',
	);

	// In your custom rule...

	Validator::register('awesome', function($attribute, $value, $parameters)
	{
	    return $value == $parameters[0];
	});

In this case, the parameters argument of your validation rule would receive an array containing one element: "yes".

Another method for creating and storing custom validation rules is to extend the Validator class itself. By extending the class you create a new version of the validator that has all of the pre-existing functionality combined with your own custom additions. You can even choose to replace some of the default methods if you'd like. Let's look at an example:

First, create a class that extends **Laravel\Validator** and place it in your **application/libraries** directory:

#### Defining a custom validator class:

	<?php

	class Validator extends Laravel\Validator {}

Next, remove the Validator alias from **config/application.php**. This is necessary so that you don't end up with 2 classes named "Validator" which will certainly conflict with one another.

Next, let's take our "awesome" rule and define it in our new class:

#### Adding a custom validation rule:

	<?php

	class Validator extends Laravel\Validator {

	    public function validate_awesome($attribute, $value, $parameters)
	    {
	        return $value == 'awesome';
	    }

	}

Notice that the method is named using the **validate_rule** naming convention. The rule is named "awesome" so the method must be named "validate_awesome". This is one way in which registering your custom rules and extending the Validator class are different. Validator classes simply need to return true or false. That's it!

Keep in mind that you'll still need to create a custom message for any validation rules that you create.  The method for doing so is the same no matter how you define your rule!
