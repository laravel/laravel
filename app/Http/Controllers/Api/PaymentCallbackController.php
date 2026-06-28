<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessSupplierOrder;
use App\Models\Order;
use App\Models\Transaction;
use App\Services\FonnteService;
use App\Services\TripayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentCallbackController extends Controller
{
    public function handle(Request $request)
    {
        $json      = $request->getContent();
        $signature = $request->header('X-Callback-Signature', '');

        Log::info('[Callback] Received', [
            'ip'        => $request->ip(),
            'signature' => $signature ? 'present' : 'missing',
        ]);

        // ── Verify Tripay signature ─────────────────────────────────────────
        $tripay = app(TripayService::class);

        if (! $tripay->verifyCallback($json, $signature)) {
            Log::warning('[Callback] Invalid signature rejected', ['ip' => $request->ip()]);
            return response()->json(['error' => 'Invalid signature'], 403);
        }

        $data      = json_decode($json, true);
        $status    = $data['status'] ?? 'UNKNOWN';
        $reference = $data['reference'] ?? '';

        Log::info('[Callback] Processing payment', [
            'reference' => $reference,
            'status'    => $status,
        ]);

        // ── Find Transaction ────────────────────────────────────────────────
        $transaction = Transaction::where('reference', $reference)->first();

        if (! $transaction) {
            Log::error('[Callback] Transaction not found', ['reference' => $reference]);
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        $order = Order::with(['customer', 'product.category'])->find($transaction->order_id);

        if (! $order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        $fonnte = app(FonnteService::class);

        // ── Handle Status ───────────────────────────────────────────────────
        if ($status === 'PAID') {
            // Prevent duplicate processing
            if (in_array($order->status, ['paid', 'processing', 'success'])) {
                Log::info('[Callback] Already processed', ['order_id' => $order->id]);
                return response()->json(['success' => true]);
            }

            $transaction->update([
                'status'  => 'paid',
                'paid_at' => now(),
            ]);
            $order->update(['status' => 'paid']);

            // Notify customer: payment received
            $fonnte->sendMessage(
                $order->customer->phone,
                "💰 *Pembayaran Diterima!*\n\n" .
                "📦 Order: *ARC-{$order->id}*\n" .
                "🎮 Game: {$order->product->category->name}\n" .
                "💎 Produk: {$order->product->name}\n\n" .
                "⚙️ Sedang memproses topup ke akunmu...\n" .
                "Estimasi: *1-5 menit*. Mohon tunggu! 🎮"
            );

            // Generate supplier ref & dispatch job
            $refId = 'ARC-' . $order->id . '-' . time();
            $order->update(['supplier_ref' => $refId]);

            ProcessSupplierOrder::dispatch($order->id, $refId);

            Log::info('[Callback] Job dispatched', [
                'order_id' => $order->id,
                'ref_id'   => $refId,
            ]);

        } elseif (in_array($status, ['EXPIRED', 'FAILED'])) {
            if ($order->status === 'pending') {
                $transaction->update(['status' => strtolower($status)]);
                $order->update(['status' => 'expired']);

                $fonnte->sendMessage(
                    $order->customer->phone,
                    "❌ *Pembayaran " . ($status === 'EXPIRED' ? 'Kadaluarsa' : 'Gagal') . "*\n\n" .
                    "📦 Order: *ARC-{$order->id}*\n\n" .
                    "Silakan order ulang. Ketik *list* untuk mulai."
                );
            }
        } else {
            Log::warning('[Callback] Unknown status', ['status' => $status, 'reference' => $reference]);
        }

        return response()->json(['success' => true]);
    }
}
