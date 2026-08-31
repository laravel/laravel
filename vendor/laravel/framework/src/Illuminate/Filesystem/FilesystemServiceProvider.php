<?php

namespace Illuminate\Filesystem;

use Illuminate\Contracts\Foundation\CachesRoutes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class FilesystemServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the filesystem.
     *
     * @return void
     */
    public function boot()
    {
        $this->serveFiles();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerNativeFilesystem();
        $this->registerFlysystem();
    }

    /**
     * Register the native filesystem implementation.
     *
     * @return void
     */
    protected function registerNativeFilesystem()
    {
        $this->app->singleton('files', function () {
            return new Filesystem;
        });
    }

    /**
     * Register the driver based filesystem.
     *
     * @return void
     */
    protected function registerFlysystem()
    {
        $this->registerManager();

        $this->app->singleton('filesystem.disk', function ($app) {
            return $app['filesystem']->disk($this->getDefaultDriver());
        });

        $this->app->singleton('filesystem.cloud', function ($app) {
            return $app['filesystem']->disk($this->getCloudDriver());
        });
    }

    /**
     * Register the filesystem manager.
     *
     * @return void
     */
    protected function registerManager()
    {
        $this->app->singleton('filesystem', function ($app) {
            return new FilesystemManager($app);
        });
    }

    /**
     * Register protected file serving.
     *
     * @return void
     */
    protected function serveFiles()
    {
        if ($this->app instanceof CachesRoutes && $this->app->routesAreCached()) {
            return;
        }

        foreach ($this->app['config']['filesystems.disks'] ?? [] as $disk => $config) {
            if (! $this->shouldServeFiles($config)) {
                continue;
            }

            $this->app->booted(function ($app) use ($disk, $config) {
                $uri = isset($config['url'])
                    ? rtrim(parse_url($config['url'])['path'], '/')
                    : '/storage';

                $isProduction = $app->isProduction();

                Route::get($uri.'/{path}', function (Request $request, string $path) use ($disk, $config, $isProduction) {
                    return (new ServeFile(
                        $disk,
                        $config,
                        $isProduction
                    ))($request, $path);
                })->where('path', '.*')->name('storage.'.$disk);

                Route::put($uri.'/{path}', function (Request $request, string $path) use ($disk, $config, $isProduction) {
                    return (new ReceiveFile(
                        $disk,
                        $config,
                        $isProduction
                    ))($request, $path);
                })->where('path', '.*')->name('storage.'.$disk.'.upload');
            });
        }
    }

    /**
     * Determine if the disk is serveable.
     *
     * @param  array  $config
     * @return bool
     */
    protected function shouldServeFiles(array $config)
    {
        return $config['driver'] === 'local' && ($config['serve'] ?? false);
    }

    /**
     * Get the default file driver.
     *
     * @return string
     */
    protected function getDefaultDriver()
    {
        return $this->app['config']['filesystems.default'];
    }

    /**
     * Get the default cloud based file driver.
     *
     * @return string
     */
    protected function getCloudDriver()
    {
        return $this->app['config']['filesystems.cloud'];
    }
}
