<?php

namespace App\Http\Middleware;

use App\Http\Requests\RolePostRequest;
use App\Models\User;
use Closure;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(RolePostRequest $request, Closure $next)
    {
        $payload = $request->all();
        $userRole = User::findOrFail($payload['user_id'])->role;
        if ($userRole->id == 1) {
            return $next($request);
        }
        return response("Unauthoraized user, should be Admin", 403);
    }
}
