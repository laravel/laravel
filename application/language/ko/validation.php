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

	"accepted"       => ":attribute 가 모두 유효해야 합니다.",
	"active_url"     => ":attribute 는 유효한 URL이 아닙니다.",
	"after"          => ":attribute 는 :date 이후 날짜만 가능합니다.",
	"alpha"          => ":attribute 는 오직 문자만 가능합니다.",
	"alpha_dash"     => ":attribute 는 오직 문자, 숫자, 대쉬(-)만 가능합니다.",
	"alpha_num"      => ":attribute 는 오직 문자와 숫자만 포함해야 합니다.",
	"array"          => ":attribute 는 선택된 항목이 있어야 합니다.",
	"before"         => ":attribute 는 :date 이전 날짜만 가능합니다.",
	"between"        => array(
		"numeric" => ":attribute 는 :min - :max 사이에 있어야 합니다.",
		"file"    => ":attribute 는 :min - :max 킬로바이트 사이에 있어야 합니다.",
		"string"  => ":attribute 는 :min - :max 문자 사이에 있어야 합니다.",
	),
	"confirmed"      => ":attribute 은 항목이 일치하지 않습니다.",
	"count"          => ":attribute 는 정확히 :count 만큼 선택해야 합니다.",
	"countbetween"   => ":attribute :min 과 :max 사이만큼 선택해야 합니다.",
	"countmax"       => ":attribute 는 :max 보다 덜 선택해야 합니다.",
	"countmin"       => ":attribute 는 :min 보다 많이 선택해야 합니다.",
	"different"      => ":attribute 과 :other 는 달라야 합니다.",
	"email"          => ":attribute 포맷이 유효하지 않습니다.",
	"exists"         => "선택된 :attribute 가 유효하지 않습니다.",
	"image"          => ":attribute 는 이미지이어야 합니다.",
	"in"             => "선택된 :attribute 가 유효하지 않습니다.",
	"integer"        => ":attribute 는 숫자(정수)이어야 합니다.",
	"ip"             => ":attribute 는 유효하지 않는 IP address 입니다.",
	"match"          => ":attribute 는 유효하지 않은 포맷입니다.",
	"max"            => array(
		"numeric" => ":attribute 는 :max 보다 작아야 합니다.",
		"file"    => ":attribute 는 :max 킬로바이트보다 작아야 합니다.",
		"string"  => ":attribute 는 :max 문자수보다 작아야 합니다.",
	),
	"mimes"          => ":attribute type: :values 파일이어야 합니다.",
	"min"            => array(
		"numeric" => ":attribute 는 최소한 :min 이상 이어야 합니다.",
		"file"    => ":attribute 는 최소한 :min 킬로바이트 이상 이어야 합니다.",
		"string"  => ":attribute 는 최소한 :min 문자수 이상 이어야 합니다",
	),
	"not_in"         => "선택된 :attribute 가 유효하지 않습니다.",
	"numeric"        => ":attribute 는 숫자만 가능 합니다.",
	"required"       => ":attribute 는 반드시 입력해야 합니다.",
	"same"           => ":attribute 과 :other 는 반드시 일치해야 합니다.",
	"size"           => array(
		"numeric" => ":attribute 는 :size 이어야 합니다.",
		"file"    => ":attribute 는 :size 킬로바이트 이어야 합니다..",
		"string"  => ":attribute 는 :size 문자수 이어야 합니다.",
	),
	"unique"         => ":attribute 는 이미 입려되었습니다.",
	"url"            => ":attribute 는 유효하지 않은 포맷입니다.",

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