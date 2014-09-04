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

	"accepted"       => ":attribute duhet të pranohet.",
	"active_url"     => ":attribute nuk është URL valide.",
	"after"          => ":attribute duhet të jetë datë pas :date.",
	"alpha"          => ":attribute mund të përmbajë vetëm shkronja.",
	"alpha_dash"     => ":attribute mund të përmbajë vetëm shkronja, numra dhe viza.",
	"alpha_num"      => ":attribute mund të përmbajë vetëm shkronja dhe numra.",
	"array"          => ":attribute duhet të ketë elemente të përzgjedhura.",
	"before"         => ":attribute duhet të jetë datë para :date.",
	"between"        => array(
		"numeric" => ":attribute duhet të jetë në mes :min - :max.",
		"file"    => ":attribute duhet të jetë në mes :min - :max kilobajtëve.",
		"string"  => ":attribute duhet të jetë në mes :min - :max karaktereve.",
	),
	"confirmed"      => ":attribute konfirmimi nuk përputhet.",
	"count"          => ":attributeduhet të ketë saktësisht :count elemente te përzgjedhura.",
	"countbetween"   => ":attribute duhet të jetë në mes :min and :max elemente te përzgjedhura.",
	"countmax"       => ":attribute duhet të ketë me pak se :max elemente te përzgjedhura.",
	"countmin"       => ":attribute duhet të ketë së paku :min elemente te përzgjedhura.",
	"different"      => ":attribute dhe :other duhet të jenë të ndryshme.",
	"email"          => ":attribute formati është jo valid.",
	"exists"         => ":attribute e përzgjedhur është jo valid.",
	"image"          => ":attribute duhet të jetë imazh.",
	"in"             => ":attribute e përzgjedhur është jo valid.",
	"integer"        => ":attribute duhet të jete numër i plotë.",
	"ip"             => ":attribute duhet të jetë një IP adresë e vlefshme.",
	"match"          => ":attribute formati është i pavlefshëm.",
	"max"            => array(
		"numeric" => ":attribute duhet të jetë më e vogël se :max.",
		"file"    => ":attribute duhet të jetë më e vogël se :max kilobytes.",
		"string"  => ":attribute duhet të jetë më e vogël se :max characters.",
	),
	"mimes"          => ":attribute duhet të jetë një fajll i tipit: :values.",
	"min"            => array(
		"numeric" => ":attribute duhet të jetë së paku :min.",
		"file"    => ":attribute duhet të jetë së paku :min kilobajt.",
		"string"  => ":attribute duhet të jetë së paku :min karaktere.",
	),
	"not_in"         => ":attribute e përzgjedhur është jo valid.",
	"numeric"        => ":attribute duhet të jetë numër.",
	"required"       => ":attribute fusha është e nevojshme.",
	"same"           => ":attribute dhe :other duhet të përputhen.",
	"size"           => array(
		"numeric" => ":attribute duhet të jetë :size.",
		"file"    => ":attribute duhet të jetë :size kilobajt.",
		"string"  => ":attribute duhet të jetë :size karaktere.",
	),
	"unique"         => ":attribute tashmë është marrë.",
	"url"            => ":attribute formati është i pavlefshëm.",

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