<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| The following language lines contain the default error messages used by
	| the validator class. Some of these rules have multiple versions such
	| such as the size rules. Feel free to tweak each of these messages.
	|
	*/

	"accepted"         => "القيمة :attribute يجب ان تكون مقبولة.",
	"active_url"       => "القيمة :attribute تمثل عنوان موقع إنترنت غير صحيح.",
	"after"            => "القيمة :attribute يجب أن تكون تاريخ بعد :date.",
	"alpha"            => "القيمة :attribute يجب أن تحتوي أحرف فقط.",
	"alpha_dash"       => "القيمة :attribute يجب أن تحتوي أحرف, أرقام, وشرطات فقط.",
	"alpha_num"        => "القيمة :attribute يجب أن تحتوي أحرف, أرقام فقط.",
	"before"           => "القيمة :attribute يجب أن تكون تاريخ قبل :date.",
	"between"          => array(
		"numeric" => "القيمة :attribute يجب أن تكون بين :min - :max.",
		"file"    => "القيمة :attribute يجب أن يكون حجمها بين :min - :max كيلوبايت.",
		"string"  => "القيمة :attribute يجب أن تكون بين :min - :max أحرف.",
	),
	"confirmed"        => "القيمة :attribute التأكيدية غير مطابقة.",
	"date"             => "القيمة :attribute ليست تاريخ صحيح.",
	"date_format"      => "القيمة :attribute لا تطابق الصيفة :format.",
	"different"        => "القيمتان :attribute و :other يجب أن تختلفان.",
	"digits"           => "القيمة :attribute يجب أن تكون :digits أرقام.",
	"digits_between"   => "القيمة :attribute يجب أن تكون بين :min و :max أرقام.",
	"email"            => "القيمة :attribute تمثل بريد إلكتروني غير صحيح.",
	"exists"           => "القيمة المختارة :attribute غير صحيحة.",
	"image"            => "الملف :attribute يجب أن يكون صورة.",
	"in"               => "القيمة المختارة :attribute غير صحيحة.",
	"integer"          => "القيمة :attribute يجب أن تكون رقمية.",
	"ip"               => "القيمة :attribute يجب أن تمثل عنوان بروتوكول إنترنت صحيح.",
	"max"            => array(
		"numeric" => "القيمة :attribute يجب أن تكون أقل من :max.",
		"file"    => "الملف :attribute يجب أن يكون بحجم أقل من :max كيلوبايت.",
		"string"  => "النص :attribute يجب أن يكون بطول أقل من :max حرف.",
	),
	"mimes"            => "القيمة :attribute يجب أن تكون ملف من نوع: :values.",
	"min"            => array(
		"numeric" => "القيمة :attribute يجب أن تساوي :min على الأقل.",
		"file"    => "الملف :attribute يجب أن يكون بحجم :min كيلوبايت على الأقل.",
		"string"  => "النص :attribute يجب أن يكون بطول :min حرف على الأقل.",
	),
	"not_in"           => "القيمة :attribute المختارة غير صحيحة.",
	"numeric"          => "القيمة :attribute يجب أن تكون رقماً.",
	"regex"            => "صيغة القيمة :attribute غير صحيحة.",
	"required"         => "قيمة الحقل :attribute مطلوبة.",
	"required_if"      => "قيمة الحقل :attribute مطلوبة عندما :other يكون :value.",
	"required_with"    => "قيمة الحقل :attribute عندما يكون :values موجود.",
	"required_without" => "قيمة الحقل :attribute عندما يكون :values غير موجود.",
	"same"             => "القيمتان :attribute و :other يجب أن تتطابق.",
	"size"           => array(
		"numeric" => "القيمة :attribute يجب أن تكون بحجم :size.",
		"file"    => "الملف :attribute يجب أن يكون بحجم :size كيلوبايت.",
		"string"  => "النص :attribute يجب أن يكون بطول :size حرف.",
	),
	"unique"           => "القيمة :attribute تم استخدامها من قبل.",
	"url"              => "التنسيق :attribute غير صحيح.",

	/*
	|--------------------------------------------------------------------------
	| Custom Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| Here you may specify custom validation messages for attributes using the
	| convention "attribute.rule" to name the lines. This makes it quick to
	| specify a specific custom language line for a given attribute rule.
	|
	*/

	'custom' => array(),

	/*
	|--------------------------------------------------------------------------
	| Custom Validation Attributes
	|--------------------------------------------------------------------------
	|
	| The following language lines are used to swap attribute place-holders
	| with something more reader friendly such as E-Mail Address instead
	| of "email". This simply helps us make messages a little cleaner.
	|
	*/

	'attributes' => array(),

);
