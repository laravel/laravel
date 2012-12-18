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

	"accepted"       => ":attribute pitää hyväksyä.",
	"active_url"     => ":attribute pitää olla validi URL-osoite.",
	"after"          => ":attribute pitää olla päiväys :date päiväyksen jälkeen.",
	"alpha"          => ":attribute voi vain sisältää kirjaimia.",
	"alpha_dash"     => ":attribute voi vain sisältää kirjaimia, numeroita ja viivoja.",
	"alpha_num"      => ":attribute voi vain sisältää kirjaimia ja numeroita.",
	"array"          => ":attribute pitää sisältää elementin.",
	"before"         => ":attribute pitää olla päiväys ennen :date.",
	"between"        => array(
		"numeric" => ":attribute numeron pitää olla välillä :min - :max.",
		"file"    => ":attribute tiedoston pitää olla välillä :min - :max kilobittiä.",
		"string"  => ":attribute elementin pitää olla välillä :min - :max kirjainta.",
	),
	"confirmed"      => ":attribute vahvistus ei täsmää.",
	"count"          => ":attribute pitää olla tarkalleen :count määrä elementtejä.",
	"countbetween"   => ":attribute pitää olla välillä :min ja :max määrä elementtejä.",
	"countmax"       => ":attribute pitää olla vähemmän kun :max määrä elementtejä.",
	"countmin"       => ":attribute pitää olla vähintään :min määrä elementtejä.",
	"different"      => ":attribute ja :other tulee olla eri arvoisia.",
	"email"          => ":attribute muoto on virheellinen.",
	"exists"         => "valittu :attribute on virheellinen.",
	"image"          => ":attribute pitää olla kuva.",
	"in"             => "valittu :attribute on virheellinen.",
	"integer"        => ":attribute pitää olla numero.",
	"ip"             => ":attribute pitää olla validi IP-osoite.",
	"match"          => ":attribute muoto on virheellinen.",
	"max"            => array(
		"numeric" => ":attribute pitää olla pienempi kuin :max.",
		"file"    => ":attribute pitää olla pienempi :max kilobittiä.",
		"string"  => ":attribute pitää olla pienempi :max kirjainta.",
	),
	"mimes"          => ":attribute pitää olla tiedostotyyppi: :values.",
	"min"            => array(
		"numeric" => ":attribute pitää olla vähintään :min.",
		"file"    => ":attribute pitää olla vähintään :min kilobittiä.",
		"string"  => ":attribute pitää olla vähintään :min kirjainta.",
	),
	"not_in"         => "valittu :attribute on virheellinen.",
	"numeric"        => ":attribute pitää olla numero.",
	"required"       => ":attribute kenttä on pakollinen.",
	"same"           => ":attribute ja :other on oltava samat.",
	"size"           => array(
		"numeric" => ":attribute pitää olla kokoa: :size.",
		"file"    => ":attribute pitää olla kokoa: :size kilobittiä.",
		"string"  => ":attribute pitää olla kokoa: :size kirjainta.",
	),
	"unique"         => ":attribute on jo valittu.",
	"url"            => ":attribute URL-osoite on virheellinen.",

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