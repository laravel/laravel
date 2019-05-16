<?php

namespace App\Providers;

use App\Http\Utils\OrderedRouteDefiner;
use App\Http\Utils\RouteDefiner;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Rap2hpoutre\LaravelLogViewer\LogViewerController;
use Symfony\Component\Finder\Finder;

class RouteServiceProvider extends ServiceProvider
{
    public function map()
    {
        if (config('app.debug')) {
            $this->app->get('router')->get('logs', LogViewerController::class . '@index');
        }

        $orderedRoutes = [];
        /** @var Finder $handlers */
        $handlers = Finder::create()->files()->in(app_path('Http/*/Handlers'));
        foreach ($handlers as $file) {
            $className = 'App' . str_replace([app_path(), '/', '.php'], ['', '\\', ''], $file);
            $class = new \ReflectionClass($className);

            if ($class->isSubclassOf(RouteDefiner::class)) {
                $routeOrder = 0;
                if ($class->isSubclassOf(OrderedRouteDefiner::class)) {
                    $routeOrder = $this->app->call([$className, 'getRouteOrder']);
                }

                $orderedRoutes[$className] = $routeOrder;
            }
        }

        asort($orderedRoutes, SORT_NATURAL);

        foreach ($orderedRoutes as $className => $order) {
            $this->app->call([$className, 'defineRoute']);
        }
    }
}
