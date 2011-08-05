<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| View Names & Composers
	|--------------------------------------------------------------------------
	|
	| Named views give you beautiful syntax when working with your views.
	| After you have defined a name for a view, you can create an instance of
	| that view using the expressive View::of dynamic method:
	|
	|		return View::of_layout();
	|
	| For more information, check out: http://laravel.com/docs/start/views#named-views
	|
	| View composers provide a convenient way to add common elements to a view
	| each time it is created. For example, you may wish to bind a header and
	| footer partial each time the view is created.
	|
	| The composer will receive an instance of the view being created, and is
	| free to modify the view however you wish.
	|
	| For more information, check out: http://laravel.com/docs/start/views#composers
	|
	*/

	'home.index' => array('name' => 'home', function($view)
	{
		//
	}),

);