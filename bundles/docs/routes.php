<?php

require_once __DIR__.'/libraries/markdown.php';

View::composer('docs::template', function($view)
{
	Asset::add('stylesheet', 'laravel/css/style.css');
	Asset::add('modernizr', 'laravel/js/modernizr-2.5.3.min.js');
	Asset::container('footer')->add('prettify', 'laravel/js/prettify.js');
	$view->with('sidebar', Markdown(file_get_contents(path('storage').'documentation/contents.md')));
});

Route::get('(:bundle)', function()
{
	return View::make('docs::home');
});

Route::get('docs/(:any)/(:any?)', function($section, $page = null)
{
	$root = path('storage').'documentation/';

	$file = rtrim(implode('/', array($section, $page)), '/').'.md';

	if (file_exists($path = $root.$file))
	{
		$content = Markdown(file_get_contents($path));
	}
	elseif (file_exists($path = $root.$section.'/home.md'))
	{
		$content = Markdown(file_get_contents($path));
	}
	else
	{
		return Response::error('404');
	}

	return View::make('docs::page')->with('content', $content);	
});