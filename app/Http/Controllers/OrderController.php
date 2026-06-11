<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

/**
 * @method void middleware($middleware, array $options = [])
 */

class OrderController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('auth'),
            // You can add 'except' or 'only' conditions as well:
            // new Middleware('auth', except: ['index']),
        ];
    }

    

    public function checkout()
    {
        $cart = session()->get('cart', []);
        
        if(empty($cart)) {
            return redirect()->route('home')->with('error', 'Your cart is empty');
        }
        
        $subtotal = 0;
        foreach($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        
        $deliveryFee = 5.00;
        $tax = $subtotal * 0.10;
        $total = $subtotal + $deliveryFee + $tax;
        
        return view('orders.checkout', compact('cart', 'subtotal', 'deliveryFee', 'tax', 'total'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'delivery_address' => 'required|string',
            'phone' => 'required|string',
        ]);

        $cart = session()->get('cart', []);
        
        if(empty($cart)) {
            return redirect()->route('home')->with('error', 'Your cart is empty');
        }

        DB::beginTransaction();
        
        try {
            $subtotal = 0;
            $restaurantId = null;
            
            foreach($cart as $item) {
                $subtotal += $item['price'] * $item['quantity'];
                if(!$restaurantId) {
                    $restaurantId = $item['restaurant_id'];
                }
            }
            
            $deliveryFee = 5.00;
            $tax = $subtotal * 0.10;
            $total = $subtotal + $deliveryFee + $tax;
            
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'user_id' => auth()->id(),
                'restaurant_id' => $restaurantId,
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'tax' => $tax,
                'total' => $total,
                'status' => 'pending',
                'delivery_address' => $request->delivery_address,
                'phone' => $request->phone,
                'notes' => $request->notes,
            ]);

            foreach($cart as $id => $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $id,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            }

            session()->forget('cart');
            
            DB::commit();
            
            return redirect()->route('orders.show', $order)
                ->with('success', 'Order placed successfully!');
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function show(Order $order)
    {
        Gate::authorize('view', $order);
        
        $order->load(['items.menuItem', 'restaurant']);
        
        return view('orders.show', compact('order'));
    }

    public function myOrders()
    {
        $orders = Order::where('user_id', auth()->id())
            ->with('restaurant')
            ->latest()
            ->paginate(10);
            
        return view('orders.my-orders', compact('orders'));
    }
}

