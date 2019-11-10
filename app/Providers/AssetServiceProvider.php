<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\JsonManifestVersionStrategy;

class AssetServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton('asset', function () {
            $manifestPath = public_path('manifest.json');
            $strategy = new JsonManifestVersionStrategy($manifestPath);

            return new Package($strategy);
        });
    }
}
