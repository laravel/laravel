<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MenuItem;

class CartController extends Controller
{
    public function add(Request $request)
    {
        $menuItem = MenuItem::findOrFail($request->menu_item_id);
        
        $cart = session()->get('cart', []);
        
        if(isset($cart[$menuItem->id])) {
            $cart[$menuItem->id]['quantity']++;
        } else {
            $cart[$menuItem->id] = [
                "name" => $menuItem->name,
                "quantity" => $request->quantity ?? 1,
                "price" => $menuItem->price,
                "image" => $menuItem->image,
                "restaurant_id" => $menuItem->restaurant_id
            ];
        }
        
        session()->put('cart', $cart);
        
        return response()->json([
            'success' => true,
            'message' => 'Item added to cart',
            'cart_count' => count($cart)
        ]);
    }
    public function index()
    {
        $cart = session()->get('cart', []);
        $total = 0;
        
        foreach($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        
        return view('cart.index', compact('cart', 'total'));
    }

    public function remove(Request $request)
    {
        $cart = session()->get('cart');
        
        if(isset($cart[$request->id])) {
            unset($cart[$request->id]);
            session()->put('cart', $cart);
        }
        
        return response()->json(['success' => true]);
    }

    public function clear()
    {
        session()->forget('cart');
        return redirect()->back()->with('success', 'Cart cleared');
    }
}
