<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class LocaleMiddleware
{
    /**
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $language = $request->getPreferredLanguage();
        if (array_key_exists($language, config('locale.languages'))) {
            app()->setLocale($language);
        }

        return $next($request);
    }
}
