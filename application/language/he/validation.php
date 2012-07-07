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
	"active_url"     => "הערך :attribute חייב להכיל כתובת אינטרנט פעילה.",
	"after"          => "הערך :attribute חייב להכיל תאריך אחרי :date.",
	"alpha"          => "הערך :attribute יכול להכיל רק אותיות.",
	"alpha_dash"     => "הערך :attribute יכול להכיל רק אותיות, מספרים ומקפים.",
	"alpha_num"      => "הערך :attribute יכול להכיל רק אותיות ומספרים.",
	"before"         => "הערך :attribute חייב להכיל תאריך לפני :date.",
	"between"        => array(
		"numeric" => "הערך :attribute חייב להיות בין :min ל-:max.",
		"file"    => "הערך :attribute חייב לשקול בין :min ל-:max ק&quot;ב.",
		"string"  => "הערך :attribute חייב להכיל בין :min ל-:max תווים.",
	),
	"confirmed"      => "הערכים של :attribute חייבים להיות זהים.",
	"different"      => "הערכים של :attribute ו-:other חייבים להיות שונים.",
	"email"          => "הערך :attribute חייב להכיל כתובת אימייל תקינה.",
	"exists"         => "הערך :attribute לא קיים.",
	"image"          => "הערך :attribute חייב להיות תמונה.",
	"in"             => "הערך :attribute חייב להיות ברשימה המאשרת.",
	"integer"        => "הערך :attribute חייב להיות מספר שלם.",
	"ip"             => "הערך :attribute חייב להיות כתובת IP תקינה.",
	"match"          => "התבנית של הערך :attribute אינה תקינה.",
	"max"            => array(
		"numeric" => "הערך :attribute חייב להיות פחות מ-:max.",
		"file"    => "הערך :attribute חייב לשקול פחות מ-:max ק&quotב.",
		"string"  => "הערך :attribute חייב להכיל פחות מ-:max תווים.",
	),
	"mimes"          => "הערך :attribute חייב להיות קובץ מסוג: :values.",
	"min"            => array(
		"numeric" => "הערך :attribute חייב להיות לפחות :min.",
		"file"    => "הערך :attribute חייב לשקול לפחות :min ק&quot;ב.",
		"string"  => "הערך :attribute חייב להכיל לפחות :min תווים.",
	),
	"not_in"         => "הערך :attribute נמצא ברשימה השחורה.",
	"numeric"        => "הערך :attribute חייב להיות מספר.",
	"required"       => "חובה למלא את הערך :attribute.",
	"same"           => "הערכים :attribute ו-:other חייבים להיות זהים.",
	"size"           => array(
		"numeric" => "הערך :attribute חייב להיות :size.",
		"file"    => "הערך :attribute חייב לשקול :size ק&quot;ב.",
		"string"  => "הערך :attribute חייב להכיל :size תווים.",
	),
	"unique"         => "הערך :attribute כבר קיים.",
	"url"            => "הערך :attribute חייב להכיל כתובת אינטרנט תקינה.",

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