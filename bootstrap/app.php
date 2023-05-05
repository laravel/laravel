<?php

use Illuminate\Foundation\Application;

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new Laravel application instance
| which serves as the "glue" for all the components of Laravel, and is
| the service container that can resolve all classes and components.
|
*/

return Application::create()
    ->withExceptionHandling(function ($handler) {
        //
    });
