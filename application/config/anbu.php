<?php

return array(


	/*
	|--------------------------------------------------------------------------
	| Enable Anbu
	|--------------------------------------------------------------------------
	|
	| This will cause anbu to be rendered on every request, if you would prefer
	| to enable anbu in your templates manually, simply add Anbu::render();
	| after the <body> tag.
	|
	*/

	'enable' => true,

	/*
	|--------------------------------------------------------------------------
	| Show the LOG tab.
	|--------------------------------------------------------------------------
	|
	| Display a tog showing all entries made using the Laravel Log class.
	|
	*/

	'tab_logs' => true,

	/*
	|--------------------------------------------------------------------------
	| Show the QUERIES tab.
	|--------------------------------------------------------------------------
	|
	| Display a tab showing all queries performed by the Database layer.
	|
	*/

	'tab_queries' => true,

	/*
	|--------------------------------------------------------------------------
	| Include jQuery?
	|--------------------------------------------------------------------------
	|
	| Anbu needs the jQuery JavaScript framework to function, if you are already
	| using jQuery in your templates, set this value to false.
	|
	*/

	'include_jquery' => true,

	/*
	|--------------------------------------------------------------------------
	| Event Listeners
	|--------------------------------------------------------------------------
	|
	| These are the Laravel event listeners, feel free to modify them to use
	| a different data source, or include more if necessary.
	|
	*/

	'event_listeners' => function()
	{
		// pass laravel log entries to anbu
		Event::listen('laravel.log', 'Anbu::log');

		// pass executed SQL queries to anbu
		Event::listen('laravel.query', 'Anbu::sql');
	},

);
