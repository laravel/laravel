<?php

require_once __DIR__.'/libraries/markdown.php';

View::composer('docs::template', function($view)
{
	Asset::add('stylesheet', 'css/style.css');
	Asset::add('modernizr', 'js/modernizr-2.5.3.min.js');
	Asset::container('footer')->add('prettify', 'js/prettify.js');
	$view->with('sidebar', Markdown(file_get_contents(path('storage').'documentation/contents.md')));
});

Route::get('(:bundle)', function()
{
	return View::make('docs::home');
});

Route::get('docs/(:any)/(:any?)', function($section, $page = null)
{
	$page = rtrim(implode('/', array($section, $page)), '/').'.md';

	$content = Markdown(file_get_contents(path('storage').'documentation/'.$page));

	return View::make('docs::page')->with('content', $content);	
});