<?php

namespace App\Providers;

use App\Services\Ai\AiManager;
use Illuminate\Support\ServiceProvider;

class AiServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(AiManager::class, function (): AiManager {
            return new AiManager();
        });

        $this->app->alias(AiManager::class, 'ai');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

