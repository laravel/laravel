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

	"accepted"       => "O campo :attribute deve ser aceite.",
	"active_url"     => "O campo :attribute não contém uma URL válida.",
	"after"          => "O campo :attribute deve conter uma data após :date.",
	"alpha"          => "O campo :attribute só pode conter letras.",
	"alpha_dash"     => "O campo :attribute só pode conter letras, números e traços.",
	"alpha_num"      => "O campo :attribute só pode conter letras e números.",
	"before"         => "O campo :attribute deve conter uma data anterior a :date.",
	"between"        => array(
		"numeric" => "O campo :attribute deve estar entre :min - :max.",
		"file"    => "O campo :attribute deve estar entre :min - :max kilobytes.",
		"string"  => "O campo :attribute deve estar entre :min - :max caracteres.",
	),
	"confirmed"      => "O campo :attribute e a sua confirmação não coincidem.",
	"different"      => "Os campos :attribute e :other devem ser diferentes.",
	"email"          => "O campo :attribute não contém um e-mail válido.",
	"exists"         => "O campo :attribute selecionado é inválido.",
	"image"          => "O campo :attribute deve conter uma imagem.",
	"in"             => "O campo :attribute selecionado é inválido.",
	"integer"        => "O campo :attribute deve conter um inteiro.",
	"ip"             => "O campo :attribute deve conter um endereço IP válido.",
	"match"          => "O formato do campo :attribute é inválido.",
	"max"            => array(
		"numeric" => "O campo :attribute deve conter um valor inferior a :max.",
		"file"    => "O campo :attribute deve conter um ficheiro com tamanho inferior a :max kilobytes.",
		"string"  => "O campo :attribute deve conter texto com tamanho inferior a :max caracteres.",
	),
	"mimes"          => "O campo :attribute deve conter um arquivo do tipo: :values.",
	"min"            => array(
		"numeric" => "O campo :attribute deve conter uma valor igual ou superior a :min.",
		"file"    => "O campo :attribute deve conter um ficheiro com tamanho igual ou superior a :min kilobytes.",
		"string"  => "O campo :attribute deve conter texto com tamanho igual ou superior a :min caracteres.",
	),
	"not_in"         => "O campo :attribute selecionado é inválido.",
	"numeric"        => "O campo :attribute deve conter um valor numérico.",
	"required"       => "O campo :attribute deve ser preenchido.",
	"same"           => "Os campos :attribute e :other devem conter valores iguais.",
	"size"           => array(
		"numeric" => "O campo :attribute deve conter o valor :size.",
		"file"    => "O campo :attribute deve conter um ficheiro com tamanho igaul a :size kilobyte.",
		"string"  => "O campo :attribute deve conter texto com tamanho igual a :size caracteres.",
	),
	"unique"         => "Este :attribute já existe.",
	"url"            => "O formato do campo :attribute é inválido.",

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
