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

	"accepted"         => ":attribute phải được chấp nhận.",
	"active_url"       => ":attribute không phải là một URL hợp lệ.",
	"after"            => ":attribute phải sau ngày :date.",
	"alpha"            => ":attribute chỉ có thể chứa ký tự chữ",
	"alpha_dash"       => ":attribute chỉ có thể chứa ký tự chữ, số và dấu gạch ngang (-)",
	"alpha_num"        => ":attribute chỉ có thể chứa ký tự chữ và số",
	"before"           => ":attribute phải trước ngày :date.",
	"between"          => array(
		"numeric" => ":attribute phải có giá trị trong khoản :min - :max.",
		"file"    => ":attribute phải có kích thước trong khoản :min - :max kilobytes.",
		"string"  => ":attribute phải có từ :min đến :max ký tự.",
	),
	"confirmed"        => "Giá trị xác nhận :attribute không trùng khớp.",
	"date"             => ":attribute không phải là một ngày hợp lệ.",
	"date_format"      => ":attribute không phù hợp với định dạng :format.",
	"different"        => ":attribute và :other phải khác nhau.",
	"digits"           => ":attribute phải có :digits chữ số.",
	"digits_between"   => ":attribute phải nằm trong khoản :min và :max chữ số.",
	"email"            => "Định dạng :attribute không hợp lệ.",
	"exists"           => ":attribute đã chọn không hợp lệ.",
	"image"            => ":attribute phải là một tập tin ảnh.",
	"in"               => ":attribute đã chọn không hợp lệ.",
	"integer"          => ":attribute phải là một số nguyên.",
	"ip"               => ":attribute phải là một địa chỉ IP hợp lệ.",
	"max"              => array(
		"numeric" => ":attribute không được lớn hơn :max.",
		"file"    => ":attribute không được lớn hơn :max kilobytes.",
		"string"  => ":attribute không được dài hơn :max ký tự.",
	),
	"mimes"            => ":attribute phải là một tập tin có định dạng: :values.",
	"min"              => array(
		"numeric" => ":attribute nhỏ nhất là :min.",
		"file"    => ":attribute nhỏ nhất là :min kilobytes.",
		"string"  => ":attribute ngắn nhất là :min ký tự.",
	),
	"not_in"           => "Giá trị :attribute đã chọn không hợp lệ.",
	"numeric"          => ":attribute phải là một giá trị số.",
	"regex"            => ":attribute không hợp lệ.",
	"required"         => ":attribute bắt buộc phải có giá trị.",
	"required_with"    => ":attribute bắt buộc phải nhập khi :values có giá trị.",
	"required_without" => ":attribute bắt buộc phải nhập khi :values không có giá trị.",
	"same"             => ":attribute và :other phải có giá trị giống nhau.",
	"size"             => array(
		"numeric" => ":attribute phải bằng :size.",
		"file"    => ":attribute phải bằng :size kilobytes.",
		"string"  => ":attribute phải dài :size ký tự.",
	),
	"unique"           => ":attribute đã bị chọn.",
	"url"              => ":attribute không hợp lệ.",

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
