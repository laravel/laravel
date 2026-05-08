<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class FinancialController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->get('date', now()->toDateString());
        $productStats = [];
        $totals = [
            'qty_sold'     => 0,
            'revenue'      => 0,
            'capital_used' => 0,
            'net_profit'   => 0
        ];

        $orders = Order::with('items.product')
            ->whereDate('created_at', $date)
            ->get();

        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                $product = $item->product;
                if (!$product) continue;

                $name    = $product->name;
                $cost    = (float) ($product->capital ?? 0);
                $qty     = (float) $item->quantity;
                $revenue = $qty * (float) ($product->price ?? 0);
                $capital = $cost * $qty;
                $profit  = $revenue - $capital;

                if (!isset($productStats[$name])) {
                    $productStats[$name] = [
                        'name'         => $name,
                        'cost_per_kg'  => $cost,
                        'qty_sold'     => 0,
                        'revenue'      => 0,
                        'capital_used' => 0,
                        'net_profit'   => 0,
                    ];
                }

                $productStats[$name]['qty_sold']     += $qty;
                $productStats[$name]['revenue']      += $revenue;
                $productStats[$name]['capital_used'] += $capital;
                $productStats[$name]['net_profit']   += $profit;

                $totals['qty_sold']     += $qty;
                $totals['revenue']      += $revenue;
                $totals['capital_used'] += $capital;
                $totals['net_profit']   += $profit;
            }
        }

        return view('admin.financial', compact('date', 'productStats', 'totals'));
    }
}