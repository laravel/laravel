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
    public function __construct(
        protected FonnteService $fonnte,
        protected TripayService $tripay
    ) {}

    // ─────────────────────────────────────────────────────────────────────────
    // WEBHOOK ENTRY POINT
    // ─────────────────────────────────────────────────────────────────────────

    public function webhook(Request $request)
    {
        $phone   = $request->input('sender', '');
        $message = strtolower(trim($request->input('message', '')));
        $name    = $request->input('name', 'Pelanggan');

        Log::info('[WA] Incoming', ['phone' => $phone, 'msg' => $message]);

        if (empty($phone) || empty($message)) {
            return response()->json(['status' => 'ignored']);
        }

        // Upsert customer
        $customer = Customer::firstOrCreate(
            ['phone' => $phone],
            ['name'  => $name]
        );
        if ($customer->name !== $name && ! empty($name) && $name !== 'Pelanggan') {
            $customer->update(['name' => $name]);
        }

        return match (true) {
            in_array($message, ['halo','hi','hello','hey','menu','start','mulai','hai'])
                => $this->sendMenu($customer),

            in_array($message, ['list','daftar','game'])
                => $this->sendGameList($customer),

            str_starts_with($message, 'order')
                => $this->handleOrder($customer, $message),

            str_starts_with($message, 'cek ')
                => $this->checkOrder($customer, $message),

            str_starts_with($message, 'batal ')
                => $this->cancelOrder($customer, $message),

            in_array($message, ['bantuan','help','cara','panduan','?'])
                => $this->sendHelp($customer),

            default
                => $this->sendUnknown($customer),
        };
    }

    // ─────────────────────────────────────────────────────────────────────────
    // INFO MESSAGES
    // ─────────────────────────────────────────────────────────────────────────

    private function sendMenu(Customer $customer)
    {
        $this->fonnte->sendMessage(
            $customer->phone,
            "🎮 *ArcanePay — Topup Game Cepat & Aman*\n\n" .
            "Halo *{$customer->name}*! 👋\n\n" .
            "📋 *list* — Lihat semua game\n" .
            "🛒 *order [no_game]* — Pilih nominal\n" .
            "🔍 *cek [no_order]* — Cek status pesanan\n" .
            "❌ *batal [no_order]* — Batalkan pesanan\n" .
            "❓ *bantuan* — Panduan lengkap\n\n" .
            "_Ketik *list* untuk mulai topup!_"
        );
        return response()->json(['status' => 'ok']);
    }

    private function sendGameList(Customer $customer)
    {
        $categories = Category::where('status', true)->orderBy('id')->get();

        if ($categories->isEmpty()) {
            $this->fonnte->sendMessage($customer->phone,
                "⚠️ Belum ada game tersedia. Coba lagi nanti."
            );
            return response()->json(['status' => 'ok']);
        }

        $text = "📋 *Daftar Game Tersedia*\n\n";
        foreach ($categories as $cat) {
            $zoneInfo = $cat->need_zone ? " _(butuh {$cat->zone_label})_" : '';
            $text .= "*{$cat->id}.* {$cat->name}{$zoneInfo}\n";
        }
        $text .= "\n➡️ Ketik: *order [no_game]*\nContoh: *order 1*";

        $this->fonnte->sendMessage($customer->phone, $text);
        return response()->json(['status' => 'ok']);
    }

    private function sendHelp(Customer $customer)
    {
        $this->fonnte->sendMessage(
            $customer->phone,
            "❓ *Panduan ArcanePay*\n\n" .
            "*📌 CARA ORDER:*\n" .
            "1️⃣ Ketik *list* → lihat game\n" .
            "2️⃣ Ketik *order [no]* → lihat nominal\n" .
            "3️⃣ Ketik perintah order lengkap\n" .
            "4️⃣ Bayar via link yang dikirim\n" .
            "5️⃣ Topup otomatis masuk 1-5 menit ✨\n\n" .
            "*📌 FORMAT ORDER:*\n" .
            "Game biasa:\n" .
            "*order [no_game] [no_nominal] [user_id]*\n" .
            "Contoh FF: *order 2 1 1234567890*\n\n" .
            "Game butuh Server ID (ML, Genshin, dll):\n" .
            "*order [no_game] [no_nominal] [user_id] [server_id]*\n" .
            "Contoh ML: *order 1 2 12345678 1234*\n\n" .
            "*📌 CEK ORDER:*\n" .
            "*cek [no_order]* → contoh: *cek 5*\n\n" .
            "*📌 BATAL ORDER:*\n" .
            "*batal [no_order]* → hanya bisa jika belum bayar\n\n" .
            "📞 Butuh bantuan? Hubungi admin."
        );
        return response()->json(['status' => 'ok']);
    }

    private function sendUnknown(Customer $customer)
    {
        $this->fonnte->sendMessage(
            $customer->phone,
            "❓ Perintah tidak dikenal.\n\nKetik *menu* untuk melihat daftar perintah."
        );
        return response()->json(['status' => 'ok']);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // ORDER FLOW
    // ─────────────────────────────────────────────────────────────────────────

    private function handleOrder(Customer $customer, string $message)
    {
        // Normalize & split — remove multiple spaces
        $parts = array_values(array_filter(explode(' ', $message)));
        $count = count($parts);
        // parts[0]='order' parts[1]=game_id parts[2]=product_id
        // parts[3]=user_id parts[4]=zone_id(optional)

        // "order" alone
        if ($count === 1) {
            $this->fonnte->sendMessage($customer->phone,
                "⚠️ Tambahkan nomor game.\n\nContoh: *order 1*\n\nKetik *list* untuk lihat daftar game."
            );
            return response()->json(['status' => 'ok']);
        }

        // "order [game_id]" → show products
        if ($count === 2) {
            return $this->showProducts($customer, (int) $parts[1]);
        }

        // "order [game_id] [product_id]" → show input guide
        if ($count === 3) {
            return $this->showInputGuide($customer, (int) $parts[1], (int) $parts[2]);
        }

        // "order [game_id] [product_id] [user_id] [?zone_id]"
        if ($count >= 4) {
            return $this->createOrder(
                customer: $customer,
                categoryId: (int) $parts[1],
                productId: (int) $parts[2],
                targetId: $parts[3],
                targetZone: $parts[4] ?? null
            );
        }

        return response()->json(['status' => 'ok']);
    }

    private function showProducts(Customer $customer, int $categoryId)
    {
        $category = Category::where('id', $categoryId)->where('status', true)->first();

        if (! $category) {
            $this->fonnte->sendMessage($customer->phone,
                "❌ Game tidak ditemukan.\n\nKetik *list* untuk lihat daftar game."
            );
            return response()->json(['status' => 'ok']);
        }

        $products = Product::where('category_id', $categoryId)
            ->where('status', true)
            ->orderBy('sell_price')
            ->get();

        if ($products->isEmpty()) {
            $this->fonnte->sendMessage($customer->phone,
                "⚠️ Produk untuk *{$category->name}* belum tersedia."
            );
            return response()->json(['status' => 'ok']);
        }

        $text = "🎮 *{$category->name}*\n\nPilih nominal:\n";
        foreach ($products as $p) {
            $price = number_format($p->sell_price, 0, ',', '.');
            $text .= "*{$p->id}.* {$p->name} — Rp {$price}\n";
        }

        $first = $products->first();

        if ($category->need_zone) {
            $text .= "\n⚠️ Butuh *{$category->target_label}* + *{$category->zone_label}*\n\n";
            $text .= "➡️ Format:\n*order {$categoryId} [no_nominal] [{$category->target_label}] [{$category->zone_label}]*\n";
            $text .= "Contoh:\n*order {$categoryId} {$first->id} 12345678 1234*";
        } else {
            $text .= "\n⚠️ Butuh *{$category->target_label}*\n\n";
            $text .= "➡️ Format:\n*order {$categoryId} [no_nominal] [{$category->target_label}]*\n";
            $text .= "Contoh:\n*order {$categoryId} {$first->id} 12345678*";
        }

        $this->fonnte->sendMessage($customer->phone, $text);
        return response()->json(['status' => 'ok']);
    }

    private function showInputGuide(Customer $customer, int $categoryId, int $productId)
    {
        $category = Category::where('id', $categoryId)->where('status', true)->first();
        $product  = Product::where('id', $productId)
            ->where('category_id', $categoryId)
            ->where('status', true)
            ->first();

        if (! $category || ! $product) {
            $this->fonnte->sendMessage($customer->phone,
                "❌ Produk tidak ditemukan.\n\nKetik *order {$categoryId}* untuk lihat daftar nominal."
            );
            return response()->json(['status' => 'ok']);
        }

        $price = number_format($product->sell_price, 0, ',', '.');

        if ($category->need_zone) {
            $this->fonnte->sendMessage($customer->phone,
                "📝 *{$product->name}*\n" .
                "Harga: *Rp {$price}*\n\n" .
                "Masukkan:\n" .
                "*order {$categoryId} {$productId} [{$category->target_label}] [{$category->zone_label}]*\n\n" .
                "Contoh:\n*order {$categoryId} {$productId} 12345678 1234*"
            );
        } else {
            $this->fonnte->sendMessage($customer->phone,
                "📝 *{$product->name}*\n" .
                "Harga: *Rp {$price}*\n\n" .
                "Masukkan:\n" .
                "*order {$categoryId} {$productId} [{$category->target_label}]*\n\n" .
                "Contoh:\n*order {$categoryId} {$productId} 12345678*"
            );
        }

        return response()->json(['status' => 'ok']);
    }

    private function createOrder(
        Customer $customer,
        int      $categoryId,
        int      $productId,
        string   $targetId,
        ?string  $targetZone
    ) {
        $category = Category::where('id', $categoryId)->where('status', true)->first();
        $product  = Product::where('id', $productId)
            ->where('category_id', $categoryId)
            ->where('status', true)
            ->first();

        // Validate category & product
        if (! $category || ! $product) {
            $this->fonnte->sendMessage($customer->phone,
                "❌ Game atau produk tidak valid.\n\nKetik *order {$categoryId}* untuk lihat daftar nominal."
            );
            return response()->json(['status' => 'ok']);
        }

        // Validate zone for games that need it
        if ($category->need_zone && empty($targetZone)) {
            $this->fonnte->sendMessage($customer->phone,
                "⚠️ *{$category->name}* butuh *{$category->zone_label}*!\n\n" .
                "Format:\n*order {$categoryId} {$productId} [{$category->target_label}] [{$category->zone_label}]*\n\n" .
                "Contoh:\n*order {$categoryId} {$productId} 12345678 1234*"
            );
            return response()->json(['status' => 'ok']);
        }

        // Prevent duplicate pending order within 1 hour
        $existing = Order::where('customer_id', $customer->id)
            ->where('product_id', $productId)
            ->where('target_id', $targetId)
            ->where('status', 'pending')
            ->where('created_at', '>=', now()->subHour())
            ->first();

        if ($existing) {
            $payUrl = $existing->transaction?->payment_url ?? '-';
            $this->fonnte->sendMessage($customer->phone,
                "⚠️ Kamu punya order yang belum dibayar untuk produk yang sama!\n\n" .
                "Order: *ARC-{$existing->id}*\n" .
                ($payUrl !== '-' ? "💳 Bayar: {$payUrl}\n\n" : '') .
                "Atau ketik *batal {$existing->id}* untuk batalkan dan order ulang."
            );
            return response()->json(['status' => 'ok']);
        }

        // ── Create Order ────────────────────────────────────────────────────
        $order = Order::create([
            'customer_id' => $customer->id,
            'product_id'  => $product->id,
            'target_id'   => $targetId,
            'target_zone' => $targetZone,
            'status'      => 'pending',
            'amount'      => $product->sell_price,
        ]);

        // ── Create Tripay Transaction ───────────────────────────────────────
        $tripayResult = $this->tripay->createTransaction($order, $customer);

        if (! ($tripayResult['success'] ?? false)) {
            $order->delete();
            $this->fonnte->sendMessage($customer->phone,
                "❌ Gagal membuat transaksi pembayaran.\n\n" .
                "Error: " . ($tripayResult['message'] ?? 'Tidak diketahui') . "\n\n" .
                "Silakan coba lagi dalam beberapa menit."
            );
            return response()->json(['status' => 'ok']);
        }

        $tripayData  = $tripayResult['data'];
        $merchantRef = $tripayData['merchant_ref'];
        $reference   = $tripayData['reference'];
        $checkoutUrl = $tripayData['checkout_url'];
        $payCode     = $tripayData['pay_code'] ?? null;
        $expiredTime = $tripayData['expired_time'] ?? null;

        // Update order with payment reference
        $order->update(['payment_ref' => $merchantRef]);

        // Create transaction record
        Transaction::create([
            'order_id'       => $order->id,
            'reference'      => $reference,
            'payment_method' => 'QRIS',
            'payment_url'    => $checkoutUrl,
            'pay_code'       => $payCode,
            'amount'         => $product->sell_price,
            'status'         => 'pending',
            'expired_at'     => $expiredTime
                ? \Carbon\Carbon::createFromTimestamp($expiredTime)
                : now()->addHours(24),
        ]);

        // ── Notify Customer ────────────────────────────────────────────────
        $price   = number_format($product->sell_price, 0, ',', '.');
        $expText = $expiredTime
            ? "\n⏰ Expired: *" . \Carbon\Carbon::createFromTimestamp($expiredTime)->setTimezone('Asia/Jakarta')->format('d M Y H:i') . " WIB*"
            : '';

        $this->fonnte->sendMessage(
            $customer->phone,
            "🛒 *Order Berhasil Dibuat!*\n\n" .
            "📦 No Order: *ARC-{$order->id}*\n" .
            "🎮 Game: {$category->name}\n" .
            "💎 Produk: {$product->name}\n" .
            "🎯 Target: *{$targetId}*" . ($targetZone ? " | Server: *{$targetZone}*" : '') . "\n" .
            "💰 Total: *Rp {$price}*" .
            "{$expText}\n\n" .
            "━━━━━━━━━━━━━━━━━\n" .
            "💳 *Link Pembayaran:*\n{$checkoutUrl}\n" .
            "━━━━━━━━━━━━━━━━━\n\n" .
            "_Setelah bayar, topup otomatis diproses 1-5 menit_ ⚡\n" .
            "Ketik *cek {$order->id}* untuk cek status."
        );

        return response()->json(['status' => 'ok']);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // ORDER MANAGEMENT
    // ─────────────────────────────────────────────────────────────────────────

    private function checkOrder(Customer $customer, string $message)
    {
        $parts = array_values(array_filter(explode(' ', $message)));

        if (count($parts) < 2 || ! is_numeric($parts[1])) {
            $this->fonnte->sendMessage($customer->phone,
                "⚠️ Format: *cek [no_order]*\nContoh: *cek 5*"
            );
            return response()->json(['status' => 'ok']);
        }

        $orderId = (int) $parts[1];
        $order   = Order::with(['product.category', 'transaction'])
            ->where('id', $orderId)
            ->where('customer_id', $customer->id)
            ->first();

        if (! $order) {
            $this->fonnte->sendMessage($customer->phone,
                "❌ Order *ARC-{$orderId}* tidak ditemukan."
            );
            return response()->json(['status' => 'ok']);
        }

        $statusMap = [
            'pending'    => ['⏳', 'Menunggu Pembayaran'],
            'paid'       => ['💰', 'Pembayaran Diterima'],
            'processing' => ['⚙️',  'Sedang Diproses'],
            'success'    => ['✅', 'Berhasil'],
            'failed'     => ['❌', 'Gagal'],
            'expired'    => ['⌛', 'Kadaluarsa'],
        ];

        [$emoji, $statusText] = $statusMap[$order->status] ?? ['❓', 'Unknown'];
        $price = number_format($order->amount, 0, ',', '.');

        $text = "📦 *Detail Order ARC-{$order->id}*\n\n" .
            "🎮 Game: {$order->product->category->name}\n" .
            "💎 Produk: {$order->product->name}\n" .
            "🎯 Target: {$order->target_id}" . ($order->target_zone ? " | Server: {$order->target_zone}" : '') . "\n" .
            "💰 Total: Rp {$price}\n" .
            "📅 Waktu: " . $order->created_at->setTimezone('Asia/Jakarta')->format('d M Y H:i') . " WIB\n" .
            "Status: {$emoji} *{$statusText}*";

        // Show payment link again if still pending
        if ($order->status === 'pending' && $order->transaction?->payment_url) {
            $text .= "\n\n💳 *Link Bayar:*\n{$order->transaction->payment_url}";
        }

        $this->fonnte->sendMessage($customer->phone, $text);
        return response()->json(['status' => 'ok']);
    }

    private function cancelOrder(Customer $customer, string $message)
    {
        $parts = array_values(array_filter(explode(' ', $message)));

        if (count($parts) < 2 || ! is_numeric($parts[1])) {
            $this->fonnte->sendMessage($customer->phone,
                "⚠️ Format: *batal [no_order]*\nContoh: *batal 5*"
            );
            return response()->json(['status' => 'ok']);
        }

        $orderId = (int) $parts[1];
        $order   = Order::with('transaction')
            ->where('id', $orderId)
            ->where('customer_id', $customer->id)
            ->where('status', 'pending')
            ->first();

        if (! $order) {
            $this->fonnte->sendMessage($customer->phone,
                "❌ Order *ARC-{$orderId}* tidak ditemukan atau tidak bisa dibatalkan.\n\n" .
                "_Order hanya bisa dibatalkan jika statusnya *Menunggu Pembayaran*._"
            );
            return response()->json(['status' => 'ok']);
        }

        $order->update(['status' => 'expired']);
        $order->transaction?->update(['status' => 'expired']);

        $this->fonnte->sendMessage($customer->phone,
            "✅ Order *ARC-{$order->id}* berhasil dibatalkan.\n\n" .
            "Ketik *list* untuk order baru."
        );

        return response()->json(['status' => 'ok']);
    }
}
