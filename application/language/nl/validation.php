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

	"accepted"       => "De :attribute moet worden geaccepteerd.",
	"active_url"     => "De :attribute is geen geldige URL.",
	"after"          => "De :attribute moet een datum zijn na :date.",
	"alpha"          => "De :attribute mag alleen letters bevatten.",
	"alpha_dash"     => "De :attribute mag alleen letters, nummers, en strepen bevatten.",
	"alpha_num"      => "De :attribute mag alleen letters en nummers bevatten",
	"before"         => "De :attribute moet een datim zijn voor :date.",
	"between"        => array(
		"numeric" => "De :attribute moet tussen :min - :max zijn.",
		"file"    => "De :attribute moet tussen :min - :max kilobytes zijn.",
		"string"  => "De :attribute moet tussen :min - :max karakters zijn.",
	),
	"confirmed"      => "De :attribute bevestiging komt niet overeen.",
	"different"      => "De :attribute en :other moeten verschillen van elkaar.",
	"email"          => "De :attribute is ongeldig.",
	"exists"         => "De geselecteerde :attribute is ongeldig.",
	"image"          => "De :attribute moet een plaatje zijn.",
	"in"             => "De geselecteerde :attribute is ongeldig.",
	"integer"        => "De :attribute moet een heel getal zijn.",
	"ip"             => "De :attribute moet een geldig IP-adres zijn.",
	"match"          => "De :attribute formaat is ongeldig.",
	"max"            => array(
		"numeric" => "De :attribute moet minder zijn als :max.",
		"file"    => "De :attribute moet kleiner zijn als :max kilobytes.",
		"string"  => "De :attribute moet korter zijn dan :max karakters.",
	),
	"mimes"          => "De :attribute moet een van de volgende bestandsformaten :values bevatten",
	"min"            => array(
		"numeric" => "De :attribute moet meer zijn als :min.",
		"file"    => "De :attribute moet groter zijn als :min kilobytes.",
		"string"  => "De :attribute moet langer zijn dan :min karakters.",
	),
	"not_in"         => "De geselecteerde :attribute is ongeldig.",
	"numeric"        => "De :attribute moet een nummer zijn.",
	"required"       => "De :attribute veld is vereist.",
	"same"           => "De :attribute en :other moeten overeen komen.",
	"size"           => array(
		"numeric" => "De :attribute moet :size groot zijn.",
		"file"    => "De :attribute moet :size kilobytes groot zijn.",
		"string"  => "De :attribute moet :size karakters bevatten.",
	),
	"unique"         => "De :attribute bestaat al.",
	"url"            => "De :attribute formaat is ongeldig.",

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