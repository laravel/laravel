<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Coffee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $orders = auth()->user()->orders()
            ->with('coffee')
            ->latest()
            ->get();
        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $this->authorize('view', $order);
        return view('orders.show', compact('order'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'coffee_id' => 'required|exists:coffees,id',
            'quantity' => 'required|integer|min:1|max:5',
        ]);

        try {
            DB::beginTransaction();
            
            $coffee = Coffee::lockForUpdate()->findOrFail($validated['coffee_id']);
            
            // Check stock availability
            if ($coffee->stock_quantity < $validated['quantity']) {
                DB::rollBack();
                return back()->with('error', 'Not enough stock available.');
            }
            
            // Calculate total price
            $total_price = $coffee->price * $validated['quantity'];
            
            // Check wallet balance
            $user_balance = auth()->user()->transactions()->sum('amount');
            if ($user_balance < $total_price) {
                DB::rollBack();
                return back()->with('error', 'Insufficient balance. Please top up your wallet.');
            }

            // Create the order
            $order = auth()->user()->orders()->create([
                'coffee_id' => $validated['coffee_id'],
                'quantity' => $validated['quantity'],
                'total_price' => $total_price,
                'status' => 'pending'
            ]);

            // Deduct from wallet
            auth()->user()->transactions()->create([
                'amount' => -$total_price,
                'type' => 'purchase',
                'description' => "Order #{$order->id} - {$coffee->name} x {$validated['quantity']}"
            ]);

            // Deduct stock quantity
            if (!$coffee->decrementStock($validated['quantity'])) {
                DB::rollBack();
                return back()->with('error', 'Failed to update stock quantity.');
            }

            DB::commit();
            return redirect()->route('orders.show', $order)
                ->with('success', 'Order placed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }
} 