<?php

/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylorotwell@gmail.com>
 */

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

// This file allows us to emulate Apache's "mod_rewrite" functionality from the
// built-in PHP web server. This provides a convenient way to test a Laravel
// application without having installed a "real" web server software here.
$file = __DIR__.'/public'.$uri;
if ($uri !== '/' && file_exists($file)) {
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    if ($ext === 'css') {
        header("Content-Type: text/css");
    } elseif ($ext === 'js') {
        header("Content-Type: application/x-javascript");
    }
    readfile($file);
} else {
    require_once __DIR__.'/public/index.php';
}
