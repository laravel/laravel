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

	"accepted"       => ":attribute zahteva potrditev.",
	"active_url"     => ":attribute ni veljaven URL naslov.",
	"after"          => ":attribute mora biti datum po :date.",
	"alpha"          => ":attribute lahko vsebuje le črke.",
	"alpha_dash"     => ":attribute lahko vsebuje le črke,števila in pomišljaj.",
	"alpha_num"      => ":attribute lahko vsebuje le črke in števila.",
	"array"          => ":attribute mora vsebovati izbrane elemente.",
	"before"         => ":attribute mora biti datum pred :date.",
	"between"        => array(
		"numeric" => ":attribute mora biti med :min - :max.",
		"file"    => ":attribute mora biti med :min - :max kilobajti.",
		"string"  => ":attribute je lahko dolžine med :min - :max znaki.",
	),
	"confirmed"      => ":attribute potrditev se ne ujema.",
	"count"          => ":attribute mora imeti natančno :count izbranih elementov.",
	"countbetween"   => ":attribute mora biti med :min and :max izbranih elementov.",
	"countmax"       => ":attribute mora imeti manj kot :max izbranih elementov.",
	"countmin"       => ":attribute mora vsebovati najmanj :min izbranih elementov.",
	"different"      => ":attribute in :other ne smeta biti enaka.",
	"email"          => "Format :attribute ni veljaven.",
	"exists"         => "Izbran element :attribute ne obstaja.",
	"image"          => ":attribute mora biti slika.",
	"in"             => "Izbran element :attribute je neveljaven.",
	"integer"        => ":attribute mora biti število.",
	"ip"             => ":attribute mora biti veljaven IP naslov.",
	"match"          => "Format elementa :attribute je neveljaven.",
	"max"            => array(
		"numeric" => ":attribute mora biti manj kot :max.",
		"file"    => ":attribute mora biti manj kot :max kilobajtov.",
		"string"  => ":attribute mora vsebovati manj kot :max znakov.",
	),
	"mimes"          => "Element :attribute mora biti vrste: :values.",
	"min"            => array(
		"numeric" => "attribute mora biti vsaj :min.",
		"file"    => ":attribute mora biti najmanj :min kilobajtov.",
		"string"  => ":attribute mora vsebovati najmanj :min znakov.",
	),
	"not_in"         => "Izbrani element :attribute ni veljaven.",
	"numeric"        => ":attribute mora biti število.",
	"required"       => ":attribute polje je zahtevano.",
	"same"           => ":attribute in :other morata biti enaka.",
	"size"           => array(
		"numeric" => ":attribute mora biti velik :size.",
		"file"    => ":attribute mora biti velik vsaj :size kilobajtov.",
		"string"  => ":attribute mora biti dolg :size znakov.",
	),
	"unique"         => ":attribute je že zaseden.",
	"url"            => ":attribute je v neveljavnem formatu.",

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