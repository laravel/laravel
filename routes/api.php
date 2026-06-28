<?php

use App\Http\Controllers\Api\FonnteController;
use App\Http\Controllers\Api\PaymentCallbackController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| ArcanePay API Routes
|--------------------------------------------------------------------------
| Base prefix: /api (auto-prefixed by Laravel)
|
| Public endpoints:
|   GET  /api/health           → health check (used by UptimeRobot)
|   GET  /api/games            → game catalog (for future frontend)
|
| Webhook endpoints:
|   POST /api/fonnte/webhook   → WhatsApp message handler (Fonnte)
|   POST /api/payment/callback → Payment notification (Tripay)
|
*/

// ── Health Check ──────────────────────────────────────────────────────────────
Route::get('/health', function () {
    return response()->json([
        'status'    => 'ok',
        'app'       => config('app.name'),
        'version'   => '1.0.0',
        'timestamp' => now()->toIso8601String(),
    ]);
});

// ── Public Game Catalog ───────────────────────────────────────────────────────
Route::get('/games', function () {
    $categories = \App\Models\Category::where('status', true)
        ->with(['products' => fn ($q) => $q->where('status', true)->orderBy('sell_price')])
        ->orderBy('id')
        ->get();

    return response()->json([
        'success' => true,
        'data'    => $categories->map(fn ($cat) => [
            'id'           => $cat->id,
            'name'         => $cat->name,
            'slug'         => $cat->slug,
            'icon'         => $cat->icon,
            'need_zone'    => $cat->need_zone ?? false,
            'zone_label'   => $cat->zone_label ?? 'Server ID',
            'target_label' => $cat->target_label ?? 'User ID',
            'products'     => $cat->products->map(fn ($p) => [
                'id'         => $p->id,
                'name'       => $p->name,
                'sell_price' => $p->sell_price,
            ]),
        ]),
    ]);
});

// ── WhatsApp Webhook (Fonnte) ─────────────────────────────────────────────────
// Protected by VerifyFonnteWebhook middleware
// Setup: Fonnte Dashboard → Webhook URL →
//   https://api.arcanepay.biz.id/api/fonnte/webhook?token={FONNTE_SECRET}
Route::post('/fonnte/webhook', [FonnteController::class, 'webhook'])
    ->middleware('fonnte.verify');

// ── Payment Callback (Tripay) ─────────────────────────────────────────────────
// Tripay verifies its own signature internally (X-Callback-Signature header)
// Setup: Tripay Dashboard → Merchant → Callback URL →
//   https://api.arcanepay.biz.id/api/payment/callback
Route::post('/payment/callback', [PaymentCallbackController::class, 'handle']);
