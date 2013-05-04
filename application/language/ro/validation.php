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

	"accepted"       => "Campul :attribute trebuie sa fie acceptat.",
	"active_url"     => "Campul :attribute nu este un URL valid.",
	"after"          => "Campul :attribute trebuie sa fie o data dupa :date.",
	"alpha"          => "Campul :attribute poate contine numai litere.",
	"alpha_dash"     => "Campul :attribute poate contine numai litere, numere si liniute.",
	"alpha_num"      => "Campul :attribute poate contine numai litere si numere.",
	"array"          => "Campul :attribute trebuie sa aiba elemente selectate.",
	"before"         => "Campul :attribute trebuie sa fie o data inainte de :date.",
	"between"        => array(
		"numeric" => "Campul :attribute trebuie sa fie intre :min si :max.",
		"file"    => "Campul :attribute trebuie sa fie intre :min si :max kilobytes.",
		"string"  => "Campul :attribute trebuie sa fie intre :min si :max caractere.",
	),
	"confirmed"      => "Confirmarea :attribute nu se potriveste.",
	"count"          => "Campul :attribute trebuie sa aiba exact :count elemente selectate.",
	"countbetween"   => "Campul :attribute trebuie sa aiba intre :min si :max elemente selectate.",
	"countmax"       => "Campul :attribute trebuie sa aiba mai putin de :max elemente selectate.",
	"countmin"       => "Campul :attribute trebuie sa aiba cel putin :min elemente selectate.",
	"date_format"	 => "Campul :attribute trebuie sa fie intr-un format valid.",
	"different"      => "Campurile :attribute si :other trebuie sa fie diferite.",
	"email"          => "Formatul campului :attribute este invalid.",
	"exists"         => "Campul :attribute selectat este invalid.",
	"image"          => "Campul :attribute trebuie sa fie o imagine.",
	"in"             => "Campul :attribute selectat este invalid.",
	"integer"        => "Campul :attribute trebuie sa fie un numar intreg.",
	"ip"             => "Campul :attribute trebuie sa fie o adresa IP valida.",
	"match"          => "Formatul campului :attribute este invalid.",
	"max"            => array(
		"numeric" => "Campul :attribute trebuie sa fie mai mic de :max.",
		"file"    => "Campul :attribute trebuie sa fie mai mic de :max kilobytes.",
		"string"  => "Campul :attribute trebuie sa fie mai mic de :max caractere.",
	),
	"mimes"          => "Campul :attribute trebuie sa fie un fisier de tipul: :values.",
	"min"            => array(
		"numeric" => "Campul :attribute trebuie sa fie cel putin :min.",
		"file"    => "Campul :attribute trebuie sa aiba cel putin :min kilobytes.",
		"string"  => "Campul :attribute trebuie sa aiba cel putin :min caractere.",
	),
	"not_in"         => "Campul :attribute selectat este invalid.",
	"numeric"        => "Campul :attribute trebuie sa fie un numar.",
	"required"       => "Campul :attribute este obligatoriu.",
    "required_with"  => "Campul :attribute este obligatoriu cu :field",
	"same"           => "Campul :attribute si :other trebuie sa fie identice.",
	"size"           => array(
		"numeric" => "Campul :attribute trebuie sa fie :size.",
		"file"    => "Campul :attribute trebuie sa aiba :size kilobyte.",
		"string"  => "Campul :attribute trebuie sa aiba :size caractere.",
	),
	"unique"         => "Campul :attribute a fost deja folosit.",
	"url"            => "Campul :attribute nu este intr-un format valid.",

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
