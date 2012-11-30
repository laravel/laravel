<?php 

/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Language
 * @version  3.2.3
 * @author   Sinan Eldem <sinan@sinaneldem.com.tr>
 * @link     http://sinaneldem.com.tr
 */

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

	"accepted"       => ":attribute kabul edilmelidir.",
	"active_url"     => ":attribute geçerli bir URL olmalıdır.",
	"after"          => ":attribute şundan daha eski bir tarih olmalıdır :date.",
	"alpha"          => ":attribute sadece harflerden oluşmalıdır.",
	"alpha_dash"     => ":attribute sadece harfler, rakamlar ve tirelerden oluşmalıdır.",
	"alpha_num"      => ":attribute sadece harfler ve rakamlar içermelidir.",
	"before"         => ":attribute şundan daha önceki bir tarih olmalıdır :date.",
	"between"        => array(
		"numeric" => ":attribute :min - :max arasında olmalıdır.",
		"file"    => ":attribute :min - :max arasındaki kilobyte değeri olmalıdır.",
		"string"  => ":attribute :min - :max arasında karakterden oluşmalıdır.",
	),
	"confirmed"      => ":attribute onayı eşleşmiyor.",
	"different"      => ":attribute ile :other birbirinden farklı olmalıdır.",
	"email"          => ":attribute biçimi geçersiz.",
	"exists"         => "Seçili :attribute geçersiz.",
	"image"          => ":attribute resim dosyası olmalıdır.",
	"in"             => "selected :attribute geçersiz.",
	"integer"        => ":attribute rakam olmalıdır.",
	"ip"             => ":attribute geçerli bir IP adresi olmalıdır.",
	"match"          => ":attribute biçimi geçersiz.",
	"max"            => array(
		"numeric" => ":attribute şundan küçük olmalıdır :max.",
		"file"    => ":attribute şundan küçük olmalıdır :max kilobyte.",
		"string"  => ":attribute şundan küçük olmalıdır :max karakter.",
	),
	"mimes"          => ":attribute dosya biçimi :values olmalıdır.",
	"min"            => array(
		"numeric" => ":attribute en az :min olmalıdır.",
		"file"    => ":attribute en az :min kilobyte olmalıdır.",
		"string"  => ":attribute en az :min karakter olmalıdır.",
	),
	"not_in"         => "Seçili :attribute geçersiz.",
	"numeric"        => ":attribute rakam olmalıdır.",
	"required"       => ":attribute alanı gereklidir.",
	"same"           => ":attribute ile :other eşleşmelidir.",
	"size"           => array(
		"numeric" => ":attribute :size olmalıdır.",
		"file"    => ":attribute :size kilobyte olmalıdır.",
		"string"  => ":attribute :size karakter olmalıdır.",
	),
	"unique"         => ":attribute daha önceden kayıt edilmiş.",
	"url"            => ":attribute biçimi geçersiz.",

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