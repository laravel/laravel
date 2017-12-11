<?php

namespace App\Providers;

use DirectoryIterator;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        $this->mapDir('web');

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));
    }

    /**
     * Parser for folders for route files.
     *
     * @param $name
     * @param string $prefix
     */
    protected function mapDir($name, $prefix = '')
    {
        $path = base_path('routes/' . $name);
        $iterator = new DirectoryIterator($path);
        $this->registerRoutesDirectory($iterator, $name, $prefix);
    }

    /**
     * Register the files in  folder
     *
     * @param DirectoryIterator $iterator
     * @param $middleware
     * @param string $prefix
     */
    protected function registerRoutesDirectory(DirectoryIterator $iterator, $middleware, $prefix = '')
    {
        foreach ($iterator AS $item) {
            switch (true) {
                case $item->isDot():
                    continue;
                    break;
                case $item->isDir():
                    $this->registerRoutesDirectory(new DirectoryIterator($item->getPath() . '/' . $item->getFilename()), $middleware, $prefix);
                    break;
                default:
                    $this->registerRoutesFile($item, $middleware, $prefix);
                    break;
            }
        }
    }

    /**
     * Register the specific Route file.
     *
     * @param DirectoryIterator $iterator
     * @param $middleware
     * @param string $prefix
     */
    protected function registerRoutesFile(DirectoryIterator $iterator, $middleware = '', $prefix = '')
    {
        Route::group([
            'middleware' => $middleware,
            'namespace' => $this->namespace,
            'prefix' => $prefix
        ], function () use ($iterator) {
            require $iterator->getPathname();
        });
    }

}
