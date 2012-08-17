<?php 

return array(
    // Tradução português do Brasil

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

	"accepted"       => ":attribute deve ser aceito.",
	"active_url"     => ":attribute não é uma URL válida.",
	"after"          => ":attribute deve ser uma data após :date.",
	"alpha"          => ":attribute só pode conter letras.",
	"alpha_dash"     => ":attribute só pode conter letras, números e travessões.",
	"alpha_num"      => ":attribute só pode conter letras e números.",
	"before"         => ":attribute deve ser uma data anterior a :date.",
	"between"        => array(
		"numeric" => ":attributedeve estar entre :min - :max.",
		"file"    => ":attribute deve ser entre :min - :max kilobytes.",
		"string"  => ":attribute deve ter entre :min - :max caracteres.",
	),
	"confirmed"      => "A :attribute confirmação não coincide.",
	"different"      => ":attribute e :other devem ser diferentes.",
	"email"          => ":attribute formato inválido.",
	"exists"         => "O item selecionado :attribute é inválido.",
	"image"          => ":attribute deve ser uma imagem.",
	"in"             => "O item selecionado :attribute é inválido.",
	"integer"        => ":attribute deve ser um número inteiro.",
	"ip"             => ":attribute deve ser um endereço IP válido.",
	"match"          => ":attribute formato inválido.",
	"max"            => array(
		"numeric" => ":attribute deve ser menor que :max.",
		"file"    => ":attribute deve ser menor que :max kilobytes.",
		"string"  => ":attribute deve ter menos que :max caracteres.",
	),
	"mimes"          => ":attribute deve ser um arquivo do tipo :values.",
	"min"            => array(
		"numeric" => ":attribute deve ser pelo menos :min.",
		"file"    => ":attribute deve ter pelo menos :min kilobytes.",
		"string"  => ":attribute deve ter pelo menos :min caracteres.",
	),
	"not_in"         => "O item selecionado :attribute é inválido.",
	"numeric"        => ":attribute deve ser um número.",
	"required"       => ":attribute campo é requerido.",
	"same"           => ":attribute e :other devem ser iguais.",
	"size"           => array(
		"numeric" => ":attribute deve ser :size.",
		"file"    => ":attribute deve ter :size kilobyte.",
		"string"  => ":attribute deve ter :size caracteres.",
	),
	"unique"         => ":attribute já está em uso e não pode ser atribuído (unique).",
	"url"            => ":attribute formato inválido.",

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