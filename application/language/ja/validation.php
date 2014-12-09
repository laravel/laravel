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

	/*
	|--------------------------------------------------------------------------
	| バリデーション言語設定
	|--------------------------------------------------------------------------
	|
	| 以下の言語設定はvalidatorクラスで使用しているデフォルトのエラーメッセージを構成しています。
	| 幾つかのルールは複数の設定を持っています。例えばサイズ(max,min,between)ルールです。
	| （訳注：(numeric, file, string)の間違いと思われる。
	| これらの設定は、文字列やファイルのように入力のタイプの違いにより、使い分けられます。
	|
	| これらの言語設定はあなたのアプリケーションでカスタムエラーメッセージとして表示するため
	| 気軽に変更してください。カスタムバリデーションルールのためのエラーメッセージも、
	| 同様にこのファイルに追加していただけます。
	|
	*/

	"accepted"       => ":attributeを承認してください。",
	"active_url"     => ":attributeが有効なURLではありません。",
	"after"          => ":attributeには、:date以降の日付を指定してください。",
	"alpha"          => ":attributeはアルファベッドのみがご利用できます。",
	"alpha_dash"     => ":attributeは英数字とダッシュ(-)及び下線(_)がご利用できます。",
	"alpha_num"      => ":attributeは英数字がご利用できます。",
	"array"          => "The :attribute must have selected elements.",
	"before"         => ":attributeには、:date以前の日付をご利用ください。",
	"between"        => array(
		"numeric" => ":attributeは、:minから、:maxまでの数字をご指定ください。",
		"file"    => ":attributeには、:min kBから:max kBまでのサイズのファイルをご指定ください。",
		"string"  => ":attributeは、:min文字から:max文字の間でご指定ください。",
	),
	"confirmed"      => ":attributeと、確認フィールドとが、一致していません。",
	"count"          => ":attributeは、:count個選択してください。",
	"countbetween"   => ":attributeは、:min個から:max個の間で選択してください。",
	"countmax"       => ":attributeは、:max個以下で選択してください。",
	"countmin"       => ":attributeは、最低:min個選択してください。",
	"different"      => ":attributeと:otherには、異なった内容を指定してください。",
	"email"          => ":attributeには正しいメールアドレスの形式をご指定ください。",
	"exists"         => "選択された:attributeは正しくありません。",
	"image"          => ":attributeには画像ファイルを指定してください。",
	"in"             => "選択された:attributeは正しくありません。",
	"integer"        => ":attributeは整数でご指定ください。",
	"ip"             => ":attributeには、有効なIPアドレスをご指定ください。",
	"match"          => ":attributeの入力フォーマットが間違っています。",
	"max"            => array(
		"numeric" => ":attributeには、:max以下の数字をご指定ください。",
		"file"    => ":attributeには、:max kB以下のファイルをご指定ください。",
		"string"  => ":attributeは、:max文字以下でご指定ください。",
	),
	"mimes"          => ":attributeには:valuesタイプのファイルを指定してください。",
	"min"            => array(
		"numeric" => ":attributeには、:min以上の数字をご指定ください。",
		"file"    => ":attributeには、:min kB以上のファイルをご指定ください。",
		"string"  => ":attributeは、:min文字以上でご指定ください。",
	),
	"not_in"         => "選択された:attributeは正しくありません。",
	"numeric"        => ":attributeには、数字を指定してください。",
	"required"       => ":attributeは必ず指定してください。",
	"same"           => ":attributeと:otherには同じ値を指定してください。",
	"size"           => array(
		"numeric" => ":attributeには:sizeを指定してください。",
		"file"    => ":attributeのファイルは、:sizeキロバイトでなくてはなりません。",
		"string"  => ":attributeは:size文字で指定してください。",
	),
	"unique"         => ":attributeに指定された値は既に存在しています。",
	"url"            => ":attributeのフォーマットが正しくありません。",

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

	/*
	|--------------------------------------------------------------------------
	| カスタムバリデーション言語設定
	|--------------------------------------------------------------------------
	|
	| ここでは、"属性_ルール"の記法を使用し、属性に対するカスタムバリデーションメッセージを
	| 指定してください。これにより、カスタムバリデーションをきれいに美しく保てます。
	|
	| 例えば、"email"属性のuniqueバリデーションで、カスタムバリデーションメッセージを
	| 使いたいならば、"email_unique"をカスタムメッセージとともに、配列に追加してください。
	| Validatorクラスが残りの面倒を見ます！
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

	/*
	|--------------------------------------------------------------------------
	| バリデーション属性
	|--------------------------------------------------------------------------
	|
	| 以下の言語設定は属性のプレースホルダーを例えば"email"属性を"E-Mailアドレス"という風に
	| 読み手に親切になるよう置き換えるために使用されます。
	| あなたのユーザーは、あなたに感謝するでしょう。
	|
	| Validatorクラスは、自動的にメッセージに含まれる:attributeプレースホルダーを
	| この配列の値に置き換えようと試みます。絶妙ですね。あなたも気に入ってくれるでしょう。
	*/

	'attributes' => array(),

);
