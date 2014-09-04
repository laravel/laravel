<?php 

return array(

	/*
	|--------------------------------------------------------------------------
	| Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| The following language lines contain the default error messages used
	| by the validator class. Some of the rules contain multiple versions,
	| such as the size (max, min, between) rules. These versions are used
	| for different input types such as strings and files.
	|
	| These language lines may be easily changed to provide custom error
	| messages in your application. Error messages for custom validation
	| rules may also be added to this file.
	|
	*/

	"accepted"       => "The :attribute must be accepted.",
	"active_url"     => "The :attribute is not a valid URL.",
	"alpha"          => "The :attribute may only contain letters.",
	"alpha_dash"     => "The :attribute may only contain letters, numbers, and dashes.",
	"alpha_num"      => "The :attribute may only contain letters and numbers.",
	"between"        => array(
		"numeric" => "The :attribute must be between :min - :max.",
		"file"    => "The :attribute must be between :min - :max kilobytes.",
		"string"  => "The :attribute must be between :min - :max characters.",
	),
	"confirmed"      => "The :attribute confirmation does not match.",
	"different"      => "The :attribute and :other must be different.",
	"email"          => "The :attribute format is invalid.",
	"exists"         => "The selected :attribute is invalid.",
	"image"          => "The :attribute must be an image.",
	"in"             => "The selected :attribute is invalid.",
	"integer"        => "The :attribute must be an integer.",
	"ip"             => "The :attribute must be a valid IP address.",
	"max"            => array(
		"numeric" => "The :attribute must be less than :max.",
		"file"    => "The :attribute must be less than :max kilobytes.",
		"string"  => "The :attribute must be less than :max characters.",
	),
	"mimes"          => "The :attribute must be a file of type: :values.",
	"min"            => array(
		"numeric" => "The :attribute must be at least :min.",
		"file"    => "The :attribute must be at least :min kilobytes.",
		"string"  => "The :attribute must be at least :min characters.",
	),
	"not_in"         => "The selected :attribute is invalid.",
	"numeric"        => "The :attribute must be a number.",
	"required"       => "The :attribute field is required.",
    "required_with"  => "The :attribute field is required with :field",
	"same"           => "The :attribute and :other must match.",
	"size"           => array(
		"numeric" => "The :attribute must be :size.",
		"file"    => "The :attribute must be :size kilobyte.",
		"string"  => "The :attribute must be :size characters.",
	),
	"unique"         => "The :attribute has already been taken.",
	"url"            => "The :attribute format is invalid.",

	/*
	|--------------------------------------------------------------------------
	| Custom Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| Here you may specify custom validation messages for attributes using the
	| convention "attribute_rule" to name the lines. This helps keep your
	| custom validation clean and tidy.
	|
	| So, say you want to use a custom validation message when validating that
	| the "email" attribute is unique. Just add "email_unique" to this array
	| with your custom message. The Validator will handle the rest!
	|
	*/

	'custom' => array('custom_required' => 'This field is required!'),

	/*
	|--------------------------------------------------------------------------
	| Validation Attributes
	|--------------------------------------------------------------------------
	|
	| The following language lines are used to swap attribute place-holders
	| with something more reader friendly such as "E-Mail Address" instead
	| of "email". Your users will thank you.
	|
	| The Validator class will automatically search this array of lines it
	| is attempting to replace the :attribute place-holder in messages.
	| It's pretty slick. We think you'll like it.
	|
	*/

	'attributes' => array('test_attribute' => 'attribute'),

);
