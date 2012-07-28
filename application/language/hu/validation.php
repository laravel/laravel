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

	"accepted"       => "A(z) :attribute el kell legyen fogadva.",
	"active_url"     => "A :attribute nem valós URL.",
	"after"          => "A :attribute :date utáni dátum kell legyen.",
	"alpha"          => "A(z) :attribute csak betűket tartalmazhat.",
	"alpha_dash"     => "A(z) :attribute betűket, számokat és kötőjeleket tartalmazhat.",
	"alpha_num"      => "A(z) :attribute csak betűket és számokat tartalmazhat.",
	"before"         => "A :attribute :date előtti dátum kell legyen.",
	"between"        => array(
		"numeric" => "A(z) :attribute :min - :max közötti érték kell legyen.",
		"file"    => "A(z) :attribute :min - :max kilobyte között kell legyen.",
		"string"  => "A(z) :attribute :min - :max karakterhossz között kell legyen",
	),
	"confirmed"      => "A(z) :attribute megerősítése nem egyezett meg.",
	"different"      => "A(z) :attribute és :other különböző kell legyen.",
	"email"          => "A(z) :attribute formátuma nem megfelelő.",
	"exists"         => "A(z) választott :attribute nem megfelelő.",
	"image"          => "A(z) :attribute kép kell legyen.",
	"in"             => "A(z) választott :attribute nem megfelelő.",
	"integer"        => "A :attribute szám kell legyen.",
	"ip"             => "A :attribute valós IP cím kell legyen.",
	"match"          => "A(z) :attribute formátuma nem megfelelő.",
	"max"            => array(
		"numeric" => "A :attribute kevesebb kell legyen, mint :max.",
		"file"    => "A :attribute kevesebb kell legyen :max kilobytenál.",
		"string"  => "A :attribute kevesebb karakterből kell álljon, mint :max.",
	),
	"mimes"          => "A :attribute az alábbi tipusokból való kell legyen :values.",
	"min"            => array(
		"numeric" => "A :attribute legalább :min kell legyen.",
		"file"    => "A :attribute legalább :min kilobyte kell legyen.",
		"string"  => "A :attribute legalább :min karakter hosszú kell legyen.",
	),
	"not_in"         => "A választott :attribute nem megfelelő.",
	"numeric"        => "A :attribute szám kell legyen.",
	"required"       => "A(z) :attribute megadása kötelező.",
	"same"           => "A :attribute és a :other muszáj hogy megegyezzen.",
	"size"           => array(
		"numeric" => "A(z) :attribute :size kell legyen.",
		"file"    => "A(z) :attribute :size kilobyteos kell legyen.",
		"string"  => "A(z) :attribute :size karakteres kell legyen.",
	),
	"unique"         => "A(z) :attribute már foglalt.",
	"url"            => "A(z) :attribute formátuma nem megfelelő.",

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