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

	"accepted"       => ":attribute muss akzeptiert werden.",
	"active_url"     => ":attribute ist keine g&uuml;ltige URL.",
	"after"          => ":attribute muss ein Datum nach dem :date sein.",
	"alpha"          => ":attribute darf nur Buchstaben beinhalten.",
	"alpha_dash"     => ":attribute darf nur aus Buchstaben, Nummern und Bindestrichen bestehen.",
	"alpha_num"      => ":attribute darf nur aus Buchstaben und Nummern bestehen.",
	"array"          => ":attribute muss ausgew&auml;hlte Elemente haben.",
	"before"         => ":attribute muss ein Datum vor dem :date sein.",
	"between"        => array(
		"numeric" => ":attribute muss zwischen :min und :max liegen.",
		"file"    => ":attribute muss zwischen :min und :max Kilobytes gro&szlig; sein.",
		"string"  => ":attribute muss zwischen :min und :max Zeichen lang sein.",
	),
	"confirmed"      => ":attribute stimmt nicht mit der Best&auml;tigung &uuml;berein.",
	"count"          => ":attribute muss genau :count ausgew&auml;hlte Elemente haben.",
	"countbetween"   => ":attribute muss zwischen :min und :max ausgew&auml;hlte Elemente haben.",
	"countmax"       => ":attribute muss weniger als :max ausgew&auml;hlte Elemente haben.",
	"countmin"       => ":attribute muss mindestens :min ausgew&auml;hlte Elemente haben.",
	"different"      => ":attribute und :other m&uuml;ssen verschieden sein.",
	"email"          => ":attribute ist keine g&uuml;ltige Email-Adresse.",
	"exists"         => "Der gew&auml;hlte Wert f&uuml;r :attribute ist ung&uuml;ltig.",
	"image"          => ":attribute muss ein Bild sein.",
	"in"             => "Der gew&auml;hlte Wert f&uuml;r :attribute ist ung&uuml;ltig.",
	"integer"        => ":attribute muss eine ganze Zahl sein.",
	"ip"             => ":attribute muss eine g&uuml;ltige IP-Adresse sein.",
	"match"          => ":attribute hat ein ung&uuml;ltiges Format.",
	"max"            => array(
		"numeric" => ":attribute muss kleiner als :max sein.",
		"file"    => ":attribute muss kleiner als :max Kilobytes gro&szlig; sein.",
		"string"  => ":attribute muss k&uuml;rzer als :max Zeichen sein.",
	),
	"mimes"          => ":attribute muss den Dateityp :values haben.",
	"min"            => array(
		"numeric" => ":attribute muss gr&ouml;&szlig;er als :min sein.",
		"file"    => ":attribute muss gr&ouml;&szlig;er als :min Kilobytes gro&szlig; sein.",
		"string"  => ":attribute muss l&auml;nger als :min Zeichen sein.",
	),
	"not_in"         => "Der gew&auml;hlte Wert f&uuml;r :attribute ist ung&uuml;ltig.",
	"numeric"        => ":attribute muss eine Zahl sein.",
	"required"       => ":attribute muss ausgef&uuml;llt sein.",
	"same"           => ":attribute und :other m&uuml;ssen &uuml;bereinstimmen.",
	"size"           => array(
		"numeric" => ":attribute muss gleich :size sein.",
		"file"    => ":attribute muss :size Kilobyte gro&szlig; sein.",
		"string"  => ":attribute muss :size Zeichen lang sein.",
	),
	"unique"         => ":attribute ist schon vergeben.",
	"url"            => "Das Format von :attribute ist ung&uuml;ltig.",

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