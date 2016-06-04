<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class LocaleServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $segment = Request::segment(1);
        $locale = Url::segmentToLocale($segment);
        if (!App::isLocaleSupported($locale)) {
            $locale = App::getDefaultLocale();
        }

        // set PHP, Application, 3rd party locale
        // custom localization logic can be added here
        setlocale(LC_ALL, $locale);
        App::setLocale($locale);
        Carbon::setLocale(Url::localeToLang($locale));
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }
}
