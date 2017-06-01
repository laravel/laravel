<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| The following language lines contain the default error messages used by
	| the validator class. Some of these rules have multiple versions such
	| such as the size rules. Feel free to tweak each of these messages.
	|
	*/

	"accepted"         => ":attribute musi być zaakceptowany",
	"active_url"       => ":attribute nie jest poprawnym adresem URL.",
	"after"            => ":attribute musi być datą późniejszą niż :date.",
	"alpha"            => ":attribute może zawierać tylko litery.",
	"alpha_dash"       => ":attribute może zawierać tylko litery, cyfry oraz myślnik.",
	"alpha_num"        => ":attribute może zawierać tylko litery oraz cyfry",
	"before"           => ":attribute musi być datą wcześniejszą niż :date.",
	"between"          => array(
		"numeric" => ":attribute musi przyjmować wartość pomiędzy :min - :max",
		"file"    => ":attribute musi mieć rozmiar pomiędzy :min - :max kilobajt(ów)",
		"string"  => ":attribute musi mieć pomiędzy :min - :max znak(ów).",
	),
	"confirmed"        => ":attribute oraz potwierdzenie :attribute nie pasują do siebie.",
	"date"             => ":attribute nie jest prawidłową datą.",
	"date_format"      => ":attribute nie pasuje do określonego formatu :format.",
	"different"        => ":attribute oraz :other muszą się różnić.",
	"digits"           => ":attribute musi mieć :digits cyfr.",
	"digits_between"   => ":attribute musi mieć pomiędzy :min a :max cyfr.",
	"email"            => "Format :attribute jest nieprawidłowy.",
	"exists"           => "Wybrany :attribute jest nieprawidłowy.",
	"image"            => ":attribute musi być obrazem.",
	"in"               => "Wybrany :attribute jest nieprawidłowy.",
	"integer"          => ":attribute musi być liczbą całkowitą.",
	"ip"               => ":attribute musi być poprawnym adresem IP.",
	"max"              => array(
		"numeric" => "Wartość :attribute nie może być większa niż :max.",
		"file"    => "Rozmiar :attribute nie może być większa niż :max kilobajt(ów).",
		"string"  => "Długość :attribute nie może być większa niż :max znak(ów).",
	),
	"mimes"            => ":attribute musi być plikiem rodzaju: :values.",
	"min"              => array(
		"numeric" => "Minimalna wartość :attribute to :min.",
		"file"    => "Rozmiar :attribute powinien wynosić co najmniej :min kilobajt(ów).",
		"string"  => "Długość :attribute powinna wynosić co najmniej :min znak(ów).",
	),
	"not_in"           => "Wybrana wartość :attribute jest nieprawidłowa.",
	"numeric"          => ":attribute musi być liczbą.",
	"regex"            => "Format :attribute jest nieprawidłowy.",
	"required"         => "Pole :attribute jest wymagane.",
	"required_with"    => "Pole :attribute jest wymagane gdy wartość :values jest określona.",
	"required_without" => "Pole :attribute jest wymagane gdy wartość :values nie jest określona.",
	"same"             => ":attribute oraz :other muszą pasować.",
	"size"             => array(
		"numeric" => "Wartość :attribute musi wynosić :size.",
		"file"    => "Rozmiar :attribute musi wynosić :size kilobajt(ów).",
		"string"  => "Długość :attribute powinna wynosić :size znak(ów).",
	),
	"unique"           => ":attribute jest już zajęty/a.",
	"url"              => "Format :attribute jest nieprawidłowy.",

	/*
	|--------------------------------------------------------------------------
	| Custom Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| Here you may specify custom validation messages for attributes using the
	| convention "attribute.rule" to name the lines. This makes it quick to
	| specify a specific custom language line for a given attribute rule.
	|
	*/

	'custom' => array(),

	/*
	|--------------------------------------------------------------------------
	| Custom Validation Attributes
	|--------------------------------------------------------------------------
	|
	| The following language lines are used to swap attribute place-holders
	| with something more reader friendly such as E-Mail Address instead
	| of "email". This simply helps us make messages a little cleaner.
	|
	*/

	'attributes' => array(),

);
