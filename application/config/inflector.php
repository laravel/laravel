<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Plural Word Forms
	|--------------------------------------------------------------------------
	|
	| This array of word forms is used by the Inflector library to convert
	| singular words to their plural form. The key side of the array contains
	| regular expressions used to match the singular form, while the value
	| side of the array contains their plural endings.
	|
	*/

	'plural' => array(
		'/(quiz)$/i'               => "$1zes",
		'/^(ox)$/i'                => "$1en",
		'/([m|l])ouse$/i'          => "$1ice",
		'/(matr|vert|ind)ix|ex$/i' => "$1ices",
		'/(x|ch|ss|sh)$/i'         => "$1es",
		'/([^aeiouy]|qu)y$/i'      => "$1ies",
		'/(hive)$/i'               => "$1s",
		'/(?:([^f])fe|([lr])f)$/i' => "$1$2ves",
		'/(shea|lea|loa|thie)f$/i' => "$1ves",
		'/sis$/i'                  => "ses",
		'/([ti])um$/i'             => "$1a",
		'/(tomat|potat|ech|her|vet)o$/i' => "$1oes",
		'/(bu)s$/i'                => "$1ses",
		'/(alias)$/i'              => "$1es",
		'/(octop)us$/i'            => "$1i",
		'/(ax|test)is$/i'          => "$1es",
		'/(us)$/i'                 => "$1es",
		'/s$/i'                    => "s",
		'/$/'                      => "s"
	),

	/*
	|--------------------------------------------------------------------------
	| Singular Word Forms
	|--------------------------------------------------------------------------
	|
	| This array of word forms is used by the Inflector library to convert
	| plural words to their singular form. The key side of the array contains
	| regular expressions used to match the plural form, while the value side
	| of the array contains their singular endings.
	|
	*/

	'singular' => array(
		'/(quiz)zes$/i'              => "$1",
		'/(matr)ices$/i'             => "$1ix",
		'/(vert|ind)ices$/i'         => "$1ex",
		'/^(ox)en$/i'                => "$1",
		'/(alias)es$/i'              => "$1",
		'/(octop|vir)i$/i'           => "$1us",
		'/(cris|ax|test)es$/i'       => "$1is",
		'/(shoe)s$/i'                => "$1",
		'/(o)es$/i'                  => "$1",
		'/(bus)es$/i'                => "$1",
		'/([m|l])ice$/i'             => "$1ouse",
		'/(x|ch|ss|sh)es$/i'         => "$1",
		'/(m)ovies$/i'               => "$1ovie",
		'/(s)eries$/i'               => "$1eries",
		'/([^aeiouy]|qu)ies$/i'      => "$1y",
		'/([lr])ves$/i'              => "$1f",
		'/(tive)s$/i'                => "$1",
		'/(hive)s$/i'                => "$1",
		'/(li|wi|kni)ves$/i'         => "$1fe",
		'/(shea|loa|lea|thie)ves$/i' => "$1f",
		'/(^analy)ses$/i'            => "$1sis",
		'/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i' => "$1$2sis",
		'/([ti])a$/i'                => "$1um",
		'/(n)ews$/i'                 => "$1ews",
		'/(h|bl)ouses$/i'            => "$1ouse",
		'/(corpse)s$/i'              => "$1",
		'/(us)es$/i'                 => "$1",
		'/(us|ss)$/i'                => "$1",
		'/s$/i'                      => "",
	),

	/*
	|--------------------------------------------------------------------------
	| Irregular Word Forms
	|--------------------------------------------------------------------------
	|
	| Of course, some words have irregular conversions from their singular to
	| plural forms. For these, we use this array to help the Inflector library
	| convert words such as "child" and "tooth".
	|
	| The key side of the array should contain the singular forms of the words,
	| while the value side of the array should contain the plural forms.
	|
	*/

	'irregular' => array(
		'child'  => 'children',
		'foot'   => 'feet',
		'goose'  => 'geese',
		'man'    => 'men',
		'move'   => 'moves',
		'person' => 'people',
		'sex'    => 'sexes',
		'tooth'  => 'teeth',
	),

	/*
	|--------------------------------------------------------------------------
	| Uncountable Word Forms
	|--------------------------------------------------------------------------
	|
	| For some words, their plural form and their singular form are the same.
	| For example, words such as "deer" and "police" share the same form for
	| their singular and plural.
	|
	*/

	'uncountable' => array(
		'audio',
		'equipment',
		'deer',
		'fish',
		'gold',
		'information',
		'money',
		'rice',
		'police',
		'series',
		'sheep',
		'species',
	),

);