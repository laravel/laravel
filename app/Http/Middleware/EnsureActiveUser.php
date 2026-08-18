<?php
namespace App\Http\Middleware;
use Closure; use Illuminate\Http\Request; use Symfony\Component\HttpFoundation\Response;
class EnsureActiveUser { public function handle(Request $request, Closure $next): Response { abort_unless($request->user()?->is_active,403,'Account is disabled.'); if ($request->user()->company_id) abort_unless($request->user()->company?->is_active,403,'Company is disabled.'); $token=$request->user()->currentAccessToken(); if ($token && $token->device_id) abort_if(\App\Models\Device::withoutGlobalScopes()->find($token->device_id)?->disabled_at,403,'This device has been disabled.'); return $next($request); } }
