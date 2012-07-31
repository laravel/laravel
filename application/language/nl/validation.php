<?php 

return array(

	/*
	|--------------------------------------------------------------------------
	| Dutch validation language file
	|--------------------------------------------------------------------------
	|
	*/

	"accepted"       => "Het :attribute moet geaccepteerd zijn.",
	"active_url"     => "Het :attribute is geen geldig URL.",
	"after"          => "Het :attribute moet een datum na :date zijn.",
	"alpha"          => "Het :attribute mag alleen letters bevatten.",
	"alpha_dash"     => "Het :attribute mag alleen letters, nummers, onderstreep(_) en strepen(-) bevatten.",
	"alpha_num"      => "Het :attribute mag alleen letters en nummers",
	"before"         => "Het :attribute moet een datum voor :date zijn.",
	"between"        => array(
		"numeric" => "Het :attribute moet tussen :min en :max zijn.",
		"file"    => "Het :attribute moet tussen :min en :max kilobytes zijn.",
		"string"  => "Het :attribute moet tussen :min en :max tekens zijn.",
	),
	"confirmed"      => "Het :attribute bevestiging komt niet overeen.",
	"different"      => "Het :attribute en :other moeten verschillend zijn.",
	"email"          => "Het :attribute formaat is ongeldig.",
	"exists"         => "Het gekozen :attribute is al ingebruik.",
	"image"          => "Het :attribute moet een afbeelding zijn.",
	"in"             => "Het gekozen :attribute is ongeldig.",
	"integer"        => "Het :attribute moet een getal zijn.",
	"ip"             => "Het :attribute moet een geldig IP adres bevatten.",
	"match"          => "Het :attribute formaat is ongeldig.",
	"max"            => array(
		"numeric" => "Het :attribute moet minder dan :max zijn.",
		"file"    => "Het :attribute moet minder dan :max kilobytes zijn.",
		"string"  => "Het :attribute moet minder dan :max tekens zijn.",
	),
	"mimes"          => "Het :attribute moet een bestand zijn van het bestandstype :values.",
	"min"            => array(
		"numeric" => "Het :attribute moet minimaal :min zijn.",
		"file"    => "Het :attribute moet minimaal :min kilobytes zijn.",
		"string"  => "Het :attribute moet minimaal :min characters zijn.",
	),
	"not_in"         => "Het :attribute formaat is ongeldig.",
	"numeric"        => "Het :attribute moet een nummer zijn.",
	"required"       => "Het :attribute veld is verplicht.",
	"same"           => "Het :attribute en :other moeten overeenkomen.",
	"size"           => array(
		"numeric" => "Het :attribute moet :size zijn.",
		"file"    => "Het :attribute moet :size kilobyte zijn.",
		"string"  => "Het :attribute moet :size characters zijn.",
	),
	"unique"         => "Het :attribute is al in gebruik.",
	"url"            => "Het :attribute formaat is ongeldig.",

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
