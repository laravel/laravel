<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureInstalled
{
    public function handle(Request $request, Closure $next): Response
    {
        $installed = false;
        if (file_exists(base_path('.env')) && config('app.key')) {
            try {
                $installed = (bool) optional(Setting::where('key','installed')->first())->value;
            } catch (QueryException $e) {
                $installed = false; // migrations not run yet
            }
        }

        if (!$installed && !$request->is('install*')) {
            return redirect()->to('/install');
        }

        return $next($request);
    }
}
