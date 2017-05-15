<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| The following language lines contain the default error messages used by
	| the validator class. Some of these rules have multiple versions such
	| such as the size rules. Feel free to tweak each of these messages.
	|
	*/

	"accepted"         => ":attribute moet geaccepteerd worden.",
	"active_url"       => ":attribute is geen geldige URL.",
	"after"            => ":attribute moet een datum zijn na :date.",
	"alpha"            => ":attribute mag alleen letters bevatten.",
	"alpha_dash"       => ":attribute mag alleen letters, nummers of streepjes bevatten.",
	"alpha_num"        => ":attribute mag alleen letters en nummers bevatten.",
	"before"           => ":attribute moet een datum zijn voor :date.",
	"between"          => array(
		"numeric" => ":attribute moet tussen :min en :max zijn.",
		"file"    => ":attribute moet tussen :min en :max kilobytes zijn.",
		"string"  => ":attribute moet tussen :min en :max tekens zijn.",
	),
	"confirmed"        => ":attribute herhaling komt niet overeen.",
	"date"             => ":attribute is geen geldige datum.",
	"date_format"      => ":attribute komt niet overeen met het formaat :format.",
	"different"        => ":attribute en :other moeten verschillend zijn.",
	"digits"           => ":attribute moet :digits cijfers zijn.",
	"digits_between"   => ":attribute moet tussen :min en :max cijfers zijn.",
	"email"            => ":attribute formaat is ongeldig.",
	"exists"           => "De geselecteerde :attribute is ongeldig.",
	"image"            => ":attribute moet een afbeelding zijn.",
	"in"               => "De geselecteerde :attribute is ongeldig.",
	"integer"          => ":attribute moet een integer zijn.",
	"ip"               => ":attribute moet een geldig IP address zijn.",
	"max"              => array(
		"numeric" => ":attribute mag niet groter dan :max zijn.",
		"file"    => ":attribute mag niet groter dan :max kilobytes zijn.",
		"string"  => ":attribute mag niet langer dan :max tekens zijn.",
	),
	"mimes"            => ":attribute moet een bestand van het type :values zijn.",
	"min"              => array(
		"numeric" => ":attribute moet op zijn minst :min zijn.",
		"file"    => ":attribute moet op zijn minst :min kilobytes zijn.",
		"string"  => ":attribute moet op zijn minst :min tekens zijn.",
	),
	"not_in"           => "De geselecteerde :attribute is ongeldig.",
	"numeric"          => ":attribute moet een nummer zijn.",
	"regex"            => ":attribute zijn formaat is ongeldig.",
	"required"         => ":attribute veld is vereist.",
	"required_if"      => ":attribute veld is vereist als :other :value is.",
	"required_with"    => ":attribute veld is vereist wanneer :values gepresenteerd word.",
	"required_without" => ":attribute veld is vereist als :values niet gepresenteerd word.",
	"same"             => ":attribute en :other moeten overeen komen.",
	"size"             => array(
		"numeric" => ":attribute moet :size zijn.",
		"file"    => ":attribute moet :size kilobytes zijn.",
		"string"  => ":attribute moet :size tekens zijn.",
	),
	"unique"           => ":attribute word al gebruikt.",
	"url"              => ":attribute formaat is ongeldig.",

	/*
	|--------------------------------------------------------------------------
	| Custom Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| Here you may specify custom validation messages for attributes using the
	| convention "attribute.rule" to name the lines. This makes it quick to
	| specify a specific custom language line for a given attribute rule.
	|
	*/

	'custom' => array(),

	/*
	|--------------------------------------------------------------------------
	| Custom Validation Attributes
	|--------------------------------------------------------------------------
	|
	| The following language lines are used to swap attribute place-holders
	| with something more reader friendly such as E-Mail Address instead
	| of "email". This simply helps us make messages a little cleaner.
	|
	*/

	'attributes' => array(),

);
