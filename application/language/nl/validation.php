<?php 

return array(

	/*
	|--------------------------------------------------------------------------
	| Dutch validation language file
	|--------------------------------------------------------------------------
	|
	*/

	"accepted"       => ":attribute moet geaccepteerd zijn.",
	"active_url"     => ":attribute is geen geldige URL.",
	"after"          => ":attribute moet een datum na :date zijn.",
	"alpha"          => ":attribute mag alleen letters bevatten.",
	"alpha_dash"     => ":attribute mag alleen letters, nummers, onderstreep(_) en strepen(-) bevatten.",
	"alpha_num"      => ":attribute mag alleen letters en nummers bevatten.",
	"array"          => ":attribute moet geselecteerde elementen bevatten.",
	"before"         => ":attribute moet een datum voor :date zijn.",
	"between"        => array(
		"numeric" => ":attribute moet tussen :min en :max zijn.",
		"file"    => ":attribute moet tussen :min en :max kilobytes zijn.",
		"string"  => ":attribute moet tussen :min en :max karakters zijn.",
	),
	"confirmed"      => ":attribute bevestiging komt niet overeen.",
	"count"          => ":attribute moet precies :count geselecteerde elementen bevatten.",
	"countbetween"   => ":attribute moet tussen :min en :max geselecteerde elementen bevatten.",
	"countmax"       => ":attribute moet minder dan :max geselecteerde elementen bevatten.",
	"countmin"       => ":attribute moet minimaal :min geselecteerde elementen bevatten.",
	"date_format"    => ":attribute moet een geldig datum formaat bevatten.",
	"different"      => ":attribute en :other moeten verschillend zijn.",
	"email"          => ":attribute is geen geldig e-mailadres.",
	"exists"         => ":attribute bestaat niet.",
	"image"          => ":attribute moet een afbeelding zijn.",
	"in"             => ":attribute is ongeldig.",
	"integer"        => ":attribute moet een getal zijn.",
	"ip"             => ":attribute moet een geldig IP-adres zijn.",
	"match"          => "Het formaat van :attribute is ongeldig.",
	"max"            => array(
		"numeric" => ":attribute moet minder dan :max zijn.",
		"file"    => ":attribute moet minder dan :max kilobytes zijn.",
		"string"  => ":attribute moet minder dan :max karakters zijn.",
	),
	"mimes"          => ":attribute moet een bestand zijn van het bestandstype :values.",
	"min"            => array(
		"numeric" => ":attribute moet minimaal :min zijn.",
		"file"    => ":attribute moet minimaal :min kilobytes zijn.",
		"string"  => ":attribute moet minimaal :min karakters zijn.",
	),
	"not_in"         => "Het formaat van :attribute is ongeldig.",
	"numeric"        => ":attribute moet een nummer zijn.",
	"required"       => ":attribute is verplicht.",
	"required_with"  => ":attribute is verplicht i.c.m. :field",
	"same"           => ":attribute en :other moeten overeenkomen.",
	"size"           => array(
		"numeric" => ":attribute moet :size zijn.",
		"file"    => ":attribute moet :size kilobyte zijn.",
		"string"  => ":attribute moet :size characters zijn.",
	),
	"unique"         => ":attribute is al in gebruik.",
	"url"            => ":attribute is geen geldige URL.",

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
