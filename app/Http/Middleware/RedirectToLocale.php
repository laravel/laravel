<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Request;

class RedirectToLocale
{
    /**
     * Handle an incoming request.
     * When visiting root URL / redirects to locale if required
     * Also determines preferred language
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Request::segment(1) === null) {
            $pref_lc = Request::getPreferredLanguage(App::getSupportedLocales());
            if ($pref_lc === App::getDefaultLocale() && !App::isDefaultLocaleInUrl()) {
                // do nothing for default locale when default locale is not in URL
            } else {
                return redirect(trans_url($pref_lc));
            }
        }

        return $next($request);
    }
}
