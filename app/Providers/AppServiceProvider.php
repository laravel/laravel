<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use App\Helpers\CurrencyHelper;
use App\Providers\MultilingualServiceProvider;
use Illuminate\Support\Facades\Auth;
use JavaScript;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register V2 service providers if features are enabled
        if (config('v2_features.multilingual.enabled')) {
            $this->app->register(MultilingualServiceProvider::class);
        }
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

        // Share V2 feature settings with all views
        View::share('v2Features', config('v2_features'));

        // Pass dark mode settings to JavaScript
        if (config('v2_features.dark_mode.enabled')) {
            View::composer('*', function ($view) {
                JavaScript::put([
                    'darkModeSettings' => config('v2_features.dark_mode'),
                    'userId' => Auth::id()
                ]);
            });
        }
    }
}
