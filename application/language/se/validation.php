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

	"accepted"       => ":attribute måste accepteras.",
	"active_url"     => ":attribute är inte en giltig webbadress.",
	"after"          => ":attribute måste vara ett datum efter den :date.",
	"alpha"          => ":attribute får endast innehålla bokstäver.",
	"alpha_dash"     => ":attribute får endast innehålla bokstäver, nummer och bindestreck.",
	"alpha_num"      => ":attribute får endast innehålla bokstäver och nummer.",
	"array"          => ":attribute måste ha valda element.",
	"before"         => ":attribute måste vara ett datum innan den :date.",
	"between"        => array(
		"numeric" => ":attribute måste vara ett nummer mellan :min och :max.",
		"file"    => ":attribute måste vara mellan :min till :max kilobytes stor.",
		"string"  => ":attribute måste inehålla :min till :max tecken.",
	),
	"confirmed"      => ":attribute bekräftelsen machar inte.",
	"count"          => ":attribute måste exakt ha :count valda element.",
	"countbetween"   => ":attribute får endast ha :min till :max valda element.",
	"countmax"       => ":attribute får max ha :max valda element.",
	"countmin"       => ":attribute måste minst ha :min valda element.",
	"different"      => ":attribute och :other får ej vara lika.",
	"email"          => ":attribute formatet är ogiltig.",
	"exists"         => "Det valda :attribute är ogiltigt.",
	"image"          => ":attribute måste vara en bild.",
	"in"             => "Det valda :attribute är ogiltigt.",
	"integer"        => ":attribute måste vara en siffra.",
	"ip"             => ":attribute måste vara en giltig IP-adress.",
	"match"          => ":attribute formatet är ogiltig.",
	"max"            => array(
		"numeric" => ":attribute får inte vara större än :max.",
		"file"    => ":attribute får max vara :max kilobytes stor.",
		"string"  => ":attribute får max innehålla :max tecken.",
	),
	"mimes"          => ":attribute måste vara en fil av typen: :values.",
	"min"            => array(
		"numeric" => ":attribute måste vara större än :min.",
		"file"    => ":attribute måste minst vara :min kilobytes stor.",
		"string"  => ":attribute måste minst innehålla :min tecken.",
	),
	"not_in"         => "Det valda :attribute är ogiltigt.",
	"numeric"        => ":attribute måste vara ett nummer.",
	"required"       => ":attribute fältet är obligatoriskt.",
	"same"           => ":attribute och :other måste vara likadana.",
	"size"           => array(
		"numeric" => ":attribute måste vara :size.",
		"file"    => ":attribute får endast vara :size kilobyte stor.",
		"string"  => ":attribute måste innehålla :size tecken.",
	),
	"unique"         => ":attribute används redan.",
	"url"            => ":attribute är inte en giltig webbadress.",

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

	'custom' => array(),

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

	'attributes' => array(),

);