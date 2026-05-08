<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminOrderController extends Controller
{
    private function checkAdmin()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized.');
        }
    }

    public function index()
    {
        $this->checkAdmin();

        $orders = Order::with('items.product')
            ->latest()
            ->get();

        return view('admin.orders', compact('orders'));
    }

    public function history()
    {
        $this->checkAdmin();

        $orders = Order::with('items.product')
            ->whereIn('status', ['completed', 'cancelled'])
            ->latest()
            ->get();

        return view('admin.history', compact('orders'));
    }

    public function updateStatus(Request $request, Order $order)
{
    $this->checkAdmin();

    $request->validate([
        'status' => 'required|in:confirmed,completed',
    ]);

    $allowedTransitions = [
        'pending'   => ['confirmed'],
        'confirmed' => ['completed'],
    ];

    $allowed = $allowedTransitions[$order->status] ?? [];

    if (!in_array($request->status, $allowed)) {
        return response()->json([
            'success' => false,
            'message' => "Cannot change status from '{$order->status}' to '{$request->status}'.",
        ], 422);
    }

    // Deduct stock kapag completed
    if ($request->status === 'completed') {
        foreach ($order->items as $item) {
            $product = $item->product;
            if ($product) {
                $product->stock = max(0, $product->stock - $item->quantity);
                $product->save();
            }
        }
        $order->completed_at = now();
    }

    $order->update(['status' => $request->status]);

    $labels = [
        'confirmed' => 'Order confirmed successfully!',
        'completed' => 'Order marked as completed!',
    ];

    return response()->json([
        'success' => true,
        'message' => $labels[$request->status],
        'status'  => $order->status,
    ]);
}

    public function cancel(Request $request, Order $order)
    {
        $this->checkAdmin();

        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        if (!in_array($order->status, ['pending', 'confirmed'])) {
            return response()->json([
                'success' => false,
                'message' => "Only pending or confirmed orders can be cancelled.",
            ], 422);
        }

        $order->update([
            'status'        => 'cancelled',
            'cancel_reason' => $request->reason,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Order cancelled successfully.',
        ]);
    }

    public function financial(Request $request)
    {
        $this->checkAdmin();

        $date = $request->input('date', now()->toDateString());

        // Completed orders on selected date
        $orders = Order::with('items.product')
            ->where('status', 'completed')
            ->whereDate('updated_at', $date)
            ->get();

        // All-time qty sold per product (to compute original stock → cost per kg)
        $allTimeSold = OrderItem::whereHas('order', function ($q) {
                $q->where('status', 'completed');
            })
            ->selectRaw('product_id, SUM(quantity) as total_qty')
            ->groupBy('product_id')
            ->pluck('total_qty', 'product_id');

        $productStats = [];

        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                $product = $item->product;
                if (!$product) continue;

                $pid = $product->id;

                if (!isset($productStats[$pid])) {
                    // Original stock = current remaining + all-time sold
                    $lifetimeSold  = (float) ($allTimeSold[$pid] ?? 0);
                    $originalStock = (float) $product->stock + $lifetimeSold;
                    $costPerKg     = $originalStock > 0
                        ? (float) $product->capital / $originalStock
                        : 0;

                    $productStats[$pid] = [
                        'name'        => $product->name,
                        'cost_per_kg' => $costPerKg,
                        'qty_sold'    => 0,
                        'revenue'     => 0,
                    ];
                }

                $productStats[$pid]['qty_sold'] += (float) $item->quantity;
                $productStats[$pid]['revenue']  += (float) $item->subtotal;
            }
        }

        // Compute capital used and net profit per product
        foreach ($productStats as &$stat) {
            $stat['capital_used'] = round($stat['qty_sold'] * $stat['cost_per_kg'], 2);
            $stat['net_profit']   = round($stat['revenue'] - $stat['capital_used'], 2);
        }
        unset($stat);

        $totals = [
            'qty_sold'     => array_sum(array_column($productStats, 'qty_sold')),
            'revenue'      => array_sum(array_column($productStats, 'revenue')),
            'capital_used' => array_sum(array_column($productStats, 'capital_used')),
            'net_profit'   => array_sum(array_column($productStats, 'net_profit')),
        ];

        return view('admin.financial', compact('productStats', 'totals', 'date'));
    }
}