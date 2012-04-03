<?php

/**
 * Load the Markdown library.
 */
require_once __DIR__.'/libraries/markdown.php';

/**
 * Get the parsed Markdown contents of a given page.
 *
 * @param  string  $page
 * @return string
 */
function document($page)
{
	return Markdown(file_get_contents(path('storage').'documentation/'.$page.'.md'));
}

/**
 * Determine if a documentation page exists.
 *
 * @param  string  $page
 * @return bool
 */
function document_exists($page)
{
	return file_exists(path('storage').'documentation/'.$page.'.md');
}

/**
 * Attach the sidebar to the documentatoin template.
 */
View::composer('docs::template', function($view)
{
	$view->with('sidebar', document('contents'));
});

/**
 * Handle the documentation homepage.
 *
 * This page contains the "introduction" to Laravel.
 */
Route::get('(:bundle)', function()
{
	return View::make('docs::page')->with('content', document('home'));
});

/**
 * Handle documentation routes for sections and pages.
 *
 * @param  string  $section
 * @param  string  $page
 * @return mixed
 */
Route::get('(:bundle)/(:any)/(:any?)', function($section, $page = null)
{
	$file = rtrim(implode('/', func_get_args()), '/');

	// If no page was specified, but a "home" page exists for the section,
	// we'll set the file to the home page so that the proper page is
	// display back out to the client for the requested doc page.
	if (is_null($page) and document_exists($file.'/home'))
	{
		$file .= '/home';
	}

	if (document_exists($file))
	{
		return View::make('docs::page')->with('content', document($file));
	}
	else
	{
		return Response::error('404');
	}
});