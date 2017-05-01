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

	"accepted"             => ":attribute deve ser aceite.",
	"active_url"           => "O :attribute não é uma URL válida.",
	"after"                => ":attribute deve ser uma data depois :date.",
	"alpha"                => ":attribute só pode conter letras.",
	"alpha_dash"           => ":attribute só pode conter letras, números e traços.",
	"alpha_num"            => ":attribute só pode conter letras e números.",
	"array"                => ":attribute deve ser uma matriz..",
	"before"               => ":attribute deve ser uma data antes :date.",
	"between"              => [
		"numeric" => ":attribute está entre :min e :max.",
		"file"    => ":attribute deve está entre :min e :max kilobytes.",
		"string"  => ":attribute deve está entre :min e :max caracteres.",
		"array"   => ":attribute deve ter entre :min e :max itens.",
	],
	"boolean"              => "O campo :attribute deve ser verdadeira ou falsa.",
	"confirmed"            => "Combinação de :attribute não corresponde.",
	"date"                 => ":attribute não é uma data válida.",
	"date_format"          => "O valor de :attribute não coincide com o formato :format.",
	"different"            => ":attribute e :other devem ser diferentes.",
	"digits"               => "O valor de :attribute deve ser de :digits digitos.",
	"digits_between"       => ":attribute deve estar entre :min e :max digitos.",
	"email"                => ":attribute deve ser um endereço de e-mail válido.",
	"filled"               => ":attribute é campo obrigatório.",
	"exists"               => ":attribute selecionado é inválido.",
	"image"                => ":attribute deve ser uma imagem.",
	"in"                   => ":attribute selecionado é inválido.",
	"integer"              => ":attribute deve ser um número inteiro.",
	"ip"                   => ":attribute deve ser um endereço IP válido.",
	"max"                  => [
		"numeric" => ":attribute não pode ser maior do que :max.",
		"file"    => ":attribute não pode ser maior do que :max kilobytes.",
		"string"  => ":attribute não pode ser maior do que :max caracteres.",
		"array"   => ":attribute não podem ter mais do que :max itens.",
	],
	"mimes"                => ":attribute deve ser um arquivo do tipo: :values.",
	"min"                  => [
		"numeric" => ":attribute deve ser de pelo menos :min.",
		"file"    => ":attribute deve ser de pelo menos :min kilobytes.",
		"string"  => ":attribute deve ser de pelo menos :min caracteres.",
		"array"   => ":attribute deve ter pelo menos :min itens.",
	],
	"not_in"               => ":attribute selecionado é inválido.",
	"numeric"              => ":attribute deve ser um número.",
	"regex"                => ":attribute formato é inválido.",
	"required"             => ":attribute é campo obrigatório.",
	"required_if"          => ":attribute é campo obrigatório quando :other é :value.",
	"required_with"        => ":attribute é campo obrigatório quando :values é presente.",
	"required_with_all"    => ":attribute é campo obrigatório quando :values é presente.",
	"required_without"     => ":attribute é campo obrigatório quando :values não é presente",
	"required_without_all" => ":attribute é campo obrigatório quando nenhum :values são presentes.",
	"same"                 => ":attribute and :other deve corresponder.",
	"size"                 => [
		"numeric" => ":attribute deve ser de :size.",
		"file"    => ":attribute deve ser de  :size kilobytes.",
		"string"  => ":attribute deve ser de :size characteres.",
		"array"   => ":attribute deve conter :size itens.",
	],
	"unique"               => ":attribute já foi usada.",
	"url"                  => ":attribute formato é inválido.",
	"timezone"             => ":attribute deve ser uma zona válida.",

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
			'rule-name' => 'custom-message',
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
