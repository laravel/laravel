<?php

namespace App\Http\Middleware;

use App\Models\UpdateSchedule;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnforceUpdateScheduleMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || $user->isAdmin()) {
            return $next($request);
        }

        if ($request->isMethod('GET') || $request->isMethod('HEAD') || $request->isMethod('OPTIONS')) {
            return $next($request);
        }

        if (UpdateSchedule::isReadOnlyForUser($user)) {
            return redirect()->back()->with('error', 'Periode update data telah berakhir. Akun Anda saat ini dalam mode read-only.');
        }

        return $next($request);
    }
}
