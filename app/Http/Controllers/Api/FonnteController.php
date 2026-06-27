<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Transaction;
use App\Services\FonnteService;
use App\Services\TripayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FonnteController extends Controller
{
    protected FonnteService $fonnte;
    protected TripayService $tripay;

    public function __construct(FonnteService $fonnte, TripayService $tripay)
    {
        $this->fonnte = $fonnte;
        $this->tripay = $tripay;
    }

    public function webhook(Request $request)
    {
        $phone = $request->input('sender');
        $message = strtolower(trim($request->input('message')));
        $name = $request->input('name');

        Log::info('Fonnte webhook received', [
            'phone' => $phone,
            'message' => $message,
        ]);

        $customer = Customer::firstOrCreate(
            ['phone' => $phone],
            ['name' => $name]
        );

        return match (true) {
            $message === 'halo', $message === 'hi', $message === 'menu', $message === 'start' => $this->sendMenu($customer),
            $message === 'list' => $this->sendGameList($customer),
            str_starts_with($message, 'order') => $this->handleOrderCommand($customer, $message),
            str_starts_with($message, 'cek') => $this->checkOrderStatus($customer, $message),
            str_starts_with($message, 'batal') => $this->cancelOrder($customer, $message),
            str_starts_with($message, 'bantuan') => $this->sendHelp($customer),
            default => $this->sendUnknownCommand($customer),
        };
    }

    private function sendMenu(Customer $customer)
    {
        $menu = "🎮 *ArcanePay — Topup Game*\n\nHalo *{$customer->name}*! 👋\n\nBerikut menu yang tersedia:\n\n📋 *list* — Lihat daftar game\n🛒 *order [kode_game]* — Mulai order\n🔍 *cek [kode_order]* — Cek status order\n❌ *batal [kode_order]* — Batalkan order\n❓ *bantuan* — Panduan penggunaan\n\nKetik *list* untuk melihat game yang tersedia.";

        $this->fonnte->sendMessage($customer->phone, $menu);
        return response()->json(['status' => 'ok']);
    }

    private function sendGameList(Customer $customer)
    {
        $categories = Category::where('status', true)->get();

        if ($categories->isEmpty()) {
            $this->fonnte->sendMessage($customer->phone, "❌ Belum ada game yang tersedia. Silakan coba lagi nanti.");
            return response()->json(['status' => 'ok']);
        }

        $list = "📋 *Daftar Game Tersedia*\n\n";
        foreach ($categories as $index => $cat) {
            $list .= "*{$cat->id}*. {$cat->name}\n";
        }
        $list .= "\nKetik: *order [nomor_game]*\nContoh: *order 1*";

        $this->fonnte->sendMessage($customer->phone, $list);
        return response()->json(['status' => 'ok']);
    }

    private function handleOrderCommand(Customer $customer, string $message)
    {
        $parts = explode(' ', $message);
        
        if (count($parts) < 2) {
            $this->fonnte->sendMessage($customer->phone, 
                "⚠️ Format salah!\n\nKetik: *order [nomor_game]*\nContoh: *order 1*\n\nKetik *list* untuk melihat daftar game."
            );
            return response()->json(['status' => 'ok']);
        }

        $gameId = (int) $parts[1];
        $category = Category::find($gameId);

        if (!$category) {
            $this->fonnte->sendMessage($customer->phone, "❌ Game tidak ditemukan. Ketik *list* untuk melihat daftar game.");
            return response()->json(['status' => 'ok']);
        }

        $products = Product::where('category_id', $category->id)
            ->where('status', true)
            ->get();

        if ($products->isEmpty()) {
            $this->fonnte->sendMessage($customer->phone, "❌ Belum ada produk untuk game {$category->name}.");
            return response()->json(['status' => 'ok']);
        }

        $list = "🎮 *{$category->name}*\n\nPilih nominal:\n";
        foreach ($products as $product) {
            $list .= "*{$product->id}*. {$product->name} — Rp " . number_format($product->sell_price, 0, ',', '.') . "\n";
        }
        $list .= "\nKetik: *order {$category->id} [nomor_produk] [user_id]*\nContoh: *order 1 5 12345678*";

        $this->fonnte->sendMessage($customer->phone, $list);
        return response()->json(['status' => 'ok']);
    }

    private function checkOrderStatus(Customer $customer, string $message)
    {
        $parts = explode(' ', $message);
        
        if (count($parts) < 2) {
            $this->fonnte->sendMessage($customer->phone, 
                "⚠️ Format salah!\n\nKetik: *cek [kode_order]*\nContoh: *cek ARC-123*"
            );
            return response()->json(['status' => 'ok']);
        }

        $orderCode = $parts[1];
        $order = Order::where('payment_ref', 'like', "%{$orderCode}%")
            ->orWhere('id', $orderCode)
            ->where('customer_id', $customer->id)
            ->first();

        if (!$order) {
            $this->fonnte->sendMessage($customer->phone, "❌ Order tidak ditemukan.");
            return response()->json(['status' => 'ok']);
        }

        $statusEmoji = match ($order->status) {
            'pending' => '⏳',
            'paid' => '💰',
            'processing' => '⚙️',
            'success' => '✅',
            'failed' => '❌',
            'expired' => '⌛',
            default => '❓',
        };

        $statusText = match ($order->status) {
            'pending' => 'Menunggu Pembayaran',
            'paid' => 'Pembayaran Diterima',
            'processing' => 'Diproses ke Supplier',
            'success' => 'Berhasil',
            'failed' => 'Gagal',
            'expired' => 'Kadaluarsa',
            default => 'Unknown',
        };

        $text = "📦 *Detail Order*\n\n";
        $text .= "Order ID: *ARC-{$order->id}*\n";
        $text .= "Game: {$order->product->category->name}\n";
        $text .= "Produk: {$order->product->name}\n";
        $text .= "Target ID: {$order->target_id}\n";
        $text .= "Total: Rp " . number_format($order->amount, 0, ',', '.') . "\n";
        $text .= "Status: {$statusEmoji} *{$statusText}*\n";
        $text .= "Waktu: {$order->created_at->format('d M Y H:i')}\n";

        $this->fonnte->sendMessage($customer->phone, $text);
        return response()->json(['status' => 'ok']);
    }

    private function cancelOrder(Customer $customer, string $message)
    {
        $parts = explode(' ', $message);
        
        if (count($parts) < 2) {
            $this->fonnte->sendMessage($customer->phone, "⚠️ Format: *batal [kode_order]*");
            return response()->json(['status' => 'ok']);
        }

        $orderCode = $parts[1];
        $order = Order::where('id', $orderCode)
            ->where('customer_id', $customer->id)
            ->where('status', 'pending')
            ->first();

        if (!$order) {
            $this->fonnte->sendMessage($customer->phone, "❌ Order tidak ditemukan atau sudah tidak bisa dibatalkan.");
            return response()->json(['status' => 'ok']);
        }

        $order->update(['status' => 'expired']);
        $this->fonnte->sendMessage($customer->phone, "✅ Order *ARC-{$order->id}* berhasil dibatalkan.");
        return response()->json(['status' => 'ok']);
    }

    private function sendHelp(Customer $customer)
    {
        $help = "❓ *Panduan ArcanePay*\n\n*Cara Order:*\n1. Ketik *list* untuk lihat game\n2. Ketik *order [nomor_game]*\n3. Pilih nominal\n4. Masukkan User ID\n5. Bayar via QRIS/Transfer\n6. Tunggu konfirmasi otomatis\n\n*Format Order Lengkap:*\n*order [game] [produk] [user_id]*\nContoh: *order 1 5 12345678*\n\n*Cek Status:*\n*cek [kode_order]*\n\n*Catatan:*\n• Pastikan User ID benar\n• Order tidak bisa dibatalkan setelah dibayar\n• Proses 1-5 menit setelah pembayaran\n\nButuh bantuan? Chat admin langsung.";

        $this->fonnte->sendMessage($customer->phone, $help);
        return response()->json(['status' => 'ok']);
    }

    private function sendUnknownCommand(Customer $customer)
    {
        $this->fonnte->sendMessage($customer->phone, 
            "❓ Perintah tidak dikenali.\n\nKetik *menu* untuk melihat daftar perintah."
        );
        return response()->json(['status' => 'ok']);
    }
}
