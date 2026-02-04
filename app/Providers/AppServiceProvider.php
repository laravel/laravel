<?php

namespace App\Providers;

use App\Models\Material;
use App\Policies\MaterialPolicy;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register policies using Gate
        Gate::policy(Material::class, MaterialPolicy::class);
    }
}

