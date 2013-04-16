<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| The following language lines contain the default error messages used by
	| the validator class. Some of these rules have multiple versions such
	| such as the size rules. Feel free to tweak each of these messages.
	|
	*/

	"accepted"         => "El campo :attribute debe ser aceptado.",
	"active_url"       => "El campo :attribute no es una URL v&aacute;lida.",
	"after"            => "El campo :attribute debe ser una fecha despu&eacute;s de :date.",
	"alpha"            => "El campo :attribute solo debe contener letras.",
	"alpha_dash"       => "El campo :attribute solo puede contener letras, n&uacute;meros y guiones.",
	"alpha_num"        => "El campo :attribute solo puede contener letras y n&uacute;meros.",
	"before"           => "El campo :attribute debe se una fecha antes de :date.",
	"between"          => array(
		"numeric" => "El campo :attribute debe estar entre :min - :max.",
		"file"    => "El campo :attribute debe tener entre :min - :max kilobytes.",
		"string"  => "El campo :attribute debe tener entre :min - :max caracteres.",
	),
	"confirmed"        => "La confirmaci&oacute;n del campo :attribute no coincide.",
	"date"             => "El campo :attribute no es una fecha v&aacute;lida.",
	"date_format"      => "El campo :attribute debe tener un formato de fecha v&aacute;lido.",
	"different"        => "Los campos :attribute y :other deben ser distintos.",
	"digits"           => "El campo :attribute debe tener :digits d&iacute;gitos.",
	"digits_between"   => "El campo :attribute debe tener entre :min y :max d&iacute;gitos.",
	"email"            => "El formaro del :attribute no es v&aacute;lido.",
	"exists"           => "El campo :attribute seleccionado no es v&aacute;lido.",
	"image"            => "El campo :attribute debe ser una imagen.",
	"in"               => "El campo :attribute seleccionado no es v&aacute;lido.",
	"integer"          => "El campo :attribute debe ser un n&uacute;mero entero.",
	"ip"               => "El campo :attribute debe ser una direcci&oacute;n IP v&aacute;lida.",
	"max"              => array(
		"numeric" => "El campo :attribute no debe ser mayor que :max.",
		"file"    => "EL campo :attribute no debe ser mayor de :max kilobytes.",
		"string"  => "El campo :attribute no debe ser mayor de :max caracteres.",
	),
	"mimes"            => "El campo :attribute debe ser un archivo del tipo :values.",
	"min"              => array(
		"numeric" => "El campo :attribute debe ser al menos :min.",
		"file"    => "El campo :attribute debe tener almenos :min kilobytes.",
		"string"  => "El campo :attribute debe tener almenos :min caracteres.",
	),
	"not_in"           => "El campo :attribute seleccionado no es v&aacute;lido.",
	"numeric"          => "El campo :attribute debe ser un n&uacute;mero.",
	"regex"            => "El formato del campo :attribute no es v&aacute;lido.",
	"required"         => "El campo :attribute es requerido.",
	"required_with"    => "El campo :attribute es requerido cuando el campo :values est&aacute; presente.",
	"required_without" => "Se requere del campo :attribute cuando el campo :values no est&aacute; presente.",
	"same"             => "Loa campos :attribute y :other deben coincidir.",
	"size"             => array(
		"numeric" => "El campo :attribute debe tener un tama&ntilde;o de :size.",
		"file"    => "El :attribute debe tener :size kilobytes de tama&ntilde;o.",
		"string"  => "El campo :attribute debe tener :size caracteres.",
	),
	"unique"           => "El :attribute no est&aacute; disponible.",
	"url"              => "El formato de :attribute no es v&aacute;lido.",

	/*
	|--------------------------------------------------------------------------
	| Custom Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| Here you may specify custom validation messages for attributes using the
	| convention "attribute.rule" to name the lines. This makes it quick to
	| specify a specific custom language line for a given attribute rule.
	|
	*/

	'custom' => array(),

	/*
	|--------------------------------------------------------------------------
	| Custom Validation Attributes
	|--------------------------------------------------------------------------
	|
	| The following language lines are used to swap attribute place-holders
	| with something more reader friendly such as E-Mail Address instead
	| of "email". This simply helps us make messages a little cleaner.
	|
	*/

	'attributes' => array(),

);
