<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| The following language lines contain the default error messages used by
	| the validator class. Some of these rules have multiple versions such
	| as the size rules. Feel free to tweak each of these messages here.
	|
	*/

	"accepted"             => "El :attribute debe ser aceptado.",
	"active_url"           => "El :attribute no es una URL válida.",
	"after"                => "El :attribute debe ser una fecha posterior a :date.",
	"alpha"                => "El :attribute sólo puede contener letras.",
	"alpha_dash"           => "El :attribute sólo puede contener letras, números y guiones.",
	"alpha_num"            => "El :attribute sólo puede contener letras y números.",
	"array"                => "El :attribute debe ser un array.",
	"before"               => "El :attribute debe ser una fecha anterior a :date.",
	"between"              => [
		"numeric" => "El :attribute debe estar entre :min y :max.",
		"file"    => "El :attribute debe estar entre :min y :max Kilobytes.",
		"string"  => "El :attribute debe estar entre :min y :max caracteres.",
		"array"   => "El :attribute debe estar entre :min y :max.",
	],
	"boolean"              => "El :attribute debe ser verdadero o falso.",
	"confirmed"            => "EL :attribute no coincide con la confirmación.",
	"date"                 => "El :attribute no es una fecha validad.",
	"date_format"          => "El :attribute no coincide con el formato :format.",
	"different"            => "El :attribute y :other deben ser diferentes.",
	"digits"               => "El :attribute debe ser :digits dígitos.",
	"digits_between"       => "El :attribute debe estar entre :min y :max dígitos.",
	"email"                => "El :attribute debe ser una dirección de correo electrónica válida.",
	"filled"               => "El :attribute es un campo requerido.",
	"exists"               => "El :attribute seleccionado no es válido.",
	"image"                => "El :attribute debe ser una imagen.",
	"in"                   => "El :attribute seleccionado es invalido.",
	"integer"              => "El :attribute debe ser un entero.",
	"ip"                   => "El :attribute debe ser una dirección IP válida.",
	"max"                  => [
		"numeric" => "El :attribute no puede ser superior a :max.",
		"file"    => "El :attribute no puede ser mayor a :max kilobytes.",
		"string"  => "El :attribute no puede ser mayor a :max caracteres.",
		"array"   => "El :attribute no puede tener más de :max items.",
	],
	"mimes"                => "El :attribute debe ser un archivo de tipo: :values.",
	"min"                  => [
		"numeric" => "El :attribute debe ser al menos :min.",
		"file"    => "El :attribute debe ser de al menos :min kilobytes.",
		"string"  => "El :attribute debe ser de al menos :min caracteres.",
		"array"   => "El :attribute debe tener por lo menos :min items.",
	],
	"not_in"               => "El :attribute seleccionado no es valido.",
	"numeric"              => "El :attribute debe ser un número.",
	"regex"                => "El formato de :attribute no es valido.",
	"required"             => "El :attribute es requerido.",
	"required_if"          => "El :attribute es requerido cuando :other es :value.",
	"required_with"        => "El :attribute es requerido cuando :values esta presente.",
	"required_with_all"    => "El :attribute es requerido cuando :values estan presentes.",
	"required_without"     => "El :attribute es requerido cuando :values no esta presente.",
	"required_without_all" => "El :attribute es requerido cuando ninguno de :values esta presente.",
	"same"                 => "El :attribute y :other deben incluirse.",
	"size"                 => [
		"numeric" => "El :attribute debe ser :size.",
		"file"    => "El :attribute debe ser :size kilobytes.",
		"string"  => "El :attribute debe ser :size caracteres.",
		"array"   => "El :attribute debe contener :size items.",
	],
	"unique"               => "El :attribute ya se encuentra registrado.",
	"url"                  => "El :attribute tienen formato no válido.",
	"timezone"             => "El :attribute debe ser una zona horaria válida.",

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

	'custom' => [
		'attribute-name' => [
			'rule-name' => 'Mensaje personalizado',
		],
	],

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

	'attributes' => [],

];
