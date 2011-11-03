<?php 

return array(

	/*
	|--------------------------------------------------------------------------
	| Validation Attribute Language Lines
	|--------------------------------------------------------------------------
	|
	| The following language lines are used to swap attribute place-holders
	| with something more reader friendly, such as "E-Mail Address" instead
	| of "email".
	|
	| The Validator class will automatically search this array of lines when
	| attempting to replace the :attribute place-holder in error messages.
	|
	*/

	'attributes' => array(),

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
	| These language lines may be easily changed by the developer to provide
	| custom error messages in their application. Error messages for custom
	| validation rules may also be added to this file.
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
	"email"          => "The :attribute format is invalid.",
	"image"          => "The :attribute must be an image.",
	"in"             => "The selected :attribute is invalid.",
	"integer"        => "The :attribute must be an integer.",
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
	"size"           => array(
		"numeric" => "The :attribute must be :size.",
		"file"    => "The :attribute must be :size kilobyte.",
		"string"  => "The :attribute must be :size characters.",
	),
	"unique"         => "The :attribute has already been taken.",
	"url"            => "The :attribute format is invalid.",

);