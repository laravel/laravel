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

	"accepted"       => "Bahagian :attribute perlu diterima terlebih dahulu.",
	"active_url"     => "Bahagian :attribute mengandungi URL yang tidak sah.",
	"after"          => "Bahagian :attribute perlu mengandungi tarikh selepas :date.",
	"alpha"          => "Bahagian :attribute hanya boleh mengandungi huruf.",
	"alpha_dash"     => "Bahagian :attribute hanya boleh mengandungi huruf, nombor, dan sengkang.",
	"alpha_num"      => "Bahagian :attribute hanya boleh mengandungi huruf dan nombor.",
	"array"          => "Bahagian :attribute perlu mengandungi elemen yang dipilih.",
	"before"         => "Bahagian :attribute perlu mengandungi tarikh sebelum :date.",
	"between"        => array(
		"numeric" => "Bahagian :attribute perlu mengandungi nilai di antara :min - :max.",
		"file"    => "Bahagian :attribute perlu mengandungi saiz di antara :min - :max kilobytes.",
		"string"  => "Bahagian :attribute perlu mengandungi huruf di antara :min - :max.",
	),
	"confirmed"      => "Bahagian :attribute mengandungi pengesahan yang tidak sepadan.",
	"count"          => "Bahagian :attribute perlu mengandungi :count elemen yang dipilih.",
	"countbetween"   => "Bahagian :attribute perlu mengandungi di antara :min and :max elemen yang dipilih.",
	"countmax"       => "Bahagian :attribute perlu mengandungi kurang daripada :max elemen yang dipilih.",
	"countmin"       => "Bahagian :attribute perlu mengandungi sekurang-kurangnya :min elemen yang dipilih.",
	"different"      => "Bahagian :attribute dan :other perlu mengandungi nilai yang berbeza.",
	"email"          => "Bahagian :attribute mengandungi format yang tidak sah.",
	"exists"         => "Bahagian :attribute yang dipilih adalah tidak sah.",
	"image"          => "Bahagian :attribute perlu adalah sebuah gambar.",
	"in"             => "Bahagian :attribute yang dipilih adalah tidak sah.",
	"integer"        => "Bahagian :attribute perlu adalah sebuah nombor.",
	"ip"             => "Bahagian :attribute perlu mengandungi IP yang sah.",
	"match"          => "Bahagian :attribute mengandungi format yang tidak sah.",
	"max"            => array(
		"numeric" => "Bahagian :attribute perlu mengandungi nilai kurang daripada :max.",
		"file"    => "Bahagian :attribute perlu mengandungi saiz kurang daripada :max kilobytes.",
		"string"  => "Bahagian :attribute perlu mengandungi huruf kurang daripada :max.",
	),
	"mimes"          => "Bahagian :attribute perlu adalah sebuah file dari jenis: :values.",
	"min"            => array(
		"numeric" => "Bahagian :attribute perlu mengandungi nilai sekurang-kurangnya :min.",
		"file"    => "Bahagian :attribute perlu mengandungi saiz sekurang-kurangnya :min kilobytes.",
		"string"  => "Bahagian :attribute perlu mengadungi huruf sekurang-kurangnya :min.",
	),
	"not_in"         => "Bahagian :attribute yang dipilih adalah tidak sah.",
	"numeric"        => "Bahagian :attribute perlu adalah sebuah nombor.",
	"required"       => "Bahagian :attribute perlu diisi.",
	"same"           => "Bahagian :attribute dan :other perlu sama.",
	"size"           => array(
		"numeric" => "Bahagian :attribute perlu mengandungi nilai sebanyak :size.",
		"file"    => "Bahagian :attribute perlu mengandungi saiz sebanyak :size kilobyte.",
		"string"  => "Bahagian :attribute perlu mengandungi huruf sebanyak :size.",
	),
	"unique"         => "Bahagian :attribute mempunyai nilai yang sudah digunakan.",
	"url"            => "Bahagian :attribute mengandungi format yang tidak sah.",

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