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

	"accepted"       => ":attribute kabul edilmek zorunda.",
	"active_url"     => ":attribute geçerli bir URL değil.",
	"after"          => ":attribute , :date tarihinden sonraki bir tarih olmalı.",
	"alpha"          => ":attribute sadece harfleri içermeli.",
	"alpha_dash"     => ":attribute sadece harfleri, rakamları ve çizgi işaretlerini içermeli.",
	"alpha_num"      => ":attribute sadece harf ve rakamları içermeli.",
	"before"         => ":attribute , :date tarihinden önceki bir tarih olmalı.",
	"between"        => array(
		"numeric" => ":attribute , :min - :max sayıları arasında olmalı.",
		"file"    => ":attribute , :min - :max kilobayt arasında olmalı.",
		"string"  => ":attribute , :min - :max karakter arasında olmalı.",
	),
	"confirmed"      => ":attribute karşılaştırması uyuşmuyor.",
	"different"      => ":attribute ve :other birbirinden farklı olmalı.",
	"email"          => ":attribute geçerli bir e-posta adresi değil.",
	"exists"         => ":attribute geçerli değil.",
	"image"          => ":attribute bir imaj olmalı.",
	"in"             => "Seçilen :attribute geçerli değil.",
	"integer"        => ":attribute bir sayı olmalı.",
	"ip"             => ":attribute geçerli bir IP adresi olmalı.",
	"match"          => ":attribute biçimi uygun değil.",
	"max"            => array(
		"numeric" => ":attribute , :max sayısından küçük olmalı.",
		"file"    => ":attribute dosyası :max kilobayttan küçük olmalı.",
		"string"  => ":attribute , :max karakterden az olmalı.",
	),
	"mimes"          => ":attribute , :values cinsinden bir dosya olmalı.",
	"min"            => array(
		"numeric" => ":attribute en az :min olmalı.",
		"file"    => ":attribute en az :min kilobayt olmalı.",
		"string"  => ":attribute en az :min karakter olmalı.",
	),
	"not_in"         => ":attribute geçerli değil.",
	"numeric"        => ":attribute bir sayı olmalı.",
	"required"       => ":attribute zorunlu bir alan.",
	"same"           => ":attribute ve :other uyuşmuyor.",
	"size"           => array(
		"numeric" => ":attribute must be :size.",
		"file"    => ":attribute must be :size kilobyte.",
		"string"  => ":attribute must be :size characters.",
	),
	"unique"         => ":attribute daha önce alınmış.",
	"url"            => ":attribute geçerli değil.",

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