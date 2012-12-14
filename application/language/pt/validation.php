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

	"accepted"       => "O :attribute deve ser aceite.",
	"active_url"     => "O :attribute não é uma URL válida.",
	"after"          => "O :attribute deve ser uma data após :date.",
	"alpha"          => "O :attribute só pode conter letras.",
	"alpha_dash"     => "O :attribute só pode conter letras, números e traços.",
	"alpha_num"      => "O :attribute só pode conter letras e números.",
	"before"         => "O :attribute deve ser uma data anterior à :date.",
	"between"        => array(
		"numeric" => "O :attribute deve estar entre :min - :max.",
		"file"    => "O :attribute deve estar entre :min - :max kilobytes.",
		"string"  => "O :attribute deve estar entre :min - :max caracteres.",
	),
	"confirmed"      => "O :attribute confirmação não coincide.",
	"different"      => "O :attribute e :other devem ser diferentes.",
	"email"          => "O :attribute não é um e-mail válido.",
	"exists"         => "O :attribute selecionado é inválido.",
	"image"          => "O :attribute deve ser uma imagem.",
	"in"             => "O :attribute selecionado é inválido.",
	"integer"        => "O :attribute deve ser um inteiro.",
	"ip"             => "O :attribute deve ser um endereço IP válido.",
	"match"          => "O formato :attribute é inválido.",
	"max"            => array(
		"numeric" => "O :attribute deve ser inferior a :max.",
		"file"    => "O :attribute deve ser inferior a :max kilobytes.",
		"string"  => "O :attribute deve ser inferior a :max caracteres.",
	),
	"mimes"          => "O :attribute deve ser um arquivo do tipo: :values.",
	"min"            => array(
		"numeric" => "O :attribute deve conter pelo menos :min.",
		"file"    => "O :attribute deve conter pelo menos :min kilobytes.",
		"string"  => "O :attribute deve conter pelo menos :min caracteres.",
	),
	"not_in"         => "O :attribute selecionado é inválido.",
	"numeric"        => "O :attribute deve ser um número.",
	"required"       => "O campo :attribute deve ser preenchido.",
	"same"           => "O :attribute e :other devem ser iguais.",
	"size"           => array(
		"numeric" => "O :attribute deve ser :size.",
		"file"    => "O :attribute deve ter :size kilobyte.",
		"string"  => "O :attribute deve ter :size caracteres.",
	),
	"unique"         => "Este :attribute já existe.",
	"url"            => "O formato :attribute é inválido.",

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