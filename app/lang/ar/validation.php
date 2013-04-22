<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| The following language lines contain the default error messages used by
	| the validator class. Some of these rules have multiple versions
	| such as the size rules. Feel free to tweak each of these messages.
	|
	*/

	"accepted"         => "يجب الموافقة على  :attribute .",
	"active_url"       => ":attribute ليس رابط صحيح.",
	"after"            => ":attribute يجب أن يكون تاريخ بعد :date.",
	"alpha"            => ":attribute يجب أن تحتوي على أحرف فقط.",
	"alpha_dash"       => ":attribute يجب أن تحتوي على أحرف وأرقام وشرطات.",
	"alpha_num"        => ":attribute يجب أن تحتوي على أحرف وأرقام فقط.",
	"before"           => ":attribute يجب أن يكون تاريخ قبل  :date.",
	"between"          => array(
		"numeric" => ":attribute يجب أن يكون بين  :min - :max.",
		"file"    => ":attribute يجب أن يكون الحجم بين :min - :max kilobytes.",
		"string"  => ":attribute يجب أن يكون عدد الأحرف بين :min - :max حرف.",
	),
	"confirmed"        => ":attribute تأكيد غير مطابق.",
	"date"             => ":attribute ليس تاريخ صحيح.",
	"date_format"      => ":attribute ليس مطابق للصيغة  :format.",
	"different"        => ":attribute و  :other يجب أن يكون مختلف.",
	"digits"           => ":attribute يجب أن يكون  :digits أرقام.",
	"digits_between"   => ":attribute يجب أن يكون بين  :min و  :max أرقام .",
	"email"            => "صيغة  :attribute غير صحيحة.",
	"exists"           => "قيمة  :attribute المختارة غير صحيحة أو موجودة من قبل.",
	"image"            => ":attribute يجب أن تكون صورة.",
	"in"               => "قيمة  :attribute المختارة غير صحيحة.",
	"integer"          => ":attribute يجب أن يكون عدد صحيح.",
	"ip"               => ":attribute يجب أن يكون عنوان إنترنت )IP )  صحيح.",
	"max"              => array(
		"numeric" => ":attribute يجب أن لا يزيد عن :max.",
		"file"    => ":attribute يجب أن لا يزيد حجمه عن  :max كيلوبايت.",
		"string"  => ":attribute يجب أن لا يزيد طوله عن  :max أحرف.",
	),
	"mimes"            => ":attribute يجب أن يكون ملف من نوع: :values.",
	"min"              => array(
		"numeric" => ":attribute يجب أن لا يقل عن  :min.",
		"file"    => ":attribute يجب أن لا يقل حجمه عن  :min كيلوبايت.",
		"string"  => ":attribute يجب أن لا يقل طوله عن  :min حرف.",
	),
	"not_in"           => "قيمة :attribute المختارة غير صحيحة.",
	"numeric"          => ":attribute يجب أن يكون رقم.",
	"regex"            => "صيغة  :attribute غير صحيحة.",
	"required"         => ":attribute حقل إلزامي.",
	"required_with"    => ":attribute حقل إلزامي عند وجود  :values .",
	"required_without" => ":attribute حقل إلزامي عند عدم وجود  :values .",
	"same"             => "يجب تطابق  :attribute و  :other.",
	"size"             => array(
		"numeric" => ":attribute يجب أن يكون  :size.",
		"file"    => ":attribute يجب أن يكون حجمه  :size كيلوبايت.",
		"string"  => ":attribute يجب أن يكون طوله  :size حرف.",
	),
	"unique"           => ":attribute تم اختياره مسبقاً.",
	"url"              => "صيغة  :attribute غير صحيحة.",

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
