<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| View Storage Paths
	|--------------------------------------------------------------------------
	|
	| Most templating systems load templates from disk. Here you may specify
	| an array of paths that should be checked for your views. Of course
	| the usual Laravel view path has already been registered for you.
	|
	*/

	'paths' => array(__DIR__.'/../views'),

	/*
	|--------------------------------------------------------------------------
	| Pagination View
	|--------------------------------------------------------------------------
	|
	| This view will be used to render the pagination link output, and can
	| be easily customized here to show any view you like. A clean view
	| compatible with Twitter's Bootstrap is given to you by default.
	|
	*/

	'pagination' => 'pagination::slider',

	/*
	|--------------------------------------------------------------------------
	| Blade Content Tags
	|--------------------------------------------------------------------------
	|
	| When using "blade" views, you may specify the content tags used in Blade.
	| Defaults are provided, but feel free to change them to avoid conflicts
	| with other engines.
	|
	| Tags must be a two-value array, where the first element is the opening
	| tag and the last element is the closing tag.
	|
	*/

	'content_tags' => array('{{', '}}'),

	/*
	|--------------------------------------------------------------------------
	| Blade Raw Content Tags
	|--------------------------------------------------------------------------
	|
	| These are the tags used when outputting raw (not escaped) content in a
	| Blade view. The same rules apply as 'content_tags' above.
	|
	*/

	'raw_content_tags' => array('{{{', '}}}'),

);
