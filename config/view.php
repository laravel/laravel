<?php

return [

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

	'paths' => [base_path().'/resources/views'],

	/*
	|--------------------------------------------------------------------------
	| Compiled View Path
	|--------------------------------------------------------------------------
	|
	| This option determines where all the compiled Blade templates will be
	| stored for your application. Typically, this is within the storage
	| directory. However, as usual, you are free to change this value.
	|
	*/

	'compiled' => storage_path().'/framework/views',

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

	'pagination' => 'pagination::slider-3',

];
