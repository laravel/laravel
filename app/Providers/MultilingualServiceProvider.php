<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

class MultilingualServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if (Config::get('v2_features.multilingual.enabled')) {
            $this->setupLocale();
            $this->shareLocaleDataWithViews();
        }
    }

    /**
     * Setup the application locale based on session or default setting
     */
    protected function setupLocale(): void
    {
        $locales = Config::get('v2_features.multilingual.available_locales', ['ar']);
        $defaultLocale = Config::get('v2_features.multilingual.default_locale', 'ar');
        
        // Get locale from session or use default
        $locale = Session::get('locale', $defaultLocale);
        
        // Ensure locale is valid
        if (!in_array($locale, $locales)) {
            $locale = $defaultLocale;
        }
        
        // Set application locale
        App::setLocale($locale);
        
        // Set RTL/LTR direction
        $rtlLocales = ['ar'];
        Session::put('textDirection', in_array($locale, $rtlLocales) ? 'rtl' : 'ltr');
    }

    /**
     * Share locale data with all views
     */
    protected function shareLocaleDataWithViews(): void
    {
        View::composer('*', function ($view) {
            $view->with([
                'currentLocale' => App::getLocale(),
                'availableLocales' => Config::get('v2_features.multilingual.available_locales'),
                'textDirection' => Session::get('textDirection', 'rtl')
            ]);
        });
    }
}
