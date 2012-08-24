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

	"accepted"       => ":attribute skal accepteres.",
	"active_url"     => ":attribute er ikke en gyldig URL.",
	"after"          => ":attribute skal v&aelig;re en dato efter :date.",
	"alpha"          => ":attribute m&aring; kun indeholde bogstaver.",
	"alpha_dash"     => ":attribute m&aring; kun indeholde bogstaver, numre, og skr&aring;streg.",
	"alpha_num"      => ":attribute m&aring; kun indeholde bogstaver og numre.",
	"array"          => ":attribute skal have valgte elementer.",
	"before"         => ":attribute skal have en dato f&oslash;r :date.",
	"between"        => array(
		"numeric" => ":attribute skal v&aelig;re mellem :min - :max.",
		"file"    => ":attribute skal v&aelig;re mellem :min - :max kilobytes.",
		"string"  => ":attribute skal v&aelig;re mellem :min - :max karakterer.",
	),
	"confirmed"      => ":attribute bekr&aelig;ftelse stemmer ikke overens.",
	"count"          => ":attribute skal v&aelig;re pr&aelig;cis :count valgte elementer.",
	"countbetween"   => ":attribute skal v&aelig;re mellem :min and :max valgte elementer.",
	"countmax"       => ":attribute skal have mindre end :max valgte elementer.",
	"countmin"       => ":attribute skal have minimum :min valgte elementer.",
	"different"      => ":attribute og :other skal v&aelig;re forskellige.",
	"email"          => "Formatet for :attribute er ugyldigt.",
	"exists"         => "Den valgte :attribute er ugyldig.",
	"image"          => ":attribute skal v&aelig;re et billede.",
	"in"             => "Den valgte :attribute er ugyldig.",
	"integer"        => ":attribute m&aring; kun indeholde tal.",
	"ip"             => ":attribute skal v&aelig;re en gyldig IP adresse.",
	"match"          => "Formatet for :attribute er ugyldigt.",
	"max"            => array(
		"numeric" => ":attribute skal v&aelig;re mindre end :max.",
		"file"    => ":attribute skal v&aelig;re mindre end :max kilobytes.",
		"string"  => ":attribute skal v&aelig;re mindre end :max karakterer.",
	),
	"mimes"          => ":attribute skal have filtypen type: :values.",
	"min"            => array(
		"numeric" => ":attribute ska minimum v&aelig;re :min.",
		"file"    => ":attribute skal v&aelig;re mindst :min kilobytes.",
		"string"  => ":attribute skal v&aelig;re mindst :min karakterer.",
	),
	"not_in"         => "Den valgte :attribute er ugyldig.",
	"numeric"        => ":attribute skal v&aelig;re et nummer.",
	"required"       => ":attribute er kr&aelig;vet.",
	"same"           => ":attribute og :other stemmer ikke overens.",
	"size"           => array(
		"numeric" => ":attribute skal v&aelig;re :size.",
		"file"    => ":attribute skal v&aelig;re :size kilobyte.",
		"string"  => ":attribute skal v&aelig;re :size karakterer.",
	),
	"unique"         => ":attribute er allerede optaget.",
	"url"            => ":attribute formatet er ugyldigt.",

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