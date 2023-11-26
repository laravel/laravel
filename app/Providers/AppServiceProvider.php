<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;


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
        /**
         * Set default string length for database schema.
         * Necessary for older MySQL/MariaDB versions to support utf8mb4 encoding (like emojis).
         * Ensures compatibility with index size limits (191 * 4 = 764 bytes).
         * Uncomment if using older database versions; newer versions handle this automatically.
         *
         * Before use add namespace: use Illuminate\Support\Facades\Schema;
         */
//        Schema::defaultStringLength(191);
    }
}
