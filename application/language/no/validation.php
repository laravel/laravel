<?php 

/**
 * Norwegian language file: validation.php
 *
 * @package  Language
 * @version  3.2.13
 * @author   Joachim Martinsen <joachim@martinsen.is>
 * @link     http://www.martinsen.is/
 */

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

	"accepted"       => ":attribute &aring; aksepteres.",
	"active_url"     => ":attribute er ikke en gyldig adresse.",
	"after"          => ":attribute m&aring; v&aelig;re en dato etter :date.",
	"alpha"          => ":attribute kan kun inneholde bokstaver.",
	"alpha_dash"     => ":attribute kan kun inneholde bokstaver, tall og bindestrek.",
	"alpha_num"      => ":attribute kan kun inneholde bokstaver og tall.",
	"array"          => ":attribute m&aring; ha valgte elementer.",
	"before"         => ":attribute m&aring; v&aelig;re en dato f&oslash;r :date.",
	"between"        => array(
		"numeric" => ":attribute m&aring; v&aelig;re mellom :min - :max.",
		"file"    => ":attribute m&aring; v&aelig;re mellom :min - :max kilobytes.",
		"string"  => ":attribute m&aring; v&aelig;re mellom :min - :max tegn.",
	),
	"confirmed"      => ":attribute bekreftelsen er ikke lik.",
	"count"          => ":attribute kan kun ha :count valgte elementer.",
	"countbetween"   => ":attribute m&aring; ha mellom :min og :max valgte elementer.",
	"countmax"       => ":attribute kan ikke ha fler enn :max valgte elementer.",
	"countmin"       => ":attribute m&aring; ha minst :min valgte elementer.",
	"date_format"	 => ":attribute m&aring; v&aelig;re en gyldig dato.",
	"different"      => ":attribute og :other kan ikke v&aelig;re like.",
	"email"          => "Formatet til :attribute er ugyldig.",
	"exists"         => "Valgte :attribute er ugyldig.",
	"image"          => ":attribute m&aring; v&aelig;re et bilde.",
	"in"             => "Valgte :attribute er ugyldig.",
	"integer"        => ":attribute m&aring; v&aelig;re et heltall.",
	"ip"             => ":attribute m&aring; v&aelig;re en gyldig IP-adresse.",
	"match"          => "Formatet til :attribute er ugyldig.",
	"max"            => array(
		"numeric" => ":attribute m&aring; v&aelig;re mindre enn :max.",
		"file"    => ":attribute m&aring; v&aelig;re mindre enn :max kilobytes.",
		"string"  => ":attribute m&aring; v&aelig;re mindre enn :max tegn.",
	),
	"mimes"          => ":attribute m&aring; v&aelig;re av filtypen: :values.",
	"min"            => array(
		"numeric" => ":attribute m&aring; v&aelig;re minst :min.",
		"file"    => ":attribute m&aring; v&aelig;re minst :min kilobytes.",
		"string"  => ":attribute m&aring; v&aelig;re minst :min tegn.",
	),
	"not_in"         => "Valgte :attribute er ugyldig.",
	"numeric"        => ":attribute m&aring; v&aelig;re et tall.",
	"required"       => "Feltet :attribute kreves.",
    "required_with"  => "Feltet :attribute kreves med :field",
	"same"           => ":attribute og :other m&aring; v&aelig;re like.",
	"size"           => array(
		"numeric" => ":attribute m&aring; v&aelig;re :size.",
		"file"    => ":attribute m&aring; v&aelig;re :size kilobyte.",
		"string"  => ":attribute m&aring; v&aelig;re :size tegn.",
	),
	"unique"         => ":attribute er opptatt.",
	"url"            => "Formatet til :attribute  er ugyldig.",

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
