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

	"accepted"         => ":attribute muss akzeptiert werden.",
	"active_url"       => ":attribute ist keine g&uuml;ltige Internet-Adresse.",
	"after"            => ":attribute muss ein Datum nach dem :date sein.",
	"alpha"            => ":attribute darf nur aus Buchstaben bestehen.",
	"alpha_dash"       => ":attribute darf nur aus Buchstaben, Zahlen, Binde- und Unterstrichen bestehen. Umlaute (&auml;, &ouml; &uuml) und Eszett (&szlig;) sind nicht erlaubt.",
	"alpha_num"        => ":attribute darf nur aus Buchstaben und Zahlen bestehen.",
	"before"           => ":attribute muss ein Datum vor dem :date sein.",
	"between"          => array(
		"numeric" => ":attribute muss zwischen :min - :max liegen.",
		"file"    => ":attribute muss zwischen :min - :max kilobytes gro&szlig; sein.",
		"string"  => ":attribute muss zwischen :min - :max Zeichen lang sein.",
	),
	"confirmed"        => ":attribute stimmt nicht mit der Best&auml;tigung &uuml;berein.",
	"date"             => ":attribute ist kein g&uuml;ltiges Datum.",
	"date_format"      => ":attribute entspricht nicht dem g&uuml;ltigen Format f&uuml;r :format.",
	"different"        => ":attribute und :other m&uuml;ssen sich unterscheiden.",
	"digits"           => ":attribute muss :digits Stellen haben.",
	"digits_between"   => ":attribute muss zwischen :min und :max Stellen haben.",
	"email"            => ":attribute Format ist ung&uuml;ltig.",
	"exists"           => "Der gew&auml;hlte Wert f&uuml;r :attribute ist ung&uuml;ltig.",
	"image"            => ":attribute muss ein Bild sein.",
	"in"               => "Der gew&auml;hlte Wert f&uuml;r :attribute ist ung&uuml;ltig.",
	"integer"          => ":attribute muss eine ganze Zahl sein.",
	"ip"               => ":attribute muss eine g&uuml;ltige IP-Adresse sein.",
	"max"              => array(
		"numeric" => ":attribute muss kleiner als :max sein.",
		"file"    => ":attribute muss kleiner als :max Kilobytes gro&szlig; sein.",
		"string"  => ":attribute muss k&uuml;rzer als :max Zeichen sein.",
	),
	"mimes"            => ":attribute muss den Dateityp :values haben.",
	"min"              => array(
		"numeric" => ":attribute muss gr&ouml;&szlig;er als :min sein.",
		"file"    => ":attribute muss gr&ouml;&szlig;er als :min Kilobytes gro&szlig; sein.",
		"string"  => ":attribute muss l&auml;nger als :min Zeichen sein.",
	),
	"not_in"           => "Der gew&auml;hlte Wert f&uuml;r :attribute ist ung&uuml;ltig.",
	"numeric"          => ":attribute muss eine Zahl sein.",
	"regex"            => ":attribute, das Format ist ung&uuml;ltig.",
	"required"         => ":attribute muss ausgef&uuml;llt sein.",
    "required_if"      => ":attribute muss ausgef&uuml;llt sein whenn :other :value ist.",
	"required_with"    => ":attribute muss angegeben werden wenn :values ausgef&uuml;llt wurde.",
	"required_without" => ":attribute muss angegeben werden wenn :values nicht ausgef&uuml;llt wurde.",
	"same"             => ":attribute und :other m&uuml;ssen &uuml;bereinstimmen.",
	"size"             => array(
		"numeric" => ":attribute muss gleich :size sein.",
		"file"    => ":attribute muss :size Kilobyte gro&szlig; sein.",
		"string"  => ":attribute muss :size Zeichen lang sein.",
	),
	"unique"           => ":attribute ist schon vergeben.",
	"url"              => "Das Format von :attribute ist ung&uuml;ltig.",

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
