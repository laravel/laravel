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
	| Hebrew Translation:
	| Nir Lahad - www.nirlah.com
	|
	*/

	"accepted"       => "חובה להסכים ל-:attribute.",
	"active_url"     => ":attribute אינה כתובת אינטרנט פעילה.",
	"after"          => ":attribute חייב להיות תאריך אחרי :date.",
	"alpha"          => ":attribute יכול להכיל רק אותיות.",
	"alpha_dash"     => ":attribute יכול להיות רק אותיות, מספרים ומקפים.",
	"alpha_num"      => ":attribute יכול להכיל רק אותיות ומספרים.",
	"before"         => ":attribute חייב להיות תאריך אחרי :date.",
	"between"        => array(
		"numeric" => ":attribute חייב להיות בין :min ל-:max.",
		"file"    => ":attribute חייב לשקול בין :min ל-:max ק&quot;ב.",
		"string"  => ":attribute חייב להכיל בין :min ל-:max תווים.",
	),
	"confirmed"      => "אישור ה-:attribute אינו תואם למקור.",
	"different"      => ":attribute ו-:other חייבים להיות שונים.",
	"email"          => ":attribute אינה כתובת דוא&quot;ל תקינה.",
	"exists"         => "הערך של :attribute לא קיים.",
	"image"          => ":attribute חייבת להיות תמונה.",
	"in"             => ":attribute אינו נמצא ברשימה המאשרת.",
	"integer"        => ":attribute חייב להיות מספר שלם.",
	"ip"             => ":attribute חייבת להיות כתובת IP תקינה.",
	"match"          => "התבנית של :attribute אינה תקינה.",
	"max"            => array(
		"numeric" => ":attribute חייב להיות פחות מ-:max.",
		"file"    => ":attribute חייב לשקול פחות מ-:max ק&quotב.",
		"string"  => ":attribute חייב להכיל פחות מ-:max תווים.",
	),
	"mimes"          => ":attribute חייב להיות קובץ מסוג: :values.",
	"min"            => array(
		"numeric" => ":attribute חייב להיות לפחות :min.",
		"file"    => ":attribute חייב לשקול לפחות :min ק&quot;ב.",
		"string"  => ":attribute חייב להכיל לפחות :min תווים.",
	),
	"not_in"         => ":attribute נמצא ברשימה השחורה.",
	"numeric"        => ":attribute חייב להיות מספר.",
	"required"       => "חובה למלא :attribute.",
	"same"           => ":attribute ו-:other חייבים להתאים.",
	"size"           => array(
		"numeric" => ":attribute חייב להיות :size.",
		"file"    => ":attribute חייב לשקול :size ק&quot;ב.",
		"string"  => ":attribute חייב להכיל :size תווים.",
	),
	"unique"         => ":attribute כבר קיים.",
	"url"            => ":attribute אינה כתובת אינטרנט תקנית.",

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