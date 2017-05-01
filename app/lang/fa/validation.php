<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| The following language lines contain the default error messages used by
	| the validator class. Some of these rules have multiple versions such
	| as the size rules. Feel free to tweak each of these messages here.
	|
	*/

	"accepted"         => ":attribute باید پذیرفته شود.",
	"active_url"       => "آدرس :attribute معتبر نیست",
	"after"            => ":attribute باید تاریخی بعد از :date باشد.",
	"alpha"            => ":attribute باید شامل حروف الفبا باشد.",
	"alpha_dash"       => ":attribute باید شامل حروف الفبا و عدد و خظ تیره (-) باشد.",
	"alpha_num"        => ":attribute باید شامل حروف الفبا و عدد باشد.",
	"array"            => ":attribute باید شامل حروف آرایه باشد.",
	"before"           => ":attribute باید تاریخی قبل از :date باشد.",
	"between"          => array(
		"numeric" => ":attribute باید بین :min و :max باشد.",
		"file"    => ":attribute باید بین :min و :max کیلوبایت باشد.",
		"string"  => ":attribute باید بین :min و :max کاراکتر باشد.",
		"array"   => ":attribute باید بین :min و :max آیتم باشد.",
	),
	"confirmed"        => ":attribute مطابقت ندارد.",
	"date"             => ":attribute یک تاریخ معتبر نیست.",
	"date_format"      => ":attribute با الگوی :format مطاقبت ندارد.",
	"different"        => ":attribute و :other باید متفاوت باشند.",
	"digits"           => ":attribute باید :digits رقم باشد.",
	"digits_between"   => ":attribute باید بین :min و :max رقم باشد.",
	"email"            => "فرمت :attribute معتبر نیست.",
	"exists"           => ":attribute انتخاب شده، معتبر نیست.",
	"image"            => ":attribute باید تصویر باشد.",
	"in"               => ":attribute انتخاب شده، معتبر نیست.",
	"integer"          => ":attribute باید نوع داده ای عددی (integer) باشد.",
	"ip"               => ":attribute باید IP آدرس معتبر باشد.",
	"max"              => array(
		"numeric" => ":attribute نباید بزرگتر از :max باشد.",
		"file"    => ":attribute نباید بزرگتر از :max کیلوبایت باشد.",
		"string"  => ":attribute نباید بیشتر از max کاراکتر باشد.",
		"array"   => ":attribute نباید بیشتر از :max آیتم باشد.",
	),
	"mimes"            => ":attribute باید یکی از فرمت های :values باشد.",
	"min"              => array(
		"numeric" => ":attribute نباید کوچکتر از :max باشد.",
		"file"    => ":attribute نباید کوچکتر از :max کیلوبایت باشد.",
		"string"  => ":attribute نباید کمتر از max کاراکتر باشد.",
		"array"   => ":attribute نباید کمتر از :max آیتم باشد.",
	),
	"not_in"           => ":attribute انتخاب شده، معتبر نیست.",
	"numeric"          => ":attribute باید شامل عدد باشد.",
	"regex"            => ":attribute یک فرمت معتبر نیست",
	"required"         => "فیلد :attribute الزامی است",
	"required_if"      => "فیلد :attribute هنگامی که :other برابر با :value است، الزامیست.",
	"required_with"    => "فیلد :attribute هنگامی که :values دارای مقدار باشد، الزامیست.",
	"required_without" => "فیلد :attribute هنگامی که :values دارای مقدار نباشد، الزامیست",
	"same"             => ":attribute و :other باید مانند هم باشند.",
	"size"             => array(
		"numeric" => ":attribute باید برابر با :size باشد.",
		"file"    => ":attribute باید برابر با :size کیلوبایت باشد.",
		"string"  => ":attribute باید برابر با :size کاراکتر باشد.",
		"array"   => ":attribute باسد شامل :size آیتم باشد.",
	),
	"unique"           => ":attribute قبلا انتخاب شده است.",
	"url"              => "فرمت آدرس :attribute اشتباه است.",

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
