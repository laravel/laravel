<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\FonnteService;
use App\Services\SupplierManager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessSupplierOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** Max attempts before marking as failed */
    public int $tries = 3;

    /** Timeout per attempt (seconds) */
    public int $timeout = 90;

    /** Wait between retries (seconds) */
    public array $backoff = [30, 60];

    public function __construct(
        protected int    $orderId,
        protected string $refId
    ) {}

    public function handle(SupplierManager $supplier, FonnteService $fonnte): void
    {
        $order = Order::with(['customer', 'product.category'])->find($this->orderId);

        if (! $order) {
            Log::error('[ProcessSupplierOrder] Order not found', ['order_id' => $this->orderId]);
            return;
        }

        // Skip if already handled
        if (in_array($order->status, ['success', 'failed', 'expired'])) {
            Log::info('[ProcessSupplierOrder] Already handled, skip', [
                'order_id' => $this->orderId,
                'status'   => $order->status,
            ]);
            return;
        }

        $order->update(['status' => 'processing']);

        // Get per-supplier codes if set
        $supplierCodes = is_array($order->product->supplier_codes)
            ? $order->product->supplier_codes
            : [];

        Log::info('[ProcessSupplierOrder] Processing', [
            'order_id' => $this->orderId,
            'ref_id'   => $this->refId,
            'product'  => $order->product->name,
        ]);

        $result = $supplier->order(
            sku: $order->product->supplier_code,
            target: $order->target_id,
            zone: $order->target_zone,
            refId: $this->refId,
            supplierCodes: $supplierCodes
        );

        Log::info('[ProcessSupplierOrder] Supplier result', [
            'order_id' => $this->orderId,
            'success'  => $result['success'],
            'pending'  => $result['pending'],
            'driver'   => $result['driver'] ?? 'none',
            'status'   => $result['status'],
        ]);

        if ($result['success']) {
            // ✅ Topup berhasil
            $order->update(['status' => 'success']);

            $sn = $result['sn'];
            $fonnte->sendMessage(
                $order->customer->phone,
                "✅ *Topup Berhasil!*\n\n" .
                "📦 Order: *ARC-{$order->id}*\n" .
                "🎮 Game: {$order->product->category->name}\n" .
                "💎 Produk: {$order->product->name}\n" .
                "🎯 Target: {$order->target_id}" . ($order->target_zone ? " | Server: {$order->target_zone}" : '') . "\n" .
                ($sn ? "🔑 SN: `{$sn}`\n" : '') .
                "\nTerima kasih! Main terus ya 🎮⭐"
            );

        } elseif ($result['pending']) {
            // ⏳ Masih pending di supplier — akan dicek ulang oleh scheduler
            Log::info('[ProcessSupplierOrder] Still pending at supplier', ['ref_id' => $this->refId]);
            // Biarkan status tetap 'processing'
            // Scheduler (CheckPendingOrders) akan cek ulang

        } else {
            // ❌ Semua supplier gagal
            $order->update(['status' => 'failed']);

            $fonnte->sendMessage(
                $order->customer->phone,
                "❌ *Topup Gagal*\n\n" .
                "📦 Order: *ARC-{$order->id}*\n" .
                "💎 Produk: {$order->product->name}\n\n" .
                "Maaf, proses topup gagal. Tim kami akan menghubungi kamu untuk *refund* dalam 1x24 jam.\n\n" .
                "📞 Butuh bantuan segera? Chat admin."
            );
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('[ProcessSupplierOrder] Job permanently failed', [
            'order_id' => $this->orderId,
            'error'    => $exception->getMessage(),
        ]);

        $order = Order::with('customer')->find($this->orderId);
        if ($order && ! in_array($order->status, ['success', 'failed'])) {
            $order->update(['status' => 'failed']);

            try {
                $fonnte = app(FonnteService::class);
                $fonnte->sendMessage(
                    $order->customer->phone,
                    "⚠️ *Order ARC-{$order->id}* mengalami kendala teknis.\n\n" .
                    "Tim kami akan menghubungi kamu untuk konfirmasi refund."
                );
            } catch (\Exception $e) {
                Log::error('[ProcessSupplierOrder] Failed to notify customer', ['error' => $e->getMessage()]);
            }
        }
    }
}
