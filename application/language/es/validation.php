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

	"accepted"       => "El campo :attribute debe ser aceptado.",
	"active_url"     => "El campo :attribute no es una URL válida.",
	"after"          => "El campo :attribute debe ser una fecha posterior a :date.",
	"alpha"          => "El campo :attribute sólo puede contener letras.",
	"alpha_dash"     => "El campo :attribute sólo puede contener letras, números, y guiones.",
	"alpha_num"      => "El campo :attribute sólo puede contener letras y números.",
	"array"          => "El campo :attribute debe tener elementos seleccionados.",
	"before"         => "El campo :attribute debe ser una fecha anterior a :date.",
	"between"        => array(
		"numeric" => "El campo :attribute debe estar entre :min - :max.",
		"file"    => "El campo :attribute debe estar entre :min - :max kilobytes.",
		"string"  => "El campo :attribute debe estar entre :min - :max carácteres.",
	),
	"confirmed"      => "El campo :attribute no se ha confirmado.",
	"count"          => "El campo :attribute debe tener exactamente :count elementos seleccionados.",
	"countbetween"   => "El campo :attribute debe tener entre :min y :max elementos seleccionados.",
	"countmax"       => "El campo :attribute debe tener menos de :max elementos seleccionados.",
	"countmin"       => "El campo :attribute debe tener almenos :min elementos seleccionados.",
	"different"      => "El campo :attribute y :other deben ser diferentes.",
	"email"          => "El campo :attribute tiene un formato inválido.",
	"exists"         => "El campo seleccionado :attribute es inválido.",
	"image"          => "El campo :attribute debe ser una imagen.",
	"in"             => "El campo seleccionado :attribute es inválido.",
	"integer"        => "El campo :attribute debe ser un número entero.",
	"ip"             => "El campo :attribute debe ser una dirección IP válida.",
	"match"          => "El campo :attribute tiene un formato inválido.",
	"max"            => array(
		"numeric" => "El campo :attribute debe ser menor que :max.",
		"file"    => "El campo :attribute debe ser menor que :max kilobytes.",
		"string"  => "El campo :attribute debe ser menor que :max carácteres.",
	),
	"mimes"          => "El campo :attribute debe ser un archivo de tipo: :values.",
	"min"            => array(
		"numeric" => "El campo :attribute debe tener almenos :min.",
		"file"    => "El campo :attribute debe tener almenos :min kilobytes.",
		"string"  => "El campo :attribute debe tener almenos :min carácteres.",
	),
	"not_in"         => "El campo seleccionado :attribute es inválido.",
	"numeric"        => "El campo :attribute debe ser un número.",
	"required"       => "El campo :attribute es requerido.",
	"same"           => "El campo :attribute y :other deben coincidir.",
	"size"           => array(
		"numeric" => "El campo :attribute debe ser :size.",
		"file"    => "El campo :attribute debe ser :size kilobyte.",
		"string"  => "El campo :attribute debe ser :size carácteres.",
	),
	"unique"         => "El campo :attribute ya existe y no se puede repetir.",
	"url"            => "El campo :attribute tiene un formato inválido.",

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