<?php

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Register The Composer Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader
| for our application. We just need to utilize it! We'll require it
| into the script here so that we do not have to worry about the
| loading of any our classes "manually". Feels great to relax.
|
*/

require __DIR__.'/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Run the artisan clear-compiled command without loading the framework
|--------------------------------------------------------------------------
|
| When running composer update, laravel runs the clear-compiled command.
| Under most circumstances this works fine, however if your package
| requirements change, and your updating via version control there is
| potential for this to fail as your providers config array could differ
| from whats installed causing the application to fail during provider 
| registration. This snippet simply short circuits artisan and runs the
| same functionality as the command, but without Application creation.
| This doesnt remove the actual clear-compiled command from artisan, as
| using the Artisan::call() method would still result in the command being 
| used and not this.
|
*/
if (php_sapi_name() == 'cli' && isset($_SERVER['argv'])) {
    $argv = $_SERVER['argv'];
    if (in_array('artisan', $argv) && in_array('clear-compiled', $argv)) {
        $files = glob('./bootstrap/cache/*');
        foreach ($files as $file) {
            if (file_exists($file)) {
                @unlink($file);
            }
        }
        exit();
    }
}

/*
|--------------------------------------------------------------------------
| Include The Compiled Class File
|--------------------------------------------------------------------------
|
| To dramatically increase your application's performance, you may use a
| compiled class file which contains all of the classes commonly used
| by a request. The Artisan "optimize" is used to create this file.
|
*/

$compiledPath = __DIR__.'/cache/compiled.php';

if (file_exists($compiledPath)) {
    require $compiledPath;
}
