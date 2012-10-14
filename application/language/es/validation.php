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

	"accepted"       => ":attribute debe ser aceptado.",
	"active_url"     => ":attribute debe ser una URL válida.",
	"after"          => ":attribute debe ser una fecha después de :date.",
	"alpha"          => ":attribute solo debe contener letras.",
	"alpha_dash"     => ":attribute solo debe contener letras, números y guiónes.",
	"alpha_num"      => ":attribute solo debe contener letras y números.",
	"array"          => ":attribute debe tener un elemento seleccionado.",
	"before"         => ":attribute debe ser una fecha anterior a :date.",
	"between"        => array(
		"numeric" => ":attribute debe ser entre :min y :max.",
		"file"    => ":attribute debe ser entre :min y :max kilobytes.",
		"string"  => ":attribute debe contener entre :min y :max caracteres.",
	),
	"confirmed"      => ":attribute no es igual.",
	"count"          => ":attribute debe contener exactamente :count elementos seleccionados.",
	"countbetween"   => ":attribute debe contener entre :min y :max elementos seleccionados.",
	"countmax"       => ":attribute debe contener menos de :max elementos seleccionados.",
	"countmin"       => ":attribute debe contener al menos :min elementos seleccionados.",
	"different"      => ":attribute y :other deben ser distintos.",
	"email"          => ":attribute tiene un formato inválido.",
	"exists"         => "El elemento seleccionado :attribute es inválido.",
	"image"          => ":attribute debe ser de tipo imágen.",
	"in"             => ":attribute es inválido.",
	"integer"        => ":attribute debe ser entero.",
	"ip"             => ":attribute debe ser una dirección IP válida.",
	"match"          => ":attribute contiene un formato inválido.",
	"max"            => array(
		"numeric" => ":attribute debe ser menor a :max.",
		"file"    => ":attribute debe ser menor a :max kilobytes.",
		"string"  => ":attribute debe ser menor a :max caracteres.",
	),
	"mimes"          => ":attribute debe ser archivo tipo: :values.",
	"min"            => array(
		"numeric" => ":attribute debe ser mínimo :min.",
		"file"    => ":attribute debe ser mínimo de :min kilobytes.",
		"string"  => ":attribute debe contener mínimo :min caracteres.",
	),
	"not_in"         => ":attribute es invalido.",
	"numeric"        => ":attribute debe ser numérico.",
	"required"       => ":attribute es requerido.",
	"same"           => ":attribute y :other debe ser iguales.",
	"size"           => array(
		"numeric" => ":attribute debe ser :size.",
		"file"    => ":attribute debe ser :size kilobyte.",
		"string"  => ":attribute debe ser de :size caracteres.",
	),
	"unique"         => ":attribute se encuentra ocupado.",
	"url"            => ":attribute el formato es inválido.",

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