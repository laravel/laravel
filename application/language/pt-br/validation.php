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

	"accepted"       => "O campo :attribute deve ser aceito.",
	"active_url"     => "A URL :attribute nao e valida.",
	"after"          => "A data :attribute deve ser posterior a :date.",
	"alpha"          => "O campo :attribute deve conter somente letras.",
	"alpha_dash"     => "O campo :attribute deve conter somente letras, numeros e travessoes.",
	"alpha_num"      => "O campo :attribute deve conter somente letras e numeros.",
	"before"         => "A data :attribute deve ser anterior a :date.",
	"between"        => array(
		"numeric" => "O campo :attribute deve ficar entre :min - :max.",
		"file"    => "O arquivo :attribute deve estar entre :min - :max kilobytes.",
		"string"  => "O campo :attribute deve ser entre :min - :max caracteres.",
	),
	"confirmed"      => "O campo :attribute de confirmacao nao equivale.",
	"different"      => "O campo :attribute e :other devem ser diferentes.",
	"email"          => "O campo :attribute nao e um email valido.",
	"exists"         => "O campo selecionado :attribute e invalido.",
	"image"          => "O campo :attribute deve ser uma imagem.",
	"in"             => "O campo selecionado :attribute e invalido.",
	"integer"        => "O campo :attribute deve ser um numero inteiro.",
	"ip"             => "O campo :attribute deve ser um endereco IP valido.",
	"match"          => "O formato do campo :attribute e invalido.",
	"max"            => array(
		"numeric" => "O campo :attribute deve ser menor que :max.",
		"file"    => "O arquivo :attribute deve ser menos que :max kilobytes.",
		"string"  => "O campo :attribute deve ser menos que :max caracteres.",
	),
	"mimes"          => "O campo :attribute deve ser um arquivo dos tipos: :values.",
	"min"            => array(
		"numeric" => "O campo :attribute deve ser pelo menos :min.",
		"file"    => "O arquivo :attribute deve ser mais que :min kilobytes.",
		"string"  => "O campo :attribute deve ter pelo menos :min caracteres.",
	),
	"not_in"         => "O campo selecionado :attribute e invalido.",
	"numeric"        => "O campo :attribute deve ser um numero.",
	"required"       => "O campo :attribute e obrigatorio.",
	"same"           => "O campo :attribute e :other devem ser iguais.",
	"size"           => array(
		"numeric" => "O campo :attribute deve ter tamanho :size.",
		"file"    => "O arquivo :attribute deve ter :size kilobyte.",
		"string"  => "O campo :attribute deve ter :size caracteres.",
	),
	"unique"         => "O campo :attribute ja foi escolhido.",
	"url"            => "A URL :attribute possui um formato invalido.",

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