<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use App\Traits\ApiResponses;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function(Throwable $e, Request $request){
            $className = get_class($e);
            $index = strrpos($className, '\\');

            return response()->json([
                'type' => substr($className, $index+1),
                'status' => 0,
                'message' => $e->getMessage()
            ]);
        });
    })->create();
