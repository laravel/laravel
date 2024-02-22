<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Controller;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AllowLanguages
{
    private $controller;

    public function __construct(Controller $controller)
    {
        $this->controller = $controller;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $default_lang_code = app()->getLocale() ?? 'en';
        $language_allowed = $this->controller->allowLanguages();
        $language = request()->header()['x-language'][0] ?? $default_lang_code;
        $language = in_array($language, $language_allowed) ? $language : $default_lang_code;
        app()->setLocale($language);

        return $next($request);
    }
}
