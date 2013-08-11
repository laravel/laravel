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

  "accepted"       => "El campo :attribute debe ser aceptado.",
	"active_url"     => "El campo :attribute no es una URL v&aacute;lida.",
	"after"          => "El campo :attribute debe ser una fecha posterior a :date.",
	"alpha"          => "El campo :attribute debe contener &uacute;nicamente letras.",
	"alpha_dash"     => "El campo :attribute debe contener &uacute;nicamente letras, n&uacute;meros y guiones.",
	"alpha_num"      => "El campo :attribute debe contener &uacute;nicamente letras y n&uacute;meros.",
	"array"          => "El campo :attribute debe tener elementos seleccionados.",
	"before"         => "El campo :attribute debe ser una fecha anterior a :date.",
	"between"        => array(
		"numeric" => "El campo :attribute debe estar entre :min - :max.",
		"file"    => "El campo :attribute debe tener entre :min - :max kilobytes.",
		"string"  => "El campo :attribute debe tener entre :min - :max caracteres.",
		"array"   => "El campo :attribute debe tener entre :min - :max items.",
	),
	"confirmed"        => "La confirmaci&oacute;n del campo :attribute es diferente.",
	"date"             => "El campo :attribute no es una fecha v&aacute;lida.",
	"date_format"      => "El campo :attribute no coincide con el formato :format.",
	"different"        => "El campo :attribute y :other deben ser diferentes.",
	"digits"           => "El campo :attribute debe ser de :digits digitos.",
	"digits_between"   => "El campo :attribute must be between :min and :max digits.",
  "email"            => "El formato del campo :attribute no es v&aacute;lido.",
	"exists"           => "El campo :attribute seleccionado no es v&aacute;lido.",
	"image"            => "El campo :attribute debe ser una imagen.",
	"in"               => "El campo :attribute seleccionado no es v&aacute;lido.",
	"integer"          => "El campo :attribute debe ser un n&uacute;mero entero.",
	"ip"               => "El campo :attribute debe ser una direcci&oacute;n IP v&aacute;lida.",
  "max"              => array(
		"numeric" => "El campo :attribute debe ser menor que :max.",
		"file"    => "El campo :attribute debe tener menos que :max kilobytes.",
		"string"  => "El campo :attribute debe tener menos de :max caracteres.",
		"array"   => "El campo :attribute no debe tener mas de :max items.",
	),
	"mimes"            => "El campo :attribute debe ser un fichero del tipo: :values.",
  "min"              => array(
		"numeric" => "El campo :attribute debe ser al menos :min.",
		"file"    => "El campo :attribute debe tener al menos :min kilobytes.",
		"string"  => "El campo :attribute debe tener al menos :min caracteres.",
		"array"   => "El campo :attribute debe tener al menos :min items.",
	),
	"not_in"           => "El campo :attribute seleccionado no es v&aacute;lido.",
	"numeric"          => "El campo :attribute debe ser un n&uacute;mero.",
	"regex"            => "El campo :attribute tiene un formato no v&aacute;lido.",
	"required"         => "El campo :attribute es obligatorio.",
	"required_if"      => "El campo :attribute es requerido cuando :other es :value.",
	"required_with"    => "El campo :attribute es requerido cuando :values esta presente.",
	"required_without" => "El campo :attribute es requerido cuando :values no esta presente.",
	"same"             => "El campo :attribute y el campo :other deben ser iguales.",
  "size"             => array(
		"numeric" => "El campo :attribute debe ser :size.",
		"file"    => "El campo :attribute debe tener :size kilobytes.",
		"string"  => "El campo :attribute debe tener :size caracteres.",
		"array"   => "El campo :attribute debe contener :size items.",
	),
  "unique"           => "El campo :attribute est&aacute; ocupado.",
	"url"              => "El formato del campo :attribute no es v&aacute;lido.",

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
