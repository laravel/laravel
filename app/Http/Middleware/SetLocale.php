<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    /**
     * تحديد لغة التطبيق بناءً على الجلسة أو طلب المستخدم.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // تحديد اللغة من الجلسة إذا كانت موجودة
        if (Session::has('locale') && in_array(Session::get('locale'), config('app.available_locales', ['ar']))) {
            App::setLocale(Session::get('locale'));
        }
        // تحديد اللغة من المتصفح إذا كانت مدعومة
        elseif ($request->hasHeader('Accept-Language')) {
            $locale = substr($request->header('Accept-Language'), 0, 2);
            if (in_array($locale, config('app.available_locales', ['ar']))) {
                App::setLocale($locale);
                Session::put('locale', $locale);
            }
        }

        return $next($request);
    }
}
