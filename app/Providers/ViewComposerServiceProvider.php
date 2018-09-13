<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use EngageInteractive\LaravelFrontend\ConfigProvider;

class ViewComposerServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->make(ConfigProvider::class)->get('enabled')) {
            View::composer('app/*', 'App\Http\ViewComposers\FrontendViewComposer');
        }
    }
}
