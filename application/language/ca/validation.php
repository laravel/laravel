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

	"accepted"       => "El camp: attribute ha de ser acceptat.",
	"active_url"     => "El camp: attribute no es una URL vàlida.",
	"after"          => "El camp: attribute ha de ser una data posterior a :date.",
	"alpha"          => "El camp: attribute només pot contenir lletres.",
	"alpha_dash"     => "El camp: attribute només pot contenir lletres, nombres, i guions.",
	"alpha_num"      => "El camp: attribute només pot contenir lletres i nombres.",
	"array"          => "El camp: attribute ha de tenir elements seleccionats.",
	"before"         => "El camp: attribute ha de ser una data anterior a :date.",
	"between"        => array(
		"numeric" => "El camp: attribute ha de romandre entre :min - :max.",
		"file"    => "El camp: attribute ha de romandre entre :min - :max kilobytes.",
		"string"  => "El camp: attribute ha de romandre entre :min - :max caràcters.",
	),
	"confirmed"      => "El camp: attribute no se ha confirmado.",
	"count"          => "El camp: attribute ha de tenir exactament :count elements seleccionats.",
	"countbetween"   => "El camp: attribute ha de tenir entre :min i :max elements seleccionats.",
	"countmax"       => "El camp: attribute ha de tenir menys de :max elements seleccionats.",
	"countmin"       => "El camp: attribute ha de tenir almenys :min elements seleccionats.",
	"different"      => "El camp: attribute i :other deben ser diferentes.",
	"email"          => "El camp: attribute té un format invàlid.",
	"exists"         => "o seleccionado :attribute es invàlid.",
	"image"          => "El camp: attribute ha de ser una imagen.",
	"in"             => "o seleccionado :attribute es invàlid.",
	"integer"        => "El camp: attribute ha de ser un número entero.",
	"ip"             => "El camp: attribute ha de ser una dirección IP vàlida.",
	"match"          => "El camp: attribute té un format invàlid.",
	"max"            => array(
		"numeric" => "El camp: attribute ha de ser menor que :max.",
		"file"    => "El camp: attribute ha de ser menor que :max kilobytes.",
		"string"  => "El camp: attribute ha de ser menor que :max caràcters.",
	),
	"mimes"          => "El camp: attribute ha de ser un arxius de tipus: :values.",
	"min"            => array(
		"numeric" => "El camp: attribute ha de tenir almenys :min.",
		"file"    => "El camp: attribute ha de tenir almenys :min kilobytes.",
		"string"  => "El camp: attribute ha de tenir almenys :min caràcters.",
	),
	"not_in"         => "o seleccionado :attribute es invàlid.",
	"numeric"        => "El camp: attribute ha de ser un número.",
	"required"       => "El camp: attribute es requerit.",
	"same"           => "El camp: attribute i :other deben coincidir.",
	"size"           => array(
		"numeric" => "El camp: attribute ha de ser :size.",
		"file"    => "El camp: attribute ha de ser :size kilobyte.",
		"string"  => "El camp: attribute ha de ser :size caràcters.",
	),
	"unique"         => "El camp: attribute ja existeix i no es pot repetir.",
	"url"            => "El camp: attribute té un format invàlid.",

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