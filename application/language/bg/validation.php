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

	"accepted"       => "Трябва да приемете :attribute.",
	"active_url"     => "Полето :attribute не е валиден URL адрес.",
	"after"          => "Полето :attribute трябва да бъде дата след :date.",
	"alpha"          => "Полето :attribute трябва да съдържа само букви.",
	"alpha_dash"     => "Полето :attribute трябва да съдържа само букви, цифри, долна черта и тире.",
	"alpha_num"      => "Полето :attribute трябва да съдържа само букви и цифри.",
	"array"          => "The :attribute must have selected elements.",
	"before"         => "Полето :attribute трябва да бъде дата преди :date.",
	"between"        => array(
		"numeric" => "Полето :attribute трябва да бъде между :min и :max.",
		"file"    => "Полето :attribute трябва да бъде между :min и :max килобайта.",
		"string"  => "Полето :attribute трябва да бъде между :min и :max знака.",
	),
	"confirmed"      => "Полето :attribute не е потвърдено.",
	"count"          => "The :attribute must have exactly :count selected elements.",
	"countbetween"   => "The :attribute must have between :min and :max selected elements.",
	"countmax"       => "The :attribute must have less than :max selected elements.",
	"countmin"       => "The :attribute must have at least :min selected elements.",
	"different"      => "Полетата :attribute и :other трябва да са различни.",
	"email"          => "Полето :attribute е с невалиден формат.",
	"exists"         => "Избраната стойност на :attribute вече съществува.",
	"image"          => "Полето :attribute трябва да бъде изображение.",
	"in"             => "Стойността на :attribute е невалидна.",
	"integer"        => "Полето :attribute трябва да бъде цяло число.",
	"ip"             => "Полето :attribute трябва да бъде IP адрес.",
	"match"          => "Полето :attribute е с невалиден формат.",
	"max"            => array(
		"numeric" => "Полето :attribute трябва да бъде по-малко от :max.",
		"file"    => "Полето :attribute трябва да бъде по-малко от :max килобайта.",
		"string"  => "Полето :attribute трябва да бъде по-малко от :max знака.",
	),
	"mimes"          => "Полето :attribute трябва да бъде файл от тип: :values.",
	"min"            => array(
		"numeric" => "Полето :attribute трябва да бъде минимум :min.",
		"file"    => "Полето :attribute трябва да бъде минимум :min килобайта.",
		"string"  => "Полето :attribute трябва да бъде минимум :min знака.",
	),
	"not_in"         => "Стойността на :attribute е невалидна.",
	"numeric"        => "Полето :attribute трябва да бъде число.",
	"required"       => "Полето :attribute е задължително.",
	"same"           => "Стойностите на :attribute и :other трябва да съвпадат.",
	"size"           => array(
		"numeric" => "Полето :attribute трябва да бъде :size.",
		"file"    => "Полето :attribute трябва да бъде :size килобайта.",
		"string"  => "Полето :attribute трябва да бъде :size знака.",
	),
	"unique"         => "Стойността на :attribute вече съществува.",
	"url"            => "Полето :attribute е с невалиден формат.",

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