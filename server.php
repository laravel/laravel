<?php

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$uri = urldecode($uri);

$paths = require __DIR__.'/bootstrap/paths.php';

$requested = $paths['public'].$uri;

// This file allows us to emulate Apache's "mod_rewrite" functionality from the
// built-in PHP web server. This provides a convenient way to test a Laravel
// application without having installed a "real" web server software here.
if ($uri !== '/' and (is_file($requested) OR is_dir($requested) && (file_exists($requested.'/index.php') OR file_exists($requested.'/index.html'))))
{
	return false;
}

require_once $paths['public'].'/index.php';
