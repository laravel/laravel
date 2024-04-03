<?php

declare(strict_types=1);

use Illuminate\Foundation\Configuration\Middleware;
use Lightit\Application;
use Lightit\Shared\App\Console\CommandManager;
use Lightit\Shared\App\Exceptions\ExceptionHandler;

$exceptionManager = new ExceptionHandler();

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withEvents(discover: [
        __DIR__.'/../src/Shared/App/Listeners',
    ])
    ->withMiddleware(callback: function (Middleware $middleware) {
        //
    })
    ->withCommands(commands: CommandManager::getCommands())
    ->withExceptions(using: $exceptionManager->getClosure())
    ->create();
