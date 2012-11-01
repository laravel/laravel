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

	"accepted"       => ":attribute m&aring;ste accepteras.",
	"active_url"     => ":attribute &auml;r inte en giltig webbadress.",
	"after"          => ":attribute m&aring;ste vara ett datum efter den :date.",
	"alpha"          => ":attribute f&aring;r endast inneh&aring;lla bokst&auml;ver.",
	"alpha_dash"     => ":attribute f&aring;r endast inneh&aring;lla bokst&auml;ver, nummer och bindestreck.",
	"alpha_num"      => ":attribute f&aring;r endast inneh&aring;lla bokst&auml;ver och nummer.",
	"array"          => ":attribute m&aring;ste ha valda element.",
	"before"         => ":attribute m&aring;ste vara ett datum innan den :date.",
	"between"        => array(
		"numeric" => ":attribute m&aring;ste vara ett nummer mellan :min och :max.",
		"file"    => ":attribute m&aring;ste vara mellan :min till :max kilobytes stor.",
		"string"  => ":attribute m&aring;ste inneh&aring;lla :min till :max tecken.",
	),
	"confirmed"      => ":attribute bekr&auml;ftelsen matchar inte.",
	"count"          => ":attribute m&aring;ste exakt ha :count valda element.",
	"countbetween"   => ":attribute f&aring;r endast ha :min till :max valda element.",
	"countmax"       => ":attribute f&aring;r max ha :max valda element.",
	"countmin"       => ":attribute m&aring;ste minst ha :min valda element.",
	"different"      => ":attribute och :other f&aring;r ej vara lika.",
	"email"          => ":attribute formatet &auml;r ogiltig.",
	"exists"         => "Det valda :attribute &auml;r ogiltigt.",
	"image"          => ":attribute m&aring;ste vara en bild.",
	"in"             => "Det valda :attribute &auml;r ogiltigt.",
	"integer"        => ":attribute m&aring;ste vara en siffra.",
	"ip"             => ":attribute m&aring;ste vara en giltig IP-adress.",
	"match"          => ":attribute formatet &auml;r ogiltig.",
	"max"            => array(
		"numeric" => ":attribute f&aring;r inte vara st&ouml;rre &auml;n :max.",
		"file"    => ":attribute f&aring;r max vara :max kilobytes stor.",
		"string"  => ":attribute f&aring;r max inneh&aring;lla :max tecken.",
	),
	"mimes"          => ":attribute m&aring;ste vara en fil av typen: :values.",
	"min"            => array(
		"numeric" => ":attribute m&aring;ste vara st&ouml;rre &auml;n :min.",
		"file"    => ":attribute m&aring;ste minst vara :min kilobytes stor.",
		"string"  => ":attribute m&aring;ste minst inneh&aring;lla :min tecken.",
	),
	"not_in"         => "Det valda :attribute &auml;r ogiltigt.",
	"numeric"        => ":attribute m&aring;ste vara ett nummer.",
	"required"       => ":attribute f&auml;ltet &auml;r obligatoriskt.",
	"same"           => ":attribute och :other m&aring;ste vara likadana.",
	"size"           => array(
		"numeric" => ":attribute m&aring;ste vara :size.",
		"file"    => ":attribute f&aring;r endast vara :size kilobyte stor.",
		"string"  => ":attribute m&aring;ste inneh&aring;lla :size tecken.",
	),
	"unique"         => ":attribute anv&auml;nds redan.",
	"url"            => ":attribute formatet &auml;r ogiltig",

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