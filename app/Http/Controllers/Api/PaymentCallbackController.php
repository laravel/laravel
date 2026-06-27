<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Transaction;
use App\Services\FonnteService;
use App\Services\SupplierService;
use App\Services\TripayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentCallbackController extends Controller
{
    public function handle(Request $request)
    {
        $jsonPayload = $request->getContent();
        $callbackSignature = $request->header('X-Callback-Signature');

        Log::info('Payment callback received', [
            'signature' => $callbackSignature ? 'present' : 'missing',
        ]);

        $tripay = new TripayService();

        if (!$tripay->verifyCallback($jsonPayload, $callbackSignature)) {
            Log::warning('Invalid callback signature');
            return response()->json(['error' => 'Invalid signature'], 403);
        }

        $data = json_decode($jsonPayload, true);
        $status = $data['status'] ?? 'UNKNOWN';
        $reference = $data['reference'] ?? '';

        Log::info('Payment callback processed', [
            'reference' => $reference,
            'status' => $status,
        ]);

        $transaction = Transaction::where('reference', $reference)->first();
        if (!$transaction) {
            Log::error('Transaction not found', ['reference' => $reference]);
            return response()->json(['error' => 'Not found'], 404);
        }

        $order = Order::with(['customer', 'product.category'])->find($transaction->order_id);
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        $fonnte = new FonnteService();
        $supplier = new SupplierService();

        if ($status === 'PAID') {
            $transaction->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);
            $order->update(['status' => 'processing']);

            $fonnte->sendMessage($order->customer->phone,
                "💰 *Pembayaran Berhasil!*\n\n" .
                "Order: *ARC-{$order->id}*\n" .
                "Game: {$order->product->category->name}\n" .
                "Produk: {$order->product->name}\n" .
                "Status: *Diproses ke supplier* ⚙️\n" .
                "Estimasi: 1-5 menit\n\n" .
                "Mohon ditunggu ya! 🎮"
            );

            $refId = 'ARC-' . $order->id . '-' . time();
            $supplierResponse = $supplier->order(
                $order->product->supplier_code,
                $order->target_id,
                $order->target_zone,
                $refId
            );

            $supplierStatus = $supplierResponse['data']['status'] ?? 'Gagal';

            if (in_array($supplierStatus, ['Sukses', 'Success'])) {
                $order->update([
                    'status' => 'success',
                    'supplier_ref' => $refId,
                ]);

                $fonnte->sendMessage($order->customer->phone,
                    "✅ *Order Selesai!*\n\n" .
                    "Order: *ARC-{$order->id}*\n" .
                    "Game: {$order->product->category->name}\n" .
                    "Produk: {$order->product->name}\n" .
                    "Status: *BERHASIL* 🎉\n\n" .
                    "Terima kasih telah menggunakan ArcanePay!\n" .
                    "Rate kami ⭐⭐⭐⭐⭐"
                );
            } else {
                $order->update(['status' => 'processing']);
                Log::warning('Supplier pending', [
                    'order_id' => $order->id,
                    'supplier_status' => $supplierStatus,
                ]);

                $fonnte->sendMessage($order->customer->phone,
                    "⏳ *Order Diproses*\n\n" .
                    "Order: *ARC-{$order->id}*\n" .
                    "Status: *Menunggu konfirmasi supplier*\n" .
                    "Akan diupdate otomatis. Mohon ditunggu."
                );
            }
        } elseif (in_array($status, ['EXPIRED', 'FAILED'])) {
            $transaction->update(['status' => strtolower($status)]);
            $order->update(['status' => 'expired']);

            $fonnte->sendMessage($order->customer->phone,
                "❌ *Pembayaran Gagal/Kadaluarsa*\n\n" .
                "Order: *ARC-{$order->id}*\n" .
                "Status: *{$status}*\n\n" .
                "Silakan order ulang dengan ketik *list*"
            );
        }

        return response()->json(['success' => true]);
    }
}
