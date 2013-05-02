<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Custom transliteration character map
	|--------------------------------------------------------------------------
	|
	| Set list of custom transliteration rules in the following format
	| for language specific characters:
	|	array(
	|   'de' => array(
	|     'Ä' => 'Ae', 'Ö' => 'Oe'
	|   )
	| )
	|
	| or just general custom characters:
	|
	|	array('Ä' => 'Ae', 'Ö' => 'Oe')
	|
	|
	*/

	'ascii' => array(),

	/*
	|--------------------------------------------------------------------------
	| List of words to be removed
	|--------------------------------------------------------------------------
	|
	| Set list of words that you want to be removed from the string
	| when you use Str::slug() method. Applied before transliteration.
	| In most cases this list might contain conjunctions or filler-words.
	|
	*/

	'remove' => array(),

);

