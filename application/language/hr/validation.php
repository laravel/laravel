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

	"accepted"       => "Morate prihvatiti polje :attribute.",
	"active_url"     => "Polje :attribute ne sadrži važeći URL.",
	"after"          => "Polje :attribute mora biti datum poslije :date.",
	"alpha"          => "Polje :attribute smije sadržavati samo slova.",
	"alpha_dash"     => "Polje :attribute smije sadržavati samo slova, brojke i crtice.",
	"alpha_num"      => "Polje :attribute smije sadržavati samo slova i brojke.",
	"array"          => "Polje :attribute mora imati odabrane elemente.",
	"before"         => "Polje :attribute mora biti datum prije :date.",
	"between"        => array(
		"numeric" => "Vrijednost polja :attribute mora biti između :min - :max.",
		"file"    => "Valičina polja :attribute mora biti između :min - :max kilobytea.",
		"string"  => "Dužina polja :attribute mora biti između :min - :max znakova.",
	),
	"confirmed"      => "Potvrda za polje :attribute nije važeća.",
	"count"          => "Polje :attribute mora imati točno :count odabranih elemenata.",
	"countbetween"   => "Polje :attribute mora imati između :min i :max odabranih elemenata.",
	"countmax"       => "Polje :attribute mora imati manje od :max odabranih elemenata.",
	"countmin"       => "Polje :attribute mora imati najmanje :min odabranih elemenata.",
	"different"      => "Polja :attribute i :other moraju biti različita.",
	"email"          => "Polje :attribute mora biti pravilna e-mail adresa.",
	"exists"         => "Odabrani element za polje :attribute nije važeći.",
	"image"          => "Polje :attribute mora sadržavati sliku.",
	"in"             => "Odabrani element za polje :attribute nije važeći.",
	"integer"        => "Polje :attribute mora biti integer.",
	"ip"             => "Polje :attribute mora biti pravilna IP adresa.",
	"match"          => "Format za polje :attribute nije važeći.",
	"max"            => array(
		"numeric" => "Vrijednost polja :attribute mora biti manja od :max.",
		"file"    => "Veličina polja :attribute mora biti manja od :max kilobytea.",
		"string"  => "Dužina polja :attribute mora biti manja od :max znakova.",
	),
	"mimes"          => "Polje :attribute mora biti datoteka tipa: :values.",
	"min"            => array(
		"numeric" => "Vrijednost polja :attribute mora biti veća od :min.",
		"file"    => "Veličina polja :attribute mora biti veća od :min kilobytea.",
		"string"  => "Dužina polja :attribute mora biti veća od :min znakova.",
	),
	"not_in"         => "Odabrani element za polje :attribute nije važeći.",
	"numeric"        => "Polje :attribute mora biti broj.",
	"required"       => "Polje :attribute mora biti ispunjeno.",
	"same"           => "Polja :attribute i :other se moraju poduddarati.",
	"size"           => array(
		"numeric" => "Polje :attribute mora biti veličine :size.",
		"file"    => "Polje :attribute mora biti veličine :size kilobytea.",
		"string"  => "Polje :attribute mora biti dužine :size znakova.",
	),
	"unique"         => ":attribute već postoji.",
	"url"            => "Format za polje :attribute nije važeći.",

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