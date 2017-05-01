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

	"accepted"       => ":attribute بايد مورد قبول واقع شود.",
	"active_url"     => ":attribute يک آدرس صحيح نمي باشد.",
	"after"          => ":attribute بايد يک روز بعد از :date باشد.",
	"alpha"          => ":attribute بايد فقط شامل حروف باشد.",
	"alpha_dash"     => ":attribute ميتواند شامل حروف ، اعداد و خط تيره باشد.",
	"alpha_num"      => ":attribute ميتواند شامل حروف و اعداد باشد.",
	"array"          => ":attribute فقط ميتواند شامل موارد داده شده باشد.",
	"before"         => ":attribute بايد روزي قبل از :date باشد.",
	"between"        => array(
		"numeric" => ":attribute بايد مابين :min و :max باشد.",
		"file"    => "اندازه :attribute بايد مابين :min و :max کيلو بايت باشد.",
		"string"  => "طول :attribute بايد مابين :min و :max حرف باشد.",
	),
	"confirmed"      => "تاييد :attribute با :attribute يکي نيست.",
	"count"          => ":attribute بايد شامل دقيفا :count انتخاب باشد.",
	"countbetween"   => ":attribute بايد شامل حداقل :min و حداکثر :max انتخاب باشد.",
	"countmax"       => ":attribute بايد شامل حداکثر :max انتخاب باشد.",
	"countmin"       => ":attribute ميتواند حداقل :min انتخاب داشته باشد.",
	"different"      => ":attribute و :other نبايد يکسان باشند.",
	"email"          => ":attribute يک آدرس نا معتبر مي باشد.",
	"exists"         => ":attribute انتخاب شده نا معتبر مي باشد.",
	"image"          => ":attribute بايد يک تصوير باشد.",
	"in"             => ":attribute انتخاب شده نا معتبر است.",
	"integer"        => ":attribute بايد يک عدد صحيح باشد.",
	"ip"             => ":attribute بايد يک IP ي معتبر باشد..",
	"match"          => "قالب :attribute نا معتبر است.",
	"max"            => array(
		"numeric" => ":attribute بايد کمتر از :max باشد.",
		"file"    => "اندازه :attribute بايد کمتر از :max کيلو بايت باشد.",
		"string"  => ":attribute بايک کمتر از :max حرف باشد.",
	),
	"mimes"          => ":attribute بايد يک فايل از نوع type: :values باشد.",
	"min"            => array(
		"numeric" => ":attribute بايد حداقل :min باشد.",
		"file"    => "اندازه :attribute بايد حداقل :min کيلو بايت باشد.",
		"string"  => ":attribute حداقل بايد شامل :min حرف باشد.",
	),
	"not_in"         => ":attribute انتخاب شده صحيح نمي باشد.",
	"numeric"        => ":attribute ميتواند فقط شامل عدد باشد.",
	"required"       => ":attribute يک گزينه اجباريست و بايد وارد شود.",
	"same"           => ":attribute و :other بايد يکسان باشند.",
	"size"           => array(
		"numeric" => ":attribute بايد  به طول :size باشد.",
		"file"    => "اندازه :attribute بايد :size کيلوبايت باشد.",
		"string"  => "طول :attribute بايد :size حرف باشد.",
	),
	"unique"         => ":attribute قبلا استفاده شده است.",
	"url"            => "قالب :attribute صحيح نمي باشد.",

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