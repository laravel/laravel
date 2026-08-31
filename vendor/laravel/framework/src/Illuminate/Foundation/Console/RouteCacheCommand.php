<?php

namespace Illuminate\Foundation\Console;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Kernel as ConsoleKernelContract;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Routing\RouteCollection;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'route:cache')]
class RouteCacheCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'route:cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a route cache file for faster route registration';

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Create a new route command instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->callSilent('route:clear');

        $routes = $this->getFreshApplicationRoutes();

        if (count($routes) === 0) {
            return $this->components->error("Your application doesn't have any routes.");
        }

        foreach ($routes as $route) {
            $route->prepareForSerialization();
        }

        $this->files->put(
            $this->laravel->getCachedRoutesPath(), $this->buildRouteCacheFile($routes)
        );

        $this->components->info('Routes cached successfully.');
    }

    /**
     * Boot a fresh copy of the application and get the routes.
     *
     * @return \Illuminate\Routing\RouteCollection
     */
    protected function getFreshApplicationRoutes()
    {
        return tap($this->getFreshApplication()['router']->getRoutes(), function ($routes) {
            $routes->refreshNameLookups();
            $routes->refreshActionLookups();
        });
    }

    /**
     * Get a fresh application instance.
     *
     * @return \Illuminate\Contracts\Foundation\Application
     */
    protected function getFreshApplication()
    {
        return tap(require $this->laravel->bootstrapPath('app.php'), function ($app) {
            $app->make(ConsoleKernelContract::class)->bootstrap();
        });
    }

    /**
     * Build the route cache file.
     *
     * @param  \Illuminate\Routing\RouteCollection  $routes
     * @return string
     */
    protected function buildRouteCacheFile(RouteCollection $routes)
    {
        $stub = $this->files->get(__DIR__.'/stubs/routes.stub');

        return str_replace('{{routes}}', var_export($routes->compile(), true), $stub);
    }
}
