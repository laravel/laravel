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
	| Spanish Translation:
	| Mario Cuba - http://mariocuba.net
	|
	*/

	"accepted"       => ":attribute tiene que ser aceptado.",
	"active_url"     => ":attribute no es una URL válida.",
	"after"          => ":attribute debe ser una fecha después de :date.",
	"alpha"          => ":attribute solo puede contener letras.",
	"alpha_dash"     => ":attribute solo puede contener letras, números, y guiones.",
	"alpha_num"      => ":attribute solo puede contener letras y números.",
	"array"          => ":attribute debe tener elementos seleccionados.",
	"before"         => ":attribute debe ser una fecha antes de :date.",
	"between"        => array(
		"numeric" => ":attribute debe estar entre :min y :max.",
		"file"    => ":attribute debe estar entre :min y :max kilobytes.",
		"string"  => ":attribute debe estar entre :min y :max caracteres.",
	),
	"confirmed"      => ":attribute no está confirmado.",
	"count"          => ":attribute debe tener exactamente :count elementos seleccionados.",
	"countbetween"   => ":attribute debe tener entre :min y :max elementos seleccionados.",
	"countmax"       => ":attribute debe tener menos de :max elementos seleccionados.",
	"countmin"       => ":attribute debe tener por lo menos :min elementos seleccionados.",
	"different"      => ":attribute y :other deben ser distintos.",
	"email"          => "El formato del campo :attribute es inválido.",
	"exists"         => ":attribute es inválido.",
	"image"          => ":attribute debe ser una imagen.",
	"in"             => ":attribute es inválido.",
	"integer"        => ":attribute debe ser un número entero.",
	"ip"             => ":attribute debe ser una dirección IP válida.",
	"match"          => "El formato del campo :attribute es inválido.",
	"max"            => array(
		"numeric" => ":attribute debe ser menor que :max.",
		"file"    => ":attribute debe ser menor de :max kilobytes.",
		"string"  => ":attribute debe ser menor de :max caracteres.",
	),
	"mimes"          => ":attribute debe ser un archivo del tipo: :values.",
	"min"            => array(
		"numeric" => ":attribute debe ser por lo menos :min.",
		"file"    => ":attribute debe tener un tamaño mínimo de :min kilobytes.",
		"string"  => ":attribute debe tener una longitud mínima de :min caracteres.",
	),
	"not_in"         => ":attribute es inválido.",
	"numeric"        => ":attribute debe ser un número.",
	"required"       => "El campo :attribute es requerido.",
	"same"           => ":attribute y :other deben ser iguales.",
	"size"           => array(
		"numeric" => ":attribute debe tener un tamaño de :size.",
		"file"    => ":attribute debe tener un tamaño de :size kilobytes.",
		"string"  => ":attribute debe tener una longitud de :size caracteres.",
	),
	"unique"         => ":attribute ya existe.",
	"url"            => "El formato de :attribute es inválido.",

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