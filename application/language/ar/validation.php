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

	"accepted"       => "القيمة :attribute يجب أن تكون مقبولة.",
	"active_url"     => "القيمة :attribute تمثل عنوان موقع إنترنت غير صحيح.",
	"after"          => "القيمة :attribute يجب أن تكون بعد تاريخ :date.",
	"alpha"          => "القيمة :attribute يمكنها أن تحتوي على أحرف فقط.",
	"alpha_dash"     => "القيمة :attribute يمكنها أن تحتوي على أحرف و أرقام و إشارة الناقص فقط.",
	"alpha_num"      => "القيمة :attribute يمكنها أن تحتوي على أحرف و أرقام فقط.",
	"array"          => "The :attribute must have selected elements.",
	"before"         => "القيمة :attribute يجب أن تكون قبل تاريخ :date.",
	"between"        => array(
		"numeric" => "القيمة :attribute يجب أن تكون بين :min و :max.",
		"file"    => "الملف :attribute يجب أن يكون بحجم من :min إلى :max كيلوبايت.",
		"string"  => "النص :attribute يجب أن يكون بطول من :min إلى :max حرف.",
	),
	"confirmed"      => "القيمة :attribute التأكيدية غير مطابقة.",
	"count"          => "The :attribute must have exactly :count selected elements.",
	"countbetween"   => "The :attribute must have between :min and :max selected elements.",
	"countmax"       => "The :attribute must have less than :max selected elements.",
	"countmin"       => "The :attribute must have at least :min selected elements.",
	"different"      => "القيمتان :attribute و :other يجب أن تختلفان.",
	"email"          => "القيمة :attribute تمثل بريد إلكتروني غير صحيح.",
	"exists"         => "القيمة المختارة :attribute غير موجودة.",
	"image"          => "القيمة :attribute يجب أن تكون صورة.",
	"in"             => "القيمة المختارة :attribute غير موجودة.",
	"integer"        => "القيمة :attribute يجب أن تكون رقماً.",
	"ip"             => "القيمة :attribute يجب أن تمثل عنوان بروتوكول إنترنت صحيح.",
	"match"          => "القيمة :attribute هي بتنسيق غير صحيح.",
	"max"            => array(
		"numeric" => "القيمة :attribute يجب أن تكون أقل من :max.",
		"file"    => "الملف :attribute يجب أن يكون بحجم أقل من :max كيلوبايت.",
		"string"  => "النص :attribute يجب أن يكون بطول أقل من :max حرف.",
	),
	"mimes"          => "القيمة :attribute يجب أن تكون ملف من نوع: :values.",
	"min"            => array(
		"numeric" => "القيمة :attribute يجب أن تساوي :min على الأقل.",
		"file"    => "الملف :attribute يجب أن يكون بحجم :min كيلوبايت على الأقل.",
		"string"  => "النص :attribute يجب أن يكون بطول :min حرف على الأقل.",
	),
	"not_in"         => "القيمة :attribute المختارة غير صحيحة.",
	"numeric"        => "القيمة :attribute يجب أن تكون رقماً.",
	"required"       => "القيمة :attribute مطلوبة.",
	"same"           => "القيمتان :attribute و :other يجب أن تتطابقان.",
	"size"           => array(
		"numeric" => "القيمة :attribute يجب أن تكون بحجم :size.",
		"file"    => "الملف :attribute يجب أن يكون بحجم :size كيلوبايت.",
		"string"  => "النص :attribute يجب أن يكون بطول :size حرف.",
	),
	"unique"         => "القيمة :attribute تم استخدامها من قبل.",
	"url"            => "التنسيق :attribute غير صحيح.",

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