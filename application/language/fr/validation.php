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

	"accepted"       => "Le champ :attribute doit être accepté.",
	"active_url"     => "Le champ :attribute n'est pas une URL valide.",
	"after"          => "Le champ :attribute doit être une date après :date.",
	"alpha"          => "Le champ :attribute ne doit contenir que des lettres.",
	"alpha_dash"     => "Le champ :attribute ne doit contenir que des lettres, nombres et des tirets.",
	"alpha_num"      => "Le champ :attribute ne doit contenir que des lettres et nombres.",
	"array"          => "The :attribute must have selected elements.",
	"before"         => "Le champ :attribute doit être une date avant :date.",
	"between"        => array(
		"numeric" => "Le champ :attribute doit être entre :min - :max.",
		"file"    => "Le champ :attribute doit être entre :min - :max kilo-octets.",
		"string"  => "Le champ :attribute doit être entre :min - :max caractères.",
	),
	"confirmed"      => "Le champ :attribute confirmation est différent.",
	"count"          => "The :attribute must have exactly :count selected elements.",
	"countbetween"   => "The :attribute must have between :min and :max selected elements.",
	"countmax"       => "The :attribute must have less than :max selected elements.",
	"countmin"       => "The :attribute must have at least :min selected elements.",
	"different"      => "Les champ :attribute et :other doivent être différents.",
	"email"          => "Le format du champ :attribute est invalide.",
	"exists"         => "Le champ sélectionné :attribute est invalide.",
	"image"          => "Le champ :attribute doit être une image.",
	"in"             => "Le champ sélectionné :attribute est invalide.",
	"integer"        => "Le champ :attribute doit être un entier.",
	"ip"             => "Le champ :attribute doit être une adresse IP valide.",
	"match"          => "Le format du champ :attribute est invalide.",
	"max"            => array(
		"numeric" => "Le :attribute doit être plus petit que :max.",
		"file"    => "Le :attribute doit être plus petit que :max kilo-octets.",
		"string"  => "Le :attribute doit être plus petit que :max caractères.",
	),
	"mimes"          => "Le champ :attribute doit être un fichier de type: :values.",
	"min"            => array(
		"numeric" => "Le champ :attribute doit être au moins :min.",
		"file"    => "Le champ :attribute doit être au moins :min kilo-octets.",
		"string"  => "Le champ :attribute doit être au moins :min caractères.",
	),
	"not_in"         => "Le champ sélectionné :attribute est invalide.",
	"numeric"        => "Le champ :attribute doit être un nombre.",
	"required"       => "Le champ :attribute est requis",
	"same"           => "Le champ :attribute et :other doivent être identique.",
	"size"           => array(
		"numeric" => "Le champ :attribute doit être :size.",
		"file"    => "Le champ :attribute doit être de :size kilo-octets.",
		"string"  => "Le champ :attribute doit être de :size caractères.",
	),
	"unique"         => "Le champ :attribute est déjà utilisé.",
	"url"            => "Le champ :attribute à un format invalide.",

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
