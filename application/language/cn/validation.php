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

	"accepted"       => "您必须同意 :attribute 才能继续.",
	"active_url"     => "链接 :attribute 不合法.",
	"after"          => " :attribute 时间必须晚于 :date.",
	"alpha"          => "字段 :attribute 只能包含字母.",
	"alpha_dash"     => "字段 :attribute 只能包含 字母, 数字, 中横线.",
	"alpha_num"      => "字段 :attribute 只能包含 字母, 数字.",
	"array"          => "字段 :attribute 必须选择.",
	"before"         => " :attribute 日期必须早于 :date.",
	"between"        => array(
		"numeric" => "数字 :attribute 必须大于 :min 且小于 :max.",
		"file"    => "文件 :attribute 大小必须大于 :min 且小于 :max KB.",
		"string"  => "字段 :attribute 长度必须大于 :min 且小于 :max .",
	),
	"confirmed"      => " :attribute 确认项不匹配.",
	"count"          => " :attribute 必须只能包含 :count 项.",
	"countbetween"   => " :attribute 至少要有 :min 项，最多 :max 项.",
	"countmax"       => " :attribute 不能超过 :max 项.",
	"countmin"       => " :attribute 至少要选择 :min 项.",
	"date_format"	 => " :attribute 不是合法的日期格式.",
	"different"      => " :attribute 和 :other 不能一样.",
	"email"          => " :attribute 不是合法的邮箱地址.",
	"exists"         => " :attribute 不合法或不存在.",
	"image"          => " :attribute 必须为图片类型.",
	"in"             => " 选择的 :attribute 不正确.",
	"integer"        => " :attribute 必须为整数.",
	"ip"             => " :attribute 必须为合法的IP地址.",
	"match"          => " :attribute 格式不正确.",
	"max"            => array(
		"numeric" => "数字 :attribute 不能大于 :max.",
		"file"    => "文件 :attribute 不能超过 :max KB.",
		"string"  => "字段 :attribute 不能超过 :max 字符.",
	),
	"mimes"          => "文件 :attribute 只能为类型: :values.",
	"min"            => array(
		"numeric" => "数字 :attribute 不能小于 :min.",
		"file"    => "文件 :attribute 不能小于 :min KB.",
		"string"  => "字段 :attribute 不能少于 :min 字符.",
	),
	"not_in"         => "选择的 :attribute 不正确.",
	"numeric"        => " :attribute 必须的是数字.",
	"required"       => " :attribute 不能为空.",
    "required_with"  => "字段 :attribute field is required with :field",
	"same"           => "The :attribute and :other must match.",
	"size"           => array(
		"numeric" => "数字 :attribute 只能是 :size.",
		"file"    => "文件 :attribute 只能是 :size KB.",
		"string"  => "字段 :attribute 只能包含 :size 字符.",
	),
	"unique"         => ":attribute 已经被占用.",
	"url"            => ":attribute 不合法.",

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
