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

	"accepted"       => ":attribute doit être accepté.",
	"active_url"     => ":attribute n'est pas une URL active.",
	"after"          => ":attribute doit être après :date.",
	"alpha"          => ":attribute ne doit contenir que des lettres.",
	"alpha_dash"     => ":attribute ne doit contenir que des lettres, des chiffres et des tirets.",
	"alpha_num"      => ":attribute ne doit contenir que des chiffres et des lettres.",
	"before"         => ":attribute doit $etre avant :date.",
	"between"        => array(
		"numeric" => ":attribute doit être entre :min et :max.",
		"file"    => "Le poid de :attribute doit être entre :min et :max kilo octets.",
		"string"  => ":attribute doit contenir entre :min et :max caractères.",
	),
	"confirmed"      => "La confirmation de :attribute ne correspond pas.",
	"different"      => ":attribute et :other doivent être différents.",
	"email"          => ":attribute n'a pas un format valide.",
	"exists"         => ":attribute n'existe pas.",
	"image"          => ":attribute doit être une image.",
	"in"             => ":attribute n'est pas valide.",
	"integer"        => ":attribute doit être un entier.",
	"ip"             => ":attribute doit être une adresse IP valide.",
	"match"          => ":attribute n'a pas un format valide.",
	"max"            => array(
		"numeric" => ":attribute doit être inférieur à :max.",
		"file"    => "Le poid de :attribute  doit être inférieur à :max kilo octets.",
		"string"  => ":attribute doit contenir moins de :max caractères.",
	),
	"mimes"          => ":attribute doit être un fichier de type :values.",
	"min"            => array(
		"numeric" => ":attribute doit être supérieur à :min.",
		"file"    => "Le poid de :attribute doit être supérieur à :min kilo octets.",
		"string"  => ":attribute doit contenir au minimum :min caractères.",
	),
	"in"             => ":attribute n'est pas valide.",
	"numeric"        => ":attribute doit être un nombre.",
	"required"       => ":attribute est obligatoire.",
	"same"           => ":attribute et :other doivent être identiques.",
	"size"           => array(
		"numeric" => ":attribute doit être égal à :size.",
		"file"    => ":attribute doit avoir un poid de :size kilo octets.",
		"string"  => ":attribute doit contenir :size caractères.",
	),
	"unique"         => "Le :attribute est déjà pris.",
	"url"            => "Le format de :attribute est invalide.",

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