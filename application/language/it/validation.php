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

	"accepted"       => ":attribute deve essere accettato.",
	"active_url"     => ":attribute non &egrave; un URL valido.",
	"after"          => ":attribute deve essere una data successiva al :date.",
	"alpha"          => ":attribute pu&ograve; contenere solo lettere.",
	"alpha_dash"     => ":attribute pu&ograve; contenere solo numeri lettere e dashes.",
	"alpha_num"      => ":attribute pu&ograve; contenere solo lettere e numeri.",
	"array"          => ":attribute deve avere almeno un elemento selezionato.",
	"before"         => ":attribute deve essere una data che precede :date.",
	"between"        => array(
		"numeric" => ":attribute deve trovarsi tra :min - :max.",
		"file"    => ":attribute deve trovarsi tra :min - :max kilobytes.",
		"string"  => ":attribute deve trovarsi tra :min - :max caratteri.",
	),
	"confirmed"      => "Il campo di conferma per :attribute non coincide.",
	"count"          => ":attribute deve avere esattamente :count elementi selezionati.",
	"countbetween"   => ":attribute deve avere esattamente almeno :min o al pi&ugrave; :max elementi selezionati.",
	"countmax"       => ":attribute deve avere meno di :max elementi selezionati.",
	"countmin"       => ":attribute deve avere almeno :min elementi selezionati.",
	"different"      => ":attribute e :other devono essere differenti.",
	"email"          => ":attribute non &egrave; valido.",
	"exists"         => ":attribute selezionato/a non &egrave; valido.",
	"image"          => ":attribute deve essere un'immagine.",
	"in"             => ":attribute selezionato non &egrave; valido.",
	"integer"        => ":attribute deve essere intero.",
	"ip"             => ":attribute deve essere un indirizzo IP valido.",
	"match"          => ":attribute non &egrave; valido.",
	"max"            => array(
		"numeric" => ":attribute deve essere minore di :max.",
		"file"    => ":attribute non deve essere pi&ugrave grande di :max kilobytes.",
		"string"  => ":attribute non pu&ograve; contenere pi&ugrave; di :max caratteri.",
	),
	"mimes"          => ":attribute deve essere del tipo: :values.",
	"min"            => array(
		"numeric" => ":attribute deve valere almeno :min.",
		"file"    => ":attribute deve essere pi&ugrave; grande di :min kilobytes.",
		"string"  => ":attribute deve contenere almeno :min caratteri.",
	),
	"not_in"         => "Il valore selezionato per :attribute non &egrave; valido.",
	"numeric"        => ":attribute deve essere un numero.",
	"required"       => ":attribute non pu&ograve; essere omesso.",
	"same"           => ":attribute e :other devono coincidere.",
	"size"           => array(
		"numeric" => ":attribute deve valere :size.",
		"file"    => ":attribute deve essere grande :size kilobyte.",
		"string"  => ":attribute deve contenere :size caratteri.",
	),
	"unique"         => ":attribute &egrave; stato gi&agrave; usato.",
	"url"            => ":attribute deve essere un URL.",

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