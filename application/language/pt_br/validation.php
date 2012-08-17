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

	"accepted"       => "O campo :attribute deve ser aceito.",
	"active_url"     => "O campo :attribute não é uma URL válida.",
	"after"          => "O campo :attribute deve ser uma data após :date.",
	"alpha"          => "O campo :attribute só pode conter letras.",
	"alpha_dash"     => "O campo :attribute só pode conter letras, números e travessões.",
	"alpha_num"      => "O campo :attribute só pode conter letras e números.",
	"before"         => "O campo :attribute deve ser uma data anterior a :date.",
	"between"        => array(
		"numeric" => "O valor de :attribute deve estar entre :min - :max.",
		"file"    => "O valor de :attribute deve ser entre :min - :max kilobytes.",
		"string"  => "O valor de :attribute deve ter entre :min - :max caracteres.",
	),
	"confirmed"      => "A confirmação do campo :attribute não coincide.",
	"different"      => "Os campos :attribute e :other devem ser diferentes.",
	"email"          => "Formato de :attribute é inválido.",
	"exists"         => "O item selecionado em :attribute é inválido.",
	"image"          => "O campo :attribute deve ser uma imagem.",
	"in"             => "O item selecionado em :attribute é inválido.",
	"integer"        => "O valor de :attribute deve ser um número inteiro.",
	"ip"             => "O valor de :attribute deve ser um endereço IP válido.",
	"match"          => "Formato de :attribute é inválido.",
	"max"            => array(
		"numeric" => "O valor de :attribute deve ser menor que :max.",
		"file"    => "O valor de :attribute deve ser menor que :max kilobytes.",
		"string"  => "O valor de :attribute deve ter menos que :max caracteres.",
	),
	"mimes"          => "O campo :attribute deve ser um arquivo do tipo :values.",
	"min"            => array(
		"numeric" => "O valor de :attribute deve ser pelo menos :min.",
		"file"    => "O valor de :attribute deve ter pelo menos :min kilobytes.",
		"string"  => "O valor de :attribute deve ter pelo menos :min caracteres.",
	),
	"not_in"         => "O item selecionado em :attribute é inválido.",
	"numeric"        => "O campo :attribute deve ser um número.",
	"required"       => "O campo :attribute é obrigatório",
	"same"           => "Os valores de :attribute e :other devem ser iguais.",
	"size"           => array(
		"numeric" => "O valor de :attribute deve ser :size.",
		"file"    => "O valor de :attribute deve ter :size kilobyte.",
		"string"  => "O valor de :attribute deve ter :size caracteres.",
	),
	"unique"         => ":attribute já está em uso e não pode ser atribuído (unique).",
	"url"            => "O formato de :attribute é inválido.",

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