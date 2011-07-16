## Validation

- [The Basics](#basics)
- [Validation Rules](#rules)
- [Retrieving Error Messages](#errors)
- [Specifying Custom Error Messages](#messages)
- [Creating Custom Validation Rules](#custom)

<a name="basics"></a>
### The Basics

Almost every interactive web application needs to validate data. For instance, a registration form probably requires the password to be confirmed. Maybe the e-mail address must be unique. Validating data can be a cumbersome process. Thankfully, it isn't in Laravel. The **Validator** class provides as awesome array of validation helpers to make validating your data a breeze.

To get started, let's imagine we have the following array:

	$array = array('name' => 'Taylor', 'email' => 'example@gmail.com');

Next, we're ready to define [validation rules](#rules) for our array:

	$rules = array(
		'name'  => array('required', 'max:50'),
		'email' => array('required', 'email', 'unique:users'),
	);

If you don't like using arrays, you may also delimit rules using a pipe character:

	$rules = array(
		'name'  => 'required|max:50',
		'email' => 'required|email|unique:users',
	);

Great! Now we're ready to make a **Validator** instance and validate our array:

	$validator = Validator::make($array, $rules);

	if ( ! $validator->valid())
	{
		return $validator->errors;
	}

Via the **errors** property, you can access a simple error collector class that makes working with your error messages a breeze. Of course, default error messages have been setup for all validation rules. The default messages live in the **application/lang/en/validation.php** file.

Now you are familiar with the basic usage of the Validator class. You're ready to dig in and learn about the rules you can use to validate your data!

<a name="rules"></a>
### Validation Rules

- [Required](#rule-required)
- [Alpha, Alpha Numeric, & Alpha Dash](#rule-alphas)
- [Size](#rule-size)
- [Numericality](#rule-numeric)
- [Inclusion & Exclusion](#rule-inclusion)
- [Confirmation](#rule-confirmed)
- [Acceptance](#rule-accepted)
- [Uniqueness](#rule-unique)
- [E-Mail Addresses](#rule-email)
- [URLs](#rule-urls)
- [Uploads](#rule-uploads)

<a name="rule-required"></a>
#### Required

The **required** rule validates that an attribute is present in the array and is not an empty string:

	$rules = array(
		'name' => 'required',
	);

<a name="rule-alphas"></a>
#### Alpha, Alpha Numeric, & Alpha Dash

The **alpha** rule validates that an attribute consists solely of letters:

	$rules = array(
		'name' => 'alpha',
	);

The **alpha_num** rule validates that an attribute consists solely of letters and numbers:

	$rules = array(
		'username' => 'alpha_num',
	);

The **alpha_dash** rule validates that an attribute consists solely of letters, numbers, dashes, and underscores:

	$rules = array(
		'username' => 'alpha_dash',
	);

<a name="rule-size"></a>
#### Size

The **size** rule validates that an attribute is of a given length, or, if the attribute is numeric, is a given value:

	$rules = array(
		'name' => 'size:10',
	);

The **between** rule validates that an attribute is between a given minimum and maximum:

	$rules = array(
		'payment' => 'between:10,50',
	);

> **Note:** All minimum and maximum checks are inclusive.

The **min** rule validates that an attribute is greater than or equal to a given value:

	$rules = array(
		'payment' => 'min:10',
	);

The **max** rule validates that an attribute is less than or equal to a given value:

	$rules = array(
		'payment' => 'max:50',
	);

<a name="rule-numeric"></a>
#### Numericality

The **numeric** rule validates that an attribute is (surprise!) numeric:

	$rules = array(
		'payment' => 'numeric',
	);

The **integer** rule validates that an attribute is an integer:

	$rules = array(
		'payment' => 'integer',
	);

<a name="rule-inclusion"></a>
#### Inclusion & Exclusion

The **in** rule validates that an attribute is contained in a list of values:

	$rules = array(
		'size' => 'in:small,medium,large',
	);

The **not_in** rule validates that an attribute is not contained in a list of values:

	$rules = array(
		'language' => 'not_in:cobol,assembler',
	);

<a name="rule-confirmed"></a>
#### Confirmation

The **confirmed** rule validates that, for a given attribute, a matching **attribute_confirmation** attribute exists. For example, given the following rule:

	$rules = array(
		'password' => 'confirmed',
	);

The Validator will make sure that the **password** attribute matches the **password_confirmation** attribute in the array being validated.

<a name="rule-accepted"></a>
#### Acceptance

The **accepted** rule validates that an attribute is equal to **yes** or **1**. This rule is helpful for validating checkbox form fields such as "terms of service".

	$rules = array(
		'terms' => 'accepted',
	);

<a name="rule-unique"></a>
#### Uniqueness

The **unique** rule validates the uniqueness of an attribute on a given database table:

	$rules = array(
		'email' => 'unique:users',
	);

In the example above, the **email** attribute will be checked for uniqueness on the **users** table. Need to verify uniqueness on a column name other than the attribute name? No problem:

	$rules = array(
		'email' => 'unique:users,email_address',
	);

<a name="rule-email"></a>
#### E-Mail Addresses

The **email** rule validates that an attribute contains a correctly formatted e-mail address:

	$rules = array(
		'email' => 'email',
	);

<a name="rule-urls"></a>
#### URLs

The **url** rule validates that an attribute contains a correctly formatted URL:

	$rules = array(
		'link' => 'url',
	);

The **active_url** rule uses the PHP **checkdnsrr** function to verify that a URL is active:

	$rules = array(
		'link' => 'active_url',
	);

<a name="rule-uploads"></a>
#### Uploads

The **mimes** rule validates that an uploaded file has a given MIME type. This rule uses the PHP Fileinfo extension to read the contents of the file and determine the actual MIME type. Any extension defined in the **application/config/mimes.php** file may be passed to this rule as a parameter:

	$rules = array(
		'picture' => 'mimes:jpg,gif',
	);

	$validator = Validator::make(Input::file(), $rules);

Need to validate form data and upload data at the same time? Use the **all** method on the **Input** class to get form and upload data in one array:

	$validator = Validator::make(Input::all(), $rules);

The **image** rule validates that an uploaded file has a **jpg**, **gif**, **bmp**, or **png** MIME type:

	$rules = array(
		'picture' => 'image',
	);

You may also validate the size of an upload using the **max** rule. Simply specify the maximum number of **kilobytes** the upload may be:

	$rules = array(
		'picture' => 'image|max:100',
	);

<a name="errors"></a>
### Retrieving Error Messages

Laravel makes working with your error messages a cinch using a simple error collector class. After calling the **valid** or **invalid** method on a **Validator** instance, you may access the errors via the **errors** property:

	if ( ! $validator->valid())
	{
		return $validator->errors;
	}

The error collector has the following simple functions for retrieving your error messages: **has**, **first**, **get**, and **all**.

The **has** method will check if an error message exists for a given attribute:

	if ($validator->errors->has('email'))
	{
		// The e-mail attribute has errors...
	}

The **first** method will return the first error message for a given attribute:

	echo $validator->errors->first('email');

Sometimes you may need to format the error message by wrapping it in HTML. No problem. Along with the **:message** place-holder, pass the format as the second parameter to the method:

	echo $validator->errors->first('email', '<p>:message</p>');

The **get** method returns an array containing all of the error messages for a given attribute:

	return $validator->errors->get('email');

	return $validator->errors->get('email', '<p>:message</p>');

The **all** method returns an array containing all error messages for all attributes:

	return $validator->errors->all();

	return $validator->errors->all('<p>:message</p>');

<a name="messages"></a>
### Specifying Custom Error Messages

Want to use an error message other than the default? Maybe you even want to use a custom error message for a given attribute and rule. Either way, the **Validator** class makes it easy.

Simply create an array of custom messages to pass to the Validator instance:

	$messages = array(
		'required' => 'The :attribute field is required.',
	);

	$validator = Validator::make(Input::get(), $rules, $messages);

Great! Now our custom message will be used anytime a **required** validation check fails. But, what is this **:attribute** stuff in our message? To make your life easier, the Validator class will replace the **:attribute** place-holder with the actual name of the attribute! It will even remove underscores from the attribute name.

You may also use the **:size**, **:min**, **:max**, and **:values** place-holders when constructing your error messages:

	$messages = array(
		'size'    => 'The :attribute must be exactly :size.',
		'between' => 'The :attribute must be between :min - :max.',
		'in'      => 'The :attribute must be one of the following types: :values',
	);

So, what if you need to specify a custom **required** message, but only for the **email** attribute? No problem. Just specify the message using an **attribute_rule** naming convention:

	$messages = array(
		'email_required' => 'We need to know your e-mail address!',
	);

In the example above, the custom required message will be used for the **email** attribute, while the default message will be used for all other attributes.

<a name="custom"></a>
### Creating Custom Validation Rules

Need to create your own validation rules? You will love how easy it is! First, create a class that extends **System\Validator** and place it in your **application/libraries** directory:

	<?php

	class Validator extends System\Validator {}

Next, remove the **Validator** alias from **application/config/aliases.php**.

Alright! You're ready to define your own validation rule. Create a function on your new validator using a **validate_rule** naming convention. Validator methods simply need to return **true** or **false**. It couldn't be any easier, right?

	<?php

	class Validator extends System\Validator {
		
		public function validate_awesome($attribute, $parameters)
		{
			return $attribute == 'awesome';
		}

	}

Let's dig into this example. The **validate_awesome** function receives two arguments. The first is the value of the attribute being validated, the second is an array of parameters that were specified for the rule, such as a size or list of accepted values (more on that in a second).

Now, how do you use your new validator? It's refreshingly simple:

	$rules = array(
		'username' => 'required|awesome',
	);

Of course, you will need to define an error message for your new rule. You can do this either in an ad-hoc messages array:

	$messages = array(
		'awesome' => 'The attribute value must be awesome!',
	);

	$validator = Validator::make(Input::get(), $rules, $messages);

Or by adding an entry for your rule in the **application/lang/en/validation.php** file:

	'awesome' => 'The attribute value must be awesome!',

As mentioned above, you may even specify and receive a list of parameters in your custom validator:

	// When building your rules array...

	$rules = array(
		'username' => 'required|awesome:yes',
	);

	// In your custom validator...

	class Validator extends System\Validator {
		
		public function validate_awesome($attribute, $parameters)
		{
			return $attribute == $parameters[0];
		}

	}

In this case, the **parameters** argument of your validation rule would receive an array containing one element: "yes".