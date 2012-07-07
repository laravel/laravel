<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| The following language lines contain the default error messages used
	| by the validator class. Some of the rules contain multiple versions,
	| such as the size (max, min, entre) rules. These versions are used
	| for different input types such as strings and files.
	|
	| These language lines may be easily changed to provide custom error
	| messages in your application. Error messages for custom validation
	| rules may also be added to this file.
	|
	*/

	"accepted"       => "Le champ :attribute doit &amp;ecirc;tre accept&eacute;.",
	"active_url"     => "Le champ :attribute n'est pas une URL valide.",
	"after"          => "Le champ :attribute doit &ecirc;tre une date post&eacute;rieure &agrave; :date.",
	"alpha"          => "Le champ :attribute ne doit contenir que des lettres.",
	"alpha_dash"     => "Le champ :attribute ne doit contenir que des lettres, chiffres ou tirets.",
	"alpha_num"      => "Le champ :attribute ne doit contenir que des lettres ou des chiffres.",
	"before"         => "Le champ :attribute doit &ecirc;tre une date ant&eacute;rieure &agrave; :date.",
	"entre"        => array(
		"numeric" => "Le champ :attribute doit &amp;ecirc;tre entre :min - :max.",
		"file"    => "Le champ :attribute doit &amp;ecirc;tre entre :min - :max kilobytes.",
		"string"  => "Le champ :attribute doit &amp;ecirc;tre entre :min - :max caract&egrave;res.",
	),
	"confirmed"      => "La confirmation du champ :attribute ne correspond pas.",
	"different"      => "Les champs :attribute et :other doivent &amp;ecirc;tre diff&eacute;rents.",
	"email"          => "Le format du champ :attribute est invalide.",
	"exists"         => "Le :attribute s&eacute;l&eacute;ctionn&eacute; est invalide.",
	"image"          => "Le champ :attribute doit &amp;ecirc;tre une image.",
	"in"             => "Le :attribute s&eacute;l&eacute;ctionn&eacute; est invalide.",
	"integer"        => "Le champ :attribute doit &amp;ecirc;tre un entier.",
	"ip"             => "Le champ :attribute doit &amp;ecirc;tre une adresse IP valide.",
	"match"          => "Le format du champ :attribute est invalide.",
	"max"            => array(
		"numeric" => "Le champ :attribute doit &amp;ecirc;tre inf&eacute;rieur &agrave; :max.",
		"file"    => "Le champ :attribute doit &amp;ecirc;tre inf&eacute;rieur &agrave; :max kilobytes.",
		"string"  => "Le champ :attribute doit &amp;ecirc;tre inf&eacute;reur &agrave; :max caract&egrave;res.",
	),
	"mimes"          => "Le champ :attribute doit &amp;ecirc;tre un fichier de type : :values.",
	"min"            => array(
		"numeric" => "Le champ :attribute doit &amp;ecirc;tre sup&eacute;rieur &agrave; :min.",
		"file"    => "Le champ :attribute doit &amp;ecirc;tre sup&eacute;rieur &agrave; :min kilobytes.",
		"string"  => "Le champ :attribute doit &amp;ecirc;tre sup&eacute;rieur &agrave; :min caract&egrave;res.",
	),
	"not_in"         => "Le :attribute s&eacute;l&eacute;ctionn&eacute; est invalide.",
	"numeric"        => "Le champ :attribute doit &amp;ecirc;tre un nombre.",
	"required"       => "Le champ :attribute est requis.",
	"same"           => "Les champs :attribute et :other doivent correspondre.",
	"size"           => array(
		"numeric" => "Le champ :attribute doit &amp;ecirc;tre de :size.",
		"file"    => "Le champ :attribute doit &amp;ecirc;tre de :size kilobyte.",
		"string"  => "Le champ :attribute doit &amp;ecirc;tre de :size caract&egrave;res.",
	),
	"unique"         => "Le champ :attribute est d&eacute;j&agrave; pris.",
	"url"            => "Le format du champ :attribute est invalide.",

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