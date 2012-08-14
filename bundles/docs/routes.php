<?php

/**
 * Load the Markdown library.
 */
require_once __DIR__.'/libraries/markdown.php';

/**
 * Set the path to the documentation.
 */
set_path('docs', path('sys').'documentation'.DS);

/**
 * Handle routes for documentation files.
 *
 * @param  string  $file
 * @return mixed
 */
Route::get('(:bundle)([a-zA-Z0-9\_\-/]*)', function($file)
{
	if ($file === '')
		$file = 'home';

	// $file = str_replace('/', DS, $file);

	if ( ! file_exists($file = path('docs').$file.'.md'))
		return Response::error('404');

	$sidebar = @file_get_contents(path('docs').'contents.md') ?: 'File not found.';

	$content = file_get_contents($file);

	return View::make('docs::page')
		->with('sidebar', Markdown($sidebar))
		->with('content', Markdown($content));
});