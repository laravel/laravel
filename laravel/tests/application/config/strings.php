<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| String Inflection
	|--------------------------------------------------------------------------
	|
	| This array contains the singular and plural forms of words. It's used by
	| the "singular" and "plural" methods on the Str class to convert a given
	| word from singular to plural and vice versa.
	|
	| Note that the regular expressions are only for inflecting English words.
	| To inflect a non-English string, simply add its singular and plural
	| form to the array of "irregular" word forms.
	|
	*/

	'plural' => array(
		'/(quiz)$/i' => "$1zes",
		'/^(ox)$/i' => "$1en",
		'/([m|l])ouse$/i' => "$1ice",
		'/(matr|vert|ind)ix|ex$/i' => "$1ices",
		'/(x|ch|ss|sh)$/i' => "$1es",
		'/([^aeiouy]|qu)y$/i' => "$1ies",
		'/(hive)$/i' => "$1s",
		'/(?:([^f])fe|([lr])f)$/i' => "$1$2ves",
		'/(shea|lea|loa|thie)f$/i' => "$1ves",
		'/sis$/i' => "ses",
		'/([ti])um$/i' => "$1a",
		'/(tomat|potat|ech|her|vet)o$/i' => "$1oes",
		'/(bu)s$/i' => "$1ses",
		'/(alias)$/i' => "$1es",
		'/(octop)us$/i' => "$1i",
		'/(ax|test)is$/i' => "$1es",
		'/(us)$/i' => "$1es",
		'/s$/i' => "s",
		'/$/' => "s"
	),

	'singular' => array(
		'/(quiz)zes$/i' => "$1",
		'/(matr)ices$/i' => "$1ix",
		'/(vert|ind)ices$/i' => "$1ex",
		'/^(ox)en$/i' => "$1",
		'/(alias)es$/i' => "$1",
		'/(octop|vir)i$/i' => "$1us",
		'/(cris|ax|test)es$/i' => "$1is",
		'/(shoe)s$/i' => "$1",
		'/(o)es$/i' => "$1",
		'/(bus)es$/i' => "$1",
		'/([m|l])ice$/i' => "$1ouse",
		'/(x|ch|ss|sh)es$/i' => "$1",
		'/(m)ovies$/i' => "$1ovie",
		'/(s)eries$/i' => "$1eries",
		'/([^aeiouy]|qu)ies$/i' => "$1y",
		'/([lr])ves$/i' => "$1f",
		'/(tive)s$/i' => "$1",
		'/(hive)s$/i' => "$1",
		'/(li|wi|kni)ves$/i' => "$1fe",
		'/(shea|loa|lea|thie)ves$/i' => "$1f",
		'/(^analy)ses$/i' => "$1sis",
		'/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i' => "$1$2sis",
		'/([ti])a$/i' => "$1um",
		'/(n)ews$/i' => "$1ews",
		'/(h|bl)ouses$/i' => "$1ouse",
		'/(corpse)s$/i' => "$1",
		'/(us)es$/i' => "$1",
		'/(us|ss)$/i' => "$1",
		'/s$/i' => "",
	),

	'irregular' => array(
		'child' => 'children',
		'foot' => 'feet',
		'goose' => 'geese',
		'man' => 'men',
		'move' => 'moves',
		'person' => 'people',
		'sex' => 'sexes',
		'tooth' => 'teeth',
	),

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

	/*
	|--------------------------------------------------------------------------
	| ASCII Characters
	|--------------------------------------------------------------------------
	|
	| This array contains foreign characters and their 7-bit ASCII equivalents.
	| The array is used by the "ascii" method on the Str class to get strings
	| ready for inclusion in a URL slug.
	|
	| Of course, the "ascii" method may also be used by you for whatever your
	| application requires. Feel free to add any characters we missed, and be
	| sure to let us know about them!
	|
	*/

	'ascii' => array(

		'/æ|ǽ/' => 'ae',
		'/œ/' => 'oe',
		'/À|Á|Â|Ã|Ä|Å|Ǻ|Ā|Ă|Ą|Ǎ|А/' => 'A',
		'/à|á|â|ã|ä|å|ǻ|ā|ă|ą|ǎ|ª|а/' => 'a',
		'/Б/' => 'B',
		'/б/' => 'b',
		'/Ç|Ć|Ĉ|Ċ|Č|Ц/' => 'C',
		'/ç|ć|ĉ|ċ|č|ц/' => 'c',
		'/Ð|Ď|Đ|Д/' => 'Dj',
		'/ð|ď|đ|д/' => 'dj',
		'/È|É|Ê|Ë|Ē|Ĕ|Ė|Ę|Ě|Е|Ё|Э/' => 'E',
		'/è|é|ê|ë|ē|ĕ|ė|ę|ě|е|ё|э/' => 'e',
		'/Ф/' => 'F',
		'/ƒ|ф/' => 'f',
		'/Ĝ|Ğ|Ġ|Ģ|Г/' => 'G',
		'/ĝ|ğ|ġ|ģ|г/' => 'g',
		'/Ĥ|Ħ|Х/' => 'H',
		'/ĥ|ħ|х/' => 'h',
		'/Ì|Í|Î|Ï|Ĩ|Ī|Ĭ|Ǐ|Į|İ|И/' => 'I',
		'/ì|í|î|ï|ĩ|ī|ĭ|ǐ|į|ı|и/' => 'i',
		'/Ĵ|Й/' => 'J',
		'/ĵ|й/' => 'j',
		'/Ķ|К/' => 'K',
		'/ķ|к/' => 'k',
		'/Ĺ|Ļ|Ľ|Ŀ|Ł|Л/' => 'L',
		'/ĺ|ļ|ľ|ŀ|ł|л/' => 'l',
		'/М/' => 'M',
		'/м/' => 'm',
		'/Ñ|Ń|Ņ|Ň|Н/' => 'N',
		'/ñ|ń|ņ|ň|ŉ|н/' => 'n',
		'/Ö|Ò|Ó|Ô|Õ|Ō|Ŏ|Ǒ|Ő|Ơ|Ø|Ǿ|О/' => 'O',
		'/ö|ò|ó|ô|õ|ō|ŏ|ǒ|ő|ơ|ø|ǿ|º|о/' => 'o',
		'/П/' => 'P',
		'/п/' => 'p',
		'/Ŕ|Ŗ|Ř|Р/' => 'R',
		'/ŕ|ŗ|ř|р/' => 'r',
		'/Ś|Ŝ|Ş|Ș|Š|С/' => 'S',
		'/ś|ŝ|ş|ș|š|ſ|с/' => 's',
		'/Ţ|Ț|Ť|Ŧ|Т/' => 'T',
		'/ţ|ț|ť|ŧ|т/' => 't',
		'/Ù|Ú|Û|Ũ|Ū|Ŭ|Ů|Ü|Ű|Ų|Ư|Ǔ|Ǖ|Ǘ|Ǚ|Ǜ|У/' => 'U',
		'/ù|ú|û|ũ|ū|ŭ|ů|ü|ű|ų|ư|ǔ|ǖ|ǘ|ǚ|ǜ|у/' => 'u',
		'/В/' => 'V',
		'/в/' => 'v',
		'/Ý|Ÿ|Ŷ|Ы/' => 'Y',
		'/ý|ÿ|ŷ|ы/' => 'y',
		'/Ŵ/' => 'W',
		'/ŵ/' => 'w',
		'/Ź|Ż|Ž|З/' => 'Z',
		'/ź|ż|ž|з/' => 'z',
		'/Æ|Ǽ/' => 'AE',
		'/ß/'=> 'ss',
		'/Ĳ/' => 'IJ',
		'/ĳ/' => 'ij',
		'/Œ/' => 'OE',
		'/Ч/' => 'Ch',
		'/ч/' => 'ch',
		'/Ю/' => 'Ju',
		'/ю/' => 'ju',
		'/Я/' => 'Ja',
		'/я/' => 'ja',
		'/Ш/' => 'Sh',
		'/ш/' => 'sh',
		'/Щ/' => 'Shch',
		'/щ/' => 'shch',
		'/Ж/' => 'Zh',
		'/ж/' => 'zh',

	),

);