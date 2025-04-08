<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\Helpers\CurrencyHelper;

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
        // إضافة دالة تنسيق السعر كمساعد Blade
        Blade::directive('formatPrice', function ($expression) {
            return "<?php echo \App\Helpers\CurrencyHelper::formatPrice($expression); ?>";
        });
    }
}
