<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $pendingOrders   = Order::with('items.product')
            ->where('user_id', $userId)
            ->where('status', 'pending')
            ->latest()
            ->get();

        $confirmedOrders = Order::with('items.product')
            ->where('user_id', $userId)
            ->where('status', 'confirmed')
            ->latest()
            ->get();

        $completedOrders = Order::with('items.product')
            ->where('user_id', $userId)
            ->where('status', 'completed')
            ->latest()
            ->get();

        $unreadNotifications = UserNotification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();
            $products = \App\Models\Product::all();

        return view('customer.orders', compact(
        'pendingOrders',
        'confirmedOrders',
        'completedOrders',
        'unreadNotifications',
        'products'
));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'               => 'required|string|max:255',
            'phone'              => 'required|string|max:20',
            'address'            => 'required|string',
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|numeric|min:0.5',
        ]);

        DB::beginTransaction();
        try {
            $totalAmount    = 0;
            $orderItemsData = [];

            foreach ($request->items as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);
                $qty     = (float) $item['quantity'];

                // ── STOCK CHECK ──
                if ($product->stock < $qty) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Hindi sapat ang stock ng {$product->name}. Available: {$product->stock} kg.",
                    ], 422);
                }

                $price    = (float) $product->price;
                $subtotal = round($qty * $price, 2);
                $totalAmount += $subtotal;

                $orderItemsData[] = [
                    'product_id' => $product->id,
                    'quantity'   => $qty,
                    'unit_price' => $price,
                    'subtotal'   => $subtotal,
                ];

                // ── DEDUCT STOCK ──
                $product->decrement('stock', $qty);
            }

            $order = Order::create([
                'user_id'          => Auth::id(),
                'customer_name'    => $request->name,
                'customer_phone'   => $request->phone,
                'customer_address' => $request->address,
                'notes'            => $request->notes,
                'pickup_date'      => Carbon::tomorrow()->toDateString(),
                'total_amount'     => $totalAmount,
                'status'           => 'pending',
            ]);

            foreach ($orderItemsData as $data) {
                $order->items()->create($data);
            }

            // Remove ordered items from cart
            $productIds = collect($request->items)->pluck('product_id');
            CartItem::where('user_id', Auth::id())
                ->whereIn('product_id', $productIds)
                ->delete();

            DB::commit();

            return response()->json([
                'success'  => true,
                'order_id' => $order->id,
                'message'  => 'Order placed successfully!',
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Request $request, Order $order)
{
    // Only allow cancellation of pending or confirmed orders
    if (!in_array($order->status, ['pending', 'confirmed'])) {
        return response()->json([
            'success' => false,
            'message' => 'This order can no longer be cancelled.'
        ], 422);
    }

    // Optional: only the owner can cancel
    if ($order->user_id !== auth()->id()) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized.'
        ], 403);
    }

    $order->update([
        'status'        => 'cancelled',
        'cancel_reason' => $request->input('reason', ''),
    ]);

    return response()->json(['success' => true, 'message' => 'Order cancelled successfully.']);
}
}