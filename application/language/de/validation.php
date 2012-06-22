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
	"active_url"     => ":attribute ist keine korrekte URL.",
	"after"          => ":attribute muss ein Datum nach :date sein.",
	"alpha"          => ":attribute darf nur Buchstaben beinhalten.",
	"alpha_dash"     => ":attribute sollte nur aus Buchstaben, Nummern und Bindestriche bestehen.",
	"alpha_num"      => ":attribute sollte nur aus Buchstaben und Nummern bestehen.",
	"before"         => ":attribute muss ein Datum vor :date sein.",
	"between"        => array(
		"numeric" => ":attribute muss zwischen :min und :max sein.",
		"file"    => ":attribute muss zwischen :min und :max Kilobytes sein.",
		"string"  => ":attribute muss zwischen :min und :max Zeichen sein.",
	),
	"confirmed"      => "Die Best&auml;tigung f&uuml;r :attribute stimmt nicht &uuml;berein.",
	"different"      => ":attribute und :other m&uuml;ssen verschieden sein.",
	"email"          => "Das Format f&uuml; :attribute ist ung&uuml;ltig.",
	"exists"         => "Die selektierte :attribute ist ung&uuml;ltig.",
	"image"          => ":attribute muss ein Bild sein.",
	"in"             => "Die selektierte :attribute ist ung&uuml;ltig.",
	"integer"        => ":attribute muss eine Ganzzahl sein.",
	"ip"             => ":attribute muss eine g&uuml;ltige IP sein.",
	"match"          => ":attribute hat ein ung&uuml;ltiges format.",
	"max"            => array(
		"numeric" => ":attribute muss kleiner sein als :max.",
		"file"    => ":attribute muss kleiner sein :max Kilobytes.",
		"string"  => ":attribute muss k&uuml;rzer sein als :max Zeichen.",
	),
	"mimes"          => ":attribute muss eine Datei sein des Formats: :values.",
	"min"            => array(
		"numeric" => ":attribute muss gr&ouml;&szlig;er sein als :min.",
		"file"    => ":attribute muss gr&ouml;&szlig;er sein als :min Kilobytes.",
		"string"  => ":attribute muss l&auml;nger sein als :min Zeichen.",
	),
	"not_in"         => "Die selektierte :attribute ist ung&uuml;ltig.",
	"numeric"        => ":attribute muss eine Nummer sein.",
	"required"       => "Das :attribute Feld muss aufgef&uuml;llt sein.",
	"same"           => ":attribute und :other m&uuml;ssen &uuml;bereinstimmen.",
	"size"           => array(
		"numeric" => ":attribute muss :size sein.",
		"file"    => ":attribute muss :size Kilobyte sein.",
		"string"  => ":attribute muss :size Zeichen sein.",
	),
	"unique"         => ":attribute ist schon vergeben.",
	"url"            => "Das Format f&uuml; :attribute ist ung&uuml;ltig.",

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