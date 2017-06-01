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

	"accepted"       => "O :attribute tem que ser aceite.",
	"active_url"     => "O :attribute não é um URL válido.",
	"after"          => "O :attribute deve ser uma data depois de :date.",
	"alpha"          => "O :attribute deve conter apenas letras.",
	"alpha_dash"     => "O :attribute deve conter apenas letras, números e traços.",
	"alpha_num"      => "O :attribute deve conter apenas letras e números.",
	"array"          => "O :attribute deve ter elementos selecionados.",
	"before"         => "O :attribute deve ser uma data anterior a :date.",
	"between"        => array(
		"numeric" => "O :attribute deve estar entre :min - :max.",
		"file"    => "O :attribute deve estar entre :min - :max kilobytes.",
		"string"  => "O :attribute deve estar entre :min - :max caracteres.",
	),
	"confirmed"      => "A confirmação do :attribute deve ser igual.",
	"count"          => "O :attribute deve ter exactamente :count elementos selecionados.",
	"countbetween"   => "O :attribute deve ter um valor minimo de :min e maximo de :max campos selecionados.",
	"countmax"       => "O :attribute deve ter menos de :max campos selecionados.",
	"countmin"       => "O :attribute deve ter no minimo :min campos selecionados.",
	"different"      => "O :attribute e :other devem ser diferentes.",
	"email"          => "O formato do :attribute não é válido.",
	"exists"         => "O :attribute selecionado não é válido.",
	"image"          => "O :attribute deve ser uma imagem.",
	"in"             => "O campo selecionado :attribute não é válido.",
	"integer"        => "O :attribute deve ser um inteiro.",
	"ip"             => "O :attribute deve conter um IP válido.",
	"match"          => "O formato do :attribute não é válido.",
	"max"            => array(
		"numeric" => "O :attribute deve ser menor que :max.",
		"file"    => "O :attribute deve ser menor que :max kilobytes.",
		"string"  => "O :attribute deve ter menos de :max caracteres.",
	),
	"mimes"          => "O :attribute deve ser do tipo: :values.",
	"min"            => array(
		"numeric" => "O :attribute deve ser menor que :min.",
		"file"    => "O :attribute deve ser menor que :min kilobytes.",
		"string"  => "O :attribute deve ter menos de :min characters.",
	),
	"not_in"         => "O :attribute selecionado não é válido.",
	"numeric"        => "O :attribute deve ser um número.",
	"required"       => "O :attribute é obrigatório.",
	"same"           => "O :attribute e :other devem ser iguais.",
	"size"           => array(
		"numeric" => "O :attribute deve ter um tamanho de :size.",
		"file"    => "O :attribute deve ter :size kilobyte.",
		"string"  => "O :attribute dece ter :size caracteres.",
	),
	"unique"         => "O :attribute Já foi está em uso.",
	"url"            => "O formato do :attribute não é válido.",

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