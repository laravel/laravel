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

	"accepted"       => ":attribute phải được chấp thuận.",
	"active_url"     => ":attribute không phải là một URL hợp lệ.",
	"after"          => ":attribute phải là một ngày sau ngày :date.",
	"alpha"          => ":attribute chỉ được chứa chữ cái.",
	"alpha_dash"     => ":attribute chỉ được chứa chữ cái, chữ số và ký tự \"_\".",
	"alpha_num"      => ":attribute chỉ được chứa chữ cái và chữ số.",
	"array"          => ":attribute phải có phần tử được chọn.",
	"before"         => ":attribute phải là một ngày trước ngày :date.",
	"between"        => array(
		"numeric" => ":attribute phải nằm trong khoảng :min - :max.",
		"file"    => ":attribute phải có dung lượng nằm trong khoảng :min - :max kilobytes.",
		"string"  => ":attribute phải có độ dài nằm trong khoảng :min - :max kí tự.",
	),
	"confirmed"      => ":attribute xác nhận không đúng.",
	"count"          => ":attribute phải có đúng :count phần tử được chọn.",
	"countbetween"   => ":attribute phải có từ :min đến :max phần tử được chọn.",
	"countmax"       => ":attribute phải có ít hơn :max phần tử được chọn.",
	"countmin"       => ":attribute phải có tối thiểu :min phần tử được chọn.",
	"different"      => ":attribute và :other phải khác nhau.",
	"email"          => "Định dạng :attribute  không hợp lệ.",
	"exists"         => "Giá trị :attribute đã chọn không hợp lệ.",
	"image"          => ":attribute phải là file ảnh.",
	"in"             => "Giá trị :attribute đã chọn không hợp lệ.",
	"integer"        => ":attribute phải là một số nguyên.",
	"ip"             => ":attribute phải là một địa chỉ IP hợp lệ.",
	"match"          => "Định dạng :attribute không hợp lệ.",
	"max"            => array(
		"numeric" => ":attribute phải nhỏ hơn :max.",
		"file"    => ":attribute phải có dung lượng nhỏ hơn :max kilobytes.",
		"string"  => ":attribute phải có độ dài nhỏ hơn :max kí tự.",
	),
	"mimes"          => ":attribute phải là một file có thuộc dạng: :values.",
	"min"            => array(
		"numeric" => ":attribute phải lớn hơn hoặc bằng :min.",
		"file"    => ":attribute phải có dung lượng lớn hơn hoặc bằng :min kilobytes.",
		"string"  => ":attribute phải có độ dài lớn hơn hoặc bằng :min kí tự.",
	),
	"not_in"         => "Giá trị :attribute đã chọn không hợp lệ.",
	"numeric"        => ":attribute phải là một số.",
	"required"       => "Hãy nhập trường :attribute.",
	"same"           => ":attribute và :other phải giống nhau.",
	"size"           => array(
		"numeric" => ":attribute phải là :size.",
		"file"    => ":attribute phải có dung lượng là :size kilobyte.",
		"string"  => ":attribute phải dài :size kí tự.",
	),
	"unique"         => ":attribute đã bị sử dụng rồi.",
	"url"            => "Định dạng :attribute không hợp lệ.",

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