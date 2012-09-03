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

	"accepted"       => "Isian :attribute harus diterima.",
	"active_url"     => "Isian :attribute bukan URL yang valid.",
	"after"          => "Isian :attribute harus tanggal setelah :date.",
	"alpha"          => "Isian :attribute hanya boleh berisi huruf.",
	"alpha_dash"     => "Isian :attribute hanya boleh berisi huruf, angka, dan strip.",
	"alpha_num"      => "Isian :attribute hanya boleh berisi huruf dan angka.",
	"array"          => "The :attribute must have selected elements.",
	"before"         => "Isian :attribute harus tanggal sebelum :date.",
	"between"        => array(
		"numeric" => "Isian :attribute harus antara :min - :max.",
		"file"    => "Isian :attribute harus antara :min - :max kilobytes.",
		"string"  => "Isian :attribute harus antara  :min - :max karakter.",
	),
	"confirmed"      => "Konfirmasi :attribute tidak cocok.",
	"count"          => "The :attribute must have exactly :count selected elements.",
	"countbetween"   => "The :attribute must have between :min and :max selected elements.",
	"countmax"       => "The :attribute must have less than :max selected elements.",
	"countmin"       => "The :attribute must have at least :min selected elements.",
	"different"      => "Isian :attribute dan :other harus berbeda.",
	"email"          => "Format isian :attribute tidak valid.",
	"exists"         => "Isian :attribute yang dipilih tidak valid.",
	"image"          => ":attribute harus berupa gambar.",
	"in"             => "Isian :attribute yang dipilih tidak valid.",
	"integer"        => "Isian :attribute harus merupakan bilangan.",
	"ip"             => "Isian :attribute harus alamat IP yang valid.",
	"match"          => "Format isian :attribute tidak valid.",
	"max"            => array(
		"numeric" => "Isian :attribute harus kurang dari :max.",
		"file"    => "Isian :attribute harus kurang dari :max kilobytes.",
		"string"  => "Isian :attribute harus kurang dari :max karakter.",
	),
	"mimes"          => "Isian :attribute harus dokumen berjenis : :values.",
	"min"            => array(
		"numeric" => "Isian :attribute harus minimal :min.",
		"file"    => "Isian :attribute harus minimal :min kilobytes.",
		"string"  => "Isian :attribute harus minimal :min karakter.",
	),
	"not_in"         => "Isian :attribute yang dipilih tidak valid.",
	"numeric"        => "Isian :attribute harus berupa angka.",
	"required"       => "Isian :attribute wajib diisi.",
	"same"           => "Isian :attribute dan :other harus sama.",
	"size"           => array(
		"numeric" => "Isian :attribute harus berukuran :size.",
		"file"    => "Isian :attribute harus berukuran :size kilobyte.",
		"string"  => "Isian :attribute harus berukuran :size karakter.",
	),
	"unique"         => "Isian :attribute sudah ada sebelumnya.",
	"url"            => "Format isian :attribute tidak valid.",

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