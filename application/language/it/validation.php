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

	"accepted"       => "Il campo :attribute deve essere accettato.",
	"active_url"     => "Il campo :attribute non &egrave; un URL valido.",
	"after"          => "Il campo :attribute deve essere una data successiva a :date.",
	"alpha"          => "Il campo :attribute pu&ograve; contenere solo lettere.",
	"alpha_dash"     => "Il campo :attribute pu&ograve; contenere solo lettere, numeri e trattini.",
	"alpha_num"      => "Il campo :attribute pu&ograve; contenere solo lettere e numeri.",
	"array"          => "Il campo :attribute deve avere almeno un elemento selezionato.",
	"before"         => "Il campo :attribute deve essere una data precedente a :date.",
	"between"        => array(
		"numeric" => "Il valore di :attribute deve essere compreso tra :min - :max.",
		"file"    => "Il file :attribute deve essere di dimensioni comprese tra :min - :max kilobytes.",
		"string"  => "Il campo :attribute deve essere di lunghezza compresa tra :min e :max caratteri.",
	),
	"confirmed"      => "Il campo :attribute di conferma non corrisponde.",
	"count"          => "Il campo :attribute deve avere esattamente :count elementi selezionati.",
	"countbetween"   => "Il campo :attribute deve avere tra :min e :max elementi selezionati.",
	"countmax"       => "Il campo :attribute deve avere meno di :max elementi selezionati.",
	"countmin"       => "Il campo :attribute deve avere almeno :min elementi selezionati.",
	"different"      => "Il campo :attribute e :other non possono essere uguali.",
	"email"          => "Il formato del campo :attribute non &egrave; valido.",
	"exists"         => "Il :attribute selezionato non &egrave; valido.",
	"image"          => "Il campo :attribute deve essere una immagine.",
	"in"             => "Il :attribute selezionato non &egrave; valido.",
	"integer"        => "Il campo :attribute deve essere un intero.",
	"ip"             => "Il campo :attribute deve contenere un indirizzo IP valido.",
	"match"          => "Il formato del campo :attribute non &egrave; valido.",
	"max"            => array(
		"numeric" => "Il valore di :attribute deve essere minore di :max.",
		"file"    => "Il file :attribute deve essere di dimensioni minori di :max kilobytes.",
		"string"  => "Il campo :attribute deve essere lungo massimo :max caratteri.",
	),
	"mimes"          => "Il campo :attribute deve essere un file di tipo: :values.",
	"min"            => array(
		"numeric" => "Il valore di :attribute deve essere almeno :min.",
		"file"    => "Il file :attribute deve avere una dimensione minima di :min kilobytes.",
		"string"  => "Il campo :attribute deve avere una lunghezza minima di :min caratteri.",
	),
	"not_in"         => "Il campo :attribute selezionato non &egrave; valido.",
	"numeric"        => "Il campo :attribute deve essere un numero.",
	"required"       => "Il campo :attribute &egrave; obbligatorio.",
	"same"           => "Il campo :attribute e il campo :other devono corrispondere.",
	"size"           => array(
		"numeric" => "Il campo :attribute deve essere :size.",
		"file"    => "Il campo :attribute deve essere :size kilobytes.",
		"string"  => "Il campo :attribute deve essere :size characters.",
	),
	"unique"         => "Il campo :attribute &egrave; gi&agrave; stato utilizzato.",
	"url"            => "Il formato del campo :attribute non &egrave; valido.",

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