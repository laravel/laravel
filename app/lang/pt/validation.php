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

	"accepted"         => "O :attribute deve ser aceite.",
	"active_url"       => "O :attribute não é uma URL válida.",
	"after"            => "O :attribute deve ser uma data depois de :date.",
	"alpha"            => "O :attribute só pode conter letras.",
	"alpha_dash"       => "O :attribute só pode conter letras, números, e hífens.",
	"alpha_num"        => "O :attribute só pode conter letras e números.",
	"before"           => "O :attribute deve ser uma data antes de :date.",
	"between"          => array(
		"numeric" => "O :attribute deve ter entre :min - :max.",
		"file"    => "O :attribute deve ter entre :min - :max kilobytes.",
		"string"  => "O :attribute deve ter entre :min - :max caracteres.",
	),
	"confirmed"        => "O :attribute confirmação não coincide.",
	"date"             => "O :attribute não é uma data válida.",
	"date_format"      => "O :attribute não corresponde ao formato :format.",
	"different"        => "O :attribute e :other devem ser diferentes.",
	"digits"           => "O :attribute deve ter :digits dígitos.",
	"digits_between"   => "O :attribute deve ter entre :min e :max dígitos.",
	"email"            => "O :attribute não é um endereço de e-mail válido.",
	"exists"           => "O :attribute seleccionado é inválido.",
	"image"            => "O :attribute deve ser uma imagem.",
	"in"               => "O :attribute seleccionado é inválido.",
	"integer"          => "O :attribute deve ser um número inteiro.",
	"ip"               => "O :attribute deve ser um endereço IP válido.",
	"max"              => array(
		"numeric" => "O :attribute deve ser inferior a :max.",
		"file"    => "O :attribute deve ter menos que :max kilobytes.",
		"string"  => "O :attribute deve ter menos que :max caracteres.",
	),
	"mimes"            => "O :attribute deve ser um ficheiro do tipo: :values.",
	"min"              => array(
		"numeric" => "O :attribute deve ser pelo menos :min.",
		"file"    => "O :attribute deve ter pelo menos :min kilobytes.",
		"string"  => "O :attribute deve ter pelo menos :min caracteres.",
	),
	"not_in"           => "O :attribute seleccionado é inválido.",
	"numeric"          => "O :attribute deve ser um número.",
	"regex"            => "O formato do campo \":attribute\" é inválido.",
	"required"         => "O campo \":attribute\" é obrigatório.",
	"required_if"      => "O campo \":attribute\" é obrigatório quando :other é :value.",
	"required_with"    => "O campo \":attribute\" é obrigatório quando :values está presente.",
	"required_without" => "O campo \":attribute\" é obrigatório quando :values não está presente.",
	"same"             => "O :attribute e :other devem ser iguais.",
	"size"             => array(
		"numeric" => "O :attribute deve ser :size.",
		"file"    => "O :attribute deve ter :size kilobytes.",
		"string"  => "O :attribute deve ter :size caracteres.",
	),
	"unique"           => "Este :attribute já existe.",
	"url"              => "O :attribute não é uma URL válida.",

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
