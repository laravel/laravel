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

	"accepted"       => "Polje :attribute mora biti prihvaćeno.",
	"active_url"     => "Polje :attribute nije validan URL.",
	"after"          => "Polje :attribute mora biti datum posle :date.",
	"alpha"          => "Polje :attribute može sadržati samo slova.",
	"alpha_dash"     => "Polje :attribute može sadržati samo slova, brojeve i povlake.",
	"alpha_num"      => "Polje :attribute može sadržati samo slova i brojeve.",
	"array"          => "Polje :attribute mora imati odabrane elemente.",
	"before"         => "Polje :attribute mora biti datum pre :date.",
	"between"        => array(
		"numeric" => "Polje :attribute mora biti izmedju :min - :max.",
		"file"    => "Fajl :attribute mora biti izmedju :min - :max kilobajta.",
		"string"  => "Polje :attribute mora biti izmedju :min - :max karaktera.",
	),
	"confirmed"      => "Potvrda polja :attribute se ne poklapa.",
	"count"          => "Polje :attribute mora imati tačno :count odabranih elemenata.",
	"countbetween"   => "Polje :attribute mora imati izmedju :min i :max odabranih elemenata.",
	"countmax"       => "Polje :attribute mora imati manje od :max odabranih elemenata.",
	"countmin"       => "Polje :attribute mora imati najmanje :min odabranih elemenata.",
	"different"      => "Polja :attribute i :other moraju biti različita.",
	"email"          => "Format polja :attribute nije validan.",
	"exists"         => "Odabrano polje :attribute nije validno.",
	"image"          => "Polje :attribute mora biti slika.",
	"in"             => "Odabrano polje :attribute nije validno.",
	"integer"        => "Polje :attribute mora biti broj.",
	"ip"             => "Polje :attribute mora biti validna IP adresa.",
	"match"          => "Format polja :attribute nije validan.",
	"max"            => array(
		"numeric" => "Polje :attribute mora biti manje od :max.",
		"file"    => "Polje :attribute mora biti manje od :max kilobajta.",
		"string"  => "Polje :attribute mora sadržati manje od :max karaktera.",
	),
	"mimes"          => "Polje :attribute mora biti fajl tipa: :values.",
	"min"            => array(
		"numeric" => "Polje :attribute mora biti najmanje :min.",
		"file"    => "Fajl :attribute mora biti najmanje :min kilobajta.",
		"string"  => "Polje :attribute mora sadržati najmanje :min karaktera.",
	),
	"not_in"         => "Odabrani element polja :attribute nije validan.",
	"numeric"        => "Polje :attribute mora biti broj.",
	"required"       => "Polje :attribute je obavezno.",
	"same"           => "Polja :attribute i :other se moraju poklapati.",
	"size"           => array(
		"numeric" => "Polje :attribute mora biti :size.",
		"file"    => "Fajl :attribute mora biti :size kilobajta.",
		"string"  => "Polje :attribute mora biti :size karaktera.",
	),
	"unique"         => "Polje :attribute već postoji.",
	"url"            => "Format polja :attribute nije validan.",

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