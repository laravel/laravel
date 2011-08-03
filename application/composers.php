<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| View Composers
	|--------------------------------------------------------------------------
	|
	| View composers provide a convenient way to add common elements to a view
	| each time it is created. For example, you may wish to bind a header and
	| footer partial each time the view is created.
	|
	| The composer will receive an instance of the view being created, and is
	| free to modify the view however you wish. Be sure to always return the
	| view instance at the end of your composer.
	|
	| For more information, check out: http://laravel.com/docs/start/views#composers
	|
	*/

	'home/index' => function($view)
	{
		return $view;
	},

);