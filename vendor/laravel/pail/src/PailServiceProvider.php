<?php

namespace Laravel\Pail;

use Illuminate\Console\Events\CommandStarting;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Log\Events\MessageLogged;
use Illuminate\Queue\Events\JobExceptionOccurred;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Env;
use Illuminate\Support\ServiceProvider;
use Laravel\Pail\Console\Commands\PailCommand;

class PailServiceProvider extends ServiceProvider
{
    /**
     * Registers the application services.
     */
    public function register(): void
    {
        $this->app->singleton(
            Files::class,
            fn (Application $app) => new Files($app->storagePath('pail'))
        );

        $this->app->singleton(Handler::class, fn (Application $app) => new Handler(
            $app,
            $app->make(Files::class),
            $app->runningInConsole(),
        ));
    }

    /**
     * Bootstraps the application services.
     */
    public function boot(): void
    {
        if (! $this->runningPailTests() && ($this->app->runningUnitTests() || ($_ENV['VAPOR_SSM_PATH'] ?? false))) {
            return;
        }

        /** @var Dispatcher $events */
        $events = $this->app->make('events');

        $events->listen(MessageLogged::class, function (MessageLogged $messageLogged) {
            /** @var Handler $handler */
            $handler = $this->app->make(Handler::class);

            $handler->log($messageLogged);
        });

        $events->listen([CommandStarting::class, JobProcessing::class, JobExceptionOccurred::class], function (CommandStarting|JobProcessing|JobExceptionOccurred $lifecycleEvent) {
            /** @var Handler $handler */
            $handler = $this->app->make(Handler::class);

            $handler->setLastLifecycleEvent($lifecycleEvent);
        });

        $events->listen([JobProcessed::class], function () {
            /** @var Handler $handler */
            $handler = $this->app->make(Handler::class);

            $handler->setLastLifecycleEvent(null);
        });

        if ($this->app->runningInConsole()) {
            $this->commands([
                PailCommand::class,
            ]);
        }
    }

    /**
     * Determines if the Pail's test suite is running.
     */
    protected function runningPailTests(): bool
    {
        return (bool) (Env::get('PAIL_TESTS') ?? false);
    }
}
