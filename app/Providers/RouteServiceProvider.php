<?php

namespace App\Providers;

use App\Http\Utils\RouteDefiner;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use ReflectionClass;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     */
    public const HOME = '/';

    public function map(): void
    {
        $routes = [];

        $handlers = Finder::create()->files()->in(base_path('app/Http/*/Handlers'));
        foreach ($handlers as $file) {
            $className = $this->fullyQualifiedName($file);

            $reflection = new ReflectionClass($className);
            if ($reflection->isInstantiable() && $reflection->implementsInterface(RouteDefiner::class)) {
                $routeOrder = $this->app->call([$className, 'routePriority']);
                $routes[$className] = $routeOrder;
            }
        }

        arsort($routes, SORT_NUMERIC);

        foreach ($routes as $className => $order) {
            $this->app->call([$className, 'defineRoute']);
        }
    }

    private function fullyQualifiedName(SplFileInfo $file): string
    {
        $namespace = ucfirst(str_replace('/', '\\', substr($file->getPath(), strlen(base_path()) + 1)));
        $class = $file->getFilenameWithoutExtension();

        return "$namespace\\$class";
    }
}
