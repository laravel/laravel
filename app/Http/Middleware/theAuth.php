<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;

class theAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::user()) {
            $status = 0;
            $message = 'Please login or register for further process';

            if ($request->ajax()) {
                return response()->json(['status_login' => $status, 'message' => $message]);
            } else {
                return redirect()->route('showLoginForm');
            }
        }

        return $next($request);
    }
}
