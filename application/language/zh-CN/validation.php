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

	"accepted"       => ":attribute 必须接受。",
	"active_url"     => ":attribute 不是一个有效的URL。",
	"after"          => ":attribute 必须是一个在 :date 之后的日期。",
	"alpha"          => ":attribute 只能由字母组成。",
	"alpha_dash"     => ":attribute 只能由字母、数字和斜杠组成。",
	"alpha_num"      => ":attribute 只能由字母和数字组成。",
	"array"          => ":attribute 必须有已选元素。",
	"before"         => ":attribute 必须是一个在 :date 之前的日期。",
	"between"        => array(
		"numeric" => ":attribute 必须介于 :min - :max 之间。",
		"file"    => ":attribute 必须介于 :min - :max 千字节之间。",
		"string"  => ":attribute 必须介于 :min - :max 个字符之间。",
	),
	"confirmed"      => ":attribute 确认不匹配。",
	"count"          => ":attribute 必须等于 :count 个已选元素。",
	"countbetween"   => ":attribute 必须介于 :min 和 :max 个已选元素之间。",
	"countmax"       => ":attribute 必须小于 :max 个已选元素。",
	"countmin"       => ":attribute 必须大于 :min 个已选元素。",
	"date_format"	 => ":attribute 必须是一个有效的日期格式。",
	"different"      => ":attribute 和 :other 必须不同。",
	"email"          => ":attribute 格式非法。",
	"exists"         => "已选的属性 :attribute 非法。",
	"image"          => ":attribute 必须是一张图片。",
	"in"             => "已选的属性 :attribute 非法。",
	"integer"        => ":attribute 必须是一个整数。",
	"ip"             => ":attribute 必须是一个有效的IP地址。",
	"match"          => ":attribute 格式非法。",
	"max"            => array(
		"numeric" => ":attribute 必须小于 :max 。",
		"file"    => ":attribute 必须小于 :max 千字节。",
		"string"  => ":attribute 必须小于 :max 个字符。",
	),
	"mimes"          => ":attribute 必须是一个 :values 类型的文件。",
	"min"            => array(
		"numeric" => ":attribute 必须大于 :min 。",
		"file"    => ":attribute 必须大于 :min 千字节。",
		"string"  => ":attribute 必须大于 :min 个字符。",
	),
	"not_in"         => "已选的属性 :attribute 非法。",
	"numeric"        => ":attribute 必须是一个数字。",
	"required"       => ":attribute 字段必填。",
    "required_with"  => ":attribute 属性需要填写 :field",
	"same"           => ":attribute 和 :other 不匹配。",
	"size"           => array(
		"numeric" => ":attribute 必须是 :size 。",
		"file"    => ":attribute 必须是 :size 千字节。",
		"string"  => ":attribute 必须是 :size 个字符。",
	),
	"unique"         => ":attribute 已经有人使用。",
	"url"            => ":attribute 格式非法。",

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
