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
	| Simplified Chinese translation.
	| 简体中文版译文由 Laravel中文网(http://www.golaravel.com) 维护。
	|
	*/

	"accepted"       => "必须接受 :attribute 。",
	"active_url"     => ":attribute 不是有效的URL。",
	"after"          => ":attribute 必须是有效的日期值，且在 :date 之后。",
	"alpha"          => ":attribute 只能包含英文字符。",
	"alpha_dash"     => ":attribute 只能包含英文字母、数字或下划线。",
	"alpha_num"      => ":attribute 只能包含英文字母和数字。",
	"array"          => ":attribute 必须是数组内的某元素。",
	"before"         => ":attribute 必须是有效的日期值，且在 :date 之后。",
	"between"        => array(
		"numeric" => ":attribute 必须介于 :min - :max 之间。",
		"file"    => ":attribute 必须介于 :min - :max K字节之间。",
		"string"  => ":attribute 必须介于 :min - :max 个字符之间。",
	),
	"confirmed"      => ":attribute 确认信息不匹配。",
	"count"          => ":attribute 必须选择 :count 个元素。",
	"countbetween"   => ":attribute 选择的元素必须介于 :min 到 :max 个之间。",
	"countmax"       => ":attribute 必须选择少于 :max 个元素。",
	"countmin"       => ":attribute 必须选择至少 :min 个元素。",
	"different"      => ":attribute 和 :other 必须不同。",
	"email"          => ":attribute 格式不正确。",
	"exists"         => "你所选择的 :attribute 是非法的。",
	"image"          => ":attribute 必须是图像格式。",
	"in"             => "你所选择的 :attribute 是非法的。",
	"integer"        => ":attribute 必须是整数类型。",
	"ip"             => ":attribute 必须是合格的IP地址。",
	"match"          => ":attribute 格式不正确。",
	"max"            => array(
		"numeric" => ":attribute 必须小于 :max 。",
		"file"    => ":attribute 必须小于 :max K字节。",
		"string"  => ":attribute 必须小于 :max 个字符。",
	),
	"mimes"          => ":attribute 必须是（ :values）中的一种类型。",
	"min"            => array(
		"numeric" => ":attribute 必须至少是 :min 。",
		"file"    => ":attribute 必须至少是 :min K字节。",
		"string"  => ":attribute 必须至少是 :min 个字符。",
	),
	"not_in"         => ":attribute 的值不正确",
	"numeric"        => ":attribute 必须是数字类型。",
	"required"       => ":attribute 字段必须填写。",
	"same"           => ":attribute 和 :other 必须匹配。",
	"size"           => array(
		"numeric" => ":attribute 必须是 :size 。",
		"file"    => ":attribute 必须是 :size K字节。",
		"string"  => ":attribute 必须是 :size 个字符。",
	),
	"unique"         => ":attribute 已经存在。",
	"url"            => ":attribute 格式不正确。",

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