<?php

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$uri = urldecode($uri);

$paths = require __DIR__.'/bootstrap/paths.php';

$requested = $paths['public'].$uri;

// This file allows us to emulate Apache's "mod_rewrite" functionality from the
// built-in PHP web server. This provides a convenient way to test a Laravel
// application without having installed a "real" web server software here.
if ($uri !== '/' and file_exists($requested))
{
	return false;
}

// The following code enables filename-based cache busting. Requests
// such as "/css/style.1234567.css" are automatically converted to
// "/css/style.css". This approach was made popular by the HTML5
// Boilerplate project, see html5boilerplate.com for more info.
if (preg_match('/^(.+)\.(\d+)\.(css|gif|jpeg|jpg|js|png|svg)$/', $uri, $matches))
{
	$requested = $paths['public'].$matches[1].'.'.$matches[3];

	if (file_exists($requested))
	{
		$types = array(
			'css' => 'text/css',
			'gif' => 'image/gif',
			'jpeg' => 'image/jpeg',
			'jpg' => 'image/jpeg',
			'js' => 'application/javascript',
			'png' => 'image/png',
			'svg' => 'image/svg+xml'
		);

		header('Content-type: '.$types[$matches[3]]);
		readfile($requested);
		exit;
	}
}

require_once $paths['public'].'/index.php';
