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

	"accepted"       => ":attribute musi zostać zaakceptowane.",
	"active_url"     => ":attribute nie jest prawidłowym adresem URL.",
	"after"          => ":attribute musi zawierać datę, która jest po :date.",
	"alpha"          => ":attribute może zawierać jedynie litery.",
	"alpha_dash"     => ":attribute może zawierać jedynie litery, cyfry i myślniki.",
	"alpha_num"      => ":attribute może zawierać jedynie litery i cyfry.",
	"array"          => "The :attribute must have selected elements.",
	"before"         => ":attribute musi zawierać datę, która jest przed :date.",
	"between"        => array(
		"numeric" => ":attribute musi mieścić się w granicach :min - :max.",
		"file"    => ":attribute musi mieć :min - :max kilobajtów.",
		"string"  => ":attribute musi mieć :min - :max znaków.",
	),
	"confirmed"      => "Potwierdzenie :attribute się nie zgadza.",
	"count"          => "The :attribute must have exactly :count selected elements.",
	"countbetween"   => "The :attribute must have between :min and :max selected elements.",
	"countmax"       => "The :attribute must have less than :max selected elements.",
	"countmin"       => "The :attribute must have at least :min selected elements.",
	"different"      => ":attribute i :other muszą się od siebie różnić.",
	"email"          => "The :attribute format is invalid.",
	"exists"         => "Zaznaczona opcja :attribute jest nieprawidłowa.",
	"image"          => ":attribute musi być obrazkiem.",
	"in"             => "Zaznaczona opcja :attribute jest nieprawidłowa.",
	"integer"        => ":attribute musi być liczbą całkowitą.",
	"ip"             => ":attribute musi być prawidłowym adresem IP.",
	"match"          => "Format :attribute jest nieprawidłowy.",
	"max"            => array(
		"numeric" => ":attribute musi być poniżej :max.",
		"file"    => ":attribute musi mieć poniżej :max kilobajtów.",
		"string"  => ":attribute musi mieć poniżej :max znaków.",
	),
	"mimes"          => ":attribute musi być plikiem rodzaju :values.",
	"min"            => array(
		"numeric" => ":attribute musi być co najmniej :min.",
		"file"    => "Plik :attribute musi mieć co najmniej :min kilobajtów.",
		"string"  => ":attribute musi mieć co najmniej :min znaków.",
	),
	"not_in"         => "Zaznaczona opcja :attribute jest nieprawidłowa.",
	"numeric"        => ":attribute musi być numeryczne.",
	"required"       => "Pole :attribute jest wymagane.",
	"same"           => ":attribute i :other muszą być takie same.",
	"size"           => array(
		"numeric" => ":attribute musi mieć rozmiary :size.",
		"file"    => ":attribute musi mieć :size kilobajtów.",
		"string"  => ":attribute musi mieć :size znaków.",
	),
	"unique"         => ":attribute zostało już użyte.",
	"url"            => "Format pola :attribute jest nieprawidłowy.",

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