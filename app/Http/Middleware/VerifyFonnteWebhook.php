<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Verifikasi bahwa request webhook datang dari Fonnte.
 *
 * Setup:
 * 1. Isi FONNTE_SECRET di .env dengan string acak yang kamu buat sendiri
 * 2. Di dashboard Fonnte → Webhook URL, tambahkan ?token=xxx
 *    Contoh: https://api.arcanepay.biz.id/api/fonnte/webhook?token=rahasia123
 *
 * Jika FONNTE_SECRET kosong, verifikasi dilewati (dev mode).
 */
class VerifyFonnteWebhook
{
    public function handle(Request $request, Closure $next): mixed
    {
        $secret = config('services.fonnte.secret');

        // Skip verification if secret not configured (local dev)
        if (empty($secret)) {
            return $next($request);
        }

        // Accept token from ?token= query param or X-Fonnte-Token header
        $provided = $request->query('token')
                 ?? $request->header('X-Fonnte-Token')
                 ?? '';

        if (! hash_equals($secret, $provided)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
