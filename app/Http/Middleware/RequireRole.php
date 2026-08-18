<?php
namespace App\Http\Middleware;
use Closure; use Illuminate\Http\Request; use Symfony\Component\HttpFoundation\Response;
class RequireRole { public function handle(Request $request, Closure $next, string ...$roles): Response { abort_unless(in_array($request->user()?->role?->value,$roles,true),403); return $next($request); } }
